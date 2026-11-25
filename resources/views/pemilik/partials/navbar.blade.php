<header class="pemilik-header">
    <div class="pemilik-header-inner">
        <a href="{{ route('pemilik.dashboard') }}" class="pemilik-top-brand">
            <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
        </a>

        <button
            class="menu-toggle"
            id="menuToggle"
            type="button"
            aria-label="Buka menu"
            aria-expanded="false"
            aria-controls="navLinks"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="pemilik-top-nav" id="navLinks" aria-label="Navigasi Pemilik Hewan">
            <a href="{{ route('pemilik.dashboard') }}" class="{{ request()->routeIs('pemilik.dashboard') || request()->routeIs('pemilik.beranda') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('pemilik.hewan.index') }}" class="{{ request()->routeIs('pemilik.hewan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-paw"></i>
                <span>Data Hewan</span>
            </a>
            <a href="{{ route('pemilik.jadwal.index') }}" class="{{ request()->routeIs('pemilik.jadwal.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days"></i>
                <span>Jadwal &amp; Layanan</span>
            </a>
            <a href="{{ route('pemilik.pemesanan.index') }}" class="{{ request()->routeIs('pemilik.pemesanan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-ticket"></i>
                <span>Pemesanan Antrean</span>
            </a>
            <a href="{{ route('landing') }}#artikel">
                <i class="fa-solid fa-newspaper"></i>
                <span>Artikel</span>
            </a>

            <div class="pemilik-mobile-actions">
                <a href="{{ route('pemilik.profil') }}" class="pemilik-btn pemilik-btn-outline">
                    <i class="fa-solid fa-user"></i> Profil Saya
                </a>
                <form action="{{ route('logout') }}" method="POST" class="pemilik-logout">
                    @csrf
                    <button type="button" class="pemilik-btn pemilik-btn-primary" onclick="confirmLogout('pemilik hewan')">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </nav>

        <div class="pemilik-top-actions">
            <a href="{{ route('pemilik.profil') }}" class="pemilik-profile-pill {{ request()->routeIs('pemilik.profil*') ? 'active' : '' }}" title="Lihat Profil">
                <span class="pemilik-avatar">
                    @if (auth()->user()->foto)
                        <img src="{{ asset('storage/'.auth()->user()->foto) }}" alt="Foto {{ auth()->user()->name }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </span>
                <span class="pemilik-profile-meta">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>Pemilik Hewan</small>
                </span>
            </a>

            <form action="{{ route('logout') }}" method="POST" class="pemilik-logout" id="formLogout">
                @csrf
                <button type="button" class="pemilik-logout-btn" onclick="confirmLogout('pemilik hewan')" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</header>
