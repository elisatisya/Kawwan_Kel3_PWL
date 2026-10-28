<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran akun baru.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('pemilik.beranda');
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun pemilik hewan baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'min:16', 'max:20', 'unique:users,nik', 'regex:/^[0-9]+$/'],
            'nomor_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-()]+$/'],
            'alamat' => ['required', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK atau nomor identitas wajib diisi.',
            'nik.min' => 'NIK minimal terdiri dari 16 digit angka.',
            'nik.max' => 'NIK maksimal 20 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nik.regex' => 'NIK hanya boleh memuat angka.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'nik' => trim($validated['nik']),
            'nomor_telepon' => trim($validated['nomor_telepon']),
            'alamat' => trim($validated['alamat']),
            'email' => ! empty($validated['email']) ? trim($validated['email']) : null,
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_PEMILIK,
        ]);

        Auth::login($user);

        return redirect()->route('pemilik.beranda');
    }
}
