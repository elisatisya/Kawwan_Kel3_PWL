<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $hariIni = now()->toDateString();

        // 1. Ringkasan Pengguna
        $totalPengguna = User::count();
        $menungguVerifikasiCount = User::where('status', User::STATUS_MENUNGGU)->count();
        $aktifCount = User::where('status', User::STATUS_AKTIF)->count();
        $ditolakCount = User::where('status', User::STATUS_DITOLAK)->count();
        $nonaktifCount = User::where('status', User::STATUS_NONAKTIF)->count();

        $totalPemilik = User::whereIn('role', [User::ROLE_PEMILIK, 'pemilik'])->count();
        $totalDokter = User::whereIn('role', [User::ROLE_DOKTER, 'dokter'])->count();
        $totalKader = User::where('role', User::ROLE_KADER)->count();

        // 2. Ringkasan Layanan & Antrean
        $totalHewan = Hewan::count();
        $jadwalAktif = JadwalPelayanan::where('status', 'tersedia')
            ->whereDate('tanggal', '>=', $hariIni)
            ->count();

        $pemesananHariIni = Pemesanan::with(['hewan', 'user', 'jadwalPelayanan'])
            ->whereHas('jadwalPelayanan', fn ($q) => $q->whereDate('tanggal', $hariIni))
            ->get();

        $antreanHariIniCount = $pemesananHariIni->count();
        $antreanMenungguCount = $pemesananHariIni->whereIn('status', ['menunggu', 'dikonfirmasi'])->count();
        $antreanSelesaiCount = $pemesananHariIni->where('status', 'selesai')->count();

        // 3. Prioritas Verifikasi Pengguna (Pengguna baru yang belum diverifikasi, urut dari yang terlama mendaftar)
        $prioritasVerifikasi = User::where('status', User::STATUS_MENUNGGU)
            ->withCount('hewan')
            ->orderBy('created_at', 'asc')
            ->take(6)
            ->get();

        // 4. Data Antrean Hari Ini untuk Tabel Operasional
        $antreanHariIni = $pemesananHariIni
            ->sortBy(fn ($p) => $p->jadwalPelayanan?->waktu_mulai ?? '00:00')
            ->values()
            ->take(5);

        // 5. Data Grafik Registrasi 7 Hari Terakhir
        $grafikHari = [];
        $grafikRegistrasi = [];
        $grafikVerifikasi = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $grafikHari[] = $date->format('d M');
            $grafikRegistrasi[] = User::whereDate('created_at', $date->toDateString())->count();
            $grafikVerifikasi[] = User::whereDate('verified_at', $date->toDateString())->count();
        }

        // 6. Data Komposisi Status & Role
        $komposisiStatus = [
            'labels' => ['Aktif', 'Menunggu', 'Ditolak', 'Nonaktif'],
            'data' => [$aktifCount, $menungguVerifikasiCount, $ditolakCount, $nonaktifCount],
        ];

        $komposisiRole = [
            'labels' => ['Pemilik Hewan', 'Dokter Hewan', 'Kader Puskesmas', 'Admin'],
            'data' => [
                $totalPemilik,
                $totalDokter,
                $totalKader,
                User::where('role', User::ROLE_ADMIN)->count(),
            ],
        ];

        $stat = [
            'total_pengguna' => $totalPengguna,
            'menunggu_verifikasi' => $menungguVerifikasiCount,
            'aktif' => $aktifCount,
            'ditolak' => $ditolakCount,
            'nonaktif' => $nonaktifCount,
            'total_pemilik' => $totalPemilik,
            'total_dokter' => $totalDokter,
            'total_kader' => $totalKader,
            'total_hewan' => $totalHewan,
            'jadwal_aktif' => $jadwalAktif,
            'antrean_hari_ini' => $antreanHariIniCount,
            'antrean_menunggu' => $antreanMenungguCount,
            'antrean_selesai' => $antreanSelesaiCount,
        ];

        $grafik = [
            'registrasi' => [
                'labels' => $grafikHari,
                'pendaftar' => $grafikRegistrasi,
                'terverifikasi' => $grafikVerifikasi,
            ],
            'status' => $komposisiStatus,
            'role' => $komposisiRole,
        ];

        return view('admin.dashboard', compact('stat', 'prioritasVerifikasi', 'antreanHariIni', 'grafik'));
    }
}
