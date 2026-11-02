@extends('admin.layout')

@section('title', 'Verifikasi Pengguna')

@section('content')

    {{-- Header Halaman --}}
    <div class="admin-head">
        <div class="admin-head-info">
            <h1>Verifikasi <span>Pengguna</span></h1>
            <p>Validasi identitas (NIK) dan kelayakan pendaftar untuk menjamin keamanan akses layanan kesehatan hewan.</p>
        </div>
        <div class="admin-head-actions">
            @if ($counter['menunggu'] > 0)
                <span class="admin-badge-urgent" style="font-size: 13px; padding: 8px 16px;">
                    {{ $counter['menunggu'] }} Menunggu Persetujuan
                </span>
            @endif
        </div>
    </div>

    {{-- KPI Cards Ringkasan Verifikasi (Clean, No Icon) --}}
    <div class="admin-kpi-grid">
        <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi']) }}" class="admin-kpi-card is-pending {{ $statusFilter === 'menunggu_verifikasi' ? 'is-active' : '' }}">
            <div class="admin-kpi-data">
                <span class="admin-kpi-val">{{ $counter['menunggu'] }}</span>
                <span class="admin-kpi-title">Menunggu Verifikasi</span>
            </div>
            <span class="admin-kpi-arrow">&rarr;</span>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['status' => 'aktif']) }}" class="admin-kpi-card is-active-user {{ $statusFilter === 'aktif' ? 'is-active' : '' }}">
            <div class="admin-kpi-data">
                <span class="admin-kpi-val">{{ $counter['aktif'] }}</span>
                <span class="admin-kpi-title">Terverifikasi & Aktif</span>
            </div>
            <span class="admin-kpi-arrow">&rarr;</span>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['status' => 'ditolak']) }}" class="admin-kpi-card is-rejected {{ $statusFilter === 'ditolak' ? 'is-active' : '' }}">
            <div class="admin-kpi-data">
                <span class="admin-kpi-val">{{ $counter['ditolak'] }}</span>
                <span class="admin-kpi-title">Verifikasi Ditolak</span>
            </div>
            <span class="admin-kpi-arrow">&rarr;</span>
        </a>

        <a href="{{ route('admin.verifikasi.index', ['status' => 'nonaktif']) }}" class="admin-kpi-card is-inactive {{ $statusFilter === 'nonaktif' ? 'is-active' : '' }}">
            <div class="admin-kpi-data">
                <span class="admin-kpi-val">{{ $counter['nonaktif'] }}</span>
                <span class="admin-kpi-title">Akun Dinonaktifkan</span>
            </div>
            <span class="admin-kpi-arrow">&rarr;</span>
        </a>
    </div>

    {{-- Filter Tabs & Search Card --}}
    <section class="admin-card">
        {{-- Navigation Tabs --}}
        <div class="admin-filter-tabs">
            <a href="{{ route('admin.verifikasi.index', ['status' => 'semua', 'role' => $roleFilter, 'search' => $search]) }}" class="tab-link {{ $statusFilter === 'semua' ? 'active' : '' }}">
                Semua Status <span class="tab-badge">{{ $counter['total'] }}</span>
            </a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi', 'role' => $roleFilter, 'search' => $search]) }}" class="tab-link {{ $statusFilter === 'menunggu_verifikasi' ? 'active' : '' }}">
                Menunggu Verifikasi <span class="tab-badge is-orange">{{ $counter['menunggu'] }}</span>
            </a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'aktif', 'role' => $roleFilter, 'search' => $search]) }}" class="tab-link {{ $statusFilter === 'aktif' ? 'active' : '' }}">
                Terverifikasi <span class="tab-badge is-green">{{ $counter['aktif'] }}</span>
            </a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'ditolak', 'role' => $roleFilter, 'search' => $search]) }}" class="tab-link {{ $statusFilter === 'ditolak' ? 'active' : '' }}">
                Ditolak <span class="tab-badge is-red">{{ $counter['ditolak'] }}</span>
            </a>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'nonaktif', 'role' => $roleFilter, 'search' => $search]) }}" class="tab-link {{ $statusFilter === 'nonaktif' ? 'active' : '' }}">
                Nonaktif <span class="tab-badge">{{ $counter['nonaktif'] }}</span>
            </a>
        </div>

        {{-- Search & Filter Toolbar --}}
        <form method="GET" action="{{ route('admin.verifikasi.index') }}" class="admin-search-toolbar">
            <input type="hidden" name="status" value="{{ $statusFilter }}">

            <div class="toolbar-search-input">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIK, email, atau nomor HP..." class="form-control">
                @if($search)
                    <a href="{{ route('admin.verifikasi.index', ['status' => $statusFilter, 'role' => $roleFilter]) }}" class="search-clear-btn" title="Hapus pencarian">&times;</a>
                @endif
            </div>

            <div class="toolbar-filter-select">
                <select name="role" class="form-control" onchange="this.form.submit()">
                    <option value="semua" {{ $roleFilter === 'semua' ? 'selected' : '' }}>Semua Role</option>
                    <option value="pemilik" {{ $roleFilter === 'pemilik' ? 'selected' : '' }}>Pemilik Hewan</option>
                    <option value="dokter" {{ $roleFilter === 'dokter' ? 'selected' : '' }}>Dokter Hewan</option>
                    <option value="kader" {{ $roleFilter === 'kader' ? 'selected' : '' }}>Kader Puskesmas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            @if($search !== '' || $roleFilter !== 'semua' || $statusFilter !== 'semua')
                <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-outline btn-sm">Reset</a>
            @endif
        </form>

        {{-- Form Batch Approval --}}
        <form id="formBatchApprove" action="{{ route('admin.verifikasi.batch-approve') }}" method="POST">
            @csrf

            {{-- Floating Batch Action Bar --}}
            <div class="admin-batch-bar" id="adminBatchBar" style="display: none;">
                <div class="batch-bar-info">
                    <span><strong id="batchSelectedCount">0</strong> pengguna dipilih</span>
                </div>
                <div class="batch-bar-actions">
                    <button type="button" class="btn btn-primary btn-sm" onclick="confirmBatchApprove()">
                        Setujui Terpilih
                    </button>
                </div>
            </div>

            {{-- Data Verifikasi (Clean Card Layout) --}}
            @if ($penggunaList->isEmpty())
                <div class="admin-empty-state">
                    <h4>Tidak Ditemukan Pengguna</h4>
                    <p>Tidak ada data pengguna yang sesuai dengan kriteria filter atau pencarian Anda.</p>
                </div>
            @else
                <div class="admin-card-list-header">
                    <label class="admin-check-all-label">
                        <input type="checkbox" id="checkAll" aria-label="Pilih semua">
                        <span>Pilih Semua</span>
                    </label>
                    <span class="text-muted" style="font-size: 13px;">
                        Menampilkan {{ $penggunaList->firstItem() ?? 0 }}–{{ $penggunaList->lastItem() ?? 0 }} dari {{ $penggunaList->total() }} pengguna
                    </span>
                </div>

                <div class="admin-user-card-list">
                    @foreach ($penggunaList as $user)
                        <div class="admin-user-card {{ $user->status === \App\Models\User::STATUS_MENUNGGU ? 'is-pending' : '' }}">
                            {{-- Card Header: Avatar + Identity + Badges --}}
                            <div class="admin-user-card-header">
                                <div class="admin-user-card-check">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox" onchange="updateBatchBar()">
                                </div>
                                <div class="admin-user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div class="admin-user-card-identity">
                                    <span class="admin-user-fullname">{{ $user->name }}</span>
                                    <div class="admin-user-card-badges">
                                        <span class="admin-user-role-badge is-{{ str_replace(' ', '_', strtolower($user->role)) }}">
                                            {{ $user->role_label }}
                                        </span>
                                        <span class="admin-status-badge {{ $user->status_badge_class }}">
                                            {{ $user->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Body: Info Grid --}}
                            <div class="admin-user-card-body">
                                <div class="admin-user-card-info">
                                    <div class="card-info-item">
                                        <div>
                                            <small>NIK</small>
                                            <span>
                                                <code>{{ $user->nik ?? 'Belum ada NIK' }}</code>
                                                @if($user->nik)
                                                    <button type="button" class="btn-copy-nik" onclick="copyToClipboard('{{ $user->nik }}', this)" title="Salin NIK">
                                                        Salin
                                                    </button>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-info-item">
                                        <div>
                                            <small>Nomor Telepon</small>
                                            <span>{{ $user->nomor_telepon ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="card-info-item">
                                        <div>
                                            <small>Alamat Email</small>
                                            <span>{{ $user->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="card-info-item">
                                        <div>
                                            <small>Waktu Terdaftar</small>
                                            <span>{{ $user->created_at ? $user->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="card-info-item">
                                        <div>
                                            <small>Hewan Terdaftar</small>
                                            <span>{{ $user->hewan_count }} ekor</span>
                                        </div>
                                    </div>
                                    <div class="card-info-item">
                                        <div>
                                            <small>Alamat Domisili</small>
                                            <span title="{{ $user->alamat }}">{{ Str::limit($user->alamat ?? '-', 50) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($user->catatan_verifikasi)
                                    <div class="admin-user-card-note">
                                        <span><strong>Catatan:</strong> {{ Str::limit($user->catatan_verifikasi, 100) }}</span>
                                        @if($user->verifiedBy)
                                            <small class="text-muted">— Diverifikasi oleh: {{ $user->verifiedBy->name }}</small>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Card Footer: Action Buttons (Clean Text) --}}
                            <div class="admin-user-card-actions">
                                @if($user->status !== \App\Models\User::STATUS_AKTIF)
                                    <button type="button" class="btn btn-action is-approve" onclick="confirmApprove('{{ $user->id }}', '{{ addslashes($user->name) }}')" title="Setujui Verifikasi">
                                        Setujui
                                    </button>
                                @endif

                                @if($user->status === \App\Models\User::STATUS_MENUNGGU)
                                    <button type="button" class="btn btn-action is-reject" onclick="openRejectModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->nik }}')" title="Tolak Verifikasi">
                                        Tolak
                                    </button>
                                @endif

                                @if($user->id !== auth()->id())
                                    <form id="formToggleVerifikasi_{{ $user->id }}" action="{{ route('admin.verifikasi.toggle', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="button" class="btn btn-action {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'is-deactivate' : 'is-activate' }}" onclick="confirmToggleVerifikasi('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->status }}')">
                                            {{ $user->status === \App\Models\User::STATUS_AKTIF ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="btn btn-action is-detail" onclick="openDetailModal('{{ $user->id }}')" title="Lihat Detail">
                                    Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links (Clean) --}}
                <div class="admin-pagination-wrap">
                    <span class="text-muted">
                        Menampilkan {{ $penggunaList->firstItem() ?? 0 }}–{{ $penggunaList->lastItem() ?? 0 }} dari {{ $penggunaList->total() }} pengguna
                    </span>
                    {{ $penggunaList->links('vendor.pagination.custom') }}
                </div>
            @endif
        </form>
    </section>

    {{-- MODAL KONFIRMASI SETUJUI (Clean) --}}
    <div class="admin-modal-backdrop" id="modalApproveBackdrop">
        <div class="admin-modal" id="modalApprove">
            <div class="admin-modal-head">
                <h4>Verifikasi & Setujui Akun</h4>
                <button type="button" class="admin-modal-close" onclick="closeApproveModal()">&times;</button>
            </div>
            <form id="formApprove" action="" method="POST">
                @csrf
                <div class="admin-modal-body">
                    <p class="admin-modal-desc">
                        Apakah Anda yakin ingin memverifikasi akun pengguna <strong id="approveUserName">-</strong>?
                    </p>
                    <p class="text-muted" style="font-size: 13px;">
                        Setelah disetujui, akun ini akan langsung aktif dan dapat menggunakan semua fitur pendaftaran hewan serta pemesanan layanan kesehatan.
                    </p>
                    <div class="form-group" style="margin-top: 12px;">
                        <label class="form-label" for="approveCatatan">Catatan Verifikasi (Opsional):</label>
                        <input type="text" name="catatan" id="approveCatatan" class="form-control" placeholder="Contoh: Identitas KTP dan NIK telah diverifikasi valid">
                    </div>
                </div>
                <div class="admin-modal-foot">
                    <button type="button" class="btn btn-outline btn-sm" onclick="closeApproveModal()">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Ya, Setujui Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

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

                    <label class="form-label">Pilih Alasan Cepat:</label>
                    <div class="admin-reason-presets">
                        <button type="button" class="preset-chip" onclick="setRejectReason('Format NIK tidak valid atau tidak sesuai standar kependudukan (16 digit angka).')">NIK tidak valid</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Domisili/alamat tidak berada di wilayah kerja operasional Puskeswan.')">Luar Wilayah</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Nomor kontak tidak dapat dihubungi untuk konfirmasi data.')">Kontak Tidak Aktif</button>
                        <button type="button" class="preset-chip" onclick="setRejectReason('Identitas terindikasi duplikasi dengan akun lain yang telah terdaftar.')">Akun Duplikat</button>
                    </div>

                    <div class="form-group" style="margin-top: 14px;">
                        <label class="form-label" for="rejectCatatan">Catatan / Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" id="rejectCatatan" rows="3" class="form-control" placeholder="Tuliskan alasan penolakan secara jelas agar pengguna mengetahui kendala identitasnya..." required></textarea>
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

@endsection

@push('scripts')
<script>
    function confirmApprove(userId, userName) {
        const form = document.getElementById('formApprove');
        form.action = `/admin/verifikasi/${userId}/approve`;
        document.getElementById('approveUserName').textContent = userName;
        document.getElementById('approveCatatan').value = 'Verifikasi NIK dan identitas disetujui oleh Administrator.';
        document.getElementById('modalApproveBackdrop').classList.add('is-open');
    }

    function closeApproveModal() {
        document.getElementById('modalApproveBackdrop').classList.remove('is-open');
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

    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const boxes = document.querySelectorAll('.user-checkbox');
            boxes.forEach(box => box.checked = checkAll.checked);
            updateBatchBar();
        });
    }

    function updateBatchBar() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const bar = document.getElementById('adminBatchBar');
        const countSpan = document.getElementById('batchSelectedCount');
        if (checked.length > 0) {
            bar.style.display = 'flex';
            countSpan.textContent = checked.length;
        } else {
            bar.style.display = 'none';
        }
    }

    function confirmBatchApprove() {
        const count = document.querySelectorAll('.user-checkbox:checked').length;
        if (count === 0) {
            kawwanToast('Pilih setidaknya satu pengguna terlebih dahulu.', 'warning');
            return;
        }

        kawwanConfirm({
            title: 'Verifikasi Massal Pengguna',
            message: `Apakah Anda yakin ingin memverifikasi dan mengaktifkan <strong>${count} pengguna</strong> terpilih secara massal?`,
            detail: 'Semua akun yang dipilih akan langsung aktif dan status verifikasi NIK disetujui.',
            type: 'success',
            confirmText: 'Ya, Verifikasi Semua',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById('formBatchApprove').submit();
            }
        });
    }

    function confirmToggleVerifikasi(userId, userName, currentStatus) {
        const isAktif = currentStatus === 'aktif';
        kawwanConfirm({
            title: isAktif ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Kembali Akun',
            message: isAktif
                ? `Apakah Anda yakin ingin menonaktifkan akun <strong>${userName}</strong>? Pengguna tidak akan dapat mengakses layanan selama akun dinonaktifkan.`
                : `Aktifkan kembali akun <strong>${userName}</strong> agar dapat mengakses seluruh layanan Puskeswan?`,
            type: isAktif ? 'warning' : 'success',
            confirmText: isAktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
            cancelText: 'Batal',
            onConfirm: () => {
                document.getElementById(`formToggleVerifikasi_${userId}`).submit();
            }
        });
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const original = btn.textContent;
            btn.textContent = 'Tersalin!';
            kawwanToast(`NIK ${text} berhasil disalin ke clipboard!`, 'info');
            setTimeout(() => { btn.textContent = original; }, 1500);
        });
    }
</script>
@endpush
