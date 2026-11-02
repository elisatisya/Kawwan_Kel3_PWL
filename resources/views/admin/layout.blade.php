<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') — KAWWAN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    {{-- Eye-Catching Floating Toast Container --}}
    <div class="kawwan-toast-container" id="kawwanToastContainer"></div>

    {{-- Eye-Catching Custom Confirm Dialog Modal --}}
    <div class="kawwan-dialog-backdrop" id="kawwanConfirmBackdrop">
        <div class="kawwan-dialog type-primary" id="kawwanConfirmDialog">
            <div class="kawwan-dialog-header">
                <span class="kawwan-dialog-tag" id="kawwanConfirmTag">Konfirmasi</span>
                <h3 class="kawwan-dialog-title" id="kawwanConfirmTitle">Konfirmasi Tindakan</h3>
            </div>
            <div class="kawwan-dialog-body">
                <p class="kawwan-dialog-desc" id="kawwanConfirmMessage">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                <div class="kawwan-dialog-box" id="kawwanConfirmDetail" style="display: none;"></div>
            </div>
            <div class="kawwan-dialog-actions">
                <button type="button" class="btn btn-outline btn-sm" id="kawwanConfirmCancelBtn">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" id="kawwanConfirmOkBtn">Lanjutkan</button>
            </div>
        </div>
    </div>

    <div class="admin-shell">

        <div class="admin-backdrop" id="adminBackdrop"></div>

        <aside class="admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <img src="{{ asset('images/logo-kawwan.png') }}" alt="Logo KAWWAN">
            </a>

            <div class="admin-badge-role">
                Panel Administrator
            </div>

            <p class="admin-menu-label">Menu Utama</p>

            @php
                $pendingCount = \App\Models\User::where('status', \App\Models\User::STATUS_MENUNGGU)->count();
            @endphp

            <nav class="admin-menu" aria-label="Navigasi admin">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.verifikasi.index') }}" class="{{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}">
                    <span>Verifikasi Pengguna</span>
                    @if($pendingCount > 0)
                        <span class="admin-menu-counter">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
                    <span>Pengelolaan Pengguna</span>
                </a>

                <p class="admin-menu-label" style="margin-top: 14px;">Operasional Sistem</p>

                <a href="{{ route('admin.dashboard') }}#operasional" class="is-stub">
                    <span>Jadwal Pelayanan</span>
                </a>

                <a href="{{ route('admin.dashboard') }}#antrean" class="is-stub">
                    <span>Pemesanan Antrean</span>
                </a>

                <a href="{{ url('/') }}" target="_blank">
                    <span>Lihat Halaman Publik</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" class="admin-logout" id="formLogout">
                    @csrf
                    <button type="button" class="btn btn-outline admin-btn-logout" onclick="confirmLogout()">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="admin-topbar-left">
                    <button class="admin-menu-toggle" id="adminMenuToggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false">
                        Menu
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
                @yield('content')
            </main>
        </div>

    </div>

    {{-- System Core Notification & Dialog Scripts --}}
    <script>
        // Global Toast Notification Helper (Eye-Catching)
        window.kawwanToast = function(message, type = 'success', title = null) {
            const container = document.getElementById('kawwanToastContainer');
            if (!container) return;

            const titles = {
                success: 'Berhasil',
                warning: 'Perhatian',
                error: 'Terjadi Kesalahan',
                info: 'Informasi'
            };

            const toast = document.createElement('div');
            toast.className = `kawwan-toast is-${type}`;
            toast.innerHTML = `
                <div class="kawwan-toast-header">
                    <span class="kawwan-toast-badge">${titles[type] || 'Notifikasi'}</span>
                    <button type="button" class="kawwan-toast-close" aria-label="Tutup notifikasi">&times;</button>
                </div>
                ${title ? `<p class="kawwan-toast-title">${title}</p>` : ''}
                <p class="kawwan-toast-message">${message}</p>
                <div class="kawwan-toast-progress"></div>
            `;

            container.appendChild(toast);

            const closeToast = () => {
                toast.classList.add('is-hiding');
                setTimeout(() => toast.remove(), 320);
            };

            toast.querySelector('.kawwan-toast-close').addEventListener('click', closeToast);
            setTimeout(closeToast, 4500);
        };

        // Global Custom Confirmation Dialog (Eye-Catching, replaces browser confirm())
        window.kawwanConfirm = function({
            title = 'Konfirmasi Tindakan',
            message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            detail = null,
            type = 'primary', // 'primary', 'success', 'warning', 'danger'
            confirmText = 'Lanjutkan',
            cancelText = 'Batal',
            onConfirm = null
        }) {
            const backdrop = document.getElementById('kawwanConfirmBackdrop');
            const dialog = document.getElementById('kawwanConfirmDialog');
            const tagEl = document.getElementById('kawwanConfirmTag');
            const titleEl = document.getElementById('kawwanConfirmTitle');
            const msgEl = document.getElementById('kawwanConfirmMessage');
            const detailEl = document.getElementById('kawwanConfirmDetail');
            const cancelBtn = document.getElementById('kawwanConfirmCancelBtn');
            const okBtn = document.getElementById('kawwanConfirmOkBtn');

            if (!backdrop) return;

            dialog.className = `kawwan-dialog type-${type}`;

            const tags = {
                success: 'Persetujuan',
                danger: 'Tindakan Berisiko',
                warning: 'Peringatan',
                primary: 'Konfirmasi'
            };
            tagEl.textContent = tags[type] || 'Konfirmasi';

            titleEl.textContent = title;
            msgEl.innerHTML = message;

            if (detail) {
                detailEl.innerHTML = detail;
                detailEl.style.display = 'block';
            } else {
                detailEl.style.display = 'none';
            }

            cancelBtn.textContent = cancelText;
            okBtn.textContent = confirmText;

            if (type === 'danger') {
                okBtn.className = 'btn btn-danger btn-sm';
            } else if (type === 'success') {
                okBtn.className = 'btn btn-action is-approve btn-sm';
            } else {
                okBtn.className = 'btn btn-primary btn-sm';
            }

            backdrop.classList.add('is-open');

            const cleanup = () => {
                backdrop.classList.remove('is-open');
                cancelBtn.onclick = null;
                okBtn.onclick = null;
            };

            cancelBtn.onclick = cleanup;
            okBtn.onclick = () => {
                cleanup();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            };

            backdrop.onclick = (e) => {
                if (e.target === backdrop) {
                    cleanup();
                }
            };
        };

        function confirmLogout() {
            kawwanConfirm({
                title: 'Konfirmasi Keluar',
                message: 'Apakah Anda yakin ingin mengakhiri sesi administrator dan keluar dari sistem?',
                type: 'warning',
                confirmText: 'Ya, Keluar',
                cancelText: 'Tetap di Sini',
                onConfirm: () => {
                    document.getElementById('formLogout').submit();
                }
            });
        }

        // Otomatis munculkan Toast untuk Flash Session Laravel
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                kawwanToast(@json(session('success')), 'success');
            @endif

            @if(session('warning'))
                kawwanToast(@json(session('warning')), 'warning');
            @endif

            @if(session('info'))
                kawwanToast(@json(session('info')), 'info');
            @endif

            @if(session('error'))
                kawwanToast(@json(session('error')), 'error');
            @endif

            @if($errors->any())
                @foreach($errors->all() as $err)
                    kawwanToast(@json($err), 'error');
                @endforeach
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
