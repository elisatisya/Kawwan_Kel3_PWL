@extends('kader.layout')

@section('title', 'Layanan & Harga')

@section('content')

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

