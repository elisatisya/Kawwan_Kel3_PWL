<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan formulir pengaturan ulang kata sandi.
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Proses pembaruan kata sandi pengguna.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'Token reset password tidak valid atau tidak ditemukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');

        // Gunakan broker bawaan Laravel Password::reset
        $status = Password::reset($credentials, function (User $user, string $password) {
            // Perbarui hanya password dan remember_token.
            // Kolom 'status' dan 'role' TIDAK DIUBAH agar hak akses dan status verifikasi tetap utuh.
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.')
                ->with('success', 'Password Anda berhasil diperbarui! Silakan masuk menggunakan password baru.');
        }

        return back()->withErrors([
            'email' => 'Tautan reset password ini tidak valid atau sudah kedaluwarsa. Silakan ajukan tautan baru.',
        ])->onlyInput('email');
    }
}
