<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Data Hewan' }} — KAWWAN Pemilik Hewan</title>

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
                    <h2>{{ 'Data Hewan' }}</h2>
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
            <h1>Data <span>Hewan Peliharaan</span></h1>
            <p>Tambahkan dan kelola identitas hewan peliharaan Anda untuk keperluan pendaftaran pelayanan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="pemilik-alert is-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="pemilik-alert is-error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="pemilik-split-layout">

        {{-- Form Tambah / Edit Hewan --}}
        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>{{ $hewanEdit ? 'Edit Data Hewan' : 'Tambah Hewan Baru' }}</h2>
                    <p>{{ $hewanEdit ? 'Perbarui informasi hewan peliharaan yang dipilih.' : 'Lengkapi data dasar hewan peliharaan Anda.' }}</p>
                </div>
            </div>

            <form method="POST"
                  action="{{ $hewanEdit ? route('pemilik.hewan.update', $hewanEdit) : route('pemilik.hewan.store') }}"
                  class="pemilik-simple-form">
                @csrf
                @if ($hewanEdit)
                    @method('PUT')
                @endif

                <div class="pemilik-form-group">
                    <label for="jenis">Jenis Hewan *</label>
                    <select id="jenis" name="jenis" required>
                        @php
                            $jenisVal = old('jenis', $hewanEdit?->jenis ?? '');
                        @endphp
                        <option value="">Pilih jenis hewan</option>
                        <option value="Kucing" @selected(strtolower($jenisVal) === 'kucing')>Kucing</option>
                        <option value="Anjing" @selected(strtolower($jenisVal) === 'anjing')>Anjing</option>
                        <option value="Kelinci" @selected(strtolower($jenisVal) === 'kelinci')>Kelinci</option>
                        <option value="Burung" @selected(strtolower($jenisVal) === 'burung')>Burung</option>
                        <option value="Lainnya" @selected(strtolower($jenisVal) === 'lainnya')>Lainnya</option>
                    </select>
                    @error('jenis')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="ras">Ras (Opsional)</label>
                    <input type="text"
                           id="ras"
                           name="ras"
                           value="{{ old('ras', $hewanEdit?->ras ?? '') }}"
                           placeholder="Contoh: Persia, Domestik, Golden Retriever">
                    @error('ras')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="jenis_kelamin">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        @php
                            $kelaminVal = old('jenis_kelamin', $hewanEdit?->jenis_kelamin ?? '');
                        @endphp
                        <option value="">Pilih jenis kelamin</option>
                        <option value="jantan" @selected($kelaminVal === 'jantan')>Jantan</option>
                        <option value="betina" @selected($kelaminVal === 'betina')>Betina</option>
                    </select>
                    @error('jenis_kelamin')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="tanggal_lahir">Tanggal Lahir (Opsional)</label>
                    <input type="date"
                           id="tanggal_lahir"
                           name="tanggal_lahir"
                           max="{{ now()->toDateString() }}"
                           value="{{ old('tanggal_lahir', $hewanEdit?->tanggal_lahir?->format('Y-m-d') ?? '') }}">
                    @error('tanggal_lahir')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-actions">
                    <button type="submit" class="pemilik-btn pemilik-btn-primary">
                        {{ $hewanEdit ? 'Simpan Perubahan' : 'Simpan Hewan' }}
                    </button>
                    @if ($hewanEdit)
                        <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-outline">
                            Batal
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Tabel Daftar Hewan --}}
        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>Daftar Hewan Saya</h2>
                    <p>Total: {{ $daftarHewan->count() }} hewan terdaftar</p>
                </div>
            </div>

            @if ($daftarHewan->isEmpty())
                <div class="pemilik-empty">
                    <h3>Belum Ada Data Hewan</h3>
                    <p>Gunakan formulir di samping untuk menambahkan hewan peliharaan pertama Anda.</p>
                </div>
            @else
                <div class="pemilik-table-wrap">
                    <table class="pemilik-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Hewan</th>
                                <th>Ras</th>
                                <th>Kelamin</th>
                                <th>Umur</th>
                                <th>Riwayat Booking</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftarHewan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ ucfirst($item->jenis) }}</strong></td>
                                    <td>{{ $item->ras ?: '-' }}</td>
                                    <td>{{ ucfirst($item->jenis_kelamin) }}</td>
                                    <td>{{ $item->umur ?: '-' }}</td>
                                    <td>{{ $item->pemesanan_count }} kali</td>
                                    <td>
                                        <div class="pemilik-table-actions">
                                            <a href="{{ route('pemilik.hewan.edit', $item) }}" class="pemilik-action-link">
                                                Edit
                                            </a>
                                            <form method="POST"
                                                  action="{{ route('pemilik.hewan.destroy', $item) }}"
                                                  data-confirm="Yakin ingin menghapus data hewan {{ $item->jenis }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="pemilik-action-link is-danger">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

    </div>

</div>
            </main>
        </div>
    </div>

</body>
</html>
