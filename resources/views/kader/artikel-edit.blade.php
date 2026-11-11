@extends('kader.layout')

@section('title', 'Edit Artikel')

@section('content')
<div class="dokter-head">
    <div>
        <h1>Edit <span>Artikel</span></h1>
        <p>Perbarui konten artikel kesehatan hewan Anda.</p>
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
    <form action="{{ route('kader.artikel.update', $artikel) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label" for="judul">Judul Artikel <span class="text-danger">*</span></label>
            <input type="text" name="judul" id="judul" class="form-control"
                   value="{{ old('judul', $artikel->judul) }}" placeholder="Masukkan judul artikel..." required>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="kategori">Kategori <span class="text-danger">*</span></label>
            <input type="text" name="kategori" id="kategori" class="form-control"
                   value="{{ old('kategori', $artikel->kategori) }}" placeholder="Contoh: Nutrisi, Vaksinasi, Penyakit..." required>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="ringkasan">Ringkasan</label>
            <textarea name="ringkasan" id="ringkasan" rows="3" class="form-control"
                      placeholder="Tulis ringkasan singkat artikel...">{{ old('ringkasan', $artikel->ringkasan) }}</textarea>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="isi">Isi Artikel <span class="text-danger">*</span></label>
            <textarea name="isi" id="isi" rows="10" class="form-control"
                      placeholder="Tulis isi artikel selengkapnya di sini..." required>{{ old('isi', $artikel->isi) }}</textarea>
        </div>

        <div class="form-group" style="margin-top: 14px;">
            <label class="form-label" for="gambar">Ganti Gambar Artikel (Opsional)</label>

            @if ($artikel->gambar)
                <div style="margin-bottom: 10px;">
                    <img src="{{ asset('storage/' . $artikel->gambar) }}" alt="Gambar Artikel"
                         style="max-width: 240px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <small class="text-muted d-block" style="margin-top: 4px;">Gambar saat ini. Upload baru untuk mengganti.</small>
                </div>
            @endif

            <input type="file" name="gambar" id="gambar" class="form-control"
                   accept="image/jpg,image/jpeg,image/png,image/webp">
            <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks: 2MB.</small>
        </div>

        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('kader.artikel.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>
@endsection

