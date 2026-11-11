document.addEventListener('DOMContentLoaded', () => {
    initLandingMenu();
    setupSidebarToggle('dokterMenuToggle', 'dokterSidebar', 'dokterBackdrop');
    setupSidebarToggle('pemilikMenuToggle', 'pemilikSidebar', 'pemilikBackdrop');
    setupSidebarToggle('adminMenuToggle', 'adminSidebar', 'adminBackdrop');
    initSmoothScroll();
    initPasswordToggles();
    initStatCounters();
    initDokterCharts();
    initAdminCharts();
});

function initLandingMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const navLinks = document.getElementById('navLinks');

    if (!menuToggle || !navLinks) {
        return;
    }

    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = navLinks.classList.toggle('show');

        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute(
            'aria-label',
            isOpen ? 'Tutup menu' : 'Buka menu'
        );

        const icon = menuToggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars', !isOpen);
            icon.classList.toggle('fa-xmark', isOpen);
        }
    });

    navLinks.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('show');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Buka menu');

            const icon = menuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (
            navLinks.classList.contains('show') &&
            !navLinks.contains(e.target) &&
            !menuToggle.contains(e.target)
        ) {
            navLinks.classList.remove('show');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Buka menu');

            const icon = menuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && navLinks.classList.contains('show')) {
            navLinks.classList.remove('show');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Buka menu');

            const icon = menuToggle.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
    });
}

function setupSidebarToggle(toggleId, sidebarId, backdropId) {
    const toggle = document.getElementById(toggleId);
    const sidebar = document.getElementById(sidebarId);
    const backdrop = document.getElementById(backdropId);

    if (!toggle || !sidebar) {
        return;
    }

    const setSidebarState = (open) => {
        sidebar.classList.toggle('is-open', open);
        if (backdrop) {
            backdrop.classList.toggle('is-visible', open);
        }
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Tutup navigasi' : 'Buka navigasi');

        const icon = toggle.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars', !open);
            icon.classList.toggle('fa-xmark', open);
        }

        if (window.innerWidth <= 860) {
            document.body.classList.toggle('sidebar-locked', open);
        }
    };

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = sidebar.classList.contains('is-open');
        setSidebarState(!isOpen);
    });

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            setSidebarState(false);
        });
    }

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 860) {
                setSidebarState(false);
            }
        });
    });

    document.addEventListener('click', (e) => {
        if (
            sidebar.classList.contains('is-open') &&
            !sidebar.contains(e.target) &&
            !toggle.contains(e.target)
        ) {
            setSidebarState(false);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            setSidebarState(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 860 && sidebar.classList.contains('is-open')) {
            setSidebarState(false);
        }
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (!targetId || targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const navbar = document.querySelector('.navbar');
                const navHeight = navbar ? navbar.offsetHeight : 70;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - navHeight - 10;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth',
                });
            }
        });
    });
}

function initPasswordToggles() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target') || (btn.id === 'togglePassword' ? 'password' : null);
            if (!targetId) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            }
        });
    });

    const legacyButtons = document.querySelectorAll('.kw-password-toggle-btn, .kw-auth-show-btn');
    legacyButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            if (!targetId) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            if (btn.classList.contains('kw-auth-show-btn')) {
                btn.textContent = isPassword ? 'HIDE' : 'SHOW';
            } else {
                btn.classList.toggle('is-revealed', isPassword);
            }
        });
    });
}

function initStatCounters() {
    document.querySelectorAll('.dokter-stat-value[data-count], .pemilik-stat-value[data-count]').forEach((el) => {
        const target = parseInt(el.dataset.count, 10);
        if (Number.isNaN(target) || target === 0) return;

        const duration = 700;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            el.textContent = Math.round(target * progress);
            if (progress < 1) requestAnimationFrame(tick);
        };

        el.textContent = '0';
        requestAnimationFrame(tick);
    });
}

function initDokterCharts() {
    const sumber = document.getElementById('dokterChartData');
    if (!sumber || typeof Chart === 'undefined') return;

    const grafik = JSON.parse(sumber.textContent);

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#81766E';

    const harian = document.getElementById('chartHarian');
    if (harian) {
        new Chart(harian, {
            type: 'line',
            data: {
                labels: grafik.harian.labels,
                datasets: [{
                    label: 'Pemeriksaan',
                    data: grafik.harian.data,
                    borderColor: '#FF6B00',
                    backgroundColor: 'rgba(255, 107, 0, 0.10)',
                    pointBackgroundColor: '#FF6B00',
                    pointRadius: 4,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#F2EBE6' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const hewan = document.getElementById('chartHewan');
    if (hewan) {
        const palet = ['#FF6B00', '#181310', '#F3A66B', '#81766E', '#B84A00', '#F3DCC9'];
        const labels = grafik.hewan.labels;
        const data = grafik.hewan.data.map(Number);
        const total = data.reduce((a, b) => a + b, 0);

        new Chart(hewan, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: labels.map((_, i) => palet[i % palet.length]),
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const nilai = Number(ctx.parsed);
                                const persen = total ? Math.round((nilai / total) * 100) : 0;
                                return ` ${ctx.label}: ${nilai} pemeriksaan (${persen}%)`;
                            },
                        },
                    },
                },
            },
        });
    }
}

function initAdminCharts() {
    const sumber = document.getElementById('adminChartData');
    if (!sumber || typeof Chart === 'undefined') return;

    let grafik;
    try {
        grafik = JSON.parse(sumber.textContent);
    } catch (e) {
        return;
    }

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#81766E';

    // 1. Chart Registrasi & Verifikasi 7 Hari Terakhir
    const ctxRegistrasi = document.getElementById('chartRegistrasi');
    if (ctxRegistrasi && grafik.registrasi) {
        new Chart(ctxRegistrasi, {
            type: 'bar',
            data: {
                labels: grafik.registrasi.labels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Pendaftar Baru',
                        data: grafik.registrasi.pendaftar,
                        backgroundColor: '#F3DCC9',
                        borderColor: '#FF6B00',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        type: 'line',
                        label: 'Diverifikasi',
                        data: grafik.registrasi.terverifikasi,
                        borderColor: '#FF6B00',
                        backgroundColor: 'rgba(255, 107, 0, 0.15)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#FF6B00',
                        pointRadius: 4,
                        fill: false,
                        tension: 0.3,
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 14 }
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#F2EBE6' },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });
    }

    // 2. Chart Komposisi Status Akun
    const ctxStatus = document.getElementById('chartStatus');
    if (ctxStatus && grafik.status) {
        const paletStatus = ['#1E6B37', '#FF6B00', '#DC2626', '#81766E'];
        const total = grafik.status.data.reduce((a, b) => Number(a) + Number(b), 0);

        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: grafik.status.labels,
                datasets: [{
                    data: grafik.status.data,
                    backgroundColor: paletStatus,
                    borderWidth: 3,
                    borderColor: '#FFFFFF',
                    hoverOffset: 6,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const val = Number(ctx.parsed);
                                const pct = total ? Math.round((val / total) * 100) : 0;
                                return ` ${ctx.label}: ${val} pengguna (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('profilForm');
    if (!form) return;

    const input = document.getElementById('profilFoto');
    const preview = document.getElementById('profilFotoPreview');
    const inisial = document.getElementById('profilFotoInisial');
    const editables = form.querySelectorAll('[data-edit]');

    const setEditing = (on) => {
        form.classList.toggle('is-editing', on);
        editables.forEach((el) => { el.disabled = !on; });
    };

    setEditing(form.classList.contains('is-editing'));

    const editBtn = document.getElementById('profilEditBtn');
    if (editBtn) editBtn.addEventListener('click', () => setEditing(true));

    if (input) {
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
            if (inisial) inisial.hidden = true;
        });
    }
});

/* ===== KADER: dropdown hewan mengikuti pemilik ===== */
document.addEventListener('DOMContentLoaded', () => {
    const sumber = document.getElementById('kaderHewanData');
    const pemilik = document.getElementById('kaderPemilik');
    const hewan = document.getElementById('kaderHewan');

    if (!sumber || !pemilik || !hewan) return;

    const data = JSON.parse(sumber.textContent);

    const isiHewan = () => {
        const daftar = data[pemilik.value] || [];
        const terpilih = hewan.dataset.selected || '';

        hewan.innerHTML = '';

        const kosong = document.createElement('option');
        kosong.value = '';
        kosong.textContent = !pemilik.value
            ? 'Pilih pemilik dulu'
            : (daftar.length ? 'Pilih hewan' : 'Pemilik belum punya data hewan');
        hewan.appendChild(kosong);

        daftar.forEach((h) => {
            const opt = document.createElement('option');
            opt.value = h.id;
            opt.textContent = `${h.nama} (${h.jenis})`;
            if (String(h.id) === String(terpilih)) opt.selected = true;
            hewan.appendChild(opt);
        });

        hewan.disabled = daftar.length === 0;
    };

    pemilik.addEventListener('change', () => {
        hewan.dataset.selected = '';
        isiHewan();
    });

    isiHewan();
});


/* ===== KADER: Modal Pembatalan Booking ===== */
(function () {
    let bookingCancelForm = null;

    window.openBookingModal = function (button) {
        bookingCancelForm = button.closest('form');
        const modal = document.getElementById('bookingCancelModal');
        if (modal) modal.classList.add('show');
    };

    window.closeBookingModal = function () {
        const modal = document.getElementById('bookingCancelModal');
        if (modal) modal.classList.remove('show');
        bookingCancelForm = null;
    };

    window.confirmBookingCancel = function () {
        if (bookingCancelForm) {
            bookingCancelForm.submit();
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('bookingCancelModal');
        if (!modal) return;

        modal.addEventListener('click', function (event) {
            if (event.target === this) {
                closeBookingModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeBookingModal();
            }
        });
    });
})();

// ============================================================
// Admin: Global Toast Notification Helper
// ============================================================
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

// ============================================================
// Admin: Global Custom Confirmation Dialog
// ============================================================
window.kawwanConfirm = function({
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    detail = null,
    type = 'primary',
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

// ============================================================
// Admin: Confirm Logout
// ============================================================
window.confirmLogout = function() {
    if (typeof kawwanConfirm !== 'function') return;
    kawwanConfirm({
        title: 'Konfirmasi Keluar',
        message: 'Apakah Anda yakin ingin mengakhiri sesi administrator dan keluar dari sistem?',
        type: 'warning',
        confirmText: 'Ya, Keluar',
        cancelText: 'Tetap di Sini',
        onConfirm: () => {
            const form = document.getElementById('formLogout');
            if (form) form.submit();
        }
    });
};

// ============================================================
// Admin Pengguna: Modal Functions
// ============================================================
(function() {
    if (!document.getElementById('modalCreateBackdrop')) return;

    window.openCreateUserModal = function() {
        document.getElementById('modalCreateBackdrop').classList.add('is-open');
    };
    window.closeCreateUserModal = function() {
        document.getElementById('modalCreateBackdrop').classList.remove('is-open');
    };

    window.openEditUserModal = function(user) {
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
    };
    window.closeEditUserModal = function() {
        document.getElementById('modalEditBackdrop').classList.remove('is-open');
    };

    window.openDetailModal = async function(userId) {
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
                hewanHtml = `<div class="detail-hewan-grid">${data.hewan.map(h => `<div class="detail-hewan-card"><strong>${h.nama}</strong><span>${h.jenis} (${h.ras}) &bull; ${h.jenis_kelamin}</span><small class="text-muted">Umur: ${h.umur}</small></div>`).join('')}</div>`;
            }
            content.innerHTML = `
                <div class="admin-detail-layout">
                    <div class="detail-profile-top">
                        <div class="admin-avatar-lg">${data.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <h3 style="margin:0;">${data.name}</h3>
                            <span class="admin-user-role-badge">${data.role_label}</span>
                            <span class="admin-status-badge ${data.status_badge_class}" style="margin-left:6px;">${data.status_label}</span>
                        </div>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">NIK</span><span class="detail-val"><code>${data.nik}</code></span></div>
                        <div class="detail-item"><span class="detail-label">Nomor Telepon</span><span class="detail-val">${data.nomor_telepon}</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-val">${data.email}</span></div>
                        <div class="detail-item"><span class="detail-label">Terdaftar Sejak</span><span class="detail-val">${data.created_at_formatted}</span></div>
                    </div>
                    <div class="detail-section"><span class="detail-label">Alamat Domisili</span><p class="detail-address-box">${data.alamat}</p></div>
                    ${data.catatan_verifikasi ? `<div class="detail-section"><span class="detail-label">Catatan Verifikasi</span><p class="detail-note-box">${data.catatan_verifikasi}</p>${data.verified_by_name ? `<small class="text-muted">Diverifikasi oleh: ${data.verified_by_name} pada ${data.verified_at_formatted}</small>` : ''}</div>` : ''}
                    <div class="detail-section" style="margin-top:14px;"><span class="detail-label">Hewan Peliharaan Terdaftar (${data.hewan.length})</span>${hewanHtml}</div>
                </div>`;
        } catch (e) {
            content.innerHTML = `<div class="admin-alert is-error">Gagal mengambil data pengguna: ${e.message}</div>`;
        }
    };
    window.closeDetailModal = function() {
        document.getElementById('modalDetailBackdrop').classList.remove('is-open');
    };

    window.confirmChangeRole = function(selectEl, userId, userName, originalRole) {
        const newRoleText = selectEl.options[selectEl.selectedIndex].text;
        kawwanConfirm({
            title: 'Ubah Peran Pengguna',
            message: `Apakah Anda yakin ingin mengubah peran <strong>${userName}</strong> menjadi <strong>${newRoleText}</strong>?`,
            detail: 'Perubahan peran ini akan langsung mempengaruhi hak akses fitur pengguna di sistem.',
            type: 'warning',
            confirmText: 'Ya, Ubah Peran',
            cancelText: 'Batal',
            onConfirm: () => { document.getElementById(`formRole_${userId}`).submit(); }
        });
        const cancelBtn = document.getElementById('kawwanConfirmCancelBtn');
        const prevHandler = cancelBtn.onclick;
        cancelBtn.onclick = () => {
            selectEl.value = originalRole;
            if (typeof prevHandler === 'function') prevHandler();
        };
    };

    window.confirmTogglePengguna = function(userId, userName, currentStatus) {
        const isAktif = currentStatus === 'aktif';
        kawwanConfirm({
            title: isAktif ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Akun Pengguna',
            message: isAktif
                ? `Apakah Anda yakin ingin menonaktifkan akun <strong>${userName}</strong>? Pengguna tidak akan dapat mengakses sistem.`
                : `Aktifkan kembali akun <strong>${userName}</strong> agar dapat mengakses seluruh layanan?`,
            type: isAktif ? 'warning' : 'success',
            confirmText: isAktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
            cancelText: 'Batal',
            onConfirm: () => { document.getElementById(`formTogglePengguna_${userId}`).submit(); }
        });
    };

    window.confirmDeletePengguna = function(userId, userName) {
        kawwanConfirm({
            title: 'Hapus Akun Pengguna',
            message: `PERINGATAN: Apakah Anda yakin ingin menghapus akun <strong>${userName}</strong> secara permanen?`,
            detail: 'Tindakan ini tidak dapat dibatalkan. Seluruh data akun akan dihapus dari basis data.',
            type: 'danger',
            confirmText: 'Ya, Hapus Permanen',
            cancelText: 'Batal',
            onConfirm: () => { document.getElementById(`formDeletePengguna_${userId}`).submit(); }
        });
    };
})();

// ============================================================
// Admin Verifikasi: Modal Functions
// ============================================================
(function() {
    if (!document.getElementById('modalApproveBackdrop')) return;

    window.confirmApprove = function(userId, userName) {
        const form = document.getElementById('formApprove');
        form.action = `/admin/verifikasi/${userId}/approve`;
        document.getElementById('approveUserName').textContent = userName;
        document.getElementById('approveCatatan').value = 'Verifikasi NIK dan identitas disetujui oleh Administrator.';
        document.getElementById('modalApproveBackdrop').classList.add('is-open');
    };
    window.closeApproveModal = function() {
        document.getElementById('modalApproveBackdrop').classList.remove('is-open');
    };

    window.openRejectModal = function(userId, userName, userNik) {
        const form = document.getElementById('formReject');
        form.action = `/admin/verifikasi/${userId}/reject`;
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectUserNik').textContent = userNik || '-';
        document.getElementById('rejectCatatan').value = '';
        document.getElementById('modalRejectBackdrop').classList.add('is-open');
    };
    window.closeRejectModal = function() {
        document.getElementById('modalRejectBackdrop').classList.remove('is-open');
    };

    window.setRejectReason = function(reason) {
        document.getElementById('rejectCatatan').value = reason;
    };

    window.openDetailModal = async function(userId) {
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
                hewanHtml = `<div class="detail-hewan-grid">${data.hewan.map(h => `<div class="detail-hewan-card"><strong>${h.nama}</strong><span>${h.jenis} (${h.ras}) &bull; ${h.jenis_kelamin}</span><small class="text-muted">Umur: ${h.umur}</small></div>`).join('')}</div>`;
            }
            content.innerHTML = `
                <div class="admin-detail-layout">
                    <div class="detail-profile-top">
                        <div class="admin-avatar-lg">${data.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <h3 style="margin:0;">${data.name}</h3>
                            <span class="admin-user-role-badge">${data.role_label}</span>
                            <span class="admin-status-badge ${data.status_badge_class}" style="margin-left:6px;">${data.status_label}</span>
                        </div>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">NIK</span><span class="detail-val"><code>${data.nik}</code></span></div>
                        <div class="detail-item"><span class="detail-label">Nomor Telepon</span><span class="detail-val">${data.nomor_telepon}</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-val">${data.email}</span></div>
                        <div class="detail-item"><span class="detail-label">Terdaftar Sejak</span><span class="detail-val">${data.created_at_formatted}</span></div>
                    </div>
                    <div class="detail-section"><span class="detail-label">Alamat Domisili</span><p class="detail-address-box">${data.alamat}</p></div>
                    ${data.catatan_verifikasi ? `<div class="detail-section"><span class="detail-label">Catatan Verifikasi</span><p class="detail-note-box">${data.catatan_verifikasi}</p>${data.verified_by_name ? `<small class="text-muted">Diverifikasi oleh: ${data.verified_by_name} pada ${data.verified_at_formatted}</small>` : ''}</div>` : ''}
                    <div class="detail-section" style="margin-top:14px;"><span class="detail-label">Hewan Peliharaan Terdaftar (${data.hewan.length})</span>${hewanHtml}</div>
                </div>`;
        } catch (e) {
            content.innerHTML = `<div class="admin-alert is-error">Gagal mengambil data pengguna: ${e.message}</div>`;
        }
    };
    window.closeDetailModal = function() {
        document.getElementById('modalDetailBackdrop').classList.remove('is-open');
    };

    const checkAll = document.getElementById('checkAll');
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            document.querySelectorAll('.user-checkbox').forEach(box => box.checked = checkAll.checked);
            updateBatchBar();
        });
    }

    window.updateBatchBar = function() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const bar = document.getElementById('adminBatchBar');
        const countSpan = document.getElementById('batchSelectedCount');
        if (!bar) return;
        if (checked.length > 0) {
            bar.style.display = 'flex';
            countSpan.textContent = checked.length;
        } else {
            bar.style.display = 'none';
        }
    };

    window.confirmBatchApprove = function() {
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
            onConfirm: () => { document.getElementById('formBatchApprove').submit(); }
        });
    };

    window.confirmToggleVerifikasi = function(userId, userName, currentStatus) {
        const isAktif = currentStatus === 'aktif';
        kawwanConfirm({
            title: isAktif ? 'Nonaktifkan Akun Pengguna' : 'Aktifkan Kembali Akun',
            message: isAktif
                ? `Apakah Anda yakin ingin menonaktifkan akun <strong>${userName}</strong>? Pengguna tidak akan dapat mengakses layanan selama akun dinonaktifkan.`
                : `Aktifkan kembali akun <strong>${userName}</strong> agar dapat mengakses seluruh layanan Puskeswan?`,
            type: isAktif ? 'warning' : 'success',
            confirmText: isAktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
            cancelText: 'Batal',
            onConfirm: () => { document.getElementById(`formToggleVerifikasi_${userId}`).submit(); }
        });
    };

    window.copyToClipboard = function(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const original = btn.textContent;
            btn.textContent = 'Tersalin!';
            kawwanToast(`NIK ${text} berhasil disalin ke clipboard!`, 'info');
            setTimeout(() => { btn.textContent = original; }, 1500);
        });
    };
})();

// ============================================================
// Admin Dashboard: Verifikasi Modal Functions
// ============================================================
(function() {
    if (!document.getElementById('modalRejectBackdrop') && !document.getElementById('formApproveUser_')) return;

    window.confirmApproveDashboard = function(userId, userName) {
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
    };

    window.openRejectModal = window.openRejectModal || function(userId, userName, userNik) {
        const form = document.getElementById('formReject');
        if (!form) return;
        form.action = `/admin/verifikasi/${userId}/reject`;
        document.getElementById('rejectUserName').textContent = userName;
        document.getElementById('rejectUserNik').textContent = userNik || '-';
        document.getElementById('rejectCatatan').value = '';
        document.getElementById('modalRejectBackdrop').classList.add('is-open');
    };

    window.closeRejectModal = window.closeRejectModal || function() {
        const el = document.getElementById('modalRejectBackdrop');
        if (el) el.classList.remove('is-open');
    };

    window.setRejectReason = window.setRejectReason || function(reason) {
        const el = document.getElementById('rejectCatatan');
        if (el) el.value = reason;
    };

    window.openDetailModal = window.openDetailModal || async function(userId) {
        const backdrop = document.getElementById('modalDetailBackdrop');
        const content = document.getElementById('modalDetailContent');
        if (!backdrop) return;
        backdrop.classList.add('is-open');
        content.innerHTML = '<div class="admin-modal-loading">Memuat data pengguna...</div>';
        try {
            const res = await fetch(`/admin/verifikasi/${userId}`);
            if (!res.ok) throw new Error('Gagal memuat data');
            const data = await res.json();
            let hewanHtml = '<p class="text-muted" style="margin: 6px 0;">Belum ada hewan terdaftar.</p>';
            if (data.hewan && data.hewan.length > 0) {
                hewanHtml = `<div class="detail-hewan-grid">${data.hewan.map(h => `<div class="detail-hewan-card"><strong>${h.nama}</strong><span>${h.jenis} (${h.ras}) &bull; ${h.jenis_kelamin}</span><small class="text-muted">Umur: ${h.umur}</small></div>`).join('')}</div>`;
            }
            content.innerHTML = `
                <div class="admin-detail-layout">
                    <div class="detail-profile-top">
                        <div class="admin-avatar-lg">${data.name.charAt(0).toUpperCase()}</div>
                        <div>
                            <h3 style="margin:0;">${data.name}</h3>
                            <span class="admin-user-role-badge">${data.role_label}</span>
                            <span class="admin-status-badge ${data.status_badge_class}" style="margin-left:6px;">${data.status_label}</span>
                        </div>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item"><span class="detail-label">NIK</span><span class="detail-val"><code>${data.nik}</code></span></div>
                        <div class="detail-item"><span class="detail-label">Nomor Telepon</span><span class="detail-val">${data.nomor_telepon}</span></div>
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-val">${data.email}</span></div>
                        <div class="detail-item"><span class="detail-label">Terdaftar Sejak</span><span class="detail-val">${data.created_at_formatted}</span></div>
                    </div>
                    <div class="detail-section"><span class="detail-label">Alamat Domisili</span><p class="detail-address-box">${data.alamat}</p></div>
                    ${data.catatan_verifikasi ? `<div class="detail-section"><span class="detail-label">Catatan Verifikasi</span><p class="detail-note-box">${data.catatan_verifikasi}</p>${data.verified_by_name ? `<small class="text-muted">Diverifikasi oleh: ${data.verified_by_name} pada ${data.verified_at_formatted}</small>` : ''}</div>` : ''}
                    <div class="detail-section" style="margin-top:14px;"><span class="detail-label">Hewan Peliharaan Terdaftar (${data.hewan.length})</span>${hewanHtml}</div>
                </div>`;
        } catch (e) {
            content.innerHTML = `<div class="admin-alert is-error">Gagal mengambil data pengguna: ${e.message}</div>`;
        }
    };

    window.closeDetailModal = window.closeDetailModal || function() {
        const el = document.getElementById('modalDetailBackdrop');
        if (el) el.classList.remove('is-open');
    };
})();
