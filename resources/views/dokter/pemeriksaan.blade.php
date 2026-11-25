<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan — KAWWAN Dokter</title>

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
            <a href="{{ route('dokter.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Dokter</p>

            <nav class="dokter-menu" aria-label="Navigasi dokter">
                <a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('dokter.jadwal.index') }}" class="{{ request()->routeIs('dokter.jadwal.*') ? 'active' : '' }}">
                    Jadwal Praktik
                </a>
                <a href="{{ route('dokter.pemeriksaan.index') }}" class="{{ request()->routeIs('dokter.pemeriksaan.*') ? 'active' : '' }}">
                    Pemeriksaan
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
                    <h2>Pemeriksaan</h2>
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
                        <span class="dokter-user-name">{{ trim((auth()->user()->gelar_depan ? auth()->user()->gelar_depan.' ' : '').auth()->user()->name.(auth()->user()->gelar_belakang ? ', '.auth()->user()->gelar_belakang : '')) }}</span>
                        <span class="dokter-user-role">Dokter Hewan</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">

                @if (session('success'))
                    <div class="dokter-alert is-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="dokter-alert is-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
                @endif

                <section class="dokter-card">
                    <div class="dokter-card-head">
                        <div>
                            <h3>Antrean Pasien</h3>
                            <p class="dokter-card-sub">Pasien yang pembayarannya sudah diverifikasi Kader.</p>
                        </div>

                        <form method="GET" class="dokter-toolbar">
                            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()">
                        </form>
                    </div>

                    @if ($antrean->isEmpty())
                        <p class="dokter-empty">Tidak ada antrean pada tanggal ini.</p>
                    @else
                        <div class="dokter-table-wrap">
                            <table class="dokter-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Jenis Hewan</th>
                                        <th>Pemilik</th>
                                        <th>Layanan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($antrean as $a)
                                        @php $p = $a->pemesanan; @endphp
                                        <tr>
                                            <td><strong>{{ $a->nomor_antrean }}</strong></td>
                                            <td>
                                                 {{ ucfirst($p->hewan?->jenis ?? '-') }}{{ $p->hewan?->ras ? ' ('.$p->hewan->ras.')' : '' }}
                                            </td>
                                            <td>{{ $p->user?->name ?? '-' }}</td>
                                            <td>{{ $p->pemesananLayanan->map(fn ($l) => $l->layanan?->nama)->filter()->join(', ') ?: '-' }}</td>
                                            <td>
                                                <span class="dokter-badge is-{{ $a->status }}">{{ \Illuminate\Support\Str::headline($a->status) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('dokter.pemeriksaan.show', $a) }}" class="btn btn-primary btn-sm">
                                                    {{ $a->status === 'selesai' ? 'Lihat' : 'Periksa' }}
                                                </a>
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