
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
                <!-- Card 1: Data Hewan -->
                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-paw"></i>
                    </div>
                    <h3>Data Hewan</h3>
                    <p>
                        Simpan dan kelola informasi hewan peliharaan
                        agar mudah diakses saat dibutuhkan.
                    </p>
                    <div class="service-card-footer">
                        <button type="button" class="btn-lihat-detail" data-layanan="hewan">
                            <span>Lihat Detail</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </article>

                <!-- Card 2: Pemesanan Antrean -->
                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3>Pemesanan Antrean</h3>
                    <p>
                        Lihat jadwal pelayanan yang tersedia dan
                        lakukan pemesanan dengan lebih praktis.
                    </p>
                    <div class="service-card-footer">
                        <button type="button" class="btn-lihat-detail" data-layanan="antrean">
                            <span>Lihat Detail</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </article>

                <!-- Card 3: Riwayat Pemeriksaan -->
                <article class="service-card">
                    <div class="service-icon">
                        <i class="fa-solid fa-file-medical"></i>
                    </div>
                    <h3>Riwayat Pemeriksaan</h3>
                    <p>
                        Akses catatan pemeriksaan untuk membantu
                        memantau riwayat kesehatan hewan.
                    </p>
                    <div class="service-card-footer">
                        <button type="button" class="btn-lihat-detail" data-layanan="riwayat">
                            <span>Lihat Detail</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
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

    <!-- ================= MODAL DETAIL LAYANAN KAWWAN ================= -->
    <div id="modalLayanan" class="kawwan-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="kawwan-modal-overlay" onclick="closeLayananModal()"></div>
        <div class="kawwan-modal-content">
            <div class="modal-header">
                <div class="modal-title-box">
                    <span id="modalLayananTag" class="badge-sub">Layanan KAWWAN</span>
                    <h3 id="modalLayananTitle">Detail Layanan</h3>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeLayananModal()" aria-label="Tutup">&times;</button>
            </div>

            <div class="modal-body">
                <p id="modalLayananDesc" class="modal-desc"></p>

                <div class="detail-info-grid">
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-clock"></i> Jam Operasional</div>
                        <div id="modalLayananJam" class="info-value">08.00 - 15.30 WIB</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-calendar-day"></i> Jadwal Layanan</div>
                        <div id="modalLayananHari" class="info-value">Senin s/d Sabtu</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-user-doctor"></i> Tim Dokter Hewan</div>
                        <div id="modalLayananDokter" class="info-value">Dokter Hewan Puskeswan</div>
                    </div>
                    <div class="info-box">
                        <div class="info-label"><i class="fa-solid fa-shield-cat"></i> Kuota & Akses</div>
                        <div id="modalLayananAkses" class="info-value">Tersedia Online</div>
                    </div>
                </div>

                <div class="prep-note">
                    <h4><i class="fa-solid fa-circle-check"></i> Kemudahan & Manfaat untuk Anda:</h4>
                    <ul id="modalLayananPoints">
                        <!-- Diisi secara dinamis via JS -->
                    </ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary-action" onclick="closeLayananModal()">Tutup</button>
                @auth
                    <a id="modalActionBtn" href="{{ url('/pemilik/hewan') }}" class="btn-primary-action">
                        <span id="modalActionText">Buka Halaman</span> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="btn-primary-action">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> <span id="modalLoginText">Login untuk Akses</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- STYLING KHUSUS BUTTON & MODAL DETAIL -->
    <style>
        .service-card-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #F5ECE3;
        }

        .btn-lihat-detail {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9px 18px;
            border-radius: 999px;
            background-color: #FFF5EE;
            border: 1.5px solid #FFD4BA;
            color: #FF6B00;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            box-sizing: border-box;
        }

        .btn-lihat-detail i {
            transition: transform 0.22s ease;
            font-size: 12px;
        }

        .btn-lihat-detail:hover {
            background-color: #FF6B00;
            border-color: #FF6B00;
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(255, 107, 0, 0.28);
            transform: translateY(-2px);
        }

        .btn-lihat-detail:hover i {
            transform: translateX(4px);
        }

        /* Modal Layanan */
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
    </style>

    <!-- SCRIPT POP-UP MODAL LAYANAN -->
    <script>
        const KAWWAN_SERVICES = {
            hewan: {
                tag: "Data Hewan Peliharaan",
                title: "Manajemen Data Hewan",
                desc: "Simpan profil lengkap hewan kesayangan Anda secara digital dan terpusat. Data nama, jenis, ras, jenis kelamin, usia, hingga riwayat khusus anabul akan langsung terhubung ke rekam medis dan antrean klinik.",
                jam: "Akses 24 Jam Mandiri",
                hari: "Tersedia Setiap Hari",
                dokter: "Terkoneksi ke Dokter & Kader",
                akses: "Data Terproteksi & Akurat",
                url: "{{ url('/pemilik/hewan') }}",
                actionText: "Buka Kelola Data Hewan",
                loginText: "Login untuk Kelola Data",
                points: [
                    "Pencatatan identitas resmi anabul untuk kepastian riwayat medis.",
                    "Dapat mendaftarkan banyak hewan sekaligus dalam satu akun pemilik.",
                    "Mempercepat verifikasi oleh kader saat tiba di klinik puskeswan.",
                    "Profil hewan dapat diperbarui kapan saja secara mandiri."
                ]
            },
            antrean: {
                tag: "Pemesanan Jadwal & Antrean",
                title: "Pemesanan Antrean Online",
                desc: "Pilih tanggal, jam operasional, dan dokter spesialis yang bertugas secara real-time. Dapatkan estimasi antrean yang jelas sehingga Anda tidak perlu menunggu lama di ruang tunggu puskeswan.",
                jam: "08.00 - 15.30 WIB",
                hari: "Senin s/d Sabtu",
                dokter: "drh. Amanda / drh. Reza",
                akses: "Sistem Kuota Terintegrasi",
                url: "{{ url('/pemilik/jadwal') }}",
                actionText: "Lihat Jadwal & Pesan Antrean",
                loginText: "Login untuk Pesan Antrean",
                points: [
                    "Melihat kuota pasien yang masih tersedia pada setiap sesi layanan.",
                    "Pilihan layanan lengkap: Vaksinasi, Pemeriksaan Umum, atau Konsultasi.",
                    "Konfirmasi status pemesanan secara transparan dan terdata aman.",
                    "Pemberitahuan nomor antrean langsung di perangkat Anda."
                ]
            },
            riwayat: {
                tag: "Catatan Medis & Pemeriksaan",
                title: "Rekam Medis Digital Hewan",
                desc: "Pantau riwayat kesehatan, diagnosa penyakit, tindakan medis, resep obat, serta catatan khusus dari dokter hewan pemeriksa yang tersimpan secara terstruktur dan aman.",
                jam: "Akses Riwayat 24 Jam",
                hari: "Tersedia Kapan Saja",
                dokter: "Diverifikasi Tim Dokter Resmi",
                akses: "Privasi Terjaga Antar Pemilik",
                url: "{{ url('/pemilik/pemesanan') }}",
                actionText: "Lihat Riwayat Pemeriksaan",
                loginText: "Login untuk Lihat Riwayat",
                points: [
                    "Riwayat catatan medis tersimpan permanen dan tidak hilang.",
                    "Melihat rincian tindakan dan instruksi perawatan dari dokter.",
                    "Mempermudah penanganan saat pemeriksaan berkala atau rujukan.",
                    "Hanya bisa diakses oleh pemilik hewan yang bersangkutan."
                ]
            }
        };

        function openLayananModal(key) {
            const data = KAWWAN_SERVICES[key];
            if (!data) return;

            const modal = document.getElementById('modalLayanan');
            if (!modal) return;

            document.getElementById('modalLayananTag').textContent = data.tag;
            document.getElementById('modalLayananTitle').textContent = data.title;
            document.getElementById('modalLayananDesc').textContent = data.desc;
            document.getElementById('modalLayananJam').textContent = data.jam;
            document.getElementById('modalLayananHari').textContent = data.hari;
            document.getElementById('modalLayananDokter').textContent = data.dokter;
            document.getElementById('modalLayananAkses').textContent = data.akses;

            const actionBtn = document.getElementById('modalActionBtn');
            const actionText = document.getElementById('modalActionText');
            if (actionBtn && actionText) {
                actionBtn.href = data.url;
                actionText.textContent = data.actionText;
            }

            const loginText = document.getElementById('modalLoginText');
            if (loginText) {
                loginText.textContent = data.loginText;
            }

            const pointsList = document.getElementById('modalLayananPoints');
            if (pointsList) {
                pointsList.innerHTML = '';
                data.points.forEach(point => {
                    const li = document.createElement('li');
                    li.textContent = point;
                    pointsList.appendChild(li);
                });
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLayananModal() {
            const modal = document.getElementById('modalLayanan');
            if (modal) {
                modal.classList.remove('active');
            }
            document.body.style.overflow = '';
        }

        // Event listener saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-layanan]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const key = this.getAttribute('data-layanan');
                    openLayananModal(key);
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeLayananModal();
                }
            });
        });
    </script>
</body>
</html>
