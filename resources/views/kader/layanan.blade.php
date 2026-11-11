<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentMode === 'create' ? 'Tambah Layanan' : ($currentMode === 'edit' ? 'Edit Layanan' : 'Layanan & Harga') }} — KAWWAN Kader</title>

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
                    <h2>{{ $currentMode === 'create' ? 'Tambah Layanan' : ($currentMode === 'edit' ? 'Edit Layanan' : 'Layanan & Harga') }}</h2>
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
@if ($currentMode === 'create' || $currentMode === 'edit')
    @php
        $isEdit = $currentMode === 'edit';
        $dokterTerpilih = old(
            'dokter_ids',
            $isEdit ? $layanan->dokter->pluck('id')->toArray() : []
        );
    @endphp

    <div class="form-layanan">

        <h1>{{ $isEdit ? 'Edit' : 'Tambah' }} <span>Layanan</span></h1>
        <p>{{ $isEdit ? 'Perbarui informasi, harga, dan dokter yang menangani layanan.' : 'Masukkan informasi, harga, dan dokter yang menangani layanan baru.' }}</p>

        <form action="{{ $isEdit ? route('kader.layanan.update', $layanan) : route('kader.layanan.store') }}" method="POST">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="form-group">
                <label>Nama Layanan *</label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $isEdit ? $layanan->nama : '') }}"
                    placeholder="Contoh: Pemeriksaan Umum"
                    required
                >
                @error('nama')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea
                    name="deskripsi"
                    rows="4"
                    placeholder="Jelaskan layanan yang diberikan"
                >{{ old('deskripsi', $isEdit ? $layanan->deskripsi : '') }}</textarea>
                @error('deskripsi')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Harga Layanan (Rp) *</label>
                <input
                    type="number"
                    name="biaya"
                    min="0"
                    value="{{ old('biaya', $isEdit ? $layanan->biaya : 0) }}"
                    required
                >
                <div class="form-hint">Masukkan 0 jika layanan diberikan secara gratis.</div>
                @error('biaya')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Durasi Pelayanan (menit)</label>
                <input
                    type="number"
                    name="durasi_menit"
                    min="1"
                    value="{{ old('durasi_menit', $isEdit ? $layanan->durasi_menit : '') }}"
                    placeholder="Contoh: 30"
                >
                @error('durasi_menit')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Dokter yang Menangani *</label>

                <div class="dokter-list">
                    @forelse ($dokter as $item)
                        <label class="dokter-option">
                            <input
                                type="checkbox"
                                name="dokter_ids[]"
                                value="{{ $item->id }}"
                                {{ in_array($item->id, $dokterTerpilih) ? 'checked' : '' }}
                            >
                            <span>{{ $item->name }}</span>
                        </label>
                    @empty
                        <p class="form-error-inline">
                            Belum ada akun dokter yang terdaftar.
                        </p>
                    @endforelse
                </div>

                <div class="form-hint">
                    Pilih minimal satu dokter yang menangani layanan ini.
                </div>

                @error('dokter_ids')
                    <div class="form-error">{{ $message }}</div>
                @enderror

                @error('dokter_ids.*')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button
                    type="submit"
                    class="form-btn form-primary"
                    {{ $dokter->isEmpty() ? 'disabled' : '' }}
                >
                    <i class="fa-solid fa-save"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Layanan' }}
                </button>

                <a
                    href="{{ route('kader.layanan.index') }}"
                    class="form-btn form-secondary"
                >
                    Kembali
                </a>
            </div>

        </form>

    </div>
@else
    <div class="layanan-page">

        <div class="layanan-heading">
            <h1>Layanan <span>&amp; Harga</span></h1>
            <p>Kelola jenis pelayanan, tarif, dan dokter yang menangani layanan kesehatan hewan.</p>
        </div>

        @if (session('success'))
            <div class="layanan-alert">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="layanan-alert layanan-alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="layanan-panel">

            <div class="layanan-toolbar">
                <h2>Daftar Layanan</h2>

                <a href="{{ route('kader.layanan.create') }}" class="layanan-btn">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Layanan
                </a>
            </div>

            <div class="layanan-table-wrap">
                <table class="layanan-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Layanan</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Dokter yang Menangani</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($layanan as $item)
                            <tr>
                                <td>{{ $layanan->firstItem() + $loop->index }}</td>

                                <td>
                                    <strong>{{ $item->nama }}</strong>
                                </td>

                                <td>{{ $item->deskripsi ?: '-' }}</td>

                                <td>
                                    @if ($item->biaya == 0)
                                        <span class="layanan-free">Gratis</span>
                                    @else
                                        Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                    @endif
                                </td>

                                <td>
                                    {{ $item->durasi_menit ? $item->durasi_menit . ' menit' : '-' }}
                                </td>

                                <td>
                                    @if ($item->dokter->isNotEmpty())
                                        <div class="dokter-tags">
                                            @foreach ($item->dokter as $dokterItem)
                                                <span class="dokter-tag">
                                                    {{ $dokterItem->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="dokter-kosong">
                                            Belum ditentukan
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($item->aktif)
                                        <span class="layanan-badge layanan-aktif">Aktif</span>
                                    @else
                                        <span class="layanan-badge layanan-nonaktif">Nonaktif</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="layanan-actions">
                                        <a href="{{ route('kader.layanan.edit', $item) }}"
                                           class="layanan-action">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>

                                        <form action="{{ route('kader.layanan.status', $item) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="layanan-action">
                                                {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="layanan-empty">
                                    Belum ada layanan yang ditambahkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kader-pagination-wrap">
                {{ $layanan->links() }}
            </div>

        </div>
    </div>
@endif
            </main>
        </div>

    </div>

</body>
</html>
