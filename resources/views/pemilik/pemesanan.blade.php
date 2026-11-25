<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Pemesanan Antrean' }} — KAWWAN Pemilik Hewan</title>

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

    {{-- Panduan Alur Singkat --}}
    <div class="pemilik-steps-bar">
        <div class="pemilik-step-item">
            <span class="pemilik-step-num">1</span>
            <div>
                <strong>Pilih Hewan &amp; Jadwal</strong>
                <span>Tentukan anabul dan jam praktik dokter</span>
            </div>
        </div>
        <div class="pemilik-step-item">
            <span class="pemilik-step-num">2</span>
            <div>
                <strong>Pilih Layanan</strong>
                <span>Lihat estimasi biaya secara transparan</span>
            </div>
        </div>
        <div class="pemilik-step-item">
            <span class="pemilik-step-num">3</span>
            <div>
                <strong>Dapatkan Nomor Antrean</strong>
                <span>Pantau status &amp; konfirmasi pembayaran</span>
            </div>
        </div>
    </div>

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
                <div class="pemilik-empty-icon"><i class="fa-solid fa-paw"></i></div>
                <h3>Anda Belum Memiliki Data Hewan</h3>
                <p>Tambahkan data hewan peliharaan Anda terlebih dahulu sebelum melakukan pemesanan antrean.</p>
                <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Data Hewan</span>
                </a>
            </div>
        @elseif ($jadwalTersedia->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
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
                                            Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}
                                            ({{ match($bayar->metode) {
                                                'qris'             => 'QRIS',
                                                'gopay'            => 'GoPay',
                                                'ovo'              => 'OVO',
                                                'dana'             => 'DANA',
                                                'transfer_bca'     => 'Transfer BCA',
                                                'transfer_bri'     => 'Transfer BRI',
                                                'transfer_mandiri' => 'Transfer Mandiri',
                                                'transfer_bni'     => 'Transfer BNI',
                                                default            => strtoupper($bayar->metode),
                                            } }})
                                        </div>
                                    @else
                                        <span class="pemilik-badge is-neutral">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td>
                                    @if (! in_array($p->status, ['dibatalkan', 'tidak_hadir', 'selesai']) && (! $bayar || $bayar->status === 'ditolak'))
                                        {{-- FORM PEMBAYARAN ONLINE --}}
                                        <form method="POST"
                                              action="{{ route('pemilik.pemesanan.bayar', $p) }}"
                                              enctype="multipart/form-data"
                                              class="pemilik-inline-pay-form"
                                              id="pay-form-{{ $p->id }}">
                                            @csrf

                                            {{-- Jumlah otomatis dari biaya layanan --}}
                                            <input type="hidden" name="jumlah" value="{{ $totalBiaya > 0 ? $totalBiaya : 0 }}">

                                            {{-- Pilih Metode --}}
                                            <select name="metode" required
                                                    onchange="togglePayInfo(this, {{ $p->id }})">
                                                <option value="">-- Pilih Metode --</option>
                                                <optgroup label="QRIS">
                                                    <option value="qris">QRIS (Scan QR)</option>
                                                </optgroup>
                                                <optgroup label="E-Wallet">
                                                    <option value="gopay">GoPay</option>
                                                    <option value="ovo">OVO</option>
                                                    <option value="dana">DANA</option>
                                                </optgroup>
                                                <optgroup label="Transfer Bank">
                                                    <option value="transfer_bca">Transfer BCA</option>
                                                    <option value="transfer_bri">Transfer BRI</option>
                                                    <option value="transfer_mandiri">Transfer Mandiri</option>
                                                    <option value="transfer_bni">Transfer BNI</option>
                                                </optgroup>
                                            </select>

                                            {{-- Panel Info Pembayaran (muncul setelah pilih metode) --}}
                                            <div class="pay-info-panel" id="pay-info-{{ $p->id }}" style="display:none">

                                                {{-- QR Code (hanya untuk QRIS) --}}
                                                <div class="pay-qr-wrap" id="pay-qr-{{ $p->id }}" style="display:none">
                                                    @if (file_exists(public_path('images/qris-kawwan.png')))
                                                        <img src="{{ asset('images/qris-kawwan.png') }}"
                                                             alt="QR Code QRIS KAWWAN"
                                                             class="pay-qr-img">
                                                    @else
                                                        <div class="pay-qr-placeholder">
                                                            <i class="fa-solid fa-qrcode"></i>
                                                            <span>QR Code KAWWAN Puskesmas</span>
                                                        </div>
                                                    @endif
                                                    <p class="pay-qr-note">Scan QR di atas lalu upload bukti pembayaran.</p>
                                                </div>

                                                {{-- Instruksi Transfer / E-Wallet --}}
                                                <div class="pay-transfer-info" id="pay-transfer-{{ $p->id }}" style="display:none">
                                                    <div class="pay-rekening-label" id="pay-rek-label-{{ $p->id }}"></div>
                                                    <div class="pay-rekening-no" id="pay-rek-no-{{ $p->id }}"></div>
                                                    <div class="pay-rekening-name" id="pay-rek-name-{{ $p->id }}"></div>
                                                </div>

                                                {{-- Nominal --}}
                                                <div class="pay-amount-row">
                                                    <span>Total Pembayaran:</span>
                                                    <strong>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong>
                                                </div>

                                                {{-- Upload Bukti --}}
                                                <label class="pay-upload-label">
                                                    <i class="fa-solid fa-paperclip"></i>
                                                    Upload Bukti Pembayaran
                                                    <input type="file"
                                                           name="bukti_pembayaran"
                                                           accept=".jpg,.jpeg,.png,.webp"
                                                           class="pay-file-input"
                                                           required>
                                                </label>
                                                <span class="pay-file-name" id="pay-fname-{{ $p->id }}">Belum ada file dipilih</span>
                                            </div>

                                            <button type="submit" class="pemilik-btn-sm" id="pay-btn-{{ $p->id }}" style="display:none">
                                                <i class="fa-solid fa-paper-plane"></i> Kirim Pembayaran
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
