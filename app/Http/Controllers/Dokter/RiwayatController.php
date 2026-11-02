<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Hewan;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $pasien = Hewan::with(['pemilik', 'pemesanan.pemeriksaan'])
            ->whereHas('pemesanan.pemeriksaan')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama', 'ilike', "%{$q}%")
                        ->orWhereHas('pemilik', fn ($p) => $p->where('name', 'ilike', "%{$q}%"));
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('dokter.riwayat.index', compact('pasien', 'q'));
    }

    public function show(Hewan $hewan)
    {
        $hewan->load('pemilik');

        $riwayat = Pemeriksaan::with('pemesanan.jadwalPelayanan')
            ->whereHas('pemesanan', fn ($q) => $q->where('hewan_id', $hewan->id))
            ->latest()
            ->get();

        return view('dokter.riwayat.show', compact('hewan', 'riwayat'));
    }
}