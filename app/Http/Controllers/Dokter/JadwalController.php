<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{

    public function index()
    {
        $jadwal = JadwalPelayanan::where('dokter_id', auth()->id())
            ->withCount([
                'pemesanan as terpakai' => fn ($q) => $q->whereNotIn('status', ['dibatalkan', 'tidak_hadir']),
            ])
            ->orderByDesc('tanggal')
            ->orderBy('waktu_mulai')
            ->paginate(10);

        return view('dokter.jadwal', compact('jadwal'));
    }

    public function create()
    {
        return view('dokter.jadwal-form', ['jadwal' => new JadwalPelayanan]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['dokter_id'] = auth()->id();

        if ($this->bentrok($data)) {
            return back()->withInput()->withErrors([
                'waktu_mulai' => 'Waktu bentrok dengan jadwal Anda yang lain pada tanggal tersebut.',
            ]);
        }

        JadwalPelayanan::create($data);

        return redirect()->route('dokter.jadwal.index')
            ->with('success', 'Jadwal pelayanan berhasil ditambahkan.');
    }

    public function edit(JadwalPelayanan $jadwal)
    {
        abort_unless($jadwal->dokter_id === auth()->id(), 403);

        return view('dokter.jadwal-form', compact('jadwal'));
    }

    public function update(Request $request, JadwalPelayanan $jadwal): RedirectResponse
    {
        abort_unless($jadwal->dokter_id === auth()->id(), 403);

        $data = $this->validasi($request);
        $data['dokter_id'] = auth()->id();

        if ($this->bentrok($data, $jadwal->id)) {
            return back()->withInput()->withErrors([
                'waktu_mulai' => 'Waktu bentrok dengan jadwal Anda yang lain pada tanggal tersebut.',
            ]);
        }


        $terpakai = $jadwal->pemesanan()
            ->whereNotIn('status', ['dibatalkan', 'tidak_hadir'])
            ->count();

        if (! is_null($data['kuota']) && $data['kuota'] < $terpakai) {
            return back()->withInput()->withErrors([
                'kuota' => "Kuota tidak boleh kurang dari booking yang sudah masuk ($terpakai).",
            ]);
        }

        $jadwal->update($data);

        return redirect()->route('dokter.jadwal.index')
            ->with('success', 'Jadwal pelayanan berhasil diperbarui.');
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


    private function bentrok(array $data, ?int $abaikanId = null): bool
    {
        return JadwalPelayanan::where('dokter_id', auth()->id())
            ->whereDate('tanggal', $data['tanggal'])
            ->when($abaikanId, fn ($q) => $q->where('id', '!=', $abaikanId))
            ->where('waktu_mulai', '<', $data['waktu_selesai'])
            ->where('waktu_selesai', '>', $data['waktu_mulai'])
            ->exists();
    }
}