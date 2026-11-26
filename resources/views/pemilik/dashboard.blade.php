<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Dashboard Pemilik Hewan' }} — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        @include('pemilik.partials.navbar')

        <div class="pemilik-main-wrap">
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>Dashboard Pemilik Hewan</h2>
                </div>

                <a href="{{ route('pemilik.profil') }}" class="pemilik-user-profile" title="Lihat Profil">
                    <div class="pemilik-avatar">
                        @if (auth()->user()->foto)
                            <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </a>
            </header>

            <main class="pemilik-main">
<div class="pemilik-container">

    <section class="pemilik-hero-banner">
        <div class="pemilik-welcome-text">
            <span class="pemilik-welcome-tag"><i class="fa-solid fa-paw"></i> Portal Pemilik Hewan</span>
            <h1>Halo, {{ $user->name }}</h1>
            <p>Kelola data kesehatan hewan peliharaan Anda, lihat jadwal dan layanan puskeswan yang tersedia, serta pantau status antrean secara terpadu.</p>
            <div class="pemilik-welcome-actions" style="margin-top: 18px;">
                <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-primary">
                    <i class="fa-regular fa-calendar-check"></i>
                    <span>Pesan Antrean Pelayanan</span>
                </a>
                <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-outline">
                    <i class="fa-solid fa-paw"></i>
                    <span>Kelola Data Hewan</span>
                </a>
            </div>
        </div>
        <div class="pemilik-hero-visual">
            <img src="{{ asset('images/klinik-hewan.png') }}" alt="Ilustrasi Klinik Hewan KAWWAN">
        </div>
    </section>

    <section class="pemilik-stats">
        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon"><i class="fa-solid fa-paw"></i></div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Hewan Peliharaan</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_hewan'] }}">{{ $stat['total_hewan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon"><i class="fa-solid fa-ticket"></i></div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Antrean Aktif</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['antrean_aktif'] }}">{{ $stat['antrean_aktif'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon"><i class="fa-solid fa-file-medical"></i></div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Riwayat Rekam Medis</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_pemeriksaan'] }}">{{ $stat['total_pemeriksaan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
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
                    <div class="pemilik-empty-icon"><i class="fa-solid fa-paw"></i></div>
                    <h3>Belum Ada Data Hewan</h3>
                    <p>Daftarkan hewan peliharaan Anda sekarang untuk memudahkan pemesanan antrean dan pencatatan rekam medis puskeswan.</p>
                    <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        <span>Daftarkan Hewan Sekarang</span>
                    </a>
                </div>
            @else
                <div class="pemilik-pet-list">
                    @foreach ($daftarHewan as $pet)
                        @php
                            $petIcon = match(strtolower($pet->jenis)) {
                                'kucing' => 'fa-cat',
                                'anjing' => 'fa-dog',
                                'burung' => 'fa-dove',
                                default => 'fa-paw',
                            };
                        @endphp
                        <div class="pemilik-pet-card">
                            <div class="pemilik-pet-avatar">
                                <i class="fa-solid {{ $petIcon }}"></i>
                            </div>
                            <div class="pemilik-pet-details">
                                <div class="pemilik-pet-top">
                                    <h4 class="pemilik-pet-name">{{ ucfirst($pet->jenis) }}</h4>
                                    <span class="pemilik-badge is-neutral">{{ $pet->ras ?: 'Campuran' }}</span>
                                </div>
                                <div class="pemilik-pet-meta">
                                    <span><i class="fa-solid fa-venus-mars"></i> {{ ucfirst($pet->jenis_kelamin) }}</span>
                                    @if ($pet->umur)
                                        <span><i class="fa-regular fa-clock"></i> {{ $pet->umur }}</span>
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
                    <div class="pemilik-empty-icon"><i class="fa-regular fa-calendar-check"></i></div>
                    <h3>Tidak Ada Antrean Aktif</h3>
                    <p>Anda belum memiliki jadwal pemesanan antrean pelayanan yang sedang menunggu atau aktif.</p>
                    <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-outline">
                        <i class="fa-solid fa-ticket"></i>
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
                <div class="pemilik-empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
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
                                <i class="fa-regular fa-calendar"></i>
                                <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="pemilik-schedule-time">
                                <i class="fa-regular fa-clock"></i>
                                <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                            </div>
                        </div>
                        <div class="pemilik-schedule-footer">
                            <a href="{{ route('pemilik.pemesanan.index', ['jadwal' => $jadwal->id]) }}" class="pemilik-btn-schedule">
                                <span>Pilih Jadwal</span>
                                <i class="fa-solid fa-arrow-right"></i>
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
                <div class="pemilik-empty-icon"><i class="fa-solid fa-file-medical"></i></div>
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
