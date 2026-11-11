@extends('kader.layout')

@section('title', 'Daftarkan Booking')

@section('content')

    <div class="dokter-head">
        <h1>Daftarkan <span>Booking</span></h1>
        <p>Pilih pemilik, hewan, dan jadwal pelayanan. Kuota dicek otomatis saat disimpan.</p>
    </div>

    <section class="dokter-card">
        <form method="POST" action="{{ route('kader.pemesanan.store') }}" class="kader-form">
            @csrf

            <div>
                <label class="form-label" for="kaderPemilik">Pemilik hewan</label>
                <select id="kaderPemilik" name="user_id" class="form-control" required>
                    <option value="">Pilih pemilik</option>
                    @foreach ($pemilik as $u)
                        <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>
                            {{ $u->name }}{{ $u->nomor_telepon ? ' – '.$u->nomor_telepon : '' }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="kaderHewan">Hewan</label>
                <select id="kaderHewan" name="hewan_id" class="form-control" data-selected="{{ old('hewan_id') }}" required disabled>
                    <option value="">Pilih pemilik dulu</option>
                </select>
                @error('hewan_id')<p class="kader-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label" for="kaderJadwal">Jadwal pelayanan</label>
                <select id="kaderJadwal" name="jadwal_pelayanan_id" class="form-control" required>
                    <option value="">Pilih jadwal</option>
                    @foreach ($jadwal as $j)
                        @php $sisa = $j->kuota === null ? 'tanpa batas' : ($j->kuota - $j->terpakai); @endphp
                        <option value="{{ $j->id }}" @selected((old('jadwal_pelayanan_id') ?? $jadwalDipilih) == $j->id)>
                            {{ $j->tanggal->format('d M Y') }} · {{ substr($j->waktu_mulai, 0, 5) }}-{{ substr($j->waktu_selesai, 0, 5) }} WIB · sisa kuota {{ $sisa }}
                        </option>
                    @endforeach
                </select>
                @error('jadwal_pelayanan_id')<p class="kader-error">{{ $message }}</p>@enderror
                @if ($jadwal->isEmpty())
                    <p class="kader-error">Belum ada jadwal yang tersedia. Jadwal dibuat oleh admin.</p>
                @endif
            </div>

            <div class="kader-aksi">
                <button type="submit" class="btn btn-primary">Simpan &amp; lanjut ke pembayaran</button>
                <a href="{{ route('kader.pemesanan.index') }}" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </section>

    <script id="kaderHewanData" type="application/json">@json($hewanPerPemilik)</script>

@endsection

