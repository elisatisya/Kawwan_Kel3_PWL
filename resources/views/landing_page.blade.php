
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>KAWWAN — Kesehatan Hewan Lebih Mudah</title>
    <meta name="description" content="KAWWAN membantu pemilik hewan mengakses jadwal pelayanan, pemesanan antrean, riwayat pemeriksaan, dan informasi kesehatan hewan.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <header class="navbar">       
        <a href="{{ url('/') }}" class="brand">
            <img
                src="{{ asset('images/logo-kawwan.png') }}"
                alt="Logo KAWWAN"
                class="brand-logo"
            >
        </a>

        <button
            class="menu-toggle"
            id="menuToggle"
            type="button"
            aria-label="Buka menu"
            aria-expanded="false"
            aria-controls="navLinks"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="nav-links" id="navLinks" aria-label="Navigasi utama">
            <a href="{{ url('/') }}">Beranda</a>
            <a href="#layanan">Layanan</a>
            <a href="#artikel">Artikel</a>
            <a href="#tentang">Tentang</a>

            @auth
                <a href="{{ url('/profil') }}" class="mobile-profile">
                    <i class="fa-regular fa-user"></i> Profil
                </a>

                <form action="{{ route('logout') }}" method="POST" class="mobile-logout">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            @else
                <div class="mobile-auth">
                    <a href="{{ url('/login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary">Daftar</a>
                </div>
            @endauth
        </nav>

        @auth
            <div class="nav-actions">
                <a href="{{ url('/profil') }}" class="profile-link">
                    <i class="fa-regular fa-user"></i>
                    Profil
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        @else
            <div class="nav-actions">
                <a href="{{ url('/login') }}" class="btn btn-outline">Login</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">
                    Daftar <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        @endauth
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <p class="eyebrow">
                    KESEHATAN HEWAN
                    <span>•</span>
                    LEBIH DEKAT
                    <span>•</span>
                    LEBIH MUDAH
                </p>

                <h1>
                    Rawat Hewan<br>
                    Kesayanganmu<br>
                    <span>dengan Lebih Mudah.</span>
                </h1>

                <p class="hero-description">
                    KAWWAN hadir untuk memudahkan kamu dalam mengakses
                    layanan kesehatan hewan, memantau riwayat pemeriksaan,
                    dan mendapatkan informasi terpercaya.
                </p>

                <div class="hero-buttons">
                    @auth
                        <a href="#layanan" class="btn btn-primary btn-large">
                            Jelajahi Layanan
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ url('/register') }}" class="btn btn-primary btn-large">
                            Mulai Sekarang
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endauth

                    <a href="#layanan" class="btn btn-outline btn-large">
                        Pelajari Layanan
                    </a>
                </div>

                <div class="hero-features">
                    <div class="feature">
                        <span class="feature-icon">
                            <i class="fa-regular fa-calendar-check"></i>
                        </span>
                        <div>
                            <strong>Praktis</strong>
                            <p>Pemesanan antrean</p>
                        </div>
                    </div>

                    <div class="feature">
                        <span class="feature-icon">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </span>
                        <div>
                            <strong>Terpercaya</strong>
                            <p>Layanan kesehatan</p>
                        </div>
                    </div>

                    <div class="feature">
                        <span class="feature-icon">
                            <i class="fa-solid fa-file-medical"></i>
                        </span>
                        <div>
                            <strong>Terintegrasi</strong>
                            <p>Riwayat pemeriksaan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <img
                    src="{{ asset('images/klinik-hewan.png') }}"
                    alt="Dokter hewan sedang memeriksa anjing di klinik"
                    class="hero-image"
                >
            </div>
        </section>

        <section class="services" id="layanan">
            <div class="section-heading">
                <p class="eyebrow">LAYANAN KAWWAN</p>
                <h2>Perawatan hewan jadi lebih mudah.</h2>
                <p>
                    Kelola kebutuhan kesehatan hewan peliharaanmu
                    melalui satu platform.
                </p>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <h3>Data Hewan</h3>
                    <p>
                        Simpan dan kelola informasi hewan peliharaan
                        agar mudah diakses saat dibutuhkan.
                    </p>
                    <a href="{{ url('/pemilik/hewan') }}" class="service-link">
                        Kelola Data <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </article>

                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3>Pemesanan Antrean</h3>
                    <p>
                        Lihat jadwal pelayanan yang tersedia dan
                        lakukan pemesanan dengan lebih praktis.
                    </p>
                    <a href="{{ url('/pemilik/jadwal') }}" class="service-link">
                        Lihat Jadwal <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </article>

                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-file-medical"></i>
                    </div>
                    <h3>Riwayat Pemeriksaan</h3>
                    <p>
                        Akses catatan pemeriksaan untuk membantu
                        memantau riwayat kesehatan hewan.
                    </p>
                    <a href="{{ url('/pemilik/riwayat') }}" class="service-link">
                        Lihat Riwayat <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </article>
            </div>
        </section>

        <section class="about" id="tentang">
            <div class="about-content">
                <p class="eyebrow">TENTANG KAWWAN</p>
                <h2>
                    Karena setiap hewan berhak mendapatkan
                    perawatan terbaik.
                </h2>
            </div>

            <p class="about-description">
                KAWWAN merupakan platform layanan kesehatan hewan
                yang membantu pemilik mengakses informasi, jadwal
                pelayanan, pemesanan antrean, dan riwayat pemeriksaan
                secara lebih praktis.
            </p>
        </section>

        <section class="article-section" id="artikel">
            <div class="article-content">
                <p class="eyebrow">INFORMASI TERPERCAYA</p>
                <h2>Lebih mengenal kebutuhan kesehatan hewanmu.</h2>
                <p>
                    Temukan informasi dan tips perawatan hewan
                    untuk mendukung kesehatan sahabatmu.
                </p>
            </div>

            <a href="{{ url('/artikel') }}" class="btn btn-outline">
                Jelajahi Artikel
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </section>
    </main>

    <footer class="footer">
        <a href="{{ url('/') }}" class="brand footer-brand">
            <i class="fa-solid fa-paw brand-paw"></i>
            <span>KAWWAN</span>
        </a>

        <p>Merawat dengan perhatian, melayani dengan kepedulian.</p>

        <span class="copyright">
            © {{ date('Y') }} KAWWAN
        </span>
    </footer>

</body>
</html>
