<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFFFF">

    <title>Daftar Akun — KAWWAN</title>
    <meta name="description" content="Buat akun untuk mulai menggunakan layanan Puskeswan KAWWAN.">

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
            <div class="auth-card auth-card-register">
                <h1 class="auth-title">Daftar Akun</h1>
                <p class="auth-subtitle">
                    Buat akun untuk mulai menggunakan<br>
                    layanan KAWWAN.
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

                <form method="POST" action="{{ route('register') }}" class="auth-form auth-form-register" novalidate>
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <div class="input-wrapper @error('name') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-regular fa-user"></i>
                            </span>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                class="form-input"
                                placeholder="Masukkan nama lengkap Anda"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nik" class="form-label">NIK</label>
                        <div class="input-wrapper @error('nik') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-regular fa-id-card"></i>
                            </span>
                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                value="{{ old('nik') }}"
                                required
                                maxlength="20"
                                class="form-input"
                                placeholder="Masukkan NIK atau nomor identitas Anda"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                        <div class="input-wrapper @error('nomor_telepon') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-solid fa-phone"></i>
                            </span>
                            <input
                                type="tel"
                                id="nomor_telepon"
                                name="nomor_telepon"
                                value="{{ old('nomor_telepon') }}"
                                required
                                class="form-input"
                                placeholder="Masukkan nomor telepon Anda"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alamat" class="form-label">Alamat</label>
                        <div class="input-wrapper @error('alamat') is-invalid @enderror">
                            <span class="input-icon">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <input
                                type="text"
                                id="alamat"
                                name="alamat"
                                value="{{ old('alamat') }}"
                                required
                                class="form-input"
                                placeholder="Masukkan alamat Anda"
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
                                placeholder="Buat password Anda"
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
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
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
                                placeholder="Ulangi password Anda"
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

                    <button type="submit" class="btn-login btn-register">
                        <span>Daftar</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="auth-divider"></div>

                <p class="auth-footer-text">
                    Sudah punya akun? <a href="{{ route('login') }}" class="register-link">Login sekarang</a>
                </p>
            </div>
        </div>
    </main>

</body>
</html>
