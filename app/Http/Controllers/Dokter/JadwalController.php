<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    // Halaman jadwal: tab "saya", "semua" (semua dokter) dan "kalender" (mingguan)
    public function index(Request $request)
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'dokter_id' => ['nullable', 'integer'],
            'minggu' => ['nullable', 'date'],
        ]);

        $lihat = in_array($request->query('lihat'), ['semua', 'kalender'], true) ? $request->query('lihat') : 'saya';

        if ($lihat === 'kalender') {
            $awal = Carbon::parse($request->query('minggu', now()))->startOfWeek();
            $akhir = $awal->copy()->endOfWeek();

            $perTanggal = JadwalPelayanan::with('dokter')
                ->whereNotNull('dokter_id')
                ->whereDate('tanggal', '>=', $awal->toDateString())
                ->whereDate('tanggal', '<=', $akhir->toDateString())
                ->orderBy('waktu_mulai')
                ->get()
                ->groupBy(fn ($j) => $j->tanggal->toDateString());

            $hari = collect(range(0, 6))->map(fn ($i) => $awal->copy()->addDays($i));
            $mingguLalu = $awal->copy()->subWeek()->toDateString();
            $mingguDepan = $awal->copy()->addWeek()->toDateString();

            return view('dokter.jadwal', compact('lihat', 'awal', 'akhir', 'hari', 'perTanggal', 'mingguLalu', 'mingguDepan'));
        }

        if ($lihat === 'semua') {
            $dari = $request->query('dari', now()->toDateString());
            $sampai = $request->query('sampai', now()->addDays(14)->toDateString());
            $dokterId = $request->query('dokter_id');

            $semuaJadwal = JadwalPelayanan::with('dokter')
                ->withCount([
                    'pemesanan as terpakai' => fn ($q) => $q->whereNotIn('status', ['dibatalkan', 'tidak_hadir']),
                ])
                ->whereNotNull('dokter_id')
                ->whereDate('tanggal', '>=', $dari)
                ->whereDate('tanggal', '<=', $sampai)
                ->when($dokterId, fn ($q) => $q->where('dokter_id', $dokterId))
                ->orderBy('tanggal')
                ->orderBy('waktu_mulai')
                ->get()
                ->groupBy(fn ($j) => $j->tanggal->toDateString());

            $daftarDokter = User::whereIn('role', [User::ROLE_DOKTER, 'dokter'])
                ->where('status', User::STATUS_AKTIF)
                ->orderBy('name')
                ->get();

            return view('dokter.jadwal', compact('lihat', 'semuaJadwal', 'daftarDokter', 'dari', 'sampai', 'dokterId'));
        }

        $jadwal = JadwalPelayanan::where('dokter_id', auth()->id())
            ->withCount([
                'pemesanan as terpakai' => fn ($q) => $q->whereNotIn('status', ['dibatalkan', 'tidak_hadir']),
            ])
            ->orderByDesc('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(10);

        return view('dokter.jadwal', compact('lihat', 'jadwal'));
    }

    public function create()
    {
        return view('dokter.jadwal-form', [
            'jadwal' => new JadwalPelayanan,
            'jadwalLain' => $this->jadwalDokterLain(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request);
    }

    public function edit(JadwalPelayanan $jadwal)
    {
        abort_unless($jadwal->dokter_id === auth()->id(), 403);

        return view('dokter.jadwal-form', [
            'jadwal' => $jadwal,
            'jadwalLain' => $this->jadwalDokterLain(),
        ]);
    }

    public function update(Request $request, JadwalPelayanan $jadwal): RedirectResponse
    {
        abort_unless($jadwal->dokter_id === auth()->id(), 403);

        return $this->simpan($request, $jadwal);
    }

    public function destroy(JadwalPelayanan $jadwal): RedirectResponse
    {
        abort_unless($jadwal->dokter_id === auth()->id(), 403);

        if ($jadwal->pemesanan()->exists()) {
            return back()->with('error', 'Jadwal sudah punya booking, tidak bisa dihapus. Ubah status menjadi ditutup saja.');
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal pelayanan berhasil dihapus.');
    }

    // Simpan jadwal baru ($jadwal null) atau perbarui jadwal ($jadwal terisi)
    private function simpan(Request $request, ?JadwalPelayanan $jadwal = null): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['dokter_id'] = auth()->id();

        // 1) Bentrok dengan jadwal sendiri -> DITOLAK
        $bentrokSaya = $this->cariBentrok($data, $jadwal?->id, true);

        if ($bentrokSaya->isNotEmpty()) {
            return back()->withInput()->withErrors([
                'waktu_mulai' => 'Bentrok dengan jadwal Anda sendiri: '.$this->deskripsi($bentrokSaya, false).'.',
            ]);
        }

        // 2) Kuota tidak boleh lebih kecil dari booking yang sudah masuk (saat ubah)
        if ($jadwal) {
            $terpakai = $jadwal->pemesanan()
                ->whereNotIn('status', ['dibatalkan', 'tidak_hadir'])
                ->count();

            if ($data['kuota'] < $terpakai) {
                return back()->withInput()->withErrors([
                    'kuota' => "Kuota tidak boleh kurang dari booking yang sudah masuk ($terpakai).",
                ]);
            }
        }

        // 3) Bersamaan dengan dokter lain -> PERINGATAN, boleh lanjut kalau Simpan sekali lagi
        $bentrokLain = $this->cariBentrok($data, null, false);
        $tanda = $data['tanggal'].'|'.$data['waktu_mulai'].'|'.$data['waktu_selesai'];

        if ($bentrokLain->isNotEmpty() && $request->input('konfirmasi') !== $tanda) {
            return back()->withInput()
                ->with('peringatan', 'Jam ini bersamaan dengan jadwal dokter lain: '.$this->deskripsi($bentrokLain, true).'. Klik Simpan sekali lagi jika tetap ingin menyimpan.')
                ->with('butuh_konfirmasi', $tanda);
        }

        if ($jadwal) {
            $jadwal->update($data);
            $pesan = 'Jadwal pelayanan berhasil diperbarui.';
        } else {
            JadwalPelayanan::create($data);
            $pesan = 'Jadwal pelayanan berhasil ditambahkan.';
        }

        return redirect()->route('dokter.jadwal.index')->with('success', $pesan);
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['required', 'date_format:H:i', 'after:waktu_mulai'],
            'kuota' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(['tersedia', 'ditutup'])],
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'waktu_mulai.required' => 'Waktu mulai wajib diisi.',
            'waktu_selesai.required' => 'Waktu selesai wajib diisi.',
            'waktu_selesai.after' => 'Waktu selesai harus setelah waktu mulai.',
            'kuota.required' => 'Kuota pasien wajib diisi.',
            'kuota.integer' => 'Kuota harus berupa angka.',
            'kuota.min' => 'Kuota minimal 1 pasien.',
            'kuota.max' => 'Kuota maksimal 100 pasien per jadwal.',
        ]);
    }

    // Cari jadwal yang jamnya bertabrakan pada tanggal yang sama
    // $milikSaya = true  -> hanya jadwal saya
    // $milikSaya = false -> hanya jadwal dokter lain
    private function cariBentrok(array $data, ?int $abaikanId, bool $milikSaya)
    {
        return JadwalPelayanan::with('dokter')
            ->whereNotNull('dokter_id')
            ->when(
                $milikSaya,
                fn ($q) => $q->where('dokter_id', auth()->id()),
                fn ($q) => $q->where('dokter_id', '!=', auth()->id())
            )
            ->whereDate('tanggal', $data['tanggal'])
            ->when($abaikanId, fn ($q) => $q->where('id', '!=', $abaikanId))
            ->where('waktu_mulai', '<', $data['waktu_selesai'])
            ->where('waktu_selesai', '>', $data['waktu_mulai'])
            ->orderBy('waktu_mulai')
            ->get();
    }

    // Teks ringkas: "Rabu, 26 November 2026 pukul 08:00 - 11:00 WIB (drh. Rina Maharani)"
    private function deskripsi($koleksi, bool $denganDokter): string
    {
        return $koleksi->map(function ($j) use ($denganDokter) {
            $teks = Carbon::parse($j->tanggal)->locale('id')->translatedFormat('l, d F Y')
                .' pukul '.substr($j->waktu_mulai, 0, 5).' - '.substr($j->waktu_selesai, 0, 5).' WIB';

            if ($denganDokter && $j->dokter) {
                $teks .= ' ('.trim(($j->dokter->gelar_depan ?? '').' '.$j->dokter->name.' '.($j->dokter->gelar_belakang ?? '')).')';
            }

            return $teks;
        })->join('; ');
    }

    // Jadwal dokter lain 14 hari ke depan (info saja, untuk menghindari bentrok)
    private function jadwalDokterLain()
    {
        return JadwalPelayanan::with('dokter')
            ->whereNotNull('dokter_id')
            ->where('dokter_id', '!=', auth()->id())
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->whereDate('tanggal', '<=', now()->addDays(14)->toDateString())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->get();
    }
}