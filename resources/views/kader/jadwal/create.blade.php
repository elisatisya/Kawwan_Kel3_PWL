
@extends('kader.layout')

@section('title', 'Tambah Jadwal')

@section('content')

<style>
    .jadwal-form {
        max-width:750px;
        padding:25px;
        background:#fff;
        border:1px solid #f1ddcf;
        border-radius:14px;
    }

    .jadwal-form h1 {
        font-size:23px;
        font-weight:800;
        margin:0 0 7px;
    }

    .jadwal-form h1 span { color:#ff6900; }

    .jadwal-form p {
        color:#777;
        font-size:13px;
        margin-bottom:25px;
    }

    .form-group { margin-bottom:18px; }

    .form-group label {
        display:block;
        margin-bottom:8px;
        font-size:13px;
        font-weight:700;
    }

    .form-group input,
    .form-group select {
        width:100%;
        padding:11px 13px;
        border:1px solid #ead8cc;
        border-radius:9px;
        font-family:inherit;
        font-size:13px;
        box-sizing:border-box;
    }

    .form-error {
        color:#c0392b;
        font-size:12px;
        margin-top:5px;
    }

    .form-actions {
        display:flex;
        gap:10px;
        margin-top:25px;
    }

    .form-btn {
        padding:11px 17px;
        border-radius:9px;
        font-size:13px;
        font-weight:700;
        cursor:pointer;
        text-decoration:none;
        border:1px solid #ddd;
    }

    .form-primary {
        background:#ff6900;
        color:#fff;
        border-color:#ff6900;
    }

    .form-secondary {
        background:#fff;
        color:#444;
    }
</style>

<div class="jadwal-form">

    <h1>Tambah <span>Jadwal Pelayanan</span></h1>
    <p>Tentukan dokter, tanggal, waktu, dan kuota pelayanan.</p>

    @if ($dokter->isEmpty())
        <div class="form-error">
            Belum ada akun dokter yang terdaftar.
        </div>
    @endif

    <form action="{{ route('kader.jadwal.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Dokter yang Bertugas *</label>

            <select name="dokter_id" required>
                <option value="">Pilih dokter</option>

                @foreach ($dokter as $d)
                    <option value="{{ $d->id }}"
                        @selected(old('dokter_id') == $d->id)>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>

            @error('dokter_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Tanggal Pelayanan *</label>
            <input type="date" name="tanggal"
                   min="{{ now()->toDateString() }}"
                   value="{{ old('tanggal') }}" required>

            @error('tanggal')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Waktu Mulai *</label>
            <input type="time" name="waktu_mulai"
                   value="{{ old('waktu_mulai') }}" required>

            @error('waktu_mulai')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Waktu Selesai *</label>
            <input type="time" name="waktu_selesai"
                   value="{{ old('waktu_selesai') }}" required>

            @error('waktu_selesai')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Kuota Pelayanan</label>
            <input type="number" name="kuota" min="1"
                   value="{{ old('kuota') }}"
                   placeholder="Kosongkan jika tanpa batas">

            @error('kuota')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="form-btn form-primary"
                    {{ $dokter->isEmpty() ? 'disabled' : '' }}>
                <i class="fa-solid fa-save"></i>
                Simpan Jadwal
            </button>

            <a href="{{ route('kader.jadwal.index') }}"
               class="form-btn form-secondary">
                Kembali
            </a>
        </div>
    </form>

</div>

@endsection
