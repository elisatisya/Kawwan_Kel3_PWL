<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::with('dokter')
            ->latest()
            ->paginate(10);

        return view('kader.layanan.index', compact('layanan'));
    }

    public function create()
    {
        $dokter = User::whereIn('role', [
            User::ROLE_DOKTER,
            'dokter'
        ])->orderBy('name')->get();

        return view('kader.layanan.create', compact('dokter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'biaya' => ['required', 'integer', 'min:0'],
            'durasi_menit' => ['nullable', 'integer', 'min:1'],
            'dokter_ids' => ['required', 'array', 'min:1'],
            'dokter_ids.*' => [
                'required',
                'distinct',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role', [
                        User::ROLE_DOKTER,
                        'dokter'
                    ]);
                }),
            ],
        ], [
            'dokter_ids.required' => 'Pilih minimal satu dokter.',
            'dokter_ids.min' => 'Pilih minimal satu dokter.',
            'dokter_ids.*.exists' => 'Dokter yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($data) {
            $dokterIds = $data['dokter_ids'];
            unset($data['dokter_ids']);

            $data['aktif'] = true;

            $layanan = Layanan::create($data);
            $layanan->dokter()->sync($dokterIds);
        });

        return redirect()
            ->route('kader.layanan.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        $dokter = User::whereIn('role', [
            User::ROLE_DOKTER,
            'dokter'
        ])->orderBy('name')->get();

        $layanan->load('dokter');

        return view('kader.layanan.edit', compact('layanan', 'dokter'));
    }

    public function update(Request $request, Layanan $layanan): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'biaya' => ['required', 'integer', 'min:0'],
            'durasi_menit' => ['nullable', 'integer', 'min:1'],
            'dokter_ids' => ['required', 'array', 'min:1'],
            'dokter_ids.*' => [
                'required',
                'distinct',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role', [
                        User::ROLE_DOKTER,
                        'dokter'
                    ]);
                }),
            ],
        ], [
            'dokter_ids.required' => 'Pilih minimal satu dokter.',
            'dokter_ids.min' => 'Pilih minimal satu dokter.',
            'dokter_ids.*.exists' => 'Dokter yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($data, $layanan) {
            $dokterIds = $data['dokter_ids'];
            unset($data['dokter_ids']);

            $layanan->update($data);
            $layanan->dokter()->sync($dokterIds);
        });

        return redirect()
            ->route('kader.layanan.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function toggleStatus(Layanan $layanan): RedirectResponse
    {
        $layanan->update([
            'aktif' => !$layanan->aktif,
        ]);

        return redirect()
            ->route('kader.layanan.index')
            ->with(
                'success',
                $layanan->aktif
                    ? 'Layanan berhasil diaktifkan.'
                    : 'Layanan berhasil dinonaktifkan.'
            );
    }
}
