<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktik — KAWWAN Dokter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <div class="dokter-shell">

        <div class="dokter-backdrop" id="dokterBackdrop"></div>

        <aside class="dokter-sidebar" id="dokterSidebar">
            <a href="{{ route('dokter.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Dokter</p>

            <nav class="dokter-menu" aria-label="Navigasi dokter">
                <a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('dokter.jadwal.index') }}" class="{{ request()->routeIs('dokter.jadwal.*') ? 'active' : '' }}">
                    Jadwal Praktik
                </a>
                <a href="{{ route('dokter.pemeriksaan.index') }}" class="{{ request()->routeIs('dokter.pemeriksaan.*') ? 'active' : '' }}">
                    Pemeriksaan
                </a>
                <a href="{{ route('dokter.riwayat.index') }}" class="{{ request()->routeIs('dokter.riwayat.*') ? 'active' : '' }}">
                    Riwayat Pemeriksaan
                </a>
                <a href="{{ route('dokter.profil') }}" class="{{ request()->routeIs('dokter.profil*') ? 'active' : '' }}">
                    Profil
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="dokter-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('dokter hewan')">
                    Keluar
                </button>
            </form>
        </aside>

        <div class="dokter-main">
            <header class="dokter-topbar">
                <div class="dokter-topbar-left">
                    <button class="dokter-menu-toggle" id="dokterMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>Jadwal Praktik</h2>
                </div>

                <div class="dokter-user">
                    <div class="dokter-avatar">
                        @if (auth()->user()->foto)
                            <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <span class="dokter-user-name">{{ auth()->user()->name }}</span>
                        <span class="dokter-user-role">Dokter Hewan</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">

                @if (session('success'))
                    <div class="dokter-alert is-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="dokter-alert is-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
                @endif

                <section class="dokter-card">
                    <div class="dokter-card-head">
                        <div>
                            <h3>Daftar Jadwal Praktik</h3>
                            <p class="dokter-card-sub">Jadwal praktik {{ trim((auth()->user()->gelar_depan ?? '').' '.auth()->user()->name.' '.(auth()->user()->gelar_belakang ?? '')) }}. Jadwal ini tampil untuk Kader dan Pemilik Hewan.</p>
                        </div>
                        <a href="{{ route('dokter.jadwal.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-plus"></i> Tambah Jadwal
                        </a>
                    </div>

                    <div class="jadwal-tabs">
                        <a href="{{ route('dokter.jadwal.index') }}" class="jadwal-tab {{ $lihat === 'saya' ? 'is-active' : '' }}">Jadwal Saya</a>
                        <a href="{{ route('dokter.jadwal.index', ['lihat' => 'semua']) }}" class="jadwal-tab {{ $lihat === 'semua' ? 'is-active' : '' }}">Semua Dokter</a>
                        <a href="{{ route('dokter.jadwal.index', ['lihat' => 'kalender']) }}" class="jadwal-tab {{ $lihat === 'kalender' ? 'is-active' : '' }}">Kalender</a>
                    </div>

                    {{-- ===== TAB KALENDER MINGGUAN ===== --}}
                    @if ($lihat === 'kalender')
                        <div class="kal-nav">
                            <a href="{{ route('dokter.jadwal.index', ['lihat' => 'kalender', 'minggu' => $mingguLalu]) }}" class="btn btn-outline btn-sm">&laquo; Minggu lalu</a>
                            <strong>{{ $awal->copy()->locale('id')->translatedFormat('d M') }} - {{ $akhir->copy()->locale('id')->translatedFormat('d M Y') }}</strong>
                            <a href="{{ route('dokter.jadwal.index', ['lihat' => 'kalender', 'minggu' => $mingguDepan]) }}" class="btn btn-outline btn-sm">Minggu depan &raquo;</a>
                        </div>

                        <div class="kal-grid">
                            @foreach ($hari as $h)
                                @php $items = $perTanggal[$h->toDateString()] ?? collect(); @endphp
                                <div class="kal-hari {{ $h->isToday() ? 'is-today' : '' }}">
                                    <div class="kal-hari-judul">{{ $h->copy()->locale('id')->translatedFormat('D') }} <span>{{ $h->format('d') }}</span></div>

                                    @forelse ($items as $j)
                                        @php $saya = $j->dokter_id === auth()->id(); @endphp
                                        <div class="kal-item {{ $saya ? 'is-mine' : '' }} {{ $j->status === 'ditutup' ? 'is-closed' : '' }}">
                                            <strong>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }}</strong>
                                            <span>{{ $saya ? 'Anda' : trim(($j->dokter?->gelar_depan ?? '').' '.($j->dokter?->name ?? '-')) }}</span>
                                        </div>
                                    @empty
                                        <p class="kal-kosong">-</p>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>

                    {{-- ===== TAB SEMUA DOKTER ===== --}}
                    @elseif ($lihat === 'semua')
                        <form method="GET" action="{{ route('dokter.jadwal.index') }}" class="jadwal-filter">
                            <input type="hidden" name="lihat" value="semua">

                            <div class="form-group">
                                <label for="dari">Dari tanggal</label>
                                <input type="date" id="dari" name="dari" value="{{ $dari }}">
                            </div>

                            <div class="form-group">
                                <label for="sampai">Sampai tanggal</label>
                                <input type="date" id="sampai" name="sampai" value="{{ $sampai }}">
                            </div>

                            <div class="form-group">
                                <label for="dokter_id">Dokter</label>
                                <select id="dokter_id" name="dokter_id">
                                    <option value="">Semua dokter</option>
                                    @foreach ($daftarDokter as $d)
                                        <option value="{{ $d->id }}" @selected($dokterId == $d->id)>
                                            {{ trim(($d->gelar_depan ?? '').' '.$d->name.' '.($d->gelar_belakang ?? '')) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </form>

                        @forelse ($semuaJadwal as $tgl => $items)
                            <h4 class="jadwal-hari-judul">{{ \Carbon\Carbon::parse($tgl)->locale('id')->translatedFormat('l, d F Y') }}</h4>

                            <div class="dokter-table-wrap">
                                <table class="dokter-table">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Dokter</th>
                                            <th>Kuota</th>
                                            <th>Terisi</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $j)
                                            @php $saya = $j->dokter_id === auth()->id(); @endphp
                                            <tr class="{{ $saya ? 'is-mine' : '' }}">
                                                <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                                <td>
                                                    {{ trim(($j->dokter?->gelar_depan ?? '').' '.($j->dokter?->name ?? '-').' '.($j->dokter?->gelar_belakang ?? '')) }}
                                                    @if ($saya)
                                                        <span class="dokter-badge is-anda">Anda</span>
                                                    @endif
                                                </td>
                                                <td>{{ $j->kuota ?? '-' }}</td>
                                                <td>{{ $j->terpakai }}</td>
                                                <td><span class="dokter-badge is-{{ $j->status }}">{{ ucfirst($j->status) }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @empty
                            <p class="dokter-empty">Tidak ada jadwal praktik pada rentang tanggal ini.</p>
                        @endforelse

                    {{-- ===== TAB JADWAL SAYA ===== --}}
                    @elseif ($jadwal->isEmpty())
                        <p class="dokter-empty">Belum ada jadwal praktik. Klik "Tambah Jadwal" untuk membuat.</p>
                    @else
                        <div class="dokter-table-wrap">
                            <table class="dokter-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Kuota</th>
                                        <th>Terpakai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwal as $j)
                                        <tr>
                                            <td>{{ $j->tanggal->locale('id')->translatedFormat('d M Y') }}</td>
                                            <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                                            <td>{{ $j->kuota ?? 'Tanpa batas' }}</td>
                                            <td>{{ $j->terpakai }}</td>
                                            <td>
                                                <span class="dokter-badge is-{{ $j->status }}">{{ ucfirst($j->status) }}</span>
                                            </td>
                                            <td>
                                                <div class="dokter-actions">
                                                    <a href="{{ route('dokter.jadwal.edit', $j) }}" class="btn btn-outline btn-sm">Ubah</a>

                                                    <form action="{{ route('dokter.jadwal.destroy', $j) }}" method="POST"
                                                          onsubmit="return confirm('Hapus jadwal ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $jadwal->links() }}
                    @endif
                </section>

            </main>
        </div>
    </div>

</body>
</html>