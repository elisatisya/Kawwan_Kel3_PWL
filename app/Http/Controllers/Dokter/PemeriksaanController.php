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
    // Antrean pasien milik dokter yang login (default: hari ini), bisa difilter status dan dicari
    public function index(Request $request)
    {
        $request->validate([
            'tanggal' => ['nullable', 'date'],
            'status' => ['nullable', 'in:menunggu,sedang_dilayani,selesai'],
        ]);

        $tanggal = $request->query('tanggal', now()->toDateString());
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));

        $antrean = Antrean::with([
            'pemesanan.hewan',
            'pemesanan.user',
            'pemesanan.pemesananLayanan.layanan',
            'pemesanan.pemeriksaan',
        ])
            ->where('dokter_id', auth()->id())
            ->whereDate('tanggal', $tanggal)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($q !== '', function ($query) use ($q) {
                $kw = '%'.strtolower($q).'%';

                $query->whereHas('pemesanan', function ($p) use ($kw) {
                    $p->where(function ($w) use ($kw) {
                        $w->whereHas('user', fn ($u) => $u->whereRaw('LOWER(name) LIKE ?', [$kw]))
                            ->orWhereHas('hewan', fn ($h) => $h->whereRaw('LOWER(jenis) LIKE ?', [$kw])
                                ->orWhereRaw('LOWER(COALESCE(ras, \'\')) LIKE ?', [$kw]));
                    });
                });
            })
            ->orderBy('nomor_antrean')
            ->get();

        return view('dokter.pemeriksaan', compact('antrean', 'tanggal', 'status', 'q'));
    }

    // Halaman form pemeriksaan untuk satu antrean
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

    // Halaman siap cetak hasil pemeriksaan
    public function cetak(Antrean $antrean)
    {
        $this->pastikanMilikSaya($antrean);

        $antrean->load([
            'pemesanan.hewan.pemilik',
            'pemesanan.user',
            'pemesanan.jadwalPelayanan',
            'pemesanan.pemesananLayanan.layanan',
            'pemesanan.pemeriksaan',
        ]);

        abort_unless($antrean->pemesanan->pemeriksaan, 404);

        return view('dokter.pemeriksaan-cetak', compact('antrean'));
    }

    // Tombol "Mulai Pemeriksaan": dikonfirmasi -> sedang_dilayani
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

    // Simpan hasil pemeriksaan: sedang_dilayani -> selesai
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

            // Pemesanan baru "selesai" kalau semua antrean dokter-nya selesai
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