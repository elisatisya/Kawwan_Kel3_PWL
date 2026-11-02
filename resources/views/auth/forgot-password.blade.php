<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFFFFF">

    <title>Lupa Password — KAWWAN</title>
    <meta name="description" content="Atur ulang password akun Puskeswan KAWWAN Anda dengan memasukkan alamat email yang terdaftar.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="auth-page">

    <header class="auth-header">
        <a href="{{ route('login') }}" class="auth-back-btn" aria-label="Kembali ke halaman login">
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
                <h1 class="auth-title">Lupa Password?</h1>
                <p class="auth-subtitle">
                    Masukkan email yang terdaftar pada akun KAWWAN Anda. Kami akan mengirimkan tautan untuk mengatur ulang password.
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

                <form method="POST" action="{{ route('password.email') }}" class="auth-form" novalidate>
                    @csrf

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
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="form-input"
                                placeholder="nama@email.com"
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <span>Kirim Tautan Reset</span>
                        <i class="fa-solid fa-paper-plane"></i>
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
