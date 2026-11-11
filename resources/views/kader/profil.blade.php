@extends('kader.layout')

@section('title', 'Profil Saya')

@section('content')

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

@endsection

