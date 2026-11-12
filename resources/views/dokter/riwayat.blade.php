<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($hewan) ? 'Catatan Pasien' : 'Riwayat Pemeriksaan' }} — KAWWAN Dokter</title>

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
                    <h2>{{ isset($hewan) ? 'Catatan Pasien' : 'Riwayat Pemeriksaan' }}</h2>
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
@isset($hewan)
        <a href="{{ route('dokter.riwayat.index') }}" class="riwayat-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke riwayat
        </a>

        <section class="dokter-card">
            <h3>{{ ucfirst($hewan->jenis) }}{{ $hewan->ras ? ' (' . $hewan->ras . ')' : '' }}</h3>
            <p class="dokter-card-sub">Identitas pasien hewan.</p>

            <div class="riwayat-info">
                <div><span>Jenis Hewan</span><strong>{{ ucfirst($hewan->jenis) }}</strong></div>
                <div><span>Ras</span><strong>{{ $hewan->ras ?: '-' }}</strong></div>
                <div><span>Jenis Kelamin</span><strong>{{ ucfirst($hewan->jenis_kelamin) }}</strong></div>
                <div><span>Umur</span><strong>{{ $hewan->umur ?? '-' }}</strong></div>
                <div><span>Pemilik</span><strong>{{ $hewan->pemilik?->name ?? '-' }}</strong></div>
                <div><span>Telepon</span><strong>{{ $hewan->pemilik?->nomor_telepon ?: '-' }}</strong></div>
            </div>
        </section>

        <div class="riwayat-list">
            @forelse ($riwayat as $r)
                <section class="dokter-card">
                    <div class="riwayat-item-head">
                        <span class="riwayat-item-date">{{ $r->created_at->format('d M Y, H:i') }} WIB</span>
                        <span class="dokter-badge is-{{ $r->pemesanan->status }}">{{ \Illuminate\Support\Str::headline($r->pemesanan->status) }}</span>
                    </div>

                    <div class="riwayat-field"><span>Keluhan</span><p>{{ $r->keluhan ?: '-' }}</p></div>
                    <div class="riwayat-field"><span>Hasil Pemeriksaan</span><p>{{ $r->hasil_pemeriksaan ?: '-' }}</p></div>
                    <div class="riwayat-field"><span>Tindakan</span><p>{{ $r->tindakan ?: '-' }}</p></div>
                    <div class="riwayat-field"><span>Catatan</span><p>{{ $r->catatan ?: '-' }}</p></div>
                </section>
            @empty
                <section class="dokter-card">
                    <p class="dokter-empty">Belum ada catatan pemeriksaan untuk hewan ini.</p>
                </section>
            @endforelse
        </div>
    @else
        <div class="riwayat-head">
            <h2 class="riwayat-judul">Daftar Pasien Hewan</h2>
            <p class="riwayat-sub">Pilih hewan untuk melihat catatan pemeriksaannya.</p>
        </div>

        <section class="dokter-card">
            <form method="GET" action="{{ route('dokter.riwayat.index') }}" class="riwayat-search">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari jenis hewan atau pemilik...">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>

            @if ($pasien->isEmpty())
                <p class="dokter-empty">Belum ada riwayat pemeriksaan.</p>
            @else
                <div class="dokter-table-wrap">
                    <table class="riwayat-table">
                        <thead>
                            <tr>
                                <th>Jenis Hewan</th>
                                <th>Ras</th>
                                <th>Pemilik</th>
                                <th>Total</th>
                                <th>Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pasien as $h)
                                @php
                                    $catatan = $h->pemesanan->pluck('pemeriksaan')->filter();
                                    $terakhir = $catatan->sortByDesc('created_at')->first()?->created_at;
                                @endphp
                                <tr>
                                    <td><strong>{{ ucfirst($h->jenis) }}</strong></td>
                                    <td>{{ $h->ras ?: '-' }}</td>
                                    <td>{{ $h->pemilik?->name ?? '-' }}</td>
                                    <td>{{ $catatan->count() }}</td>
                                    <td>{{ $terakhir ? $terakhir->format('d M Y') : '-' }}</td>
                                    <td><a href="{{ route('dokter.riwayat.show', $h) }}" class="riwayat-link">Lihat catatan</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $pasien->links() }}
            @endif
        </section>
    @endisset
            </main>
        </div>

    </div>

</body>
</html>
