<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Dashboard Dokter Hewan' }} — KAWWAN Dokter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <div class="dokter-shell">

        <div class="dokter-backdrop" id="dokterBackdrop"></div>

        <aside class="dokter-sidebar" id="dokterSidebar">
            <a href="{{ route('dokter.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Dokter</p>

            <nav class="dokter-menu" aria-label="Navigasi dokter">
                <a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('dokter.riwayat.index') }}" class="{{ request()->routeIs('dokter.riwayat.*') ? 'active' : '' }}">
                    Riwayat Pemeriksaan
                </a>
                <a href="{{ route('dokter.profil') }}" class="{{ request()->routeIs('dokter.profil*') ? 'active' : '' }}">
                    Profil
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="dokter-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('dokter hewan')">
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
                    <h2>{{ 'Dashboard Dokter Hewan' }}</h2>
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
                        <span class="dokter-user-role">Dokter Hewan</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">
<div class="dokter-head">
        <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
        <p>Ringkasan pelayanan pemeriksaan hewan hari ini.</p>
    </div>

    <div class="dokter-stats">
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-paw"></i></div>
            <div>
                <p class="dokter-stat-label">Pasien Hari Ini</p>
                <p class="dokter-stat-value" data-count="{{ $stat['pasien_hari_ini'] }}">{{ $stat['pasien_hari_ini'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div>
                <p class="dokter-stat-label">Menunggu Pemeriksaan</p>
                <p class="dokter-stat-value" data-count="{{ $stat['menunggu_pemeriksaan'] }}">{{ $stat['menunggu_pemeriksaan'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div>
                <p class="dokter-stat-label">Selesai Diperiksa</p>
                <p class="dokter-stat-value" data-count="{{ $stat['selesai_diperiksa'] }}">{{ $stat['selesai_diperiksa'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-file-medical"></i></div>
            <div>
                <p class="dokter-stat-label">Total Pemeriksaan</p>
                <p class="dokter-stat-value" data-count="{{ $stat['total_pemeriksaan'] }}">{{ $stat['total_pemeriksaan'] }}</p>
            </div>
        </div>
    </div>

    <div class="dokter-charts" id="dokterChartContainer" data-chart='@json($grafik)'>
        <section class="dokter-card">
            <h3>Pemeriksaan 7 Hari Terakhir</h3>
            <p class="dokter-card-sub">Jumlah pemeriksaan yang dicatat per hari.</p>
            <div class="dokter-chart-box">
                <canvas id="chartHarian"></canvas>
            </div>
        </section>

        <section class="dokter-card">
            <h3>Hewan Diperiksa</h3>
            <p class="dokter-card-sub">Perbandingan berdasarkan jenis dan ras hewan.</p>
            @if (count($grafik['hewan']['data']) > 0)
                <div class="dokter-chart-box">
                    <canvas id="chartHewan"></canvas>
                </div>
            @else
                <p class="dokter-empty">Belum ada data pemeriksaan.</p>
            @endif
        </section>
    </div>

    <section class="dokter-card">
        <h3>Antrean Hari Ini</h3>
        <p class="dokter-card-sub">Daftar pemesanan pada jadwal pelayanan hari ini.</p>

        @if ($antrean->isEmpty())
            <p class="dokter-empty">Belum ada antrean untuk hari ini.</p>
        @else
            <div class="dokter-table-wrap">
                <table class="dokter-table">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <th>Pemilik</th>
                            <th>Jenis Hewan</th>
                            <th>Ras</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antrean as $item)
                            <tr>
                                <td>{{ $item['jam'] }}</td>
                                <td>{{ $item['pemilik'] }}</td>
                                <td><strong>{{ ucfirst($item['jenis']) }}</strong></td>
                                <td>{{ $item['ras'] }}</td>
                                <td><span class="dokter-badge is-{{ $item['status_kode'] }}">{{ $item['status_label'] }}</span></td>
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
