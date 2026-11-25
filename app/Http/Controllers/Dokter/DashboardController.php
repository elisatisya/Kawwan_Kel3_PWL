<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Antrean;
use App\Models\Pemeriksaan;
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
        $dokterId = auth()->id();
        $hariIni = now()->toDateString();

        // Pemeriksaan milik dokter ini = pemeriksaan pada pemesanan yang punya antrean untuk dokter ini
        $milikSaya = fn ($q) => $q->whereHas('pemesanan.antrean', fn ($a) => $a->where('dokter_id', $dokterId));

        $antrean = Antrean::with([
            'pemesanan.hewan',
            'pemesanan.user',
            'pemesanan.jadwalPelayanan',
            'pemesanan.pemesananLayanan.layanan',
        ])
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal', $hariIni)
            ->orderBy('nomor_antrean')
            ->get()
            ->map(function ($a) use ($dokterId) {
                $p = $a->pemesanan;
                $j = $p->jadwalPelayanan;
                $kode = $this->kodeStatus($a->status);

                return [
                    'id' => $a->id,
                    'nomor' => $a->nomor_antrean,
                    'jam' => $j ? substr($j->waktu_mulai, 0, 5).' - '.substr($j->waktu_selesai, 0, 5).' WIB' : '-',
                    'pemilik' => $p->user?->name ?? '-',
                    'jenis' => $p->hewan?->jenis ?? '-',
                    'ras' => $p->hewan?->ras ?: '-',
                    'layanan' => $p->pemesananLayanan
                        ->where('dokter_id', $dokterId)
                        ->map(fn ($l) => $l->layanan?->nama)
                        ->filter()
                        ->join(', ') ?: '-',
                    'status_kode' => $kode,
                    'status_label' => self::LABEL_STATUS[$kode] ?? ucfirst((string) $a->status),
                ];
            });

        $stat = [
            'pasien_hari_ini' => $antrean->count(),
            'menunggu_pemeriksaan' => $antrean->whereIn('status_kode', ['menunggu', 'dikonfirmasi'])->count(),
            'selesai_diperiksa' => $antrean->where('status_kode', 'selesai')->count(),
            'total_pemeriksaan' => $milikSaya(Pemeriksaan::query())->count(),
        ];

        $labelHari = [];
        $dataHari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $labelHari[] = $tanggal->format('d M');
            $dataHari[] = $milikSaya(Pemeriksaan::query())
                ->whereDate('created_at', $tanggal->toDateString())
                ->count();
        }

        $labelHewan = "trim(hewan.jenis || ' ' || coalesce(hewan.ras, ''))";

        $hewanDiperiksa = DB::table('pemeriksaan')
            ->join('pemesanan', 'pemeriksaan.pemesanan_id', '=', 'pemesanan.id')
            ->join('antrean', function ($join) use ($dokterId) {
                $join->on('antrean.pemesanan_id', '=', 'pemesanan.id')
                    ->where('antrean.dokter_id', '=', $dokterId);
            })
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