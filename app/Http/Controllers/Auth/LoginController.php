<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan formulir login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user);
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $email = Str::lower(trim($request->input('email')));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Rate limiting: maksimal 5 percobaan per menit per identitas/IP
        $throttleKey = Str::transliterate($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('email');
        }

        if (Auth::attempt([
            'email' => $email,
            'password' => $password,
        ], $remember)) {

            $user = Auth::user();

            if ($user->status === User::STATUS_NONAKTIF) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        $defaultRoute = match ($user->role) {
            User::ROLE_ADMIN => 'admin.dashboard',
            User::ROLE_DOKTER, 'dokter' => 'dokter.dashboard',
            User::ROLE_KADER => 'kader.dashboard',
            default => 'pemilik.dashboard',
        };

        return redirect()->route($defaultRoute);
    }
}