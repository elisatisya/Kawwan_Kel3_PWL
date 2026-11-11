<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Dashboard Pemilik Hewan' }} — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        <div class="pemilik-backdrop" id="pemilikBackdrop"></div>

        <aside class="pemilik-sidebar" id="pemilikSidebar">
            <a href="{{ route('pemilik.dashboard') }}" class="pemilik-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="pemilik-menu-label">Menu Pemilik</p>

            <nav class="pemilik-menu" aria-label="Navigasi Pemilik Hewan">
                <a href="{{ route('pemilik.dashboard') }}" class="{{ request()->routeIs('pemilik.dashboard') || request()->routeIs('pemilik.beranda') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pemilik.hewan.index') }}" class="{{ request()->routeIs('pemilik.hewan.*') ? 'active' : '' }}">
                    <span>Data Hewan</span>
                </a>
                <a href="{{ route('pemilik.jadwal.index') }}" class="{{ request()->routeIs('pemilik.jadwal.*') ? 'active' : '' }}">
                    <span>Jadwal Pelayanan</span>
                </a>
                <a href="{{ route('pemilik.pemesanan.index') }}" class="{{ request()->routeIs('pemilik.pemesanan.*') ? 'active' : '' }}">
                    <span>Pemesanan Antrean</span>
                </a>
                <a href="{{ route('landing') }}#artikel">
                    <span>Artikel Edukasi</span>
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="pemilik-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('pemilik hewan')">
                    Keluar
                </button>
            </form>
        </aside>

        <div class="pemilik-main-wrap">
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        Menu
                    </button>
                    <h2>{{ 'Dashboard Pemilik Hewan' }}</h2>
                </div>

                <div class="pemilik-user-profile">
                    <div class="pemilik-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </div>
            </header>

            <main class="pemilik-main">
<div class="pemilik-container">

    <section class="pemilik-welcome">
        <div class="pemilik-welcome-content">
            <div class="pemilik-welcome-text">
                <span class="pemilik-welcome-tag">Portal Pemilik Hewan</span>
                <h1>Halo, {{ $user->name }}</h1>
                <p>Kelola data kesehatan hewan peliharaan Anda, lihat jadwal dan layanan puskeswan yang tersedia, serta pantau status antrean secara terpadu.</p>
            </div>
            <div class="pemilik-welcome-actions">
                <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-primary">
                    <span>Kelola Data Hewan</span>
                </a>
                <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-outline">
                    <span>Pesan Antrean Pelayanan</span>
                </a>
            </div>
        </div>
    </section>

    <section class="pemilik-stats">
        <div class="pemilik-stat-card">
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Hewan Peliharaan</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_hewan'] }}">{{ $stat['total_hewan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Antrean Aktif</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['antrean_aktif'] }}">{{ $stat['antrean_aktif'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Riwayat Rekam Medis</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_pemeriksaan'] }}">{{ $stat['total_pemeriksaan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Jadwal Tersedia</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['jadwal_tersedia'] }}">{{ $stat['jadwal_tersedia'] }}</span>
            </div>
        </div>
    </section>

    <div class="pemilik-grid-2">
        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>Hewan Peliharaan Saya</h2>
                    <p>Daftar hewan yang terhubung dengan akun Anda</p>
                </div>
                <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn-sm">
                    Tambah / Kelola
                </a>
            </div>

            @if ($daftarHewan->isEmpty())
                <div class="pemilik-empty">
                    <h3>Belum Ada Data Hewan</h3>
                    <p>Daftarkan hewan peliharaan Anda sekarang untuk memudahkan pemesanan antrean dan pencatatan rekam medis puskeswan.</p>
                    <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-primary">
                        <span>Daftarkan Hewan Sekarang</span>
                    </a>
                </div>
            @else
                <div class="pemilik-pet-list">
                    @foreach ($daftarHewan as $pet)
                        <div class="pemilik-pet-card">
                            <div class="pemilik-pet-details">
                                <div class="pemilik-pet-top">
                                    <h4 class="pemilik-pet-name">{{ ucfirst($pet->jenis) }}</h4>
                                    <span class="pemilik-badge is-neutral">{{ $pet->ras ?: 'Campuran' }}</span>
                                </div>
                                <div class="pemilik-pet-meta">
                                    <span>Kelamin: {{ ucfirst($pet->jenis_kelamin) }}</span>
                                    @if ($pet->umur)
                                        <span>Umur: {{ $pet->umur }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>Pemesanan &amp; Antrean Aktif</h2>
                    <p>Status pemeriksaan hewan yang sedang berjalan</p>
                </div>
                <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn-sm">
                    Lihat Semua
                </a>
            </div>

            @if ($pemesananAktif->isEmpty())
                <div class="pemilik-empty">
                    <h3>Tidak Ada Antrean Aktif</h3>
                    <p>Anda belum memiliki jadwal pemesanan antrean pelayanan yang sedang menunggu atau aktif.</p>
                    <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-outline">
                        <span>Pesan Antrean Pemeriksaan</span>
                    </a>
                </div>
            @else
                <div class="pemilik-booking-list">
                    @foreach ($pemesananAktif as $order)
                        <div class="pemilik-booking-card">
                            <div class="pemilik-booking-main">
                                <div class="pemilik-booking-date">
                                    <span class="pemilik-booking-day">{{ \Carbon\Carbon::parse($order->jadwalPelayanan?->tanggal)->format('d') }}</span>
                                    <span class="pemilik-booking-month">{{ \Carbon\Carbon::parse($order->jadwalPelayanan?->tanggal)->format('M Y') }}</span>
                                </div>
                                <div class="pemilik-booking-details">
                                    <h4>{{ ucfirst($order->hewan?->jenis ?? 'Hewan') }}{{ $order->hewan?->ras ? ' (' . $order->hewan->ras . ')' : '' }}</h4>
                                    <p>{{ substr($order->jadwalPelayanan?->waktu_mulai ?? '', 0, 5) }} - {{ substr($order->jadwalPelayanan?->waktu_selesai ?? '', 0, 5) }} WIB</p>
                                </div>
                            </div>
                            <div class="pemilik-booking-status">
                                @php
                                    $statusClass = match(strtolower((string)$order->status)) {
                                        'dikonfirmasi' => 'is-dikonfirmasi',
                                        'sedang_dilayani' => 'is-sedang-dilayani',
                                        default => 'is-menunggu',
                                    };
                                @endphp
                                <span class="pemilik-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Jadwal Pelayanan Puskeswan Terdekat</h2>
                <p>Pilih jadwal yang masih memiliki kuota untuk membawa hewan peliharaan Anda</p>
            </div>
            <a href="{{ route('pemilik.jadwal.index') }}" class="pemilik-btn-sm">
                Semua Jadwal &amp; Layanan
            </a>
        </div>

        @if ($jadwalTersedia->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Jadwal Pelayanan</h3>
                <p>Saat ini belum ada jadwal pelayanan puskeswan yang dibuka. Silakan cek kembali nanti.</p>
            </div>
        @else
            <div class="pemilik-schedule-grid">
                @foreach ($jadwalTersedia as $jadwal)
                    <div class="pemilik-schedule-card">
                        <div class="pemilik-schedule-header">
                            <span class="pemilik-badge is-tersedia">Tersedia</span>
                            <span class="pemilik-schedule-quota">Kuota: {{ $jadwal->kuota ?? 'Tanpa batas' }}</span>
                        </div>
                        <div class="pemilik-schedule-body">
                            <div class="pemilik-schedule-calendar">
                                <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="pemilik-schedule-time">
                                <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                            </div>
                        </div>
                        <div class="pemilik-schedule-footer">
                            <a href="{{ route('pemilik.pemesanan.index', ['jadwal' => $jadwal->id]) }}" class="pemilik-btn-schedule">
                                <span>Pilih Jadwal</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Riwayat Rekam Medis Terakhir</h2>
                <p>Hasil diagnosa, tindakan, dan catatan medis dari dokter hewan</p>
            </div>
        </div>

        @if ($riwayatPemeriksaan->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Riwayat Pemeriksaan</h3>
                <p>Riwayat rekam medis hewan peliharaan Anda setelah selesai diperiksa oleh dokter hewan akan tampil di sini.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis Hewan</th>
                            <th>Keluhan</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>Tindakan Dokter</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatPemeriksaan as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->jadwalPelayanan?->tanggal)->format('d M Y') }}</td>
                                <td><strong>{{ ucfirst($history->hewan?->jenis ?? '-') }}{{ $history->hewan?->ras ? ' (' . $history->hewan->ras . ')' : '' }}</strong></td>
                                <td>{{ $history->pemeriksaan?->keluhan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->hasil_pemeriksaan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->tindakan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>
            </main>
        </div>
    </div>

</body>
</html>
