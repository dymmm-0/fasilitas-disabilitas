<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
