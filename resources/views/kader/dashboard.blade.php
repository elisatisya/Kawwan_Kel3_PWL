
@extends('kader.layout')

@section('title', 'Dashboard Kader')

@section('content')

<style>
    /* DASHBOARD STATISTIK */
    .kader-dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .kader-dashboard-stats .dokter-card {
        margin: 0;
        min-width: 0;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .kader-dashboard-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .kader-dashboard-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- HEADER --}}
<div class="dokter-head">
    <h1>Dashboard <span>Kader Puskesmas</span></h1>
    <p>Selamat datang di panel pengelolaan pelayanan KAWWAN.</p>
</div>

{{-- STATISTIK DASHBOARD --}}
<div class="kader-dashboard-stats">

    <div class="dokter-card">
        <h3>Booking Hari Ini</h3>
        <h2>{{ $stat['booking_hari_ini'] }}</h2>
        <p>Total pemesanan hari ini</p>
    </div>

    <div class="dokter-card">
        <h3>Menunggu Kedatangan</h3>
        <h2>{{ $stat['menunggu_kedatangan'] }}</h2>
        <p>Pemilik yang menunggu pelayanan</p>
    </div>

    <div class="dokter-card">
        <h3>Belum Dibayar</h3>
        <h2>{{ $stat['belum_dibayar'] }}</h2>
        <p>Pemesanan belum memiliki pembayaran</p>
    </div>

    <div class="dokter-card">
        <h3>Artikel Diajukan</h3>
        <h2>{{ $stat['artikel_diajukan'] }}</h2>
        <p>Artikel yang diajukan</p>
    </div>

</div>

{{-- JADWAL PELAYANAN HARI INI --}}
<section class="dokter-card">

    <h2>Jadwal Pelayanan Hari Ini</h2>
    <p>Daftar pemesanan berdasarkan jadwal pelayanan.</p>

    @if ($pemesananHariIni->isEmpty())

        <p class="dokter-empty">
            Belum ada pemesanan untuk hari ini.
        </p>

    @else

        <div class="dokter-table-wrap">
            <table class="dokter-table">

                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($pemesananHariIni as $p)

                        @php
                            $bayar = $pembayaran->get($p->id);
                        @endphp

                        <tr>
                            {{-- WAKTU --}}
                            <td>
                                {{ substr($p->jadwalPelayanan->waktu_mulai, 0, 5) }}
                                -
                                {{ substr($p->jadwalPelayanan->waktu_selesai, 0, 5) }}
                            </td>

                            {{-- PEMILIK --}}
                            <td>
                                {{ $p->user?->name ?? '-' }}
                            </td>

                            {{-- HEWAN --}}
                            <td>
                                {{ $p->hewan?->nama ?? '-' }}
                            </td>

                            {{-- STATUS BOOKING --}}
                            <td>
                                <span class="dokter-badge is-{{ $p->status }}">
                                    {{ $labelStatus[$p->status] ?? ucfirst($p->status) }}
                                </span>
                            </td>

                            {{-- STATUS PEMBAYARAN --}}
                            <td>
                                @if ($bayar && $bayar->status === 'lunas')

                                    <span class="admin-status-badge is-aktif">
                                        Lunas
                                    </span>

                                @elseif ($bayar && $bayar->status === 'menunggu_verifikasi')

                                    <span class="admin-status-badge is-menunggu">
                                        Menunggu Verifikasi
                                    </span>

                                @elseif ($bayar && $bayar->status === 'ditolak')

                                    <span class="admin-status-badge is-menunggu">
                                        Ditolak
                                    </span>

                                @else

                                    <span class="admin-status-badge is-menunggu">
                                        Belum Dibayar
                                    </span>

                                @endif
                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

    @endif

</section>

@endsection
