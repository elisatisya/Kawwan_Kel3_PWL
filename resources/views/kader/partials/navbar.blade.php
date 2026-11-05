
<aside class="dokter-sidebar" id="dokterSidebar">
    <a href="{{ route('kader.dashboard') }}" class="dokter-brand">
        <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
    </a>

    <p class="dokter-menu-label">Menu Kader Puskesmas</p>

    <nav class="dokter-menu" aria-label="Navigasi kader">

        {{-- Dashboard --}}
        <a href="{{ route('kader.dashboard') }}"
           class="{{ request()->routeIs('kader.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>

        {{-- Jadwal Pelayanan --}}
        <a href="{{ route('kader.jadwal.index') }}"
           class="{{ request()->routeIs('kader.jadwal.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i> Jadwal Pelayanan
        </a>

        {{-- Booking & Pembayaran --}}
        <a href="{{ route('kader.pemesanan.index') }}"
           class="{{ request()->routeIs('kader.pemesanan.*') || request()->routeIs('kader.pembayaran.*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list"></i> Booking & Pembayaran
        </a>

        {{-- Layanan dan Harga --}}
        <a href="{{ route('kader.layanan.index') }}"
        class="{{ request()->routeIs('kader.layanan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-stethoscope"></i> Layanan & Harga
        </a>
        
        {{-- Antrean --}}
        <a href="{{ route('kader.antrean.index') }}"
           class="{{ request()->routeIs('kader.antrean.*') ? 'active' : '' }}">
            <i class="fa-solid fa-ticket"></i> Antrean
        </a>

        {{-- Artikel --}}
        <a href="{{ route('kader.artikel.index') }}"
           class="{{ request()->routeIs('kader.artikel.*') ? 'active' : '' }}">
            <i class="fa-solid fa-newspaper"></i> Artikel
        </a>

        {{-- Profil --}}
        <a href="{{ route('kader.profil') }}"
           class="{{ request()->routeIs('kader.profil*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i> Profil
        </a>

    </nav>

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" class="dokter-logout">
        @csrf
        <button type="submit" class="btn btn-outline">
            Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
    </form>
</aside>
