
@extends('kader.layout')

@section('title', 'Jadwal Pelayanan')

@section('content')

<style>
    .jadwal-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }

    .jadwal-heading h1 {
        font-size: 25px;
        font-weight: 800;
        margin: 0 0 7px;
        color: #171717;
    }

    .jadwal-heading h1 span {
        color: #ff6900;
    }

    .jadwal-heading p {
        color: #777;
        font-size: 14px;
        margin: 0;
    }

    .jadwal-alert {
        padding: 13px 16px;
        border-radius: 9px;
        background: #e4f6eb;
        color: #187746;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .jadwal-table-wrap {
        overflow-x: auto;
    }

    .jadwal-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .jadwal-table th {
        padding: 14px 13px;
        background: #f8f1ec;
        color: #776d66;
        font-size: 11px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .jadwal-table td {
        padding: 15px 13px;
        border-bottom: 1px solid #f0e9e4;
        font-size: 13px;
        vertical-align: middle;
    }

    .jadwal-doctor {
        font-weight: 700;
        color: #333;
    }

    .jadwal-layanan {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 230px;
    }

    .layanan-tag {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 6px;
        background: #fff0e5;
        color: #e95f00;
        font-size: 11px;
        font-weight: 600;
    }

    .layanan-empty {
        color: #999;
        font-size: 12px;
    }

    .jadwal-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .jadwal-tersedia {
        background: #dcf5e7;
        color: #16834c;
    }

    .jadwal-penuh {
        background: #fff2cf;
        color: #a66a00;
    }

    .jadwal-nonaktif {
        background: #fde5e5;
        color: #b33b3b;
    }

    .jadwal-empty {
        padding: 35px;
        text-align: center;
        color: #888;
    }

    .jadwal-info {
        color: #888;
        font-size: 12px;
        margin-top: 16px;
    }
</style>

<div class="jadwal-heading">
    <div>
        <h1>Jadwal <span>Pelayanan</span></h1>
        <p>Pantau jadwal praktik dokter, layanan, dan kuota pelayanan.</p>
    </div>
</div>

@if (session('success'))
    <div class="jadwal-alert">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

<section class="dokter-card">

    @if ($jadwal->isEmpty())
        <div class="jadwal-empty">
            <i class="fa-regular fa-calendar-xmark"
               style="font-size:30px;margin-bottom:12px;"></i>

            <p>Belum ada jadwal pelayanan yang akan datang.</p>
        </div>
    @else

        <div class="jadwal-table-wrap">
            <table class="jadwal-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Dokter</th>
                        <th>Layanan</th>
                        <th>Jam Praktik</th>
                        <th>Kuota</th>
                        <th>Terpakai</th>
                        <th>Sisa Kuota</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($jadwal as $j)
                        @php
                            $sisa = $j->kuota === null
                                ? null
                                : max(0, $j->kuota - $j->terpakai);

                            $bisa = $j->status === 'tersedia'
                                && ($sisa === null || $sisa > 0);
                        @endphp

                        <tr>
                            <td>
                                {{ $j->tanggal->format('d M Y') }}
                            </td>

                            <td>
                                <span class="jadwal-doctor">
                                    {{ $j->dokter?->name ?? 'Belum ditentukan' }}
                                </span>
                            </td>

                            <td>
                                <div class="jadwal-layanan">
                                    @forelse ($j->dokter?->layananDokter ?? [] as $layanan)
                                        <span class="layanan-tag">
                                            {{ $layanan->nama }}
                                        </span>
                                    @empty
                                        <span class="layanan-empty">
                                            Belum ada layanan
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            <td>
                                {{ substr($j->waktu_mulai, 0, 5) }}
                                -
                                {{ substr($j->waktu_selesai, 0, 5) }} WIB
                            </td>

                            <td>
                                {{ $j->kuota ?? 'Tanpa batas' }}
                            </td>

                            <td>
                                {{ $j->terpakai }}
                            </td>

                            <td>
                                {{ $sisa ?? '-' }}
                            </td>

                            <td>
                                @if ($bisa)
                                    <span class="jadwal-badge jadwal-tersedia">
                                        Tersedia
                                    </span>
                                @elseif ($j->status === 'tersedia')
                                    <span class="jadwal-badge jadwal-penuh">
                                        Penuh
                                    </span>
                                @else
                                    <span class="jadwal-badge jadwal-nonaktif">
                                        {{ ucfirst($j->status) }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px;">
            {{ $jadwal->links() }}
        </div>

        <p class="jadwal-info">
            <i class="fa-solid fa-circle-info"></i>
            Halaman ini digunakan Kader untuk memantau jadwal praktik dokter.
            Pengelolaan jadwal dilakukan oleh dokter masing-masing.
        </p>

    @endif

</section>

@endsection
