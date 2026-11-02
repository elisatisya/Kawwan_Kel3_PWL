<aside class="dokter-sidebar" id="dokterSidebar">
    <a href="{{ route('dokter.dashboard') }}" class="dokter-brand">
        <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
    </a>

    <p class="dokter-menu-label">Menu Dokter</p>

    <nav class="dokter-menu" aria-label="Navigasi dokter">
        <a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
            <svg class="menu-icon-grid" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg> Dashboard
        </a>
        <a href="{{ route('dokter.riwayat.index') }}" class="{{ request()->routeIs('dokter.riwayat.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-medical"></i> Riwayat Pemeriksaan
        </a>
        <a href="{{ route('dokter.profil') }}" class="{{ request()->routeIs('dokter.profil*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-doctor"></i> Profil
        </a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="dokter-logout">
        @csrf
        <button type="submit" class="btn btn-outline">
            Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
    </form>
</aside>