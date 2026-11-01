@extends('admin.layout')

@section('title', 'Pengelolaan Pengguna')

@section('content')

    {{-- Header Halaman --}}
    <div class="admin-head">
        <div class="admin-head-info">
            <h1>Pengelolaan <span>Pengguna</span> 👥</h1>
            <p>Kelola data seluruh akun pengguna, atur hak akses / peran, dan buat akun petugas (Dokter & Kader) secara terkontrol.</p>
        </div>
        <div class="admin-head-actions">
            <button type="button" class="btn btn-primary" onclick="openCreateUserModal()">
                <i class="fa-solid fa-user-plus"></i> Tambah Petugas (Dokter/Kader)
            </button>
        </div>
    </div>

    {{-- Summary Role Cards --}}
    <div class="admin-pills-bar" style="margin-bottom: 24px;">
        <div class="admin-pill-item">
            <i class="fa-solid fa-users"></i>
            <span>Total: <strong>{{ $roleCounts['total'] }}</strong> Pengguna</span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-user"></i>
            <span>Pemilik Hewan: <strong>{{ $roleCounts['pemilik'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-user-doctor"></i>
            <span>Dokter Hewan: <strong>{{ $roleCounts['dokter'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-hand-holding-medical"></i>
            <span>Kader Puskesmas: <strong>{{ $roleCounts['kader'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-shield-halved"></i>
            <span>Admin: <strong>{{ $roleCounts['admin'] }}</strong></span>
        </div>
    </div>

    {{-- Main Filter & Data Card --}}
    <section class="admin-card">
        {{-- Search & Filter Toolbar --}}
        <form method="GET" action="{{ route('admin.pengguna.index') }}" class="admin-search-toolbar">
            <div class="toolbar-search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
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
                <div class="admin-empty-icon">
                    <i class="fa-solid fa-users-slash"></i>
                </div>
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
                            <th>Role / Peran</th>
                            <th>Status Akun</th>
                            <th>Peliharaan</th>
                            <th>Waktu Daftar</th>
                            <th style="text-align: right;">Kelola</th>
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
                                        <span><i class="fa-solid fa-envelope"></i> {{ $user->email ?? 'Tanpa email' }}</span>
                                        <small class="text-muted"><i class="fa-solid fa-phone"></i> {{ $user->nomor_telepon ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>
                                    {{-- Form Cepat Ubah Role --}}
                                    @if($user->id === auth()->id())
                                        <span class="admin-user-role-badge is-admin">{{ $user->role_label }}</span>
                                    @else
                                        <form action="{{ route('admin.pengguna.role', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" class="form-control admin-select-sm" onchange="if(confirm('Ubah role {{ $user->name }} menjadi ' + this.options[this.selectedIndex].text + '?')) { this.form.submit(); } else { this.value='{{ $user->role }}'; }">
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
                                        <span class="admin-badge-count"><i class="fa-solid fa-paw text-orange"></i> {{ $user->hewan_count }} ekor</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-date-text">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                                </td>
                                <td>
                                    <div class="admin-actions-cell" style="justify-content: flex-end;">
                                        {{-- Toggle Status --}}
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.verifikasi.toggle', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $user->status === \App\Models\User::STATUS_AKTIF ? "Nonaktifkan akun {$user->name}?" : "Aktifkan akun {$user->name}?" }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-action {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'is-deactivate' : 'is-activate' }}" title="{{ $user->status === \App\Models\User::STATUS_AKTIF ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fa-solid {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'fa-ban' : 'fa-unlock' }}"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Detail --}}
                                        <button type="button" class="btn btn-action is-detail" onclick="openDetailModal('{{ $user->id }}')" title="Detail Pengguna">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        {{-- Hapus (Kecuali Akun Sendiri) --}}
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.pengguna.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun {{ $user->name }} secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action is-delete" title="Hapus Akun">
                                                    <i class="fa-solid fa-trash-can"></i>
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
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </section>

    {{-- MODAL TAMBAH PETUGAS (DOKTER / KADER) --}}
    <div class="admin-modal-backdrop" id="modalCreateBackdrop">
        <div class="admin-modal" id="modalCreate">
            <div class="admin-modal-head">
                <h4><i class="fa-solid fa-user-plus text-orange"></i> Buat Akun Petugas Baru</h4>
                <button type="button" class="admin-modal-close" onclick="closeCreateUserModal()">&times;</button>
            </div>
            <form action="{{ route('admin.pengguna.store') }}" method="POST">
                @csrf
                <div class="admin-modal-body">
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">
                        Pembuatan akun dengan peran khusus (Dokter Hewan atau Kader) harus dilakukan melalui proses terkontrol oleh Administrator sistem, bukan registrasi publik.
                    </p>

                    <div class="form-group">
                        <label class="form-label" for="createRole">Peran / Role Petugas <span class="text-danger">*</span></label>
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

    {{-- MODAL DETAIL PENGGUNA --}}
    <div class="admin-modal-backdrop" id="modalDetailBackdrop">
        <div class="admin-modal admin-modal-lg" id="modalDetail">
            <div class="admin-modal-head">
                <h4><i class="fa-solid fa-id-card-clip text-orange"></i> Detail Lengkap Pengguna</h4>
                <button type="button" class="admin-modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="admin-modal-body" id="modalDetailContent">
                <div class="admin-modal-loading">
                    <i class="fa-solid fa-spinner fa-spin"></i> Memuat data pengguna...
                </div>
            </div>
            <div class="admin-modal-foot">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openCreateUserModal() {
        document.getElementById('modalCreateBackdrop').classList.add('is-open');
    }

    function closeCreateUserModal() {
        document.getElementById('modalCreateBackdrop').classList.remove('is-open');
    }

    async function openDetailModal(userId) {
        const backdrop = document.getElementById('modalDetailBackdrop');
        const content = document.getElementById('modalDetailContent');
        backdrop.classList.add('is-open');
        content.innerHTML = '<div class="admin-modal-loading"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data pengguna...</div>';

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
                                <strong><i class="fa-solid fa-paw text-orange"></i> ${h.nama}</strong>
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
                            <span class="detail-label"><i class="fa-solid fa-id-card"></i> NIK</span>
                            <span class="detail-val"><code>${data.nik}</code></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fa-solid fa-phone"></i> Nomor Telepon</span>
                            <span class="detail-val">${data.nomor_telepon}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fa-solid fa-envelope"></i> Email</span>
                            <span class="detail-val">${data.email}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label"><i class="fa-solid fa-calendar-day"></i> Terdaftar Sejak</span>
                            <span class="detail-val">${data.created_at_formatted}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <span class="detail-label"><i class="fa-solid fa-location-dot"></i> Alamat Domisili</span>
                        <p class="detail-address-box">${data.alamat}</p>
                    </div>

                    ${data.catatan_verifikasi ? `
                        <div class="detail-section">
                            <span class="detail-label"><i class="fa-solid fa-clipboard-check"></i> Catatan Verifikasi</span>
                            <p class="detail-note-box">${data.catatan_verifikasi}</p>
                            ${data.verified_by_name ? `<small class="text-muted">Diverifikasi oleh: ${data.verified_by_name} pada ${data.verified_at_formatted}</small>` : ''}
                        </div>
                    ` : ''}

                    <div class="detail-section" style="margin-top: 14px;">
                        <span class="detail-label"><i class="fa-solid fa-dog"></i> Hewan Peliharaan Terdaftar (${data.hewan.length})</span>
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
@endpush
