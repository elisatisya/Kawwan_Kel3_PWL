<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use Illuminate\Http\Request;

class AntreanController extends Controller
{
    public function index()
    {
        $antrean = Antrean::with([
            'pemesanan.user',
            'pemesanan.hewan',
            'pemesanan.pemesananLayanan.layanan',
            'dokter',
        ])
        ->orderBy('tanggal')
        ->orderBy('nomor_antrean')
        ->get();

        return view('kader.antrean.index', compact('antrean'));
    }
}