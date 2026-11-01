@extends('admin.layout')

@section('title', 'Dashboard Administrator')

@section('content')

    {{-- Header Sambutan --}}
    <div class="admin-head">
        <div class="admin-head-info">
            <h1>Halo, <span>{{ auth()->user()->name }}</span> 👋</h1>
            <p>Ringkasan data operasional sistem KAWWAN dan antrean verifikasi pengguna.</p>
        </div>
        <div class="admin-head-actions">
            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-primary">
                <i class="fa-solid fa-user-check"></i> Kelola Verifikasi
            </a>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline">
                <i class="fa-solid fa-user-plus"></i> Tambah Petugas
            </a>
        </div>
    </div>

    {{-- Banner Peringatan Verifikasi Tertunda --}}
    @if ($stat['menunggu_verifikasi'] > 0)
        <div class="admin-urgent-banner">
            <div class="admin-urgent-icon">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="admin-urgent-content">
                <strong>Prioritas Tindakan: {{ $stat['menunggu_verifikasi'] }} Pengguna Menunggu Verifikasi NIK</strong>
                <p>Verifikasi identitas diperlukan agar pemilik hewan dapat mendaftarkan hewan peliharaannya dan memesan antrean layanan kesehatan.</p>
            </div>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-primary admin-urgent-btn">
                Tinjau Sekarang <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    @endif

    {{-- Grid Kartu Statistik Utama --}}
    <div class="admin-stats">
        {{-- Stat 1: Total Pengguna --}}
        <div class="admin-stat-card">
            <div class="admin-stat-icon is-near-black">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="admin-stat-body">
                <span class="admin-stat-label">Total Pengguna</span>
                <span class="admin-stat-value" data-count="{{ $stat['total_pengguna'] }}">{{ $stat['total_pengguna'] }}</span>
                <span class="admin-stat-sub">Terdaftar di sistem</span>
            </div>
        </div>

        {{-- Stat 2: Menunggu Verifikasi (Highlight) --}}
        <div class="admin-stat-card is-highlight">
            <div class="admin-stat-icon is-orange">
                <i class="fa-solid fa-user-clock"></i>
            </div>
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
            <div class="admin-stat-icon is-peach">
                <i class="fa-solid fa-paw"></i>
            </div>
            <div class="admin-stat-body">
                <span class="admin-stat-label">Pasien Hewan</span>
                <span class="admin-stat-value" data-count="{{ $stat['total_hewan'] }}">{{ $stat['total_hewan'] }}</span>
                <span class="admin-stat-sub">Tercatat di Puskeswan</span>
            </div>
        </div>

        {{-- Stat 4: Antrean Hari Ini --}}
        <div class="admin-stat-card">
            <div class="admin-stat-icon is-green">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="admin-stat-body">
                <span class="admin-stat-label">Antrean Hari Ini</span>
                <span class="admin-stat-value" data-count="{{ $stat['antrean_hari_ini'] }}">{{ $stat['antrean_hari_ini'] }}</span>
                <span class="admin-stat-sub">{{ $stat['antrean_menunggu'] }} menunggu pelayanan</span>
            </div>
        </div>
    </div>

    {{-- Mini Pills Summary --}}
    <div class="admin-pills-bar">
        <div class="admin-pill-item">
            <i class="fa-solid fa-user"></i>
            <span>Pemilik Hewan: <strong>{{ $stat['total_pemilik'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-user-doctor"></i>
            <span>Dokter Hewan: <strong>{{ $stat['total_dokter'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-hand-holding-medical"></i>
            <span>Kader Puskesmas: <strong>{{ $stat['total_kader'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-circle-check text-green"></i>
            <span>Akun Aktif: <strong>{{ $stat['aktif'] }}</strong></span>
        </div>
        <div class="admin-pill-item">
            <i class="fa-solid fa-calendar-day"></i>
            <span>Jadwal Buka: <strong>{{ $stat['jadwal_aktif'] }}</strong></span>
        </div>
    </div>

    {{-- Section: Prioritas Verifikasi Pengguna --}}
    <section class="admin-card" id="verifikasi-prioritas">
        <div class="admin-card-head">
            <div>
                <h3 class="admin-card-title">
                    <i class="fa-solid fa-shield-halved text-orange"></i> Prioritas Verifikasi Pengguna
                </h3>
                <p class="admin-card-sub">Pendaftar baru yang menunggu validasi identitas NIK untuk mengakses fitur sistem.</p>
            </div>
            <a href="{{ route('admin.verifikasi.index', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-outline btn-sm">
                Lihat Semua Antrean ({{ $stat['menunggu_verifikasi'] }}) <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        @if ($prioritasVerifikasi->isEmpty())
            <div class="admin-empty-state">
                <div class="admin-empty-icon is-success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h4>Semua Pengguna Terverifikasi</h4>
                <p>Saat ini tidak ada antrean verifikasi pengguna baru yang tertunda. Sistem berjalan lancar!</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Pemohon</th>
                            <th>NIK (Nomor Induk)</th>
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
                                        <i class="fa-solid fa-id-card"></i> {{ $user->nik ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-contact-cell">
                                        <span><i class="fa-solid fa-phone"></i> {{ $user->nomor_telepon ?? '-' }}</span>
                                        <span class="text-muted"><i class="fa-solid fa-envelope"></i> {{ $user->email ?? 'Tanpa email' }}</span>
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
                                        {{-- Tombol Setujui Langsung --}}
                                        <form action="{{ route('admin.verifikasi.approve', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Verifikasi dan aktifkan akun {{ $user->name }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-action is-approve" title="Setujui Verifikasi">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak (Buka Modal) --}}
                                        <button type="button" class="btn btn-action is-reject" onclick="openRejectModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->nik }}')" title="Tolak Verifikasi">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>

                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn btn-action is-detail" onclick="openDetailModal('{{ $user->id }}')" title="Lihat Detail Lengkap">
                                            <i class="fa-solid fa-eye"></i>
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
                    <h3 class="admin-card-title"><i class="fa-solid fa-chart-line text-orange"></i> Aktivitas 7 Hari Terakhir</h3>
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
                    <h3 class="admin-card-title"><i class="fa-solid fa-chart-pie text-orange"></i> Komposisi Status Akun</h3>
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
                    <i class="fa-solid fa-clock text-orange"></i> Antrean Pelayanan Hari Ini ({{ now()->translatedFormat('d F Y') }})
                </h3>
                <p class="admin-card-sub">Pantauan reservasi dan kedatangan pemeriksaan hewan.</p>
            </div>
        </div>

        @if ($antreanHariIni->isEmpty())
            <div class="admin-empty-state">
                <div class="admin-empty-icon">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
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
                            <th>Hewan Pasien</th>
                            <th>Jenis & Ras</th>
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
                                <td><strong>{{ $pemesanan->hewan?->nama ?? '-' }}</strong></td>
                                <td>{{ $pemesanan->hewan?->jenis ?? '-' }} ({{ $pemesanan->hewan?->ras ?: '-' }})</td>
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

    {{-- MODAL TOLAK VERIFIKASI --}}
    <div class="admin-modal-backdrop" id="modalRejectBackdrop">
        <div class="admin-modal" id="modalReject">
            <div class="admin-modal-head">
                <h4><i class="fa-solid fa-triangle-exclamation text-danger"></i> Tolak Verifikasi Akun</h4>
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

    {{-- Chart Data JSON Script --}}
    <script id="adminChartData" type="application/json">@json($grafik)</script>

@endsection

@push('scripts')
<script>
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
