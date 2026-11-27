<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Hewan;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    // Hanya pemesanan yang punya antrean untuk dokter yang login
    private function milikSaya(): \Closure
    {
        return fn ($q) => $q->whereHas('antrean', fn ($a) => $a->where('dokter_id', auth()->id()));
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $milikSaya = $this->milikSaya();

        $pasien = Hewan::with([
            'pemilik',
            'pemesanan' => fn ($p) => $milikSaya($p)->with('pemeriksaan'),
        ])
            ->whereHas('pemesanan', fn ($p) => $milikSaya($p)->whereHas('pemeriksaan'))
            ->when($q !== '', function ($query) use ($q) {
                $keyword = '%'.strtolower($q).'%';
                $query->where(function ($w) use ($keyword) {
                    $w->whereRaw('LOWER(jenis) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(COALESCE(ras, \'\')) LIKE ?', [$keyword])
                        ->orWhereHas('pemilik', fn ($p) => $p->whereRaw('LOWER(name) LIKE ?', [$keyword]));
                });
            })
            ->orderBy('jenis')
            ->paginate(10)
            ->withQueryString();

        return view('dokter.riwayat', compact('pasien', 'q'));
    }

    public function show(Hewan $hewan)
    {
        $hewan->load('pemilik');
        $milikSaya = $this->milikSaya();

        $riwayat = Pemeriksaan::with([
            'pemesanan.jadwalPelayanan',
            'pemesanan.antrean',
        ])
            ->whereHas('pemesanan', fn ($q) => $milikSaya($q->where('hewan_id', $hewan->id)))
            ->latest()
            ->get();

        return view('dokter.riwayat', compact('hewan', 'riwayat'));
    }
}