<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KAWWAN — Kawan Hewan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage:       #C5CFA8;
            --sage-light: #D9E5C1;
            --cream:      #FFF8E7;
            --peach:      #F5DDB8;
            --brown:      #C99A5E;
            --brown-dk:   #9E7540;
            --ink:        #2E2A22;
            --ink-mid:    #6B5E4A;
            --ink-soft:   #A89880;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
            font-weight: 300;
        }

        /* ── NAV ───────────────────────────────────────── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 99;
            padding: 1.5rem 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background .4s;
        }
        nav.solid {
            background: rgba(255,248,231,.92);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(197,207,168,.3);
        }
        .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: .06em;
            color: var(--ink);
        }
        .logo span { color: var(--brown); }
        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }
        .nav-links a {
            font-size: .82rem;
            letter-spacing: .06em;
            color: var(--ink-mid);
            text-decoration: none;
            transition: color .2s;
        }
        .nav-links a:hover { color: var(--brown); }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .nav-login {
            font-size: .82rem;
            color: var(--ink-mid);
            text-decoration: none;
            letter-spacing: .04em;
            transition: color .2s;
        }
        .nav-login:hover { color: var(--brown); }
        .nav-cta {
            font-size: .82rem;
            letter-spacing: .06em;
            color: var(--cream);
            background: var(--brown);
            text-decoration: none;
            padding: .6rem 1.4rem;
            border-radius: 2px;
            transition: background .2s;
        }
        .nav-cta:hover { background: var(--brown-dk); }

        /* ── HERO ──────────────────────────────────────── */
        .hero {
            min-height: 100svh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .hero-left {
            background: var(--sage-light);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 8rem 4rem 5rem;
            position: relative;
            overflow: hidden;
        }
        .hero-left::before {
            content: '🐾';
            position: absolute;
            top: 6rem; left: 4rem;
            font-size: 1.5rem;
            opacity: .4;
        }
        .hero-eyebrow {
            font-size: .72rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--ink-mid);
            margin-bottom: 1.5rem;
        }
        .hero-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(3rem, 5.5vw, 5rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--ink);
        }
        .hero-title em {
            font-style: italic;
            color: var(--brown-dk);
        }
        .hero-desc {
            margin-top: 2rem;
            font-size: .9rem;
            line-height: 1.8;
            color: var(--ink-mid);
            max-width: 400px;
        }
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-top: 2.5rem;
        }
        .btn-main {
            font-size: .82rem;
            letter-spacing: .06em;
            color: var(--cream);
            background: var(--brown);
            text-decoration: none;
            padding: .85rem 2rem;
            border-radius: 2px;
            transition: background .2s, transform .2s;
        }
        .btn-main:hover { background: var(--brown-dk); transform: translateY(-1px); }
        .btn-text {
            font-size: .82rem;
            color: var(--ink-mid);
            text-decoration: none;
            border-bottom: 1px solid var(--ink-soft);
            padding-bottom: 2px;
            transition: color .2s, border-color .2s;
        }
        .btn-text:hover { color: var(--brown); border-color: var(--brown); }

        /* Hero right — image / visual area */
        .hero-right {
            background: var(--peach);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
        }
        .hero-right-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 70%, var(--sage-light) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, var(--cream) 0%, transparent 50%);
        }
        /* Large decorative letter */
        .hero-deco-letter {
            position: absolute;
            font-family: 'Cormorant Garamond', serif;
            font-size: 32vw;
            font-weight: 300;
            font-style: italic;
            color: rgba(201,154,94,.12);
            line-height: 1;
            bottom: -4vw;
            right: -2vw;
            user-select: none;
            pointer-events: none;
        }
        /* Floating antrian card */
        .antrian-card {
            position: relative;
            z-index: 2;
            margin: 0 3rem 4rem 0;
            background: rgba(255,248,231,.85);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,248,231,.9);
            border-radius: 4px;
            padding: 2rem 2.5rem;
            width: 240px;
        }
        .ac-label {
            font-size: .67rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--ink-soft);
            margin-bottom: .6rem;
        }
        .ac-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 4rem;
            font-weight: 300;
            color: var(--brown-dk);
            line-height: 1;
            margin-bottom: .4rem;
        }
        .ac-name {
            font-size: .82rem;
            color: var(--ink-mid);
        }
        .ac-pet {
            font-size: .72rem;
            color: var(--ink-soft);
            margin-top: .2rem;
        }
        .ac-divider {
            height: 1px;
            background: var(--peach);
            margin: 1rem 0;
        }
        .ac-status {
            font-size: .72rem;
            letter-spacing: .08em;
            color: var(--brown);
        }

        /* ── ABOUT STRIP ───────────────────────────────── */
        .strip {
            background: var(--sage);
            padding: 1.5rem 4rem;
            display: flex;
            align-items: center;
            gap: 3rem;
            overflow: hidden;
        }
        .strip-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-shrink: 0;
        }
        .strip-icon { font-size: 1rem; }
        .strip-text {
            font-size: .78rem;
            letter-spacing: .06em;
            color: var(--ink);
        }
        .strip-dot {
            width: 4px; height: 4px;
            border-radius: 50%;
            background: var(--ink-mid);
            flex-shrink: 0;
        }

        /* ── LAYANAN (Roles) ────────────────────────────── */
        .layanan {
            padding: 7rem 4rem;
        }
        .section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 4rem;
            gap: 2rem;
        }
        .section-kicker {
            font-size: .7rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--brown);
            margin-bottom: 1rem;
        }
        .section-h {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 300;
            line-height: 1.2;
            color: var(--ink);
        }
        .section-h em { font-style: italic; }
        .section-p {
            max-width: 380px;
            font-size: .87rem;
            line-height: 1.8;
            color: var(--ink-mid);
            align-self: flex-end;
        }

        .cards-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5px;
            background: var(--sage-light);
            border: 1.5px solid var(--sage-light);
        }
        .role-card {
            background: var(--cream);
            padding: 2.5rem 2rem;
            transition: background .3s;
            cursor: default;
        }
        .role-card:hover { background: #FFFCF5; }
        .rc-num {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            color: var(--ink-soft);
            margin-bottom: 1.5rem;
        }
        .rc-icon { font-size: 1.8rem; margin-bottom: 1.25rem; }
        .rc-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 400;
            color: var(--ink);
            margin-bottom: .75rem;
        }
        .rc-desc {
            font-size: .83rem;
            line-height: 1.75;
            color: var(--ink-mid);
        }
        .rc-list {
            margin-top: 1.25rem;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        .rc-list li {
            font-size: .78rem;
            color: var(--ink-mid);
            padding-left: 1rem;
            position: relative;
        }
        .rc-list li::before {
            content: '—';
            position: absolute;
            left: 0;
            color: var(--brown);
        }

        /* ── ALUR KERJA ─────────────────────────────────── */
        .alur {
            padding: 6rem 4rem;
            background: var(--ink);
        }
        .alur .section-kicker { color: var(--sage); }
        .alur .section-h { color: var(--cream); }
        .alur .section-h em { color: var(--peach); }

        .steps {
            margin-top: 4rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
        }
        .step {
            padding: 2rem 2rem 2rem 0;
            border-left: 1px solid rgba(255,248,231,.1);
            padding-left: 2rem;
        }
        .step:first-child { border-left: none; padding-left: 0; }
        .step-n {
            font-family: 'Cormorant Garamond', serif;
            font-size: 3rem;
            font-weight: 300;
            color: rgba(255,248,231,.15);
            line-height: 1;
            margin-bottom: 1rem;
        }
        .step-icon { font-size: 1.3rem; margin-bottom: .75rem; }
        .step-title {
            font-size: .87rem;
            font-weight: 500;
            color: var(--cream);
            margin-bottom: .5rem;
            letter-spacing: .02em;
        }
        .step-desc {
            font-size: .78rem;
            line-height: 1.7;
            color: rgba(255,248,231,.5);
        }

        /* ── FITUR LIST ─────────────────────────────────── */
        .fitur {
            padding: 7rem 4rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6rem;
            align-items: start;
        }
        .fitur-left { position: sticky; top: 6rem; }
        .fitur-right { display: flex; flex-direction: column; gap: 0; }
        .fitur-item {
            padding: 2rem 0;
            border-bottom: 1px solid var(--sage-light);
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            transition: opacity .3s;
        }
        .fitur-item:first-child { padding-top: 0; }
        .fi-icon {
            width: 40px; height: 40px;
            border-radius: 2px;
            background: var(--sage-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: .1rem;
        }
        .fi-title {
            font-size: .95rem;
            font-weight: 500;
            color: var(--ink);
            margin-bottom: .4rem;
        }
        .fi-desc {
            font-size: .82rem;
            line-height: 1.7;
            color: var(--ink-mid);
        }

        /* ── CTA ────────────────────────────────────────── */
        .cta {
            padding: 7rem 4rem;
            background: var(--sage-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .cta-eyebrow {
            font-size: .7rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--brown);
            margin-bottom: 1.5rem;
        }
        .cta-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--ink);
            max-width: 700px;
        }
        .cta-title em { font-style: italic; color: var(--brown-dk); }
        .cta-sub {
            margin-top: 1.5rem;
            font-size: .9rem;
            line-height: 1.8;
            color: var(--ink-mid);
            max-width: 460px;
        }
        .cta-btns {
            margin-top: 2.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        /* ── FOOTER ─────────────────────────────────────── */
        footer {
            padding: 3rem 4rem;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .footer-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            color: var(--cream);
            letter-spacing: .06em;
        }
        .footer-logo span { color: var(--peach); }
        .footer-copy {
            font-size: .75rem;
            color: rgba(255,248,231,.35);
            letter-spacing: .04em;
        }
        .footer-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        .footer-links a {
            font-size: .75rem;
            color: rgba(255,248,231,.45);
            text-decoration: none;
            letter-spacing: .04em;
            transition: color .2s;
        }
        .footer-links a:hover { color: var(--peach); }

        /* ── RESPONSIVE ─────────────────────────────────── */
        @media (max-width: 900px) {
            nav { padding: 1.2rem 2rem; }
            .nav-links { display: none; }
            .hero { grid-template-columns: 1fr; min-height: auto; }
            .hero-left { padding: 7rem 2rem 4rem; }
            .hero-right { min-height: 60vw; }
            .antrian-card { margin: 0 2rem 3rem 0; }
            .strip { padding: 1.2rem 2rem; gap: 2rem; }
            .layanan, .fitur, .alur, .cta { padding: 5rem 2rem; }
            .section-head { flex-direction: column; }
            .section-p { max-width: 100%; }
            .cards-row { grid-template-columns: 1fr; }
            .steps { grid-template-columns: 1fr 1fr; gap: 2rem; }
            .step { border-left: none; padding-left: 0; }
            .fitur { grid-template-columns: 1fr; gap: 3rem; }
            .fitur-left { position: static; }
            footer { padding: 2.5rem 2rem; }
            .footer-links { display: none; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav id="nav">
    <div class="logo">KAW<span>WAN</span></div>
    <ul class="nav-links">
        <li><a href="#layanan">Layanan</a></li>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#fitur">Fitur</a></li>
    </ul>
    <div class="nav-right">
        <a href="/login" class="nav-login">Masuk</a>
        <a href="/register" class="nav-cta">Daftar</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-left">
        <p class="hero-eyebrow">Kawan Hewan · Sistem Informasi Klinik</p>
        <h1 class="hero-title">
            Klinik hewan<br>
            yang lebih<br>
            <em>terhubung.</em>
        </h1>
        <p class="hero-desc">
            Platform digital yang menyatukan pemilik hewan, dokter, dan kader klinik dalam satu alur pelayanan yang otomatis dan efisien.
        </p>
        <div class="hero-actions">
            <a href="/register" class="btn-main">Mulai Sekarang</a>
            <a href="#cara-kerja" class="btn-text">Lihat cara kerja →</a>
        </div>
    </div>
    <div class="hero-right">
        <div class="hero-right-bg"></div>
        <div class="hero-deco-letter">K</div>
        <div class="antrian-card">
            <p class="ac-label">Nomor Antrian</p>
            <div class="ac-num">A07</div>
            <p class="ac-name">Budi Santoso</p>
            <p class="ac-pet">🐱 Mochi · Kucing Persia</p>
            <div class="ac-divider"></div>
            <p class="ac-status">✓ Kedatangan terkonfirmasi</p>
        </div>
    </div>
</section>

<!-- STRIP -->
<div class="strip">
    <div class="strip-item">
        <span class="strip-icon">📱</span>
        <span class="strip-text">Antrian online dari rumah</span>
    </div>
    <div class="strip-dot"></div>
    <div class="strip-item">
        <span class="strip-icon">📋</span>
        <span class="strip-text">Rekam medis digital</span>
    </div>
    <div class="strip-dot"></div>
    <div class="strip-item">
        <span class="strip-icon">💊</span>
        <span class="strip-text">Manajemen obat real-time</span>
    </div>
    <div class="strip-dot"></div>
    <div class="strip-item">
        <span class="strip-icon">🔗</span>
        <span class="strip-text">Alur terhubung otomatis</span>
    </div>
    <div class="strip-dot"></div>
    <div class="strip-item">
        <span class="strip-icon">✅</span>
        <span class="strip-text">Konfirmasi kedatangan digital</span>
    </div>
</div>

<!-- LAYANAN / ROLES -->
<section class="layanan" id="layanan">
    <div class="section-head">
        <div>
            <p class="section-kicker">Untuk Siapa</p>
            <h2 class="section-h">Satu platform,<br><em>tiga peran.</em></h2>
        </div>
        <p class="section-p">
            KAWWAN dirancang untuk memenuhi kebutuhan spesifik setiap pihak yang terlibat dalam ekosistem klinik hewan — dari pemilik hingga tenaga medis.
        </p>
    </div>
    <div class="cards-row">
        <div class="role-card">
            <p class="rc-num">01</p>
            <div class="rc-icon">🏠</div>
            <h3 class="rc-title">Pemilik Hewan</h3>
            <p class="rc-desc">Akses layanan klinik kapan saja, dari mana saja, tanpa harus antre fisik.</p>
            <ul class="rc-list">
                <li>Lihat jadwal klinik online</li>
                <li>Ambil nomor antrian dari rumah</li>
                <li>Pantau status pemeriksaan</li>
            </ul>
        </div>
        <div class="role-card">
            <p class="rc-num">02</p>
            <div class="rc-icon">🩺</div>
            <h3 class="rc-title">Dokter Hewan</h3>
            <p class="rc-desc">Fokus pada pemeriksaan, biarkan sistem yang mencatat dan mengelola data.</p>
            <ul class="rc-list">
                <li>Catat rekam medis saat periksa</li>
                <li>Akses riwayat pasien sebelumnya</li>
                <li>Tulis resep langsung di sistem</li>
            </ul>
        </div>
        <div class="role-card">
            <p class="rc-num">03</p>
            <div class="rc-icon">🧑‍⚕️</div>
            <h3 class="rc-title">Kader Klinik</h3>
            <p class="rc-desc">Kelola operasional klinik sehari-hari dengan lebih cepat dan akurat.</p>
            <ul class="rc-list">
                <li>Konfirmasi kedatangan pasien</li>
                <li>Kelola data stok obat</li>
                <li>Perbarui status pengambilan obat</li>
            </ul>
        </div>
    </div>
</section>

<!-- ALUR KERJA -->
<section class="alur" id="cara-kerja">
    <p class="section-kicker">Cara Kerja</p>
    <h2 class="section-h">Dari rumah hingga obat,<br><em>semuanya terhubung.</em></h2>
    <div class="steps">
        <div class="step">
            <div class="step-n">01</div>
            <div class="step-icon">📝</div>
            <p class="step-title">Ambil Antrian Online</p>
            <p class="step-desc">Pemilik hewan mendaftar dan mengambil nomor antrian dari rumah sesuai jadwal yang tersedia.</p>
        </div>
        <div class="step">
            <div class="step-n">02</div>
            <div class="step-icon">✅</div>
            <p class="step-title">Konfirmasi Kedatangan</p>
            <p class="step-desc">Saat tiba di klinik, kader mengkonfirmasi kedatangan pasien secara digital dalam sistem.</p>
        </div>
        <div class="step">
            <div class="step-n">03</div>
            <div class="step-icon">🩺</div>
            <p class="step-title">Pemeriksaan & Rekam Medis</p>
            <p class="step-desc">Dokter memeriksa dan langsung mencatat rekam medis serta resep obat dalam sistem.</p>
        </div>
        <div class="step">
            <div class="step-n">04</div>
            <div class="step-icon">💊</div>
            <p class="step-title">Pengambilan Obat</p>
            <p class="step-desc">Kader menyiapkan dan memperbarui status obat. Pasien selesai tanpa hambatan.</p>
        </div>
    </div>
</section>

<!-- FITUR -->
<section class="fitur" id="fitur">
    <div class="fitur-left">
        <p class="section-kicker">Fitur</p>
        <h2 class="section-h">Semua yang klinik<br>hewan butuhkan.</h2>
        <p style="margin-top:1rem; font-size:.87rem; line-height:1.8; color:var(--ink-mid); max-width:320px;">
            Dirancang untuk mendigitalkan seluruh alur pelayanan, bukan sekadar memindahkan proses manual ke layar.
        </p>
    </div>
    <div class="fitur-right">
        <div class="fitur-item">
            <div class="fi-icon">📱</div>
            <div>
                <p class="fi-title">Antrian Online</p>
                <p class="fi-desc">Pemilik hewan bisa melihat jadwal dan mengambil nomor antrian dari rumah — tanpa perlu datang pagi hanya untuk antre.</p>
            </div>
        </div>
        <div class="fitur-item">
            <div class="fi-icon">📋</div>
            <div>
                <p class="fi-title">Rekam Medis Digital</p>
                <p class="fi-desc">Dokter mencatat langsung saat pemeriksaan. Riwayat tersimpan aman, mudah diakses kapan pun dibutuhkan.</p>
            </div>
        </div>
        <div class="fitur-item">
            <div class="fi-icon">✅</div>
            <div>
                <p class="fi-title">Konfirmasi Kedatangan</p>
                <p class="fi-desc">Kader mengkonfirmasi kehadiran pasien secara digital. Antrian berjalan tertib dan transparan untuk semua pihak.</p>
            </div>
        </div>
        <div class="fitur-item">
            <div class="fi-icon">💊</div>
            <div>
                <p class="fi-title">Manajemen Obat</p>
                <p class="fi-desc">Stok obat terkelola, status pengambilan diperbarui real-time. Tidak ada lagi miskomunikasi antara dokter dan kader.</p>
            </div>
        </div>
        <div class="fitur-item">
            <div class="fi-icon">🔗</div>
            <div>
                <p class="fi-title">Alur Otomatis & Terhubung</p>
                <p class="fi-desc">Setiap proses terhubung satu sama lain secara otomatis — data mengalir tanpa perlu input ulang di setiap tahap.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <p class="cta-eyebrow">Mulai Sekarang</p>
    <h2 class="cta-title">
        Saatnya klinik Anda<br>
        <em>beroperasi lebih cerdas.</em>
    </h2>
    <p class="cta-sub">
        Bergabunglah dengan KAWWAN dan digitalisasikan seluruh alur pelayanan klinik hewan Anda hari ini.
    </p>
    <div class="cta-btns">
        <a href="/register" class="btn-main">Daftar Sekarang</a>
        <a href="/login" class="btn-text">Sudah punya akun? Masuk →</a>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-logo">KAW<span>WAN</span></div>
    <ul class="footer-links">
        <li><a href="#layanan">Layanan</a></li>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#fitur">Fitur</a></li>
        <li><a href="/login">Masuk</a></li>
    </ul>
    <p class="footer-copy">© 2024 KAWWAN — Kawan Hewan</p>
</footer>

<script>
    const nav = document.getElementById('nav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('solid', window.scrollY > 10);
    });
</script>
</body>
</html>
