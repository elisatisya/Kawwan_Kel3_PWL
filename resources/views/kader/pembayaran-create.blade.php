@extends('kader.layout')

@section('title', 'Catat Pembayaran')

@section('content')

    <div class="dokter-head">
        <h1>Catat <span>Pembayaran</span></h1>
        <p>Isi pembayaran untuk pemesanan berikut.</p>
    </div>

    @if (session('success'))
        <div class="dokter-alert is-success"><p>{{ session('success') }}</p></div>
    @endif

    <section class="dokter-card">
        <h3>Detail Pemesanan</h3>
        <p class="dokter-card-sub">
            {{ $pemesanan->user?->name ?? '-' }} · {{ $pemesanan->hewan?->nama ?? '-' }} ({{ $pemesanan->hewan?->jenis ?? '-' }})<br>
            {{ $pemesanan->jadwalPelayanan->tanggal->format('d M Y') }},
            {{ substr($pemesanan->jadwalPelayanan->waktu_mulai, 0, 5) }} - {{ substr($pemesanan->jadwalPelayanan->waktu_selesai, 0, 5) }} WIB
        </p>

        <form method="POST" action="{{ route('kader.pembayaran.store', $pemesanan) }}" class="kader-form">
            @csrf

            <div class="form-row-2">
                <div>
                    <label class="form-label" for="jumlah">Jumlah (Rp)</label>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="0" step="1" inputmode="numeric" class="form-control" required>
                    @error('jumlah')<p class="kader-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label" for="metode">Metode</label>
                    <select id="metode" name="metode" class="form-control" required>
                        @foreach ($metode as $kode => $label)
                            <option value="{{ $kode }}" @selected(old('metode', 'tunai') === $kode)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('metode')<p class="kader-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label" for="catatan">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="3" maxlength="500" class="form-control">{{ old('catatan') }}</textarea>
                @error('catatan')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div class="kader-aksi">
                <button type="submit" class="btn btn-primary">Simpan pembayaran</button>
                <a href="{{ route('kader.pemesanan.index') }}" class="btn btn-outline">Nanti saja</a>
            </div>
        </form>
    </section>

@endsection

