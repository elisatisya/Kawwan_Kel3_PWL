<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:pemilik_hewan'])->prefix('pemilik')->name('pemilik.')->group(function () {
    Route::get('/beranda', [LandingPageController::class, 'index'])->name('beranda');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Admin Dashboard (coming soon)';
    })->name('dashboard');
});

Route::middleware(['auth', 'role:dokter_hewan'])->prefix('dokter')->name('dokter.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Dokter Hewan Dashboard (coming soon)';
    })->name('dashboard');
});

Route::middleware(['auth', 'role:kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', function () {
        return 'Kader Dashboard (coming soon)';
    })->name('dashboard');
});
