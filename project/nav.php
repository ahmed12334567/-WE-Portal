<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 p-2 shadow-sm" style="width: 36px; height: 36px; background: var(--primary-gradient) !important;">
                <i class="bi bi-mortarboard-fill fs-5"></i>
            </span>
            <span>WE Portal</span>
        </a>

        <div class="d-flex align-items-center gap-2 order-lg-last ms-2">
            <button id="themeToggleBtn" class="theme-toggle-btn" type="button" aria-label="Toggle dark/light mode" title="Toggle theme">
                <i id="themeIcon" class="bi bi-moon-stars-fill"></i>
            </button>

            <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-secondary"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto gap-2">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'student.php' ? 'active' : '' ?>" href="student.php">
                        <i class="bi bi-search"></i>
                        <span>Check Result</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i>
                        <span>Admin Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    (function() {
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.className = 'bi bi-sun-fill text-warning';
                } else {
                    themeIcon.className = 'bi bi-moon-stars-fill text-dark';
                }
            }
        }

        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
        applyTheme(currentTheme);

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', function() {
                const activeTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(activeTheme);
            });
        }
    })();
</script>
