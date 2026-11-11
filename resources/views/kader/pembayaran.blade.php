<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($metode) ? 'Catat Pembayaran' : 'Verifikasi Pembayaran' }} — KAWWAN Kader</title>

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
                    <h2>{{ isset($metode) ? 'Catat Pembayaran' : 'Verifikasi Pembayaran' }}</h2>
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
@isset($metode)
    <div class="dokter-head">
        <h1>Catat <span>Pembayaran</span></h1>
        <p>Isi pembayaran untuk pemesanan berikut.</p>
    </div>

    @if (session('success'))
        <div class="dokter-alert is-success"><p>{{ session('success') }}</p></div>
    @endif

    <section class="dokter-card">
        <h3>Detail Pemesanan</h3>
        <p class="dokter-card-sub">
            {{ $pemesanan->user?->name ?? '-' }} · {{ ucfirst($pemesanan->hewan?->jenis ?? '-') }}{{ $pemesanan->hewan?->ras ? ' (' . $pemesanan->hewan->ras . ')' : '' }}<br>
            {{ $pemesanan->jadwalPelayanan->tanggal->format('d M Y') }},
            {{ substr($pemesanan->jadwalPelayanan->waktu_mulai, 0, 5) }} - {{ substr($pemesanan->jadwalPelayanan->waktu_selesai, 0, 5) }} WIB
        </p>

        <form method="POST" action="{{ route('kader.pembayaran.store', $pemesanan) }}" class="kader-form">
            @csrf

            <div class="form-row-2">
                <div>
                    <label class="form-label" for="jumlah">Jumlah (Rp)</label>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="0" step="1" inputmode="numeric" class="form-control" required>
                    @error('jumlah')<p class="kader-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label" for="metode">Metode</label>
                    <select id="metode" name="metode" class="form-control" required>
                        @foreach ($metode as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('metode', 'tunai') === $kode)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('metode')<p class="kader-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="catatan">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="3" maxlength="500" class="form-control">{{ old('catatan') }}</textarea>
                @error('catatan')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div class="kader-aksi">
                <button type="submit" class="btn btn-primary">Simpan pembayaran</button>
                <a href="{{ route('kader.pemesanan.index') }}" class="btn btn-outline">Nanti saja</a>
            </div>
        </form>
    </section>
@else
    <div class="payment-page">

        <div class="payment-heading">
            <h1>Verifikasi <span>Pembayaran</span></h1>
            <p>Periksa bukti pembayaran sebelum memberikan konfirmasi.</p>
        </div>

        @if ($errors->any())
            <div class="payment-alert">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- INFORMASI BOOKING --}}
        <section class="payment-panel">
            <h2 class="payment-title">Informasi Pemesanan</h2>

            <div class="payment-info">
                <div class="payment-info-item">
                    <small>Nama Pemilik</small>
                    <strong>{{ $pemesanan->user?->name ?? '-' }}</strong>
                </div>

                <div class="payment-info-item">
                    <small>Jenis Hewan</small>
                    <strong>{{ ucfirst($pemesanan->hewan?->jenis ?? '-') }}{{ $pemesanan->hewan?->ras ? ' (' . $pemesanan->hewan->ras . ')' : '' }}</strong>
                </div>

                <div class="payment-info-item">
                    <small>Tanggal Pelayanan</small>
                    <strong>
                        {{ $pemesanan->jadwalPelayanan?->tanggal?->format('d M Y') ?? '-' }}
                    </strong>
                </div>

                <div class="payment-info-item">
                    <small>Status Booking</small>
                    <strong>{{ ucfirst(str_replace('_', ' ', $pemesanan->status)) }}</strong>
                </div>
            </div>
        </section>

        {{-- INFORMASI PEMBAYARAN --}}
        <section class="payment-panel">
            <h2 class="payment-title">Informasi Pembayaran</h2>

            <div class="payment-info">
                <div class="payment-info-item">
                    <small>Jumlah Pembayaran</small>
                    <strong>
                        Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}
                    </strong>
                </div>

                <div class="payment-info-item">
                    <small>Metode Pembayaran</small>
                    <strong>{{ strtoupper($pembayaran->metode) }}</strong>
                </div>

                <div class="payment-info-item">
                    <small>Status Pembayaran</small>
                    <strong>
                        @if ($pembayaran->status === 'menunggu_verifikasi')
                            <span class="payment-status status-waiting">Menunggu Verifikasi</span>
                        @elseif ($pembayaran->status === 'lunas')
                            <span class="payment-status status-paid">Lunas</span>
                        @else
                            <span class="payment-status status-rejected">Ditolak</span>
                        @endif
                    </strong>
                </div>

                <div class="payment-info-item">
                    <small>Waktu Verifikasi</small>
                    <strong>
                        {{ $pembayaran->dibayar_at?->format('d M Y H:i') ?? '-' }}
                    </strong>
                </div>
            </div>
        </section>

        {{-- BUKTI PEMBAYARAN --}}
        <section class="payment-panel">
            <h2 class="payment-title">Foto Bukti Pembayaran</h2>

            @if ($pembayaran->bukti_pembayaran)
                <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                   target="_blank"
                   rel="noopener noreferrer">
                    <img
                        src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}"
                        alt="Bukti pembayaran"
                        class="payment-proof"
                    >
                </a>
                <p class="payment-proof-caption">
                    Klik foto untuk melihat ukuran penuh.
                </p>
            @else
                <div class="payment-empty">
                    <i class="fa-solid fa-image"></i>
                    <p>Bukti pembayaran belum tersedia.</p>
                </div>
            @endif
        </section>

        {{-- AKSI VERIFIKASI --}}
        @if ($pembayaran->status === 'menunggu_verifikasi')

            <section class="payment-panel">
                <h2 class="payment-title">Tindakan Kader</h2>

                <div class="payment-actions">

                    <form method="POST"
                          action="{{ route('kader.pembayaran.verifikasi', $pemesanan) }}"
                          data-confirm="Apakah bukti pembayaran ini valid dan ingin diverifikasi?">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="payment-btn payment-btn-success">
                            <i class="fa-solid fa-check"></i>
                            Verifikasi Pembayaran
                        </button>
                    </form>

                </div>

                <form method="POST"
                      action="{{ route('kader.pembayaran.tolak', $pemesanan) }}"
                      class="payment-reject-form"
                      data-confirm="Yakin ingin menolak bukti pembayaran ini?">
                    @csrf
                    @method('PATCH')

                    <label for="catatan" class="payment-reject-label">
                        Alasan Penolakan
                    </label>

                    <textarea
                        name="catatan"
                        id="catatan"
                        class="payment-input"
                        placeholder="Tuliskan alasan jika bukti pembayaran tidak valid..."
                        required
                        maxlength="500"
                    >{{ old('catatan') }}</textarea>

                    <div class="payment-actions">
                        <button type="submit"
                                class="payment-btn payment-btn-danger">
                            <i class="fa-solid fa-xmark"></i>
                            Tolak Pembayaran
                        </button>
                    </div>
                </form>
            </section>

        @elseif ($pembayaran->status === 'ditolak')

            <section class="payment-panel">
                <h2 class="payment-title">Alasan Penolakan</h2>
                <p>{{ $pembayaran->catatan ?? '-' }}</p>
            </section>

        @endif

        <div>
            <a href="{{ route('kader.pemesanan.index') }}"
               class="payment-btn payment-btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar Booking
            </a>
        </div>

    </div>
@endisset
            </main>
        </div>

    </div>

</body>
</html>
