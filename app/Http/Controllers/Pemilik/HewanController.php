<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Hewan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HewanController extends Controller
{
    public function index(Request $request): View
    {
        $daftarHewan = $request->user()
            ->hewan()
            ->withCount('pemesanan')
            ->latest()
            ->get();

        return view('pemilik.hewan', [
            'daftarHewan' => $daftarHewan,
            'hewanEdit' => null,
        ]);
    }

    public function edit(Request $request, Hewan $hewan): View
    {
        $this->pastikanMilikPemilik($request, $hewan);

        $daftarHewan = $request->user()
            ->hewan()
            ->withCount('pemesanan')
            ->latest()
            ->get();

        return view('pemilik.hewan', [
            'daftarHewan' => $daftarHewan,
            'hewanEdit' => $hewan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jenis' => ['required', 'string', 'max:50'],
            'ras' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['jantan', 'betina'])],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
        ], [
            'jenis.required' => 'Jenis hewan wajib dipilih.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);

        $data['nama'] = $data['jenis'];

        $request->user()->hewan()->create($data);

        return redirect()
            ->route('pemilik.hewan.index')
            ->with('success', 'Data hewan berhasil ditambahkan.');
    }

    public function update(Request $request, Hewan $hewan): RedirectResponse
    {
        $this->pastikanMilikPemilik($request, $hewan);

        $data = $request->validate([
            'jenis' => ['required', 'string', 'max:50'],
            'ras' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['jantan', 'betina'])],
            'tanggal_lahir' => ['nullable', 'date', 'before_or_equal:today'],
        ], [
            'jenis.required' => 'Jenis hewan wajib dipilih.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh melebihi hari ini.',
        ]);

        $data['nama'] = $data['jenis'];

        $hewan->update($data);

        return redirect()
            ->route('pemilik.hewan.index')
            ->with('success', 'Data hewan berhasil diperbarui.');
    }

    public function destroy(Request $request, Hewan $hewan): RedirectResponse
    {
        $this->pastikanMilikPemilik($request, $hewan);

        $hewan->delete();

        return redirect()
            ->route('pemilik.hewan.index')
            ->with('success', 'Data hewan berhasil dihapus.');
    }

    private function pastikanMilikPemilik(Request $request, Hewan $hewan): void
    {
        abort_unless(
            $hewan->user_id === $request->user()->id,
            403,
            'Anda tidak memiliki akses ke data hewan ini.'
        );
    }
}
