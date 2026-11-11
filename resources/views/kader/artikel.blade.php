@extends('kader.layout')

@section('title', 'Artikel')

@section('content')
<div class="dokter-head">
    <div>
        <h1>Artikel <span>Kesehatan Hewan</span></h1>
        <p>Kelola artikel kesehatan hewan yang kamu buat.</p>
    </div>

    <a href="{{ route('kader.artikel.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Artikel
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="dokter-card">
    <h2>Daftar Artikel Saya</h2>

    @if ($artikel->isEmpty())
        <div class="dokter-empty">
            <i class="fa-solid fa-newspaper"></i>
            <p>Belum ada artikel yang dibuat.</p>
            <a href="{{ route('kader.artikel.create') }}">
                Mulai buat artikel
            </a>
        </div>
    @else
        <div class="dokter-table-wrap">
            <table class="dokter-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Judul Artikel</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($artikel as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($artikel->currentPage() - 1) * $artikel->perPage() }}</td>

                        <td>{{ $item->judul }}</td>

                        <td>{{ $item->kategori }}</td>

                        <td>
                            <span class="dokter-badge is-{{ $item->status }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>

                        <td>{{ $item->created_at->format('d/m/Y') }}</td>

                        <td>
                            <div class="kader-aksi">
                                <a href="{{ route('kader.artikel.edit', $item->id) }}"
                                   class="btn btn-outline">
                                    Edit
                                </a>

                                @if ($item->status === 'draft')
                                    <form action="{{ route('kader.artikel.ajukan', $item->id) }}"
                                          method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary">
                                            Ajukan
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('kader.artikel.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $artikel->links() }}
        </div>
    @endif
</div>
@endsection

