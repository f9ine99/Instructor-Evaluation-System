<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('admin');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
$currentUserId = (int) ($user['id'] ?? 0);
$fn = trim((string) ($user['full_name'] ?? ''));
$avatarLetters = $fn !== '' ? strtoupper(substr($fn, 0, 2)) : 'AD';

$roleLabels = [
    'admin' => 'Administrator',
    'hr' => 'HR',
    'dean' => 'Dean',
    'instructor' => 'Instructor',
    'student' => 'Student',
];
$rKey = (string) ($user['role'] ?? '');
$settingsRoleLabel = isset($roleLabels[$rKey]) ? $roleLabels[$rKey] : ($rKey !== '' ? ucfirst($rKey) : '—');

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
<div class="admin-layout" id="dashboardLayout">
    <aside class="sidebar" id="dashboardSidebar" aria-label="Admin navigation">
        <div class="sidebar-header">
            <div class="sidebar-logo" title="HOPE">HOPE</div>
            <button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-expanded="true" aria-controls="dashboardSidebar" title="Collapse sidebar">
                <svg class="sidebar-collapse-btn__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
        </div>
        <nav class="nav-links">
            <button type="button" class="nav-item active" data-section="overview" aria-current="page" title="Overview">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span>Overview</span>
            </button>
            <button type="button" class="nav-item" data-section="manage" title="Manage Data">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Manage Data</span>
            </button>
            <button type="button" class="nav-item" data-section="settings" title="Settings">
                <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
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

        <section id="overview" class="section-content active admin-overview" aria-label="System overview">
            <div class="admin-overview-inner">
                <header class="admin-overview-hero">
                    <div class="admin-overview-hero__badge" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <div>
                        <p class="admin-overview-hero__lead">Whole-institution counts: people, organizational units, evaluations, and response quality.</p>
                    </div>
                </header>

                <div id="adminGapBanner" class="admin-gap-banner-root" hidden aria-live="polite"></div>

                <div id="statsGrid" class="admin-overview-mount" aria-live="polite">
                    <p class="admin-overview-loading">Loading overview…</p>
                </div>
            </div>
        </section>

        <section id="manage" class="section-content admin-manage" aria-hidden="true" aria-label="Manage data">
            <div class="admin-manage-inner">
                <div class="admin-manage-controls">
                    <div class="admin-manage-controls__primary">
                        <nav class="admin-manage-segment" role="tablist" aria-label="Dataset">
                            <button type="button" class="tab-btn admin-manage-segment__btn active" data-tab="users" role="tab" aria-selected="true">Users</button>
                            <button type="button" class="tab-btn admin-manage-segment__btn" data-tab="courses" role="tab" aria-selected="false">Classes</button>
                            <button type="button" class="tab-btn admin-manage-segment__btn" data-tab="departments" role="tab" aria-selected="false">Departments</button>
                        </nav>
                        <p class="admin-manage-hint" id="manageDatasetHint">Use role filters below to load one role at a time.</p>
                    </div>
                    <div class="admin-toolbar-actions admin-manage-controls__actions">
                        <button type="button" class="admin-add-student-cta" id="btnAddStudent" style="display:none;" aria-label="Add a student">
                            <span class="admin-add-student-cta__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </span>
                            <span class="admin-add-student-cta__label">
                                <span class="admin-add-student-cta__title">Add student</span>
                                <span class="admin-add-student-cta__hint">Dept + class enrollments</span>
                            </span>
                        </button>
                        <button type="button" class="admin-add-instructor-cta" id="btnAddInstructor" style="display:none;" aria-label="Add an instructor">
                            <span class="admin-add-instructor-cta__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                            <span class="admin-add-instructor-cta__label">
                                <span class="admin-add-instructor-cta__title">Add instructor</span>
                                <span class="admin-add-instructor-cta__hint">Teaches department classes</span>
                            </span>
                        </button>
                        <button type="button" class="admin-add-staff-link" id="btnAddStaff" style="display:none;">Add staff (dean / HR / admin)</button>
                        <button type="button" class="admin-manage-course-btn" id="btnAddCourse" style="display:none;">
                            <span class="admin-manage-course-btn__icon" aria-hidden="true">+</span>
                            Add class
                        </button>
                        <button type="button" class="admin-add-staff-link" id="btnAddDepartment" style="display:none;" title="Create a department">+ Add department</button>
                    </div>
                </div>

                <div class="admin-data-panel">
                    <div class="admin-data-panel__toolbar">
                        <div class="admin-data-panel__titles">
                            <span class="admin-data-panel__kicker">Current view</span>
                            <h3 class="admin-data-panel__name" id="managePanelTitle">Users</h3>
                        </div>
                        <span class="admin-data-panel__count" id="managePanelCount" aria-live="polite"></span>
                    </div>
                    <div class="admin-user-role-bar" id="adminUserRoleBar" hidden>
                        <span class="admin-user-role-bar__label" id="adminUserRoleBarLabel">Filter by role</span>
                        <div class="admin-user-role-bar__chips" role="group" aria-label="Filter users by role">
                            <button type="button" class="admin-role-filter active" data-user-role="" aria-pressed="true">All</button>
                            <button type="button" class="admin-role-filter" data-user-role="student" aria-pressed="false">Students</button>
                            <button type="button" class="admin-role-filter" data-user-role="instructor" aria-pressed="false">Instructors</button>
                            <button type="button" class="admin-role-filter" data-user-role="dean" aria-pressed="false">Deans</button>
                            <button type="button" class="admin-role-filter" data-user-role="hr" aria-pressed="false">HR</button>
                            <button type="button" class="admin-role-filter" data-user-role="admin" aria-pressed="false">Admins</button>
                        </div>
                    </div>
                    <div class="admin-data-panel__table-wrap">
                        <table class="data-table admin-manage-table">
                            <thead id="tableHead"></thead>
                            <tbody id="tableBody">
                                <tr><td colspan="7" class="admin-manage-loading">Loading…</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section id="settings" class="section-content admin-settings" aria-hidden="true" aria-label="Settings">
            <div class="admin-settings-shell">
                <header class="admin-settings-hero">
                    <div class="admin-settings-hero__badge" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="admin-settings-hero__lead">Administrator profile, live session check, and password updates for this browser session.</p>
                    </div>
                </header>

                <div class="admin-settings-grid">
                    <article class="admin-settings-panel admin-settings-panel--profile">
                        <div class="admin-settings-panel__head">
                            <span class="admin-settings-panel__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.85"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </span>
                            <div>
                                <h3 class="admin-settings-panel__title">Account &amp; session</h3>
                                <p class="admin-settings-panel__subtitle">Details from your sign-in and a live check against the server.</p>
                            </div>
                        </div>
                        <div class="admin-settings-session-row">
                            <span class="admin-settings-session-label">Server check</span>
                            <p class="admin-settings-session" id="adminSessionStatus" role="status">Open this section to verify your session.</p>
                        </div>
                        <dl class="admin-settings-kv">
                            <div class="admin-settings-kv__row">
                                <dt>Name</dt>
                                <dd id="adminSettingsKvFullName"><?= htmlspecialchars($user['full_name'] ?? '') ?></dd>
                            </div>
                            <div class="admin-settings-kv__row">
                                <dt>Username</dt>
                                <dd><span class="admin-settings-kv__mono" id="adminSettingsKvUsername"><?= htmlspecialchars($user['username'] ?? '') ?></span></dd>
                            </div>
                            <div class="admin-settings-kv__row">
                                <dt>Email</dt>
                                <dd id="adminSettingsKvEmail"><?= !empty($user['email']) ? htmlspecialchars((string) $user['email']) : '—' ?></dd>
                            </div>
                            <div class="admin-settings-kv__row">
                                <dt>Role</dt>
                                <dd><span class="admin-settings-chip" id="adminSettingsKvRole"><?= htmlspecialchars($settingsRoleLabel) ?></span></dd>
                            </div>
                            <div class="admin-settings-kv__row admin-settings-kv__row--meta">
                                <dt>User ID</dt>
                                <dd id="adminSettingsKvUserId"><?= htmlspecialchars((string) ($user['id'] ?? '')) ?></dd>
                            </div>
                        </dl>
                    </article>

                    <article class="admin-settings-panel admin-settings-panel--security">
                        <div class="admin-settings-panel__head">
                            <span class="admin-settings-panel__icon admin-settings-panel__icon--lock" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.85"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </span>
                            <div>
                                <h3 class="admin-settings-panel__title">Password</h3>
                                <p class="admin-settings-panel__subtitle">Use a strong passphrase. You will need your current password to continue.</p>
                            </div>
                        </div>
                        <aside class="admin-settings-notice" role="note">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>After you update your password, <strong>this session ends</strong>. You’ll be returned to sign-in — use your <strong>new</strong> password there.</p>
                        </aside>
                        <form id="formChangePassword" class="admin-settings-form" autocomplete="off">
                            <div class="form-group">
                                <label class="form-label" for="cpCurrent">Current password</label>
                                <input type="password" class="form-input admin-settings-input" id="cpCurrent" name="current_password" required autocomplete="current-password" maxlength="128" placeholder="Your existing password">
                            </div>
                            <div class="form-group admin-settings-password-pair">
                                <div class="admin-settings-password-field">
                                    <label class="form-label" for="cpNew">New password</label>
                                    <input type="password" class="form-input admin-settings-input" id="cpNew" name="new_password" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="Minimum 8 characters">
                                </div>
                                <div class="admin-settings-password-field">
                                    <label class="form-label" for="cpConfirm">Confirm</label>
                                    <input type="password" class="form-input admin-settings-input" id="cpConfirm" name="new_password_confirm" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="Repeat new password">
                                </div>
                            </div>
                            <p class="admin-settings-form-msg" id="cpFormMsg" role="alert" aria-live="polite" hidden></p>
                            <div class="admin-settings-form-actions">
                                <button type="submit" class="btn-submit admin-settings-submit" id="cpSubmit">
                                    Update password &amp; sign out
                                </button>
                            </div>
                        </form>
                    </article>
                </div>
            </div>
        </section>
    </main>
</div>

<!-- Create user (mode: student | instructor | staff) -->
<div id="modalUser" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalUserTitle" aria-hidden="true">
    <div class="admin-modal-surface admin-modal-surface--user">
        <h2 id="modalUserTitle">Add student</h2>
        <p class="admin-modal-lead" id="modalUserLead">Students have a department and join <strong>classes</strong> (rosters) via enrollments — evaluations use those rosters.</p>

        <div id="cuStudentModeTabs" class="admin-modal-mode-tabs" style="display:none;" role="tablist" aria-label="Student creation mode">
            <button type="button" class="admin-modal-mode-tabs__btn is-active" role="tab" aria-selected="true" data-student-tab="single">One student</button>
            <button type="button" class="admin-modal-mode-tabs__btn" role="tab" aria-selected="false" data-student-tab="bulk">Bulk upload</button>
        </div>

        <div id="cuStudentPanelSingle">
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

        <div id="cuStudentPanelBulk" style="display:none;">
            <form id="formBulkImportStudents">
                <p class="admin-field-hint" style="margin-top:0;">One student ID per line, or CSV: <code style="font-size:11px;">student_id,full name,email</code>. ID-only lines use the display name “New student” (change it later or include the name in CSV). Shared password; first login forces a new password.</p>
                <div id="bulkClassLockBanner" class="admin-bulk-class-lock" role="note" hidden></div>
                <input type="hidden" id="bulkClassCourseInput" name="bulk_class_course" value="">
                <div class="form-group">
                    <label class="form-label" for="bulkDept">Department <span style="color:var(--error);">*</span></label>
                    <select class="form-select" id="bulkDept" name="department_id" required>
                        <option value="">Select…</option>
                    </select>
                </div>
                <div id="bulkEnrollmentCourseSection">
                <fieldset class="admin-enroll-fieldset">
                    <legend class="admin-enroll-legend">Additional class enrollments</legend>
                    <p class="admin-field-hint" style="margin-top:0;">Optional when choosing department yourself. Tick extra classes (same department) or open a class from <strong>Manage → Classes → Class roster</strong> to import directly into one class.</p>
                    <div id="bulkCourseChecks" class="admin-course-checks"></div>
                </fieldset>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bulkLines">Student IDs <span style="color:var(--error);">*</span></label>
                    <textarea class="form-input admin-bulk-student-lines" id="bulkLines" name="lines" rows="10" required placeholder="STU001&#10;STU002&#10;2024001,Ada Lovelace,ada@example.edu" spellcheck="false"></textarea>
                    <p class="admin-field-hint">Up to 500 accounts per import. Usernames must be unique in the system.</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="bulkSharedPassword">Shared password <span style="color:var(--error);">*</span></label>
                    <input type="password" class="form-input" id="bulkSharedPassword" required minlength="8" autocomplete="new-password" placeholder="Temporary password for all (min. 8 characters)">
                </div>
                <div class="admin-modal-actions">
                    <button type="button" class="btn btn--secondary" data-close-modal="modalUser">Cancel</button>
                    <button type="submit" class="admin-create-user-submit" id="bulkSubmitBtn">
                        <span class="admin-create-user-submit__spinner" aria-hidden="true"></span>
                        <span class="admin-create-user-submit__leading" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        </span>
                        <span class="admin-create-user-submit__text">Import students</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create class (course offering — instructor + term + dept) -->
<div id="modalCourse" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalCourseTitle" aria-hidden="true">
    <div class="admin-modal-surface">
        <h2 id="modalCourseTitle">Add class</h2>
        <p class="admin-modal-lead" style="margin-top:-12px;margin-bottom:16px;font-size:13px;color:var(--text-muted);">One row here = how students enroll and how deans tie evaluations — think “Section / offering,” not catalog theory.</p>
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
                <button type="submit" class="btn-submit">Create class</button>
            </div>
        </form>
    </div>
</div>

<!-- Class roster: view enrollment + bulk add to this class -->
<div id="modalClassHub" class="dean-modal-overlay admin-class-hub-overlay" role="dialog" aria-modal="true" aria-labelledby="classHubTitle" aria-hidden="true">
    <div class="admin-modal-surface admin-modal-surface--class-hub">
        <h2 id="classHubTitle">Class roster</h2>
        <div id="classHubMeta" class="admin-class-hub-meta"></div>
        <div id="classHubRoster" class="admin-class-hub-roster" role="region" aria-label="Enrolled students"></div>
        <div id="classHubEnrollSection" class="admin-class-hub-enroll">
            <h3 class="admin-class-hub-enroll-title">Enroll existing students</h3>
            <p class="admin-class-hub-enroll-lead">Active students in this class’s home department who are not on the roster yet.</p>
            <div id="classHubStudentPicker" class="admin-class-hub-picker" role="group" aria-label="Students available to enroll"></div>
        </div>
        <div class="admin-modal-actions admin-class-hub-actions">
            <button type="button" class="btn btn--secondary" data-close-modal="modalClassHub">Close</button>
            <button type="button" class="admin-class-hub-action-btn admin-class-hub-action-btn--enroll" id="btnClassHubEnrollSelected" disabled>Enroll selected</button>
            <button type="button" class="admin-class-hub-action-btn admin-class-hub-action-btn--bulk" id="btnClassHubBulkAdd">Bulk add new students</button>
        </div>
    </div>
</div>

<!-- Admin reset user password -->
<div id="modalResetPassword" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalResetPwTitle" aria-hidden="true">
    <div class="admin-modal-surface admin-modal-surface--reset-pw">
        <h2 id="modalResetPwTitle">Reset password</h2>
        <p class="admin-modal-lead" id="modalResetPwLead">Set a new password for this account. Prefer a strong temporary value and deliver it securely (in person / official channel).</p>
        <p class="admin-reset-pw-user" id="modalResetPwUserLabel" aria-live="polite"></p>
        <form id="formResetUserPassword">
            <input type="hidden" id="rpUserId" name="user_id" value="">
            <input type="hidden" id="rpUserRole" value="">
            <div class="form-group" id="rpMustChangeWrap">
                <label class="admin-course-check admin-reset-pw-check">
                    <input type="checkbox" id="rpMustChange" name="must_change_password" checked>
                    <span>Students: require a new password on first sign-in (recommended)</span>
                </label>
                <p class="admin-field-hint" id="rpMustChangeHint">For instructors, HR, dean, or admin accounts, this option is skipped — they sign in with the password you set here.</p>
            </div>
            <div class="form-group">
                <label class="form-label" for="rpNewPw">New password <span style="color:var(--error);">*</span></label>
                <input type="password" class="form-input" id="rpNewPw" name="new_password" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="Temporary password (min 8 characters)">
            </div>
            <div class="form-group">
                <label class="form-label" for="rpConfirmPw">Confirm <span style="color:var(--error);">*</span></label>
                <input type="password" class="form-input" id="rpConfirmPw" name="confirm" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="Repeat new password">
            </div>
            <p class="admin-settings-form-msg admin-reset-pw-msg" id="rpFormMsg" role="alert" aria-live="polite" hidden></p>
            <div class="admin-modal-actions">
                <button type="button" class="btn btn--secondary" id="rpCancelBtn">Cancel</button>
                <button type="submit" class="btn-submit" id="rpSubmitBtn">Save password</button>
            </div>
        </form>
    </div>
</div>

<!-- Create department -->
<div id="modalDepartment" class="dean-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalDeptTitle" aria-hidden="true">
    <div class="admin-modal-surface">
        <h2 id="modalDeptTitle">Add department</h2>
        <p class="admin-modal-lead">Departments are created first — then instructors, classes (offerings), and student home departments tie to these units.</p>
        <form id="formCreateDepartment">
            <div class="form-group">
                <label class="form-label" for="cdName">Department name <span style="color:var(--error);">*</span></label>
                <input class="form-input" id="cdName" name="name" required maxlength="100" placeholder="e.g. Computer Science" autocomplete="organization">
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
    var currentUserRoleFilter = '';
    var lookups = { departments: [], users: [], courses: [] };
    var userModalMode = 'student';
    var studentBulkTab = 'single';
    var pendingBulkClassContext = null;
    var adminClassHubContext = null;
    var lastManageCoursesPayload = [];

    function setStudentBulkTab(tab) {
        studentBulkTab = tab === 'bulk' ? 'bulk' : 'single';
        var tabRoot = document.getElementById('cuStudentModeTabs');
        var singlePanel = document.getElementById('cuStudentPanelSingle');
        var bulkPanel = document.getElementById('cuStudentPanelBulk');
        if (tabRoot) {
            tabRoot.querySelectorAll('[data-student-tab]').forEach(function (btn) {
                var on = btn.getAttribute('data-student-tab') === studentBulkTab;
                btn.classList.toggle('is-active', on);
                btn.setAttribute('aria-selected', on ? 'true' : 'false');
            });
        }
        if (singlePanel) singlePanel.style.display = studentBulkTab === 'single' ? 'block' : 'none';
        if (bulkPanel) bulkPanel.style.display = studentBulkTab === 'bulk' ? 'block' : 'none';
        if (studentBulkTab === 'bulk') {
            refreshBulkStudentCourseChecks();
        }
    }

    var tabStrip = document.getElementById('cuStudentModeTabs');
    if (tabStrip) {
        tabStrip.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-student-tab]');
            if (!btn) return;
            setStudentBulkTab(btn.getAttribute('data-student-tab'));
        });
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    /** Safe for embedding in HTML double-quoted attributes */
    function escapeAttr(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;');
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

    var SETTINGS_ROLE_LABELS = {
        admin: 'Administrator',
        hr: 'HR',
        dean: 'Dean',
        instructor: 'Instructor',
        student: 'Student'
    };

    function applyAdminSettingsProfileFromLiveSession(u) {
        if (!u) return;
        var fn = document.getElementById('adminSettingsKvFullName');
        if (fn) fn.textContent = u.full_name || '';
        var uns = document.getElementById('adminSettingsKvUsername');
        if (uns) uns.textContent = u.username || '';
        var em = document.getElementById('adminSettingsKvEmail');
        if (em) em.textContent = u.email && String(u.email).trim() !== '' ? String(u.email) : '—';
        var uid = document.getElementById('adminSettingsKvUserId');
        if (uid && u.id != null) uid.textContent = String(u.id);
        var rl = document.getElementById('adminSettingsKvRole');
        var r = u.role ? String(u.role) : '';
        if (rl) rl.textContent = SETTINGS_ROLE_LABELS[r] || (r !== '' ? r.charAt(0).toUpperCase() + r.slice(1) : '—');
    }

    function refreshAdminSessionStatus() {
        var el = document.getElementById('adminSessionStatus');
        if (!el) return;
        el.textContent = 'Checking session…';
        el.className = 'admin-settings-session admin-settings-session--pending';
        fetch('/api/auth.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ action: 'session_check' })
        })
            .then(function (r) {
                return r.json().then(function (d) {
                    return { r: r, d: d };
                });
            })
            .then(function (x) {
                if (!x.r.ok || !x.d.success || !x.d.user) {
                    el.className = 'admin-settings-session admin-settings-session--bad';
                    el.innerHTML =
                        'Session invalid or expired. <a href="login.php" class="admin-settings-link">Sign in again</a>.';
                    return;
                }
                var u = x.d.user;
                applyAdminSettingsProfileFromLiveSession(u);
                el.className = 'admin-settings-session admin-settings-session--ok';
                el.textContent =
                    'Session active — profile reloaded from the database for user #' +
                    String(u.id) +
                    ' (' +
                    String(u.username) +
                    ').';
            })
            .catch(function () {
                el.className = 'admin-settings-session admin-settings-session--bad';
                el.textContent = 'Could not verify session (network error).';
            });
    }

    function updateManageDatasetTitleAndRoleBar() {
        var pt = document.getElementById('managePanelTitle');
        var bar = document.getElementById('adminUserRoleBar');
        if (bar) bar.hidden = currentTab !== 'users';
        if (!pt) return;
        if (currentTab !== 'users') {
            var panelTitles = { users: 'Users', courses: 'Classes (course offerings)', departments: 'Departments' };
            pt.textContent = panelTitles[currentTab] || currentTab;
            return;
        }
        var byRole = {
            '': 'Users',
            student: 'Students',
            instructor: 'Instructors',
            dean: 'Deans',
            hr: 'HR staff',
            admin: 'Administrators'
        };
        pt.textContent = byRole[currentUserRoleFilter] || 'Users';
        document.querySelectorAll('.admin-role-filter').forEach(function (b) {
            var role = b.getAttribute('data-user-role') || '';
            var on = role === currentUserRoleFilter;
            b.classList.toggle('active', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    }

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
        var titles = { overview: 'System Overview', manage: 'Manage Data', settings: 'Settings' };
        document.getElementById('pageTitle').textContent = titles[id] || 'Admin';
        if (id === 'manage') {
            var mh = document.getElementById('manageDatasetHint');
            if (mh && currentTab === 'users') {
                mh.textContent = currentUserRoleFilter
                    ? ('Listing only ' + (currentUserRoleFilter === 'hr' ? 'HR' : currentUserRoleFilter) + ' accounts.')
                    : 'Use role filters below to load one role at a time.';
            }
            updateManageDatasetTitleAndRoleBar();
            refreshManageTableIfNeeded();
        }
        if (id === 'settings') {
            refreshAdminSessionStatus();
        }
    }

    document.querySelectorAll('.nav-item[data-section]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switchSection(btn.getAttribute('data-section'));
        });
    });

    function manageSectionIsActive() {
        var sec = document.getElementById('manage');
        return !!(sec && sec.classList.contains('active'));
    }

    /** Only hits /api/admin list when Manage Data is visible — avoids redundant work on Overview load. */
    function refreshManageTableIfNeeded() {
        if (manageSectionIsActive()) {
            loadTable();
        }
    }

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
        document.getElementById('btnAddDepartment').style.display =
            currentTab === 'departments' || currentTab === 'courses' ? 'inline-block' : 'none';
    }

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            var on = b.getAttribute('data-tab') === tab;
            b.classList.toggle('active', on);
            if (b.classList.contains('admin-manage-segment__btn')) {
                b.setAttribute('aria-selected', on ? 'true' : 'false');
            }
        });
        var datasetHints = {
            courses: '',
            departments: 'Organizational units used when assigning users and catalog courses.'
        };
        var mh = document.getElementById('manageDatasetHint');
        if (mh) {
            if (tab === 'users') {
                mh.textContent = currentUserRoleFilter
                    ? ('Listing only ' + (currentUserRoleFilter === 'hr' ? 'HR' : currentUserRoleFilter) + ' accounts. Switch filters or choose All.')
                    : 'Use role filters below to load one role at a time.';
            } else {
                mh.textContent = datasetHints[tab] || '';
            }
        }
        var pc = document.getElementById('managePanelCount');
        if (pc) pc.textContent = '';
        updateManageDatasetTitleAndRoleBar();
        updateAddButtons();
        refreshManageTableIfNeeded();
    }

    document.querySelectorAll('.admin-role-filter').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentUserRoleFilter = btn.getAttribute('data-user-role') || '';
            updateManageDatasetTitleAndRoleBar();
            var mh = document.getElementById('manageDatasetHint');
            if (mh && currentTab === 'users') {
                mh.textContent = currentUserRoleFilter
                    ? ('Listing only ' + (currentUserRoleFilter === 'hr' ? 'HR' : currentUserRoleFilter) + ' accounts.')
                    : 'Use role filters below to load one role at a time.';
            }
            refreshManageTableIfNeeded();
        });
    });

    document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.addEventListener('click', function () {
            switchTab(b.getAttribute('data-tab'));
        });
    });

    function clearBulkImportClassLockedUI() {
        var bd = document.getElementById('bulkDept');
        if (bd) bd.disabled = false;
        var bi = document.getElementById('bulkClassCourseInput');
        if (bi) bi.value = '';
        var ban = document.getElementById('bulkClassLockBanner');
        if (ban) {
            ban.hidden = true;
            ban.textContent = '';
        }
        var sec = document.getElementById('bulkEnrollmentCourseSection');
        if (sec) sec.style.display = '';
    }

    function setModalOpen(id, open) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.toggle('is-open', open);
        m.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (!open && id === 'modalClassHub') {
            adminClassHubContext = null;
        }
        if (!open && id === 'modalUser') {
            pendingBulkClassContext = null;
            clearBulkImportClassLockedUI();
        }
        if (!open && id === 'modalResetPassword') {
            var f = document.getElementById('formResetUserPassword');
            if (f) f.reset();
            var msg = document.getElementById('rpFormMsg');
            if (msg) {
                msg.hidden = true;
                msg.textContent = '';
            }
            document.getElementById('rpUserId').value = '';
            document.getElementById('rpUserRole').value = '';
            var mw = document.getElementById('rpMustChangeWrap');
            if (mw) mw.hidden = false;
            var cb = document.getElementById('rpMustChange');
            if (cb) cb.checked = true;
            var lb = document.getElementById('modalResetPwUserLabel');
            if (lb) lb.textContent = '';
        }
    }

    document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setModalOpen(btn.getAttribute('data-close-modal'), false);
        });
    });

    ['modalUser', 'modalCourse', 'modalDepartment', 'modalClassHub', 'modalResetPassword'].forEach(function (mid) {
        var node = document.getElementById(mid);
        if (!node) return;
        node.addEventListener('click', function (e) {
            if (e.target.id === mid) setModalOpen(mid, false);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        ['modalUser', 'modalCourse', 'modalDepartment', 'modalClassHub', 'modalResetPassword'].forEach(function (mid) {
            var el = document.getElementById(mid);
            if (el && el.classList.contains('is-open')) setModalOpen(mid, false);
        });
    });

    document.getElementById('rpCancelBtn').addEventListener('click', function () {
        setModalOpen('modalResetPassword', false);
    });

    document.getElementById('tableBody').addEventListener('click', function (e) {
        var rp = e.target.closest('[data-act="reset-password"]');
        if (!rp || rp.disabled) return;
        var uid = parseInt(rp.getAttribute('data-user-id'), 10);
        var role = (rp.getAttribute('data-user-role') || '').trim();
        var login = rp.getAttribute('data-user-login') || '';
        var fullName = rp.getAttribute('data-user-fullname') || '';
        if (isNaN(uid) || uid <= 0) return;

        document.getElementById('rpUserId').value = String(uid);
        document.getElementById('rpUserRole').value = role;
        var mw = document.getElementById('rpMustChangeWrap');
        var mcb = document.getElementById('rpMustChange');
        var mhint = document.getElementById('rpMustChangeHint');
        if (role === 'student') {
            if (mw) mw.hidden = false;
            if (mcb) mcb.checked = true;
            if (mhint) {
                mhint.textContent =
                    'Recommended for forgotten passwords: student signs in once with your temporary password, then sets their own.';
            }
        } else {
            if (mw) mw.hidden = true;
            if (mcb) mcb.checked = false;
            if (mhint) {
                mhint.textContent = '';
            }
        }
        var ul = document.getElementById('modalResetPwUserLabel');
        if (ul) {
            ul.innerHTML =
                escapeHtml(login) +
                ' <span style="opacity:0.85;">#' +
                uid +
                '</span> · ' +
                escapeHtml(fullName) +
                (role !== 'student' ? ' <span style="opacity:0.75;">(' + escapeHtml(role) + ')</span>' : '');
        }
        document.getElementById('rpFormMsg').hidden = true;
        document.getElementById('rpFormMsg').textContent = '';
        setModalOpen('modalResetPassword', true);
        setTimeout(function () {
            var i = document.getElementById('rpNewPw');
            if (i) i.focus();
        }, 100);
    });

    document.getElementById('formResetUserPassword').addEventListener('submit', async function (ev) {
        ev.preventDefault();
        var uid = parseInt(document.getElementById('rpUserId').value, 10);
        var role = (document.getElementById('rpUserRole').value || '').trim();
        var p1 = document.getElementById('rpNewPw').value;
        var p2 = document.getElementById('rpConfirmPw').value;
        var msg = document.getElementById('rpFormMsg');
        var sub = document.getElementById('rpSubmitBtn');
        msg.hidden = true;
        msg.textContent = '';
        if (isNaN(uid) || uid <= 0) return;
        if (p1 !== p2) {
            msg.textContent = 'Passwords do not match.';
            msg.hidden = false;
            return;
        }
        if (p1.length < 8) {
            msg.textContent = 'Password must be at least 8 characters.';
            msg.hidden = false;
            return;
        }
        var body = {
            action: 'reset_user_password',
            user_id: uid,
            new_password: p1,
            must_change_password: role === 'student' ? !!document.getElementById('rpMustChange').checked : false
        };
        sub.disabled = true;
        try {
            var resp = await fetch('/api/admin.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(body)
            });
            var d = await resp.json().catch(function () {
                return {};
            });
            if (!resp.ok || !d.success) {
                msg.textContent = d.message || 'Could not reset password.';
                msg.hidden = false;
                sub.disabled = false;
                return;
            }
            showToast(d.message || 'Password updated.', 'success');
            setModalOpen('modalResetPassword', false);
            loadTable();
        } catch (err) {
            msg.textContent = err.message || 'Network error.';
            msg.hidden = false;
        }
        sub.disabled = false;
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

    function refreshBulkStudentCourseChecks() {
        var host = document.getElementById('bulkCourseChecks');
        if (!host) return;
        var bulkDeptSel = document.getElementById('bulkDept');
        if (!bulkDeptSel) return;
        var deptId = parseInt(bulkDeptSel.value, 10);
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
            return '<label class="admin-course-check"><input type="checkbox" name="bulk_course" value="' + parseInt(c.id, 10) + '"><span>' +
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

        var bulkTabs = document.getElementById('cuStudentModeTabs');
        var bulkPanel = document.getElementById('cuStudentPanelBulk');
        var singlePanel = document.getElementById('cuStudentPanelSingle');
        if (mode === 'student') {
            var pbGrab = pendingBulkClassContext;
            pendingBulkClassContext = null;

            if (bulkTabs) bulkTabs.style.display = 'flex';
            fillDepartmentOptions(document.getElementById('bulkDept'), false);
            var bf = document.getElementById('formBulkImportStudents');
            if (bf) bf.reset();
            var bch = document.getElementById('bulkCourseChecks');
            if (bch) bch.innerHTML = '';
            clearBulkImportClassLockedUI();

            if (pbGrab) {
                document.getElementById('bulkDept').value = String(pbGrab.deptId);
                document.getElementById('bulkDept').disabled = true;
                document.getElementById('bulkClassCourseInput').value = String(pbGrab.courseId);
                var bban = document.getElementById('bulkClassLockBanner');
                bban.hidden = false;
                bban.textContent = 'All imports are enrolled in this class: ' + pbGrab.label + '.';
                document.getElementById('bulkEnrollmentCourseSection').style.display = 'none';
                setStudentBulkTab('bulk');
            } else {
                document.getElementById('bulkClassLockBanner').hidden = true;
                document.getElementById('bulkEnrollmentCourseSection').style.display = '';
                setStudentBulkTab('single');
                refreshBulkStudentCourseChecks();
            }
        } else {
            if (bulkTabs) bulkTabs.style.display = 'none';
            if (bulkPanel) bulkPanel.style.display = 'none';
            if (singlePanel) singlePanel.style.display = 'block';
        }

        if (mode === 'student') {
            roleHidden.value = 'student';
            title.textContent = 'Add student';
            lead.textContent = 'Use <strong>Manage → Classes → Class roster</strong> to add existing students or bulk-import new accounts into one class; here you can also add a single student.';
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
    document.getElementById('bulkDept').addEventListener('change', function () {
        refreshBulkStudentCourseChecks();
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

    function setBulkImportSubmitting(on) {
        var sub = document.getElementById('bulkSubmitBtn');
        var bulkForm = document.getElementById('formBulkImportStudents');
        var cancelBtns = bulkForm ? bulkForm.querySelectorAll('button[data-close-modal]') : [];
        if (sub) {
            sub.disabled = !!on;
            sub.classList.toggle('is-loading', !!on);
        }
        cancelBtns.forEach(function (b) { b.disabled = !!on; });
    }

    document.getElementById('formBulkImportStudents').addEventListener('submit', async function (e) {
        e.preventDefault();
        var deptVal = document.getElementById('bulkDept').value;
        var lines = document.getElementById('bulkLines').value;
        var pw = document.getElementById('bulkSharedPassword').value;
        if (!deptVal || !lines.trim() || pw.length < 8) {
            showToast('Department, at least one student line, and a password (8+ characters) are required.', 'error');
            return;
        }
        setBulkImportSubmitting(true);
        try {
            var bulkChecked = [].slice.call(document.querySelectorAll('#bulkCourseChecks input[name="bulk_course"]:checked'));
            var bulkCourseIds = bulkChecked.map(function (x) { return parseInt(x.value, 10); }).filter(function (id) { return !isNaN(id) && id > 0; });
            var bulkPayload = {
                action: 'bulk_import_students',
                department_id: parseInt(deptVal, 10),
                shared_password: pw,
                students: lines
            };
            if (bulkCourseIds.length) bulkPayload.course_ids = bulkCourseIds;
            var lockedClass = parseInt(document.getElementById('bulkClassCourseInput').value, 10);
            if (!isNaN(lockedClass) && lockedClass > 0) {
                bulkPayload.class_course_id = lockedClass;
            }
            await postAdmin(bulkPayload);
            showToast('Students imported.', 'success');
            setModalOpen('modalUser', false);
            e.target.reset();
            await loadLookups();
            loadDashboard();
        } catch (err) {
            showToast(err.message || 'Import failed.', 'error');
        } finally {
            setBulkImportSubmitting(false);
        }
    });

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
            showToast('Class created.', 'success');
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

    var deactivatePrompts = { user: 'Deactivate this user account? They will no longer be able to sign in.', course: 'Deactivate this class offering? It will be hidden from new scheduling and roster actions.', department: 'Deactivate this department?' };

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

    function renderAdminEnrollmentGaps(gaps) {
        var max = 12;
        var slice = gaps.slice(0, max);
        var lines = slice
            .map(function (g) {
                return (
                    '<li><span class="admin-enrollment-gaps__sheet">' +
                    escapeHtml(g.sheet_title || '—') +
                    '</span> · ' +
                    escapeHtml(g.course_code || '') +
                    ' · <span class="admin-enrollment-gaps__status">' +
                    escapeHtml(g.sheet_status || '') +
                    '</span> · ' +
                    escapeHtml(g.department_name || '') +
                    '</li>'
                );
            })
            .join('');
        var more =
            gaps.length > max
                ? '<p class="admin-enrollment-gaps__more">Showing ' + max + ' of ' + gaps.length + '. Enroll students under <strong>Manage → Classes</strong> for each course.</p>'
                : '';
        return (
            '<div class="admin-enrollment-gaps" role="region" aria-label="Evaluation roster gaps">' +
            '<div class="admin-enrollment-gaps__icon" aria-hidden="true">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.85"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' +
            '</div>' +
            '<div class="admin-enrollment-gaps__body">' +
            '<h3 class="admin-enrollment-gaps__title">Roster gap</h3>' +
            '<p class="admin-enrollment-gaps__lead">These evaluations are in <strong>draft</strong>, <strong>scheduled</strong>, or <strong>open</strong>, but the linked class has <strong>no enrollments</strong>. Students cannot submit until you add rosters (<strong>Manage → Classes → Class roster</strong>).</p>' +
            '<ul class="admin-enrollment-gaps__list">' +
            lines +
            '</ul>' +
            more +
            '</div></div>'
        );
    }

    function loadDashboard() {
        var statsEl = document.getElementById('statsGrid');
        var gapEl = document.getElementById('adminGapBanner');
        function errStats(msg) {
            if (gapEl) {
                gapEl.hidden = true;
                gapEl.innerHTML = '';
            }
            statsEl.innerHTML =
                '<div class="admin-overview-error" role="alert">' + escapeHtml(msg) + '</div>';
        }
        function ovTile(iconSvg, title, value, caption, modifier) {
            var mod = modifier ? ' admin-overview-tile--' + modifier : '';
            return (
                '<article class="admin-overview-tile' +
                mod +
                '">' +
                '<div class="admin-overview-tile__top">' +
                '<span class="admin-overview-tile__icon" aria-hidden="true">' +
                iconSvg +
                '</span>' +
                '<div class="admin-overview-tile__body">' +
                '<h3 class="admin-overview-tile__title">' +
                escapeHtml(title) +
                '</h3>' +
                '<p class="admin-overview-tile__value">' +
                escapeHtml(String(value)) +
                '</p>' +
                '</div></div>' +
                (caption
                    ? '<p class="admin-overview-tile__caption">' + escapeHtml(caption) + '</p>'
                    : '') +
                '</article>'
            );
        }
        Promise.all([
            fetch('/api/analytics.php?action=system_stats', { credentials: 'same-origin', headers: { Accept: 'application/json' } }).then(function (r) {
                return r.json().then(function (d) {
                    return { r: r, d: d };
                });
            }),
            fetch('/api/analytics.php?action=enrollment_gaps', { credentials: 'same-origin', headers: { Accept: 'application/json' } }).then(function (r) {
                return r.json().then(function (d) {
                    return { r: r, d: d };
                });
            })
        ])
            .then(function (pair) {
                var x = pair[0];
                var gx = pair[1];
                if (gapEl) {
                    if (gx.r.ok && gx.d.success && gx.d.gaps && gx.d.gaps.length) {
                        gapEl.hidden = false;
                        gapEl.innerHTML = renderAdminEnrollmentGaps(gx.d.gaps);
                    } else {
                        gapEl.hidden = true;
                        gapEl.innerHTML = '';
                    }
                }
                if (x.r.status === 401) {
                    errStats('Session expired. Sign in again.');
                    return;
                }
                if (!x.r.ok || !x.d.success) {
                    errStats(x.d.message || 'HTTP ' + x.r.status);
                    refreshManageTableIfNeeded();
                    return;
                }
                var s = x.d.stats || {};
                var usersActive =
                    typeof s.total_users_active === 'number'
                        ? s.total_users_active
                        : (s.total_students || 0) + (s.total_instructors || 0) + (s.total_staff || 0);
                var svg = function (paths) {
                    return (
                        '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.85" aria-hidden="true">' +
                        paths +
                        '</svg>'
                    );
                };
                var icUsers = svg(
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'
                );
                var icBuilding = svg(
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
                );
                var icBook = svg(
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'
                );
                var icInbox = svg(
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-7 7m7-7H3m17 0H3"/>'
                );
                var icStar = svg(
                    '<path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'
                );
                var studs = s.total_students || 0;
                var instr = s.total_instructors || 0;
                var staff = s.total_staff || 0;
                var dept = s.total_departments || 0;
                var courses = s.total_courses_active || 0;
                var sheets = s.total_evaluations || 0;
                var openEv = s.open_evaluations || 0;
                var pendingRv = s.pending_reviews || 0;
                var subs = s.total_submissions || 0;
                var avgScore = s.system_avg_score != null ? Number(s.system_avg_score) : 0;
                var avgStr =
                    avgScore > 0
                        ? avgScore.toFixed(2)
                        : (subs > 0 ? '—' : '0');

                statsEl.innerHTML =
                    '<div class="admin-overview-grid">' +
                    ovTile(icUsers, 'Active accounts', usersActive, studs + ' students · ' + instr + ' instructors · ' + staff + ' staff', 'users') +
                    ovTile(icBuilding, 'Departments', dept, 'Active organizational units', 'orgs') +
                    ovTile(icBook, 'Courses in catalog', courses, 'Active courses available for evaluations', '') +
                    '</div>' +
                    '<div class="admin-overview-subgrid">' +
                    '<div class="admin-overview-strip">' +
                    '<h4 class="admin-overview-strip__title">Evaluation pipeline</h4>' +
                    '<ul class="admin-overview-strip__list">' +
                    '<li><span class="admin-overview-strip__k">Collecting responses</span> <strong>' +
                    openEv +
                    '</strong> open</li>' +
                    '<li><span class="admin-overview-strip__k">Closed — ready for review</span> <strong>' +
                    pendingRv +
                    '</strong></li>' +
                    '<li><span class="admin-overview-strip__k">Evaluation sheets total</span> <strong>' +
                    sheets +
                    '</strong></li>' +
                    '</ul></div>' +
                    '<div class="admin-overview-strip admin-overview-strip--accent">' +
                    '<h4 class="admin-overview-strip__title">Response quality</h4>' +
                    '<div class="admin-overview-mini">' +
                    ovTile(icStar, 'System avg. rating', avgStr, 'Across rubric responses' + (subs <= 0 ? ' (no data yet).' : '.'), '') +
                    '</div>' +
                    '<div class="admin-overview-mini admin-overview-mini--tight">' +
                    ovTile(icInbox, 'Student submissions', subs, 'Completed evaluation submissions', '') +
                    '</div>' +
                    '</div></div>';

                refreshManageTableIfNeeded();
            })
            .catch(function () {
                if (gapEl) {
                    gapEl.hidden = true;
                    gapEl.innerHTML = '';
                }
                errStats('Could not load dashboard.');
                refreshManageTableIfNeeded();
            });
    }

    function tableColspan() {
        if (currentTab === 'courses') return 8;
        if (currentTab === 'departments') return 6;
        if (currentTab === 'users' && currentUserRoleFilter) return 5;
        return 6;
    }

    function syncClassHubEnrollBtnState() {
        var picker = document.getElementById('classHubStudentPicker');
        var btn = document.getElementById('btnClassHubEnrollSelected');
        if (!picker || !btn || btn.dataset.loading === '1') return;
        var n = picker.querySelectorAll('input[type="checkbox"][name="class_hub_enroll"]:checked').length;
        btn.disabled = n === 0;
    }

    function renderClassHubRosterContent(rosterEl, rosterRes, rosterData) {
        if (!rosterRes.ok || !rosterData.success) {
            rosterEl.innerHTML =
                '<p class="admin-class-hub-error">' + escapeHtml(rosterData.message || 'Could not load roster.') + '</p>';
            return;
        }
        var n = parseInt(rosterData.count, 10) || 0;
        var studs = Array.isArray(rosterData.students) ? rosterData.students : [];
        if (n === 0) {
            rosterEl.innerHTML =
                '<p class="admin-class-hub-empty">No students on the roster yet. Enroll existing students below, or use <strong>Bulk add new students</strong> for new accounts.</p>';
            return;
        }
        var lines = studs
            .map(function (u) {
                return (
                    '<li>' +
                    escapeHtml((u.full_name || u.username || '').trim() || '—') +
                    ' <span class="admin-class-hub-roster__id">(' +
                    escapeHtml(u.username || '') +
                    ')</span></li>'
                );
            })
            .join('');
        var more =
            n > studs.length
                ? '<p class="admin-class-hub-more">Showing ' + studs.length + ' of ' + n + ' enrolled.</p>'
                : '';
        rosterEl.innerHTML =
            '<p class="admin-class-hub-count"><strong>' +
            n +
            '</strong> enrolled</p><ul class="admin-class-hub-roster-list">' +
            lines +
            '</ul>' +
            more;
    }

    function renderClassHubStudentPicker(enrollBtn, pickerEl, pickRes, pickData) {
        if (!pickRes.ok || !pickData.success) {
            enrollBtn.disabled = true;
            pickerEl.innerHTML =
                '<p class="admin-class-hub-error">' + escapeHtml(pickData.message || 'Could not load students for this class.') + '</p>';
            return;
        }
        var all = Array.isArray(pickData.students) ? pickData.students : [];
        var open = all.filter(function (s) {
            return !s.enrolled;
        });
        if (!all.length) {
            enrollBtn.disabled = true;
            pickerEl.innerHTML =
                '<p class="admin-class-hub-empty">No active students in this department yet. Add accounts via Manage → Users or <strong>Bulk add new students</strong>.</p>';
            return;
        }
        if (!open.length) {
            enrollBtn.disabled = true;
            pickerEl.innerHTML =
                '<p class="admin-class-hub-empty">Every active student in this department is already on this roster.</p>';
            return;
        }
        pickerEl.innerHTML =
            '<p class="admin-class-hub-pick-hint">' +
            open.length +
            ' student(s) available to add.</p>' +
            '<ul class="admin-class-hub-pick-list">' +
            open
                .map(function (s) {
                    var id = parseInt(s.id, 10);
                    return (
                        '<li><label class="admin-class-hub-pick-item">' +
                        '<input type="checkbox" name="class_hub_enroll" value="' +
                        id +
                        '">' +
                        '<span>' +
                        escapeHtml((s.full_name || s.username || '').trim() || '—') +
                        ' <span class="admin-class-hub-roster__id">(' +
                        escapeHtml(s.username || '') +
                        ')</span></span></label></li>'
                    );
                })
                .join('') +
            '</ul>';
        enrollBtn.disabled = true;
    }

    async function reloadClassHubPanel() {
        if (!adminClassHubContext || !adminClassHubContext.id) return;
        var cid = adminClassHubContext.id;
        var roster = document.getElementById('classHubRoster');
        var picker = document.getElementById('classHubStudentPicker');
        var enrollBtn = document.getElementById('btnClassHubEnrollSelected');
        roster.innerHTML = '<p class="admin-class-hub-loading">Updating…</p>';
        picker.innerHTML = '<p class="admin-class-hub-loading">Updating…</p>';
        enrollBtn.disabled = true;
        enrollBtn.dataset.loading = '1';
        try {
            var rosterRes = await fetch(
                '/api/evaluations.php?action=course_enrollment_preview&course_id=' + encodeURIComponent(cid),
                { credentials: 'same-origin', headers: { Accept: 'application/json' } }
            );
            var pickRes = await fetch(
                '/api/admin.php?action=students_for_class_enrollment&course_id=' + encodeURIComponent(cid),
                { credentials: 'same-origin', headers: { Accept: 'application/json' } }
            );
            var rosterData = await rosterRes.json().catch(function () {
                return {};
            });
            var pickData = await pickRes.json().catch(function () {
                return {};
            });
            renderClassHubRosterContent(roster, rosterRes, rosterData);
            renderClassHubStudentPicker(enrollBtn, picker, pickRes, pickData);
        } catch (e) {
            roster.innerHTML = '<p class="admin-class-hub-error">Could not refresh roster.</p>';
            picker.innerHTML = '<p class="admin-class-hub-error">Could not refresh student list.</p>';
        } finally {
            delete enrollBtn.dataset.loading;
            syncClassHubEnrollBtnState();
        }
    }

    async function openClassHubModal(c) {
        adminClassHubContext = {
            id: parseInt(c.id, 10),
            department_id: parseInt(c.department_id, 10),
            code: c.code || '',
            title: c.title || '',
            instructor_name: c.instructor_name || '',
            semester: c.semester || '',
            academic_year: c.academic_year || '',
            department_name: c.department_name || ''
        };
        var meta = document.getElementById('classHubMeta');
        var roster = document.getElementById('classHubRoster');
        var picker = document.getElementById('classHubStudentPicker');
        var enrollBtn = document.getElementById('btnClassHubEnrollSelected');
        enrollBtn.disabled = true;
        delete enrollBtn.dataset.loading;
        meta.innerHTML =
            '<p class="admin-class-hub-meta__title"><strong>' +
            escapeHtml(adminClassHubContext.code) +
            '</strong> · ' +
            escapeHtml(adminClassHubContext.title) +
            '</p>' +
            '<p class="admin-class-hub-meta__sub">' +
            escapeHtml(adminClassHubContext.department_name) +
            ' · ' +
            escapeHtml(adminClassHubContext.instructor_name) +
            ' · ' +
            escapeHtml(String(adminClassHubContext.semester)) +
            ' ' +
            escapeHtml(String(adminClassHubContext.academic_year)) +
            '</p>';
        roster.innerHTML = '<p class="admin-class-hub-loading">Loading roster…</p>';
        picker.innerHTML = '<p class="admin-class-hub-loading">Loading eligible students…</p>';
        setModalOpen('modalClassHub', true);
        try {
            var rosterRes = await fetch(
                '/api/evaluations.php?action=course_enrollment_preview&course_id=' + encodeURIComponent(adminClassHubContext.id),
                { credentials: 'same-origin', headers: { Accept: 'application/json' } }
            );
            var pickRes = await fetch(
                '/api/admin.php?action=students_for_class_enrollment&course_id=' + encodeURIComponent(adminClassHubContext.id),
                { credentials: 'same-origin', headers: { Accept: 'application/json' } }
            );
            var rosterData = await rosterRes.json().catch(function () {
                return {};
            });
            var pickData = await pickRes.json().catch(function () {
                return {};
            });
            renderClassHubRosterContent(roster, rosterRes, rosterData);
            renderClassHubStudentPicker(enrollBtn, picker, pickRes, pickData);
            syncClassHubEnrollBtnState();
        } catch (err) {
            roster.innerHTML = '<p class="admin-class-hub-error">Network error loading roster.</p>';
            picker.innerHTML = '<p class="admin-class-hub-error">Network error loading students.</p>';
            enrollBtn.disabled = true;
        }
    }

    function loadTable() {
        var body = document.getElementById('tableBody');
        var head = document.getElementById('tableHead');
        var cs = tableColspan();
        var url = '/api/admin.php?action=list&entity=' + encodeURIComponent(currentTab);
        if (currentTab === 'users' && currentUserRoleFilter) {
            url += '&role=' + encodeURIComponent(currentUserRoleFilter);
        }
        fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json().then(function (d) { return { r: r, d: d }; }); })
            .then(function (x) {
                if (x.r.status === 401) {
                    if (currentTab === 'courses') lastManageCoursesPayload = [];
                    body.innerHTML = '<tr><td colspan="' + cs + '" class="admin-manage-msg admin-manage-msg--error">Session expired — sign in again.</td></tr>';
                    document.getElementById('managePanelCount').textContent = '';
                    return;
                }
                if (!x.r.ok || !x.d.success) {
                    if (currentTab === 'courses') lastManageCoursesPayload = [];
                    body.innerHTML = '<tr><td colspan="' + cs + '" class="admin-manage-msg admin-manage-msg--error">' + escapeHtml(x.d.message || ('HTTP ' + x.r.status)) + '</td></tr>';
                    document.getElementById('managePanelCount').textContent = '';
                    return;
                }
                var data = Array.isArray(x.d.data) ? x.d.data : [];
                if (currentTab === 'courses') {
                    lastManageCoursesPayload = data;
                } else {
                    lastManageCoursesPayload = [];
                }
                var cnt = document.getElementById('managePanelCount');
                if (cnt) cnt.textContent = data.length === 0 ? 'No records' : (data.length === 1 ? '1 record' : data.length + ' records');

                if (currentTab === 'users') {
                    var showRoleCol = !currentUserRoleFilter;
                    var thead = '<tr><th scope="col">ID</th><th scope="col">Username</th><th scope="col">Full name</th>';
                    if (showRoleCol) thead += '<th scope="col">Role</th>';
                    thead += '<th scope="col">Status</th><th scope="col" class="admin-manage-table__actions">Actions</th></tr>';
                    head.innerHTML = thead;
                    var emptyLbl = !currentUserRoleFilter
                        ? 'No users in the directory.'
                        : ({
                            student: 'No students match this filter.',
                            instructor: 'No instructors yet.',
                            dean: 'No deans.',
                            hr: 'No HR accounts.',
                            admin: 'No other administrators.'
                        }[currentUserRoleFilter] || 'No accounts for this role.');
                    body.innerHTML = data.map(function (u) {
                        var active = u.status === 'active';
                        var uidNum = parseInt(u.id, 10);
                        var canDeact = active && uidNum !== currentUserId;
                        var canResetPw = active && uidNum !== currentUserId;
                        var btn = canDeact
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="user" data-id="' + u.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled title="' + (!active ? 'Already inactive' : 'Cannot deactivate your own account') + '">—</button>';
                        var resetTitleSafe = '';
                        if (!canResetPw) {
                            resetTitleSafe = escapeAttr(!active ? 'Inactive account' : 'Use Settings to change your own password');
                        }
                        var resetBtn = canResetPw
                            ? '<button type="button" class="table-action-btn table-action-btn--resetpw" data-act="reset-password" data-user-id="' +
                              uidNum +
                              '" data-user-role="' +
                              escapeAttr(u.role || '') +
                              '" data-user-login="' +
                              escapeAttr(u.username || '') +
                              '" data-user-fullname="' +
                              escapeAttr(u.full_name || '') +
                              '">Reset password</button>'
                            : '<button type="button" class="table-action-btn" disabled title="' + resetTitleSafe + '">—</button>';
                        var row = '<tr><td class="admin-manage-mono">' + u.id + '</td><td class="admin-manage-strong">' + escapeHtml(u.username) + '</td><td>' + escapeHtml(u.full_name) + '</td>';
                        if (showRoleCol) row += '<td><span class="admin-chip admin-chip--role">' + escapeHtml(u.role) + '</span></td>';
                        row +=
                            '<td><span class="status-badge ' +
                            (active ? 'status-active' : 'status-pending') +
                            '">' +
                            escapeHtml(u.status) +
                            '</span></td><td class="admin-manage-table__actions">' +
                            '<span class="admin-manage-actions-pair">' +
                            resetBtn +
                            btn +
                            '</span></td></tr>';
                        return row;
                    }).join('') || ('<tr><td colspan="' + cs + '" class="admin-manage-msg admin-manage-msg--empty">' + escapeHtml(emptyLbl) + '</td></tr>');
                } else if (currentTab === 'courses') {
                    head.innerHTML =
                        '<tr><th scope="col">Code</th><th scope="col">Title</th><th scope="col">Department</th><th scope="col">Instructor</th><th scope="col">Term</th><th scope="col" class="admin-manage-mono admin-th-enrolled">Enrolled</th><th scope="col">Status</th><th scope="col" class="admin-manage-table__actions">Actions</th></tr>';
                    body.innerHTML = data.map(function (c) {
                        var active = c.status === 'active';
                        var enr = c.enrollment_count != null ? parseInt(c.enrollment_count, 10) : 0;
                        if (isNaN(enr)) enr = 0;
                        var evalGap = parseInt(c.evaluation_roster_gap, 10) === 1;
                        var rosterBtn = active
                            ? '<button type="button" class="table-action-btn table-action-btn--roster" data-act="class-roster" data-course-id="' +
                              c.id +
                              '">Roster</button> '
                            : '<button type="button" class="table-action-btn" disabled title="Inactive class">Roster</button> ';
                        var btn = active
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="course" data-id="' + c.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled>—</button>';
                        var enrolledCell =
                            '<span class="admin-course-enrolled-num">' +
                            enr +
                            '</span>' +
                            (evalGap
                                ? '<br><span class="admin-course-eval-gap-chip" title="This class has an evaluation in draft, scheduled, or open, but no enrollments yet. Open Roster to add students — otherwise nobody can submit.">Eval needs roster</span>'
                                : '');
                        return (
                            '<tr class="' +
                            (evalGap ? 'admin-course-row--eval-gap' : '') +
                            '"><td class="admin-manage-mono">' +
                            escapeHtml(c.code) +
                            '</td><td>' +
                            escapeHtml(c.title) +
                            '</td><td>' +
                            escapeHtml(c.department_name) +
                            '</td><td>' +
                            escapeHtml(c.instructor_name) +
                            '</td><td>' +
                            escapeHtml(c.semester + ' ' + c.academic_year) +
                            '</td><td class="admin-course-enrolled-cell">' +
                            enrolledCell +
                            '</td><td><span class="status-badge ' +
                            (active ? 'status-active' : 'status-pending') +
                            '">' +
                            escapeHtml(c.status) +
                            '</span></td><td class="admin-manage-table__actions">' +
                            rosterBtn +
                            btn +
                            '</td></tr>'
                        );
                    }).join('') ||
                        '<tr><td colspan="' +
                        cs +
                        '" class="admin-manage-msg admin-manage-msg--empty">No class offerings yet — add one to start enrolments.</td></tr>';
                } else {
                    head.innerHTML =
                        '<tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Department head</th><th scope="col">Faculty</th><th scope="col">Status</th><th scope="col" class="admin-manage-table__actions">Actions</th></tr>';
                    body.innerHTML = data.map(function (d) {
                        var active = d.status === 'active';
                        var btn = active
                            ? '<button type="button" class="table-action-btn" data-act="deactivate" data-entity="department" data-id="' + d.id + '">Deactivate</button>'
                            : '<button type="button" class="table-action-btn" disabled>—</button>';
                        var headLabel = (d.head_name != null && String(d.head_name).trim() !== '')
                            ? escapeHtml(String(d.head_name).trim())
                            : '<span class="admin-manage-placeholder">No head assigned</span>';
                        return (
                            '<tr><td class="admin-manage-mono">' +
                            d.id +
                            '</td><td class="admin-manage-strong">' +
                            escapeHtml(d.name) +
                            '</td><td>' +
                            headLabel +
                            '</td><td>' +
                            escapeHtml(String(d.faculty_count)) +
                            '</td><td><span class="status-badge ' +
                            (active ? 'status-active' : 'status-pending') +
                            '">' +
                            escapeHtml(d.status) +
                            '</span></td><td class="admin-manage-table__actions">' +
                            btn +
                            '</td></tr>'
                        );
                    }).join('') || '<tr><td colspan="6" class="admin-manage-msg admin-manage-msg--empty">No departments yet — add one to get started.</td></tr>';
                }

                body.querySelectorAll('[data-act="deactivate"]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        deactivate(btn.getAttribute('data-entity'), parseInt(btn.getAttribute('data-id'), 10));
                    });
                });
            })
            .catch(function () {
                if (currentTab === 'courses') lastManageCoursesPayload = [];
                body.innerHTML = '<tr><td colspan="' + cs + '" class="admin-manage-msg admin-manage-msg--error">Could not load directory.</td></tr>';
                var c = document.getElementById('managePanelCount');
                if (c) c.textContent = '';
            });
    }

    document.getElementById('tableBody').addEventListener('click', function (e) {
        var br = e.target.closest('[data-act="class-roster"]');
        if (!br || br.disabled) return;
        var cid = parseInt(br.getAttribute('data-course-id'), 10);
        if (isNaN(cid) || cid <= 0) return;
        var row = lastManageCoursesPayload.find(function (c) {
            return parseInt(c.id, 10) === cid;
        });
        if (row) openClassHubModal(row);
    });

    var btnClassHubBulk = document.getElementById('btnClassHubBulkAdd');
    if (btnClassHubBulk) {
        btnClassHubBulk.addEventListener('click', function () {
            if (!adminClassHubContext || !adminClassHubContext.id) return;
            pendingBulkClassContext = {
                courseId: adminClassHubContext.id,
                deptId: adminClassHubContext.department_id,
                label: adminClassHubContext.code + ' — ' + adminClassHubContext.title
            };
            setModalOpen('modalClassHub', false);
            openUserModal('student');
        });
    }

    var modalClassHubNode = document.getElementById('modalClassHub');
    if (modalClassHubNode) {
        modalClassHubNode.addEventListener('change', function (e) {
            var t = e.target;
            if (t && t.matches && t.matches('#classHubStudentPicker input[name="class_hub_enroll"]')) {
                syncClassHubEnrollBtnState();
            }
        });
    }

    var btnClassHubEnrollSel = document.getElementById('btnClassHubEnrollSelected');
    if (btnClassHubEnrollSel) {
        btnClassHubEnrollSel.addEventListener('click', async function () {
            if (!adminClassHubContext || !adminClassHubContext.id) return;
            var ids = []
                .slice.call(document.querySelectorAll('#classHubStudentPicker input[name="class_hub_enroll"]:checked'))
                .map(function (cb) {
                    return parseInt(cb.value, 10);
                })
                .filter(function (id) {
                    return !isNaN(id) && id > 0;
                });
            if (!ids.length) return;
            var bulkAddBtn = document.getElementById('btnClassHubBulkAdd');
            btnClassHubEnrollSel.dataset.loading = '1';
            btnClassHubEnrollSel.disabled = true;
            if (bulkAddBtn) bulkAddBtn.disabled = true;
            try {
                var resp = await postAdmin({
                    action: 'enroll_students_in_course',
                    course_id: adminClassHubContext.id,
                    student_ids: ids
                });
                showToast(resp.message || 'Students enrolled.', 'success');
                await reloadClassHubPanel();
                refreshManageTableIfNeeded();
                await loadLookups();
            } catch (err) {
                showToast(err.message || 'Enrollment failed.', 'error');
            } finally {
                if (bulkAddBtn) bulkAddBtn.disabled = false;
                delete btnClassHubEnrollSel.dataset.loading;
                syncClassHubEnrollBtnState();
            }
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

    document.getElementById('formChangePassword').addEventListener('submit', async function (e) {
        e.preventDefault();
        var msgEl = document.getElementById('cpFormMsg');
        var nwEl = document.getElementById('cpNew');
        var cfEl = document.getElementById('cpConfirm');
        var nw = nwEl.value;
        msgEl.hidden = true;
        if (nw !== cfEl.value) {
            msgEl.textContent = 'New password and confirmation do not match.';
            msgEl.className = 'admin-settings-form-msg admin-settings-form-msg--error';
            msgEl.hidden = false;
            return;
        }
        var btn = document.getElementById('cpSubmit');
        var cpRedirectAfterPw = false;
        var cpBtnDefaultLabel = 'Update password & sign out';
        btn.disabled = true;
        try {
            var r = await fetch('/api/auth.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify({
                    action: 'change_password',
                    current_password: document.getElementById('cpCurrent').value,
                    new_password: nw
                })
            });
            var d = await r.json().catch(function () {
                return {};
            });
            if (!r.ok || !d.success) {
                msgEl.textContent = d.message || 'HTTP ' + r.status;
                msgEl.className = 'admin-settings-form-msg admin-settings-form-msg--error';
                msgEl.hidden = false;
                return;
            }
            if (d.sign_in_again) {
                cpRedirectAfterPw = true;
                msgEl.textContent = d.message || 'Redirecting to sign-in…';
                msgEl.className = 'admin-settings-form-msg admin-settings-form-msg--success';
                msgEl.hidden = false;
                showToast(d.message || 'Signed out — sign in with your new password.', 'success');
                btn.textContent = 'Signing out…';
                setTimeout(function () {
                    window.location.href = 'login.php';
                }, 950);
                return;
            }
            e.target.reset();
            msgEl.textContent = d.message || 'Password updated.';
            msgEl.className = 'admin-settings-form-msg admin-settings-form-msg--success';
            msgEl.hidden = false;
            showToast(d.message || 'Password updated.', 'success');
            refreshAdminSessionStatus();
        } catch (err2) {
            msgEl.textContent = 'Could not reach the server.';
            msgEl.className = 'admin-settings-form-msg admin-settings-form-msg--error';
            msgEl.hidden = false;
        } finally {
            if (!cpRedirectAfterPw) {
                btn.disabled = false;
                btn.textContent = cpBtnDefaultLabel;
            }
        }
    });

    updateAddButtons();
    loadLookups().then(function () { loadDashboard(); });
})();
</script>
<script src="<?= htmlspecialchars($ab) ?>/js/dashboard-sidebar.js"></script>
</body>
</html>
