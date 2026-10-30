<aside class="pemilik-sidebar" id="pemilikSidebar">
    <a href="{{ route('pemilik.dashboard') }}" class="pemilik-brand">
        <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
    </a>

    <p class="pemilik-menu-label">Menu Pemilik</p>

    <nav class="pemilik-menu" aria-label="Navigasi Pemilik Hewan">
        <a href="{{ route('pemilik.dashboard') }}" class="{{ request()->routeIs('pemilik.dashboard') || request()->routeIs('pemilik.beranda') ? 'active' : '' }}">
            <svg class="menu-icon-grid" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
            <span>Dashboard</span>
        </a>
        <a href="#">
            <i class="fa-solid fa-paw"></i>
            <span>Data Hewan</span>
        </a>
        <a href="#">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Jadwal Pelayanan</span>
        </a>
        <a href="#">
            <i class="fa-solid fa-ticket"></i>
            <span>Pemesanan Antrean</span>
        </a>
        <a href="#">
            <i class="fa-solid fa-notes-medical"></i>
            <span>Riwayat Rekam Medis</span>
        </a>
        <a href="{{ route('landing') }}#artikel">
            <i class="fa-solid fa-newspaper"></i>
            <span>Artikel Edukasi</span>
        </a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="pemilik-logout">
        @csrf
        <button type="submit" class="btn btn-outline">
            Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
    </form>
</aside>
