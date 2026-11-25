@php
    $p = $antrean->pemesanan;
    $hewan = $p->hewan;
    $hasil = $p->pemeriksaan;
    $jadwal = $p->jadwalPelayanan;
    $dokter = auth()->user();
    $namaDokter = trim(($dokter->gelar_depan ?? '').' '.$dokter->name.' '.($dokter->gelar_belakang ?? ''));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pemeriksaan Hewan - {{ $antrean->nomor_antrean }}</title>

    @vite(['resources/css/styles.css'])
</head>
<body class="cetak-body">

    <div class="cetak-toolbar">
        <button type="button" onclick="window.print()">Cetak PDF</button>
        <a href="{{ route('dokter.pemeriksaan.show', $antrean) }}">Kembali</a>
    </div>

    <div class="cetak-kertas">

        <div class="cetak-kop">
            <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            <div class="cetak-kop-teks">
                <h1>KAWWAN</h1>
                <p>Sistem Informasi Pelayanan Kesehatan Hewan</p>
            </div>
        </div>

        <div class="cetak-judul">
            <h2>Hasil Pemeriksaan Hewan</h2>
            <p>Nomor Antrean: {{ $antrean->nomor_antrean }}</p>
        </div>

        <p class="cetak-sec">A. Data Pasien</p>
        <table class="cetak-kv">
            <tr><td class="cetak-k">Tanggal Pemeriksaan</td><td class="cetak-t">:</td><td class="cetak-v">{{ $antrean->tanggal->locale('id')->translatedFormat('l, d F Y') }}</td></tr>
            @if ($jadwal)
                <tr><td class="cetak-k">Jam Praktik</td><td class="cetak-t">:</td><td class="cetak-v">{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</td></tr>
            @endif
            <tr><td class="cetak-k">Nama Pemilik</td><td class="cetak-t">:</td><td class="cetak-v">{{ $p->user?->name ?? '-' }}</td></tr>
            <tr><td class="cetak-k">Jenis Hewan</td><td class="cetak-t">:</td><td class="cetak-v">{{ ucfirst($hewan?->jenis ?? '-') }}{{ $hewan?->ras ? ' ('.$hewan->ras.')' : '' }}</td></tr>
            <tr><td class="cetak-k">Jenis Kelamin</td><td class="cetak-t">:</td><td class="cetak-v">{{ ucfirst($hewan?->jenis_kelamin ?? '-') }}</td></tr>
            <tr><td class="cetak-k">Umur</td><td class="cetak-t">:</td><td class="cetak-v">{{ $hewan?->umur ?? '-' }}</td></tr>
            <tr><td class="cetak-k">Layanan</td><td class="cetak-t">:</td><td class="cetak-v">{{ $p->pemesananLayanan->map(fn ($l) => $l->layanan?->nama)->filter()->join(', ') ?: '-' }}</td></tr>
        </table>

        <p class="cetak-sec">B. Hasil Pemeriksaan</p>
        <table class="cetak-kv">
            <tr><td class="cetak-k">Keluhan</td><td class="cetak-t">:</td><td class="cetak-v cetak-isi">{{ $hasil->keluhan ?: '-' }}</td></tr>
            <tr><td class="cetak-k">Hasil Pemeriksaan</td><td class="cetak-t">:</td><td class="cetak-v cetak-isi">{{ $hasil->hasil_pemeriksaan ?: '-' }}</td></tr>
            <tr><td class="cetak-k">Tindakan</td><td class="cetak-t">:</td><td class="cetak-v cetak-isi">{{ $hasil->tindakan ?: '-' }}</td></tr>
            <tr><td class="cetak-k">Catatan</td><td class="cetak-t">:</td><td class="cetak-v cetak-isi">{{ $hasil->catatan ?: '-' }}</td></tr>
        </table>

        <div class="cetak-ttd">
            <p>Dokter Pemeriksa,</p>
            <div class="cetak-ttd-ruang"></div>
            <p class="cetak-ttd-nama">{{ $namaDokter }}</p>
        </div>

        <p class="cetak-kaki">
            Dokumen ini dicetak dari sistem KAWWAN pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB.
        </p>

    </div>

</body>
</html>