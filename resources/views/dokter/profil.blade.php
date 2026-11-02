@extends('dokter.layout')

@section('title', 'Profil Dokter')

@section('content')

    @php $editing = $errors->any(); @endphp

    @if (session('success'))
        <div class="profil-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form id="profilForm" method="POST" action="{{ route('dokter.profil.update') }}" enctype="multipart/form-data" class="profil-form {{ $editing ? 'is-editing' : '' }}">
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
                </div>

                <h2>{{ trim(($user->gelar_depan ? $user->gelar_depan.' ' : '').$user->name.($user->gelar_belakang ? ', '.$user->gelar_belakang : '')) }}</h2>
                <span class="profil-role"><i class="fa-solid fa-user-doctor"></i> Dokter Hewan</span>
                <p class="profil-email">{{ $user->email }}</p>

                <button type="button" id="profilEditBtn" class="btn btn-outline profil-view-only">
                    <i class="fa-solid fa-pen"></i> Edit Profil
                </button>

                <p class="profil-hint profil-edit-only">Klik ikon kamera untuk mengganti foto. Format JPG, PNG, atau WEBP, maksimal 2 MB.</p>
                @error('foto')<p class="profil-error">{{ $message }}</p>@enderror
            </aside>

            <div class="profil-main">
                <section class="dokter-card">
                    <h3><i class="fa-regular fa-id-card"></i> Informasi Pribadi</h3>
                    <p class="dokter-card-sub">Data akun yang tampil di sistem KAWWAN.</p>

                    <div class="profil-grid">
                        <div class="profil-field">
                            <label for="gelar_depan">Gelar Depan</label>
                            <input type="text" id="gelar_depan" name="gelar_depan" value="{{ old('gelar_depan', $user->gelar_depan) }}" placeholder="Contoh: drh." data-edit @disabled(! $editing)>
                            @error('gelar_depan')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="profil-field">
                            <label for="gelar_belakang">Gelar Belakang</label>
                            <input type="text" id="gelar_belakang" name="gelar_belakang" value="{{ old('gelar_belakang', $user->gelar_belakang) }}" placeholder="Contoh: M.Si." data-edit @disabled(! $editing)>
                            @error('gelar_belakang')<p class="profil-error">{{ $message }}</p>@enderror
                        </div>
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
                    <a href="{{ route('dokter.profil') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>

        </div>
    </form>

@endsection