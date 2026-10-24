<?php

use App\Http\Controllers\FacilityController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/facilities', [FacilityController::class, 'index']);

// Status pengajuan untuk guest tanpa akun: dicari lewat surel pengaju yang mengisi
// formulir kontribusi. Publik, jadi dibatasi agar tidak bisa dipakai menyisir data.
Route::get('/submissions/status', [SubmissionController::class, 'status'])->middleware('throttle:10,1');
