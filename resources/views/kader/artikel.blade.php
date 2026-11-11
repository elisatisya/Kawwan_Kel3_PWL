<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentMode === 'create' ? 'Tambah Artikel' : ($currentMode === 'edit' ? 'Edit Artikel' : 'Artikel') }} — KAWWAN Kader</title>

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
                    <h2>{{ $currentMode === 'create' ? 'Tambah Artikel' : ($currentMode === 'edit' ? 'Edit Artikel' : 'Artikel') }}</h2>
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
    @endphp

    <div class="form-layanan">
        <h1>{{ $isEdit ? 'Edit' : 'Tambah' }} <span>Artikel</span></h1>
        <p>{{ $isEdit ? 'Perbarui konten artikel kesehatan hewan.' : 'Tulis artikel edukasi kesehatan hewan baru.' }}</p>

        <form action="{{ $isEdit ? route('kader.artikel.update', $artikel->id) : route('kader.artikel.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="judul">Judul Artikel *</label>
                <input type="text" id="judul" name="judul"
                       value="{{ old('judul', $isEdit ? $artikel->judul : '') }}"
                       placeholder="Masukkan judul artikel" required>
                @error('judul') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori *</label>
                <input type="text" id="kategori" name="kategori"
                       value="{{ old('kategori', $isEdit ? $artikel->kategori : '') }}"
                       placeholder="Contoh: Perawatan Hewan" required>
                @error('kategori') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="ringkasan">Ringkasan</label>
                <textarea id="ringkasan" name="ringkasan" rows="3"
                          placeholder="Ringkasan singkat artikel">{{ old('ringkasan', $isEdit ? $artikel->ringkasan : '') }}</textarea>
                @error('ringkasan') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="isi">Isi Artikel *</label>
                <textarea id="isi" name="isi" rows="6"
                          placeholder="Tuliskan isi artikel lengkap..." required>{{ old('isi', $isEdit ? $artikel->isi : '') }}</textarea>
                @error('isi') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="gambar">Gambar Sampul</label>
                <input type="file" id="gambar" name="gambar" accept=".jpg,.jpeg,.png,.webp">
                @error('gambar') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="form-btn form-primary">
                    <i class="fa-solid fa-save"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Draft' }}
                </button>

                <a href="{{ route('kader.artikel.index') }}" class="form-btn form-secondary">
                    Kembali
                </a>
            </div>
        </form>
    </div>
@else
    <div class="dokter-head">
        <div>
            <h1>Artikel <span>Kesehatan Hewan</span></h1>
            <p>Kelola artikel kesehatan hewan yang kamu buat.</p>
        </div>

        <a href="{{ route('kader.artikel.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Artikel
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="dokter-card">
        <h2>Daftar Artikel Saya</h2>

        @if ($artikel->isEmpty())
            <div class="dokter-empty">
                <i class="fa-solid fa-newspaper"></i>
                <p>Belum ada artikel yang dibuat.</p>
                <a href="{{ route('kader.artikel.create') }}">
                    Mulai buat artikel
                </a>
            </div>
        @else
            <div class="dokter-table-wrap">
                <table class="dokter-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($artikel as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($artikel->currentPage() - 1) * $artikel->perPage() }}</td>

                            <td>{{ $item->judul }}</td>

                            <td>{{ $item->kategori }}</td>

                            <td>
                                <span class="dokter-badge is-{{ $item->status }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>

                            <td>{{ $item->created_at->format('d/m/Y') }}</td>

                            <td>
                                <div class="kader-aksi">
                                    <a href="{{ route('kader.artikel.edit', $item->id) }}"
                                       class="btn btn-outline">
                                        Edit
                                    </a>

                                    @if ($item->status === 'draft')
                                        <form action="{{ route('kader.artikel.ajukan', $item->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary">
                                                Ajukan
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('kader.artikel.destroy', $item->id) }}"
                                          method="POST"
                                          data-confirm="Yakin ingin menghapus artikel ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline">
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

            <div class="mt-3">
                {{ $artikel->links() }}
            </div>
        @endif
    </div>
@endif
            </main>
        </div>

    </div>

</body>
</html>
