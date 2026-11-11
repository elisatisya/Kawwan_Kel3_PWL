@extends('kader.layout')

@section('title', 'Jadwal Pelayanan')

@section('content')

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

