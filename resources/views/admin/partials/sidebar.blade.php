<div class="admin-backdrop" id="adminBackdrop"></div>

<aside class="admin-sidebar" id="adminSidebar">
    <a href="{{ route('admin.dashboard') }}" class="admin-brand">
        <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
    </a>

    @php
        $pendingCount = \App\Models\User::where('status', \App\Models\User::STATUS_MENUNGGU)->count();
    @endphp

    <p class="admin-menu-label">Menu Utama</p>

    <nav class="admin-menu" aria-label="Navigasi admin">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.verifikasi.index') }}" class="{{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-check"></i>
            <span>Verifikasi Pengguna</span>
            @if($pendingCount > 0)
                <span class="admin-menu-counter">{{ $pendingCount }}</span>
            @endif
        </a>

        <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Pengelolaan Pengguna</span>
        </a>

        <p class="admin-menu-label" style="margin-top: 14px;">Operasional Sistem</p>

        <a href="{{ route('admin.jadwal.index') }}" class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-days"></i>
            <span>Jadwal Pelayanan</span>
        </a>

        <a href="{{ route('admin.pemesanan.index') }}" class="{{ request()->routeIs('admin.pemesanan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-ticket"></i>
            <span>Pemesanan Antrean</span>
        </a>

        <a href="{{ url('/') }}" target="_blank">
            <i class="fa-solid fa-arrow-up-right-from-square"></i>
            <span>Lihat Halaman Publik</span>
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" class="admin-logout" id="formLogout">
            @csrf
            <button type="button" class="btn btn-outline admin-btn-logout" onclick="confirmLogout('administrator')">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
