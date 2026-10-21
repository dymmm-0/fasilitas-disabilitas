<?php

use Illuminate\Support\Facades\Route;

// SPA AksesKita: satu Blade shell, sisanya ditangani Vue Router.
// `/up` dikecualikan agar health check bawaan Laravel tetap hidup.
Route::view('/', 'app');
Route::view('/{any}', 'app')->where('any', '^(?!up$).*');
