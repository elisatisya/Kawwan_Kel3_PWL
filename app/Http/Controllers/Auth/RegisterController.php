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
     public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('pemilik.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'min:16', 'max:20', 'unique:users,nik', 'regex:/^[0-9]+$/'],
            'nomor_telepon' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-()]+$/'],
            'alamat' => ['required', 'string', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK atau nomor identitas wajib diisi.',
            'nik.min' => 'NIK minimal terdiri dari 16 digit.',
            'nik.max' => 'NIK maksimal 20 digit.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'nik.regex' => 'NIK hanya boleh memuat angka.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => trim($validated['name']),
            'nik' => trim($validated['nik']),
            'nomor_telepon' => trim($validated['nomor_telepon']),
            'alamat' => trim($validated['alamat']),
            'email' => strtolower(trim($validated['email'])),
            'password' => $validated['password'],
            'role' => User::ROLE_PEMILIK,
            'status' => User::STATUS_MENUNGGU,
        ]);

        return redirect()->route('login')->with(
            'success',
            'Pendaftaranmu sedang diverifikasi, mohon ditunggu dan segera cek email untuk melihat informasi.'
        );
    }
}
