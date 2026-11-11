<<<<<<< HEAD
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Profil Saya' }} — KAWWAN Kader</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
=======
@extends('kader.layout')
>>>>>>> origin/main

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
@php
    $editing = $errors->any();
@endphp

@if (session('success'))
    <div class="profil-alert">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

<div class="dokter-head">
    <div>
        <h1>Profil Saya</h1>
        <p>Kelola informasi akun Kader Puskesmas Anda.</p>
    </div>
</div>

<form method="POST"
      action="{{ route('kader.profil.update') }}"
      id="profilForm"
      class="profil-form {{ $editing ? 'is-editing' : '' }}">

    @csrf
    @method('PUT')

    <section class="kader-card">
        <div class="row">
            <div class="profil-avatar-wrap">
                <div class="profil-foto">
                    @if ($user->foto)
                        <img src="{{ asset('storage/'.$user->foto) }}" alt="Foto profil">
                    @else
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
            </div>

            <div>
                <h3>{{ $user->name }}</h3>
                <p class="kader-card-sub">Kader Puskesmas</p>
                <p class="profil-email">{{ $user->email }}</p>
            </div>
        </div>
    </section>

    <section class="kader-card">
        <h3><i class="fa-solid fa-user"></i> Informasi Akun</h3>
        <p class="kader-card-sub">
            Informasi identitas dan kontak akun Anda.
        </p>

        <div class="profil-grid">
            <div class="profil-field">
                <label>Nama Lengkap</label>
                <input type="text" name="name"
                    value="{{ old('name', $user->name) }}"
                    required data-edit @disabled(! $editing)>
                @error('name') <p class="profil-error">{{ $message }}</p> @enderror
            </div>

            <div class="profil-field">
                <label>NIK</label>
                <input type="text" value="{{ $user->nik ?: '-' }}" disabled>
            </div>

            <div class="profil-field">
                <label>Email</label>
                <input type="email" name="email"
                    value="{{ old('email', $user->email) }}"
                    required data-edit @disabled(! $editing)>
                @error('email') <p class="profil-error">{{ $message }}</p> @enderror
            </div>

            <div class="profil-field">
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon"
                    value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                    data-edit @disabled(! $editing)>
                @error('nomor_telepon') <p class="profil-error">{{ $message }}</p> @enderror
            </div>

            <div class="profil-field profil-full">
                <label>Alamat</label>
                <textarea name="alamat" rows="3"
                    data-edit @disabled(! $editing)>{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat') <p class="profil-error">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <section class="kader-card profil-edit-only">
        <h3><i class="fa-solid fa-lock"></i> Keamanan Akun</h3>
        <p class="kader-card-sub">Isi jika ingin mengganti password.</p>

        <div class="profil-grid">
            <div class="profil-field">
                <label>Password Baru</label>
                <input type="password" name="password"
                    placeholder="Minimal 8 karakter"
                    autocomplete="new-password"
                    data-edit @disabled(! $editing)>
                @error('password') <p class="profil-error">{{ $message }}</p> @enderror
            </div>

            <div class="profil-field">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                    data-edit @disabled(! $editing)>
            </div>
        </div>
    </section>

    <div class="profil-actions">
        <button type="button" id="profilEditBtn"
            class="btn btn-outline profil-view-only">
            <i class="fa-solid fa-pen"></i> Edit Profil
        </button>

        <div class="profil-edit-only">
            <a href="{{ route('kader.profil') }}" class="btn btn-outline">
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </div>

</form>
            </main>
        </div>

<<<<<<< HEAD
    </div>

</body>
</html>
=======
@endsection

>>>>>>> origin/main
