@extends('kader.layout')

@section('title', 'Booking & Pembayaran')

@section('content')

<div class="booking-page">

    {{-- HEADER --}}
    <div class="booking-heading">
        <h1>Booking & <span>Pembayaran</span></h1>
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

@endsection

