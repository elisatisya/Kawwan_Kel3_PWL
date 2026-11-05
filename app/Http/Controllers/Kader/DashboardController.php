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
        $hariIni = now()->toDateString();

        $pemesananHariIni = Pemesanan::with(['hewan', 'user', 'jadwalPelayanan'])
            ->whereHas('jadwalPelayanan', fn ($q) => $q->whereDate('tanggal', $hariIni))
            ->get()
            ->sortBy(fn ($p) => $p->jadwalPelayanan->waktu_mulai)
            ->values();

        $pembayaran = Pembayaran::whereIn('pemesanan_id', $pemesananHariIni->pluck('id'))
            ->get()
            ->keyBy('pemesanan_id');

        $aktif = $pemesananHariIni->whereNotIn('status', ['dibatalkan', 'tidak_hadir']);

        $stat = [
            'booking_hari_ini' => $pemesananHariIni->count(),
            'menunggu_kedatangan' => $pemesananHariIni->whereIn('status', ['menunggu', 'dikonfirmasi'])->count(),
            'belum_dibayar' => $aktif->filter(fn ($p) => ! $pembayaran->has($p->id))->count(),
            'artikel_diajukan' => Artikel::where('user_id', auth()->id())->where('status', 'diajukan')->count(),
        ];

        $labelStatus = self::LABEL_STATUS;

        return view('kader.dashboard', compact('stat', 'pemesananHariIni', 'pembayaran', 'labelStatus'));
    }
}