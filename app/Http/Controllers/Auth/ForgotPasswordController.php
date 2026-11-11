<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $email = Str::lower(trim((string) $request->input('email')));

        // Pembatasan permintaan (throttling): maksimal 3 kali per 2 menit
        $throttleKey = 'forgot-password|'.Str::transliterate($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Terlalu banyak permintaan reset password. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        RateLimiter::hit($throttleKey, 120);

        // Kirim tautan reset menggunakan broker bawaan Laravel
        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'Mohon tunggu beberapa saat sebelum meminta tautan reset kembali.',
            ])->onlyInput('email');
        }

        // Pesan seragam untuk keamanan (tidak membocorkan apakah email terdaftar atau tidak)
        return back()->with(
            'status',
            'Tautan untuk mengatur ulang password telah dikirim ke email Anda jika terdaftar dalam sistem.'
        );
    }
}
