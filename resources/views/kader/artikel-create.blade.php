@extends('kader.layout')

@section('title', 'Tambah Artikel')

@section('content')
<div class="dokter-head">
    <div>
        <h1>Tambah <span>Artikel</span></h1>
        <p>Buat artikel kesehatan hewan baru untuk dipublikasikan.</p>
    </div>
    <a href="{{ route('kader.artikel.index') }}" class="btn btn-outline">
        &larr; Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin: 0; padding-left: 18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="dokter-card">
    <form action="{{ route('kader.artikel.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label" for="judul">Judul Artikel <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="judul" class="form-control"
                   value="{{ old('judul') }}" placeholder="Masukkan judul artikel..." required>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="kategori">Kategori <span class="text-danger">*</span></label>
            <input type="text" name="kategori" id="kategori" class="form-control"
                   value="{{ old('kategori') }}" placeholder="Contoh: Nutrisi, Vaksinasi, Penyakit..." required>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="ringkasan">Ringkasan</label>
            <textarea name="ringkasan" id="ringkasan" rows="3" class="form-control"
                      placeholder="Tulis ringkasan singkat artikel...">{{ old('ringkasan') }}</textarea>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="isi">Isi Artikel <span class="text-danger">*</span></label>
            <textarea name="isi" id="isi" rows="10" class="form-control"
                      placeholder="Tulis isi artikel selengkapnya di sini..." required>{{ old('isi') }}</textarea>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="gambar">Gambar Artikel (Opsional)</label>
            <input type="file" name="gambar" id="gambar" class="form-control"
                   accept="image/jpg,image/jpeg,image/png,image/webp">
            <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks: 2MB.</small>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Simpan sebagai Draft</button>
            <a href="{{ route('kader.artikel.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection

