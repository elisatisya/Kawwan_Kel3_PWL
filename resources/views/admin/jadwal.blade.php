<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelayanan — KAWWAN Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>
    <div class="kawwan-toast-container" id="kawwanToastContainer"></div>

    <div class="admin-shell">
        @include('admin.partials.sidebar')

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>Jadwal Pelayanan</h2>
                </div>

                <div class="admin-user">
                    <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <span class="admin-user-name">{{ auth()->user()->name }}</span>
                        <span class="admin-user-role">Administrator</span>
                    </div>
                </div>
            </header>

            <main class="admin-content">
                <div class="admin-head">
                    <div class="admin-head-info">
                        <h1>Jadwal <span>Pelayanan Puskeswan</span></h1>
                        <p>Kelola jadwal praktik dokter hewan, kuota pelayanan, dan ketersediaan sesi kunjungan.</p>
                    </div>
                    <div class="admin-head-actions">
                        <button type="button" class="btn btn-primary" onclick="document.getElementById('modalTambahJadwal').classList.add('is-open')">
                            <i class="fa-solid fa-plus"></i> Tambah Jadwal
                        </button>
                    </div>
                </div>

                @if (session('success'))
                    <div class="pemilik-alert is-success" style="margin-bottom: 16px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="pemilik-alert is-error" style="margin-bottom: 16px;">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="pemilik-alert is-error" style="margin-bottom: 16px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="admin-stats">
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Total Jadwal</span>
                            <span class="admin-stat-value">{{ $stat['total'] }}</span>
                            <span class="admin-stat-sub">Seluruh sesi tercatat</span>
                        </div>
                    </div>
                    <div class="admin-stat-card is-highlight">
                        <div class="admin-stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Tersedia</span>
                            <span class="admin-stat-value text-orange">{{ $stat['tersedia'] }}</span>
                            <span class="admin-stat-sub">Dapat dipesan pemilik</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-solid fa-users-slash"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Penuh</span>
                            <span class="admin-stat-value">{{ $stat['penuh'] }}</span>
                            <span class="admin-stat-sub">Kuota telah terpenuhi</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-regular fa-clock"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Jadwal Mendatang</span>
                            <span class="admin-stat-value">{{ $stat['mendatang'] }}</span>
                            <span class="admin-stat-sub">Mulai hari ini & ke depan</span>
                        </div>
                    </div>
                </div>

                <section class="admin-card">
                    <form method="GET" action="{{ route('admin.jadwal.index') }}" class="admin-search-toolbar">
                        <div class="toolbar-search-input">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama dokter atau tanggal (YYYY-MM-DD)..." class="form-control">
                        </div>
                        <div class="toolbar-filter-select">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="tersedia" {{ $statusFilter === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="penuh" {{ $statusFilter === 'penuh' ? 'selected' : '' }}>Penuh</option>
                                <option value="tutup" {{ $statusFilter === 'tutup' ? 'selected' : '' }}>Tutup</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        @if ($search !== '' || $statusFilter !== 'semua')
                            <a href="{{ route('admin.jadwal.index') }}" class="btn btn-outline btn-sm">Reset</a>
                        @endif
                    </form>

                    @if ($jadwalList->isEmpty())
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                            <h4>Belum Ada Jadwal Pelayanan</h4>
                            <p>Tambahkan jadwal praktik dokter hewan agar pemilik hewan dapat memesan antrean.</p>
                        </div>
                    @else
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam Praktik</th>
                                        <th>Dokter Bertugas</th>
                                        <th>Kuota</th>
                                        <th>Pemesanan</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwalList as $j)
                                        <tr>
                                            <td><strong>{{ $j->tanggal->translatedFormat('d M Y') }}</strong></td>
                                            <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                            <td>{{ $j->dokter?->name ?? 'Dokter Puskeswan' }}</td>
                                            <td>{{ $j->kuota ?? 'Tanpa batas' }}</td>
                                            <td>{{ $j->pemesanan_count }} pemesanan</td>
                                            <td>
                                                <form action="{{ route('admin.jadwal.status', $j) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-control admin-select-sm" onchange="this.form.submit()">
                                                        <option value="tersedia" {{ $j->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                                        <option value="penuh" {{ $j->status === 'penuh' ? 'selected' : '' }}>Penuh</option>
                                                        <option value="tutup" {{ $j->status === 'tutup' ? 'selected' : '' }}>Tutup</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <div class="admin-actions-cell" style="justify-content: flex-end;">
                                                    <form action="{{ route('admin.jadwal.destroy', $j) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action is-delete">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="kader-pagination-wrap">
                            {{ $jadwalList->links() }}
                        </div>
                    @endif
                </section>
            </main>
        </div>
    </div>

    {{-- Modal Tambah Jadwal --}}
    <div id="modalTambahJadwal" class="pemilik-modal {{ $errors->any() ? 'is-open' : '' }}" role="dialog" aria-modal="true">
        <div class="pemilik-modal-overlay" onclick="document.getElementById('modalTambahJadwal').classList.remove('is-open')"></div>
        <div class="pemilik-modal-dialog">
            <div class="pemilik-modal-head">
                <div>
                    <h2>Tambah Jadwal Pelayanan</h2>
                    <p>Tentukan dokter hewan, tanggal, jam praktik, dan kuota antrean.</p>
                </div>
                <button type="button" class="pemilik-modal-close" onclick="document.getElementById('modalTambahJadwal').classList.remove('is-open')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.jadwal.store') }}" class="pemilik-simple-form">
                @csrf
                <div class="pemilik-form-group">
                    <label for="dokter_id">Dokter Hewan *</label>
                    <select id="dokter_id" name="dokter_id" required>
                        <option value="">Pilih dokter hewan</option>
                        @foreach ($dokterList as $dok)
                            <option value="{{ $dok->id }}" @selected(old('dokter_id') == $dok->id)>{{ $dok->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pemilik-form-group">
                    <label for="tanggal">Tanggal Praktik *</label>
                    <input type="date" id="tanggal" name="tanggal" min="{{ now()->toDateString() }}" value="{{ old('tanggal', now()->toDateString()) }}" required>
                </div>
                <div class="pemilik-form-grid-2">
                    <div class="pemilik-form-group">
                        <label for="waktu_mulai">Waktu Mulai *</label>
                        <input type="time" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', '08:00') }}" required>
                    </div>
                    <div class="pemilik-form-group">
                        <label for="waktu_selesai">Waktu Selesai *</label>
                        <input type="time" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', '12:00') }}" required>
                    </div>
                </div>
                <div class="pemilik-form-group">
                    <label for="kuota">Kuota Maksimal (Opsional)</label>
                    <input type="number" id="kuota" name="kuota" min="1" max="200" value="{{ old('kuota', 15) }}" placeholder="Kosongkan jika tanpa batas">
                </div>
                <div class="pemilik-form-actions" style="justify-content: flex-end; margin-top: 6px;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('modalTambahJadwal').classList.remove('is-open')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
