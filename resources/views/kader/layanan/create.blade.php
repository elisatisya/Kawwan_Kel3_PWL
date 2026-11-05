
@extends('kader.layout')

@section('title', 'Tambah Layanan')

@section('content')

<style>
    .form-layanan { max-width:750px; background:#fff; padding:25px; border:1px solid #f1ddcf; border-radius:14px; }
    .form-layanan h1 { font-size:23px; font-weight:800; margin-bottom:7px; }
    .form-layanan h1 span { color:#ff6900; }
    .form-layanan p { color:#777; font-size:13px; margin-bottom:25px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; margin-bottom:8px; font-size:13px; font-weight:700; }
    .form-group input, .form-group textarea { width:100%; padding:11px 13px; border:1px solid #ead8cc; border-radius:9px; font-family:inherit; font-size:13px; box-sizing:border-box; }
    .form-group input:focus, .form-group textarea:focus { outline:none; border-color:#ff6900; }
    .form-error { color:#c0392b; font-size:12px; margin-top:5px; }
    .form-actions { display:flex; gap:10px; margin-top:25px; }
    .form-btn { padding:11px 17px; border-radius:9px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; border:1px solid #ddd; }
    .form-primary { background:#ff6900; color:#fff; border-color:#ff6900; }
    .form-secondary { background:#fff; color:#444; }
</style>

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
