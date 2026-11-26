<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Data Hewan' }} — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        @include('pemilik.partials.navbar')

        <div class="pemilik-main-wrap">
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>Data Hewan</h2>
                </div>

                <a href="{{ route('pemilik.profil') }}" class="pemilik-user-profile" title="Lihat Profil">
                    <div class="pemilik-avatar">
                        @if (auth()->user()->foto)
                            <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </a>
            </header>

            <main class="pemilik-main">
<div class="pemilik-container">

    <div class="pemilik-page-head">
        <div>
            <h1>Data <span>Hewan Peliharaan</span></h1>
            <p>Tambahkan dan kelola identitas hewan peliharaan Anda untuk keperluan pendaftaran pelayanan.</p>
        </div>
        <button type="button" class="pemilik-btn pemilik-btn-primary" onclick="openHewanModal()">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Hewan</span>
        </button>
    </div>

    @if (session('success'))
        <div class="pemilik-alert is-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="pemilik-alert is-error">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Tabel Daftar Hewan (Full Width) --}}
    <section class="pemilik-card">
        <div class="pemilik-card-header">
            <div>
                <h2>Daftar Hewan Saya</h2>
                <p>Total: {{ $daftarHewan->count() }} hewan terdaftar</p>
            </div>
        </div>

        @if ($daftarHewan->isEmpty())
            <div class="pemilik-empty">
                <div class="pemilik-empty-icon"><i class="fa-solid fa-paw"></i></div>
                <h3>Belum Ada Data Hewan</h3>
                <p>Klik tombol <strong>Tambah Hewan</strong> di kanan atas untuk menambahkan hewan peliharaan pertama Anda.</p>
                <button type="button" class="pemilik-btn pemilik-btn-primary" onclick="openHewanModal()">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Hewan</span>
                </button>
            </div>
        @else
            <div class="pemilik-table-wrap">
                <table class="pemilik-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis Hewan</th>
                            <th>Ras</th>
                            <th>Kelamin</th>
                            <th>Umur</th>
                            <th>Riwayat Booking</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarHewan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ ucfirst($item->jenis) }}</strong></td>
                                <td>{{ $item->ras ?: '-' }}</td>
                                <td>{{ ucfirst($item->jenis_kelamin) }}</td>
                                <td>{{ $item->umur ?: '-' }}</td>
                                <td>{{ $item->pemesanan_count }} kali</td>
                                <td>
                                    <div class="pemilik-table-actions">
                                        <a href="{{ route('pemilik.hewan.edit', $item) }}"
                                           class="btn-icon-action is-edit"
                                           title="Edit Data Hewan"
                                           aria-label="Edit Data Hewan">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form method="POST"
                                              action="{{ route('pemilik.hewan.destroy', $item) }}"
                                              data-confirm="Yakin ingin menghapus data hewan {{ $item->jenis }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn-icon-action is-delete"
                                                    title="Hapus Data Hewan"
                                                    aria-label="Hapus Data Hewan">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Modal Popup Tambah / Edit Hewan --}}
    <div id="modalHewan"
         class="pemilik-modal {{ ($hewanEdit || $errors->any()) ? 'is-open' : '' }}"
         role="dialog"
         aria-modal="true"
         aria-hidden="{{ ($hewanEdit || $errors->any()) ? 'false' : 'true' }}"
         data-edit-redirect="{{ $hewanEdit ? route('pemilik.hewan.index') : '' }}">
        <div class="pemilik-modal-overlay" onclick="closeHewanModal()"></div>
        <div class="pemilik-modal-dialog">
            <div class="pemilik-modal-head">
                <div>
                    <h2>{{ $hewanEdit ? 'Edit Data Hewan' : 'Tambah Hewan Baru' }}</h2>
                    <p>{{ $hewanEdit ? 'Perbarui informasi hewan peliharaan yang dipilih.' : 'Lengkapi data dasar hewan peliharaan Anda.' }}</p>
                </div>
                <button type="button" class="pemilik-modal-close" onclick="closeHewanModal()" aria-label="Tutup">&times;</button>
            </div>

            <form method="POST"
                  action="{{ $hewanEdit ? route('pemilik.hewan.update', $hewanEdit) : route('pemilik.hewan.store') }}"
                  class="pemilik-simple-form">
                @csrf
                @if ($hewanEdit)
                    @method('PUT')
                @endif

                @php
                    $jenisStandar = ['kucing', 'anjing', 'kelinci', 'burung'];
                    $jenisVal = old('jenis', $hewanEdit?->jenis ?? '');
                    $isLainnya = $jenisVal !== '' && ! in_array(strtolower($jenisVal), $jenisStandar, true);
                    $jenisLainnyaVal = old('jenis_lainnya', ($isLainnya && strtolower($jenisVal) !== 'lainnya') ? $jenisVal : '');
                @endphp

                <div class="pemilik-form-group">
                    <label for="jenis">Jenis Hewan *</label>
                    <select id="jenis" name="jenis" required>
                        <option value="">Pilih jenis hewan</option>
                        <option value="Kucing" @selected(strtolower($jenisVal) === 'kucing')>Kucing</option>
                        <option value="Anjing" @selected(strtolower($jenisVal) === 'anjing')>Anjing</option>
                        <option value="Kelinci" @selected(strtolower($jenisVal) === 'kelinci')>Kelinci</option>
                        <option value="Burung" @selected(strtolower($jenisVal) === 'burung')>Burung</option>
                        <option value="Lainnya" @selected($isLainnya)>Lainnya</option>
                    </select>
                    @error('jenis')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group" id="jenisLainnyaGroup" @if (! $isLainnya) hidden @endif>
                    <label for="jenis_lainnya">Sebutkan Jenis Hewan *</label>
                    <input type="text"
                           id="jenis_lainnya"
                           name="jenis_lainnya"
                           value="{{ $jenisLainnyaVal }}"
                           maxlength="50"
                           placeholder="Contoh: Hamster, Kura-kura, Sugar Glider, Iguana"
                           @required($isLainnya)>
                    @error('jenis_lainnya')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="ras">Ras (Opsional)</label>
                    <input type="text"
                           id="ras"
                           name="ras"
                           value="{{ old('ras', $hewanEdit?->ras ?? '') }}"
                           placeholder="Contoh: Persia, Domestik, Golden Retriever">
                    @error('ras')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="jenis_kelamin">Jenis Kelamin *</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        @php
                            $kelaminVal = old('jenis_kelamin', $hewanEdit?->jenis_kelamin ?? '');
                        @endphp
                        <option value="">Pilih jenis kelamin</option>
                        <option value="jantan" @selected($kelaminVal === 'jantan')>Jantan</option>
                        <option value="betina" @selected($kelaminVal === 'betina')>Betina</option>
                    </select>
                    @error('jenis_kelamin')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-group">
                    <label for="tanggal_lahir">Tanggal Lahir (Opsional)</label>
                    <input type="date"
                           id="tanggal_lahir"
                           name="tanggal_lahir"
                           max="{{ now()->toDateString() }}"
                           value="{{ old('tanggal_lahir', $hewanEdit?->tanggal_lahir?->format('Y-m-d') ?? '') }}">
                    @error('tanggal_lahir')
                        <span class="pemilik-form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pemilik-form-actions" style="justify-content: flex-end; margin-top: 6px;">
                    @if ($hewanEdit)
                        <a href="{{ route('pemilik.hewan.index') }}" class="pemilik-btn pemilik-btn-outline">
                            Batal
                        </a>
                    @else
                        <button type="button" class="pemilik-btn pemilik-btn-outline" onclick="closeHewanModal()">
                            Batal
                        </button>
                    @endif
                    <button type="submit" class="pemilik-btn pemilik-btn-primary">
                        {{ $hewanEdit ? 'Simpan Perubahan' : 'Simpan Hewan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
            </main>
        </div>
    </div>

</body>
</html>
