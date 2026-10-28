@extends('dokter.layout')

@section('title', 'Dashboard Dokter Hewan')

@section('content')

    <div class="dokter-head">
        <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
        <p>Ringkasan pelayanan pemeriksaan hewan hari ini.</p>
    </div>

    <div class="dokter-stats">
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-paw"></i></div>
            <div>
                <p class="dokter-stat-label">Pasien Hari Ini</p>
                <p class="dokter-stat-value" data-count="{{ $stat['pasien_hari_ini'] }}">{{ $stat['pasien_hari_ini'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <div>
                <p class="dokter-stat-label">Menunggu Pemeriksaan</p>
                <p class="dokter-stat-value" data-count="{{ $stat['menunggu_pemeriksaan'] }}">{{ $stat['menunggu_pemeriksaan'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div>
                <p class="dokter-stat-label">Selesai Diperiksa</p>
                <p class="dokter-stat-value" data-count="{{ $stat['selesai_diperiksa'] }}">{{ $stat['selesai_diperiksa'] }}</p>
            </div>
        </div>
        <div class="dokter-stat">
            <div class="dokter-stat-icon"><i class="fa-solid fa-file-medical"></i></div>
            <div>
                <p class="dokter-stat-label">Total Pemeriksaan</p>
                <p class="dokter-stat-value" data-count="{{ $stat['total_pemeriksaan'] }}">{{ $stat['total_pemeriksaan'] }}</p>
            </div>
        </div>
    </div>

    <div class="dokter-charts">
        <section class="dokter-card">
            <h3>Pemeriksaan 7 Hari Terakhir</h3>
            <p class="dokter-card-sub">Jumlah pemeriksaan yang dicatat per hari.</p>
            <div class="dokter-chart-box">
                <canvas id="chartHarian"></canvas>
            </div>
        </section>

        <section class="dokter-card">
            <h3>Hewan Diperiksa</h3>
            <p class="dokter-card-sub">Perbandingan berdasarkan jenis dan ras hewan.</p>
            @if (count($grafik['hewan']['data']) > 0)
                <div class="dokter-chart-box">
                    <canvas id="chartHewan"></canvas>
                </div>
            @else
                <p class="dokter-empty">Belum ada data pemeriksaan.</p>
            @endif
        </section>
    </div>

    <section class="dokter-card">
        <h3>Antrean Hari Ini</h3>
        <p class="dokter-card-sub">Daftar pemesanan pada jadwal pelayanan hari ini.</p>

        @if ($antrean->isEmpty())
            <p class="dokter-empty">Belum ada antrean untuk hari ini.</p>
        @else
            <div class="dokter-table-wrap">
                <table class="dokter-table">
                    <thead>
                        <tr>
                            <th>Jam</th>
                            <th>Pemilik</th>
                            <th>Hewan</th>
                            <th>Jenis</th>
                            <th>Ras</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antrean as $item)
                            <tr>
                                <td>{{ $item['jam'] }}</td>
                                <td>{{ $item['pemilik'] }}</td>
                                <td>{{ $item['hewan'] }}</td>
                                <td>{{ $item['jenis'] }}</td>
                                <td>{{ $item['ras'] }}</td>
                                <td><span class="dokter-badge is-{{ $item['status_kode'] }}">{{ $item['status_label'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <script id="dokterChartData" type="application/json">@json($grafik)</script>

@endsection