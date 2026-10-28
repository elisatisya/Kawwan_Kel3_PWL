<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — KAWWAN Dokter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <div class="dokter-shell">

        <aside class="dokter-sidebar">
            <a href="{{ route('dokter.dashboard') }}" class="dokter-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <p class="dokter-menu-label">Menu Dokter</p>

            <nav class="dokter-menu" aria-label="Navigasi dokter">
                <a href="{{ route('dokter.dashboard') }}" class="{{ request()->routeIs('dokter.dashboard') ? 'active' : '' }}">
                    <svg class="menu-icon-grid" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg> Dashboard
                </a>
                <a href="#">
                    <i class="fa-solid fa-calendar-check"></i> Jadwal Pemeriksaan
                </a>
                <a href="#">
                    <i class="fa-solid fa-stethoscope"></i> Pemeriksaan
                </a>
                <a href="#">
                    <i class="fa-solid fa-file-medical"></i> Riwayat Pemeriksaan
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="dokter-logout">
                @csrf
                <button type="submit" class="btn btn-outline">
                    Keluar <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </aside>

        <div class="dokter-main">
            <header class="dokter-topbar">
                <h2>@yield('title', 'Dashboard')</h2>

                <div class="dokter-user">
                    <div class="dokter-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div>
                        <span class="dokter-user-name">{{ auth()->user()->name }}</span>
                        <span class="dokter-user-role">Dokter Hewan</span>
                    </div>
                </div>
            </header>

            <main class="dokter-content">
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>