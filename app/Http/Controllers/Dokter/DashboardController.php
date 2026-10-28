<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private const LABEL_STATUS = [
        'menunggu' => 'Menunggu',
        'dikonfirmasi' => 'Dikonfirmasi',
        'sedang_dilayani' => 'Sedang Dilayani',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
        'tidak_hadir' => 'Tidak Hadir',
    ];

    public function index()
    {
        $hariIni = now()->toDateString();

        $pemesananHariIni = Pemesanan::with(['hewan', 'user', 'jadwalPelayanan'])
            ->whereHas('jadwalPelayanan', fn ($q) => $q->whereDate('tanggal', $hariIni))
            ->get()
            ->sortBy(fn ($p) => $p->jadwalPelayanan->waktu_mulai)
            ->values();

        $antrean = $pemesananHariIni->map(function ($p) {
            $kode = $this->kodeStatus($p->status);

            return [
                'jam' => substr($p->jadwalPelayanan->waktu_mulai, 0, 5).' - '.substr($p->jadwalPelayanan->waktu_selesai, 0, 5).' WIB',
                'pemilik' => $p->user?->name ?? '-',
                'hewan' => $p->hewan?->nama ?? '-',
                'jenis' => $p->hewan?->jenis ?? '-',
                'ras' => $p->hewan?->ras ?: '-',
                'status_kode' => $kode,
                'status_label' => self::LABEL_STATUS[$kode] ?? ucfirst((string) $p->status),
            ];
        });

        $stat = [
            'pasien_hari_ini' => $antrean->count(),
            'menunggu_pemeriksaan' => $antrean->whereIn('status_kode', ['menunggu', 'dikonfirmasi'])->count(),
            'selesai_diperiksa' => $antrean->where('status_kode', 'selesai')->count(),
            'total_pemeriksaan' => Pemeriksaan::count(),
        ];

        $labelHari = [];
        $dataHari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labelHari[] = $tanggal->format('d M');
            $dataHari[] = Pemeriksaan::whereDate('created_at', $tanggal->toDateString())->count();
        }

        $labelHewan = "trim(hewan.jenis || ' ' || coalesce(hewan.ras, ''))";

        $hewanDiperiksa = DB::table('pemeriksaan')
            ->join('pemesanan', 'pemeriksaan.pemesanan_id', '=', 'pemesanan.id')
            ->join('hewan', 'pemesanan.hewan_id', '=', 'hewan.id')
            ->select(DB::raw("$labelHewan as label"), DB::raw('count(*) as total'))
            ->groupBy(DB::raw($labelHewan))
            ->orderByDesc('total')
            ->pluck('total', 'label');

        $grafik = [
            'harian' => ['labels' => $labelHari, 'data' => $dataHari],
            'hewan' => [
                'labels' => $hewanDiperiksa->keys()->values(),
                'data' => $hewanDiperiksa->values(),
            ],
        ];

        return view('dokter.dashboard', compact('stat', 'antrean', 'grafik'));
    }

    private function kodeStatus(?string $status): string
    {
        return Str::of((string) $status)->lower()->trim()->replace(' ', '_')->toString();
    }
}