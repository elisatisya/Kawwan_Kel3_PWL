<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $daftarHewan = $user->hewan()->latest()->get();

        $pemesananAktif = $user->pemesanan()
            ->with(['hewan', 'jadwalPelayanan'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'sedang_dilayani'])
            ->latest()
            ->get();

        $riwayatPemeriksaan = $user->pemesanan()
            ->with(['hewan', 'jadwalPelayanan', 'pemeriksaan'])
            ->whereHas('pemeriksaan')
            ->latest()
            ->take(5)
            ->get();

        $jadwalTersedia = JadwalPelayanan::where('status', 'tersedia')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->take(4)
            ->get();

        $stat = [
            'total_hewan' => $daftarHewan->count(),
            'antrean_aktif' => $pemesananAktif->count(),
            'total_pemeriksaan' => $user->pemesanan()->whereHas('pemeriksaan')->count(),
            'jadwal_tersedia' => JadwalPelayanan::where('status', 'tersedia')->whereDate('tanggal', '>=', now()->toDateString())->count(),
        ];

        return view('pemilik.dashboard', compact(
            'user',
            'daftarHewan',
            'pemesananAktif',
            'riwayatPemeriksaan',
            'jadwalTersedia',
            'stat'
        ));
    }
}
