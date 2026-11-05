
@extends('kader.layout')

@section('title', 'Edit Layanan')

@section('content')

<style>
    .form-layanan {
        max-width: 750px;
        background: #fff;
        padding: 25px;
        border: 1px solid #f1ddcf;
        border-radius: 14px;
    }

    .form-layanan h1 {
        font-size: 23px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .form-layanan h1 span {
        color: #ff6900;
    }

    .form-layanan p {
        color: #777;
        font-size: 13px;
        margin-bottom: 25px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 11px 13px;
        border: 1px solid #ead8cc;
        border-radius: 9px;
        font-family: inherit;
        font-size: 13px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #ff6900;
    }

    .dokter-list {
        border: 1px solid #ead8cc;
        border-radius: 9px;
        padding: 13px;
    }

    .dokter-option {
        display: flex !important;
        align-items: center;
        gap: 9px;
        padding: 8px 0;
        font-size: 13px !important;
        font-weight: 400 !important;
    }

    .dokter-option input {
        width: auto;
        accent-color: #ff6900;
    }

    .form-error {
        color: #c0392b;
        font-size: 12px;
        margin-top: 5px;
    }

    .form-hint {
        color: #888;
        font-size: 12px;
        margin-top: 7px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
    }

    .form-btn {
        padding: 11px 17px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        border: 1px solid #ddd;
    }

    .form-primary {
        background: #ff6900;
        color: #fff;
        border-color: #ff6900;
    }

    .form-secondary {
        background: #fff;
        color: #444;
    }
</style>

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
