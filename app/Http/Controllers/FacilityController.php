<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\JsonResponse;

class FacilityController extends Controller
{
    /**
     * Seluruh fasilitas untuk peta, daftar, dan dashboard.
     *
     * shortcut: semua baris dikirim sekali karena peta butuh semua penanda sekaligus;
     * tambahkan paginasi bila jumlah fasilitas sudah melewati beberapa ribu baris.
     */
    public function index(): JsonResponse
    {
        $facilities = Facility::query()->orderBy('external_id')->get();

        return response()->json([
            'total' => $facilities->count(),
            'facilities' => $facilities->map->toPayload()->all(),
        ]);
    }
}
