<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($pemilik) ? 'Daftarkan Booking' : 'Booking & Pembayaran' }} — KAWWAN Kader</title>

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
                    <h2>{{ isset($pemilik) ? 'Daftarkan Booking' : 'Booking & Pembayaran' }}</h2>
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
@isset($pemilik)
    <div class="dokter-head">
        <h1>Daftarkan <span>Booking</span></h1>
        <p>Pilih pemilik, hewan, dan jadwal pelayanan. Kuota dicek otomatis saat disimpan.</p>
    </div>

    <section class="dokter-card">
        <form method="POST" action="{{ route('kader.pemesanan.store') }}" class="kader-form">
            @csrf

            <div>
                <label class="form-label" for="kaderPemilik">Pemilik hewan</label>
                <select id="kaderPemilik" name="user_id" class="form-control" data-hewan='@json($hewanPerPemilik)' required>
                    <option value="">Pilih pemilik</option>
                    @foreach ($pemilik as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                            {{ $u->name }}{{ $u->nomor_telepon ? ' — '.$u->nomor_telepon : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="kaderHewan">Hewan</label>
                <select id="kaderHewan" name="hewan_id" class="form-control" data-selected="{{ old('hewan_id') }}" required disabled>
                    <option value="">Pilih pemilik dulu</option>
                </select>
                @error('hewan_id')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="kaderJadwal">Jadwal pelayanan</label>
                <select id="kaderJadwal" name="jadwal_pelayanan_id" class="form-control" required>
                    <option value="">Pilih jadwal</option>
                    @foreach ($jadwal as $j)
                        @php $sisa = $j->kuota === null ? 'tanpa batas' : ($j->kuota - $j->terpakai); @endphp
                        <option value="{{ $j->id }}" @selected((old('jadwal_pelayanan_id') ?? $jadwalDipilih) == $j->id)>
                            {{ $j->tanggal->format('d M Y') }} · {{ substr($j->waktu_mulai, 0, 5) }}-{{ substr($j->waktu_selesai, 0, 5) }} WIB · sisa kuota {{ $sisa }}
                        </option>
                    @endforeach
                </select>
                @error('jadwal_pelayanan_id')<p class="kader-error">{{ $message }}</p>@enderror
                @if ($jadwal->isEmpty())
                    <p class="kader-error">Belum ada jadwal yang tersedia. Jadwal dibuat oleh admin.</p>
                @endif
            </div>

            <div class="kader-aksi">
                <button type="submit" class="btn btn-primary">Simpan &amp; lanjut ke pembayaran</button>
                <a href="{{ route('kader.pemesanan.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </section>
@else
    <div class="booking-page">

        {{-- HEADER --}}
        <div class="booking-heading">
            <h1>Booking &amp; <span>Pembayaran</span></h1>
            <p>Kelola pemesanan, konfirmasi kedatangan, dan pembayaran pelayanan hewan.</p>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="booking-alert booking-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="booking-alert booking-alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <section class="booking-panel">

            {{-- PENCARIAN DAN FILTER --}}
            <form method="GET"
                  action="{{ route('kader.pemesanan.index') }}"
                  class="booking-toolbar">

                <div class="booking-search">
                    <label for="filterCari">Cari pemesanan</label>
                    <input
                        type="text"
                        id="filterCari"
                        name="cari"
                        value="{{ $cari ?? '' }}"
                        placeholder="Cari berdasarkan nama pemilik atau jenis hewan..."
                    >
                </div>

                <div class="booking-filter-row">

                    <div class="booking-filter-item">
                        <label for="filterTanggal">Tanggal pelayanan</label>
                        <input
                            type="date"
                            id="filterTanggal"
                            name="tanggal"
                            value="{{ $tanggal ?? '' }}"
                        >
                    </div>

                    <div class="booking-filter-item">
                        <label for="filterStatus">Status pemesanan</label>
                        <select id="filterStatus" name="status">
                            <option value="">Semua status</option>

                            @foreach ($labelStatus as $kode => $label)
                                <option value="{{ $kode }}"
                                    @selected(($status ?? '') === $kode)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="booking-filter-item">
                        <label for="filterPembayaran">Status pembayaran</label>
                        <select id="filterPembayaran" name="status_pembayaran">
                            <option value="">Semua status</option>
                            <option value="belum_dibayar"
                                @selected(($statusPembayaran ?? '') === 'belum_dibayar')>
                                Belum Dibayar
                            </option>
                            <option value="menunggu_verifikasi"
                                @selected(($statusPembayaran ?? '') === 'menunggu_verifikasi')>
                                Menunggu Verifikasi
                            </option>
                            <option value="lunas"
                                @selected(($statusPembayaran ?? '') === 'lunas')>
                                Lunas
                            </option>
                            <option value="ditolak"
                                @selected(($statusPembayaran ?? '') === 'ditolak')>
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="booking-buttons">
                        <button type="submit"
                                class="booking-btn booking-btn-primary">
                            <i class="fa-solid fa-filter"></i>
                            Terapkan
                        </button>

                        <a href="{{ route('kader.pemesanan.index') }}"
                           class="booking-btn booking-btn-secondary">
                            <i class="fa-solid fa-rotate-left"></i>
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            {{-- JUDUL DAFTAR --}}
            <div class="booking-section-title">
                <h2>Daftar Booking</h2>

                <div class="booking-section-meta">
                    <span class="booking-count">
                        {{ $pemesanan->total() }} pemesanan ditemukan
                    </span>

                    <a href="{{ route('kader.pemesanan.create') }}"
                       class="booking-btn booking-btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        Daftarkan Booking
                    </a>
                </div>
            </div>

            @if ($pemesanan->isEmpty())

                <div class="booking-empty">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Belum ada pemesanan yang sesuai.</p>
                    <small>Coba ubah kata kunci atau filter pencarian.</small>
                </div>

            @else

                <div class="booking-table-wrap">
                    <table class="booking-table">

                        <thead>
                            <tr>
                                <th>Jadwal Pelayanan</th>
                                <th>Pemilik</th>
                                <th>Jenis Hewan</th>
                                <th>Status Booking</th>
                                <th>Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pemesanan as $p)

                                @php
                                    $bayar = $pembayaran->get($p->id);

                                    $statusTampilan = $p->status === 'dikonfirmasi'
                                        ? 'Hadir'
                                        : ($labelStatus[$p->status] ?? ucfirst($p->status));
                                @endphp

                                <tr>

                                    {{-- JADWAL --}}
                                    <td>
                                        @if ($p->jadwalPelayanan)
                                            <div class="booking-main-text">
                                                {{ $p->jadwalPelayanan->tanggal->format('d M Y') }}
                                            </div>

                                            <div class="booking-sub-text">
                                                <i class="fa-regular fa-clock"></i>
                                                {{ substr($p->jadwalPelayanan->waktu_mulai, 0, 5) }}
                                                -
                                                {{ substr($p->jadwalPelayanan->waktu_selesai, 0, 5) }}
                                                WIB
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- PEMILIK --}}
                                    <td>
                                        <div class="booking-main-text">
                                            {{ $p->user?->name ?? '-' }}
                                        </div>
                                        <div class="booking-sub-text">
                                            Pemilik hewan
                                        </div>
                                    </td>

                                    {{-- HEWAN --}}
                                    <td>
                                        <div class="booking-animal">
                                            <div class="booking-animal-icon">
                                                <i class="fa-solid fa-paw"></i>
                                            </div>

                                            <div class="booking-main-text">
                                                {{ ucfirst($p->hewan?->jenis ?? '-') }}{{ $p->hewan?->ras ? ' (' . $p->hewan->ras . ')' : '' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- STATUS BOOKING --}}
                                    <td>
                                        <span class="booking-status booking-status-{{ $p->status }}">
                                            {{ $statusTampilan }}
                                        </span>
                                    </td>

                                    {{-- PEMBAYARAN --}}
                                    <td>
                                        <div class="booking-payment">
                                            @if ($bayar)
                                                @if ($bayar->status === 'menunggu_verifikasi')
                                                    <span class="booking-payment-label booking-unpaid">
                                                        Menunggu Verifikasi
                                                    </span>
                                                @elseif ($bayar->status === 'lunas')
                                                    <span class="booking-payment-label booking-paid">
                                                        <i class="fa-solid fa-check-circle"></i>
                                                        Lunas
                                                    </span>
                                                @elseif ($bayar->status === 'ditolak')
                                                    <span class="booking-payment-label booking-unpaid">
                                                        Ditolak
                                                    </span>
                                                @endif

                                                <span class="booking-sub-text">
                                                    Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                                    · {{ strtoupper($bayar->metode) }}
                                                </span>
                                            @else
                                                <span class="booking-payment-label booking-unpaid">
                                                    Belum Dibayar
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- AKSI --}}
                                    <td>
                                        <div class="booking-actions">

                                            @if ($bayar)
                                                <a href="{{ route('kader.pembayaran.show', $p) }}"
                                                   class="booking-action-btn booking-action-pay">
                                                    <i class="fa-solid fa-eye"></i>
                                                    Periksa Pembayaran
                                                </a>
                                            @endif

                                            @foreach ($transisi[$p->status] ?? [] as $kode => $label)

                                                <form method="POST"
                                                      action="{{ route('kader.pemesanan.status', $p) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input
                                                        type="hidden"
                                                        name="status"
                                                        value="{{ $kode }}"
                                                    >

                                                    <button
                                                        type="{{ $kode === 'dibatalkan' ? 'button' : 'submit' }}"
                                                        class="booking-action-btn"
                                                        @if ($kode === 'dibatalkan')
                                                            data-action="open-booking-modal"
                                                        @endif
                                                    >
                                                        {{ $label }}
                                                    </button>
                                                </form>

                                            @endforeach

                                        </div>
                                    </td>

                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="kader-pagination-wrap">
                    {{ $pemesanan->links() }}
                </div>

            @endif

        </section>
    </div>

    {{-- MODAL KONFIRMASI PEMBATALAN --}}
    <div class="booking-modal-overlay" id="bookingCancelModal">
        <div class="booking-modal">

            <div class="booking-modal-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <h3>Batalkan Pemesanan?</h3>

            <p>
                Apakah kamu yakin ingin membatalkan pemesanan ini?
                Tindakan ini akan mengubah status booking menjadi dibatalkan.
            </p>

            <div class="booking-modal-actions">

                <button type="button"
                        class="booking-modal-back"
                        id="bookingModalBackBtn">
                    Kembali
                </button>

                <button type="button"
                        class="booking-modal-confirm"
                        id="bookingModalConfirmBtn">
                    Ya, Batalkan
                </button>

            </div>
        </div>
    </div>
@endisset
            </main>
        </div>

    </div>

</body>
</html>
