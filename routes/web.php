<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForemanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\TrackController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'index'])->name('index');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate')->middleware('throttle:5,1');

// Register foreman (public — butuh kunci valid)
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store')->middleware('throttle:3,5');
Route::get('/register/check-username', [RegisterController::class, 'checkUsername'])->name('register.check-username')->middleware('throttle:30,1');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Serve private files (proof, receipts, avatars)
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
    // Hapus struk individual via AJAX
    Route::delete('/receipts/{receipt}', [StockInController::class, 'destroyReceipt'])->name('receipts.destroy');

    // Stok Gudang
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

    // Stok Keluar
    Route::get('/stock-out', [StockOutController::class, 'index'])->name('stock-out.index');
    Route::post('/stock-out', [StockOutController::class, 'store'])->name('stock-out.store');
    Route::get('/stock-out/history', [StockOutController::class, 'history'])->name('stock-out.history');

    // Profile (semua role)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.user');
    Route::post('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update-info');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/check-username', [ProfileController::class, 'checkUsername'])->name('profile.check-username')->middleware('throttle:30,1');
    Route::post('/profile/check-password', [ProfileController::class, 'checkCurrentPassword'])->name('profile.check-password')->middleware('throttle:10,1');

    // Director-only
    Route::middleware('role:director')->group(function () {
        Route::get('/track', [TrackController::class, 'index'])->name('track.index');

        // Data Mandor
        Route::get('/foreman', [ForemanController::class, 'index'])->name('foreman.index');
        Route::post('/foreman/generate-key', [ForemanController::class, 'generateKey'])->name('foreman.generate-key');
    });
});
