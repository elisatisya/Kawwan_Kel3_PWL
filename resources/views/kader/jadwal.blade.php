<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($dokter) ? 'Tambah Jadwal' : 'Jadwal Pelayanan' }} — KAWWAN Kader</title>

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
                    <h2>{{ isset($dokter) ? 'Tambah Jadwal' : 'Jadwal Pelayanan' }}</h2>
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
@isset($dokter)
    <div class="jadwal-form">

        <h1>Tambah <span>Jadwal Pelayanan</span></h1>
        <p>Tentukan dokter, tanggal, waktu, dan kuota pelayanan.</p>

        @if ($dokter->isEmpty())
            <div class="form-error">
                Belum ada akun dokter yang terdaftar.
            </div>
        @endif

        <form action="{{ route('kader.jadwal.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Dokter yang Bertugas *</label>

                <select name="dokter_id" required>
                    <option value="">Pilih dokter</option>

                    @foreach ($dokter as $d)
                        <option value="{{ $d->id }}"
                            @selected(old('dokter_id') == $d->id)>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                @error('dokter_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Tanggal Pelayanan *</label>
                <input type="date" name="tanggal"
                       min="{{ now()->toDateString() }}"
                       value="{{ old('tanggal') }}" required>

                @error('tanggal')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Waktu Mulai *</label>
                <input type="time" name="waktu_mulai"
                       value="{{ old('waktu_mulai') }}" required>

                @error('waktu_mulai')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Waktu Selesai *</label>
                <input type="time" name="waktu_selesai"
                       value="{{ old('waktu_selesai') }}" required>

                @error('waktu_selesai')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Kuota Pelayanan</label>
                <input type="number" name="kuota" min="1"
                       value="{{ old('kuota') }}"
                       placeholder="Kosongkan jika tanpa batas">

                @error('kuota')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="form-btn form-primary"
                        {{ $dokter->isEmpty() ? 'disabled' : '' }}>
                    <i class="fa-solid fa-save"></i>
                    Simpan Jadwal
                </button>

                <a href="{{ route('kader.jadwal.index') }}"
                   class="form-btn form-secondary">
                    Kembali
                </a>
            </div>
        </form>

    </div>
@else
    <div class="jadwal-heading">
        <div>
            <h1>Jadwal <span>Pelayanan</span></h1>
            <p>Pantau jadwal praktik dokter, layanan, dan kuota pelayanan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="jadwal-alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <section class="dokter-card">

        @if ($jadwal->isEmpty())
            <div class="jadwal-empty">
                <i class="fa-regular fa-calendar-xmark jadwal-empty-icon"></i>

                <p>Belum ada jadwal pelayanan yang akan datang.</p>
            </div>
        @else

            <div class="jadwal-table-wrap">
                <table class="jadwal-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Dokter</th>
                            <th>Layanan</th>
                            <th>Jam Praktik</th>
                            <th>Kuota</th>
                            <th>Terpakai</th>
                            <th>Sisa Kuota</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jadwal as $j)
                            @php
                                $sisa = $j->kuota === null
                                    ? null
                                    : max(0, $j->kuota - $j->terpakai);

                                $bisa = $j->status === 'tersedia'
                                    && ($sisa === null || $sisa > 0);
                            @endphp

                            <tr>
                                <td>
                                    {{ $j->tanggal->format('d M Y') }}
                                </td>

                                <td>
                                    <span class="jadwal-doctor">
                                        {{ $j->dokter?->name ?? 'Belum ditentukan' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="jadwal-layanan">
                                        @forelse ($j->dokter?->layananDokter ?? [] as $layanan)
                                            <span class="layanan-tag">
                                                {{ $layanan->nama }}
                                            </span>
                                        @empty
                                            <span class="layanan-empty">
                                                Belum ada layanan
                                            </span>
                                        @endforelse
                                    </div>
                                </td>

                                <td>
                                    {{ substr($j->waktu_mulai, 0, 5) }}
                                    -
                                    {{ substr($j->waktu_selesai, 0, 5) }} WIB
                                </td>

                                <td>
                                    {{ $j->kuota ?? 'Tanpa batas' }}
                                </td>

                                <td>
                                    {{ $j->terpakai }}
                                </td>

                                <td>
                                    {{ $sisa ?? '-' }}
                                </td>

                                <td>
                                    @if ($bisa)
                                        <span class="jadwal-badge jadwal-tersedia">
                                            Tersedia
                                        </span>
                                    @elseif ($j->status === 'tersedia')
                                        <span class="jadwal-badge jadwal-penuh">
                                            Penuh
                                        </span>
                                    @else
                                        <span class="jadwal-badge jadwal-nonaktif">
                                            {{ ucfirst($j->status) }}
                                        </span>
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

            <p class="jadwal-info">
                <i class="fa-solid fa-circle-info"></i>
                Halaman ini digunakan Kader untuk memantau jadwal praktik dokter.
                Pengelolaan jadwal dilakukan oleh dokter masing-masing.
            </p>

        @endif

    </section>
@endisset
            </main>
        </div>

    </div>

</body>
</html>
