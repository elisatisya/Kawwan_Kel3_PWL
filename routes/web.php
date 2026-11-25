<?php

use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Admin Controllers
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerifikasiController;

// Pemilik Controllers
use App\Http\Controllers\Pemilik\DashboardController;
use App\Http\Controllers\Pemilik\HewanController as PemilikHewanController;
use App\Http\Controllers\Pemilik\JadwalController as PemilikJadwalController;
use App\Http\Controllers\Pemilik\PemesananController as PemilikPemesananController;
use App\Http\Controllers\Pemilik\ProfilController as PemilikProfilController; // BARU

// Dokter Controllers
use App\Http\Controllers\Dokter\JadwalController as DokterJadwalController;
use App\Http\Controllers\Dokter\PemeriksaanController;
use App\Http\Controllers\Dokter\ProfilController;
use App\Http\Controllers\Dokter\RiwayatController;

// Kader Controllers
use App\Http\Controllers\Kader\AntreanController;
use App\Http\Controllers\Kader\ArtikelController;
use App\Http\Controllers\Kader\JadwalController;
use App\Http\Controllers\Kader\LayananController;
use App\Http\Controllers\Kader\PembayaranController;
use App\Http\Controllers\Kader\PemesananController;

// LANDING PAGE
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// AUTENTIKASI
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

// LOGOUT
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// PEMILIK HEWAN
Route::middleware(['auth', 'role:pemilik_hewan,pemilik'])
    ->prefix('pemilik')
    ->name('pemilik.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/beranda', [DashboardController::class, 'index'])->name('beranda');

        // Data Hewan
        Route::get('/hewan', [PemilikHewanController::class, 'index'])->name('hewan.index');
        Route::post('/hewan', [PemilikHewanController::class, 'store'])->name('hewan.store');
        Route::get('/hewan/{hewan}/edit', [PemilikHewanController::class, 'edit'])->name('hewan.edit');
        Route::put('/hewan/{hewan}', [PemilikHewanController::class, 'update'])->name('hewan.update');
        Route::delete('/hewan/{hewan}', [PemilikHewanController::class, 'destroy'])->name('hewan.destroy');

        // Jadwal & Layanan Pelayanan
        Route::get('/jadwal', [PemilikJadwalController::class, 'index'])->name('jadwal.index');

        // Pemesanan Antrean
        Route::get('/pemesanan', [PemilikPemesananController::class, 'index'])->name('pemesanan.index');
        Route::post('/pemesanan', [PemilikPemesananController::class, 'store'])->name('pemesanan.store');
        Route::post('/pemesanan/{pemesanan}/pembayaran', [PemilikPemesananController::class, 'bayar'])->name('pemesanan.bayar');
        Route::patch('/pemesanan/{pemesanan}/batal', [PemilikPemesananController::class, 'batal'])->name('pemesanan.batal');

        // Profil (BARU)
        Route::get('/profil', [PemilikProfilController::class, 'edit'])->name('profil');
        Route::put('/profil', [PemilikProfilController::class, 'update'])->name('profil.update');

    });

// ADMIN
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

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

// DOKTER
Route::middleware(['auth', 'role:dokter_hewan,dokter'])
    ->prefix('dokter')
    ->name('dokter.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Dokter\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/riwayat', [RiwayatController::class, 'index'])
            ->name('riwayat.index');

        Route::get('/riwayat/{hewan}', [RiwayatController::class, 'show'])
            ->name('riwayat.show');

        Route::get('/profil', [ProfilController::class, 'edit'])
            ->name('profil');

        Route::put('/profil', [ProfilController::class, 'update'])
            ->name('profil.update');

        // Jadwal praktik milik dokter
        Route::get('/jadwal', [DokterJadwalController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [DokterJadwalController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [DokterJadwalController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{jadwal}/edit', [DokterJadwalController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{jadwal}', [DokterJadwalController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [DokterJadwalController::class, 'destroy'])->name('jadwal.destroy');

        // Pemeriksaan (antrean pasien)
        Route::get('/pemeriksaan', [PemeriksaanController::class, 'index'])->name('pemeriksaan.index');
        Route::get('/pemeriksaan/{antrean}', [PemeriksaanController::class, 'show'])->name('pemeriksaan.show');
        Route::get('/pemeriksaan/{antrean}/cetak', [PemeriksaanController::class, 'cetak'])->name('pemeriksaan.cetak');
        Route::patch('/pemeriksaan/{antrean}/mulai', [PemeriksaanController::class, 'mulai'])->name('pemeriksaan.mulai');
        Route::post('/pemeriksaan/{antrean}', [PemeriksaanController::class, 'simpan'])->name('pemeriksaan.simpan');

    });

// KADER
Route::middleware(['auth', 'role:kader'])
    ->prefix('kader')
    ->name('kader.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Kader\DashboardController::class, 'index'])
            ->name('dashboard');

        // Jadwal Pelayanan (Kader hanya memantau jadwal dokter)
        Route::get('/jadwal', [JadwalController::class, 'index'])
            ->name('jadwal.index');

        // Pemesanan
        Route::get('/pemesanan', [PemesananController::class, 'index'])
            ->name('pemesanan.index');

        Route::get('/pemesanan/create', [PemesananController::class, 'create'])
            ->name('pemesanan.create');

        Route::post('/pemesanan', [PemesananController::class, 'store'])
            ->name('pemesanan.store');

        Route::patch('/pemesanan/{pemesanan}/status', [PemesananController::class, 'updateStatus'])
            ->name('pemesanan.status');

        // Antrean
        Route::get('/antrean', [AntreanController::class, 'index'])
            ->name('antrean.index');

        // Pembayaran dan Verifikasi
        Route::get('/pemesanan/{pemesanan}/pembayaran', [PembayaranController::class, 'show'])
            ->name('pembayaran.show');

        Route::patch('/pemesanan/{pemesanan}/pembayaran/verifikasi', [PembayaranController::class, 'verifikasi'])
            ->name('pembayaran.verifikasi');

        Route::patch('/pemesanan/{pemesanan}/pembayaran/tolak', [PembayaranController::class, 'tolak'])
            ->name('pembayaran.tolak');

        // Pengelolaan Layanan dan Harga
        Route::resource('layanan', LayananController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        Route::patch('/layanan/{layanan}/status', [LayananController::class, 'toggleStatus'])
            ->name('layanan.status');

        // Artikel
        Route::resource('artikel', ArtikelController::class)
            ->except('show');

        Route::patch('/artikel/{artikel}/ajukan', [ArtikelController::class, 'ajukan'])
            ->name('artikel.ajukan');

        // Profil
        Route::get('/profil', [App\Http\Controllers\Kader\ProfilController::class, 'edit'])
            ->name('profil');

        Route::put('/profil', [App\Http\Controllers\Kader\ProfilController::class, 'update'])
            ->name('profil.update');

    });