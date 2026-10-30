document.addEventListener('DOMContentLoaded', () => {
    initLandingMenu();
    setupSidebarToggle('dokterMenuToggle', 'dokterSidebar', 'dokterBackdrop');
    setupSidebarToggle('pemilikMenuToggle', 'pemilikSidebar', 'pemilikBackdrop');
    initSmoothScroll();
    initPasswordToggles();
    initStatCounters();
    initDokterCharts();
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
