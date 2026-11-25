<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PemesananController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'semua');
        $search = trim((string) $request->query('search', ''));

        $query = Pemesanan::with(['user', 'hewan', 'jadwalPelayanan.dokter', 'pemesananLayanan.layanan', 'pembayaran', 'antrean'])
            ->latest();

        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('hewan', fn ($h) => $h->where('jenis', 'like', "%{$search}%")->orWhere('ras', 'like', "%{$search}%"))
                  ->orWhereHas('antrean', fn ($a) => $a->where('nomor_antrean', 'like', "%{$search}%"));
            });
        }

        $pemesananList = $query->paginate(12)->withQueryString();

        $stat = [
            'total'     => Pemesanan::count(),
            'hari_ini'  => Pemesanan::whereHas('jadwalPelayanan', fn ($q) => $q->whereDate('tanggal', now()->toDateString()))->count(),
            'menunggu'  => Pemesanan::whereIn('status', ['menunggu', 'dikonfirmasi'])->count(),
            'selesai'   => Pemesanan::where('status', 'selesai')->count(),
        ];

        return view('admin.pemesanan', compact('pemesananList', 'stat', 'statusFilter', 'search'));
    }

    public function updateStatus(Request $request, Pemesanan $pemesanan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,dikonfirmasi,sedang_dilayani,selesai,dibatalkan,tidak_hadir'],
        ]);

        $pemesanan->update(['status' => $validated['status']]);

        return back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }
}
