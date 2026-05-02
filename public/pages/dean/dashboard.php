<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('dean');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <script>try { if (localStorage.getItem('theme') === 'light') document.documentElement.classList.add('light-mode'); } catch (e) {}</script>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dean Dashboard - HOPE Evaluation System</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/variables.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/base.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/layout.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/components.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/dashboards.css">
</head>
<body class="dean-dashboard">
<div class="admin-layout" id="dashboardLayout">
    <aside class="sidebar" id="dashboardSidebar" aria-label="Dean navigation">
        <div class="sidebar-header">
            <div class="sidebar-logo" title="HOPE">HOPE</div>
            <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-expanded="true" aria-controls="dashboardSidebar" title="Collapse sidebar">
                <svg class="sidebar-collapse-btn__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
        </div>
        <nav class="nav-links">
            <button type="button" class="nav-item active" data-section="overview" aria-current="page">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Overview</span>
            </button>
            <button type="button" class="nav-item" data-section="evaluations">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Manage Evaluations</span>
            </button>
            <button type="button" class="nav-item" data-section="results">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Results Viewer</span>
            </button>
        </nav>
        <div class="sidebar-footer">
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            <button type="button" class="nav-item" id="logoutBtn" style="color:#ef4444;" aria-label="Log out">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Logout</span>
            </button>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <h1 class="page-title" id="pageTitle">Dean Overview</h1>
            <div class="top-bar-user">
                <span class="top-bar-name"><?= htmlspecialchars($user['full_name'] ?? '') ?></span>
                <div class="user-avatar" aria-hidden="true"><?= strtoupper(substr($user['full_name'] ?? '?', 0, 2)) ?></div>
            </div>
        </div>

        <p class="dean-scope-note" id="deanScopeNote">Overview metrics and lists below are scoped to <strong>your department</strong> only.</p>

        <section id="overview" class="section-content active" aria-labelledby="pageTitle">
            <div class="stats-grid" id="statsGrid">
                <p style="color:var(--text-secondary);">Loading…</p>
            </div>
            <div class="analytics-grid">
                <div class="analytics-card">
                    <h3 class="analytics-card__title">Instructor performance (your department)</h3>
                    <div id="deptInstructors"><p style="color:var(--text-secondary);">Loading…</p></div>
                </div>
                <div class="analytics-card">
                    <h3 class="analytics-card__title">Recent submissions</h3>
                    <div id="recentSubs"><p style="color:var(--text-secondary);">Loading…</p></div>
                </div>
            </div>
        </section>

        <section id="evaluations" class="section-content" aria-hidden="true">
            <div class="section-toolbar">
                <div>
                    <h2 class="section-heading">Evaluation sheets</h2>
                    <p class="section-subtitle">Create and advance the lifecycle for evaluations in your department.</p>
                </div>
                <button type="button" class="dean-create-cta" id="openCreateModalBtn">
                    <span class="dean-create-cta__icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    Create evaluation
                </button>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Status</th>
                            <th>Submissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="evalTableBody">
                        <tr><td colspan="6" style="text-align:center;">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="results" class="section-content" aria-hidden="true">
            <h2 class="section-heading">Results viewer</h2>
            <p class="dean-scope-note">Select an evaluation to see per-question averages and anonymized comments.</p>
            <div class="form-group" style="margin-bottom:24px;max-width:420px;">
                <label class="form-label" for="resultSheetSelect">Evaluation</label>
                <select class="form-select" id="resultSheetSelect">
                    <option value="">Select an evaluation…</option>
                </select>
            </div>
            <div id="resultView" class="result-view" hidden>
                <div class="analytics-grid">
                    <div class="analytics-card">
                        <h3 class="analytics-card__title">Score distribution (per question)</h3>
                        <div id="scoreDistribution"></div>
                    </div>
                    <div class="analytics-card">
                        <h3 class="analytics-card__title">Student comments</h3>
                        <div id="resultComments"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<div id="createModal" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="createModalTitle" aria-describedby="createModalDesc" aria-hidden="true">
    <div class="dean-modal-panel dean-modal-panel--create">
        <header class="dean-modal-header">
            <div class="dean-modal-header__brand" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div class="dean-modal-header__text">
                <h2 id="createModalTitle" class="dean-modal-title">New evaluation sheet</h2>
                <p id="createModalDesc" class="dean-modal-subtitle">Students will complete this form for the course and instructor you link below.</p>
            </div>
            <button type="button" class="dean-modal-close" id="createModalCloseBtn" aria-label="Close dialog">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </header>

        <div class="dean-modal-scope" role="status">
            <span class="dean-modal-scope__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </span>
            <span class="dean-modal-scope__text">Sheets you create are tied to <strong>your department</strong> (ID <?= (int) ($user['department_id'] ?? 0) ?>). Students only see evaluations for their enrollments.</span>
        </div>

        <form id="createForm" class="dean-create-form">
            <div class="dean-create-form__scroll">
            <div class="dean-form-card">
                <div class="dean-form-section__head">
                    <span class="dean-form-section__glyph" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <h3 class="dean-form-section__title">Basics</h3>
                </div>
                <div class="form-group dean-form-card__field">
                    <label class="form-label" for="createTitle">Title <span class="dean-req" aria-hidden="true">*</span></label>
                    <input class="form-input dean-modal-input" id="createTitle" name="title" required autocomplete="off" placeholder="e.g. CS101 — Fall evaluation">
                    <p class="dean-field-hint">Use a clear name so instructors and reports stay easy to find.</p>
                </div>
                <div class="form-group dean-form-card__field">
                    <label class="form-label" for="createDesc">Description <span class="dean-optional">optional</span></label>
                    <textarea class="form-textarea dean-modal-textarea" id="createDesc" name="description" rows="3" placeholder="What this evaluation covers, or notes for your team…"></textarea>
                </div>
            </div>

            <div class="dean-form-card">
                <div class="dean-form-section__head">
                    <span class="dean-form-section__glyph" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <h3 class="dean-form-section__title">Academic period</h3>
                </div>
                <div class="form-row-2 dean-form-card__grid">
                    <div class="form-group">
                        <label class="form-label" for="createYear">Academic year <span class="dean-req" aria-hidden="true">*</span></label>
                        <select class="form-select dean-modal-select" id="createYear" name="academic_year" required>
                            <?php
                            $cy = (int) date('Y');
                            for ($y = $cy + 3; $y >= max(2018, $cy - 8); $y--) {
                                $sel = $y === $cy ? ' selected' : '';
                                echo '<option value="' . $y . '"' . $sel . '>' . $y . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="createSemester">Semester <span class="dean-req" aria-hidden="true">*</span></label>
                        <select class="form-select dean-modal-select" id="createSemester" name="semester" required>
                            <option value="I">Semester I</option>
                            <option value="II">Semester II</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="dean-form-card">
                <div class="dean-form-section__head">
                    <span class="dean-form-section__glyph" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    <div class="dean-form-section__head-text">
                        <h3 class="dean-form-section__title">Course &amp; instructor</h3>
                        <p class="dean-form-section__lead">Choose a course and instructor from your department.</p>
                    </div>
                </div>
                <div class="form-row-2 dean-form-card__grid">
                    <div class="form-group">
                        <label class="form-label" for="createCourseId">Course <span class="dean-req" aria-hidden="true">*</span></label>
                        <select class="form-select dean-modal-select" id="createCourseId" name="course_id" required aria-describedby="createCourseHint">
                            <option value="">Loading courses…</option>
                        </select>
                        <p class="dean-field-hint" id="createCourseHint">Code and title from your department’s active course catalog.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="createInstructorId">Instructor <span class="dean-req" aria-hidden="true">*</span></label>
                        <select class="form-select dean-modal-select" id="createInstructorId" name="instructor_id" required aria-describedby="createInstructorHint">
                            <option value="">Loading instructors…</option>
                        </select>
                        <p class="dean-field-hint" id="createInstructorHint">Select by name — active instructors in your department only.</p>
                    </div>
                </div>
            </div>

            <div class="dean-form-card dean-form-card--muted">
                <div class="dean-form-section__head">
                    <span class="dean-form-section__glyph dean-form-section__glyph--muted" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div class="dean-form-section__head-text">
                        <h3 class="dean-form-section__title">Response window <span class="dean-optional">optional</span></h3>
                        <p class="dean-form-section__lead">Leave blank to set later, or define when students may submit.</p>
                    </div>
                </div>
                <div class="dean-datetime-row">
                    <div class="form-group dean-datetime-field">
                        <label class="form-label" for="createStart">Opens</label>
                        <input type="datetime-local" class="form-input dean-modal-input dean-input-datetime" id="createStart" name="start_date">
                    </div>
                    <div class="dean-datetime-connector" aria-hidden="true">
                        <span class="dean-datetime-connector__arrow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </span>
                    </div>
                    <div class="form-group dean-datetime-field">
                        <label class="form-label" for="createEnd">Closes</label>
                        <input type="datetime-local" class="form-input dean-modal-input dean-input-datetime" id="createEnd" name="end_date">
                    </div>
                </div>
            </div>
            </div>

            <footer class="dean-modal-footer">
                <button type="button" class="btn btn--secondary" id="cancelCreateBtn">Cancel</button>
                <button type="submit" class="btn-submit dean-create-submit" id="createSubmitBtn">
                    <span class="dean-create-submit__spinner" aria-hidden="true"></span>
                    <span class="dean-create-submit__text">Create sheet</span>
                </button>
            </footer>
        </form>
    </div>
</div>

<div id="deanToast" class="dean-toast" role="alert" aria-live="polite"></div>

<script>
(function () {
    const statusColors = { draft:'#6b7280', scheduled:'#3b82f6', open:'#10b981', closed:'#f59e0b', reviewed:'#8b5cf6', archived:'#6b7280' };
    const nextAction = { draft:'Publish', scheduled:'Open', open:'Close', closed:'Review', reviewed:'Archive' };
    const nextState = { draft:'open', scheduled:'open', open:'closed', closed:'reviewed', reviewed:'archived' };
    const sectionTitles = { overview:'Dean Overview', evaluations:'Manage Evaluations', results:'Results Viewer' };

    function escapeHtml(s) {
        if (s == null || s === '') return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    let toastTimer;
    function showToast(message, type) {
        const el = document.getElementById('deanToast');
        el.textContent = message;
        el.className = 'dean-toast is-visible dean-toast--' + (type === 'success' ? 'success' : 'error');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.classList.remove('is-visible'); }, 4200);
    }

    function loadTheme() {
        const s = localStorage.getItem('theme') || 'dark';
        document.documentElement.classList.toggle('light-mode', s === 'light');
        document.body.classList.remove('light-mode');
        const sun = document.querySelector('.sun-icon');
        const moon = document.querySelector('.moon-icon');
        if (sun) sun.style.display = s === 'light' ? 'none' : 'block';
        if (moon) moon.style.display = s === 'light' ? 'block' : 'none';
    }
    function toggleTheme() {
        const light = document.documentElement.classList.toggle('light-mode');
        document.body.classList.remove('light-mode');
        localStorage.setItem('theme', light ? 'light' : 'dark');
        const sun = document.querySelector('.sun-icon');
        const moon = document.querySelector('.moon-icon');
        if (sun) sun.style.display = light ? 'none' : 'block';
        if (moon) moon.style.display = light ? 'block' : 'none';
    }
    loadTheme();
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);

    function switchSection(id) {
        document.querySelectorAll('.nav-item[data-section]').forEach(function (btn) {
            const on = btn.getAttribute('data-section') === id;
            btn.classList.toggle('active', on);
            if (on) btn.setAttribute('aria-current', 'page');
            else btn.removeAttribute('aria-current');
        });
        document.querySelectorAll('.section-content').forEach(function (s) {
            const on = s.id === id;
            s.classList.toggle('active', on);
            s.setAttribute('aria-hidden', on ? 'false' : 'true');
        });
        document.getElementById('pageTitle').textContent = sectionTitles[id] || 'Dean';
    }

    document.querySelectorAll('.nav-item[data-section]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switchSection(btn.getAttribute('data-section'));
        });
    });

    function setModalOpen(open) {
        const m = document.getElementById('createModal');
        m.classList.toggle('is-open', open);
        m.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (open) {
            function selectStillLoading(sel) {
                return sel && sel.options.length === 1 && /loading/i.test(sel.options[0].textContent);
            }
            if (selectStillLoading(document.getElementById('createCourseId'))) {
                loadDepartmentCoursePicker();
            }
            if (selectStillLoading(document.getElementById('createInstructorId'))) {
                loadDepartmentInstructorPicker();
            }
            document.getElementById('createTitle').focus();
        }
    }
    document.getElementById('openCreateModalBtn').addEventListener('click', function () { setModalOpen(true); });
    document.getElementById('cancelCreateBtn').addEventListener('click', function () { setModalOpen(false); });
    document.getElementById('createModalCloseBtn').addEventListener('click', function () { setModalOpen(false); });
    document.getElementById('createModal').addEventListener('click', function (e) {
        if (e.target.id === 'createModal') setModalOpen(false);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('createModal').classList.contains('is-open')) {
            setModalOpen(false);
        }
    });

    async function loadDashboard() {
        const statsEl = document.getElementById('statsGrid');
        function errStats(msg) {
            statsEl.innerHTML = '<p style="color:var(--error);padding:12px;">' + escapeHtml(msg) + '</p>';
        }
        try {
            const sRes = await fetch('/api/analytics.php?action=system_stats', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            let sData;
            try { sData = await sRes.json(); } catch (x) { errStats('Invalid response from server.'); return; }
            if (sRes.status === 401) { errStats('Session expired. Sign in again.'); return; }
            if (!sRes.ok || !sData.success) { errStats(sData.message || ('HTTP ' + sRes.status)); return; }
            const s = sData.stats;
            statsEl.innerHTML =
                '<div class="stat-card"><div class="stat-info"><h3>Active evaluations</h3><div class="value">' + s.open_evaluations + '</div></div></div>' +
                '<div class="stat-card"><div class="stat-info"><h3>Total submissions</h3><div class="value">' + s.total_submissions + '</div></div></div>' +
                '<div class="stat-card"><div class="stat-info"><h3>Pending reviews</h3><div class="value">' + s.pending_reviews + '</div></div></div>' +
                '<div class="stat-card"><div class="stat-info"><h3>Average score</h3><div class="value">' + s.system_avg_score + '</div></div></div>';
        } catch (e) {
            errStats('Could not load statistics.');
        }

        try {
            const iRes = await fetch('/api/analytics.php?action=all_instructors', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            const iData = await iRes.json();
            const host = document.getElementById('deptInstructors');
            if (iData.success && iData.instructors && iData.instructors.length) {
                host.innerHTML = iData.instructors.map(function (i) {
                    const avg = i.avg_rating != null ? parseFloat(i.avg_rating).toFixed(1) : 'N/A';
                    const cls = i.avg_rating >= 4 ? 'score-high' : (i.avg_rating >= 3 ? 'score-med' : 'score-low');
                    return '<div class="dean-instructor-row"><span class="dean-instructor-name">' + escapeHtml(i.full_name) + '</span>' +
                        '<span class="score-badge ' + cls + '">' + avg + '</span></div>';
                }).join('');
            } else {
                host.innerHTML = '<p style="color:var(--text-muted);text-align:center;">No instructor data yet.</p>';
            }
        } catch (e) {
            document.getElementById('deptInstructors').innerHTML = '<p style="color:var(--error);">Could not load instructors.</p>';
        }

        try {
            const rRes = await fetch('/api/analytics.php?action=recent_submissions&limit=5', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            const rData = await rRes.json();
            const rh = document.getElementById('recentSubs');
            if (rData.success && rData.submissions && rData.submissions.length) {
                rh.innerHTML = rData.submissions.map(function (s) {
                    return '<div class="dean-submission-row"><div class="dean-submission-title">' +
                        escapeHtml(s.course_code) + ' — ' + escapeHtml(s.instructor_name) + '</div>' +
                        '<div class="dean-submission-meta">' + new Date(s.submitted_at).toLocaleString() + '</div></div>';
                }).join('');
            } else {
                rh.innerHTML = '<p style="color:var(--text-muted);text-align:center;">No submissions yet.</p>';
            }
        } catch (e) {
            document.getElementById('recentSubs').innerHTML = '<p style="color:var(--error);">Could not load activity.</p>';
        }

        loadEvalTable();
        loadDepartmentCoursePicker();
        loadDepartmentInstructorPicker();
    }

    function formatCourseOptionLabel(c) {
        var title = (c.title || '').trim();
        if (title.length > 48) {
            title = title.substring(0, 47) + '…';
        }
        var line = (c.code || '') + ' — ' + title;
        if (c.academic_year || c.semester) {
            line += ' · ' + (c.academic_year || '') + ' · ' + (c.semester || '');
        }
        return line;
    }

    async function loadDepartmentCoursePicker() {
        var sel = document.getElementById('createCourseId');
        if (!sel) return;
        try {
            var r = await fetch('/api/analytics.php?action=department_courses', {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            var d = await r.json();
            if (!r.ok || !d.success || !d.courses) {
                sel.innerHTML = '<option value="">' + escapeHtml(d.message || 'Could not load courses') + '</option>';
                return;
            }
            if (!d.courses.length) {
                sel.innerHTML = '<option value="">No active courses in your department</option>';
                return;
            }
            var html = '<option value="">Select a course…</option>';
            d.courses.forEach(function (c) {
                var id = parseInt(c.id, 10);
                if (isNaN(id)) return;
                var label = formatCourseOptionLabel(c);
                html += '<option value="' + id + '">' + escapeHtml(label) + '</option>';
            });
            sel.innerHTML = html;
        } catch (e) {
            sel.innerHTML = '<option value="">Failed to load courses</option>';
        }
    }

    async function loadDepartmentInstructorPicker() {
        var sel = document.getElementById('createInstructorId');
        if (!sel) return;
        try {
            var r = await fetch('/api/analytics.php?action=department_instructors', {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            var d = await r.json();
            if (!r.ok || !d.success || !d.instructors) {
                sel.innerHTML = '<option value="">' + escapeHtml(d.message || 'Could not load instructors') + '</option>';
                return;
            }
            if (!d.instructors.length) {
                sel.innerHTML = '<option value="">No instructors in your department</option>';
                return;
            }
            var html = '<option value="">Select an instructor…</option>';
            d.instructors.forEach(function (u) {
                var id = parseInt(u.id, 10);
                if (!isNaN(id)) {
                    html += '<option value="' + id + '">' + escapeHtml(u.full_name) + '</option>';
                }
            });
            sel.innerHTML = html;
        } catch (e) {
            sel.innerHTML = '<option value="">Failed to load instructors</option>';
        }
    }

    async function loadEvalTable() {
        try {
            const r = await fetch('/api/evaluations.php?action=list', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            const d = await r.json();
            const sel = document.getElementById('resultSheetSelect');
            const body = document.getElementById('evalTableBody');
            if (!d.success) {
                body.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--error);">' + escapeHtml(d.message || 'Could not load') + '</td></tr>';
                return;
            }
            body.innerHTML = d.sheets.map(function (s) {
                const st = s.status || '';
                const bg = statusColors[st] || '#6b7280';
                const actionCell = nextAction[st]
                    ? '<button type="button" class="btn-submit dean-table-action" data-sheet-id="' + s.id + '" data-next-state="' + nextState[st] + '">' + escapeHtml(nextAction[st]) + '</button>'
                    : '—';
                return '<tr><td style="font-weight:600;">' + escapeHtml(s.title) + '</td><td>' + escapeHtml(s.course_code) + '</td><td>' + escapeHtml(s.instructor_name) + '</td>' +
                    '<td><span class="status-badge" style="background:' + bg + '22;color:' + bg + ';border:1px solid ' + bg + '44;">' + escapeHtml(st) + '</span></td>' +
                    '<td>' + (s.submission_count != null ? s.submission_count : '0') + '</td><td>' + actionCell + '</td></tr>';
            }).join('');
            body.querySelectorAll('.dean-table-action').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    transitionSheet(parseInt(btn.getAttribute('data-sheet-id'), 10), btn.getAttribute('data-next-state'));
                });
            });
            sel.innerHTML = '<option value="">Select an evaluation…</option>';
            d.sheets.forEach(function (s) {
                sel.innerHTML += '<option value="' + s.id + '">' + escapeHtml(s.title) + ' (' + escapeHtml(s.status) + ')</option>';
            });
        } catch (e) {
            document.getElementById('evalTableBody').innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--error);">Could not load evaluations.</td></tr>';
        }
    }

    window.transitionSheet = async function (id, state) {
        if (!confirm('Transition this evaluation to "' + state + '"?')) return;
        try {
            const r = await fetch('/api/evaluations.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ action: 'transition', sheet_id: id, new_state: state })
            });
            const d = await r.json();
            if (d.success) showToast(d.message || 'Updated.', 'success');
            else showToast(d.message || 'Update failed.', 'error');
            loadEvalTable();
            loadDashboard();
        } catch (e) {
            showToast('Request failed.', 'error');
        }
    };

    document.getElementById('createForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        var submitBtn = document.getElementById('createSubmitBtn');
        function setCreating(on) {
            submitBtn.disabled = !!on;
            submitBtn.classList.toggle('is-loading', !!on);
            document.getElementById('cancelCreateBtn').disabled = !!on;
            document.getElementById('createModalCloseBtn').disabled = !!on;
        }
        const body = {
            action: 'create',
            title: document.getElementById('createTitle').value,
            description: document.getElementById('createDesc').value,
            academic_year: document.getElementById('createYear').value,
            semester: document.getElementById('createSemester').value,
            course_id: parseInt(document.getElementById('createCourseId').value, 10),
            instructor_id: parseInt(document.getElementById('createInstructorId').value, 10),
            department_id: <?= (int) ($user['department_id'] ?? 0) ?>,
            start_date: document.getElementById('createStart').value || null,
            end_date: document.getElementById('createEnd').value || null
        };
        setCreating(true);
        try {
            const r = await fetch('/api/evaluations.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(body)
            });
            const d = await r.json();
            if (d.success) {
                showToast(d.message || 'Created.', 'success');
                setModalOpen(false);
                e.target.reset();
                loadEvalTable();
                loadDashboard();
            } else {
                showToast(d.message || 'Create failed.', 'error');
            }
        } catch (err) {
            showToast('Could not create evaluation.', 'error');
        } finally {
            setCreating(false);
        }
    });

    document.getElementById('resultSheetSelect').addEventListener('change', async function () {
        const id = this.value;
        const rv = document.getElementById('resultView');
        if (!id) {
            rv.hidden = true;
            return;
        }
        rv.hidden = false;
        try {
            const qr = await fetch('/api/analytics.php?action=sheet_question_stats&sheet_id=' + encodeURIComponent(id), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            const qd = await qr.json();
            const sd = document.getElementById('scoreDistribution');
            if (qd.success && qd.questions && qd.questions.length) {
                sd.innerHTML = qd.questions.map(function (q) {
                    const avg = q.avg_rating != null ? parseFloat(q.avg_rating) : NaN;
                    const n = parseInt(q.response_count, 10) || 0;
                    const pct = !isNaN(avg) && n > 0 ? Math.min(100, Math.max(0, (avg / 5) * 100)) : 0;
                    const fillClass = avg >= 4 ? 'score-high' : (avg >= 3 ? 'score-med' : 'score-low');
                    const snippet = (q.question_text || '').length > 140 ? (q.question_text || '').substring(0, 140) + '…' : (q.question_text || '');
                    return '<div class="dean-q-block"><div class="dean-q-text">' + escapeHtml(snippet) + '</div>' +
                        '<div class="score-bar"><div class="score-fill ' + fillClass + ' score-bar-fill--real" style="width:' + pct.toFixed(1) + '%;"></div></div>' +
                        '<div class="dean-q-meta">Avg <strong>' + (n ? avg.toFixed(2) : '—') + '</strong> / 5 · ' + n + ' responses</div></div>';
                }).join('');
            } else {
                sd.innerHTML = '<p style="color:var(--text-muted);">No response data for this sheet yet.</p>';
            }
        } catch (e) {
            document.getElementById('scoreDistribution').innerHTML = '<p style="color:var(--error);">Could not load scores.</p>';
        }
        try {
            const cr = await fetch('/api/analytics.php?action=sheet_comments&sheet_id=' + encodeURIComponent(id), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' }
            });
            const cd = await cr.json();
            const rc = document.getElementById('resultComments');
            if (cd.success && cd.comments && cd.comments.length) {
                rc.innerHTML = cd.comments.map(function (c) {
                    return '<div class="dean-comment-card"><p class="dean-comment-text">“' + escapeHtml(c.comment_text) + '”</p>' +
                        '<span class="dean-comment-date">' + new Date(c.submitted_at).toLocaleDateString() + '</span></div>';
                }).join('');
            } else {
                rc.innerHTML = '<p style="color:var(--text-muted);">No comments.</p>';
            }
        } catch (e) {
            document.getElementById('resultComments').innerHTML = '<p style="color:var(--error);">Could not load comments.</p>';
        }
    });

    document.getElementById('logoutBtn').addEventListener('click', async function () {
        if (!confirm('Log out?')) return;
        try {
            const res = await fetch('/api/auth.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });
            const data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.success === false) {
                showToast(data.message || 'Could not sign out.', 'error');
                return;
            }
        } catch (e) {
            showToast('Network error.', 'error');
            return;
        }
        window.location.href = 'login.php';
    });

    loadDashboard();
})();
</script>
<script src="<?= htmlspecialchars($ab) ?>/js/dashboard-sidebar.js"></script>
</body>
</html>
