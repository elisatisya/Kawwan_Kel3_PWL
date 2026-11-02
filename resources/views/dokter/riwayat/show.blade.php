@extends('dokter.layout')

@section('title', 'Catatan Pasien')

@section('content')

    <a href="{{ route('dokter.riwayat.index') }}" class="riwayat-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke riwayat
    </a>

    <section class="dokter-card">
        <h3>{{ $hewan->nama }}</h3>
        <p class="dokter-card-sub">Identitas pasien hewan.</p>

        <div class="riwayat-info">
            <div><span>Jenis</span><strong>{{ $hewan->jenis }}</strong></div>
            <div><span>Ras</span><strong>{{ $hewan->ras ?: '-' }}</strong></div>
            <div><span>Jenis Kelamin</span><strong>{{ ucfirst($hewan->jenis_kelamin) }}</strong></div>
            <div><span>Umur</span><strong>{{ $hewan->umur ?? '-' }}</strong></div>
            <div><span>Pemilik</span><strong>{{ $hewan->pemilik?->name ?? '-' }}</strong></div>
            <div><span>Telepon</span><strong>{{ $hewan->pemilik?->nomor_telepon ?: '-' }}</strong></div>
        </div>
    </section>

    <div class="riwayat-list">
        @forelse ($riwayat as $r)
            <section class="dokter-card">
                <div class="riwayat-item-head">
                    <span class="riwayat-item-date">{{ $r->created_at->format('d M Y, H:i') }} WIB</span>
                    <span class="dokter-badge is-{{ $r->pemesanan->status }}">{{ \Illuminate\Support\Str::headline($r->pemesanan->status) }}</span>
                </div>

                <div class="riwayat-field"><span>Keluhan</span><p>{{ $r->keluhan ?: '-' }}</p></div>
                <div class="riwayat-field"><span>Hasil Pemeriksaan</span><p>{{ $r->hasil_pemeriksaan ?: '-' }}</p></div>
                <div class="riwayat-field"><span>Tindakan</span><p>{{ $r->tindakan ?: '-' }}</p></div>
                <div class="riwayat-field"><span>Catatan</span><p>{{ $r->catatan ?: '-' }}</p></div>
            </section>
        @empty
            <section class="dokter-card">
                <p class="dokter-empty">Belum ada catatan pemeriksaan untuk hewan ini.</p>
            </section>
        @endforelse
    </div>

@endsection