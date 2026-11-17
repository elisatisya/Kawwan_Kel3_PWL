<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Pemeriksaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    
    public function index(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());

        $antrean = Antrean::with([
            'pemesanan.hewan',
            'pemesanan.user',
            'pemesanan.pemesananLayanan.layanan',
            'pemesanan.pemeriksaan',
        ])
            ->where('dokter_id', auth()->id())
            ->whereDate('tanggal', $tanggal)
            ->orderBy('nomor_antrean')
            ->get();

        return view('dokter.pemeriksaan', compact('antrean', 'tanggal'));
    }

   
    public function show(Antrean $antrean)
    {
        $this->pastikanMilikSaya($antrean);

        $antrean->load([
            'pemesanan.hewan.pemilik',
            'pemesanan.user',
            'pemesanan.pemesananLayanan.layanan',
            'pemesanan.pemeriksaan',
        ]);

        return view('dokter.pemeriksaan-form', compact('antrean'));
    }

    
    public function mulai(Antrean $antrean): RedirectResponse
    {
        $this->pastikanMilikSaya($antrean);

        $pemesanan = $antrean->pemesanan;

        if ($pemesanan->status !== 'dikonfirmasi' && $pemesanan->status !== 'sedang_dilayani') {
            return back()->with('error', 'Pasien belum dikonfirmasi kehadirannya oleh Kader.');
        }

        DB::transaction(function () use ($antrean, $pemesanan) {
            $antrean->update(['status' => 'sedang_dilayani']);
            $pemesanan->update(['status' => 'sedang_dilayani']);
        });

        return redirect()->route('dokter.pemeriksaan.show', $antrean)
            ->with('success', 'Pemeriksaan dimulai.');
    }

   
    public function simpan(Request $request, Antrean $antrean): RedirectResponse
    {
        $this->pastikanMilikSaya($antrean);

        if ($antrean->status !== 'sedang_dilayani') {
            return back()->with('error', 'Mulai pemeriksaan terlebih dahulu.');
        }

        $data = $request->validate([
            'keluhan' => ['required', 'string', 'max:2000'],
            'hasil_pemeriksaan' => ['required', 'string', 'max:3000'],
            'tindakan' => ['required', 'string', 'max:3000'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ], [
            'keluhan.required' => 'Keluhan wajib diisi.',
            'hasil_pemeriksaan.required' => 'Hasil pemeriksaan wajib diisi.',
            'tindakan.required' => 'Tindakan wajib diisi.',
        ]);

        DB::transaction(function () use ($antrean, $data) {
            $pemesanan = $antrean->pemesanan;

            Pemeriksaan::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                $data
            );

            $antrean->update(['status' => 'selesai']);

           
            $masihAda = $pemesanan->antrean()->where('status', '!=', 'selesai')->exists();

            $pemesanan->update([
                'status' => $masihAda ? 'sedang_dilayani' : 'selesai',
            ]);
        });

        return redirect()->route('dokter.pemeriksaan.index')
            ->with('success', 'Hasil pemeriksaan berhasil disimpan.');
    }

    private function pastikanMilikSaya(Antrean $antrean): void
    {
        abort_unless($antrean->dokter_id === auth()->id(), 403);
    }
}