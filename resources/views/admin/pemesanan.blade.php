<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Antrean — KAWWAN Admin</title>

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
                    <h2>Pemesanan Antrean</h2>
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
                        <h1>Monitoring <span>Pemesanan Antrean</span></h1>
                        <p>Pantau seluruh reservasi antrean pemilik hewan, status layanan, dan kelancaran operasional.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="pemilik-alert is-success" style="margin-bottom: 16px;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="admin-stats">
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-solid fa-ticket"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Total Pemesanan</span>
                            <span class="admin-stat-value">{{ $stat['total'] }}</span>
                            <span class="admin-stat-sub">Seluruh reservasi</span>
                        </div>
                    </div>
                    <div class="admin-stat-card is-highlight">
                        <div class="admin-stat-icon"><i class="fa-solid fa-calendar-day"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Antrean Hari Ini</span>
                            <span class="admin-stat-value text-orange">{{ $stat['hari_ini'] }}</span>
                            <span class="admin-stat-sub">Jadwal tanggal hari ini</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Menunggu / Aktif</span>
                            <span class="admin-stat-value">{{ $stat['menunggu'] }}</span>
                            <span class="admin-stat-sub">Perlu pelayanan</span>
                        </div>
                    </div>
                    <div class="admin-stat-card">
                        <div class="admin-stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="admin-stat-body">
                            <span class="admin-stat-label">Selesai Dilayani</span>
                            <span class="admin-stat-value">{{ $stat['selesai'] }}</span>
                            <span class="admin-stat-sub">Pemeriksaan tuntas</span>
                        </div>
                    </div>
                </div>

                <section class="admin-card">
                    <form method="GET" action="{{ route('admin.pemesanan.index') }}" class="admin-search-toolbar">
                        <div class="toolbar-search-input">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pemilik, jenis hewan, atau nomor antrean..." class="form-control">
                        </div>
                        <div class="toolbar-filter-select">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="menunggu" {{ $statusFilter === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="dikonfirmasi" {{ $statusFilter === 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                <option value="sedang_dilayani" {{ $statusFilter === 'sedang_dilayani' ? 'selected' : '' }}>Sedang Dilayani</option>
                                <option value="selesai" {{ $statusFilter === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan" {{ $statusFilter === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                <option value="tidak_hadir" {{ $statusFilter === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        @if ($search !== '' || $statusFilter !== 'semua')
                            <a href="{{ route('admin.pemesanan.index') }}" class="btn btn-outline btn-sm">Reset</a>
                        @endif
                    </form>

                    @if ($pemesananList->isEmpty())
                        <div class="admin-empty-state">
                            <div class="admin-empty-icon"><i class="fa-solid fa-ticket"></i></div>
                            <h4>Belum Ada Pemesanan Antrean</h4>
                            <p>Belum ada data pemesanan antrean yang sesuai dengan filter saat ini.</p>
                        </div>
                    @else
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Jadwal</th>
                                        <th>Pemilik &amp; Hewan</th>
                                        <th>Layanan</th>
                                        <th>Nomor Antrean</th>
                                        <th>Pembayaran</th>
                                        <th>Status Booking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pemesananList as $p)
                                        <tr>
                                            <td>
                                                @if ($p->jadwalPelayanan)
                                                    <strong>{{ $p->jadwalPelayanan->tanggal->format('d M Y') }}</strong>
                                                    <div class="text-muted">{{ substr($p->jadwalPelayanan->waktu_mulai, 0, 5) }} - {{ substr($p->jadwalPelayanan->waktu_selesai, 0, 5) }} WIB</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $p->user?->name ?? '-' }}</strong>
                                                <div class="text-muted">{{ ucfirst($p->hewan?->jenis ?? '-') }}{{ $p->hewan?->ras ? ' (' . $p->hewan->ras . ')' : '' }}</div>
                                            </td>
                                            <td>
                                                @if ($p->pemesananLayanan->isNotEmpty())
                                                    {{ $p->pemesananLayanan->map(fn($pl) => $pl->layanan?->nama)->filter()->join(', ') }}
                                                @else
                                                    Pelayanan Umum
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->antrean->isNotEmpty())
                                                    @foreach ($p->antrean as $a)
                                                        <span class="pemilik-badge is-dikonfirmasi">{{ $a->nomor_antrean }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Belum terbit</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($p->pembayaran)
                                                    <span class="pemilik-badge {{ $p->pembayaran->status === 'lunas' ? 'is-tersedia' : 'is-menunggu' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $p->pembayaran->status)) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Belum dibayar</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.pemesanan.status', $p) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" class="form-control admin-select-sm" onchange="this.form.submit()">
                                                        <option value="menunggu" {{ $p->status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                                        <option value="dikonfirmasi" {{ $p->status === 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                                        <option value="sedang_dilayani" {{ $p->status === 'sedang_dilayani' ? 'selected' : '' }}>Sedang Dilayani</option>
                                                        <option value="selesai" {{ $p->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                                        <option value="dibatalkan" {{ $p->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                                        <option value="tidak_hadir" {{ $p->status === 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="kader-pagination-wrap">
                            {{ $pemesananList->links() }}
                        </div>
                    @endif
                </section>
            </main>
        </div>
    </div>
</body>
</html>
