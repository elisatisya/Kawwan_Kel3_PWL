<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFFFF">

    <title>Reset Password — KAWWAN</title>
    <meta name="description" content="Buat password baru yang aman untuk akun Puskeswan KAWWAN Anda.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="auth-page">
    <main class="auth-container">
        <div class="auth-illustration-wrap">
            <img
                src="{{ asset('images/klinik-hewan.png') }}"
                alt="Ilustrasi Klinik Hewan KAWWAN"
                class="auth-illustration-img"
            >
        </div>

        <div class="auth-card-wrap">
            <div class="auth-card">
                <a href="{{ route('login') }}" class="auth-back-btn" aria-label="Kembali ke halaman login">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <h1 class="auth-title">Reset Password</h1>
                <p class="auth-subtitle">
                    Buat password baru untuk akun Anda.<br>
                    Pastikan password baru minimal 8 karakter.
                </p>

                @if ($errors->any())
                    <div class="auth-alert" role="alert">
                        <i class="fa-solid fa-circle-exclamation auth-alert-icon"></i>
                        <div class="auth-alert-content">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="auth-alert auth-alert-success" role="alert">
                        <i class="fa-solid fa-circle-check auth-alert-icon"></i>
                        <div class="auth-alert-content">
                            <p>{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="auth-form" novalidate>
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-wrapper @error('email') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ $email ?? old('email') }}"
                                required
                                class="form-input"
                                placeholder="nama@email.com"
                                {{ !empty($email) ? 'readonly' : '' }}
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password Baru</label>
                        <div class="input-wrapper @error('password') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autofocus
                                class="form-input form-input-password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <div class="input-wrapper @error('password_confirmation') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                class="form-input form-input-password"
                                placeholder="Ulangi password baru"
                                autocomplete="new-password"
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password_confirmation"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password"
                            >
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>Simpan Password Baru</span>
                        <i class="fa-solid fa-check"></i>
                    </button>
                </form>

                <div class="auth-divider"></div>

                <p class="auth-footer-text">
                    Sudah ingat password? <a href="{{ route('login') }}" class="register-link">Kembali ke Login</a>
                </p>
            </div>
        </div>
    </main>

</body>
</html>
