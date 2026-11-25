<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Jadwal & Layanan Pelayanan' }} — KAWWAN Pemilik Hewan</title>

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
            <h1>Jadwal &amp; <span>Layanan Pelayanan</span></h1>
            <p>Informasi jadwal praktik dokter hewan yang tersedia beserta daftar jenis layanan dan tarif puskeswan.</p>
        </div>
        <a href="{{ route('pemilik.pemesanan.index') }}" class="pemilik-btn pemilik-btn-primary">
            <i class="fa-solid fa-ticket"></i>
            <span>Buat Pemesanan Antrean</span>
        </a>
    </div>

    {{-- Bagian 1: Jadwal Pelayanan Tersedia --}}
    <section class="pemilik-card">
        <div class="pemilik-card-header">
            <div>
                <h2>Jadwal Praktik Dokter</h2>
                <p>Pilih jadwal yang masih memiliki kuota tersedia untuk mendaftarkan hewan Anda</p>
            </div>
        </div>

        @if ($jadwal->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                <h3>Belum Ada Jadwal Pelayanan</h3>
                <p>Saat ini belum ada jadwal praktik dokter hewan yang dibuka untuk waktu mendatang.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam Praktik</th>
                            <th>Dokter Bertugas</th>
                            <th>Layanan Dokter</th>
                            <th>Kuota</th>
                            <th>Sisa Kuota</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jadwal as $j)
                            @php
                                $sisa = $j->kuota === null ? null : max(0, $j->kuota - $j->terpakai);
                                $tersedia = $j->status === 'tersedia' && ($sisa === null || $sisa > 0);
                            @endphp
                            <tr>
                                <td><strong>{{ $j->tanggal->translatedFormat('d M Y') }}</strong></td>
                                <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                <td>{{ $j->dokter?->name ?? 'Dokter Puskeswan' }}</td>
                                <td>
                                    @if ($j->dokter && $j->dokter->layananDokter->isNotEmpty())
                                        <div class="pemilik-tag-list">
                                            @foreach ($j->dokter->layananDokter as $l)
                                                <span class="pemilik-badge is-neutral">{{ $l->nama }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span>Pelayanan Umum</span>
                                    @endif
                                </td>
                                <td>{{ $j->kuota ?? 'Tanpa batas' }}</td>
                                <td>{{ $sisa ?? 'Tersedia' }}</td>
                                <td>
                                    @if ($tersedia)
                                        <span class="pemilik-badge is-tersedia">Tersedia</span>
                                    @else
                                        <span class="pemilik-badge is-menunggu">Penuh</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($tersedia)
                                        <a href="{{ route('pemilik.pemesanan.index', ['jadwal' => $j->id]) }}" class="pemilik-btn-sm">
                                            Pesan Antrean
                                        </a>
                                    @else
                                        <span class="pemilik-text-muted">Kuota penuh</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="kader-pagination-wrap">
                {{ $jadwal->links() }}
            </div>
        @endif
    </section>

    {{-- Bagian 2: Daftar Layanan Pelayanan & Tarif --}}
    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Daftar Layanan &amp; Tarif Pelayanan</h2>
                <p>Jenis layanan pemeriksaan dan perawatan kesehatan hewan yang tersedia di puskeswan</p>
            </div>
        </div>

        @if ($layanan->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon"><i class="fa-solid fa-stethoscope"></i></div>
                <h3>Belum Ada Data Layanan</h3>
                <p>Daftar layanan dan tarif pelayanan belum ditambahkan oleh petugas.</p>
            </div>
        @else
            <div class="pemilik-layanan-grid">
                @foreach ($layanan as $item)
                    @php
                        $namaLower = strtolower($item->nama);
                        $svcIcon = 'fa-heart-pulse';
                        if (str_contains($namaLower, 'vaksin')) {
                            $svcIcon = 'fa-syringe';
                        } elseif (str_contains($namaLower, 'steril')) {
                            $svcIcon = 'fa-shield-cat';
                        } elseif (str_contains($namaLower, 'obat') || str_contains($namaLower, 'pengobatan')) {
                            $svcIcon = 'fa-pills';
                        } elseif (str_contains($namaLower, 'vitamin')) {
                            $svcIcon = 'fa-bottle-droplet';
                        } elseif (str_contains($namaLower, 'konsultasi')) {
                            $svcIcon = 'fa-user-doctor';
                        } elseif (str_contains($namaLower, 'umum') || str_contains($namaLower, 'pemeriksaan')) {
                            $svcIcon = 'fa-stethoscope';
                        }
                    @endphp
                    <div class="pemilik-layanan-card">
                        <div>
                            <div class="pemilik-layanan-top">
                                <div class="pemilik-layanan-icon">
                                    <i class="fa-solid {{ $svcIcon }}"></i>
                                </div>
                                @if ($item->durasi_menit)
                                    <span class="pemilik-layanan-duration">
                                        <i class="fa-regular fa-clock"></i> {{ $item->durasi_menit }} menit
                                    </span>
                                @endif
                            </div>
                            <div class="pemilik-layanan-body" style="margin-top: 12px;">
                                <h3>{{ $item->nama }}</h3>
                                <p>{{ $item->deskripsi ?: 'Pelayanan kesehatan hewan oleh dokter hewan Puskeswan.' }}</p>
                            </div>
                        </div>
                        <div class="pemilik-layanan-footer">
                            @if ($item->biaya == 0)
                                <span class="pemilik-badge is-tersedia">Gratis</span>
                            @else
                                <span class="pemilik-layanan-price">Rp {{ number_format($item->biaya, 0, ',', '.') }}</span>
                            @endif
                            <span class="pemilik-layanan-doc">
                                <i class="fa-solid fa-user-doctor"></i>
                                {{ $item->dokter->isNotEmpty() ? $item->dokter->pluck('name')->join(', ') : 'Dokter Puskeswan' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>
            </main>
        </div>
    </div>

</body>
</html>
