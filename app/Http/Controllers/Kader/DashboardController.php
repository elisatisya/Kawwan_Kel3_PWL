<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Pembayaran;
use App\Models\Pemesanan;

class DashboardController extends Controller
{
    private const LABEL_STATUS = [
        'menunggu' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'sedang_dilayani' => 'Sedang Dilayani',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'tidak_hadir' => 'Tidak Hadir',
    ];

    public function index()
    {
        $hariIni  = now()->toDateString();
        $aktifTdk = ['dibatalkan', 'tidak_hadir'];

        // ── Pemesanan hari ini (untuk tabel bawah) ───────────────────────
        $pemesananHariIni = Pemesanan::with(['hewan', 'user', 'jadwalPelayanan'])
            ->whereHas('jadwalPelayanan', fn ($q) => $q->whereDate('tanggal', $hariIni))
            ->get()
            ->sortBy(fn ($p) => $p->jadwalPelayanan->waktu_mulai)
            ->values();

        // ── Pemesanan mendatang (60 hari ke depan, jika hari ini kosong) ──
        $pemesananMendatang = Pemesanan::with(['hewan', 'user', 'jadwalPelayanan'])
            ->whereHas('jadwalPelayanan', fn ($q) => $q
                ->whereDate('tanggal', '>', $hariIni)
                ->whereDate('tanggal', '<=', now()->addDays(60)->toDateString())
            )
            ->whereNotIn('status', $aktifTdk)
            ->get()
            ->sortBy(fn ($p) => [$p->jadwalPelayanan->tanggal, $p->jadwalPelayanan->waktu_mulai])
            ->values();

        // ── Ambil pembayaran untuk kedua kumpulan pemesanan ──────────────
        $allIds  = $pemesananHariIni->pluck('id')
            ->merge($pemesananMendatang->pluck('id'))
            ->unique();

        $pembayaran = Pembayaran::whereIn('pemesanan_id', $allIds)
            ->get()
            ->keyBy('pemesanan_id');

        // ── STAT GLOBAL (semua waktu, bukan hanya hari ini) ─────────────
        $totalAktif         = Pemesanan::whereNotIn('status', $aktifTdk)->count();
        $totalMenunggu      = Pemesanan::whereIn('status', ['menunggu', 'dikonfirmasi'])->count();
        $totalBelumBayarIds = Pemesanan::whereNotIn('status', $aktifTdk)->pluck('id');

        // "Belum dibayar" = tidak ada pembayaran SAMA SEKALI, atau pembayarannya ditolak
        $sudahBayarIds = Pembayaran::whereIn('pemesanan_id', $totalBelumBayarIds)
            ->whereIn('status', ['menunggu_verifikasi', 'lunas'])
            ->pluck('pemesanan_id');
        $totalBelumBayar = $totalBelumBayarIds->diff($sudahBayarIds)->count();

        $stat = [
            'booking_hari_ini'    => $pemesananHariIni->count(),       // tetap untuk info
            'total_aktif'         => $totalAktif,
            'menunggu_kedatangan' => $totalMenunggu,
            'belum_dibayar'       => max(0, $totalBelumBayar),            'artikel_diajukan'    => Artikel::where('status', 'diajukan')->count(),
        ];

        $labelStatus = self::LABEL_STATUS;

        return view('kader.dashboard', compact(
            'stat',
            'pemesananHariIni',
            'pemesananMendatang',
            'pembayaran',
            'labelStatus'
        ));
    }
}