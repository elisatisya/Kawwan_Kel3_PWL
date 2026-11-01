<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') — KAWWAN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <div class="admin-shell">

        <div class="admin-backdrop" id="adminBackdrop"></div>

        <aside class="admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <div class="admin-badge-role">
                <i class="fa-solid fa-shield-halved"></i> Panel Administrator
            </div>

            <p class="admin-menu-label">Menu Utama</p>

            @php
                $pendingCount = \App\Models\User::where('status', \App\Models\User::STATUS_MENUNGGU)->count();
            @endphp

            <nav class="admin-menu" aria-label="Navigasi admin">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="menu-icon-grid" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>
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

                <a href="{{ route('admin.dashboard') }}#operasional" class="is-stub">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Jadwal Pelayanan</span>
                </a>

                <a href="{{ route('admin.dashboard') }}#antrean" class="is-stub">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Pemesanan Antrean</span>
                </a>

                <a href="{{ url('/') }}" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Lihat Halaman Publik</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" class="admin-logout">
                    @csrf
                    <button type="submit" class="btn btn-outline admin-btn-logout">
                        Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>@yield('title', 'Dashboard')</h2>
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
                {{-- Flash Notifications --}}
                @if(session('success'))
                    <div class="admin-alert is-success" role="alert">
                        <div class="admin-alert-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="admin-alert-text">{{ session('success') }}</div>
                        <button type="button" class="admin-alert-close" onclick="this.parentElement.remove();">&times;</button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="admin-alert is-warning" role="alert">
                        <div class="admin-alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div class="admin-alert-text">{{ session('warning') }}</div>
                        <button type="button" class="admin-alert-close" onclick="this.parentElement.remove();">&times;</button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="admin-alert is-info" role="alert">
                        <div class="admin-alert-icon"><i class="fa-solid fa-circle-info"></i></div>
                        <div class="admin-alert-text">{{ session('info') }}</div>
                        <button type="button" class="admin-alert-close" onclick="this.parentElement.remove();">&times;</button>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="admin-alert is-error" role="alert">
                        <div class="admin-alert-icon"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div class="admin-alert-text">
                            @if(session('error'))
                                {{ session('error') }}
                            @else
                                <ul style="margin: 0; padding-left: 18px;">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <button type="button" class="admin-alert-close" onclick="this.parentElement.remove();">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>
