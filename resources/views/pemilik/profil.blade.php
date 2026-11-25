<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Profil Saya' }} — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        @include('pemilik.partials.navbar')

        <div class="pemilik-main-wrap">
            <main class="pemilik-main">
<div class="pemilik-container">

    <div class="pemilik-page-head">
        <div>
            <h1>Profil <span>Saya</span></h1>
            <p>Kelola informasi pribadi dan keamanan akun Pemilik Hewan Anda.</p>
        </div>
    </div>

    @php $editing = $errors->any(); @endphp

    @if (session('success'))
        <div class="profil-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form id="profilForm" method="POST" action="{{ route('pemilik.profil.update') }}" enctype="multipart/form-data" class="profil-form {{ $editing ? 'is-editing' : '' }}">
        @csrf
        @method('PUT')

        <div class="profil-layout">

            <aside class="pemilik-card profil-side">
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

                <h2>{{ $user->name }}</h2>
                <span class="profil-role"><i class="fa-solid fa-paw"></i> Pemilik Hewan</span>
                <p class="profil-email">{{ $user->email }}</p>

                <button type="button" id="profilEditBtn" class="btn btn-outline profil-view-only">
                    <i class="fa-solid fa-pen"></i> Edit Profil
                </button>

                <p class="profil-hint profil-edit-only">Klik ikon kamera untuk mengganti foto. Format JPG, PNG, atau WEBP, maksimal 2 MB.</p>
                @error('foto')<p class="profil-error">{{ $message }}</p>@enderror
            </aside>

            <div class="profil-main">
                <section class="pemilik-card">
                    <div class="pemilik-card-header">
                        <div>
                            <h2>Informasi Pribadi</h2>
                            <p>Data akun yang tampil di sistem KAWWAN.</p>
                        </div>
                    </div>

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

                <section class="pemilik-card profil-edit-only">
                    <div class="pemilik-card-header">
                        <div>
                            <h2>Ubah Password</h2>
                            <p>Kosongkan jika tidak ingin mengganti password.</p>
                        </div>
                    </div>

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
                    <a href="{{ route('pemilik.profil') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>

        </div>
    </form>

</div>
            </main>
        </div>
    </div>

</body>
</html>