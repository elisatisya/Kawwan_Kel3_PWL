<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Dashboard Administrator' }} — KAWWAN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    @vite(['resources/css/styles.css', 'resources/js/script.js'])
</head>
<body>

    <div class="kawwan-toast-container" id="kawwanToastContainer"></div>

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
                <button type="button" class="btn btn-outline admin-btn-logout" onclick="confirmLogout('administrator')">
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
                    <h2>{{ 'Dashboard Administrator' }}</h2>
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
{{-- Header Sambutan --}}
    <div class="admin-head">
        <div class="admin-head-info">
            <h1>Halo, <span>{{ auth()->user()->name }}</span></h1>
            <p>Ringkasan data operasional sistem KAWWAN dan antrean verifikasi pengguna.</p>
        </div>
        <div class="admin-head-actions">
            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-primary">
                Kelola Verifikasi
            </a>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline">
                Tambah Petugas
            </a>
        </div>
    </div>

    {{-- Banner Peringatan Verifikasi Tertunda --}}
    @if ($stat['menunggu_verifikasi'] > 0)
        <div class="admin-urgent-banner">
            <div class="admin-urgent-content">
                <strong>Prioritas Tindakan: {{ $stat['menunggu_verifikasi'] }} Pengguna Menunggu Verifikasi NIK</strong>
                <p>Verifikasi identitas diperlukan agar pemilik hewan dapat mendaftarkan hewan peliharaannya dan memesan antrean layanan kesehatan.</p>
            </div>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-primary admin-urgent-btn">
                Tinjau Sekarang
            </a>
        </div>
    @endif

    {{-- Grid Kartu Statistik Utama (Clean, No Icon) --}}
    <div class="admin-stats">
        {{-- Stat 1: Total Pengguna --}}
        <div class="admin-stat-card">
            <div class="admin-stat-body">
                <span class="admin-stat-label">Total Pengguna</span>
                <span class="admin-stat-value" data-count="{{ $stat['total_pengguna'] }}">{{ $stat['total_pengguna'] }}</span>
                <span class="admin-stat-sub">Terdaftar di sistem</span>
            </div>
        </div>

        {{-- Stat 2: Menunggu Verifikasi (Highlight) --}}
        <div class="admin-stat-card is-highlight">
            <div class="admin-stat-body">
                <div class="admin-stat-header-row">
                    <span class="admin-stat-label">Menunggu Verifikasi</span>
                    <span class="admin-badge-urgent">Prioritas</span>
                </div>
                <span class="admin-stat-value text-orange" data-count="{{ $stat['menunggu_verifikasi'] }}">{{ $stat['menunggu_verifikasi'] }}</span>
                <span class="admin-stat-sub">Butuh persetujuan admin</span>
            </div>
        </div>

        {{-- Stat 3: Total Hewan --}}
        <div class="admin-stat-card">
            <div class="admin-stat-body">
                <span class="admin-stat-label">Pasien Hewan</span>
                <span class="admin-stat-value" data-count="{{ $stat['total_hewan'] }}">{{ $stat['total_hewan'] }}</span>
                <span class="admin-stat-sub">Tercatat di Puskeswan</span>
            </div>
        </div>

        {{-- Stat 4: Antrean Hari Ini --}}
        <div class="admin-stat-card">
            <div class="admin-stat-body">
                <span class="admin-stat-label">Antrean Hari Ini</span>
                <span class="admin-stat-value" data-count="{{ $stat['antrean_hari_ini'] }}">{{ $stat['antrean_hari_ini'] }}</span>
                <span class="admin-stat-sub">{{ $stat['antrean_menunggu'] }} menunggu pelayanan</span>
            </div>
        </div>
    </div>

    {{-- Mini Pills Summary (Clean Text) --}}
    <div class="admin-pills-bar">
        <div class="admin-pill-item">
            <span>Pemilik Hewan: <strong>{{ $stat['total_pemilik'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Dokter Hewan: <strong>{{ $stat['total_dokter'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Kader Puskesmas: <strong>{{ $stat['total_kader'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Akun Aktif: <strong>{{ $stat['aktif'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Jadwal Buka: <strong>{{ $stat['jadwal_aktif'] }}</strong></span>
        </div>
    </div>

    {{-- Section: Prioritas Verifikasi Pengguna --}}
    <section class="admin-card" id="verifikasi-prioritas">
        <div class="admin-card-head">
            <div>
                <h3 class="admin-card-title">Prioritas Verifikasi Pengguna</h3>
                <p class="admin-card-sub">Pendaftar baru yang menunggu validasi identitas NIK untuk mengakses fitur sistem.</p>
            </div>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-outline btn-sm">
                Lihat Semua Antrean ({{ $stat['menunggu_verifikasi'] }})
            </a>
        </div>

        @if ($prioritasVerifikasi->isEmpty())
            <div class="admin-empty-state">
                <h4>Semua Pengguna Terverifikasi</h4>
                <p>Saat ini tidak ada antrean verifikasi pengguna baru yang tertunda. Sistem berjalan lancar.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pemohon</th>
                            <th>NIK</th>
                            <th>Kontak</th>
                            <th>Alamat Domisili</th>
                            <th>Waktu Registrasi</th>
                            <th style="text-align: right;">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prioritasVerifikasi as $user)
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                        <div>
                                            <span class="admin-user-fullname">{{ $user->name }}</span>
                                            <span class="admin-user-role-badge is-pemilik">{{ $user->role_label }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-nik-badge">
                                        {{ $user->nik ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-contact-cell">
                                        <span>{{ $user->nomor_telepon ?? '-' }}</span>
                                        <span class="text-muted">{{ $user->email ?? 'Tanpa email' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-address-text" title="{{ $user->alamat }}">
                                        {{ Str::limit($user->alamat ?? '-', 45) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-date-text">
                                        {{ $user->created_at ? $user->created_at->diffForHumans() : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-actions-cell" style="justify-content: flex-end;">
                                        {{-- Tombol Setujui Langsung (Eye-Catching Confirm) --}}
                                        <form id="formApproveUser_{{ $user->id }}" action="{{ route('admin.verifikasi.approve', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-action is-approve" onclick="confirmApproveDashboard('{{ $user->id }}', '{{ addslashes($user->name) }}')" title="Setujui Verifikasi">
                                                Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak (Buka Modal) --}}
                                        <button type="button" class="btn btn-action is-reject" onclick="openRejectModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->nik }}')" title="Tolak Verifikasi">
                                            Tolak
                                        </button>

                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn btn-action is-detail" onclick="openDetailModal('{{ $user->id }}')" title="Lihat Detail Lengkap">
                                            Detail
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Grid 2 Kolom: Grafik & Operasional --}}
    <div class="admin-charts-grid">
        {{-- Grafik Tren Pendaftaran & Verifikasi --}}
        <section class="admin-card">
            <div class="admin-card-head">
                <div>
                    <h3 class="admin-card-title">Aktivitas 7 Hari Terakhir</h3>
                    <p class="admin-card-sub">Perbandingan pendaftar baru dan akun yang diverifikasi.</p>
                </div>
            </div>
            <div class="admin-chart-box">
                <canvas id="chartRegistrasi"></canvas>
            </div>
        </section>

        {{-- Grafik Komposisi Status Akun --}}
        <section class="admin-card">
            <div class="admin-card-head">
                <div>
                    <h3 class="admin-card-title">Komposisi Status Akun</h3>
                    <p class="admin-card-sub">Rasio pengguna berdasarkan status verifikasi dan aktivitas.</p>
                </div>
            </div>
            <div class="admin-chart-box">
                <canvas id="chartStatus"></canvas>
            </div>
        </section>
    </div>

    {{-- Section: Ringkasan Operasional Hari Ini --}}
    <section class="admin-card" id="operasional">
        <div class="admin-card-head">
            <div>
                <h3 class="admin-card-title">
                    Antrean Pelayanan Hari Ini ({{ now()->translatedFormat('d F Y') }})
                </h3>
                <p class="admin-card-sub">Pantauan reservasi dan kedatangan pemeriksaan hewan.</p>
            </div>
        </div>

        @if ($antreanHariIni->isEmpty())
            <div class="admin-empty-state">
                <h4>Belum Ada Antrean Hari Ini</h4>
                <p>Tidak ada pemesanan pemeriksaan pada jadwal pelayanan hari ini.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Waktu Pelayanan</th>
                            <th>Pemilik Hewan</th>
                            <th>Jenis Hewan</th>
                            <th>Ras</th>
                            <th>Status Pelayanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($antreanHariIni as $pemesanan)
                            <tr>
                                <td>
                                    <strong>
                                        {{ substr($pemesanan->jadwalPelayanan?->waktu_mulai ?? '', 0, 5) }} -
                                        {{ substr($pemesanan->jadwalPelayanan?->waktu_selesai ?? '', 0, 5) }} WIB
                                    </strong>
                                </td>
                                <td>{{ $pemesanan->user?->name ?? '-' }}</td>
                                <td><strong>{{ ucfirst($pemesanan->hewan?->jenis ?? '-') }}</strong></td>
                                <td>{{ $pemesanan->hewan?->ras ?: '-' }}</td>
                                <td>
                                    <span class="admin-status-badge is-{{ str_replace(' ', '_', strtolower($pemesanan->status)) }}">
                                        {{ ucfirst($pemesanan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- MODAL TOLAK VERIFIKASI (Clean) --}}
    <div class="admin-modal-backdrop" id="modalRejectBackdrop">
        <div class="admin-modal" id="modalReject">
            <div class="admin-modal-head">
                <h4>Tolak Verifikasi Akun</h4>
                <button type="button" class="admin-modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <form id="formReject" action="" method="POST">
                @csrf
                <div class="admin-modal-body">
                    <p class="admin-modal-desc">
                        Anda akan menolak verifikasi akun untuk <strong id="rejectUserName">-</strong> (NIK: <code id="rejectUserNik">-</code>).
                    </p>

                    <label class="form-label" for="rejectPreset">Pilih Alasan Cepat:</label>
                    <div class="admin-reason-presets">
                        <button type="button" class="preset-chip" onclick="setRejectReason('Format NIK tidak valid atau tidak sesuai standar kependudukan (16 digit angka).')">NIK tidak valid</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Domisili/alamat tidak berada di wilayah kerja operasional Puskeswan.')">Luar Wilayah</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Nomor kontak tidak dapat dihubungi untuk konfirmasi data.')">Kontak Tidak Aktif</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Identitas terindikasi duplikasi dengan akun lain yang telah terdaftar.')">Akun Duplikat</button>
                    </div>

                    <div class="form-group" style="margin-top: 14px;">
                        <label class="form-label" for="rejectCatatan">Catatan / Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" id="rejectCatatan" rows="3" class="form-control" placeholder="Tuliskan alasan penolakan secara jelas agar pengguna dapat memperbaiki data..." required></textarea>
                    </div>
                </div>
                <div class="admin-modal-foot">
                    <button type="button" class="btn btn-outline btn-sm" onclick="closeRejectModal()">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Konfirmasi Tolak Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DETAIL PENGGUNA (Clean) --}}
    <div class="admin-modal-backdrop" id="modalDetailBackdrop">
        <div class="admin-modal admin-modal-lg" id="modalDetail">
            <div class="admin-modal-head">
                <h4>Detail Lengkap Pengguna</h4>
                <button type="button" class="admin-modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="admin-modal-body" id="modalDetailContent">
                <div class="admin-modal-loading">Memuat data pengguna...</div>
            </div>
            <div class="admin-modal-foot">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Chart Data JSON Script --}}
    <script id="adminChartData" type="application/json">@json($grafik)</script>
            </main>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                window.kawwanToast && kawwanToast(@json(session('success')), 'success');
            @endif
            @if(session('warning'))
                window.kawwanToast && kawwanToast(@json(session('warning')), 'warning');
            @endif
            @if(session('info'))
                window.kawwanToast && kawwanToast(@json(session('info')), 'info');
            @endif
            @if(session('error'))
                window.kawwanToast && kawwanToast(@json(session('error')), 'error');
            @endif
            @if($errors->any())
                @foreach($errors->all() as $err)
                    window.kawwanToast && kawwanToast(@json($err), 'error');
                @endforeach
            @endif
        });
    </script>
    <script>
    function confirmApproveDashboard(userId, userName) {
        kawwanConfirm({
            title: 'Verifikasi & Aktifkan Akun',
            message: `Apakah Anda yakin ingin memverifikasi dan mengaktifkan akun <strong>${userName}</strong>?`,
            detail: 'Setelah disetujui, akun pemilik hewan akan langsung aktif dan dapat mendaftarkan hewan serta memesan antrean pelayanan.',
            type: 'success',
            confirmText: 'Ya, Setujui',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById(`formApproveUser_${userId}`).submit();
            }
        });
    }

    function openRejectModal(userId, userName, userNik) {
        const form = document.getElementById('formReject');
        form.action = `/admin/verifikasi/${userId}/reject`;
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectUserNik').textContent = userNik || '-';
        document.getElementById('rejectCatatan').value = '';

        document.getElementById('modalRejectBackdrop').classList.add('is-open');
    }

    function closeRejectModal() {
        document.getElementById('modalRejectBackdrop').classList.remove('is-open');
    }

    function setRejectReason(reason) {
        document.getElementById('rejectCatatan').value = reason;
    }

    async function openDetailModal(userId) {
        const backdrop = document.getElementById('modalDetailBackdrop');
        const content = document.getElementById('modalDetailContent');
        backdrop.classList.add('is-open');
        content.innerHTML = '<div class="admin-modal-loading">Memuat data pengguna...</div>';

        try {
            const res = await fetch(`/admin/verifikasi/${userId}`);
            if (!res.ok) throw new Error('Gagal memuat data');
            const data = await res.json();

            let hewanHtml = '<p class="text-muted" style="margin: 6px 0;">Belum ada hewan terdaftar.</p>';
            if (data.hewan && data.hewan.length > 0) {
                hewanHtml = `
                    <div class="detail-hewan-grid">
                        ${data.hewan.map(h => `
                            <div class="detail-hewan-card">
                                <strong>${h.jenis}</strong>
                                <span>Ras: ${h.ras} &bull; ${h.jenis_kelamin}</span>
                                <small class="text-muted">Umur: ${h.umur}</small>
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            content.innerHTML = `
                <div class="admin-detail-layout">
                    <div class="detail-profile-top">
                        <div class="admin-avatar-lg">${data.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <h3 style="margin: 0;">${data.name}</h3>
                            <span class="admin-user-role-badge">${data.role_label}</span>
                            <span class="admin-status-badge ${data.status_badge_class}" style="margin-left: 6px;">${data.status_label}</span>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">NIK</span>
                            <span class="detail-val"><code>${data.nik}</code></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nomor Telepon</span>
                            <span class="detail-val">${data.nomor_telepon}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email</span>
                            <span class="detail-val">${data.email}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Terdaftar Sejak</span>
                            <span class="detail-val">${data.created_at_formatted}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <span class="detail-label">Alamat Domisili</span>
                        <p class="detail-address-box">${data.alamat}</p>
                    </div>

                    ${data.catatan_verifikasi ? `
                        <div class="detail-section">
                            <span class="detail-label">Catatan Verifikasi</span>
                            <p class="detail-note-box">${data.catatan_verifikasi}</p>
                            ${data.verified_by_name ? `<small class="text-muted">Diverifikasi oleh: ${data.verified_by_name} pada ${data.verified_at_formatted}</small>` : ''}
                        </div>
                    ` : ''}

                    <div class="detail-section" style="margin-top: 14px;">
                        <span class="detail-label">Hewan Peliharaan Terdaftar (${data.hewan.length})</span>
                        ${hewanHtml}
                    </div>
                </div>
            `;
        } catch (e) {
            content.innerHTML = `<div class="admin-alert is-error">Gagal mengambil data pengguna: ${e.message}</div>`;
        }
    }

    function closeDetailModal() {
        document.getElementById('modalDetailBackdrop').classList.remove('is-open');
    }
</script>

</body>
</html>
