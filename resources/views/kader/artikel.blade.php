<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel — KAWWAN Kader</title>

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
            <a href="{{ route('kader.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Kader Puskesmas</p>

            <nav class="dokter-menu" aria-label="Navigasi kader">
                <a href="{{ route('kader.dashboard') }}" class="{{ request()->routeIs('kader.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('kader.jadwal.index') }}" class="{{ request()->routeIs('kader.jadwal.*') ? 'active' : '' }}">
                    Jadwal Pelayanan
                </a>
                <a href="{{ route('kader.pemesanan.index') }}" class="{{ request()->routeIs('kader.pemesanan.*') || request()->routeIs('kader.pembayaran.*') ? 'active' : '' }}">
                    Booking &amp; Pembayaran
                </a>
                <a href="{{ route('kader.layanan.index') }}" class="{{ request()->routeIs('kader.layanan.*') ? 'active' : '' }}">
                    Layanan &amp; Harga
                </a>
                <a href="{{ route('kader.antrean.index') }}" class="{{ request()->routeIs('kader.antrean.*') ? 'active' : '' }}">
                    Antrean
                </a>
                <a href="{{ route('kader.artikel.index') }}" class="{{ request()->routeIs('kader.artikel.*') ? 'active' : '' }}">
                    Artikel
                </a>
                <a href="{{ route('kader.profil') }}" class="{{ request()->routeIs('kader.profil*') ? 'active' : '' }}">
                    Profil
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="dokter-logout" id="formLogout">
                @csrf
                <button type="button" class="btn btn-outline" onclick="confirmLogout('kader puskesmas')">
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
                    <h2>Artikel Kesehatan Hewan</h2>
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
                        <span class="dokter-user-role">Kader Puskesmas</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">

                <div class="dokter-head">
                    <div>
                        <h1>Kelola <span>Artikel Kesehatan Hewan</span></h1>
                        <p>Cari dan kurasi artikel dari NewsData.io API, lalu publikasikan ke halaman utama untuk pemilik hewan.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="dokter-alert is-success">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="dokter-alert is-error">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="dokter-alert is-error">
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                {{-- PANEL 1: PENCARIAN ARTIKEL DARI NEWSDATA.IO API --}}
                <section class="dokter-card kawwan-api-panel"
                         id="kaderArtikelApi"
                         data-search-url="{{ route('kader.artikel.search-api') }}"
                         data-import-url="{{ route('kader.artikel.import') }}"
                         data-csrf="{{ csrf_token() }}"
                         data-default-img="{{ asset('images/default-artikel.png') }}">

                    <div class="kawwan-panel-head">
                        <div>
                            <h2><i class="fa-solid fa-magnifying-glass"></i> Cari Artikel Kesehatan Hewan (API)</h2>
                            <p class="dokter-card-sub">Masukkan topik atau pilih kata kunci cepat untuk mengambil berita berbahasa Indonesia dari NewsData.io.</p>
                        </div>
                    </div>

                    <div class="kawwan-search-row">
                        <input type="text"
                               id="api-keyword"
                               class="kawwan-input"
                               value="kesehatan hewan"
                               placeholder="Masukkan topik, misal: rabies, vaksin kucing, perawatan anjing...">
                        <button type="button" id="btn-search-api" class="kawwan-btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Cari Artikel</span>
                        </button>
                    </div>

                    <div class="kawwan-tag-group" role="group" aria-label="Preset topik pencarian">
                        <button type="button" class="kawwan-tag-btn is-active" data-q="kesehatan hewan">Kesehatan Hewan</button>
                        <button type="button" class="kawwan-tag-btn" data-q="rabies">Rabies</button>
                        <button type="button" class="kawwan-tag-btn" data-q="vaksin kucing">Vaksin Kucing</button>
                        <button type="button" class="kawwan-tag-btn" data-q="hewan peliharaan">Hewan Peliharaan</button>
                        <button type="button" class="kawwan-tag-btn" data-q="anjing kucing">Anjing &amp; Kucing</button>
                    </div>

                    <div id="api-status" class="kawwan-api-status" aria-live="polite"></div>
                    <div id="api-results" class="kawwan-article-grid"></div>
                </section>

                {{-- PANEL 2: DAFTAR ARTIKEL TERSIMPAN DI DATABASE --}}
                <section class="dokter-card kawwan-saved-panel">
                    <div class="kawwan-panel-head">
                        <div>
                            <h2><i class="fa-solid fa-newspaper"></i> Daftar Artikel Tersimpan</h2>
                            <p class="dokter-card-sub">Artikel berstatus <strong>Published</strong> otomatis tampil di halaman utama (landing page) untuk Pemilik Hewan.</p>
                        </div>
                        <span class="antrean-count">Total: {{ $artikels->total() }} Artikel</span>
                    </div>

                    @if ($artikels->isEmpty())
                        <div class="dokter-empty kawwan-empty-state">
                            <i class="fa-solid fa-newspaper"></i>
                            <p>Belum ada artikel yang diimpor. Gunakan panel pencarian API di atas untuk menambahkan artikel.</p>
                        </div>
                    @else
                        <div class="dokter-table-wrap">
                            <table class="dokter-table kawwan-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Judul &amp; Sumber</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($artikels as $artikel)
                                        @php
                                            $thumb = $artikel->gambar
                                                ? (str_starts_with($artikel->gambar, 'http') ? $artikel->gambar : asset('storage/' . $artikel->gambar))
                                                : asset('images/default-artikel.png');
                                            $sumberLabel = $artikel->sumber ?: (parse_url($artikel->link, PHP_URL_HOST) ?: 'Sumber Eksternal');
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration + ($artikels->currentPage() - 1) * $artikels->perPage() }}</td>
                                            <td>
                                                <div class="kawwan-saved-item">
                                                    <img src="{{ $thumb }}"
                                                         alt="{{ $artikel->judul }}"
                                                         class="kawwan-saved-thumb"
                                                         data-fallback-src="{{ asset('images/default-artikel.png') }}">
                                                    <div class="kawwan-saved-meta">
                                                        <a href="{{ $artikel->link }}"
                                                           target="_blank"
                                                           rel="noopener noreferrer"
                                                           class="kawwan-saved-title">
                                                            {{ $artikel->judul }}
                                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                        </a>
                                                        <small class="kawwan-saved-source">{{ $sumberLabel }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="kawwan-badge">{{ $artikel->kategori }}</span>
                                            </td>
                                            <td>
                                                @if ($artikel->status === 'published')
                                                    <span class="kawwan-status-badge is-published">Published</span>
                                                @else
                                                    <span class="kawwan-status-badge is-draft">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ ($artikel->published_at ?? $artikel->created_at)?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td>
                                                <div class="kader-aksi">
                                                    <form action="{{ route('kader.artikel.update-status', $artikel) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $artikel->status === 'published' ? 'draft' : 'published' }}">
                                                        <button type="submit" class="kawwan-btn-secondary">
                                                            <i class="fa-solid {{ $artikel->status === 'published' ? 'fa-eye-slash' : 'fa-globe' }}"></i>
                                                            {{ $artikel->status === 'published' ? 'Jadikan Draft' : 'Publish' }}
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('kader.artikel.destroy', $artikel) }}"
                                                          method="POST"
                                                          data-confirm="Yakin ingin menghapus artikel ini dari daftar tersimpan?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn-icon-action is-delete"
                                                                title="Hapus Artikel"
                                                                aria-label="Hapus Artikel">
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

                        @if ($artikels->hasPages())
                            <div class="kader-pagination-wrap">
                                {{ $artikels->links() }}
                            </div>
                        @endif
                    @endif
                </section>

            </main>
        </div>

    </div>

</body>
</html>
