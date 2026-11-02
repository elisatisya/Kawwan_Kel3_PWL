<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Pemilik\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:pemilik_hewan,pemilik'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/beranda', [DashboardController::class, 'index'])->name('beranda');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Verifikasi Pengguna
    Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/{user}', [VerifikasiController::class, 'show'])->name('show');
        Route::post('/{user}/approve', [VerifikasiController::class, 'approve'])->name('approve');
        Route::post('/{user}/reject', [VerifikasiController::class, 'reject'])->name('reject');
        Route::post('/{user}/toggle', [VerifikasiController::class, 'toggleStatus'])->name('toggle');
        Route::post('/batch-approve', [VerifikasiController::class, 'batchApprove'])->name('batch-approve');
    });

    // Pengelolaan Pengguna
    Route::prefix('pengguna')->name('pengguna.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::patch('/{user}/role', [UserController::class, 'updateRole'])->name('role');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'role:dokter_hewan,dokter'])->prefix('dokter')->name('dokter.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dokter\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/riwayat', [App\Http\Controllers\Dokter\RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{hewan}', [App\Http\Controllers\Dokter\RiwayatController::class, 'show'])->name('riwayat.show');
    Route::get('/profil', [App\Http\Controllers\Dokter\ProfilController::class, 'edit'])->name('profil');
    Route::put('/profil', [App\Http\Controllers\Dokter\ProfilController::class, 'update'])->name('profil.update');
});

Route::middleware(['auth', 'role:kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Kader Dashboard (coming soon)';
    })->name('dashboard');
});