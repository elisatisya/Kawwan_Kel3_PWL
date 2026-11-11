<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Dashboard Kader' }} — KAWWAN Kader</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
=======
@extends('kader.layout')
>>>>>>> origin/main

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

<<<<<<< HEAD
    <div class="dokter-shell">

        <div class="dokter-backdrop" id="dokterBackdrop"></div>

        <aside class="dokter-sidebar" id="dokterSidebar">
            <a href="{{ route('kader.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Kader Puskesmas</p>

            <nav class="dokter-menu" aria-label="Navigasi kader">
                <a href="{{ route('kader.dashboard') }}" class="{{ request()->routeIs('kader.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('kader.jadwal.index') }}" class="{{ request()->routeIs('kader.jadwal.*') ? 'active' : '' }}">
                    Jadwal Pelayanan
                </a>
                <a href="{{ route('kader.pemesanan.index') }}" class="{{ request()->routeIs('kader.pemesanan.*') || request()->routeIs('kader.pembayaran.*') ? 'active' : '' }}">
                    Booking & Pembayaran
                </a>
                <a href="{{ route('kader.layanan.index') }}" class="{{ request()->routeIs('kader.layanan.*') ? 'active' : '' }}">
                    Layanan & Harga
                </a>
                <a href="{{ route('kader.antrean.index') }}" class="{{ request()->routeIs('kader.antrean.*') ? 'active' : '' }}">
                    Antrean
                </a>
                <a href="{{ route('kader.artikel.index') }}" class="{{ request()->routeIs('kader.artikel.*') ? 'active' : '' }}">
                    Artikel
                </a>
                <a href="{{ route('kader.profil') }}" class="{{ request()->routeIs('kader.profil*') ? 'active' : '' }}">
                    Profil
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="dokter-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('kader puskesmas')">
                    Keluar
                </button>
            </form>
        </aside>

        <div class="dokter-main">
            <header class="dokter-topbar">
                <div class="dokter-topbar-left">
                    <button class="dokter-menu-toggle" id="dokterMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>{{ 'Dashboard Kader' }}</h2>
                </div>

                <div class="dokter-user">
                    <div class="dokter-avatar">
                        @if (auth()->user()->foto)
                            <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>

                    <div>
                        <span class="dokter-user-name">{{ auth()->user()->name }}</span>
                        <span class="dokter-user-role">Kader Puskesmas</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">
=======
>>>>>>> origin/main
{{-- HEADER --}}
<div class="dokter-head">
    <h1>Dashboard <span>Kader Puskesmas</span></h1>
    <p>Selamat datang di panel pengelolaan pelayanan KAWWAN.</p>
</div>

{{-- STATISTIK DASHBOARD --}}
<div class="kader-dashboard-stats">

    <div class="dokter-card">
        <h3>Booking Hari Ini</h3>
        <h2>{{ $stat['booking_hari_ini'] }}</h2>
        <p>Total pemesanan hari ini</p>
    </div>

    <div class="dokter-card">
        <h3>Menunggu Kedatangan</h3>
        <h2>{{ $stat['menunggu_kedatangan'] }}</h2>
        <p>Pemilik yang menunggu pelayanan</p>
    </div>

    <div class="dokter-card">
        <h3>Belum Dibayar</h3>
        <h2>{{ $stat['belum_dibayar'] }}</h2>
        <p>Pemesanan belum memiliki pembayaran</p>
    </div>

    <div class="dokter-card">
        <h3>Artikel Diajukan</h3>
        <h2>{{ $stat['artikel_diajukan'] }}</h2>
        <p>Artikel yang diajukan</p>
    </div>

</div>

{{-- JADWAL PELAYANAN HARI INI --}}
<section class="dokter-card">

    <h2>Jadwal Pelayanan Hari Ini</h2>
    <p>Daftar pemesanan berdasarkan jadwal pelayanan.</p>

    @if ($pemesananHariIni->isEmpty())

        <p class="dokter-empty">
            Belum ada pemesanan untuk hari ini.
        </p>

    @else

        <div class="dokter-table-wrap">
            <table class="dokter-table">

                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pemilik</th>
                        <th>Jenis Hewan</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pemesananHariIni as $p)

                        @php
                            $bayar = $pembayaran->get($p->id);
                        @endphp

                        <tr>
                            {{-- WAKTU --}}
                            <td>
                                {{ substr($p->jadwalPelayanan->waktu_mulai, 0, 5) }}
                                -
                                {{ substr($p->jadwalPelayanan->waktu_selesai, 0, 5) }}
                            </td>

                            {{-- PEMILIK --}}
                            <td>
                                {{ $p->user?->name ?? '-' }}
                            </td>

                            {{-- HEWAN --}}
                            <td>
                                {{ ucfirst($p->hewan?->jenis ?? '-') }}{{ $p->hewan?->ras ? ' (' . $p->hewan->ras . ')' : '' }}
                            </td>

                            {{-- STATUS BOOKING --}}
                            <td>
                                <span class="dokter-badge is-{{ $p->status }}">
                                    {{ $labelStatus[$p->status] ?? ucfirst($p->status) }}
                                </span>
                            </td>

                            {{-- STATUS PEMBAYARAN --}}
                            <td>
                                @if ($bayar && $bayar->status === 'lunas')

                                    <span class="admin-status-badge is-aktif">
                                        Lunas
                                    </span>

                                @elseif ($bayar && $bayar->status === 'menunggu_verifikasi')

                                    <span class="admin-status-badge is-menunggu">
                                        Menunggu Verifikasi
                                    </span>

                                @elseif ($bayar && $bayar->status === 'ditolak')

                                    <span class="admin-status-badge is-menunggu">
                                        Ditolak
                                    </span>

                                @else

                                    <span class="admin-status-badge is-menunggu">
                                        Belum Dibayar
                                    </span>

                                @endif
                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

    @endif

</section>
            </main>
        </div>

    </div>

</body>
</html>
