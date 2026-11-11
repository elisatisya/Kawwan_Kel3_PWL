@extends('kader.layout')

@section('title', 'Daftar Antrean')

@section('content')

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

