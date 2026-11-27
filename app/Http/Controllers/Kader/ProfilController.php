<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('kader.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'hapus_foto' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        unset($validated['foto'], $validated['hapus_foto']);

        if ($request->hasFile('foto')) {
            // Ganti foto: hapus yang lama, simpan yang baru
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto-kader', 'public');
        } elseif ($request->boolean('hapus_foto') && $user->foto) {
            // Hapus foto: buang file dan kosongkan kolomnya
            Storage::disk('public')->delete($user->foto);
            $validated['foto'] = null;
        }

        $user->update($validated);

        return redirect()
            ->route('kader.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}