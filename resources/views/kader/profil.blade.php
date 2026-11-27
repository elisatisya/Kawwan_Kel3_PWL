<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Profil Saya' }} — KAWWAN Kader</title>

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
                    <h2>{{ 'Profil Saya' }}</h2>
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
@php $editing = $errors->any(); @endphp

    @if (session('success'))
        <div class="profil-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form id="profilForm" method="POST" action="{{ route('kader.profil.update') }}" enctype="multipart/form-data" class="profil-form {{ $editing ? 'is-editing' : '' }}">
        @csrf
        @method('PUT')

        <div class="profil-layout">

            <aside class="dokter-card profil-side">
                <div class="profil-avatar-wrap">
                    <div class="profil-foto">
                        <img id="profilFotoPreview" src="{{ $user->foto ? asset('storage/'.$user->foto) : '' }}" alt="Foto profil" @if (! $user->foto) hidden @endif>
                        <span id="profilFotoInisial" @if ($user->foto) hidden @endif>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <label for="profilFoto" class="profil-photo-btn profil-edit-only" title="Ubah foto">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="profilFoto" name="foto" accept=".jpg,.jpeg,.png,.webp" hidden data-edit>
                    <input type="hidden" name="hapus_foto" id="hapusFoto" value="0">
                </div>

                <h2>{{ $user->name }}</h2>
                <span class="profil-role"><i class="fa-solid fa-user-nurse"></i> Kader Puskesmas</span>
                <p class="profil-email">{{ $user->email }}</p>

                <button type="button" id="profilEditBtn" class="btn btn-outline profil-view-only">
                    <i class="fa-solid fa-pen"></i> Edit Profil
                </button>

                @if ($user->foto)
                    <button type="button" id="profilHapusFotoBtn" class="btn btn-outline profil-edit-only" style="margin-top: 8px;">
                        <i class="fa-solid fa-trash"></i> Hapus Foto
                    </button>
                @endif

                <p class="profil-hint profil-edit-only">Klik ikon kamera untuk mengganti foto. Format JPG, PNG, atau WEBP, maksimal 2 MB.</p>
                @error('foto')<p class="profil-error">{{ $message }}</p>@enderror
            </aside>

            <div class="profil-main">
                <section class="dokter-card">
                    <h3><i class="fa-regular fa-id-card"></i> Informasi Pribadi</h3>
                    <p class="dokter-card-sub">Data akun yang tampil di sistem KAWWAN.</p>

                    <div class="profil-grid">
                        <div class="profil-field">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama Lengkap" required data-edit @disabled(! $editing)>
                            @error('name')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="profil-field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="nama@gmail.com" required data-edit @disabled(! $editing)>
                            @error('email')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="profil-field">
                            <label for="nik">NIK</label>
                            <input type="text" id="nik" value="{{ $user->nik ?: '-' }}" disabled data-locked>
                        </div>
                        <div class="profil-field">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" placeholder="081234567890" data-edit @disabled(! $editing)>
                            @error('nomor_telepon')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="profil-field profil-full">
                            <label for="alamat">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan Alamat Lengkap" data-edit @disabled(! $editing)>{{ old('alamat', $user->alamat) }}</textarea>
                            @error('alamat')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="dokter-card profil-edit-only">
                    <h3><i class="fa-solid fa-lock"></i> Ubah Password</h3>
                    <p class="dokter-card-sub">Kosongkan jika tidak ingin mengganti password.</p>

                    <div class="profil-grid">
                        <div class="profil-field">
                            <label for="password">Password Baru</label>
                            <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" data-edit @disabled(! $editing)>
                            @error('password')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="profil-field">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password" data-edit @disabled(! $editing)>
                        </div>
                    </div>
                </section>

                <div class="profil-actions profil-edit-only">
                    <a href="{{ route('kader.profil') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>

        </div>
    </form>

    {{-- Hapus foto: foto baru benar-benar dihapus setelah klik "Simpan Perubahan" --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tombol = document.getElementById('profilHapusFotoBtn');
            if (!tombol) return;

            const penanda = document.getElementById('hapusFoto');
            const input = document.getElementById('profilFoto');
            const preview = document.getElementById('profilFotoPreview');
            const inisial = document.getElementById('profilFotoInisial');

            tombol.addEventListener('click', () => {
                penanda.value = '1';
                input.value = '';
                preview.hidden = true;
                inisial.hidden = false;
                tombol.style.display = 'none';
            });

            // Kalau lalu memilih foto baru, batalkan penghapusan
            input.addEventListener('change', () => {
                if (input.files[0]) {
                    penanda.value = '0';
                    tombol.style.display = '';
                }
            });
        });
    </script>
            </main>
        </div>

    </div>

</body>
</html>