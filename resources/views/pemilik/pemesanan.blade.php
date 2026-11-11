<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Pemesanan Antrean' }} — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        <div class="pemilik-backdrop" id="pemilikBackdrop"></div>

        <aside class="pemilik-sidebar" id="pemilikSidebar">
            <a href="{{ route('pemilik.dashboard') }}" class="pemilik-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="pemilik-menu-label">Menu Pemilik</p>

            <nav class="pemilik-menu" aria-label="Navigasi Pemilik Hewan">
                <a href="{{ route('pemilik.dashboard') }}" class="{{ request()->routeIs('pemilik.dashboard') || request()->routeIs('pemilik.beranda') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('pemilik.hewan.index') }}" class="{{ request()->routeIs('pemilik.hewan.*') ? 'active' : '' }}">
                    <span>Data Hewan</span>
                </a>
                <a href="{{ route('pemilik.jadwal.index') }}" class="{{ request()->routeIs('pemilik.jadwal.*') ? 'active' : '' }}">
                    <span>Jadwal Pelayanan</span>
                </a>
                <a href="{{ route('pemilik.pemesanan.index') }}" class="{{ request()->routeIs('pemilik.pemesanan.*') ? 'active' : '' }}">
                    <span>Pemesanan Antrean</span>
                </a>
                <a href="{{ route('landing') }}#artikel">
                    <span>Artikel Edukasi</span>
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="pemilik-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('pemilik hewan')">
                    Keluar
                </button>
            </form>
        </aside>

        <div class="pemilik-main-wrap">
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        Menu
                    </button>
                    <h2>{{ 'Pemesanan Antrean' }}</h2>
                </div>

                <div class="pemilik-user-profile">
                    <div class="pemilik-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </div>
            </header>

            <main class="pemilik-main">
<div class="pemilik-container">

    <div class="pemilik-page-head">
        <div>
            <h1>Pemesanan <span>Antrean Pelayanan</span></h1>
            <p>Daftarkan hewan peliharaan Anda pada jadwal pelayanan, pilih layanan, dan pantau nomor antrean serta status pembayaran.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="pemilik-alert is-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="pemilik-alert is-error">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Form Pendaftaran Pemesanan Antrean --}}
    <section class="pemilik-card">
        <div class="pemilik-card-header">
            <div>
                <h2>Buat Pemesanan Baru</h2>
                <p>Pilih hewan peliharaan, jadwal pelayanan yang tersedia, dan layanan yang dibutuhkan</p>
            </div>
        </div>

        @if ($daftarHewan->isEmpty())
            <div class="pemilik-empty">
                <h3>Anda Belum Memiliki Data Hewan</h3>
                <p>Tambahkan data hewan peliharaan Anda terlebih dahulu sebelum melakukan pemesanan antrean.</p>
                <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-primary">
                    Tambah Data Hewan
                </a>
            </div>
        @elseif ($jadwalTersedia->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Jadwal Tersedia</h3>
                <p>Saat ini belum ada jadwal pelayanan puskeswan yang memiliki kuota tersedia.</p>
            </div>
        @else
            <form method="POST" action="{{ route('pemilik.pemesanan.store') }}" class="pemilik-booking-form" id="pemilikBookingForm">
                @csrf

                <div class="pemilik-form-grid-2">
                    <div class="pemilik-form-group">
                        <label for="hewan_id">Pilih Hewan Peliharaan *</label>
                        <select id="hewan_id" name="hewan_id" required>
                            <option value="">Pilih hewan</option>
                            @foreach ($daftarHewan as $h)
                                <option value="{{ $h->id }}" @selected(old('hewan_id') == $h->id)>
                                    {{ ucfirst($h->jenis) }}{{ $h->ras ? ' (' . $h->ras . ')' : '' }} — {{ ucfirst($h->jenis_kelamin) }}
                                </option>
                            @endforeach
                        </select>
                        @error('hewan_id')
                            <span class="pemilik-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pemilik-form-group">
                        <label for="jadwal_pelayanan_id">Pilih Jadwal Pelayanan *</label>
                        <select id="jadwal_pelayanan_id" name="jadwal_pelayanan_id" required>
                            <option value="">Pilih jadwal pelayanan</option>
                            @foreach ($jadwalTersedia as $j)
                                @php
                                    $sisa = $j->kuota === null ? 'tanpa batas' : ($j->kuota - $j->terpakai);
                                @endphp
                                <option value="{{ $j->id }}" @selected((old('jadwal_pelayanan_id') ?? $jadwalDipilih) == $j->id)>
                                    {{ $j->tanggal->format('d M Y') }} ({{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB) — {{ $j->dokter?->name ?? 'Dokter Puskeswan' }} (Sisa kuota: {{ $sisa }})
                                </option>
                            @endforeach
                        </select>
                        @error('jadwal_pelayanan_id')
                            <span class="pemilik-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($daftarLayanan->isNotEmpty())
                    <div class="pemilik-form-group">
                        <label>Pilih Layanan Pelayanan (Opsional)</label>
                        <div class="pemilik-service-checks">
                            @php
                                $oldLayanan = old('layanan_ids', []);
                            @endphp
                            @foreach ($daftarLayanan as $l)
                                <label class="pemilik-service-check-item">
                                    <input type="checkbox"
                                           name="layanan_ids[]"
                                           value="{{ $l->id }}"
                                           data-biaya="{{ $l->biaya }}"
                                           @checked(in_array($l->id, $oldLayanan))>
                                    <div class="pemilik-service-check-info">
                                        <strong>{{ $l->nama }}</strong>
                                        <span>{{ $l->biaya == 0 ? 'Gratis' : 'Rp ' . number_format($l->biaya, 0, ',', '.') }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="pemilik-total-estimate">
                            <span>Estimasi Total Biaya:</span>
                            <strong id="pemilikTotalEstimasi">Rp 0</strong>
                        </p>
                    </div>
                @endif

                <div class="pemilik-form-actions">
                    <button type="submit" class="pemilik-btn pemilik-btn-primary">
                        Pesan Antrean Sekarang
                    </button>
                </div>
            </form>
        @endif
    </section>

    {{-- Daftar Riwayat Pemesanan & Antrean --}}
    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Daftar Pemesanan &amp; Antrean Saya</h2>
                <p>Pantau nomor antrean, status pemeriksaan, dan konfirmasi pembayaran</p>
            </div>
        </div>

        @if ($daftarPemesanan->isEmpty())
            <div class="pemilik-empty">
                <h3>Belum Ada Pemesanan Antrean</h3>
                <p>Pemesanan yang Anda daftarkan akan muncul di daftar ini beserta status antreannya.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>Jadwal</th>
                            <th>Jenis Hewan</th>
                            <th>Layanan &amp; Biaya</th>
                            <th>Nomor Antrean</th>
                            <th>Status Booking</th>
                            <th>Pembayaran</th>
                            <th>Aksi / Konfirmasi Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarPemesanan as $p)
                            @php
                                $totalBiaya = $p->pemesananLayanan->sum('biaya');
                                $bayar = $p->pembayaran;
                                $antreanList = $p->antrean;
                            @endphp
                            <tr>
                                <td>
                                    @if ($p->jadwalPelayanan)
                                        <strong>{{ $p->jadwalPelayanan->tanggal->format('d M Y') }}</strong>
                                        <div class="pemilik-text-muted">
                                            {{ substr($p->jadwalPelayanan->waktu_mulai, 0, 5) }} - {{ substr($p->jadwalPelayanan->waktu_selesai, 0, 5) }} WIB
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ ucfirst($p->hewan?->jenis ?? '-') }}</strong>
                                    <div class="pemilik-text-muted">{{ $p->hewan?->ras ?: 'Campuran' }}</div>
                                </td>
                                <td>
                                    @if ($p->pemesananLayanan->isNotEmpty())
                                        @foreach ($p->pemesananLayanan as $pl)
                                            <div>{{ $pl->layanan?->nama ?? '-' }}</div>
                                        @endforeach
                                        <div class="pemilik-text-muted">
                                            Total: Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <span>Pelayanan Umum</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($antreanList->isNotEmpty())
                                        @foreach ($antreanList as $a)
                                            <span class="pemilik-badge is-dikonfirmasi">{{ $a->nomor_antrean }}</span>
                                        @endforeach
                                    @else
                                        <span class="pemilik-text-muted">Menunggu verifikasi</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeStatus = match ($p->status) {
                                            'dikonfirmasi', 'selesai' => 'is-tersedia',
                                            'sedang_dilayani' => 'is-dikonfirmasi',
                                            default => 'is-menunggu',
                                        };
                                    @endphp
                                    <span class="pemilik-badge {{ $badgeStatus }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($bayar)
                                        @if ($bayar->status === 'lunas')
                                            <span class="pemilik-badge is-tersedia">Lunas</span>
                                        @elseif ($bayar->status === 'menunggu_verifikasi')
                                            <span class="pemilik-badge is-menunggu">Menunggu Verifikasi</span>
                                        @else
                                            <span class="pemilik-badge is-menunggu">Ditolak</span>
                                            @if ($bayar->catatan)
                                                <div class="pemilik-text-muted">Alasan: {{ $bayar->catatan }}</div>
                                            @endif
                                        @endif
                                        <div class="pemilik-text-muted">
                                            Rp {{ number_format($bayar->jumlah, 0, ',', '.') }} ({{ strtoupper($bayar->metode) }})
                                        </div>
                                    @else
                                        <span class="pemilik-badge is-neutral">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td>
                                    @if (! in_array($p->status, ['dibatalkan', 'tidak_hadir', 'selesai']) && (! $bayar || $bayar->status === 'ditolak'))
                                        <form method="POST"
                                              action="{{ route('pemilik.pemesanan.bayar', $p) }}"
                                              enctype="multipart/form-data"
                                              class="pemilik-inline-pay-form">
                                            @csrf
                                            <input type="number"
                                                   name="jumlah"
                                                   value="{{ $totalBiaya }}"
                                                   min="0"
                                                   placeholder="Nominal (Rp)"
                                                   required>
                                            <select name="metode" required>
                                                <option value="transfer">Transfer</option>
                                                <option value="qris">QRIS</option>
                                                <option value="tunai">Tunai</option>
                                            </select>
                                            <input type="file"
                                                   name="bukti_pembayaran"
                                                   accept=".jpg,.jpeg,.png,.webp"
                                                   title="Unggah bukti pembayaran">
                                            <button type="submit" class="pemilik-btn-sm">
                                                Kirim Pembayaran
                                            </button>
                                        </form>
                                    @endif

                                    @if ($p->status === 'menunggu')
                                        <form method="POST"
                                              action="{{ route('pemilik.pemesanan.batal', $p) }}"
                                              data-confirm="Yakin ingin membatalkan pemesanan antrean ini?">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="pemilik-action-link is-danger">
                                                Batalkan Booking
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="kader-pagination-wrap">
                {{ $daftarPemesanan->links() }}
            </div>
        @endif
    </section>

</div>
            </main>
        </div>
    </div>

</body>
</html>
