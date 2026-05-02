<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'light') document.documentElement.classList.add('light-mode');
            } catch (e) {}
        })();
    </script>
    <title>HOPE Instructor Evaluation System</title>
    <link rel="stylesheet" href="../../assets/css/variables.css">
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/layout.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <link rel="stylesheet" href="../../assets/css/home.css">
</head>

<body class="home-page">
    <div class="page-bg-animation" aria-hidden="true">
        <div class="pointer-glow"></div>
        <div class="pointer-glow aura-two"></div>
        <div class="pointer-glow aura-three"></div>
        <span class="collapse-flash"></span>
        <span class="float-orb orb-1"></span>
        <span class="float-orb orb-2"></span>
        <span class="float-orb orb-3"></span>
        <span class="float-orb orb-4"></span>
        <span class="float-orb orb-5"></span>
        <span class="float-orb orb-6"></span>
    </div>
    <header class="navbar-container">
        <div class="navbar-inner">
            <a href="#" class="logo">HOPE</a>

            <div class="nav-actions">
                <nav class="nav-links">
                    <!-- Links removed for minimalist UI -->
                </nav>

                <div class="nav-pill-actions">
                    <div class="dropdown">
                        <button class="nav-home-btn" id="loginDropdownBtn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login Portal
                        </button>
                        <div class="dropdown-content">
                            <div class="dropdown-header">
                                <span>Select Portal</span>
                            </div>
                            <a href="../student/login.php">
                                <div class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z" /><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5" /></svg>
                                </div>
                                <div class="dropdown-info">
                                    <span class="dropdown-title">Student Portal</span>
                                    <span class="dropdown-subtitle">Evaluate instructors</span>
                                </div>
                            </a>
                            <a href="../instructor/login.php">
                                <div class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                                </div>
                                <div class="dropdown-info">
                                    <span class="dropdown-title">Instructor Hub</span>
                                    <span class="dropdown-subtitle">View your feedback</span>
                                </div>
                            </a>
                            <a href="../dean/login.php">
                                <div class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                </div>
                                <div class="dropdown-info">
                                    <span class="dropdown-title">Dean Portal</span>
                                    <span class="dropdown-subtitle">Monitor performance</span>
                                </div>
                            </a>
                            <a href="../hr/login.php">
                                <div class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2" /><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" /></svg>
                                </div>
                                <div class="dropdown-info">
                                    <span class="dropdown-title">HR & Personnel</span>
                                    <span class="dropdown-subtitle">System management</span>
                                </div>
                            </a>
                            <a href="../admin/login.php">
                                <div class="dropdown-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /></svg>
                                </div>
                                <div class="dropdown-info">
                                    <span class="dropdown-title">Administrator</span>
                                    <span class="dropdown-subtitle">Global configuration</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <button class="theme-toggle" id="themeToggle" title="Toggle Theme" aria-label="Toggle theme">
                        <svg class="sun-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="12" cy="12" r="4.2" fill="currentColor" opacity="0.95"></circle>
                            <g stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                <line x1="12" y1="2.4" x2="12" y2="5"></line>
                                <line x1="12" y1="19" x2="12" y2="21.6"></line>
                                <line x1="2.4" y1="12" x2="5" y2="12"></line>
                                <line x1="19" y1="12" x2="21.6" y2="12"></line>
                                <line x1="5.2" y1="5.2" x2="7.1" y2="7.1"></line>
                                <line x1="16.9" y1="16.9" x2="18.8" y2="18.8"></line>
                                <line x1="16.9" y1="7.1" x2="18.8" y2="5.2"></line>
                                <line x1="5.2" y1="18.8" x2="7.1" y2="16.9"></line>
                            </g>
                        </svg>
                        <svg class="moon-icon" style="display: none;" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M14.7 2.8a9.3 9.3 0 1 0 6.5 14.9 8.3 8.3 0 0 1-6.5-14.9z" fill="currentColor"></path>
                            <circle cx="15.8" cy="8.1" r="1" fill="rgba(255,255,255,0.35)"></circle>
                            <circle cx="17.9" cy="11.4" r="0.7" fill="rgba(255,255,255,0.3)"></circle>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-bg-accent"></div>

        <h1 class="hero-title fadeInUp" style="animation-delay: 0.4s;">
            Instructor<br>
            Evaluation System
        </h1>

        <p class="hero-subtitle fadeInUp" style="animation-delay: 0.6s;">
            A modern, secure, and anonymous platform empowering the academic community. 
            Experience transparent evaluation driven by real-time data.
        </p>

        <div class="fadeInUp" style="animation-delay: 0.8s;">
            <a href="#" class="hero-cta" id="heroCta">
                Get Started
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>

    </div>

    </main>

    <script>
        // Intersection Observer for Reveal Animations
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Theme Management
        function loadTheme() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            const root = document.documentElement;
            root.classList.toggle('light-mode', savedTheme === 'light');
            document.body.classList.remove('light-mode');
            updateThemeIcon(savedTheme === 'light');
        }

        function toggleTheme() {
            const isLight = document.documentElement.classList.toggle('light-mode');
            document.body.classList.remove('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            updateThemeIcon(isLight);
        }

        function updateThemeIcon(isLight) {
            const sunIcon = document.querySelector('.sun-icon');
            const moonIcon = document.querySelector('.moon-icon');
            if (isLight) {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }

        loadTheme();
        document.getElementById('themeToggle').addEventListener('click', toggleTheme);

        // Dropdown Toggle Logic
        const loginDropdownBtn = document.getElementById('loginDropdownBtn');
        const dropdown = loginDropdownBtn.closest('.dropdown');

        function toggleDropdown(e) {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        }

        loginDropdownBtn.addEventListener('click', toggleDropdown);
        
        const heroCta = document.getElementById('heroCta');
        if (heroCta) {
            heroCta.addEventListener('click', (e) => {
                e.preventDefault();
                toggleDropdown(e);
                // Scroll to top to see the dropdown if needed
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        // Hero title per-letter animation
        const heroTitle = document.querySelector('.hero-title');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (heroTitle && !reduceMotion && heroTitle.dataset.animated !== 'true') {
            const textNodes = Array.from(heroTitle.childNodes).filter((node) => node.nodeType === Node.TEXT_NODE);
            let letterIndex = 0;

            textNodes.forEach((textNode) => {
                const fragment = document.createDocumentFragment();
                const chars = textNode.textContent.split('');

                chars.forEach((ch) => {
                    if (ch === ' ') {
                        fragment.appendChild(document.createTextNode(' '));
                        return;
                    }

                    const span = document.createElement('span');
                    span.className = 'hero-letter';
                    span.style.setProperty('--letter-delay', `${letterIndex * 40}ms`);
                    span.textContent = ch;
                    fragment.appendChild(span);
                    letterIndex += 1;
                });

                textNode.parentNode.replaceChild(fragment, textNode);
            });

            heroTitle.dataset.animated = 'true';
        }

    </script>
</body>

</html>