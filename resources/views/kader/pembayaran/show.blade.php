
@extends('kader.layout')

@section('title', 'Verifikasi Pembayaran')

@section('content')

<style>
    .payment-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .payment-heading h1 {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 800;
        color: #222;
    }

    .payment-heading h1 span {
        color: #ff6900;
    }

    .payment-heading p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .payment-panel {
        background: #fff;
        border: 1px solid #f1ddcf;
        border-radius: 14px;
        padding: 24px;
    }

    .payment-title {
        font-size: 17px;
        font-weight: 800;
        margin: 0 0 18px;
        color: #292929;
    }

    .payment-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .payment-info-item small {
        display: block;
        color: #888;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .payment-info-item strong {
        color: #292929;
        font-size: 14px;
    }

    .payment-proof {
        display: block;
        width: 100%;
        max-width: 480px;
        max-height: 550px;
        object-fit: contain;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fafafa;
    }

    .payment-empty {
        padding: 30px;
        background: #fff8f2;
        color: #a66a00;
        border-radius: 10px;
        text-align: center;
    }

    .payment-status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-waiting {
        background: #fff2cf;
        color: #a66a00;
    }

    .status-paid {
        background: #dcf5e7;
        color: #16834c;
    }

    .status-rejected {
        background: #fde5e5;
        color: #b33b3b;
    }

    .payment-input {
        width: 100%;
        min-height: 90px;
        padding: 12px;
        border: 1px solid #ead8cc;
        border-radius: 9px;
        box-sizing: border-box;
        font-family: inherit;
        resize: vertical;
    }

    .payment-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .payment-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 16px;
        border: none;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        font-family: inherit;
    }

    .payment-btn-back {
        background: #f3f3f3;
        color: #444;
    }

    .payment-btn-success {
        background: #16834c;
        color: #fff;
    }

    .payment-btn-danger {
        background: #c83e3e;
        color: #fff;
    }

    .payment-alert {
        padding: 13px 16px;
        border-radius: 9px;
        font-size: 13px;
        background: #fde9e9;
        color: #b33b3b;
    }

    @media(max-width: 650px) {
        .payment-info {
            grid-template-columns: 1fr;
        }

        .payment-panel {
            padding: 17px;
        }
    }
</style>

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
                <small>Nama Hewan</small>
                <strong>{{ $pemesanan->hewan?->nama ?? '-' }}</strong>
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
            <p style="font-size:12px;color:#888;margin-top:10px;">
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
                      onsubmit="return confirm('Apakah bukti pembayaran ini valid dan ingin diverifikasi?')">
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
                  style="margin-top:22px;">
                @csrf
                @method('PATCH')

                <label for="catatan" style="display:block;font-weight:700;font-size:13px;margin-bottom:8px;">
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
                            class="payment-btn payment-btn-danger"
                            onclick="return confirm('Yakin ingin menolak bukti pembayaran ini?')">
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

@endsection
