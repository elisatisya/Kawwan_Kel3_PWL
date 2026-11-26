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
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>Pemesanan Antrean</h2>
                </div>

                <a href="{{ route('pemilik.profil') }}" class="pemilik-user-profile" title="Lihat Profil">
                    <div class="pemilik-avatar">
                        @if (auth()->user()->foto)
                            <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </a>
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
                                    @php
                                        $bisaBayar = ! in_array($p->status, ['dibatalkan', 'tidak_hadir', 'selesai']) && (! $bayar || $bayar->status === 'ditolak');
                                        $bisaBatal = $p->status === 'menunggu';
                                        $hewanLabel = ucfirst($p->hewan?->jenis ?? '-') . ($p->hewan?->ras ? ' (' . $p->hewan->ras . ')' : '');
                                        $jadwalLabel = $p->jadwalPelayanan
                                            ? $p->jadwalPelayanan->tanggal->format('d M Y') . ', ' . substr($p->jadwalPelayanan->waktu_mulai, 0, 5) . ' - ' . substr($p->jadwalPelayanan->waktu_selesai, 0, 5) . ' WIB'
                                            : '-';
                                        $layananLabel = $p->pemesananLayanan->isNotEmpty()
                                            ? $p->pemesananLayanan->map(fn ($pl) => $pl->layanan?->nama)->filter()->join(', ')
                                            : 'Pelayanan Umum';
                                    @endphp

                                    @if ($bisaBayar || $bisaBatal)
                                        <div class="pemilik-table-actions">
                                            @if ($bisaBayar)
                                                <button type="button"
                                                        class="pemilik-pay-trigger"
                                                        onclick="openBayarModal(this)"
                                                        data-action="{{ route('pemilik.pemesanan.bayar', $p) }}"
                                                        data-jumlah="{{ $totalBiaya > 0 ? $totalBiaya : 0 }}"
                                                        data-hewan="{{ $hewanLabel }}"
                                                        data-jadwal="{{ $jadwalLabel }}"
                                                        data-layanan="{{ $layananLabel }}">
                                                    <i class="fa-solid fa-wallet"></i>
                                                    <span>{{ $bayar && $bayar->status === 'ditolak' ? 'Bayar Ulang' : 'Bayar' }}</span>
                                                </button>
                                            @endif

                                            @if ($bisaBatal)
                                                <form method="POST"
                                                      action="{{ route('pemilik.pemesanan.batal', $p) }}"
                                                      data-confirm="Yakin ingin membatalkan pemesanan antrean ini?">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn-icon-action is-delete"
                                                            title="Batalkan Booking"
                                                            aria-label="Batalkan Booking">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="pemilik-text-muted">-</span>
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

    {{-- Modal Popup Pembayaran Online --}}
    <div id="modalBayar" class="pemilik-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="pemilik-modal-overlay" onclick="closeBayarModal()"></div>
        <div class="pemilik-modal-dialog pay-modal-dialog">
            <div class="pemilik-modal-head">
                <div>
                    <h2>Pembayaran <span>Online</span></h2>
                    <p>Pilih metode pembayaran lalu unggah bukti transfer atau scan QRIS.</p>
                </div>
                <button type="button" class="pemilik-modal-close" onclick="closeBayarModal()" aria-label="Tutup">&times;</button>
            </div>

            <div class="pay-summary-card">
                <div class="pay-summary-meta">
                    <strong id="payModalHewan">-</strong>
                    <span id="payModalJadwal">-</span>
                    <small id="payModalLayanan">-</small>
                </div>
                <div class="pay-summary-total">
                    <span>Total Bayar</span>
                    <strong id="payModalTotal">Rp 0</strong>
                </div>
            </div>

            <form method="POST"
                  action=""
                  enctype="multipart/form-data"
                  id="modalBayarForm"
                  class="pemilik-simple-form">
                @csrf
                <input type="hidden" name="jumlah" id="payModalJumlahInput" value="0">

                <div class="pemilik-form-group">
                    <label for="payModalMetode">Metode Pembayaran *</label>
                    <select id="payModalMetode" name="metode" required onchange="updateModalPayInfo(this.value)">
                        <option value="">-- Pilih Metode Pembayaran --</option>
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
                </div>

                {{-- Panel Info Metode Pembayaran --}}
                <div id="payModalInfoPanel" class="pay-info-panel" hidden>
                    {{-- QRIS --}}
                    <div id="payModalQrWrap" class="pay-qr-wrap" hidden>
                        @if (file_exists(public_path('images/qris-kawwan.png')))
                            <img src="{{ asset('images/qris-kawwan.png') }}" alt="QRIS KAWWAN" class="pay-qr-img">
                        @else
                            <div class="pay-qris-card">
                                <span class="pay-qris-badge">QRIS</span>
                                <svg class="pay-qris-svg" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" aria-label="QR Code KAWWAN Puskesmas">
                                    <rect width="120" height="120" rx="8" fill="#FFFFFF"/>
                                    <rect x="10" y="10" width="30" height="30" rx="3" fill="none" stroke="#181310" stroke-width="5"/>
                                    <rect x="19" y="19" width="12" height="12" rx="1.5" fill="#FF6B00"/>
                                    <rect x="80" y="10" width="30" height="30" rx="3" fill="none" stroke="#181310" stroke-width="5"/>
                                    <rect x="89" y="19" width="12" height="12" rx="1.5" fill="#181310"/>
                                    <rect x="10" y="80" width="30" height="30" rx="3" fill="none" stroke="#181310" stroke-width="5"/>
                                    <rect x="19" y="89" width="12" height="12" rx="1.5" fill="#181310"/>
                                    <rect x="48" y="12" width="8" height="8" fill="#181310"/>
                                    <rect x="62" y="12" width="8" height="8" fill="#181310"/>
                                    <rect x="48" y="26" width="16" height="8" fill="#181310"/>
                                    <rect x="12" y="48" width="8" height="16" fill="#181310"/>
                                    <rect x="26" y="54" width="14" height="8" fill="#181310"/>
                                    <rect x="48" y="48" width="24" height="24" rx="4" fill="#FF6B00"/>
                                    <circle cx="60" cy="60" r="5" fill="#FFFFFF"/>
                                    <rect x="80" y="48" width="10" height="10" fill="#181310"/>
                                    <rect x="98" y="48" width="12" height="8" fill="#181310"/>
                                    <rect x="88" y="64" width="22" height="8" fill="#181310"/>
                                    <rect x="48" y="80" width="10" height="10" fill="#181310"/>
                                    <rect x="64" y="80" width="18" height="8" fill="#181310"/>
                                    <rect x="48" y="96" width="20" height="12" fill="#181310"/>
                                    <rect x="76" y="94" width="14" height="14" fill="#181310"/>
                                    <rect x="96" y="82" width="14" height="26" fill="#181310"/>
                                </svg>
                                <span class="pay-qris-merchant">KAWWAN PUSKESWAN</span>
                            </div>
                        @endif
                        <p class="pay-qr-note">Scan QR di atas melalui aplikasi e-wallet / m-banking Anda, lalu unggah bukti bayar di bawah.</p>
                    </div>

                    {{-- Transfer Bank / E-Wallet --}}
                    <div id="payModalTransferWrap" class="pay-transfer-info" hidden>
                        <div class="pay-transfer-row">
                            <div>
                                <div class="pay-rekening-label" id="payModalRekLabel">-</div>
                                <div class="pay-rekening-no" id="payModalRekNo">-</div>
                                <div class="pay-rekening-name" id="payModalRekName">-</div>
                            </div>
                            <button type="button" class="pay-copy-btn" id="payCopyBtn" onclick="copyRekeningNo()">
                                <i class="fa-regular fa-copy"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Upload Bukti Pembayaran --}}
                <div class="pemilik-form-group">
                    <label for="payModalBukti">Bukti Pembayaran *</label>
                    <label for="payModalBukti" class="pay-upload-dropzone" id="payUploadDropzone">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span id="payModalFileName">Klik untuk memilih gambar bukti (JPG, PNG, WEBP)</span>
                        <input type="file"
                               id="payModalBukti"
                               name="bukti_pembayaran"
                               accept=".jpg,.jpeg,.png,.webp"
                               class="pay-file-input"
                               required>
                    </label>
                    <img id="payModalPreview" class="pay-proof-preview" alt="Preview bukti pembayaran" hidden>
                </div>

                <div class="pemilik-form-actions pay-modal-actions">
                    <button type="button" class="pemilik-btn pemilik-btn-outline" onclick="closeBayarModal()">
                        Batal
                    </button>
                    <button type="submit" class="pemilik-btn pemilik-btn-primary">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
            </main>
        </div>
    </div>

</body>
</html>
