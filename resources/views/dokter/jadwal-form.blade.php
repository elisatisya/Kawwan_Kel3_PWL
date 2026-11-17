@php $edit = $jadwal->exists; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $edit ? 'Ubah Jadwal' : 'Tambah Jadwal' }} — KAWWAN Dokter</title>

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
                    <h2>{{ $edit ? 'Ubah Jadwal' : 'Tambah Jadwal' }}</h2>
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

                <a href="{{ route('dokter.jadwal.index') }}" class="riwayat-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke jadwal
                </a>

                <section class="dokter-card">
                    <h3>{{ $edit ? 'Ubah Jadwal Pelayanan' : 'Tambah Jadwal Pelayanan' }}</h3>
                    <p class="dokter-card-sub">Tentukan tanggal, waktu, dan kuota pasien.</p>

                    <form action="{{ $edit ? route('dokter.jadwal.update', $jadwal) : route('dokter.jadwal.store') }}" method="POST">
                        @csrf
                        @if ($edit) @method('PUT') @endif

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

            </main>
        </div>
    </div>

</body>
</html>