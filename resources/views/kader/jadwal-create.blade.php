@extends('kader.layout')

@section('title', 'Tambah Jadwal')

@section('content')

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

