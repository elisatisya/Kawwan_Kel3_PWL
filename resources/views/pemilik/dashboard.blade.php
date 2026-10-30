@extends('pemilik.layout')

@section('title', 'Dashboard Pemilik Hewan')

@section('content')
<div class="pemilik-container">

    <section class="pemilik-welcome">
        <div class="pemilik-welcome-content">
            <div class="pemilik-welcome-text">
                <span class="pemilik-welcome-tag">Portal Pemilik Hewan</span>
                <h1>Halo, {{ $user->name }}! 🐾</h1>
                <p>Kelola data kesehatan anabul Anda, lihat jadwal pelayanan puskeswan yang sedang buka, dan pantau status antrean rekam medis secara terpadu.</p>
            </div>
            <div class="pemilik-welcome-actions">
                <a href="#" class="pemilik-btn pemilik-btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Data Hewan</span>
                </a>
                <a href="#" class="pemilik-btn pemilik-btn-outline">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Pesan Antrean Pelayanan</span>
                </a>
            </div>
        </div>
    </section>

    <section class="pemilik-stats">
        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon is-orange">
                <i class="fa-solid fa-paw"></i>
            </div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Hewan Peliharaan</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_hewan'] }}">{{ $stat['total_hewan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon is-peach">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Antrean Aktif</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['antrean_aktif'] }}">{{ $stat['antrean_aktif'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon is-green">
                <i class="fa-solid fa-file-waveform"></i>
            </div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Riwayat Rekam Medis</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['total_pemeriksaan'] }}">{{ $stat['total_pemeriksaan'] }}</span>
            </div>
        </div>

        <div class="pemilik-stat-card">
            <div class="pemilik-stat-icon is-blue">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="pemilik-stat-info">
                <span class="pemilik-stat-label">Jadwal Tersedia</span>
                <span class="pemilik-stat-value" data-count="{{ $stat['jadwal_tersedia'] }}">{{ $stat['jadwal_tersedia'] }}</span>
            </div>
        </div>
    </section>

    <div class="pemilik-grid-2">
        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>Hewan Peliharaan Saya</h2>
                    <p>Daftar hewan yang terhubung dengan akun Anda</p>
                </div>
                <a href="#" class="pemilik-btn-sm">
                    <i class="fa-solid fa-plus"></i> Tambah
                </a>
            </div>

            @if ($daftarHewan->isEmpty())
                <div class="pemilik-empty">
                    <div class="pemilik-empty-icon">
                        <i class="fa-solid fa-dog"></i>
                    </div>
                    <h3>Belum Ada Data Hewan</h3>
                    <p>Daftarkan anabul Anda sekarang untuk memudahkan pemesanan antrean dan pencatatan rekam medis puskeswan.</p>
                    <a href="#" class="pemilik-btn pemilik-btn-primary">
                        <i class="fa-solid fa-plus"></i> Daftarkan Hewan Sekarang
                    </a>
                </div>
            @else
                <div class="pemilik-pet-list">
                    @foreach ($daftarHewan as $pet)
                        <div class="pemilik-pet-card">
                            <div class="pemilik-pet-avatar">
                                @if (strtolower($pet->jenis) === 'kucing')
                                    <i class="fa-solid fa-cat"></i>
                                @elseif (strtolower($pet->jenis) === 'anjing')
                                    <i class="fa-solid fa-dog"></i>
                                @else
                                    <i class="fa-solid fa-paw"></i>
                                @endif
                            </div>
                            <div class="pemilik-pet-details">
                                <div class="pemilik-pet-top">
                                    <h4 class="pemilik-pet-name">{{ $pet->nama }}</h4>
                                    <span class="pemilik-badge is-neutral">{{ ucfirst($pet->jenis) }}</span>
                                </div>
                                <div class="pemilik-pet-meta">
                                    <span><i class="fa-solid fa-dna"></i> {{ $pet->ras ?: 'Campuran' }}</span>
                                    <span><i class="fa-solid fa-venus-mars"></i> {{ ucfirst($pet->jenis_kelamin) }}</span>
                                    @if ($pet->umur)
                                        <span><i class="fa-solid fa-cake-candles"></i> {{ $pet->umur }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="pemilik-card">
            <div class="pemilik-card-header">
                <div>
                    <h2>Pemesanan & Antrean Aktif</h2>
                    <p>Status pemeriksaan hewan yang sedang berjalan</p>
                </div>
            </div>

            @if ($pemesananAktif->isEmpty())
                <div class="pemilik-empty">
                    <div class="pemilik-empty-icon">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <h3>Tidak Ada Antrean Aktif</h3>
                    <p>Anda belum memiliki jadwal pemesanan antrean pelayanan yang sedang menunggu atau aktif.</p>
                    <a href="#" class="pemilik-btn pemilik-btn-outline">
                        <i class="fa-solid fa-calendar-plus"></i> Pilih Jadwal Pemeriksaan
                    </a>
                </div>
            @else
                <div class="pemilik-booking-list">
                    @foreach ($pemesananAktif as $order)
                        <div class="pemilik-booking-card">
                            <div class="pemilik-booking-main">
                                <div class="pemilik-booking-date">
                                    <span class="pemilik-booking-day">{{ \Carbon\Carbon::parse($order->jadwalPelayanan?->tanggal)->format('d') }}</span>
                                    <span class="pemilik-booking-month">{{ \Carbon\Carbon::parse($order->jadwalPelayanan?->tanggal)->format('M Y') }}</span>
                                </div>
                                <div class="pemilik-booking-details">
                                    <h4>{{ $order->hewan?->nama ?? 'Hewan' }} ({{ ucfirst($order->hewan?->jenis ?? '-') }})</h4>
                                    <p><i class="fa-regular fa-clock"></i> {{ substr($order->jadwalPelayanan?->waktu_mulai ?? '', 0, 5) }} - {{ substr($order->jadwalPelayanan?->waktu_selesai ?? '', 0, 5) }} WIB</p>
                                </div>
                            </div>
                            <div class="pemilik-booking-status">
                                @php
                                    $statusClass = match(strtolower((string)$order->status)) {
                                        'dikonfirmasi' => 'is-dikonfirmasi',
                                        'sedang_dilayani' => 'is-sedang-dilayani',
                                        default => 'is-menunggu',
                                    };
                                @endphp
                                <span class="pemilik-badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Jadwal Pelayanan Puskeswan Terdekat</h2>
                <p>Pilih jadwal yang masih memiliki kuota untuk membawa hewan peliharaan Anda</p>
            </div>
        </div>

        @if ($jadwalTersedia->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <h3>Belum Ada Jadwal Pelayanan</h3>
                <p>Saat ini belum ada jadwal pelayanan puskeswan yang dibuka. Silakan cek kembali nanti.</p>
            </div>
        @else
            <div class="pemilik-schedule-grid">
                @foreach ($jadwalTersedia as $jadwal)
                    <div class="pemilik-schedule-card">
                        <div class="pemilik-schedule-header">
                            <span class="pemilik-badge is-tersedia">Tersedia</span>
                            <span class="pemilik-schedule-quota"><i class="fa-solid fa-users"></i> Kuota: {{ $jadwal->kuota ?? '-' }}</span>
                        </div>
                        <div class="pemilik-schedule-body">
                            <div class="pemilik-schedule-calendar">
                                <i class="fa-regular fa-calendar"></i>
                                <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="pemilik-schedule-time">
                                <i class="fa-regular fa-clock"></i>
                                <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                            </div>
                        </div>
                        <div class="pemilik-schedule-footer">
                            <a href="#" class="pemilik-btn-schedule">
                                <span>Pilih Jadwal</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="pemilik-card pemilik-section-margin">
        <div class="pemilik-card-header">
            <div>
                <h2>Riwayat Rekam Medis Terakhir</h2>
                <p>Hasil diagnosa, tindakan, dan catatan medis dari dokter hewan</p>
            </div>
        </div>

        @if ($riwayatPemeriksaan->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon">
                    <i class="fa-solid fa-file-medical"></i>
                </div>
                <h3>Belum Ada Riwayat Pemeriksaan</h3>
                <p>Riwayat rekam medis anabul Anda setelah selesai diperiksa oleh dokter hewan akan tampil di sini.</p>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Hewan</th>
                            <th>Keluhan</th>
                            <th>Hasil Pemeriksaan</th>
                            <th>Tindakan Dokter</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatPemeriksaan as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->jadwalPelayanan?->tanggal)->format('d M Y') }}</td>
                                <td><strong>{{ $history->hewan?->nama ?? '-' }}</strong></td>
                                <td>{{ $history->pemeriksaan?->keluhan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->hasil_pemeriksaan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->tindakan ?? '-' }}</td>
                                <td>{{ $history->pemeriksaan?->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>
@endsection
