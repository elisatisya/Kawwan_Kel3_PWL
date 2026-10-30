<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — KAWWAN Pemilik Hewan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body class="pemilik-body">

    <div class="pemilik-shell">
        <div class="pemilik-backdrop" id="pemilikBackdrop"></div>

        @include('pemilik.partials.navbar')

        <div class="pemilik-main-wrap">
            <header class="pemilik-topbar">
                <div class="pemilik-topbar-left">
                    <button class="pemilik-menu-toggle" id="pemilikMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2>@yield('title', 'Dashboard')</h2>
                </div>

                <div class="pemilik-user-profile">
                    <div class="pemilik-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="pemilik-user-name">{{ auth()->user()->name }}</span>
                        <span class="pemilik-user-role">Pemilik Hewan</span>
                    </div>
                </div>
            </header>

            <main class="pemilik-main">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
