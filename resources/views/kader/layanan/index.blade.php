
@extends('kader.layout')

@section('title', 'Layanan & Harga')

@section('content')

<style>
    .layanan-page { display:flex; flex-direction:column; gap:20px; }
    .layanan-heading h1 { font-size:25px; font-weight:800; margin:0 0 7px; }
    .layanan-heading h1 span { color:#ff6900; }
    .layanan-heading p { color:#777; font-size:14px; margin:0; }
    .layanan-panel { background:#fff; border:1px solid #f1ddcf; border-radius:14px; padding:22px; }
    .layanan-toolbar { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
    .layanan-toolbar h2 { font-size:17px; font-weight:800; margin:0; }
    .layanan-btn { display:inline-flex; align-items:center; gap:7px; padding:10px 15px; border-radius:9px; border:1px solid #ff6900; background:#ff6900; color:#fff; text-decoration:none; font-size:13px; font-weight:700; cursor:pointer; }
    .layanan-btn:hover { background:#e95f00; color:#fff; }
    .layanan-table-wrap { overflow-x:auto; }
    .layanan-table { width:100%; min-width:900px; border-collapse:collapse; }
    .layanan-table th { padding:14px; background:#f8f1ec; color:#776d66; font-size:11px; text-align:left; text-transform:uppercase; }
    .layanan-table td { padding:15px 14px; border-bottom:1px solid #f0e9e4; font-size:13px; vertical-align:middle; }
    .layanan-badge { display:inline-block; padding:6px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .layanan-aktif { background:#dcf5e7; color:#16834c; }
    .layanan-nonaktif { background:#fde5e5; color:#b33b3b; }
    .layanan-free { color:#16834c; font-weight:700; }
    .layanan-actions { display:flex; gap:7px; flex-wrap:wrap; }
    .layanan-action { padding:7px 10px; border:1px solid #ead8cc; border-radius:7px; background:#fff; color:#444; font-size:11px; font-weight:700; text-decoration:none; cursor:pointer; }
    .layanan-action:hover { border-color:#ff6900; color:#e95f00; }
    .layanan-alert { padding:13px 16px; border-radius:9px; background:#e4f6eb; color:#187746; font-size:13px; }
    .layanan-empty { padding:40px; text-align:center; color:#888; }
    .dokter-tags { display:flex; flex-wrap:wrap; gap:5px; }
    .dokter-tag { display:inline-block; padding:5px 9px; background:#fff1e7; color:#bd570d; border-radius:6px; font-size:11px; font-weight:600; }
    .dokter-kosong { color:#999; font-size:12px; }
</style>

<div class="layanan-page">

    <div class="layanan-heading">
        <h1>Layanan <span>& Harga</span></h1>
        <p>Kelola jenis pelayanan, tarif, dan dokter yang menangani layanan kesehatan hewan.</p>
    </div>

    @if (session('success'))
        <div class="layanan-alert">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="layanan-alert" style="background:#fde9e9;color:#b33b3b;">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="layanan-panel">

        <div class="layanan-toolbar">
            <h2>Daftar Layanan</h2>

            <a href="{{ route('kader.layanan.create') }}" class="layanan-btn">
                <i class="fa-solid fa-plus"></i>
                Tambah Layanan
            </a>
        </div>

        <div class="layanan-table-wrap">
            <table class="layanan-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Layanan</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Dokter yang Menangani</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($layanan as $item)
                        <tr>
                            <td>{{ $layanan->firstItem() + $loop->index }}</td>

                            <td>
                                <strong>{{ $item->nama }}</strong>
                            </td>

                            <td>{{ $item->deskripsi ?: '-' }}</td>

                            <td>
                                @if ($item->biaya == 0)
                                    <span class="layanan-free">Gratis</span>
                                @else
                                    Rp {{ number_format($item->biaya, 0, ',', '.') }}
                                @endif
                            </td>

                            <td>
                                {{ $item->durasi_menit ? $item->durasi_menit . ' menit' : '-' }}
                            </td>

                            <td>
                                @if ($item->dokter->isNotEmpty())
                                    <div class="dokter-tags">
                                        @foreach ($item->dokter as $dokter)
                                            <span class="dokter-tag">
                                                {{ $dokter->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="dokter-kosong">
                                        Belum ditentukan
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($item->aktif)
                                    <span class="layanan-badge layanan-aktif">Aktif</span>
                                @else
                                    <span class="layanan-badge layanan-nonaktif">Nonaktif</span>
                                @endif
                            </td>

                            <td>
                                <div class="layanan-actions">
                                    <a href="{{ route('kader.layanan.edit', $item) }}"
                                       class="layanan-action">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>

                                    <form action="{{ route('kader.layanan.status', $item) }}"
                                          method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="layanan-action">
                                            {{ $item->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="layanan-empty">
                                Belum ada layanan yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top:18px;">
            {{ $layanan->links() }}
        </div>

    </div>
</div>

@endsection
