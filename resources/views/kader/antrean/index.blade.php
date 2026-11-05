
@extends('kader.layout')

@section('title', 'Daftar Antrean')

@section('content')

<style>
    .antrean-page {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .antrean-heading h1 {
        margin: 0 0 7px;
        font-size: 25px;
        font-weight: 800;
        color: #171717;
    }

    .antrean-heading h1 span {
        color: #ff6900;
    }

    .antrean-heading p {
        margin: 0;
        color: #777;
        font-size: 14px;
    }

    .antrean-panel {
        background: #fff;
        border: 1px solid #f1ddcf;
        border-radius: 14px;
        padding: 22px;
    }

    .antrean-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 12px;
    }

    .antrean-summary h2 {
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .antrean-count {
        padding: 8px 13px;
        background: #fff0e4;
        color: #e95f00;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .antrean-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .antrean-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .antrean-table th {
        padding: 14px 13px;
        background: #f8f1ec;
        color: #776d66;
        font-size: 11px;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .antrean-table td {
        padding: 15px 13px;
        border-bottom: 1px solid #f0e9e4;
        font-size: 13px;
        color: #292929;
        vertical-align: middle;
    }

    .antrean-table tbody tr:hover {
        background: #fffaf6;
    }

    .antrean-number {
        display: inline-block;
        padding: 7px 12px;
        background: #fff0e4;
        color: #e95f00;
        border-radius: 8px;
        font-weight: 800;
    }

    .antrean-status {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .antrean-menunggu {
        background: #fff2cf;
        color: #a66a00;
    }

    .antrean-dipanggil {
        background: #e2efff;
        color: #2866ad;
    }

    .antrean-sedang {
        background: #e2efff;
        color: #2866ad;
    }

    .antrean-selesai {
        background: #dcf5e7;
        color: #16834c;
    }

    .antrean-tidak-hadir {
        background: #fde5e5;
        color: #b33b3b;
    }

    .antrean-empty {
        padding: 45px 15px;
        text-align: center;
        color: #888;
        font-size: 13px;
    }

    .antrean-empty i {
        display: block;
        font-size: 30px;
        color: #e6b28d;
        margin-bottom: 12px;
    }
</style>

<div class="antrean-page">

    <div class="antrean-heading">
        <h1>Daftar <span>Antrean</span></h1>
        <p>Kelola dan pantau antrean pelayanan hewan.</p>
    </div>

    <section class="antrean-panel">

        <div class="antrean-summary">
            <h2>Data Antrean</h2>

            <span class="antrean-count">
                Total: {{ $antrean->count() }} Antrean
            </span>
        </div>

        <div class="antrean-table-wrap">
            <table class="antrean-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Antrean</th>
                        <th>Tanggal</th>
                        <th>Pemilik</th>
                        <th>Hewan</th>
                        <th>Layanan</th>
                        <th>Dokter</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($antrean as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <span class="antrean-number">
                                    {{ $item->nomor_antrean }}
                                </span>
                            </td>

                            <td>
                                {{ $item->tanggal?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $item->pemesanan?->user?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $item->pemesanan?->hewan?->nama ?? '-' }}
                            </td>

                            <td>
                                @forelse ($item->pemesanan?->pemesananLayanan ?? [] as $detail)
                                    <div>{{ $detail->layanan?->nama ?? '-' }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td>
                                {{ $item->dokter?->name ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match ($item->status) {
                                        'menunggu' => 'antrean-menunggu',
                                        'dipanggil' => 'antrean-dipanggil',
                                        'sedang_dilayani' => 'antrean-sedang',
                                        'selesai' => 'antrean-selesai',
                                        'tidak_hadir' => 'antrean-tidak-hadir',
                                        default => '',
                                    };
                                @endphp

                                <span class="antrean-status {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="antrean-empty">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                    Belum ada antrean yang diterbitkan.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>
</div>

@endsection
