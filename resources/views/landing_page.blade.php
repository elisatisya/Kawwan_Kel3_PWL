
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
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Beranda</a>
            <a href="#layanan">Layanan</a>
            <a href="#fitur">Fitur</a>
            <a href="#artikel">Artikel</a>
            <a href="#tentang">Tentang</a>

            <div class="mobile-auth">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary">Dashboard Admin</a>
                    @elseif(Auth::user()->role === 'dokter' || Auth::user()->role === 'dokter_hewan')
                        <a href="{{ url('/dokter/dashboard') }}" class="btn btn-primary">Dashboard Dokter</a>
                    @elseif(Auth::user()->role === 'kader' || Auth::user()->role === 'kader_puskesmas')
                        <a href="{{ url('/kader/dashboard') }}" class="btn btn-primary">Dashboard Kader</a>
                    @else
                        <a href="{{ url('/pemilik/dashboard') }}" class="btn btn-primary">Area Pemilik</a>
                    @endif
                @else
                    <a href="{{ url('/login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ url('/register') }}" class="btn btn-primary">Daftar</a>
                @endauth
            </div>
        </nav>

        <div class="nav-actions">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-gauge-high"></i> Dashboard Admin
                    </a>
                @elseif(Auth::user()->role === 'dokter' || Auth::user()->role === 'dokter_hewan')
                    <a href="{{ url('/dokter/dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-stethoscope"></i> Dashboard Dokter
                    </a>
                @elseif(Auth::user()->role === 'kader' || Auth::user()->role === 'kader_puskesmas')
                    <a href="{{ url('/kader/dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-clipboard-user"></i> Dashboard Kader
                    </a>
                @else
                    <a href="{{ url('/pemilik/dashboard') }}" class="btn btn-primary">
                        <i class="fa-solid fa-paw"></i> Area Pemilik
                    </a>
                @endif
            @else
                <a href="{{ url('/login') }}" class="btn btn-outline">Login</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">
                    Daftar <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endauth
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
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
                    alt="Ilustrasi Klinik Hewan KAWWAN"
                    class="hero-image"
                >
            </div>
        </section>

        <!-- ================= 1. DAFTAR LAYANAN (HORIZONTAL SCROLL) ================= -->
        <section class="services-carousel-section" id="layanan">
            <div class="landing-section-container">
                <div class="section-heading">
                    <p class="eyebrow">DAFTAR LAYANAN PUSKESWAN</p>
                    <h2>Pilihan Layanan Medis untuk Sahabat Hewan</h2>
                    <p>
                        Geser ke samping untuk menjelajahi seluruh layanan klinis yang tersedia. Klik kartu untuk melihat informasi detail dan melakukan pendaftaran antrean.
                    </p>
                </div>

                <div class="carousel-control-wrapper">
                    <button type="button" class="carousel-nav-btn prev" id="carouselPrev" aria-label="Geser ke kiri">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>

                    <div class="services-scroll-container" id="servicesScrollContainer">
                        @foreach($layananList as $item)
                            @php
                                $namaLower = strtolower($item->nama);
                                $icon = 'fa-heart-pulse';
                                $badge = 'Pelayanan Medis';
                                if (str_contains($namaLower, 'vaksin')) {
                                    $icon = 'fa-syringe';
                                    $badge = 'Vaksinasi';
                                } elseif (str_contains($namaLower, 'steril')) {
                                    $icon = 'fa-shield-cat';
                                    $badge = 'Tindakan';
                                } elseif (str_contains($namaLower, 'obat') || str_contains($namaLower, 'pengobatan')) {
                                    $icon = 'fa-pills';
                                    $badge = 'Pengobatan';
                                } elseif (str_contains($namaLower, 'vitamin')) {
                                    $icon = 'fa-bottle-droplet';
                                    $badge = 'Nutrisi & Suplemen';
                                } elseif (str_contains($namaLower, 'konsultasi')) {
                                    $icon = 'fa-user-doctor';
                                    $badge = 'Konsultasi';
                                } elseif (str_contains($namaLower, 'umum') || str_contains($namaLower, 'pemeriksaan')) {
                                    $icon = 'fa-stethoscope';
                                    $badge = 'Pemeriksaan Umum';
                                }

                                $biayaTeks = (isset($item->biaya) && $item->biaya > 0)
                                    ? 'Rp ' . number_format($item->biaya, 0, ',', '.')
                                    : 'Gratis / Subsidi Puskeswan';
                                $durasiTeks = ($item->durasi_menit ?? 30) . ' Menit';
                            @endphp

                            <article class="service-scroll-card"
                                     tabindex="0"
                                     role="button"
                                     data-layanan-item
                                     data-id="{{ $item->id }}"
                                     data-nama="{{ $item->nama }}"
                                     data-deskripsi="{{ $item->deskripsi ?? 'Pelayanan kesehatan hewan terpadu oleh dokter hewan Puskeswan.' }}"
                                     data-durasi="{{ $durasiTeks }}"
                                     data-biaya="{{ $biayaTeks }}"
                                     data-badge="{{ $badge }}"
                                     data-icon="{{ $icon }}">
                                <div class="service-card-header-badge">
                                    <span class="service-tag">{{ $badge }}</span>
                                    <span class="service-duration"><i class="fa-regular fa-clock"></i> {{ $durasiTeks }}</span>
                                </div>
                                <div class="service-card-main-icon">
                                    <i class="fa-solid {{ $icon }}"></i>
                                </div>
                                <h3 class="service-card-title">{{ $item->nama }}</h3>
                                <p class="service-card-description">
                                    {{ Str::limit($item->deskripsi ?? 'Pelayanan kesehatan hewan terpadu oleh dokter hewan Puskeswan.', 95) }}
                                </p>
                                <div class="service-card-bottom">
                                    <span class="service-price-label">{{ $biayaTeks }}</span>
                                    <span class="service-click-indicator">
                                        Lihat Detail <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <button type="button" class="carousel-nav-btn next" id="carouselNext" aria-label="Geser ke kanan">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="scroll-instruction-note">
                    <i class="fa-solid fa-arrow-left-long"></i>
                    <span>Geser ke samping untuk melihat seluruh layanan</span>
                    <i class="fa-solid fa-arrow-right-long"></i>
                </div>
            </div>
        </section>

        <!-- ================= 2. FITUR UTAMA (3 KOTAK INFO SINGKAT TANPA TOMBOL DETAIL) ================= -->
        <section class="features-section" id="fitur">
            <div class="landing-section-container">
                <div class="section-heading">
                    <p class="eyebrow">FITUR UTAMA KAWWAN</p>
                    <h2>Perawatan Hewan Jadi Lebih Mudah</h2>
                    <p>
                        Kelola seluruh kebutuhan kesehatan hewan peliharaan Anda secara praktis melalui satu platform terpadu sebelum berkunjung ke klinik.
                    </p>
                </div>

                <div class="features-grid">
                    <!-- Fitur 1: Data Hewan -->
                    <article class="feature-info-card">
                        <div class="feature-info-icon">
                            <i class="fa-solid fa-paw"></i>
                        </div>
                        <h3>Data Hewan</h3>
                        <p>
                            Simpan dan kelola informasi identitas, jenis, ras, dan catatan penting hewan peliharaan agar mudah diakses saat dibutuhkan.
                        </p>
                    </article>

                    <!-- Fitur 2: Pemesanan Antrean -->
                    <article class="feature-info-card">
                        <div class="feature-info-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <h3>Pemesanan Antrean</h3>
                        <p>
                            Lihat jadwal pelayanan yang tersedia dan lakukan pemesanan secara online dengan estimasi antrean yang jelas tanpa menunggu lama.
                        </p>
                    </article>

                    <!-- Fitur 3: Riwayat Pemeriksaan -->
                    <article class="feature-info-card">
                        <div class="feature-info-icon">
                            <i class="fa-solid fa-file-medical"></i>
                        </div>
                        <h3>Riwayat Pemeriksaan</h3>
                        <p>
                            Akses rekam medis digital, riwayat diagnosa, tindakan dokter, serta resep pengobatan untuk memantau kesehatan anabul secara berkala.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ================= 3. DAFTAR ARTIKEL ================= -->
        <section class="article-section" id="artikel">
            <div class="landing-section-container">
                <div class="section-heading">
                    <p class="eyebrow">INFORMASI TERPERCAYA</p>
                    <h2>Artikel & Tips Kesehatan Hewan</h2>
                    <p>
                        Temukan informasi, panduan, dan tips perawatan terbaik untuk mendukung kesehatan sahabat berbulu Anda.
                    </p>
                </div>

                <div class="article-grid">
                    @forelse($artikelTerbaru as $art)
                        <article class="article-card">
                            <div class="article-card-media">
                                @if(!empty($art->gambar))
                                    <img src="{{ asset('storage/' . $art->gambar) }}" alt="{{ $art->judul }}" class="article-card-img">
                                @else
                                    <div class="article-card-placeholder">
                                        <i class="fa-solid fa-newspaper"></i>
                                    </div>
                                @endif
                                <span class="article-tag">{{ $art->kategori ?: 'Kesehatan' }}</span>
                            </div>
                            <div class="article-card-body">
                                <span class="article-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $art->published_at ? $art->published_at->format('d M Y') : date('d M Y') }}
                                </span>
                                <h3 class="article-card-title">{{ $art->judul }}</h3>
                                <p class="article-card-excerpt">
                                    {{ Str::limit($art->ringkasan ?: strip_tags($art->isi), 90) }}
                                </p>
                            </div>
                        </article>
                    @empty
                        <article class="article-card">
                            <div class="article-card-media">
                                <div class="article-card-placeholder">
                                    <i class="fa-solid fa-shield-virus"></i>
                                </div>
                                <span class="article-tag">Vaksinasi</span>
                            </div>
                            <div class="article-card-body">
                                <span class="article-date"><i class="fa-regular fa-calendar"></i> Tips Puskeswan</span>
                                <h3 class="article-card-title">Pentingnya Vaksinasi Tahunan untuk Hewan Peliharaan</h3>
                                <p class="article-card-excerpt">Kenali jenis-jenis vaksin wajib seperti rabies dan tricat guna melindungi anabul dari penyakit berbahaya.</p>
                            </div>
                        </article>

                        <article class="article-card">
                            <div class="article-card-media">
                                <div class="article-card-placeholder">
                                    <i class="fa-solid fa-bowl-food"></i>
                                </div>
                                <span class="article-tag">Nutrisi</span>
                            </div>
                            <div class="article-card-body">
                                <span class="article-date"><i class="fa-regular fa-calendar"></i> Tips Puskeswan</span>
                                <h3 class="article-card-title">Panduan Pola Makan Sehat Sesuai Usia Anabul</h3>
                                <p class="article-card-excerpt">Menjaga asupan gizi seimbang untuk anjing dan kucing sejak usia puppy/kitten hingga memasuki usia senior.</p>
                            </div>
                        </article>

                        <article class="article-card">
                            <div class="article-card-media">
                                <div class="article-card-placeholder">
                                    <i class="fa-solid fa-kit-medical"></i>
                                </div>
                                <span class="article-tag">Perawatan</span>
                            </div>
                            <div class="article-card-body">
                                <span class="article-date"><i class="fa-regular fa-calendar"></i> Tips Puskeswan</span>
                                <h3 class="article-card-title">Tanda-tanda Awal Hewan Peliharaan Mengalami Stres</h3>
                                <p class="article-card-excerpt">Cara mudah mengenali perubahan perilaku hewan saat merasa tidak nyaman dan langkah pencegahannya.</p>
                            </div>
                        </article>
                    @endforelse
                </div>

                <div class="article-action-wrap">
                    <a href="{{ url('/artikel') }}" class="btn btn-outline">
                        Jelajahi Semua Artikel
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- ================= 4. TENTANG KAWWAN ================= -->
        <section class="about" id="tentang">
            <div class="about-content">
                <p class="eyebrow">TENTANG KAWWAN</p>
                <h2>
                    Karena setiap hewan berhak mendapatkan
                    perawatan terbaik.
                </h2>
            </div>

            <p class="about-description">
                KAWWAN merupakan platform layanan kesehatan hewan yang membantu pemilik mengakses informasi, jadwal pelayanan, pemesanan antrean, dan riwayat pemeriksaan secara lebih praktis.
            </p>
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

    <!-- ================= MODAL DETAIL LAYANAN & DAFTAR BOOKING ================= -->
    <div id="modalDetailLayanan" class="kawwan-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="kawwan-modal-overlay" onclick="closeDetailLayananModal()"></div>
        <div class="kawwan-modal-content">
            <div class="modal-header">
                <div class="modal-title-box">
                    <span id="popupLayananBadge" class="badge-sub">Layanan Puskeswan</span>
                    <h3 id="popupLayananTitle">Nama Layanan</h3>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeDetailLayananModal()" aria-label="Tutup">&times;</button>
            </div>

            <div class="modal-body">
                <p id="popupLayananDesc" class="modal-desc"></p>

                <div class="detail-info-grid">
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-clock"></i> Estimasi Durasi</div>
                        <div id="popupLayananDurasi" class="info-value">30 Menit</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-tag"></i> Estimasi Biaya</div>
                        <div id="popupLayananBiaya" class="info-value">Subsidi Puskeswan</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-user-doctor"></i> Tim Pemeriksa</div>
                        <div class="info-value">Dokter Hewan Puskeswan</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-ticket"></i> Sistem Pelayanan</div>
                        <div class="info-value">Antrean Online Terjadwal</div>
                    </div>
                </div>

                <div class="prep-note">
                    <h4><i class="fa-solid fa-circle-check"></i> Petunjuk Sebelum Berkunjung:</h4>
                    <ul id="popupLayananPoints">
                        <li>Pastikan anabul dalam kondisi tenang, gunakan pet cargo atau tali penuntun (leash).</li>
                        <li>Bawa buku vaksin atau catatan medis sebelumnya jika pernah melakukan pemeriksaan.</li>
                        <li>Lakukan pemesanan jadwal antrean terlebih dahulu untuk mendapatkan nomor antrean pasti.</li>
                        <li>Harap hadir 15 menit sebelum waktu pelayanan yang telah Anda pilih.</li>
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-action" onclick="closeDetailLayananModal()">Tutup</button>
                @auth
                    @if(Auth::user()->role === 'pemilik_hewan' || Auth::user()->role === 'pemilik')
                        <a href="{{ route('pemilik.jadwal.index') }}" class="btn-primary-action">
                            <i class="fa-regular fa-calendar-check"></i>
                            <span>Pilih Jadwal & Pesan Antrean</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="{{ url('/' . Auth::user()->role . '/dashboard') }}" class="btn-primary-action">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Buka Dashboard</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endif
                @else
                    <div class="modal-auth-actions">
                        <a href="{{ route('login') }}" class="btn-primary-action">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Masuk untuk Booking</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('register') }}" class="btn-secondary-link">
                            Belum punya akun? <strong>Daftar di sini</strong>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- STYLING KHUSUS SECTION LAYANAN SCROLL, FITUR, & MODAL -->
    <style>
        .landing-section-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        /* 1. SECTION DAFTAR LAYANAN (CAROUSEL) */
        .services-carousel-section {
            padding: 70px 0 55px;
            background: #FCF9F6;
            border-top: 1px solid #F0E6DE;
            border-bottom: 1px solid #F0E6DE;
            scroll-margin-top: 80px;
        }

        .carousel-control-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            max-width: 1140px;
            margin: 0 auto;
        }

        .services-scroll-container {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 14px 10px 24px;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            width: 100%;
        }

        /* Custom Scrollbar for Services Carousel */
        .services-scroll-container::-webkit-scrollbar {
            height: 7px;
        }

        .services-scroll-container::-webkit-scrollbar-track {
            background: #EFE7E0;
            border-radius: 999px;
        }

        .services-scroll-container::-webkit-scrollbar-thumb {
            background: #FF9B4D;
            border-radius: 999px;
        }

        .services-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #FF6B00;
        }

        .service-scroll-card {
            flex: 0 0 300px;
            min-width: 280px;
            max-width: 310px;
            scroll-snap-align: start;
            background: #FFFFFF;
            border: 1.5px solid #F0E4DA;
            border-radius: 18px;
            padding: 24px 22px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 6px 20px rgba(24, 19, 16, 0.04);
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
            box-sizing: border-box;
        }

        .service-scroll-card:hover,
        .service-scroll-card:focus-visible {
            transform: translateY(-5px);
            border-color: #FF6B00;
            box-shadow: 0 16px 32px rgba(255, 107, 0, 0.12);
            outline: none;
        }

        .service-card-header-badge {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .service-tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #FF6B00;
            background: #FFF2E8;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid #FFD9BE;
        }

        .service-duration {
            font-size: 12px;
            color: #81766E;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .service-card-main-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #FFF2E8;
            color: #FF6B00;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
            transition: transform 0.25s ease;
        }

        .service-scroll-card:hover .service-card-main-icon {
            transform: scale(1.08);
            background: #FF6B00;
            color: #FFFFFF;
        }

        .service-card-title {
            font-size: 17px;
            font-weight: 800;
            color: #181310;
            margin: 0 0 8px;
            line-height: 1.35;
        }

        .service-card-description {
            font-size: 13px;
            color: #81766E;
            line-height: 1.6;
            margin: 0 0 16px;
            flex-grow: 1;
        }

        .service-card-bottom {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px dashed #F0E6DE;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-price-label {
            font-size: 12px;
            font-weight: 700;
            color: #181310;
        }

        .service-click-indicator {
            font-size: 12.5px;
            font-weight: 700;
            color: #FF6B00;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s ease;
        }

        .service-scroll-card:hover .service-click-indicator {
            gap: 10px;
        }

        .carousel-nav-btn {
            background: #FFFFFF;
            border: 1.5px solid #F0E4DA;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #181310;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            z-index: 5;
            flex-shrink: 0;
        }

        .carousel-nav-btn:hover {
            background: #FF6B00;
            border-color: #FF6B00;
            color: #FFFFFF;
            transform: scale(1.06);
            box-shadow: 0 8px 20px rgba(255, 107, 0, 0.25);
        }

        .carousel-nav-btn.prev {
            margin-right: 10px;
        }

        .carousel-nav-btn.next {
            margin-left: 10px;
        }

        .scroll-instruction-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #A3968D;
            margin-top: 6px;
        }

        /* 2. SECTION FITUR UTAMA (3 KOTAK INFO SINGKAT) */
        .features-section {
            padding: 70px 0 60px;
            background: #FFFFFF;
            scroll-margin-top: 80px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1040px;
            margin: 0 auto;
        }

        .feature-info-card {
            background: #FFFFFF;
            border: 1.5px solid #F0E4DA;
            border-radius: 18px;
            padding: 30px 26px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 24px rgba(24, 19, 16, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            box-sizing: border-box;
        }

        .feature-info-card:hover {
            transform: translateY(-4px);
            border-color: #FFD4BA;
            box-shadow: 0 14px 28px rgba(24, 19, 16, 0.08);
        }

        .feature-info-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #FFF2E8;
            color: #FF6B00;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 18px;
        }

        .feature-info-card h3 {
            font-size: 19px;
            font-weight: 800;
            color: #181310;
            margin: 0 0 10px;
        }

        .feature-info-card p {
            font-size: 13.5px;
            color: #81766E;
            line-height: 1.65;
            margin: 0;
        }

        /* 3. SECTION DAFTAR ARTIKEL */
        .article-section {
            padding: 70px 0 65px;
            background: #FCF9F6;
            border-top: 1px solid #F0E6DE;
            scroll-margin-top: 80px;
        }

        .article-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1040px;
            margin: 0 auto 35px;
        }

        .article-card {
            background: #FFFFFF;
            border: 1.5px solid #F0E4DA;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 24px rgba(24, 19, 16, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .article-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        }

        .article-card-media {
            position: relative;
            height: 160px;
            background: #FFF0E5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .article-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-card-placeholder {
            font-size: 38px;
            color: #FF9B4D;
        }

        .article-tag {
            position: absolute;
            bottom: 12px;
            left: 14px;
            background: rgba(24, 19, 16, 0.85);
            color: #FFFFFF;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .article-card-body {
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .article-date {
            font-size: 11.5px;
            color: #81766E;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .article-card-title {
            font-size: 16px;
            font-weight: 800;
            color: #181310;
            margin: 0 0 8px;
            line-height: 1.4;
        }

        .article-card-excerpt {
            font-size: 13px;
            color: #81766E;
            line-height: 1.6;
            margin: 0;
        }

        .article-action-wrap {
            text-align: center;
        }

        /* MODAL DETAIL LAYANAN */
        .kawwan-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999999;
            align-items: center;
            justify-content: center;
            padding: 18px;
            box-sizing: border-box;
        }

        .kawwan-modal.active {
            display: flex !important;
        }

        .kawwan-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(24, 19, 16, 0.55);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        .kawwan-modal-content {
            position: relative;
            z-index: 10;
            background: #FFFFFF;
            border-radius: 18px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
            overflow: hidden;
            animation: modalPopIn 0.25s ease-out;
            border: 1px solid #ECE7E3;
            box-sizing: border-box;
        }

        @keyframes modalPopIn {
            from { opacity: 0; transform: scale(0.94) translateY(14px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 22px 24px 16px;
            border-bottom: 1px solid #F2EBE6;
        }

        .modal-header h3 {
            font-size: 19px;
            font-weight: 800;
            color: #181310;
            margin: 4px 0 0;
            letter-spacing: -0.3px;
        }

        .btn-close-modal {
            background: #F2EBE6;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-size: 20px;
            color: #81766E;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            line-height: 1;
        }

        .btn-close-modal:hover {
            background: #FF6B00;
            color: #FFFFFF;
        }

        .modal-body {
            padding: 20px 24px;
            max-height: 65vh;
            overflow-y: auto;
        }

        .modal-desc {
            font-size: 13.5px;
            color: #81766E;
            line-height: 1.65;
            margin: 0 0 18px;
        }

        .detail-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-box {
            background: #F9F6F3;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid #EFEAE5;
        }

        .info-label {
            font-size: 11px;
            color: #81766E;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-label i {
            color: #FF6B00;
        }

        .info-value {
            font-size: 13px;
            font-weight: 700;
            color: #181310;
        }

        .prep-note {
            background: #FFF9F5;
            border-left: 3.5px solid #FF6B00;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid #FFE6D5;
            border-left: 3.5px solid #FF6B00;
        }

        .prep-note h4 {
            font-size: 13px;
            font-weight: 800;
            color: #181310;
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .prep-note h4 i {
            color: #FF6B00;
        }

        .prep-note ul {
            margin: 0;
            padding-left: 18px;
            font-size: 12.5px;
            color: #5A524D;
            line-height: 1.65;
        }

        .prep-note ul li {
            margin-bottom: 4px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            padding: 16px 24px;
            border-top: 1px solid #F2EBE6;
            background: #FAFAFA;
        }

        .modal-auth-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-secondary-action {
            background-color: #F2EBE6;
            color: #181310;
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 20px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-secondary-action:hover {
            background-color: #E5DCD5;
        }

        .btn-secondary-link {
            font-size: 13px;
            color: #81766E;
            text-decoration: none;
            transition: color 0.2s;
        }

        .btn-secondary-link strong {
            color: #FF6B00;
        }

        .btn-secondary-link:hover {
            color: #FF6B00;
        }

        .btn-primary-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #FF6B00;
            color: #FFFFFF !important;
            text-decoration: none;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            padding: 9px 22px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(255, 107, 0, 0.28);
        }

        .btn-primary-action:hover {
            background-color: #E85F00;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(255, 107, 0, 0.38);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .features-grid,
            .article-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .features-grid,
            .article-grid {
                grid-template-columns: 1fr;
            }

            .carousel-nav-btn {
                display: none;
            }

            .modal-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .modal-auth-actions {
                flex-direction: column;
                width: 100%;
            }

            .btn-primary-action,
            .btn-secondary-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <!-- JAVASCRIPT CAROUSEL & MODAL DETAIL LAYANAN -->
    <script>
        function openDetailLayananModal(data) {
            const modal = document.getElementById('modalDetailLayanan');
            if (!modal) return;

            document.getElementById('popupLayananTitle').textContent = data.nama || 'Detail Layanan';
            document.getElementById('popupLayananBadge').textContent = data.badge || 'Layanan Puskeswan';
            document.getElementById('popupLayananDesc').textContent = data.desc || 'Pelayanan kesehatan hewan terpadu oleh tim dokter hewan Puskeswan.';
            document.getElementById('popupLayananDurasi').textContent = data.durasi || '30 Menit';
            document.getElementById('popupLayananBiaya').textContent = data.biaya || 'Subsidi Puskeswan';

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailLayananModal() {
            const modal = document.getElementById('modalDetailLayanan');
            if (modal) {
                modal.classList.remove('active');
            }
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const track = document.getElementById('servicesScrollContainer');
            const prevBtn = document.getElementById('carouselPrev');
            const nextBtn = document.getElementById('carouselNext');

            if (prevBtn && track) {
                prevBtn.addEventListener('click', function () {
                    track.scrollBy({ left: -320, behavior: 'smooth' });
                });
            }

            if (nextBtn && track) {
                nextBtn.addEventListener('click', function () {
                    track.scrollBy({ left: 320, behavior: 'smooth' });
                });
            }

            // Click listener pada setiap kartu layanan
            document.querySelectorAll('[data-layanan-item]').forEach(function (card) {
                card.addEventListener('click', function () {
                    const data = {
                        nama: this.getAttribute('data-nama'),
                        desc: this.getAttribute('data-deskripsi'),
                        durasi: this.getAttribute('data-durasi'),
                        biaya: this.getAttribute('data-biaya'),
                        badge: this.getAttribute('data-badge')
                    };
                    openDetailLayananModal(data);
                });

                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDetailLayananModal();
                }
            });

            // Active highlight pada navbar sesuai klik & scroll section
            const navItems = Array.from(document.querySelectorAll('#navLinks > a'));
            const sectionMap = [
                { link: navItems.find(a => !a.getAttribute('href').startsWith('#')), el: document.querySelector('.hero') },
                { link: navItems.find(a => a.getAttribute('href') === '#layanan'), el: document.getElementById('layanan') },
                { link: navItems.find(a => a.getAttribute('href') === '#fitur'), el: document.getElementById('fitur') },
                { link: navItems.find(a => a.getAttribute('href') === '#artikel'), el: document.getElementById('artikel') },
                { link: navItems.find(a => a.getAttribute('href') === '#tentang'), el: document.getElementById('tentang') },
            ].filter(item => item.link && item.el);

            function setActiveNav(targetLink) {
                navItems.forEach(a => a.classList.remove('active'));
                if (targetLink) targetLink.classList.add('active');
            }

            navItems.forEach(link => {
                link.addEventListener('click', function () {
                    setActiveNav(this);
                });
            });

            function updateActiveNavOnScroll() {
                const scrollPos = window.scrollY + 140;
                let current = sectionMap[0]?.link;

                for (const item of sectionMap) {
                    const top = item.el.offsetTop;
                    if (scrollPos >= top) {
                        current = item.link;
                    }
                }
                setActiveNav(current);
            }

            window.addEventListener('scroll', updateActiveNavOnScroll, { passive: true });
        });
    </script>
</body>
</html>
