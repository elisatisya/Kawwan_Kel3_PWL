<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        JadwalPelayanan::tutupJadwalKadaluarsa();

        $statusFilter = $request->query('status', 'semua');
        $search = trim((string) $request->query('search', ''));

        $query = JadwalPelayanan::with('dokter')
            ->withCount('pemesanan')
            ->orderByDesc('tanggal')
            ->orderByDesc('waktu_mulai')
            ->orderByDesc('id');

        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tanggal', 'like', "%{$search}%")
                  ->orWhereHas('dokter', fn ($d) => $d->where('name', 'like', "%{$search}%"));
            });
        }

        $jadwalList = $query->paginate(10)->withQueryString();

        $dokterList = User::whereIn('role', [User::ROLE_DOKTER, 'dokter'])
            ->where('status', User::STATUS_AKTIF)
            ->orderBy('name')
            ->get();

        $stat = [
            'total'     => JadwalPelayanan::count(),
            'tersedia'  => JadwalPelayanan::where('status', 'tersedia')->count(),
            'penuh'     => JadwalPelayanan::where('status', 'penuh')->count(),
            'mendatang' => JadwalPelayanan::whereDate('tanggal', '>=', now()->toDateString())->count(),
        ];

        return view('admin.jadwal', compact('jadwalList', 'dokterList', 'stat', 'statusFilter', 'search'));
    }

    private function isWaktuSudahLewat(string $tanggal, string $waktuMulai): bool
    {
        return Carbon::parse($tanggal . ' ' . $waktuMulai)->lt(now());
    }

    private function hasBentrokJadwal(
        int $dokterId,
        string $tanggal,
        string $waktuMulai,
        string $waktuSelesai,
        ?int $ignoreId = null
    ): bool {
        $mulai = strlen($waktuMulai) === 5 ? $waktuMulai . ':00' : $waktuMulai;
        $selesai = strlen($waktuSelesai) === 5 ? $waktuSelesai . ':00' : $waktuSelesai;

        return JadwalPelayanan::where('dokter_id', $dokterId)
            ->whereDate('tanggal', $tanggal)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('waktu_mulai', '<', $selesai)
            ->where('waktu_selesai', '>', $mulai)
            ->exists();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dokter_id'     => ['required', 'exists:users,id'],
            'tanggal'       => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai'   => ['required'],
            'waktu_selesai' => ['required', 'after:waktu_mulai'],
            'kuota'         => ['nullable', 'integer', 'min:1', 'max:200'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal praktik tidak boleh menggunakan tanggal yang sudah lewat.',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        if ($this->isWaktuSudahLewat($validated['tanggal'], $validated['waktu_mulai'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'waktu_mulai' => 'Jam mulai praktik tidak boleh menggunakan waktu yang sudah lewat.',
                ]);
        }

        if ($this->hasBentrokJadwal(
            (int) $validated['dokter_id'],
            $validated['tanggal'],
            $validated['waktu_mulai'],
            $validated['waktu_selesai']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'waktu_mulai' => 'Jadwal bentrok! Dokter yang dipilih sudah memiliki jadwal pada rentang waktu tersebut.',
                ]);
        }

        JadwalPelayanan::create([
            'dokter_id'     => $validated['dokter_id'],
            'tanggal'       => $validated['tanggal'],
            'waktu_mulai'   => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kuota'         => $validated['kuota'] ?: null,
            'status'        => 'tersedia',
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelayanan baru berhasil ditambahkan.');
    }

    public function update(Request $request, JadwalPelayanan $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'dokter_id'     => ['required', 'exists:users,id'],
            'tanggal'       => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai'   => ['required'],
            'waktu_selesai' => ['required', 'after:waktu_mulai'],
            'kuota'         => ['nullable', 'integer', 'min:1', 'max:200'],
            'status'        => ['required', 'in:tersedia,penuh,tutup'],
        ], [
            'tanggal.after_or_equal' => 'Tanggal praktik tidak boleh menggunakan tanggal yang sudah lewat.',
            'waktu_selesai.after'    => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        if ($this->isWaktuSudahLewat($validated['tanggal'], $validated['waktu_mulai'])) {
            return back()
                ->withInput()
                ->with('edit_jadwal_id', $jadwal->id)
                ->withErrors([
                    'waktu_mulai' => 'Jam mulai praktik tidak boleh menggunakan waktu yang sudah lewat.',
                ]);
        }

        if ($this->hasBentrokJadwal(
            (int) $validated['dokter_id'],
            $validated['tanggal'],
            $validated['waktu_mulai'],
            $validated['waktu_selesai'],
            $jadwal->id
        )) {
            return back()
                ->withInput()
                ->with('edit_jadwal_id', $jadwal->id)
                ->withErrors([
                    'waktu_mulai' => 'Jadwal bentrok! Dokter yang dipilih sudah memiliki jadwal lain pada rentang waktu tersebut.',
                ]);
        }

        $jadwal->update([
            'dokter_id'     => $validated['dokter_id'],
            'tanggal'       => $validated['tanggal'],
            'waktu_mulai'   => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kuota'         => $validated['kuota'] ?: null,
            'status'        => $validated['status'],
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal pelayanan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, JadwalPelayanan $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:tersedia,penuh,tutup'],
        ]);

        if ($validated['status'] !== 'tutup' && $this->isWaktuSudahLewat($jadwal->tanggal->toDateString(), $jadwal->waktu_selesai)) {
            $jadwal->update(['status' => 'tutup']);

            return back()->with('error', 'Jadwal yang sudah melewati jam selesai praktik otomatis ditutup dan tidak dapat dibuka kembali.');
        }

        $jadwal->update(['status' => $validated['status']]);

        return back()->with('success', 'Status jadwal pelayanan berhasil diperbarui.');
    }

    public function destroy(JadwalPelayanan $jadwal): RedirectResponse
    {
        if ($jadwal->pemesanan()->whereIn('status', ['menunggu', 'dikonfirmasi', 'sedang_dilayani'])->exists()) {
            return back()->with('error', 'Jadwal tidak dapat dihapus karena masih memiliki pemesanan aktif.');
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal pelayanan berhasil dihapus.');
    }
}
