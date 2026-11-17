@php
    $p = $antrean->pemesanan;
    $hewan = $p->hewan;
    $hasil = $p->pemeriksaan;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemeriksaan — KAWWAN Dokter</title>

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
                    <h2>Form Pemeriksaan</h2>
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

                @if (session('success'))
                    <div class="dokter-alert is-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="dokter-alert is-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
                @endif

                <a href="{{ route('dokter.pemeriksaan.index') }}" class="riwayat-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke antrean
                </a>

                {{-- Identitas pasien --}}
                <section class="dokter-card">
                    <h3>Antrean {{ $antrean->nomor_antrean }} · {{ ucfirst($hewan?->jenis ?? '-') }}{{ $hewan?->ras ? ' ('.$hewan->ras.')' : '' }}</h3>
                    <p class="dokter-card-sub">Identitas pasien hewan dan layanan yang dipesan.</p>

                    <div class="riwayat-info">
                        <div><span>Jenis Hewan</span><strong>{{ ucfirst($hewan?->jenis ?? '-') }}</strong></div>
                        <div><span>Ras</span><strong>{{ $hewan?->ras ?: '-' }}</strong></div>
                        <div><span>Jenis Kelamin</span><strong>{{ ucfirst($hewan?->jenis_kelamin ?? '-') }}</strong></div>
                        <div><span>Umur</span><strong>{{ $hewan?->umur ?? '-' }}</strong></div>
                        <div><span>Pemilik</span><strong>{{ $p->user?->name ?? '-' }}</strong></div>
                        <div><span>Layanan</span><strong>{{ $p->pemesananLayanan->map(fn ($l) => $l->layanan?->nama)->filter()->join(', ') ?: '-' }}</strong></div>
                        <div><span>Status</span>
                            <strong><span class="dokter-badge is-{{ $antrean->status }}">{{ \Illuminate\Support\Str::headline($antrean->status) }}</span></strong>
                        </div>
                    </div>
                </section>

                {{-- Kondisi 1: belum dimulai --}}
                @if ($antrean->status === 'menunggu')
                    <section class="dokter-card">
                        <h3>Mulai Pemeriksaan</h3>

                        @if ($p->status === 'dikonfirmasi')
                            <p class="dokter-card-sub">Kehadiran pasien sudah dikonfirmasi Kader. Klik tombol di bawah untuk memulai.</p>
                            <form action="{{ route('dokter.pemeriksaan.mulai', $antrean) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-primary">Mulai Pemeriksaan</button>
                            </form>
                        @else
                            <p class="dokter-empty">Menunggu Kader mengonfirmasi kehadiran pasien.</p>
                        @endif
                    </section>

                {{-- Kondisi 2: sedang diperiksa -> form --}}
                @elseif ($antrean->status === 'sedang_dilayani')
                    <section class="dokter-card">
                        <h3>Hasil Pemeriksaan</h3>
                        <p class="dokter-card-sub">Isi data pemeriksaan, lalu simpan untuk menyelesaikan antrean.</p>

                        <form action="{{ route('dokter.pemeriksaan.simpan', $antrean) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="keluhan">Keluhan *</label>
                                <textarea id="keluhan" name="keluhan" rows="3"
                                          placeholder="Masukkan keluhan yang disampaikan pemilik">{{ old('keluhan', $hasil?->keluhan) }}</textarea>
                                @error('keluhan') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="hasil_pemeriksaan">Hasil Pemeriksaan *</label>
                                <textarea id="hasil_pemeriksaan" name="hasil_pemeriksaan" rows="4"
                                          placeholder="Masukkan diagnosis / temuan pemeriksaan">{{ old('hasil_pemeriksaan', $hasil?->hasil_pemeriksaan) }}</textarea>
                                @error('hasil_pemeriksaan') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="tindakan">Tindakan *</label>
                                <textarea id="tindakan" name="tindakan" rows="3"
                                          placeholder="Masukkan tindakan atau obat yang diberikan">{{ old('tindakan', $hasil?->tindakan) }}</textarea>
                                @error('tindakan') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="catatan">Catatan Tambahan</label>
                                <textarea id="catatan" name="catatan" rows="2"
                                          placeholder="Opsional">{{ old('catatan', $hasil?->catatan) }}</textarea>
                                @error('catatan') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="dokter-actions">
                                <a href="{{ route('dokter.pemeriksaan.index') }}" class="btn btn-outline">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan &amp; Selesai</button>
                            </div>
                        </form>
                    </section>

                {{-- Kondisi 3: selesai -> tampil saja --}}
                @else
                    <section class="dokter-card">
                        <h3>Hasil Pemeriksaan</h3>
                        <p class="dokter-card-sub">Pemeriksaan ini sudah selesai.</p>

                        <div class="riwayat-info">
                            <div><span>Keluhan</span><strong>{{ $hasil?->keluhan ?: '-' }}</strong></div>
                            <div><span>Hasil Pemeriksaan</span><strong>{{ $hasil?->hasil_pemeriksaan ?: '-' }}</strong></div>
                            <div><span>Tindakan</span><strong>{{ $hasil?->tindakan ?: '-' }}</strong></div>
                            <div><span>Catatan</span><strong>{{ $hasil?->catatan ?: '-' }}</strong></div>
                        </div>
                    </section>
                @endif

            </main>
        </div>
    </div>

</body>
</html>