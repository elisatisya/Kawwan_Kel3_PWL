@php
    $edit = $jadwal->exists;
    $judul = $edit ? 'Ubah Jadwal' : 'Tambah Jadwal';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }} — KAWWAN Dokter</title>

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
                    <h2>{{ $judul }}</h2>
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

                <a href="{{ route('dokter.jadwal.index') }}" class="riwayat-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke jadwal
                </a>

                {{-- Kartu 1: form --}}
                <section class="dokter-card">
                    <h3>{{ $edit ? 'Ubah Jadwal Praktik' : 'Tambah Jadwal Praktik' }}</h3>
                    <p class="dokter-card-sub">Tentukan tanggal, waktu, dan kuota pasien.</p>

                    @if (session('peringatan'))
                        <div class="dokter-alert is-warning"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('peringatan') }}</div>
                    @endif

                    <form action="{{ $edit ? route('dokter.jadwal.update', $jadwal) : route('dokter.jadwal.store') }}" method="POST">
                        @csrf
                        @if ($edit) @method('PUT') @endif

                        <input type="hidden" name="konfirmasi" value="{{ session('butuh_konfirmasi') }}">

                        <div class="form-group">
                            <label for="tanggal">Tanggal *</label>
                            <input type="date" id="tanggal" name="tanggal"
                                   min="{{ now()->toDateString() }}"
                                   value="{{ old('tanggal', $jadwal->tanggal?->toDateString()) }}" required>
                            @error('tanggal') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="dokter-form-row">
                            <div class="form-group">
                                <label for="waktu_mulai">Waktu Mulai *</label>
                                <input type="time" id="waktu_mulai" name="waktu_mulai"
                                       value="{{ old('waktu_mulai', $jadwal->waktu_mulai ? substr($jadwal->waktu_mulai, 0, 5) : '') }}" required>
                                @error('waktu_mulai') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="waktu_selesai">Waktu Selesai *</label>
                                <input type="time" id="waktu_selesai" name="waktu_selesai"
                                       value="{{ old('waktu_selesai', $jadwal->waktu_selesai ? substr($jadwal->waktu_selesai, 0, 5) : '') }}" required>
                                @error('waktu_selesai') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="dokter-form-row">
                            <div class="form-group">
                                <label for="kuota">Kuota Pasien (maks. 100) *</label>
                                <input type="number" id="kuota" name="kuota" min="1" max="100"
                                       placeholder="Masukkan jumlah maksimal pasien"
                                       value="{{ old('kuota', $jadwal->kuota) }}" required>
                                @error('kuota') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="status">Status *</label>
                                <select id="status" name="status" required>
                                    <option value="tersedia" @selected(old('status', $jadwal->status ?: 'tersedia') === 'tersedia')>Tersedia</option>
                                    <option value="ditutup" @selected(old('status', $jadwal->status) === 'ditutup')>Ditutup</option>
                                </select>
                                @error('status') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="dokter-actions">
                            <a href="{{ route('dokter.jadwal.index') }}" class="btn btn-outline">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </section>

                {{-- Kartu 2: jadwal dokter lain --}}
                <section class="dokter-card">
                    <h3>Jadwal Dokter Lain</h3>
                    <p class="dokter-card-sub">Jadwal praktik dokter lain 14 hari ke depan, sebagai acuan supaya jadwal Anda tidak bentrok.</p>

                    @if ($jadwalLain->isEmpty())
                        <p class="dokter-empty">Belum ada jadwal dokter lain pada 14 hari ke depan.</p>
                    @else
                        <div class="dokter-table-wrap">
                            <table class="dokter-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Dokter</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalLain as $j)
                                        <tr>
                                            <td>{{ $j->tanggal->locale('id')->translatedFormat('D, d M Y') }}</td>
                                            <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                            <td>{{ trim(($j->dokter?->gelar_depan ?? '').' '.($j->dokter?->name ?? '-').' '.($j->dokter?->gelar_belakang ?? '')) }}</td>
                                            <td><span class="dokter-badge is-{{ $j->status }}">{{ ucfirst($j->status) }}</span></td>
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