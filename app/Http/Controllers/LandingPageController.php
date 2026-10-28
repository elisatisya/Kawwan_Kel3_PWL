<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $jumlahHewan = $user ? Hewan::where('user_id', $user->id)->count() : 0;

        $jadwalMendatang = JadwalPelayanan::where('status', 'tersedia')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->first();

        $pemesananTerbaru = $user ? Pemesanan::where('user_id', $user->id)
            ->with(['hewan', 'jadwalPelayanan'])
            ->latest()
            ->first() : null;

        $jumlahPemeriksaan = $user ? Pemesanan::where('user_id', $user->id)
            ->whereHas('pemeriksaan')
            ->count() : 0;

        $artikelTerbaru = Artikel::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('landing_page', compact(
            'user',
            'jumlahHewan',
            'jadwalMendatang',
            'pemesananTerbaru',
            'jumlahPemeriksaan',
            'artikelTerbaru',
        ));
    }
}
