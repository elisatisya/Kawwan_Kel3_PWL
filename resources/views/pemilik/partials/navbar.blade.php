<div class="pemilik-backdrop" id="pemilikBackdrop"></div>

<aside class="pemilik-sidebar" id="pemilikSidebar">
    <a href="{{ route('pemilik.dashboard') }}" class="pemilik-brand">
        <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
    </a>

    <p class="pemilik-menu-label">Menu Pemilik Hewan</p>

    <nav class="pemilik-menu" aria-label="Navigasi Pemilik Hewan">
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
            <span>Artikel Edukasi</span>
        </a>
        <a href="{{ route('pemilik.profil') }}" class="{{ request()->routeIs('pemilik.profil*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span>Profil</span>
        </a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="pemilik-logout" id="formLogout">
        @csrf
        <button type="button" class="btn btn-outline" onclick="confirmLogout('pemilik hewan')">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Keluar</span>
        </button>
    </form>
</aside>
