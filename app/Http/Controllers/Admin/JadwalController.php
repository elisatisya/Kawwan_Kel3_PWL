<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'semua');
        $search = trim((string) $request->query('search', ''));

        $query = JadwalPelayanan::with('dokter')
            ->withCount('pemesanan')
            ->orderByDesc('tanggal')
            ->orderBy('waktu_mulai');

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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dokter_id'     => ['required', 'exists:users,id'],
            'tanggal'       => ['required', 'date'],
            'waktu_mulai'   => ['required'],
            'waktu_selesai' => ['required', 'after:waktu_mulai'],
            'kuota'         => ['nullable', 'integer', 'min:1', 'max:200'],
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

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

    public function updateStatus(Request $request, JadwalPelayanan $jadwal): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:tersedia,penuh,tutup'],
        ]);

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
