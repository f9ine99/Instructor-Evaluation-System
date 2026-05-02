<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('admin');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
$currentUserId = (int) ($user['id'] ?? 0);
$fn = trim((string) ($user['full_name'] ?? ''));
$avatarLetters = $fn !== '' ? strtoupper(substr($fn, 0, 2)) : 'AD';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <script>try { if (localStorage.getItem('theme') === 'light') document.documentElement.classList.add('light-mode'); } catch (e) {}</script>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>System Admin - HOPE Evaluation System</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/variables.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/base.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/layout.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/components.css">
    <link rel="stylesheet" href="<?= htmlspecialchars($ab) ?>/css/dashboards.css">
</head>
<body class="admin-dashboard">
<div class="admin-layout">
    <aside class="sidebar" aria-label="Admin navigation">
        <div class="sidebar-header">
            <div class="sidebar-logo">HOPE</div>
        </div>
        <nav class="nav-links">
            <button type="button" class="nav-item active" data-section="overview" aria-current="page">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Overview</span>
            </button>
            <button type="button" class="nav-item" data-section="manage">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Manage Data</span>
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
        <div class="top-bar" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
            <h1 class="page-title" id="pageTitle">System Overview</h1>
            <div style="display:flex;gap:16px;align-items:center;">
                <span style="color:var(--text-secondary);font-size:14px;"><?= htmlspecialchars($user['full_name'] ?? '') ?></span>
                <div class="user-avatar" style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--accent-primary),var(--accent-secondary));color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;" aria-hidden="true"><?= htmlspecialchars($avatarLetters) ?></div>
            </div>
        </div>

        <section id="overview" class="section-content active" aria-labelledby="pageTitle">
            <div class="stats-grid" id="statsGrid">
                <p style="color:var(--text-secondary);">Loading…</p>
            </div>
        </section>

        <section id="manage" class="section-content" aria-hidden="true">
            <div class="section-toolbar">
                <h2 class="section-heading">Directory</h2>
                <div class="admin-toolbar-actions">
                    <button type="button" class="admin-add-student-cta" id="btnAddStudent" style="display:none;" aria-label="Add a student">
                        <span class="admin-add-student-cta__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </span>
                        <span class="admin-add-student-cta__label">
                            <span class="admin-add-student-cta__title">Add student</span>
                            <span class="admin-add-student-cta__hint">Dept + optional enrollments</span>
                        </span>
                    </button>
                    <button type="button" class="admin-add-instructor-cta" id="btnAddInstructor" style="display:none;" aria-label="Add an instructor">
                        <span class="admin-add-instructor-cta__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </span>
                        <span class="admin-add-instructor-cta__label">
                            <span class="admin-add-instructor-cta__title">Add instructor</span>
                            <span class="admin-add-instructor-cta__hint">Faculty &amp; department</span>
                        </span>
                    </button>
                    <button type="button" class="admin-add-staff-link" id="btnAddStaff" style="display:none;">Add staff (dean / HR / admin)</button>
                    <button type="button" class="btn-submit" id="btnAddCourse" style="display:none;">+ Add course</button>
                    <button type="button" class="btn-submit" id="btnAddDepartment" style="display:none;">+ Add department</button>
                </div>
            </div>
            <div class="tab-group" style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
                <button type="button" class="tab-btn active" data-tab="users">Users</button>
                <button type="button" class="tab-btn" data-tab="courses">Courses</button>
                <button type="button" class="tab-btn" data-tab="departments">Departments</button>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead id="tableHead"></thead>
                    <tbody id="tableBody">
                        <tr><td colspan="7" style="text-align:center;">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- Create user (mode: student | instructor | staff) -->
<div id="modalUser" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalUserTitle" aria-hidden="true">
    <div class="admin-modal-surface admin-modal-surface--user">
        <h2 id="modalUserTitle">Add student</h2>
        <p class="admin-modal-lead" id="modalUserLead">Students belong to a department; you can enroll them in active courses in that department (enrollments).</p>
        <form id="formCreateUser">
            <input type="hidden" id="cuUserMode" value="student" autocomplete="off">
            <input type="hidden" id="cuRole" name="role" value="student">

            <div class="form-group">
                <label class="form-label" for="cuUsername">Username <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="cuUsername" name="username" required autocomplete="off" maxlength="50">
                <p class="admin-field-hint" id="cuUsernameHint">Often a student ID (e.g. 2021001). Unique across the system.</p>
            </div>
            <div class="form-group">
                <label class="form-label" for="cuPassword">Password <span style="color:var(--error);">*</span></label>
                <input type="password" class="form-input" id="cuPassword" name="password" required minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
            </div>
            <div class="form-group">
                <label class="form-label" for="cuFullName">Full name <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="cuFullName" name="full_name" required maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="cuEmail">Email</label>
                <input type="email" class="form-input" id="cuEmail" name="email" maxlength="150">
            </div>

            <div class="form-group" id="cuStaffRoleWrap" style="display:none;">
                <label class="form-label" for="cuRoleStaff">Role <span style="color:var(--error);">*</span></label>
                <select class="form-select" id="cuRoleStaff">
                    <option value="dean">Dean</option>
                    <option value="hr">HR</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group" id="cuDeptWrap">
                <label class="form-label" for="cuDept"><span id="cuDeptLabel">Home department</span> <span style="color:var(--error);">*</span></label>
                <select class="form-select" id="cuDept" name="department_id">
                    <option value="">Select…</option>
                </select>
                <p class="admin-field-hint" id="cuDeptHint">Required for students, instructors, and deans (see users.department_id in schema).</p>
            </div>

            <fieldset class="admin-enroll-fieldset" id="cuStudentEnrollmentWrap" style="display:none;">
                <legend class="admin-enroll-legend">Initial enrollments</legend>
                <p class="admin-field-hint" style="margin-top:0;">Optional. Creates rows in <code style="font-size:11px;">enrollments</code> (student_id, course_id). Only courses in the student’s department are listed.</p>
                <div id="cuCourseChecks" class="admin-course-checks"></div>
            </fieldset>

            <div class="admin-modal-actions">
                <button type="button" class="btn btn--secondary" id="cuCancelBtn" data-close-modal="modalUser">Cancel</button>
                <button type="submit" class="admin-create-user-submit" id="cuSubmitBtn">
                    <span class="admin-create-user-submit__spinner" aria-hidden="true"></span>
                    <span class="admin-create-user-submit__leading" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <span class="admin-create-user-submit__text" id="cuSubmitText">Create student</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Create course -->
<div id="modalCourse" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalCourseTitle" aria-hidden="true">
    <div class="admin-modal-surface">
        <h2 id="modalCourseTitle">Add course</h2>
        <form id="formCreateCourse">
            <div class="form-group">
                <label class="form-label" for="ccCode">Course code <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="ccCode" name="code" required maxlength="20" placeholder="e.g. CS101">
            </div>
            <div class="form-group">
                <label class="form-label" for="ccTitle">Title <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="ccTitle" name="title" required maxlength="150">
            </div>
            <div class="form-group">
                <label class="form-label" for="ccDept">Department <span style="color:var(--error);">*</span></label>
                <select class="form-select" id="ccDept" name="department_id" required>
                    <option value="">Select…</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="ccInstructor">Instructor <span style="color:var(--error);">*</span></label>
                <select class="form-select" id="ccInstructor" name="instructor_id" required>
                    <option value="">Select…</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="ccProgram">Program</label>
                <input class="form-input" id="ccProgram" name="program" maxlength="100">
            </div>
            <div class="form-group">
                <label class="form-label" for="ccYearLevel">Year level</label>
                <input class="form-input" id="ccYearLevel" name="year_level" maxlength="20">
            </div>
            <div class="form-group">
                <label class="form-label" for="ccSemester">Semester <span style="color:var(--error);">*</span></label>
                <select class="form-select" id="ccSemester" name="semester" required>
                    <option value="I">Semester I</option>
                    <option value="II">Semester II</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="ccAcademicYear">Academic year <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="ccAcademicYear" name="academic_year" required maxlength="10" placeholder="e.g. 2026">
            </div>
            <div class="admin-modal-actions">
                <button type="button" class="btn btn--secondary" data-close-modal="modalCourse">Cancel</button>
                <button type="submit" class="btn-submit">Create course</button>
            </div>
        </form>
    </div>
</div>

<!-- Create department -->
<div id="modalDepartment" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalDeptTitle" aria-hidden="true">
    <div class="admin-modal-surface">
        <h2 id="modalDeptTitle">Add department</h2>
        <form id="formCreateDepartment">
            <div class="form-group">
                <label class="form-label" for="cdName">Name <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="cdName" name="name" required maxlength="100">
            </div>
            <div class="admin-modal-actions">
                <button type="button" class="btn btn--secondary" data-close-modal="modalDepartment">Cancel</button>
                <button type="submit" class="btn-submit">Create department</button>
            </div>
        </form>
    </div>
</div>

<div id="adminToast" class="dean-toast" role="alert" aria-live="polite"></div>

<script>
(function () {
    var currentUserId = <?= $currentUserId ?>;
    var currentTab = 'users';
    var lookups = { departments: [], users: [], courses: [] };
    var userModalMode = 'student';

    function escapeHtml(s) {
        if (s == null || s === '') return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    var toastTimer;
    function showToast(message, type) {
        var el = document.getElementById('adminToast');
        el.textContent = message;
        el.className = 'dean-toast is-visible dean-toast--' + (type === 'success' ? 'success' : 'error');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.classList.remove('is-visible'); }, 4200);
    }

    function loadTheme() {
        var s = localStorage.getItem('theme') || 'dark';
        document.documentElement.classList.toggle('light-mode', s === 'light');
        document.body.classList.remove('light-mode');
        var sun = document.querySelector('.sun-icon');
        var moon = document.querySelector('.moon-icon');
        if (sun) sun.style.display = s === 'light' ? 'none' : 'block';
        if (moon) moon.style.display = s === 'light' ? 'block' : 'none';
    }
    function toggleTheme() {
        var light = document.documentElement.classList.toggle('light-mode');
        document.body.classList.remove('light-mode');
        localStorage.setItem('theme', light ? 'light' : 'dark');
        var sun = document.querySelector('.sun-icon');
        var moon = document.querySelector('.moon-icon');
        if (sun) sun.style.display = light ? 'none' : 'block';
        if (moon) moon.style.display = light ? 'block' : 'none';
    }
    loadTheme();
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);

    function switchSection(id) {
        document.querySelectorAll('.nav-item[data-section]').forEach(function (btn) {
            var on = btn.getAttribute('data-section') === id;
            btn.classList.toggle('active', on);
            if (on) btn.setAttribute('aria-current', 'page');
            else btn.removeAttribute('aria-current');
        });
        document.querySelectorAll('.section-content').forEach(function (s) {
            var on = s.id === id;
            s.classList.toggle('active', on);
            s.setAttribute('aria-hidden', on ? 'false' : 'true');
        });
        var titles = { overview: 'System Overview', manage: 'Manage Data' };
        document.getElementById('pageTitle').textContent = titles[id] || 'Admin';
    }

    document.querySelectorAll('.nav-item[data-section]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switchSection(btn.getAttribute('data-section'));
        });
    });

    function updateAddButtons() {
        var onUsers = currentTab === 'users';
        var show = onUsers ? 'inline-flex' : 'none';
        var showStaff = onUsers ? 'inline-block' : 'none';
        var st = document.getElementById('btnAddStudent');
        var ins = document.getElementById('btnAddInstructor');
        var sf = document.getElementById('btnAddStaff');
        if (st) st.style.display = show;
        if (ins) ins.style.display = show;
        if (sf) sf.style.display = showStaff;
        document.getElementById('btnAddCourse').style.display = currentTab === 'courses' ? 'inline-flex' : 'none';
        document.getElementById('btnAddDepartment').style.display = currentTab === 'departments' ? 'inline-flex' : 'none';
    }

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-tab') === tab);
        });
        updateAddButtons();
        loadTable();
    }

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            switchTab(b.getAttribute('data-tab'));
        });
    });

    function setModalOpen(id, open) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.toggle('is-open', open);
        m.setAttribute('aria-hidden', open ? 'false' : 'true');
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setModalOpen(btn.getAttribute('data-close-modal'), false);
        });
    });

    ['modalUser', 'modalCourse', 'modalDepartment'].forEach(function (mid) {
        document.getElementById(mid).addEventListener('click', function (e) {
            if (e.target.id === mid) setModalOpen(mid, false);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        ['modalUser', 'modalCourse', 'modalDepartment'].forEach(function (mid) {
            var el = document.getElementById(mid);
            if (el && el.classList.contains('is-open')) setModalOpen(mid, false);
        });
    });

    function updateDeptFieldRequired() {
        var dept = document.getElementById('cuDept');
        if (!dept) return;
        if (userModalMode === 'student' || userModalMode === 'instructor') {
            dept.required = true;
            return;
        }
        if (userModalMode === 'staff') {
            dept.required = document.getElementById('cuRoleStaff').value === 'dean';
        }
    }

    function refreshStudentCourseChecks() {
        var host = document.getElementById('cuCourseChecks');
        if (!host || userModalMode !== 'student') return;
        var deptId = parseInt(document.getElementById('cuDept').value, 10);
        if (!deptId) {
            host.innerHTML = '<p class="admin-field-hint">Select a department to list courses for enrollment.</p>';
            return;
        }
        var rows = (lookups.courses || []).filter(function (c) {
            return parseInt(c.department_id, 10) === deptId && c.status === 'active';
        });
        if (!rows.length) {
            host.innerHTML = '<p class="admin-field-hint">No active courses in this department.</p>';
            return;
        }
        host.innerHTML = rows.map(function (c) {
            return '<label class="admin-course-check"><input type="checkbox" name="cu_course" value="' + parseInt(c.id, 10) + '"><span>' +
                escapeHtml(c.code) + ' — ' + escapeHtml(c.title) + ' <span style="opacity:0.75;">(' + escapeHtml(c.semester) + ' ' + escapeHtml(c.academic_year) + ')</span></span></label>';
        }).join('');
    }

    function openUserModal(mode) {
        document.getElementById('formCreateUser').reset();
        document.getElementById('cuCourseChecks').innerHTML = '';
        userModalMode = mode;
        document.getElementById('cuUserMode').value = mode;
        fillDepartmentOptions(document.getElementById('cuDept'), false);

        var title = document.getElementById('modalUserTitle');
        var lead = document.getElementById('modalUserLead');
        var uHint = document.getElementById('cuUsernameHint');
        var dLabel = document.getElementById('cuDeptLabel');
        var dHint = document.getElementById('cuDeptHint');
        var staffWrap = document.getElementById('cuStaffRoleWrap');
        var enrollWrap = document.getElementById('cuStudentEnrollmentWrap');
        var submitText = document.getElementById('cuSubmitText');
        var roleHidden = document.getElementById('cuRole');

        staffWrap.style.display = mode === 'staff' ? 'block' : 'none';
        enrollWrap.style.display = mode === 'student' ? 'block' : 'none';

        if (mode === 'student') {
            roleHidden.value = 'student';
            title.textContent = 'Add student';
            lead.textContent = 'Students have a home department (users.department_id). You may enroll them in active courses in that department via the enrollments table.';
            uHint.textContent = 'Often a student ID (e.g. 2021001). Must be unique.';
            dLabel.textContent = 'Home department';
            dHint.textContent = 'Required. Course enrollments below are limited to this department.';
            dHint.style.display = 'block';
            submitText.textContent = 'Create student';
            refreshStudentCourseChecks();
        } else if (mode === 'instructor') {
            roleHidden.value = 'instructor';
            title.textContent = 'Add instructor';
            lead.textContent = 'Instructors are faculty in a department; they teach courses (courses.instructor_id references users).';
            uHint.textContent = 'Login username (e.g. sarah.j). Must be unique.';
            dLabel.textContent = 'Department';
            dHint.textContent = 'Required. Assigns the instructor to this department for reporting and course alignment.';
            dHint.style.display = 'block';
            submitText.textContent = 'Create instructor';
        } else {
            roleHidden.value = document.getElementById('cuRoleStaff').value || 'dean';
            title.textContent = 'Add staff account';
            lead.textContent = 'Deans require a department; HR and admin may have no department (same as schema conventions).';
            uHint.textContent = 'Unique username for sign-in.';
            dLabel.textContent = 'Department';
            dHint.textContent = 'Required for deans only.';
            submitText.textContent = 'Create account';
            updateDeptFieldRequired();
        }
        updateDeptFieldRequired();
        setModalOpen('modalUser', true);
        document.getElementById('cuUsername').focus();
    }

    document.getElementById('btnAddStudent').addEventListener('click', function () { openUserModal('student'); });
    document.getElementById('btnAddInstructor').addEventListener('click', function () { openUserModal('instructor'); });
    document.getElementById('btnAddStaff').addEventListener('click', function () { openUserModal('staff'); });

    document.getElementById('cuDept').addEventListener('change', function () {
        if (userModalMode === 'student') refreshStudentCourseChecks();
    });
    document.getElementById('cuRoleStaff').addEventListener('change', function () {
        document.getElementById('cuRole').value = document.getElementById('cuRoleStaff').value;
        updateDeptFieldRequired();
    });
    document.getElementById('btnAddCourse').addEventListener('click', function () {
        document.getElementById('formCreateCourse').reset();
        fillDepartmentOptions(document.getElementById('ccDept'), false);
        fillInstructorOptions();
        setModalOpen('modalCourse', true);
        document.getElementById('ccCode').focus();
    });
    document.getElementById('btnAddDepartment').addEventListener('click', function () {
        document.getElementById('formCreateDepartment').reset();
        setModalOpen('modalDepartment', true);
        document.getElementById('cdName').focus();
    });

    function fillDepartmentOptions(selectEl, includeNone) {
        var html = includeNone ? '<option value="">— None —</option>' : '<option value="">Select…</option>';
        lookups.departments.forEach(function (d) {
            if (d.status && d.status !== 'active') return;
            html += '<option value="' + parseInt(d.id, 10) + '">' + escapeHtml(d.name) + '</option>';
        });
        selectEl.innerHTML = html;
    }

    function fillInstructorOptions() {
        var sel = document.getElementById('ccInstructor');
        var html = '<option value="">Select…</option>';
        lookups.users.forEach(function (u) {
            if (u.role !== 'instructor' || u.status !== 'active') return;
            var id = parseInt(u.id, 10);
            html += '<option value="' + id + '">' + escapeHtml(u.full_name) + ' (' + escapeHtml(u.username) + ')</option>';
        });
        sel.innerHTML = html;
    }

    async function loadLookups() {
        try {
            var rD = await fetch('/api/admin.php?action=list&entity=departments', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            var jD = await rD.json();
            if (rD.ok && jD.success && jD.data) lookups.departments = jD.data;
        } catch (e) {}
        try {
            var rU = await fetch('/api/admin.php?action=list&entity=users', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            var jU = await rU.json();
            if (rU.ok && jU.success && jU.data) lookups.users = jU.data;
        } catch (e) {}
        try {
            var rC = await fetch('/api/admin.php?action=list&entity=courses', { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            var jC = await rC.json();
            if (rC.ok && jC.success && jC.data) lookups.courses = jC.data;
        } catch (e) {}
    }

    async function postAdmin(payload) {
        var r = await fetch('/api/admin.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(payload)
        });
        var d = {};
        try { d = await r.json(); } catch (x) {}
        if (!r.ok || !d.success) {
            throw new Error(d.message || ('HTTP ' + r.status));
        }
        return d;
    }

    function setUserModalSubmitting(on) {
        var sub = document.getElementById('cuSubmitBtn');
        var can = document.getElementById('cuCancelBtn');
        if (sub) {
            sub.disabled = !!on;
            sub.classList.toggle('is-loading', !!on);
        }
        if (can) can.disabled = !!on;
    }

    document.getElementById('formCreateUser').addEventListener('submit', async function (e) {
        e.preventDefault();
        var role = document.getElementById('cuRole').value;
        if (userModalMode === 'staff') {
            role = document.getElementById('cuRoleStaff').value;
        }
        var payload = {
            action: 'create',
            entity: 'user',
            username: document.getElementById('cuUsername').value.trim(),
            password: document.getElementById('cuPassword').value,
            full_name: document.getElementById('cuFullName').value.trim(),
            email: document.getElementById('cuEmail').value.trim() || null,
            role: role,
            department_id: document.getElementById('cuDept').value || null
        };
        if (userModalMode === 'student') {
            var checked = [].slice.call(document.querySelectorAll('#cuCourseChecks input[name="cu_course"]:checked'));
            var cids = checked.map(function (x) { return parseInt(x.value, 10); }).filter(function (id) { return !isNaN(id) && id > 0; });
            if (cids.length) payload.course_ids = cids;
        }
        setUserModalSubmitting(true);
        try {
            await postAdmin(payload);
            showToast('User created.', 'success');
            setModalOpen('modalUser', false);
            e.target.reset();
            await loadLookups();
            loadDashboard();
        } catch (err) {
            showToast(err.message || 'Create failed.', 'error');
        } finally {
            setUserModalSubmitting(false);
        }
    });

    document.getElementById('formCreateCourse').addEventListener('submit', async function (e) {
        e.preventDefault();
        try {
            await postAdmin({
                action: 'create',
                entity: 'course',
                code: document.getElementById('ccCode').value.trim(),
                title: document.getElementById('ccTitle').value.trim(),
                department_id: parseInt(document.getElementById('ccDept').value, 10),
                instructor_id: parseInt(document.getElementById('ccInstructor').value, 10),
                program: document.getElementById('ccProgram').value.trim() || null,
                year_level: document.getElementById('ccYearLevel').value.trim() || null,
                semester: document.getElementById('ccSemester').value,
                academic_year: document.getElementById('ccAcademicYear').value.trim()
            });
            showToast('Course created.', 'success');
            setModalOpen('modalCourse', false);
            e.target.reset();
            loadDashboard();
        } catch (err) {
            showToast(err.message || 'Create failed.', 'error');
        }
    });

    document.getElementById('formCreateDepartment').addEventListener('submit', async function (e) {
        e.preventDefault();
        try {
            await postAdmin({
                action: 'create',
                entity: 'department',
                name: document.getElementById('cdName').value.trim()
            });
            showToast('Department created.', 'success');
            setModalOpen('modalDepartment', false);
            e.target.reset();
            await loadLookups();
            loadDashboard();
        } catch (err) {
            showToast(err.message || 'Create failed.', 'error');
        }
    });

    var deactivatePrompts = { user: 'Deactivate this user account? They will no longer be able to sign in.', course: 'Deactivate this course? It will be hidden from new scheduling.', department: 'Deactivate this department?' };

    async function deactivate(entitySingular, id) {
        if (!confirm(deactivatePrompts[entitySingular] || 'Continue?')) return;
        try {
            await postAdmin({ action: 'delete', entity: entitySingular, id: id });
            showToast('Updated.', 'success');
            await loadLookups();
            loadDashboard();
        } catch (err) {
            showToast(err.message || 'Action failed.', 'error');
        }
    }

    function loadDashboard() {
        var statsEl = document.getElementById('statsGrid');
        function errStats(msg) {
            statsEl.innerHTML = '<p style="color:var(--error);padding:12px;">' + escapeHtml(msg) + '</p>';
        }
        fetch('/api/analytics.php?action=system_stats', { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json().then(function (d) { return { r: r, d: d }; }); })
            .then(function (x) {
                if (x.r.status === 401) { errStats('Session expired. Sign in again.'); loadTable(); return; }
                if (!x.r.ok || !x.d.success) { errStats(x.d.message || ('HTTP ' + x.r.status)); loadTable(); return; }
                var s = x.d.stats;
                statsEl.innerHTML =
                    '<div class="stat-card"><div class="stat-info"><h3>Total Users</h3><div class="value">' + (s.total_instructors + s.total_students) + '</div></div></div>' +
                    '<div class="stat-card"><div class="stat-info"><h3>Total Departments</h3><div class="value">' + s.total_departments + '</div></div></div>' +
                    '<div class="stat-card"><div class="stat-info"><h3>Active Evaluations</h3><div class="value">' + s.open_evaluations + '</div></div></div>' +
                    '<div class="stat-card"><div class="stat-info"><h3>Total Submissions</h3><div class="value">' + s.total_submissions + '</div></div></div>';
                loadTable();
            })
            .catch(function () {
                errStats('Could not load dashboard.');
                loadTable();
            });
    }

    function tableColspan() {
        return currentTab === 'courses' ? 7 : 6;
    }

    function loadTable() {
        var body = document.getElementById('tableBody');
        var head = document.getElementById('tableHead');
        var cs = tableColspan();
        fetch('/api/admin.php?action=list&entity=' + encodeURIComponent(currentTab), { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json().then(function (d) { return { r: r, d: d }; }); })
            .then(function (x) {
                if (x.r.status === 401) {
                    body.innerHTML = '<tr><td colspan="' + cs + '" style="text-align:center;color:var(--error);">Session expired</td></tr>';
                    return;
                }
                if (!x.r.ok || !x.d.success) {
                    body.innerHTML = '<tr><td colspan="' + cs + '" style="text-align:center;color:var(--error);">' + escapeHtml(x.d.message || ('HTTP ' + x.r.status)) + '</td></tr>';
                    return;
                }
                var data = Array.isArray(x.d.data) ? x.d.data : [];

                if (currentTab === 'users') {
                    head.innerHTML = '<tr><th>ID</th><th>Username</th><th>Full name</th><th>Role</th><th>Status</th><th>Actions</th></tr>';
                    body.innerHTML = data.map(function (u) {
                        var active = u.status === 'active';
                        var canDeact = active && parseInt(u.id, 10) !== currentUserId;
                        var btn = canDeact
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="user" data-id="' + u.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled title="' + (!active ? 'Already inactive' : 'Cannot deactivate your own account') + '">—</button>';
                        return '<tr><td>' + u.id + '</td><td>' + escapeHtml(u.username) + '</td><td>' + escapeHtml(u.full_name) + '</td><td><span class="status-badge">' + escapeHtml(u.role) + '</span></td><td><span class="status-badge ' + (active ? 'status-active' : 'status-pending') + '">' + escapeHtml(u.status) + '</span></td><td>' + btn + '</td></tr>';
                    }).join('') || '<tr><td colspan="6">No rows</td></tr>';
                } else if (currentTab === 'courses') {
                    head.innerHTML = '<tr><th>Code</th><th>Title</th><th>Department</th><th>Instructor</th><th>Term</th><th>Status</th><th>Actions</th></tr>';
                    body.innerHTML = data.map(function (c) {
                        var active = c.status === 'active';
                        var btn = active
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="course" data-id="' + c.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled>—</button>';
                        return '<tr><td>' + escapeHtml(c.code) + '</td><td>' + escapeHtml(c.title) + '</td><td>' + escapeHtml(c.department_name) + '</td><td>' + escapeHtml(c.instructor_name) + '</td><td>' + escapeHtml(c.semester + ' ' + c.academic_year) + '</td><td><span class="status-badge ' + (active ? 'status-active' : 'status-pending') + '">' + escapeHtml(c.status) + '</span></td><td>' + btn + '</td></tr>';
                    }).join('') || '<tr><td colspan="7">No rows</td></tr>';
                } else {
                    head.innerHTML = '<tr><th>ID</th><th>Name</th><th>Head</th><th>Faculty</th><th>Status</th><th>Actions</th></tr>';
                    body.innerHTML = data.map(function (d) {
                        var active = d.status === 'active';
                        var btn = active
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="department" data-id="' + d.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled>—</button>';
                        return '<tr><td>' + d.id + '</td><td>' + escapeHtml(d.name) + '</td><td>' + escapeHtml(d.head_name || '—') + '</td><td>' + escapeHtml(String(d.faculty_count)) + '</td><td><span class="status-badge ' + (active ? 'status-active' : 'status-pending') + '">' + escapeHtml(d.status) + '</span></td><td>' + btn + '</td></tr>';
                    }).join('') || '<tr><td colspan="6">No rows</td></tr>';
                }

                body.querySelectorAll('[data-act="deactivate"]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        deactivate(btn.getAttribute('data-entity'), parseInt(btn.getAttribute('data-id'), 10));
                    });
                });
            })
            .catch(function () {
                body.innerHTML = '<tr><td colspan="' + cs + '" style="text-align:center;color:var(--error);">Could not load table</td></tr>';
            });
    }

    document.getElementById('logoutBtn').addEventListener('click', async function () {
        try {
            var r = await fetch('/api/auth.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({ action: 'logout' })
            });
            await r.json().catch(function () {});
            window.location.href = 'login.php';
        } catch (e) {
            window.location.href = 'login.php';
        }
    });

    updateAddButtons();
    loadLookups().then(function () { loadDashboard(); });
})();
</script>
</body>
</html>
