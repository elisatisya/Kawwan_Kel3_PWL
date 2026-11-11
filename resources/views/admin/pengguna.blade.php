<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'Pengelolaan Pengguna' }} — KAWWAN</title>

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
                    <h2>{{ 'Pengelolaan Pengguna' }}</h2>
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
{{-- Header Halaman --}}
    <div class="admin-head">
        <div class="admin-head-info">
            <h1>Pengelolaan <span>Pengguna</span></h1>
            <p>Kelola data seluruh akun pengguna, atur hak akses / peran, dan buat akun petugas (Dokter & Kader) secara terkontrol.</p>
        </div>
        <div class="admin-head-actions">
            <button type="button" class="btn btn-primary" onclick="openCreateUserModal()">
                + Tambah Petugas
            </button>
        </div>
    </div>

    {{-- Summary Role Bar (Clean, No Icon) --}}
    <div class="admin-pills-bar" style="margin-bottom: 24px;">
        <div class="admin-pill-item">
            <span>Total: <strong>{{ $roleCounts['total'] }}</strong> Pengguna</span>
        </div>
        <div class="admin-pill-item">
            <span>Pemilik Hewan: <strong>{{ $roleCounts['pemilik'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Dokter Hewan: <strong>{{ $roleCounts['dokter'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Kader Puskesmas: <strong>{{ $roleCounts['kader'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <span>Admin: <strong>{{ $roleCounts['admin'] }}</strong></span>
        </div>
    </div>

    {{-- Main Filter & Data Card --}}
    <section class="admin-card">
        {{-- Search & Filter Toolbar --}}
        <form method="GET" action="{{ route('admin.pengguna.index') }}" class="admin-search-toolbar">
            <div class="toolbar-search-input">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, NIK, atau no. telepon..." class="form-control">
                @if($search)
                    <a href="{{ route('admin.pengguna.index', ['role' => $roleFilter, 'status' => $statusFilter]) }}" class="search-clear-btn" title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <div class="toolbar-filter-select">
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="semua" {{ $roleFilter === 'semua' ? 'selected' : '' }}>Semua Role</option>
                    <option value="pemilik" {{ $roleFilter === 'pemilik' ? 'selected' : '' }}>Pemilik Hewan</option>
                    <option value="dokter" {{ $roleFilter === 'dokter' ? 'selected' : '' }}>Dokter Hewan</option>
                    <option value="kader" {{ $roleFilter === 'kader' ? 'selected' : '' }}>Kader Puskesmas</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>

            <div class="toolbar-filter-select">
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="menunggu_verifikasi" {{ $statusFilter === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="ditolak" {{ $statusFilter === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if($search !== '' || $roleFilter !== 'semua' || $statusFilter !== 'semua')
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>

        {{-- Tabel Pengguna --}}
        @if ($users->isEmpty())
            <div class="admin-empty-state">
                <h4>Tidak Ada Pengguna yang Cocok</h4>
                <p>Silakan sesuaikan kata kunci pencarian atau filter role/status di atas.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama & Identitas</th>
                            <th>Kontak</th>
                            <th>Peran</th>
                            <th>Status Akun</th>
                            <th>Peliharaan</th>
                            <th>Waktu Daftar</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="admin-user-cell">
                                        <div class="admin-user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                        <div>
                                            <span class="admin-user-fullname">{{ $user->name }}</span>
                                            @if($user->nik)
                                                <small class="text-muted d-block font-mono">NIK: {{ $user->nik }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="admin-contact-cell">
                                        <span>{{ $user->email ?? 'Tanpa email' }}</span>
                                        <small class="text-muted">{{ $user->nomor_telepon ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>
                                    {{-- Form Cepat Ubah Role --}}
                                    @if($user->id === auth()->id())
                                        <span class="admin-user-role-badge is-admin">{{ $user->role_label }}</span>
                                    @else
                                        <form id="formRole_{{ $user->id }}" action="{{ route('admin.pengguna.role', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="form-control admin-select-sm" onchange="confirmChangeRole(this, '{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->role }}')">
                                                <option value="{{ \App\Models\User::ROLE_PEMILIK }}" {{ $user->hasRole('pemilik') ? 'selected' : '' }}>Pemilik Hewan</option>
                                                <option value="{{ \App\Models\User::ROLE_DOKTER }}" {{ $user->hasRole('dokter') ? 'selected' : '' }}>Dokter Hewan</option>
                                                <option value="{{ \App\Models\User::ROLE_KADER }}" {{ $user->isKader() ? 'selected' : '' }}>Kader Puskesmas</option>
                                                <option value="{{ \App\Models\User::ROLE_ADMIN }}" {{ $user->isAdmin() ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-status-badge {{ $user->status_badge_class }}">
                                        {{ $user->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->hewan_count > 0)
                                        <span class="admin-badge-count">{{ $user->hewan_count }} ekor</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-date-text">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                                </td>
                                <td>
                                    <div class="admin-actions-cell" style="justify-content: flex-end;">
                                        {{-- Edit Data Pengguna (CRUD Update) --}}
                                        <button type="button" class="btn-action is-edit" onclick="openEditUserModal({{ json_encode([
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'email' => $user->email,
                                            'nik' => $user->nik,
                                            'nomor_telepon' => $user->nomor_telepon,
                                            'alamat' => $user->alamat,
                                            'role' => $user->role,
                                            'status' => $user->status,
                                            'is_self' => $user->id === auth()->id()
                                        ]) }})" title="Edit Data Pengguna">
                                            Edit
                                        </button>

                                        {{-- Toggle Status --}}
                                        @if($user->id !== auth()->id())
                                            <form id="formTogglePengguna_{{ $user->id }}" action="{{ route('admin.verifikasi.toggle', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn-action {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'is-deactivate' : 'is-activate' }}" onclick="confirmTogglePengguna('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->status }}')">
                                                    {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Detail --}}
                                        <button type="button" class="btn-action is-detail" onclick="openDetailModal('{{ $user->id }}')" title="Detail Pengguna">
                                            Detail
                                        </button>

                                        {{-- Hapus (Kecuali Akun Sendiri) --}}
                                        @if($user->id !== auth()->id())
                                            <form id="formDeletePengguna_{{ $user->id }}" action="{{ route('admin.pengguna.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn-action is-delete" onclick="confirmDeletePengguna('{{ $user->id }}', '{{ addslashes($user->name) }}')" title="Hapus Akun">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="admin-pagination-wrap">
                <span class="text-muted">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                </span>
                {{ $users->links() }}
            </div>
        @endif
    </section>

    {{-- MODAL TAMBAH PETUGAS (CRUD Create) --}}
    <div class="admin-modal-backdrop" id="modalCreateBackdrop">
        <div class="admin-modal" id="modalCreate">
            <div class="admin-modal-head">
                <h4>Buat Akun Petugas Baru</h4>
                <button type="button" class="admin-modal-close" onclick="closeCreateUserModal()">&times;</button>
            </div>
            <form action="{{ route('admin.pengguna.store') }}" method="POST">
                @csrf
                <div class="admin-modal-body">
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">
                        Pembuatan akun dengan peran khusus (Dokter Hewan atau Kader) harus dilakukan melalui proses terkontrol oleh Administrator sistem, bukan registrasi publik.
                    </p>

                    <div class="form-group">
                        <label class="form-label" for="createRole">Peran Petugas <span class="text-danger">*</span></label>
                        <select name="role" id="createRole" class="form-control" required>
                            <option value="{{ \App\Models\User::ROLE_DOKTER }}">Dokter Hewan (Pelayanan medis & rekam medis)</option>
                            <option value="{{ \App\Models\User::ROLE_KADER }}">Kader Puskesmas (Pendaftaran, kedatangan, artikel)</option>
                            <option value="{{ \App\Models\User::ROLE_ADMIN }}">Administrator Sistem</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="createName">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="createName" class="form-control" placeholder="Contoh: drh. Maya Indriani" required>
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="createEmail">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="createEmail" class="form-control" placeholder="nama@kawwan.com" required>
                    </div>

                    <div class="form-row-2" style="margin-top: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="createNik">NIK / Nomor Identitas</label>
                            <input type="text" name="nik" id="createNik" class="form-control" placeholder="16 digit angka (opsional)">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="createTelp">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_telepon" id="createTelp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="createAlamat">Alamat Tempat Tinggal / Instansi</label>
                        <textarea name="alamat" id="createAlamat" rows="2" class="form-control" placeholder="Alamat lengkap petugas..."></textarea>
                    </div>

                    <div class="form-row-2" style="margin-top: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="createPassword">Password Akun <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="createPassword" class="form-control" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="createPasswordConf">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="createPasswordConf" class="form-control" placeholder="Ulangi password" required>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-foot">
                    <button type="button" class="btn btn-outline btn-sm" onclick="closeCreateUserModal()">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Buat Akun Petugas</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT PENGGUNA (CRUD Update) --}}
    <div class="admin-modal-backdrop" id="modalEditBackdrop">
        <div class="admin-modal admin-modal-lg" id="modalEdit">
            <div class="admin-modal-head">
                <h4>Edit Data Pengguna</h4>
                <button type="button" class="admin-modal-close" onclick="closeEditUserModal()">&times;</button>
            </div>
            <form id="formEditUser" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="admin-modal-body">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="editName">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row-2" style="margin-top: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="editNik">NIK</label>
                            <input type="text" name="nik" id="editNik" class="form-control" placeholder="16 digit angka">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editTelp">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_telepon" id="editTelp" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row-2" style="margin-top: 12px;">
                        <div class="form-group">
                            <label class="form-label" for="editRole">Peran / Role <span class="text-danger">*</span></label>
                            <select name="role" id="editRole" class="form-control" required>
                                <option value="{{ \App\Models\User::ROLE_PEMILIK }}">Pemilik Hewan</option>
                                <option value="{{ \App\Models\User::ROLE_DOKTER }}">Dokter Hewan</option>
                                <option value="{{ \App\Models\User::ROLE_KADER }}">Kader Puskesmas</option>
                                <option value="{{ \App\Models\User::ROLE_ADMIN }}">Administrator</option>
                            </select>
                            <small class="text-muted" id="editRoleHint" style="display: none;">Akun Anda sendiri tidak dapat diubah rolenya.</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="editStatus">Status Akun <span class="text-danger">*</span></label>
                            <select name="status" id="editStatus" class="form-control" required>
                                <option value="{{ \App\Models\User::STATUS_AKTIF }}">Aktif</option>
                                <option value="{{ \App\Models\User::STATUS_MENUNGGU }}">Menunggu Verifikasi</option>
                                <option value="{{ \App\Models\User::STATUS_DITOLAK }}">Ditolak</option>
                                <option value="{{ \App\Models\User::STATUS_NONAKTIF }}">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="editAlamat">Alamat Domisili</label>
                        <textarea name="alamat" id="editAlamat" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="editPassword">Ubah Password (Opsional)</label>
                        <input type="password" name="password" id="editPassword" class="form-control" placeholder="Kosongkan bila tidak ingin mengubah password">
                    </div>
                </div>
                <div class="admin-modal-foot">
                    <button type="button" class="btn btn-outline btn-sm" onclick="closeEditUserModal()">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DETAIL PENGGUNA --}}
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
    function openCreateUserModal() {
        document.getElementById('modalCreateBackdrop').classList.add('is-open');
    }

    function closeCreateUserModal() {
        document.getElementById('modalCreateBackdrop').classList.remove('is-open');
    }

    function openEditUserModal(user) {
        const form = document.getElementById('formEditUser');
        form.action = `/admin/pengguna/${user.id}`;

        document.getElementById('editName').value = user.name || '';
        document.getElementById('editEmail').value = user.email || '';
        document.getElementById('editNik').value = user.nik || '';
        document.getElementById('editTelp').value = user.nomor_telepon || '';
        document.getElementById('editAlamat').value = user.alamat || '';
        document.getElementById('editRole').value = user.role || '';
        document.getElementById('editStatus').value = user.status || '';
        document.getElementById('editPassword').value = '';

        const roleSelect = document.getElementById('editRole');
        const roleHint = document.getElementById('editRoleHint');
        if (user.is_self) {
            roleSelect.disabled = true;
            roleHint.style.display = 'block';
        } else {
            roleSelect.disabled = false;
            roleHint.style.display = 'none';
        }

        document.getElementById('modalEditBackdrop').classList.add('is-open');
    }

    function closeEditUserModal() {
        document.getElementById('modalEditBackdrop').classList.remove('is-open');
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
                                <strong>${h.nama}</strong>
                                <span>${h.jenis} (${h.ras}) &bull; ${h.jenis_kelamin}</span>
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

    function confirmChangeRole(selectEl, userId, userName, originalRole) {
        const newRoleText = selectEl.options[selectEl.selectedIndex].text;
        kawwanConfirm({
            title: 'Ubah Peran Pengguna',
            message: `Apakah Anda yakin ingin mengubah peran <strong>${userName}</strong> menjadi <strong>${newRoleText}</strong>?`,
            detail: 'Perubahan peran ini akan langsung mempengaruhi hak akses fitur pengguna di sistem.',
            type: 'warning',
            confirmText: 'Ya, Ubah Peran',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById(`formRole_${userId}`).submit();
            }
        });

        // Jika dibatalkan atau backdrop ditutup, kembalikan ke role semula
        const cancelBtn = document.getElementById('kawwanConfirmCancelBtn');
        const prevHandler = cancelBtn.onclick;
        cancelBtn.onclick = () => {
            selectEl.value = originalRole;
            if (typeof prevHandler === 'function') prevHandler();
        };
    }

    function confirmTogglePengguna(userId, userName, currentStatus) {
        const isAktif = currentStatus === 'aktif';
        kawwanConfirm({
            title: isAktif ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Akun Pengguna',
            message: isAktif
                ? `Apakah Anda yakin ingin menonaktifkan akun <strong>${userName}</strong>? Pengguna tidak akan dapat mengakses sistem.`
                : `Aktifkan kembali akun <strong>${userName}</strong> agar dapat mengakses seluruh layanan?`,
            type: isAktif ? 'warning' : 'success',
            confirmText: isAktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById(`formTogglePengguna_${userId}`).submit();
            }
        });
    }

    function confirmDeletePengguna(userId, userName) {
        kawwanConfirm({
            title: 'Hapus Akun Pengguna',
            message: `PERINGATAN: Apakah Anda yakin ingin menghapus akun <strong>${userName}</strong> secara permanen?`,
            detail: 'Tindakan ini tidak dapat dibatalkan. Seluruh data akun akan dihapus dari basis data.',
            type: 'danger',
            confirmText: 'Ya, Hapus Permanen',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById(`formDeletePengguna_${userId}`).submit();
            }
        });
    }
</script>

</body>
</html>
