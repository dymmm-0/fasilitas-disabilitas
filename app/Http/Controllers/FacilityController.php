<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\VerificationEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FacilityController extends Controller
{
    /** Batas atas `per_page` supaya permintaan tetap wajar. */
    private const MAX_PER_PAGE = 100;

    /**
     * Tanpa `per_page`: seluruh fasilitas, karena peta membutuhkan semua penanda
     * sekaligus dan dashboard menghitung agregat dari semua baris.
     * Dengan `per_page`: satu halaman saja, untuk tabel Pengelolaan fasilitas.
     *
     * shortcut: urutan memakai `external_id` (unik) supaya tidak ada baris yang
     * muncul dua kali atau hilang saat berpindah halaman. Ganti ke kolom lain hanya
     * bila kolom itu juga unik.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Facility::query()->orderBy('external_id');

        if (! $request->filled('per_page')) {
            $facilities = $query->get();

            return response()->json([
                'total' => $facilities->count(),
                'facilities' => $facilities->map->toPayload()->all(),
            ]);
        }

        $page = $query->paginate(min(max($request->integer('per_page'), 1), self::MAX_PER_PAGE));

        return response()->json([
            'total' => $page->total(),
            'facilities' => collect($page->items())->map->toPayload()->all(),
            'meta' => [
                'currentPage' => $page->currentPage(),
                'lastPage' => $page->lastPage(),
                'perPage' => $page->perPage(),
                'from' => $page->firstItem(),
                'to' => $page->lastItem(),
            ],
        ]);
    }

    /**
     * Hasil pemeriksaan atas satu fasilitas. Yang disimpan adalah keputusan
     * verifikasi tingkat fasilitas: identitas petugas, waktu, metode, dan catatan.
     * Delapan indikator aksesibilitas tidak disentuh — status fasilitas dan status tiap
     * indikator adalah dua hal berbeda, dan indikator tidak boleh menjadi `yes`
     * tanpa bukti.
     */
    public function verify(Request $request, Facility $facility): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(Facility::VERIFICATION_ACTIONS)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['action'] !== 'verified' && blank($data['note'] ?? null)) {
            throw ValidationException::withMessages([
                'note' => 'Catatan wajib diisi untuk minta perbaikan atau menolak fasilitas.',
            ]);
        }

        if ($data['action'] === 'verified' && $facility->verification_status === 'verified') {
            throw ValidationException::withMessages([
                'action' => "Fasilitas {$facility->external_id} sudah ditandai terverifikasi.",
            ]);
        }

        // Identitas petugas dari sesi yang sedang masuk, bukan dikarang.
        $actor = $request->user()->name;
        $statusFrom = $facility->verification_status;
        $verified = $data['action'] === 'verified';

        DB::transaction(function () use ($facility, $data, $actor, $statusFrom, $verified) {
            $facility->update([
                'verification_status' => $data['action'],
                'verified_at' => $verified ? now() : null,
                'verified_by' => $verified ? $actor : null,
                'verification_method' => $verified ? 'pemeriksaan petugas' : null,
                'verification_note' => $data['note'] ?? null,
            ]);

            VerificationEvent::create([
                'facility_id' => $facility->id,
                'status_from' => $statusFrom,
                'status_to' => $data['action'],
                'note' => $data['note'] ?? null,
                'actor' => $actor,
            ]);
        });

        return response()->json(['facility' => $facility->fresh()->toPayload()]);
    }
}
