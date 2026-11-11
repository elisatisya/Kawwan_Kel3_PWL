<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Jadwal & Layanan Pelayanan' }} — KAWWAN Pemilik Hewan</title>

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
                    <h2>{{ 'Jadwal & Layanan Pelayanan' }}</h2>
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

    <div class="pemilik-page-head">
        <div>
            <h1>Jadwal &amp; <span>Layanan Pelayanan</span></h1>
            <p>Informasi jadwal praktik dokter hewan yang tersedia beserta daftar jenis layanan dan tarif puskeswan.</p>
        </div>
        <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-primary">
            Buat Pemesanan Antrean
        </a>
    </div>

    {{-- Bagian 1: Jadwal Pelayanan Tersedia --}}
    <section class="pemilik-card">
        <div class="pemilik-card-header">
            <div>
                <h2>Jadwal Praktik Dokter</h2>
                <p>Pilih jadwal yang masih memiliki kuota tersedia untuk mendaftarkan hewan Anda</p>
            </div>
        </div>

        @if ($jadwal->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Jadwal Pelayanan</h3>
                <p>Saat ini belum ada jadwal praktik dokter hewan yang dibuka untuk waktu mendatang.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Praktik</th>
                            <th>Dokter Bertugas</th>
                            <th>Layanan Dokter</th>
                            <th>Kuota</th>
                            <th>Sisa Kuota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal as $j)
                            @php
                                $sisa = $j->kuota === null ? null : max(0, $j->kuota - $j->terpakai);
                                $tersedia = $j->status === 'tersedia' && ($sisa === null || $sisa > 0);
                            @endphp
                            <tr>
                                <td><strong>{{ $j->tanggal->translatedFormat('d M Y') }}</strong></td>
                                <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                <td>{{ $j->dokter?->name ?? 'Dokter Puskeswan' }}</td>
                                <td>
                                    @if ($j->dokter && $j->dokter->layananDokter->isNotEmpty())
                                        <div class="pemilik-tag-list">
                                            @foreach ($j->dokter->layananDokter as $l)
                                                <span class="pemilik-badge is-neutral">{{ $l->nama }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span>Pelayanan Umum</span>
                                    @endif
                                </td>
                                <td>{{ $j->kuota ?? 'Tanpa batas' }}</td>
                                <td>{{ $sisa ?? 'Tersedia' }}</td>
                                <td>
                                    @if ($tersedia)
                                        <span class="pemilik-badge is-tersedia">Tersedia</span>
                                    @else
                                        <span class="pemilik-badge is-menunggu">Penuh</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($tersedia)
                                        <a href="{{ route('pemilik.pemesanan.index', ['jadwal' => $j->id]) }}" class="pemilik-btn-sm">
                                            Pesan Antrean
                                        </a>
                                    @else
                                        <span class="pemilik-text-muted">Kuota penuh</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="kader-pagination-wrap">
                {{ $jadwal->links() }}
            </div>
        @endif
    </section>

    {{-- Bagian 2: Daftar Layanan Pelayanan & Tarif --}}
    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Daftar Layanan &amp; Tarif Pelayanan</h2>
                <p>Jenis layanan pemeriksaan dan perawatan kesehatan hewan yang tersedia di puskeswan</p>
            </div>
        </div>

        @if ($layanan->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Data Layanan</h3>
                <p>Daftar layanan dan tarif pelayanan belum ditambahkan oleh petugas.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Layanan</th>
                            <th>Deskripsi</th>
                            <th>Estimasi Durasi</th>
                            <th>Tarif</th>
                            <th>Dokter Penanggung Jawab</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($layanan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $item->nama }}</strong></td>
                                <td>{{ $item->deskripsi ?: '-' }}</td>
                                <td>{{ $item->durasi_menit ? $item->durasi_menit . ' menit' : '-' }}</td>
                                <td>
                                    @if ($item->biaya == 0)
                                        <span class="pemilik-badge is-tersedia">Gratis</span>
                                    @else
                                        <strong>Rp {{ number_format($item->biaya, 0, ',', '.') }}</strong>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->dokter->isNotEmpty())
                                        {{ $item->dokter->pluck('name')->join(', ') }}
                                    @else
                                        Dokter Puskeswan
                                    @endif
                                </td>
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
