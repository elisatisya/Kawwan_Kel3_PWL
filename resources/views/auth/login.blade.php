<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFFFF">

    <title>Login — KAWWAN</title>
    <meta name="description" content="Masuk ke portal layanan Puskeswan KAWWAN untuk mengakses data hewan dan reservasi antrean.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="auth-page">

    <header class="auth-header">
        <a href="{{ route('landing') }}" class="auth-back-btn" aria-label="Kembali ke beranda">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <a href="{{ route('landing') }}" class="auth-brand" aria-label="KAWWAN Home">
            <img
                src="{{ asset('images/logo-kawwan.png') }}"
                alt="Logo KAWWAN"
                class="auth-brand-logo"
            >
        </a>
    </header>

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
                <h1 class="auth-title">Login</h1>
                <p class="auth-subtitle">
                    Selamat datang kembali di KAWWAN.<br>
                    Masuk untuk mengakses layanan.
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

                <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="login" class="form-label">NIK / NIP</label>
                        <div class="input-wrapper @error('login') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-regular fa-id-card"></i>
                            </span>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                value="{{ old('login') }}"
                                required
                                autofocus
                                class="form-input"
                                placeholder="Masukkan NIK atau nomor identitas Anda"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper @error('password') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                class="form-input form-input-password"
                                placeholder="Masukkan password Anda"
                            >
                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-meta">
                        <label class="remember-label" for="remember">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <span>Ingat saya</span>
                        </label>

                        <a href="{{ route('landing') }}#layanan" class="forgot-link">
                            Lupa password?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>Masuk</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-divider"></div>

                <p class="auth-footer-text">
                    Belum punya akun? <a href="{{ route('register') }}" class="register-link">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </main>

</body>
</html>
