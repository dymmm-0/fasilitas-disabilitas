<?php

use Illuminate\Support\Facades\Route;

// SPA AksesKita: satu Blade shell, sisanya ditangani Vue Router.
// `/up` dikecualikan agar health check bawaan Laravel tetap hidup, `api/` agar
// routes/api.php tetap dijangkau meski `web` didaftarkan lebih dulu.
Route::view('/', 'app');
Route::view('/{any}', 'app')->where('any', '^(?!up$|api/).*');
