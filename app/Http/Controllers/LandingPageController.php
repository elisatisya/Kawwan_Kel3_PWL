<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\Hewan;
use App\Models\JadwalPelayanan;
use App\Models\Layanan;
use App\Models\Pemesanan;
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

        // Ambil 3 artikel terbaru yang sudah di-publish oleh Kader
        $artikelTerbaru = Artikel::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $layananList = Layanan::orderBy('id')->get();

        if ($layananList->isEmpty()) {
            $layananList = collect([
                (object) [
                    'id' => 1,
                    'nama' => 'Pemeriksaan Kesehatan Umum',
                    'deskripsi' => 'Pemeriksaan fisik menyeluruh untuk mengetahui kondisi kesehatan umum anabul Anda.',
                    'durasi_menit' => 30,
                    'biaya' => 0,
                ],
                (object) [
                    'id' => 2,
                    'nama' => 'Vaksinasi',
                    'deskripsi' => 'Pemberian vaksin rabies, tricat, tetracat, dan lainnya untuk mencegah penyakit menular.',
                    'durasi_menit' => 30,
                    'biaya' => 75000,
                ],
                (object) [
                    'id' => 3,
                    'nama' => 'Sterilisasi',
                    'deskripsi' => 'Tindakan bedah sterilisasi kucing atau anjing untuk mengontrol populasi dan menjaga kesehatan.',
                    'durasi_menit' => 60,
                    'biaya' => 250000,
                ],
                (object) [
                    'id' => 4,
                    'nama' => 'Pengobatan',
                    'deskripsi' => 'Penanganan dan pemberian resep obat untuk berbagai keluhan penyakit hewan kesayangan.',
                    'durasi_menit' => 45,
                    'biaya' => 150000,
                ],
                (object) [
                    'id' => 5,
                    'nama' => 'Pemberian Vitamin',
                    'deskripsi' => 'Pemberian suplemen dan vitamin untuk meningkatkan daya tahan tubuh dan nafsu makan hewan.',
                    'durasi_menit' => 15,
                    'biaya' => 50000,
                ],
                (object) [
                    'id' => 6,
                    'nama' => 'Konsultasi Kesehatan Hewan',
                    'deskripsi' => 'Konsultasi langsung dengan dokter hewan mengenai nutrisi, pola makan, dan perilaku anabul.',
                    'durasi_menit' => 20,
                    'biaya' => 0,
                ],
            ]);
        }

        return view('landing_page', compact(
            'user',
            'jumlahHewan',
            'jadwalMendatang',
            'pemesananTerbaru',
            'jumlahPemeriksaan',
            'artikelTerbaru',
            'layananList'
        ));
    }
}