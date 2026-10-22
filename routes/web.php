<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\VerificationQueueController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Sesi admin. Pengunjung (guest) tidak punya akun: kontribusi cukup mengisi nama
// pada formulir, jadi tidak ada endpoint registrasi.
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
    Route::get('/me', fn (Request $request) => ['user' => $request->user()?->only('id', 'name', 'email')]);
    Route::get('/users', fn () => User::orderBy('name')->get(['id', 'name', 'email']))->middleware('auth');

    // Kontribusi masyarakat: pengunjung tanpa akun boleh mengirim pengajuan.
    Route::post('/submissions', [SubmissionController::class, 'store'])->middleware('throttle:10,1');

    // Antrean (fasilitas + pengajuan), bukti, dan keputusan hanya untuk petugas yang masuk.
    Route::middleware('auth')->group(function () {
        Route::get('/verification-queue', [VerificationQueueController::class, 'index']);
        Route::post('/submissions/{submission:public_id}/decision', [SubmissionController::class, 'decide']);
        Route::get('/submissions/{submission:public_id}/proofs/{proof}', [SubmissionController::class, 'proof']);
        Route::post('/facilities/{facility:external_id}/verification', [FacilityController::class, 'verify']);
    });
});

// Hanya dashboard verifikasi dan admin yang butuh akun.
Route::view('/admin', 'app')->middleware('auth');
Route::view('/verifikasi', 'app')->middleware('auth');
Route::view('/masuk', 'app')->middleware('guest');

// SPA AksesKita: satu Blade shell, sisanya ditangani Vue Router.
// `/up` dikecualikan agar health check bawaan Laravel tetap hidup, `api/` agar
// routes/api.php tetap dijangkau meski `web` didaftarkan lebih dulu.
Route::view('/', 'app');
Route::view('/{any}', 'app')->where('any', '^(?!up$|api/).*');
