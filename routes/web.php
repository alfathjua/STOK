<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Splash / opening page (5-second delay then redirects to login)
Route::get('/', [AuthController::class, 'index'])->name('index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (global - both director & foreman)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Stock-in (input stok masuk)
    Route::get('/stock-in', fn () => view('dash.stock-in.index'))->name('stock-in.index');

    // Stock / warehouse (stok gudang)
    Route::get('/stock', fn () => view('dash.stock.index'))->name('stock.index');

    // Stock-out (input stok keluar)
    Route::get('/stock-out', fn () => view('dash.stock-out.index'))->name('stock-out.index');

    // Profile - my profile (profile saya)
    Route::get('/profile', fn () => view('dash.profile.user'))->name('profile.user');

    /*
    |------------------------------------------------------------------------
    | Director-only Routes
    |------------------------------------------------------------------------
    */

    Route::middleware('role:director')->group(function () {
        // Company profile (profile perusahaan)
        Route::get('/company', fn () => view('dash.profile.company'))->name('profile.company');
    });
});
