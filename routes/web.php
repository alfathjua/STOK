<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockInController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Serve private files (proof & receipts)
    Route::get('/private-file/{path}', function (string $path) {
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(
            Storage::disk('local')->path($path),
            ['Content-Type' => Storage::disk('local')->mimeType($path)]
        );
    })->where('path', '.*')->name('private.file');

    // Stok Masuk (full resource)
    Route::resource('stock-in', StockInController::class)->except(['show']);

    // Stok Gudang
    Route::get('/stock', fn () => view('dash.stock.index'))->name('stock.index');

    // Stok Keluar
    Route::get('/stock-out', fn () => view('dash.stock-out.index'))->name('stock-out.index');

    // Profile
    Route::get('/profile', fn () => view('dash.profile.user'))->name('profile.user');

    // Director-only
    Route::middleware('role:director')->group(function () {
        Route::get('/company', fn () => view('dash.profile.company'))->name('profile.company');
    });
});
