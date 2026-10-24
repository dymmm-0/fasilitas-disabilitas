<?php

namespace App\Http\Controllers;

use App\Jobs\QueueSubmission;
use App\Models\Facility;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Kontribusi masyarakat dan keputusan atas pengajuan. Semua keadaan disimpan di
 * basis data: pengajuan (`submissions`), bukti (`submission_proofs`), riwayat
 * (`verification_events`). Pembacaan antrean ada di `VerificationQueueController`
 * karena antrean memuat fasilitas dan pengajuan sekaligus.
 */
class SubmissionController extends Controller
{
    /** Jendela waktu untuk menolak kiriman ganda dari klik/percobaan ulang. */
    private const DUPLICATE_WINDOW_SECONDS = 60;

    /** Batas ukuran bukti pendukung (KB). */
    private const MAX_PROOF_KB = 4096;

    /** Jeda antrean: pengajuan baru masuk meja verifikasi setelah sekian detik. */
    private const QUEUE_DELAY_SECONDS = 60;

    /** Batas jumlah pengajuan yang ditampilkan halaman status guest. */
    private const STATUS_LIMIT = 50;

    /** Pengajuan dari masyarakat. Terbuka untuk pengunjung tanpa akun. */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'type' => ['required', Rule::in(Submission::TYPES)],
            'facility_name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(Facility::CATEGORIES)],
            'city' => ['required', 'string', 'max:120'],
            'submitted_by' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'email', 'max:190'],
            'summary' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'proof' => ['nullable', 'image', 'max:'.self::MAX_PROOF_KB],

            // Usulan fasilitas baru.
            'address' => ['required_if:type,usulan', 'nullable', 'string', 'max:500'],
            'latitude' => ['required_if:type,usulan', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['required_if:type,usulan', 'nullable', 'numeric', 'between:-180,180'],
            'accessibility' => ['required_if:type,usulan', 'nullable', 'array'],

            // Laporan koreksi fasilitas yang sudah ada.
            'facility_id' => ['required_if:type,koreksi', 'nullable', 'string', 'max:30'],
            'field' => ['required_if:type,koreksi', 'nullable', Rule::in(Submission::ATTRIBUTES)],
            'to' => ['required_if:type,koreksi', 'nullable', Rule::in(Submission::AVAILABILITY)],
        ];

        // Checklist aksesibilitas harus lengkap lima atribut: klaim yang bolong tidak
        // bisa dipakai verifikator untuk memutuskan.
        foreach (Submission::ATTRIBUTES as $attribute) {
            $rules["accessibility.$attribute"] = ['required_if:type,usulan', 'nullable', Rule::in(Submission::AVAILABILITY)];
        }

        $data = $request->validate($rules);

        // Surel adalah kunci identitas pengaju (guest tidak punya akun), jadi
        // disimpan terormalisasi supaya pencarian status konsisten di MySQL dan SQLite.
        $data['contact'] = mb_strtolower(trim($data['contact']));

        $facility = null;
        $payload = [
            'notes' => $data['notes'] ?? null,
        ];

        if ($data['type'] === 'usulan') {
            $payload += [
                'address' => $data['address'],
                'lat' => (float) $data['latitude'],
                'lng' => (float) $data['longitude'],
                'accessibility' => array_intersect_key($data['accessibility'] ?? [], array_flip(Submission::ATTRIBUTES)),
            ];
        } else {
            $facility = Facility::firstWhere('external_id', $data['facility_id']);

            if (! $facility) {
                throw ValidationException::withMessages([
                    'facility_id' => 'Fasilitas yang ingin dikoreksi tidak ditemukan.',
                ]);
            }

            [$statusColumn] = Facility::ATTRIBUTE_COLUMNS[$data['field']];
            $current = Facility::AVAILABILITY_MAP[$facility->{$statusColumn}];

            if ($current === $data['to']) {
                throw ValidationException::withMessages([
                    'to' => 'Nilai yang diusulkan sama dengan data yang tersimpan sekarang.',
                ]);
            }

            $payload += ['field' => $data['field'], 'from' => $current, 'to' => $data['to']];
        }

        $this->guardDuplicate($data);

        $proofPath = $request->hasFile('proof')
            ? $request->file('proof')->store('submission-proofs', 'local')
            : null;

        try {
            $submission = DB::transaction(function () use ($data, $payload, $facility, $request, $proofPath) {
                $submission = Submission::create([
                    'type' => $data['type'],
                    'facility_id' => $facility?->id,
                    'facility_name' => $data['facility_name'],
                    'category' => $data['category'],
                    'city' => $data['city'],
                    'submitted_by' => $data['submitted_by'],
                    'contact' => $data['contact'],
                    'summary' => $data['summary'] ?? null,
                    'payload' => $payload,
                    'status' => 'queued',
                ]);

                // Identifier publik dari primary key: tidak ada nilai yang bisa bentrok.
                $submission->update([
                    'public_id' => 'SUB-'.str_pad((string) $submission->id, 4, '0', STR_PAD_LEFT),
                ]);

                if ($proofPath !== null) {
                    $file = $request->file('proof');
                    $submission->proofs()->create([
                        'submission_id' => $submission->id,
                        'disk' => 'local',
                        'path' => $proofPath,
                        'original_name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }

                return $submission;
            });
        } catch (\Throwable $error) {
            // Berkas tidak boleh tertinggal kalau record-nya gagal dibuat.
            if ($proofPath !== null) {
                Storage::disk('local')->delete($proofPath);
            }

            throw $error;
        }

        // Dispatch setelah transaksi selesai: job tidak boleh berjalan sebelum
        // barisnya benar-benar ada di basis data.
        $readyAt = $submission->created_at->copy()->addSeconds(self::QUEUE_DELAY_SECONDS);
        QueueSubmission::dispatch($submission)->delay($readyAt);

        return response()->json([
            'submission' => $submission->load(['facility', 'createdFacility', 'proofs'])->toPayload(),
            'queue' => ['readyAt' => $readyAt->toIso8601String()],
        ], 201);
    }

    /**
     * Status pengajuan untuk guest tanpa akun: dicari lewat surel pengaju, karena
     * nama boleh sama antara dua orang. Hanya field non-sensitif yang dikembalikan —
     * surel, isian mentah, ringkasan, bukti, dan identitas petugas tidak pernah ikut.
     *
     * shortcut: pencarian per surel, maksimal 50 pengajuan terbaru; tambah pagination
     * bila volume pengajuan bertambah.
     */
    public function status(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $submissions = Submission::query()
            ->with(['facility:id,external_id', 'createdFacility:id,external_id'])
            ->whereRaw('LOWER(TRIM(contact)) = ?', [mb_strtolower(trim($data['email']))])
            ->when(
                filled($data['name'] ?? null),
                fn ($builder) => $builder->whereRaw('LOWER(TRIM(submitted_by)) = ?', [mb_strtolower(trim($data['name']))]),
            )
            ->latest('id')
            ->limit(self::STATUS_LIMIT)
            ->get();

        return response()->json([
            'total' => $submissions->count(),
            'submissions' => $submissions->map(fn (Submission $submission) => [
                'id' => $submission->public_id,
                'type' => $submission->type,
                'facilityName' => $submission->facility_name,
                'category' => $submission->category,
                'city' => $submission->city,
                'status' => $submission->status,
                'submittedAt' => $submission->created_at?->toDateString(),
                'decidedAt' => $submission->decided_at?->toIso8601String(),
                'readyAt' => $submission->status === 'queued'
                    ? $submission->created_at?->copy()->addSeconds(self::QUEUE_DELAY_SECONDS)->toIso8601String()
                    : null,
                'facilityId' => $submission->facility?->external_id,
                'createdFacilityId' => $submission->createdFacility?->external_id,
            ])->all(),
        ]);
    }

    /** Keputusan verifikasi: setujui, minta perbaikan, atau tolak. */
    public function decide(Request $request, Submission $submission): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['disetujui', 'perbaikan', 'ditolak'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $submission->isDecidable()) {
            throw ValidationException::withMessages([
                'action' => "Pengajuan {$submission->public_id} sudah diputuskan (".$submission->status.').',
            ]);
        }

        if ($data['action'] !== 'disetujui' && blank($data['note'] ?? null)) {
            throw ValidationException::withMessages([
                'note' => 'Catatan wajib diisi untuk minta perbaikan atau menolak pengajuan.',
            ]);
        }

        if ($data['action'] === 'disetujui' && ! $this->approvable($submission)) {
            throw ValidationException::withMessages([
                'action' => 'Pengajuan tidak dapat disetujui: data yang diajukan tidak lengkap '
                    .'atau fasilitas yang dikoreksi sudah tidak ada.',
            ]);
        }

        // Identitas petugas diambil dari sesi yang sedang masuk, bukan dikarang.
        $actor = $request->user()->name;

        DB::transaction(function () use ($submission, $data, $actor) {
            $updates = [
                'status' => $data['action'],
                'decided_by' => $actor,
                'decided_at' => now(),
            ];

            if ($data['action'] === 'disetujui' && $submission->type === 'usulan') {
                $updates['created_facility_id'] = $this->createFacility($submission)->id;
            }

            if ($data['action'] === 'disetujui' && $submission->type === 'koreksi') {
                [$statusColumn] = Facility::ATTRIBUTE_COLUMNS[$submission->payload['field']];
                $submission->facility->update([
                    $statusColumn => Submission::AVAILABILITY_TO_DB[$submission->payload['to']],
                ]);
            }

            $submission->events()->create([
                'submission_id' => $submission->id,
                'status_from' => $submission->status,
                'status_to' => $data['action'],
                'note' => $data['note'] ?? null,
                'actor' => $actor,
            ]);

            $submission->update($updates);
        });

        return response()->json([
            'submission' => $submission->fresh(['facility', 'createdFacility', 'proofs'])->toPayload(),
        ]);
    }

    /** Bukti pendukung bersifat privat: hanya petugas yang sudah masuk bisa membukanya. */
    public function proof(Submission $submission, int $proof)
    {
        $file = $submission->proofs()->findOrFail($proof);

        abort_unless(Storage::disk($file->disk)->exists($file->path), 404);

        return Storage::disk($file->disk)->response($file->path, $file->original_name, [
            'Content-Type' => $file->mime ?? 'application/octet-stream',
        ]);
    }

    /** Fasilitas baru hasil usulan masyarakat. Klaim pengaju tidak dianggap terverifikasi. */
    private function createFacility(Submission $submission): Facility
    {
        $claims = $submission->payload['accessibility'] ?? [];
        $statuses = [];

        foreach (Submission::ATTRIBUTES as $attribute) {
            [$statusColumn] = Facility::ATTRIBUTE_COLUMNS[$attribute];
            $statuses[$statusColumn] = Submission::AVAILABILITY_TO_DB[$claims[$attribute] ?? 'belum'];
        }

        $known = count(array_filter($statuses, fn (string $value) => $value !== 'unknown'));

        return Facility::create($statuses + [
            'external_id' => Facility::nextExternalId(),
            'name' => $submission->facility_name,
            'category' => $submission->category,
            'source_category' => 'community_submission',
            'address' => $submission->payload['address'] ?? null,
            'city' => $submission->city,
            'latitude' => $submission->payload['lat'],
            'longitude' => $submission->payload['lng'],
            // Usulan yang disetujui hanya melahirkan fasilitas baru; indikatornya masih
            // klaim pengaju sampai ada pemeriksaan lapangan.
            'verification_status' => 'pending',
            'source_name' => 'Usulan masyarakat',
            'source_ref_type' => 'submission',
            'source_ref_id' => $submission->public_id,
            'collected_at' => now()->toDateString(),
            'data_completeness_percent' => (int) round($known / count(Submission::ATTRIBUTES) * 100),
            'notes' => $submission->payload['notes'] ?? null,
        ]);
    }

    /** Pengajuan harus punya data yang bisa dijadikan dasar keputusan. */
    private function approvable(Submission $submission): bool
    {
        if ($submission->type === 'koreksi') {
            return $submission->facility !== null
                && isset(Facility::ATTRIBUTE_COLUMNS[$submission->payload['field'] ?? null])
                && isset(Submission::AVAILABILITY_TO_DB[$submission->payload['to'] ?? null]);
        }

        return is_numeric($submission->payload['lat'] ?? null)
            && is_numeric($submission->payload['lng'] ?? null);
    }

    /** Kiriman identik berulang (klik ganda atau percobaan ulang) ditolak. */
    private function guardDuplicate(array $data): void
    {
        $duplicate = Submission::query()
            ->where('submitted_by', $data['submitted_by'])
            ->where('facility_name', $data['facility_name'])
            ->where('type', $data['type'])
            ->where('created_at', '>=', now()->subSeconds(self::DUPLICATE_WINDOW_SECONDS))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'facility_name' => 'Pengajuan yang sama baru saja terkirim. Tunggu sebentar sebelum mengirim lagi.',
            ]);
        }
    }
}
