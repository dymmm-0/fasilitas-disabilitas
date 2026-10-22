<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Submission;
use App\Models\VerificationEvent;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Antrean verifikasi gabungan: fasilitas yang membutuhkan pemeriksaan (termasuk
 * 1.117 baris dataset) dan pengajuan masyarakat. Keduanya diputuskan dari halaman
 * yang sama, jadi keduanya dibaca dari satu endpoint berhalaman.
 *
 * `stats` dihitung langsung dari basis data, bukan dari baris yang sedang tampil.
 * Setiap record dihitung sekali: jumlah fasilitas memakai `COUNT(*)` atas tabel
 * fasilitas, jumlah pengajuan memakai `COUNT(*)` atas tabel pengajuan — tidak ada
 * join, jadi fasilitas dengan beberapa laporan koreksi tetap terhitung satu.
 */
class VerificationQueueController extends Controller
{
    /**
     * Penyaring antrean. `dataset` menampilkan seluruh fasilitas (untuk menelusuri
     * baris impor), `perlu` adalah default halaman ini.
     */
    public const BUCKETS = ['perlu', 'dataset', 'usulan', 'koreksi', 'menunggu', 'perbaikan', 'selesai'];

    private const DEFAULT_PER_PAGE = 10;

    private const MAX_PER_PAGE = 50;

    public function index(Request $request): JsonResponse
    {
        $bucket = in_array($request->query('bucket'), self::BUCKETS, true) ? $request->query('bucket') : 'perlu';
        $query = trim((string) $request->query('q', ''));
        $perPage = min(max($request->integer('per_page', self::DEFAULT_PER_PAGE), 1), self::MAX_PER_PAGE);

        $facilityQuery = $this->facilityQuery($bucket, $query);
        $submissionQuery = $this->submissionQuery($bucket, $query);

        // Urutan: yang terbaru lebih dahulu, `ref` sebagai pemecah seri supaya
        // halaman tidak kehilangan atau menggandakan baris.
        $page = DB::query()
            ->fromSub($facilityQuery->unionAll($submissionQuery), 'queue')
            ->orderByDesc('created_at')
            ->orderBy('ref')
            ->paginate($perPage);

        $rows = collect($page->items());

        $facilities = Facility::query()
            ->whereIn('external_id', $rows->where('kind', 'fasilitas')->pluck('ref'))
            ->get()
            ->keyBy('external_id');

        $submissions = Submission::with(['facility', 'createdFacility', 'proofs'])
            ->whereIn('public_id', $rows->where('kind', '!=', 'fasilitas')->pluck('ref'))
            ->get()
            ->keyBy('public_id');

        // Laporan koreksi yang belum diputuskan untuk fasilitas di halaman ini:
        // pengajuan tetap terhubung ke fasilitasnya, bukan membuat baris baru.
        $reports = Submission::query()
            ->whereIn('facility_id', $facilities->pluck('id'))
            ->whereIn('status', ['menunggu', 'perbaikan'])
            ->get(['public_id', 'status', 'facility_id'])
            ->groupBy('facility_id')
            ->map(fn ($group) => $group->map(fn (Submission $s) => ['id' => $s->public_id, 'status' => $s->status])->all());

        $items = $rows
            ->map(fn ($row) => $this->item($row, $facilities, $submissions, $reports))
            ->filter()
            ->values();

        return response()->json([
            'total' => $page->total(),
            'items' => $items,
            'meta' => [
                'currentPage' => $page->currentPage(),
                'lastPage' => $page->lastPage(),
                'perPage' => $page->perPage(),
                'from' => $page->firstItem(),
                'to' => $page->lastItem(),
            ],
            'stats' => $this->stats(),
            'history' => $this->history(),
        ]);
    }

    /** Fasilitas: hanya yang benar-benar membutuhkan pemeriksaan atau yang diminta penyaring. */
    private function facilityQuery(string $bucket, string $query): Builder
    {
        $builder = DB::table('facilities')
            ->select('external_id as ref', 'name', 'verification_status as status', 'created_at')
            ->selectRaw("'fasilitas' as kind");

        if ($bucket === 'usulan' || $bucket === 'koreksi') {
            // Penyaring jenis pengajuan: tidak ada fasilitas yang ditampilkan.
            $builder->whereRaw('1 = 0');
        } elseif ($bucket === 'menunggu') {
            $builder->where('verification_status', 'pending');
        } elseif ($bucket === 'perbaikan') {
            $builder->where('verification_status', 'needs_repair');
        } elseif ($bucket === 'selesai') {
            $builder->whereIn('verification_status', ['verified', 'rejected']);
        } elseif ($bucket !== 'dataset') {
            $builder->whereIn('verification_status', Facility::NEEDS_VERIFICATION);
        }

        return $this->search($builder, $query, ['name', 'external_id', 'city']);
    }

    /** Pengajuan masyarakat: usulan fasilitas baru dan laporan koreksi. */
    private function submissionQuery(string $bucket, string $query): Builder
    {
        $builder = DB::table('submissions')
            ->select('public_id as ref', 'facility_name as name', 'status', 'created_at')
            ->selectRaw('type as kind');

        if ($bucket === 'dataset') {
            $builder->whereRaw('1 = 0');
        } elseif ($bucket === 'usulan' || $bucket === 'koreksi') {
            $builder->where('type', $bucket);
        } elseif ($bucket === 'menunggu') {
            $builder->where('status', 'menunggu');
        } elseif ($bucket === 'perbaikan') {
            $builder->where('status', 'perbaikan');
        } elseif ($bucket === 'selesai') {
            $builder->whereIn('status', Submission::FINAL_STATUSES);
        } else {
            $builder->whereNotIn('status', Submission::FINAL_STATUSES);
        }

        return $this->search($builder, $query, ['facility_name', 'public_id', 'submitted_by', 'city']);
    }

    private function search(Builder $builder, string $query, array $columns): Builder
    {
        if ($query === '') {
            return $builder;
        }

        return $builder->where(function (Builder $where) use ($query, $columns) {
            foreach ($columns as $column) {
                $where->orWhere($column, 'like', '%'.$query.'%');
            }
        });
    }

    /** Bentuk baris antrean yang dibaca `resources/js/components/verification/QueueList.vue`. */
    private function item(object $row, $facilities, $submissions, $reports): ?array
    {
        if ($row->kind === 'fasilitas') {
            $facility = $facilities->get($row->ref);

            if (! $facility) {
                return null;
            }

            return [
                'kind' => 'fasilitas',
                'id' => $facility->external_id,
                'name' => $facility->name,
                'category' => $facility->category,
                'city' => $facility->city,
                'status' => Facility::VERIFICATION_MAP[$facility->verification_status],
                'source' => $facility->source_ref_type === 'submission' ? 'Usulan masyarakat' : 'Dataset OpenStreetMap',
                'submittedBy' => null,
                'createdAt' => $facility->created_at?->toDateString(),
                'pendingReports' => $reports->get($facility->id, []),
                'facility' => $facility->toPayload(),
            ];
        }

        $submission = $submissions->get($row->ref);

        if (! $submission) {
            return null;
        }

        return [
            'kind' => $submission->type,
            'id' => $submission->public_id,
            'name' => $submission->facility_name,
            'category' => $submission->category,
            'city' => $submission->city,
            'status' => $submission->status,
            'source' => 'Pengajuan masyarakat',
            'submittedBy' => $submission->submitted_by,
            'createdAt' => $submission->created_at?->toDateString(),
            'pendingReports' => [],
            'submission' => $submission->toPayload(),
        ];
    }

    /** Angka dashboard: satu hitungan per record, tidak ada penggandaan lewat join. */
    private function stats(): array
    {
        $pending = Facility::where('verification_status', 'pending')->count();
        $needsRepair = Facility::where('verification_status', 'needs_repair')->count();

        $datasetPending = Facility::where('verification_status', 'pending')
            ->where(fn ($where) => $where->whereNull('source_ref_type')->orWhere('source_ref_type', '!=', 'submission'))
            ->count();

        $communityPending = Facility::where('verification_status', 'pending')
            ->where('source_ref_type', 'submission')
            ->count();

        $submissionsOpen = Submission::whereNotIn('status', Submission::FINAL_STATUSES)->count();

        return [
            'facilitiesTotal' => Facility::count(),
            'facilitiesPending' => $pending,
            'facilitiesNeedsRepair' => $needsRepair,
            'facilitiesVerified' => Facility::where('verification_status', 'verified')->count(),
            'facilitiesRejected' => Facility::where('verification_status', 'rejected')->count(),
            'datasetPending' => $datasetPending,
            'communityPending' => $communityPending,
            'submissionsOpen' => $submissionsOpen,
            'submissionsDecided' => Submission::whereIn('status', Submission::FINAL_STATUSES)->count(),
            'queueTotal' => $pending + $needsRepair + $submissionsOpen,
        ];
    }

    /** Riwayat keputusan terbaru dari basis data, mencakup fasilitas dan pengajuan. */
    private function history(): array
    {
        return VerificationEvent::with(['submission:id,public_id,type,facility_name', 'facility:id,external_id,name'])
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(function (VerificationEvent $event) {
                $kind = $event->facility_id ? 'fasilitas' : ($event->submission?->type ?? 'usulan');

                return [
                    'id' => $event->facility?->external_id ?? $event->submission?->public_id,
                    'kind' => $kind,
                    'name' => $event->facility?->name ?? $event->submission?->facility_name ?? 'Tidak diketahui',
                    'action' => $this->label($kind, $event->status_to),
                    'from' => $this->label($kind, $event->status_from),
                    'note' => $event->note ?? 'Tanpa catatan.',
                    'by' => $event->actor ?? 'Tidak diketahui',
                    'at' => $event->created_at?->toIso8601String(),
                ];
            })
            ->all();
    }

    /** Status tersimpan (kolom database) → label yang dipakai antarmuka. */
    private function label(string $kind, ?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        return $kind === 'fasilitas' ? (Facility::VERIFICATION_MAP[$status] ?? $status) : $status;
    }
}
