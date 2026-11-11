@extends('kader.layout')

@section('title', 'Edit Layanan')

@section('content')

<div class="form-layanan">

    <h1>Edit <span>Layanan</span></h1>
    <p>Perbarui informasi, harga, dan dokter yang menangani layanan.</p>

    <form action="{{ route('kader.layanan.update', $layanan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama Layanan *</label>
            <input
                type="text"
                name="nama"
                value="{{ old('nama', $layanan->nama) }}"
                required
            >
            @error('nama')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
            @error('deskripsi')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Harga Layanan (Rp) *</label>
            <input
                type="number"
                name="biaya"
                min="0"
                value="{{ old('biaya', $layanan->biaya) }}"
                required
            >
            <div class="form-hint">Masukkan 0 jika layanan gratis.</div>
            @error('biaya')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Durasi Pelayanan (menit)</label>
            <input
                type="number"
                name="durasi_menit"
                min="1"
                value="{{ old('durasi_menit', $layanan->durasi_menit) }}"
            >
            @error('durasi_menit')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Dokter yang Menangani *</label>

            @php
                $dokterTerpilih = old(
                    'dokter_ids',
                    $layanan->dokter->pluck('id')->toArray()
                );
            @endphp

            <div class="dokter-list">
                @forelse ($dokter as $item)
                    <label class="dokter-option">
                        <input
                            type="checkbox"
                            name="dokter_ids[]"
                            value="{{ $item->id }}"
                            {{ in_array($item->id, $dokterTerpilih) ? 'checked' : '' }}
                        >
                        <span>{{ $item->name }}</span>
                    </label>
                @empty
                    <p style="color:#c0392b; margin:0;">
                        Belum ada akun dokter yang terdaftar.
                    </p>
                @endforelse
            </div>

            <div class="form-hint">
                Dokter yang sudah terpilih dapat diubah sesuai kebutuhan.
            </div>

            @error('dokter_ids')
                <div class="form-error">{{ $message }}</div>
            @enderror

            @error('dokter_ids.*')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button
                type="submit"
                class="form-btn form-primary"
                {{ $dokter->isEmpty() ? 'disabled' : '' }}
            >
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>

            <a
                href="{{ route('kader.layanan.index') }}"
                class="form-btn form-secondary"
            >
                Kembali
            </a>
        </div>

    </form>

</div>

@endsection

