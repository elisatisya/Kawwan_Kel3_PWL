<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;

class JadwalController extends Controller
{
    public function index()
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
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(10);

        return view('kader.jadwal', compact('jadwal'));
    }
}
