<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    /**
     * Menampilkan detail pembayaran dan bukti transfer/QRIS.
     */
    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load([
            'user',
            'hewan',
            'jadwalPelayanan',
            'pembayaran.pencatat',
            'pemesananLayanan.layanan',
            'pemesananLayanan.dokter',
        ]);

        if (!$pemesanan->pembayaran) {
            return redirect()
                ->route('kader.pemesanan.index')
                ->withErrors([
                    'pembayaran' => 'Pemilik belum mengunggah bukti pembayaran.',
                ]);
        }

        return view('kader.pembayaran-show', [
            'pemesanan' => $pemesanan,
            'pembayaran' => $pemesanan->pembayaran,
        ]);
    }

    /**
     * Memverifikasi pembayaran dan membuat antrean otomatis.
     */
    public function verifikasi(
        Request $request,
        Pemesanan $pemesanan
    ): RedirectResponse {
        DB::transaction(function () use ($request, $pemesanan) {

            $pembayaran = Pembayaran::where(
                'pemesanan_id',
                $pemesanan->id
            )->lockForUpdate()->firstOrFail();

            if ($pembayaran->status !== 'menunggu_verifikasi') {
                abort(422, 'Pembayaran ini sudah diproses.');
            }

            $pemesanan->load([
                'jadwalPelayanan',
                'pemesananLayanan',
            ]);

            $tanggal = $pemesanan->jadwalPelayanan?->tanggal;

            if (!$tanggal) {
                abort(422, 'Jadwal pelayanan tidak ditemukan.');
            }

            $dokterIds = $pemesanan->pemesananLayanan
                ->pluck('dokter_id')
                ->unique()
                ->values();

            if ($dokterIds->isEmpty()) {
                abort(422, 'Belum ada dokter yang ditugaskan pada booking ini.');
            }

            // Verifikasi pembayaran.
            $pembayaran->update([
                'status' => 'lunas',
                'dicatat_oleh' => $request->user()->id,
                'dibayar_at' => now(),
                'catatan' => null,
            ]);

            // Buat satu antrean untuk setiap dokter.
            foreach ($dokterIds as $dokterId) {

                // Mengunci data dokter agar nomor antrean
                // tidak dibuat bersamaan untuk dokter yang sama.
                DB::table('users')
                    ->where('id', $dokterId)
                    ->lockForUpdate()
                    ->first();

                // Cegah antrean ganda.
                $sudahAda = Antrean::where('pemesanan_id', $pemesanan->id)
                    ->where('dokter_id', $dokterId)
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

                // Ambil nomor antrean terakhir dokter pada tanggal tersebut.
                $nomorTerakhir = Antrean::where('dokter_id', $dokterId)
                    ->whereDate('tanggal', $tanggal)
                    ->get()
                    ->map(function ($item) {
                        return (int) preg_replace(
                            '/\D/',
                            '',
                            $item->nomor_antrean
                        );
                    })
                    ->max() ?? 0;

                $nomorBaru = 'A' . str_pad(
                    $nomorTerakhir + 1,
                    3,
                    '0',
                    STR_PAD_LEFT
                );

                Antrean::create([
                    'pemesanan_id' => $pemesanan->id,
                    'dokter_id' => $dokterId,
                    'tanggal' => $tanggal,
                    'nomor_antrean' => $nomorBaru,
                    'status' => 'menunggu',
                ]);
            }
        });

        return redirect()
            ->route('kader.pemesanan.index')
            ->with(
                'success',
                'Pembayaran berhasil diverifikasi dan nomor antrean berhasil diterbitkan.'
            );
    }

    /**
     * Menolak bukti pembayaran.
     */
    public function tolak(
        Request $request,
        Pemesanan $pemesanan
    ): RedirectResponse {
        $data = $request->validate([
            'catatan' => [
                'required',
                'string',
                'max:500',
            ],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi.',
            'catatan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $pembayaran = Pembayaran::where(
            'pemesanan_id',
            $pemesanan->id
        )->firstOrFail();

        if ($pembayaran->status !== 'menunggu_verifikasi') {
            return back()->withErrors([
                'pembayaran' => 'Pembayaran ini sudah diproses.',
            ]);
        }

        $pembayaran->update([
            'status' => 'ditolak',
            'dicatat_oleh' => $request->user()->id,
            'dibayar_at' => null,
            'catatan' => $data['catatan'],
        ]);

        return redirect()
            ->route('kader.pemesanan.index')
            ->with('success', 'Bukti pembayaran berhasil ditolak.');
    }
}

