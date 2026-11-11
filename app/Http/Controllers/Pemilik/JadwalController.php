<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\Layanan;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(): View
    {
        $jadwal = JadwalPelayanan::with([
            'dokter',
            'dokter.layananDokter',
        ])
            ->withCount([
                'pemesanan as terpakai' => function ($query) {
                    $query->whereNotIn('status', [
                        'dibatalkan',
                        'tidak_hadir',
                    ]);
                },
            ])
            ->where('status', 'tersedia')
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(8)
            ->withQueryString();

        $layanan = Layanan::with('dokter')
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();

        return view('pemilik.jadwal', compact('jadwal', 'layanan'));
    }
}
