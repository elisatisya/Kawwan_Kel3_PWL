<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Daftar Antrean' }} — KAWWAN Kader</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

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
                    <h2>{{ 'Daftar Antrean' }}</h2>
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
@extends('kader.layout')

@section('title', 'Daftar Antrean')

@section('content')

>>>>>>> origin/main
<div class="antrean-page">

    <div class="antrean-heading">
        <h1>Daftar <span>Antrean</span></h1>
        <p>Kelola dan pantau antrean pelayanan hewan.</p>
    </div>

    <section class="antrean-panel">

        <div class="antrean-summary">
            <h2>Data Antrean</h2>

            <span class="antrean-count">
                Total: {{ $antrean->count() }} Antrean
            </span>
        </div>

        <div class="antrean-table-wrap">
            <table class="antrean-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Antrean</th>
                        <th>Tanggal</th>
                        <th>Pemilik</th>
<<<<<<< HEAD
                        <th>Jenis Hewan</th>
=======
                        <th>Hewan</th>
>>>>>>> origin/main
                        <th>Layanan</th>
                        <th>Dokter</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($antrean as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <span class="antrean-number">
                                    {{ $item->nomor_antrean }}
                                </span>
                            </td>

                            <td>
                                {{ $item->tanggal?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $item->pemesanan?->user?->name ?? '-' }}
                            </td>

                            <td>
<<<<<<< HEAD
                                {{ ucfirst($item->pemesanan?->hewan?->jenis ?? '-') }}{{ $item->pemesanan?->hewan?->ras ? ' (' . $item->pemesanan->hewan->ras . ')' : '' }}
=======
                                {{ $item->pemesanan?->hewan?->nama ?? '-' }}
>>>>>>> origin/main
                            </td>

                            <td>
                                @forelse ($item->pemesanan?->pemesananLayanan ?? [] as $detail)
                                    <div>{{ $detail->layanan?->nama ?? '-' }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>
                                {{ $item->dokter?->name ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match ($item->status) {
                                        'menunggu' => 'antrean-menunggu',
                                        'dipanggil' => 'antrean-dipanggil',
                                        'sedang_dilayani' => 'antrean-sedang',
                                        'selesai' => 'antrean-selesai',
                                        'tidak_hadir' => 'antrean-tidak-hadir',
                                        default => '',
                                    };
                                @endphp

                                <span class="antrean-status {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="antrean-empty">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                    Belum ada antrean yang diterbitkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>
</div>
<<<<<<< HEAD
            </main>
        </div>

    </div>

</body>
</html>
=======

@endsection

>>>>>>> origin/main
