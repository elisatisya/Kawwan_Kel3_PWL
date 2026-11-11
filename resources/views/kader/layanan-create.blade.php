@extends('kader.layout')

@section('title', 'Tambah Layanan')

@section('content')

<div class="form-layanan">

    <h1>Tambah <span>Layanan</span></h1>
    <p>Masukkan informasi dan harga layanan baru.</p>

    <form action="{{ route('kader.layanan.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Nama Layanan *</label>
            <input type="text" name="nama" value="{{ old('nama') }}"
                   placeholder="Contoh: Pemeriksaan Umum" required>
            @error('nama') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      placeholder="Jelaskan layanan yang diberikan">{{ old('deskripsi') }}</textarea>
            @error('deskripsi') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Harga Layanan (Rp) *</label>
            <input type="number" name="biaya" min="0"
                   value="{{ old('biaya', 0) }}" required>
            <small style="color:#888;">Masukkan 0 jika layanan diberikan secara gratis.</small>
            @error('biaya') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label>Durasi Pelayanan (menit)</label>
            <input type="number" name="durasi_menit" min="1"
                   value="{{ old('durasi_menit') }}"
                   placeholder="Contoh: 30">
            @error('durasi_menit') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="form-btn form-primary">
                <i class="fa-solid fa-save"></i> Simpan Layanan
            </button>

            <a href="{{ route('kader.layanan.index') }}"
               class="form-btn form-secondary">Kembali</a>
        </div>
    </form>

</div>

@endsection

