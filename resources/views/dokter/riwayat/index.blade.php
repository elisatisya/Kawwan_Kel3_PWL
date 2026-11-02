@extends('dokter.layout')

@section('title', 'Riwayat Pemeriksaan')

@section('content')

   <div class="riwayat-head">
    <h2 class="riwayat-judul">Daftar Pasien Hewan</h2>
    <p class="riwayat-sub">Pilih hewan untuk melihat catatan pemeriksaannya.</p>
</div>

    <section class="dokter-card">
        <form method="GET" action="{{ route('dokter.riwayat.index') }}" class="riwayat-search">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama hewan atau pemilik...">
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        @if ($pasien->isEmpty())
            <p class="dokter-empty">Belum ada riwayat pemeriksaan.</p>
        @else
            <div class="dokter-table-wrap">
                <table class="riwayat-table">
                    <thead>
                        <tr>
                            <th>Hewan</th>
                            <th>Jenis / Ras</th>
                            <th>Pemilik</th>
                            <th>Total</th>
                            <th>Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pasien as $h)
                            @php
                                $catatan = $h->pemesanan->pluck('pemeriksaan')->filter();
                                $terakhir = $catatan->sortByDesc('created_at')->first()?->created_at;
                            @endphp
                            <tr>
                                <td>{{ $h->nama }}</td>
                                <td>{{ trim($h->jenis.' '.$h->ras) }}</td>
                                <td>{{ $h->pemilik?->name ?? '-' }}</td>
                                <td>{{ $catatan->count() }}</td>
                                <td>{{ $terakhir ? $terakhir->format('d M Y') : '-' }}</td>
                                <td><a href="{{ route('dokter.riwayat.show', $h) }}" class="riwayat-link">Lihat catatan</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $pasien->links() }}
        @endif
    </section>

@endsection