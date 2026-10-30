<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'NIK atau nomor identitas wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim((string) $request->input('login'));
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        // Rate limiting: maksimal 5 percobaan per menit per identitas/IP
        $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'login' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('login');
        }

        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user);
        }

        // Coba alternatif (jika input berupa angka tapi didaftarkan sebagai email atau sebaliknya)
        $fallbackType = ($fieldType === 'email') ? 'nik' : 'email';
        if (Auth::attempt([$fallbackType => $loginInput, 'password' => $password], $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'login' => 'NIK / Nomor Identitas atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('login');
    }

    /**
     * Proses keluar sistem (logout).
     */
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

        $defaultUrl = route($defaultRoute);
        $intendedUrl = session()->pull('url.intended');

        if (! $intendedUrl || in_array(rtrim($intendedUrl, '/'), [rtrim(url('/'), '/'), rtrim(route('login'), '/')], true)) {
            return redirect()->to($defaultUrl);
        }

        return redirect()->to($intendedUrl);
    }
}
