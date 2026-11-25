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
    const container = document.getElementById('dokterChartContainer');
    const sumber = document.getElementById('dokterChartData');
    const rawJson = container?.dataset?.chart || sumber?.textContent;
    if (!rawJson || typeof Chart === 'undefined') return;

    const grafik = JSON.parse(rawJson);

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

    if (!pemilik || !hewan) return;

    const rawHewan = pemilik.dataset.hewan || sumber?.textContent;
    if (!rawHewan) return;

    const data = JSON.parse(rawHewan);

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
            opt.textContent = h.ras ? `${h.jenis} (${h.ras})` : h.jenis;
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

/* ===== KADER: modal pembatalan booking & konfirmasi form ===== */
document.addEventListener('DOMContentLoaded', () => {
    let bookingCancelForm = null;
    const modal = document.getElementById('bookingCancelModal');
    const backBtn = document.getElementById('bookingModalBackBtn');
    const confirmBtn = document.getElementById('bookingModalConfirmBtn');

    const openBookingModal = (button) => {
        if (!modal) return;
        bookingCancelForm = button.closest('form');
        modal.classList.add('show');
    };

    const closeBookingModal = () => {
        if (!modal) return;
        modal.classList.remove('show');
        bookingCancelForm = null;
    };

    const confirmBookingCancel = () => {
        if (bookingCancelForm) {
            bookingCancelForm.submit();
        }
    };

    window.openBookingModal = openBookingModal;
    window.closeBookingModal = closeBookingModal;
    window.confirmBookingCancel = confirmBookingCancel;

    document.querySelectorAll('[data-action="open-booking-modal"]').forEach((btn) => {
        btn.addEventListener('click', () => openBookingModal(btn));
    });

    if (backBtn) {
        backBtn.addEventListener('click', closeBookingModal);
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmBookingCancel);
    }

    if (modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeBookingModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeBookingModal();
            }
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            const message = form.getAttribute('data-confirm');
            if (message) {
                event.preventDefault();
                window.kawwanConfirm({
                    title: 'Konfirmasi Tindakan',
                    message: message,
                    type: 'warning',
                    confirmText: 'Ya, Lanjutkan',
                    cancelText: 'Batal',
                    onConfirm: () => {
                        form.dataset.confirmed = 'true';
                        form.submit();
                    },
                });
            }
        });
    });
});

/* ===== PEMILIK: kalkulasi estimasi biaya pemesanan antrean ===== */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pemilikBookingForm');
    const totalEl = document.getElementById('pemilikTotalEstimasi');

    if (!form || !totalEl) return;

    const checkboxes = form.querySelectorAll('input[name="layanan_ids[]"][data-biaya]');

    const hitungTotal = () => {
        let total = 0;
        checkboxes.forEach((cb) => {
            if (cb.checked) {
                total += parseInt(cb.getAttribute('data-biaya') || '0', 10);
            }
        });
        totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    };

    checkboxes.forEach((cb) => {
        cb.addEventListener('change', hitungTotal);
    });

    hitungTotal();
});

/* ===== GLOBAL TOAST & CONFIRM DIALOG HELPERS ===== */
function ensureKawwanOverlayDOM() {
    if (!document.getElementById('kawwanToastContainer')) {
        const toastContainer = document.createElement('div');
        toastContainer.className = 'kawwan-toast-container';
        toastContainer.id = 'kawwanToastContainer';
        document.body.appendChild(toastContainer);
    }

    if (!document.getElementById('kawwanConfirmBackdrop')) {
        const backdrop = document.createElement('div');
        backdrop.className = 'kawwan-dialog-backdrop';
        backdrop.id = 'kawwanConfirmBackdrop';
        backdrop.innerHTML = `
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
        `;
        document.body.appendChild(backdrop);
    }
}

window.kawwanToast = function (message, type = 'success', title = null) {
    ensureKawwanOverlayDOM();
    const container = document.getElementById('kawwanToastContainer');
    if (!container) return;

    const titles = {
        success: 'Berhasil',
        warning: 'Perhatian',
        error: 'Terjadi Kesalahan',
        info: 'Informasi',
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

window.kawwanConfirm = function ({
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    detail = null,
    type = 'primary',
    confirmText = 'Lanjutkan',
    cancelText = 'Batal',
    onConfirm = null,
}) {
    ensureKawwanOverlayDOM();
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
        primary: 'Konfirmasi',
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

function detectCurrentRoleLabel() {
    const roleEl = document.querySelector('.admin-user-role, .dokter-user-role, .pemilik-user-role');
    if (roleEl && roleEl.textContent.trim()) {
        return roleEl.textContent.trim().toLowerCase();
    }
    const path = window.location.pathname;
    if (path.startsWith('/dokter')) return 'dokter hewan';
    if (path.startsWith('/kader')) return 'kader puskesmas';
    if (path.startsWith('/pemilik')) return 'pemilik hewan';
    return 'administrator';
}

window.confirmLogout = function (customRole = null, formElement = null) {
    const roleName = (customRole || detectCurrentRoleLabel()).toLowerCase();
    const targetForm =
        formElement ||
        document.getElementById('formLogout') ||
        document.querySelector('form.admin-logout, form.dokter-logout, form.pemilik-logout');

    window.kawwanConfirm({
        title: 'Konfirmasi Keluar',
        message: `Apakah Anda yakin ingin mengakhiri sesi ${roleName} dan keluar dari sistem?`,
        type: 'warning',
        confirmText: 'Ya, Keluar',
        cancelText: 'Tetap di Sini',
        onConfirm: () => {
            if (targetForm) {
                targetForm.dataset.confirmed = 'true';
                targetForm.submit();
            }
        },
    });
};

document.addEventListener('DOMContentLoaded', () => {
    ensureKawwanOverlayDOM();

    document.querySelectorAll('form.dokter-logout, form.pemilik-logout, form.admin-logout').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            e.preventDefault();
            const roleLabel = form.getAttribute('data-role') || detectCurrentRoleLabel();
            window.confirmLogout(roleLabel, form);
        });
    });
});

/* ===== LANDING PAGE: MODAL DETAIL LAYANAN KAWWAN ===== */
const KAWWAN_LAYANAN_DATA = {
    hewan: {
        tag: "Data Hewan Peliharaan",
        title: "Manajemen Data Hewan",
        desc: "Simpan profil lengkap hewan kesayangan Anda, mulai dari nama, jenis, ras, jenis kelamin, tanggal lahir, hingga ciri fisik. Data ini akan otomatis terhubung dengan setiap proses antrean dan catatan rekam medis dokter.",
        jam: "Akses 24 Jam Mandiri",
        hari: "Tersedia Setiap Hari",
        dokter: "Terkoneksi ke Dokter & Kader",
        akses: "Data Terproteksi & Akurat",
        url: "/pemilik/hewan",
        actionText: "Buka Kelola Data Hewan",
        loginText: "Login untuk Kelola Data",
        points: [
            "Pencatatan identitas resmi anabul untuk kepastian riwayat medis.",
            "Kemudahan mendaftarkan lebih dari satu hewan dalam satu akun.",
            "Mempercepat verifikasi oleh kader saat tiba di klinik/puskesmas.",
            "Pembaruan data hewan kapan saja tanpa birokrasi rumit."
        ]
    },
    antrean: {
        tag: "Pemesanan Jadwal & Antrean",
        title: "Pemesanan Antrean Online",
        desc: "Pilih tanggal, jam operasional, dan dokter spesialis yang bertugas secara real-time. Dapatkan estimasi antrean yang jelas sehingga Anda tidak perlu menunggu lama di ruang tunggu puskeswan.",
        jam: "08.00 - 15.30 WIB",
        hari: "Senin s/d Sabtu",
        dokter: "drh. Amanda / drh. Reza",
        akses: "Sistem Kuota Terintegrasi",
        url: "/pemilik/jadwal",
        actionText: "Lihat Jadwal & Pesan Antrean",
        loginText: "Login untuk Pesan Antrean",
        points: [
            "Melihat kuota pasien yang masih tersedia pada setiap sesi layanan.",
            "Pilih layanan yang diinginkan: Vaksinasi, Pemeriksaan Umum, atau Konsultasi.",
            "Konfirmasi status pemesanan secara transparan dan terdata aman.",
            "Pemberitahuan nomor antrean langsung di perangkat Anda."
        ]
    },
    riwayat: {
        tag: "Catatan Medis & Pemeriksaan",
        title: "Rekam Medis Digital Hewan",
        desc: "Pantau riwayat kesehatan, diagnosa penyakit, tindakan medis, resep obat, serta catatan khusus dari dokter hewan pemeriksa yang tersimpan secara terstruktur dan aman.",
        jam: "Akses Riwayat 24 Jam",
        hari: "Tersedia Kapan Saja",
        dokter: "Diverifikasi Tim Dokter Resmi",
        akses: "Privasi Terjaga Antar Pemilik",
        url: "/pemilik/pemesanan",
        actionText: "Lihat Riwayat Pemeriksaan",
        loginText: "Login untuk Lihat Riwayat",
        points: [
            "Riwayat catatan medis tersimpan permanen dan tidak hilang.",
            "Melihat rincian tindakan dan instruksi perawatan dari dokter.",
            "Mempermudah penanganan saat pemeriksaan berkala atau rujukan.",
            "Hanya bisa diakses oleh pemilik hewan yang bersangkutan."
        ]
    }
};

window.openLayananModal = function(key) {
    const data = KAWWAN_LAYANAN_DATA[key];
    if (!data) return;

    const modal = document.getElementById('modalLayanan');
    if (!modal) return;

    const tagEl = document.getElementById('modalLayananTag');
    const titleEl = document.getElementById('modalLayananTitle');
    const descEl = document.getElementById('modalLayananDesc');
    const jamEl = document.getElementById('modalLayananJam');
    const hariEl = document.getElementById('modalLayananHari');
    const dokterEl = document.getElementById('modalLayananDokter');
    const aksesEl = document.getElementById('modalLayananAkses');

    if (tagEl) tagEl.textContent = data.tag;
    if (titleEl) titleEl.textContent = data.title;
    if (descEl) descEl.textContent = data.desc;
    if (jamEl) jamEl.textContent = data.jam;
    if (hariEl) hariEl.textContent = data.hari;
    if (dokterEl) dokterEl.textContent = data.dokter;
    if (aksesEl) aksesEl.textContent = data.akses;

    const actionBtn = document.getElementById('modalActionBtn');
    const actionText = document.getElementById('modalActionText');
    if (actionBtn && actionText) {
        actionBtn.href = data.url;
        actionText.textContent = data.actionText;
    }

    const loginText = document.getElementById('modalLoginText');
    if (loginText) {
        loginText.textContent = data.loginText || "Login untuk Akses";
    }

    const pointsList = document.getElementById('modalLayananPoints');
    if (pointsList) {
        pointsList.innerHTML = '';
        data.points.forEach(point => {
            const li = document.createElement('li');
            li.textContent = point;
            pointsList.appendChild(li);
        });
    }

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

window.closeLayananModal = function() {
    const modal = document.getElementById('modalLayanan');
    if (modal) {
        modal.classList.remove('active');
    }
    document.body.style.overflow = '';
};

window.confirmLoginFirst = function(fiturName) {
    if (window.kawwanToast) {
        window.kawwanToast(`Silakan login terlebih dahulu untuk mengakses ${fiturName}.`, 'warning', 'Login Diperlukan');
    }
    return true;
};

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        window.closeLayananModal();
    }
});

/* ============================================================
   PEMBAYARAN ONLINE — Toggle metode & info rekening/QR
   ============================================================ */

/** Data rekening / no. e-wallet (sesuaikan di sini) */
const KAWWAN_PAYMENT_INFO = {
    qris: {
        type: 'qris',
    },
    gopay: {
        type: 'transfer',
        label: 'Nomor GoPay',
        no: '0812-3456-7890',
        name: 'KAWWAN Puskesmas',
    },
    ovo: {
        type: 'transfer',
        label: 'Nomor OVO',
        no: '0812-3456-7890',
        name: 'KAWWAN Puskesmas',
    },
    dana: {
        type: 'transfer',
        label: 'Nomor DANA',
        no: '0812-3456-7890',
        name: 'KAWWAN Puskesmas',
    },
    transfer_bca: {
        type: 'transfer',
        label: 'No. Rekening BCA',
        no: '1234567890',
        name: 'KAWWAN Puskesmas',
    },
    transfer_bri: {
        type: 'transfer',
        label: 'No. Rekening BRI',
        no: '0987654321',
        name: 'KAWWAN Puskesmas',
    },
    transfer_mandiri: {
        type: 'transfer',
        label: 'No. Rekening Mandiri',
        no: '1122334455',
        name: 'KAWWAN Puskesmas',
    },
    transfer_bni: {
        type: 'transfer',
        label: 'No. Rekening BNI',
        no: '5544332211',
        name: 'KAWWAN Puskesmas',
    },
};

function togglePayInfo(selectEl, pemesananId) {
    const val = selectEl.value;
    const infoPanel  = document.getElementById('pay-info-'     + pemesananId);
    const qrWrap     = document.getElementById('pay-qr-'       + pemesananId);
    const transWrap  = document.getElementById('pay-transfer-' + pemesananId);
    const rekLabel   = document.getElementById('pay-rek-label-'+ pemesananId);
    const rekNo      = document.getElementById('pay-rek-no-'   + pemesananId);
    const rekName    = document.getElementById('pay-rek-name-' + pemesananId);
    const submitBtn  = document.getElementById('pay-btn-'      + pemesananId);

    if (!infoPanel || !val) {
        if (infoPanel) infoPanel.style.display = 'none';
        if (submitBtn) submitBtn.style.display = 'none';
        return;
    }

    const info = KAWWAN_PAYMENT_INFO[val];
    if (!info) return;

    // Tampilkan panel utama
    infoPanel.style.display = 'block';
    submitBtn.style.display  = 'inline-flex';

    if (info.type === 'qris') {
        qrWrap.style.display    = 'block';
        transWrap.style.display = 'none';
    } else {
        qrWrap.style.display    = 'none';
        transWrap.style.display = 'block';
        rekLabel.textContent    = info.label;
        rekNo.textContent       = info.no;
        rekName.textContent     = 'a.n. ' + info.name;
    }
}

// File input label feedback
document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('pay-file-input')) {
        const form = e.target.closest('form');
        if (!form) return;
        const fnameEl = form.querySelector('.pay-file-name');
        if (fnameEl) {
            fnameEl.textContent = e.target.files[0]
                ? e.target.files[0].name
                : 'Belum ada file dipilih';
        }
    }
});

/* ===== LOGOUT OTOMATIS: keluar jika tidak ada aktivitas (semua role) ===== */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Setelah dialihkan ke halaman login/landing, beri tahu bahwa sesi berakhir.
    //    Bagian ini sengaja di atas pengecekan form logout, karena halaman login
    //    tidak punya form logout.
    if (sessionStorage.getItem('kawwanSesiHabis')) {
        sessionStorage.removeItem('kawwanSesiHabis');
        window.kawwanToast(
            'Sesi Anda berakhir karena tidak ada aktivitas. Silakan masuk kembali.',
            'warning',
            'Sesi Berakhir'
        );
    }

    // 2. Batas diam sebelum keluar otomatis, dalam DETIK (ubah angka ini saja)
    const BATAS_DETIK = 900; // 900 = 15 menit. Untuk tes: 20

    const formKeluar =
        document.getElementById('formLogout') ||
        document.querySelector('form[action$="/logout"]');

    if (!formKeluar) return; // halaman login/landing tidak ikut menghitung

    let timer;
    const mulaiLagi = () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            sessionStorage.setItem('kawwanSesiHabis', '1'); // penanda untuk pesan
            formKeluar.dataset.confirmed = 'true';
            formKeluar.submit();
        }, BATAS_DETIK * 1000);
    };

    ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach((ev) => {
        window.addEventListener(ev, mulaiLagi, { passive: true });
    });

    mulaiLagi();
});