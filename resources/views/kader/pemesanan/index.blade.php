
@extends('kader.layout')

@section('title', 'Booking & Pembayaran')

@section('content')

<style>
    .booking-page {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .booking-heading h1 {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 800;
        color: #171717;
    }

    .booking-heading h1 span {
        color: #ff6900;
    }

    .booking-heading p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .booking-panel {
        background: #fff;
        border: 1px solid #f1ddcf;
        border-radius: 14px;
        padding: 22px;
    }

    .booking-toolbar {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 22px;
    }

    .booking-search label,
    .booking-filter-item label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #292929;
    }

    .booking-search input,
    .booking-filter-item input,
    .booking-filter-item select {
        width: 100%;
        min-height: 43px;
        padding: 10px 13px;
        border: 1px solid #ead8cc;
        border-radius: 9px;
        background: #fff;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        box-sizing: border-box;
    }

    .booking-search input:focus,
    .booking-filter-item input:focus,
    .booking-filter-item select:focus {
        border-color: #ff6900;
        box-shadow: 0 0 0 3px rgba(255, 105, 0, .09);
    }

    .booking-filter-row {
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
    }

    .booking-filter-item {
        flex: 1;
        min-width: 170px;
    }

    .booking-buttons {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 9px;
    }

    .booking-btn {
        min-height: 40px;
        padding: 9px 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 9px;
        border: 1px solid #ddd;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }

    .booking-btn-primary {
        background: #ff6900;
        color: #fff;
        border-color: #ff6900;
    }

    .booking-btn-primary:hover {
        background: #e95f00;
        color: #fff;
    }

    .booking-btn-secondary {
        background: #fff;
        color: #333;
    }

    .booking-btn-secondary:hover {
        background: #fff5ed;
        border-color: #ff6900;
    }

    .booking-section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .booking-section-title h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
        color: #222;
    }

    .booking-count {
        color: #777;
        font-size: 12px;
    }

    .booking-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .booking-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .booking-table thead th {
        padding: 14px 13px;
        background: #f8f1ec;
        color: #776d66;
        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .booking-table tbody td {
        padding: 16px 13px;
        border-bottom: 1px solid #f0e9e4;
        vertical-align: middle;
        font-size: 13px;
        color: #292929;
    }

    .booking-table tbody tr:hover {
        background: #fffaf6;
    }

    .booking-main-text {
        font-weight: 700;
        color: #242424;
        margin-bottom: 4px;
    }

    .booking-sub-text {
        font-size: 12px;
        color: #888;
    }

    .booking-animal {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .booking-animal-icon {
        width: 35px;
        height: 35px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff0e4;
        color: #f26b16;
        border-radius: 10px;
    }

    .booking-status {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .booking-status-menunggu {
        color: #a66a00;
        background: #fff2cf;
    }

    .booking-status-dikonfirmasi {
        color: #16834c;
        background: #dcf5e7;
    }

    .booking-status-sedang_dilayani {
        color: #2866ad;
        background: #e2efff;
    }

    .booking-status-selesai {
        color: #16834c;
        background: #dcf5e7;
    }

    .booking-status-dibatalkan,
    .booking-status-tidak_hadir {
        color: #b33b3b;
        background: #fde5e5;
    }

    .booking-payment {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .booking-payment-label {
        width: fit-content;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .booking-paid {
        background: #dcf5e7;
        color: #16834c;
    }

    .booking-unpaid {
        background: #fff2cf;
        color: #a66a00;
    }

    .booking-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .booking-actions form {
        margin: 0;
    }

    .booking-action-btn {
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid #ded6d0;
        background: #fff;
        color: #444;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        font-family: inherit;
    }

    .booking-action-btn:hover {
        border-color: #ff6900;
        color: #e95f00;
    }

    .booking-action-pay {
        background: #fff0e4;
        color: #d95b00;
        border-color: #ffdec5;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .booking-empty {
        padding: 45px 15px;
        text-align: center;
        color: #888;
    }

    .booking-empty i {
        font-size: 30px;
        color: #e6b28d;
        margin-bottom: 12px;
    }

    .booking-alert {
        padding: 13px 16px;
        border-radius: 9px;
        font-size: 13px;
    }

    .booking-alert-success {
        color: #187746;
        background: #e4f6eb;
    }

    .booking-alert-error {
        color: #b33b3b;
        background: #fde9e9;
    }

    /* MODAL PEMBATALAN */
    .booking-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(25, 20, 17, .48);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
    }

    .booking-modal-overlay.show {
        display: flex;
    }

    .booking-modal {
        width: 100%;
        max-width: 420px;
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0, 0, 0, .16);
        animation: bookingModalIn .2s ease;
    }

    .booking-modal-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 17px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff0e4;
        color: #f36b16;
        font-size: 25px;
    }

    .booking-modal h3 {
        margin: 0 0 9px;
        font-size: 19px;
        font-weight: 800;
        color: #252525;
    }

    .booking-modal p {
        margin: 0;
        color: #777;
        font-size: 13px;
        line-height: 1.6;
    }

    .booking-modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .booking-modal-actions button {
        flex: 1;
        min-height: 42px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .booking-modal-back {
        background: #fff;
        border: 1px solid #ded6d0;
        color: #444;
    }

    .booking-modal-confirm {
        background: #e85f16;
        border: 1px solid #e85f16;
        color: #fff;
    }

    .booking-modal-confirm:hover {
        background: #cc4e0c;
    }

    @keyframes bookingModalIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 768px) {
        .booking-panel {
            padding: 15px;
        }

        .booking-filter-row {
            align-items: stretch;
            flex-direction: column;
        }

        .booking-filter-item {
            width: 100%;
        }

        .booking-buttons {
            width: 100%;
        }

        .booking-buttons .booking-btn {
            flex: 1;
        }

        .booking-heading h1 {
            font-size: 21px;
        }

        .booking-modal {
            padding: 23px 19px;
        }
    }
</style>

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
                    placeholder="Cari berdasarkan nama pemilik atau nama hewan..."
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

            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
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
                            <th>Hewan</th>
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
                                            {{ $p->hewan?->nama ?? '-' }}
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
                                                        onclick="openBookingModal(this)"
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
            <div style="margin-top: 18px;">
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
                    onclick="closeBookingModal()">
                Kembali
            </button>

            <button type="button"
                    class="booking-modal-confirm"
                    onclick="confirmBookingCancel()">
                Ya, Batalkan
            </button>

        </div>
    </div>
</div>

<script>
    let bookingCancelForm = null;

    function openBookingModal(button) {
        bookingCancelForm = button.closest('form');

        document.getElementById('bookingCancelModal')
            .classList.add('show');
    }

    function closeBookingModal() {
        document.getElementById('bookingCancelModal')
            .classList.remove('show');

        bookingCancelForm = null;
    }

    function confirmBookingCancel() {
        if (bookingCancelForm) {
            bookingCancelForm.submit();
        }
    }

    document.getElementById('bookingCancelModal')
        .addEventListener('click', function (event) {
            if (event.target === this) {
                closeBookingModal();
            }
        });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeBookingModal();
        }
    });
</script>

@endsection
