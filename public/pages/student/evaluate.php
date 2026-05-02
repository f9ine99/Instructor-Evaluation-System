<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('student');

AuthService::getSanitizedUserHydratedFromDb();
$user = AuthService::getCurrentUser();
if ($user && !empty($user['must_change_password'])) {
    header('Location: first-login-password.php');
    exit;
}
$assetBase = '../../assets';
?>
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
    <title>Student Evaluation - Instructor Assessment</title>
    <link rel="stylesheet" href="<?= $assetBase ?>/css/variables.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/base.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/layout.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/components.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/forms.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/evaluate.css">
</head>
<body class="evaluate-form-page">
    <div class="container">
        <div class="header">
            <div>
                <h1 class="header__title">Instructor Evaluation</h1>
            </div>
            <nav class="navbar" aria-label="Site and account">
                <a href="../common/home.php" class="nav-home-btn eval-nav-home">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="eval-nav-icon" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Home
                </a>
                <button type="button" class="nav-logout-btn" id="logoutBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="eval-nav-icon" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
                <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </nav>
        </div>

        <div class="card" id="evalSelector">
            <h2 class="card__title">Available evaluations</h2>
            <p class="eval-selector-help">Choose a course below. You can complete each open evaluation once.</p>
            <div id="evalList" class="eval-picker-list">
                <p class="eval-list-status eval-list-status--loading">Loading evaluations…</p>
            </div>
        </div>

        <div id="evalFormWrapper" hidden>
            <div class="eval-back-wrap">
                <button type="button" class="nav-logout-btn" id="backToListBtn" aria-label="Return to evaluation list">← Change course</button>
            </div>

            <aside class="eval-important" role="region" aria-labelledby="eval-important-heading">
                <div class="eval-important__inner">
                    <h2 id="eval-important-heading" class="eval-important__title">Please note</h2>
                    <p class="eval-important__lead">
                        Rate every question, then submit. Responses are recorded only when you submit; if you leave without submitting, your selections are not saved. Comments are optional.
                    </p>
                    <div class="eval-important__deadline-row" id="evalDeadlineBlock" hidden>
                        <span class="eval-important__deadline-label">Closes</span>
                        <time id="deadlineDisplay" class="eval-important__deadline"></time>
                    </div>
                    <p class="eval-important__meta" id="evalNoDeadlineNote" hidden>No closing date is listed for this form.</p>
                </div>
            </aside>

            <details class="card eval-course-details" id="evalCourseDetails">
                <summary class="eval-course-details__summary">
                    <span class="eval-course-details__summary-label">Course information</span>
                    <span class="eval-course-details__summary-line" id="evalCourseSummary">—</span>
                    <span class="eval-course-details__caret" aria-hidden="true"></span>
                </summary>
                <div class="eval-course-details__body">
                    <div class="course-info" id="courseInfo">
                        <div class="info-item">
                            <div class="info-item__label">Faculty Member</div>
                            <div class="info-item__value" id="faculty">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Department</div>
                            <div class="info-item__value" id="dept">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Course Title</div>
                            <div class="info-item__value" id="course">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Program</div>
                            <div class="info-item__value" id="program">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Year</div>
                            <div class="info-item__value" id="studentYear">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Semester</div>
                            <div class="info-item__value" id="semester">—</div>
                        </div>
                        <div class="info-item">
                            <div class="info-item__label">Academic Year</div>
                            <div class="info-item__value" id="year">—</div>
                        </div>
                    </div>
                </div>
            </details>

            <form id="evalForm" novalidate>
                <div class="card eval-questions-card">
                    <h2 class="card__title">Evaluation Questions</h2>
                    <p class="eval-questions-card__intro">Use <strong>Next</strong> to move forward. Choose a score on each step before continuing. You can also swipe left or right on the question area to change steps.</p>
                    <div class="eval-scale-compact" aria-label="Rating scale">
                        <div class="eval-scale-compact__inner">
                            <span class="eval-scale-compact__pill"><strong>5</strong> Strongly agree</span>
                            <span class="eval-scale-compact__pill"><strong>4</strong> Agree</span>
                            <span class="eval-scale-compact__pill eval-scale-compact__pill--mid"><strong>3</strong> Neutral</span>
                            <span class="eval-scale-compact__pill eval-scale-compact__pill--low"><strong>2</strong> Disagree</span>
                            <span class="eval-scale-compact__pill eval-scale-compact__pill--low"><strong>1</strong> Strongly disagree</span>
                        </div>
                    </div>
                    <div class="eval-onboard" id="evalOnboard" hidden>
                        <div class="eval-onboard__top">
                            <div class="eval-onboard__progress" role="progressbar" aria-valuemin="1" aria-valuemax="1" aria-valuenow="1" aria-label="Progress through questions" id="evalOnboardProgressWrap">
                                <div class="eval-onboard__progress-fill" id="evalOnboardProgress"></div>
                            </div>
                            <p class="eval-onboard__label" id="evalOnboardLabel" role="status">Question 1 of 1</p>
                        </div>
                        <div class="eval-onboard__viewport" id="evalOnboardViewport">
                            <div class="eval-onboard__track" id="evalOnboardTrack"></div>
                        </div>
                        <nav class="eval-onboard__nav" aria-label="Question navigation">
                            <button type="button" class="btn btn--secondary eval-onboard__prev" id="evalOnboardPrev" disabled>Back</button>
                            <button type="button" class="btn btn--primary eval-onboard__next" id="evalOnboardNext" disabled>Next</button>
                        </nav>
                    </div>
                    <p class="eval-onboard-empty" id="evalOnboardEmpty" hidden>No questions were loaded for this evaluation.</p>
                </div>

                <div class="card" id="evalCommentsCard">
                    <h2 class="card__title">Additional Comments</h2>
                    <div class="comments">
                        <label class="comments__label" for="comments">Share your thoughts about this instructor (Optional)</label>
                        <textarea id="comments" name="comments" class="comments__textarea" placeholder="Your comments help improve teaching quality..."></textarea>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn--secondary" id="resetBtn">Reset Form</button>
                    <button type="submit" class="btn btn--primary" id="submitBtn">Submit Evaluation</button>
                </div>
            </form>

            <div class="eval-privacy-strip" role="note">
                <svg class="eval-privacy-strip__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <p class="eval-privacy-strip__text">Your evaluation is anonymous. No personal identifiers are stored with your responses.</p>
            </div>
        </div>
    </div>

    <div id="evalToast" class="eval-toast" role="alert" aria-live="polite"></div>

    <script>
        function loadTheme() {
            const s = localStorage.getItem('theme') || 'dark';
            const root = document.documentElement;
            root.classList.toggle('light-mode', s === 'light');
            document.body.classList.remove('light-mode');
            const sun = document.querySelector('.sun-icon');
            const moon = document.querySelector('.moon-icon');
            sun.style.display = s === 'light' ? 'none' : 'block';
            moon.style.display = s === 'light' ? 'block' : 'none';
        }
        function toggleTheme() {
            const light = document.documentElement.classList.toggle('light-mode');
            document.body.classList.remove('light-mode');
            localStorage.setItem('theme', light ? 'light' : 'dark');
            const sun = document.querySelector('.sun-icon');
            const moon = document.querySelector('.moon-icon');
            sun.style.display = light ? 'none' : 'block';
            moon.style.display = light ? 'block' : 'none';
        }
        loadTheme();
        document.getElementById('themeToggle').addEventListener('click', toggleTheme);

        const toastEl = document.getElementById('evalToast');
        let toastTimer;
        function showEvalToast(message, type) {
            toastEl.textContent = message;
            toastEl.className = 'eval-toast is-visible eval-toast--' + (type === 'success' ? 'success' : 'error');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => { toastEl.classList.remove('is-visible'); }, 4200);
        }

        let currentSheetId = null;
        let currentQuestions = [];
        let currentOnboardStep = 0;
        let onboardTotal = 0;

        function evalQuestionBlocks() {
            return document.querySelectorAll('#evalOnboardTrack .question');
        }

        function currentOnboardQuestionEl() {
            const slides = document.querySelectorAll('#evalOnboardTrack .eval-onboard__slide');
            return slides[currentOnboardStep]?.querySelector('.question') || null;
        }

        function isCurrentOnboardStepAnswered() {
            const q = currentOnboardQuestionEl();
            if (!q) return true;
            const hid = q.querySelector('input[type="hidden"][data-qid]');
            return Boolean(hid && hid.value !== '');
        }

        function updateOnboardAria() {
            document.querySelectorAll('#evalOnboardTrack .eval-onboard__slide').forEach((slide, i) => {
                slide.setAttribute('aria-hidden', i === currentOnboardStep ? 'false' : 'true');
            });
            const wrap = document.getElementById('evalOnboardProgressWrap');
            if (wrap && onboardTotal > 0) {
                wrap.setAttribute('aria-valuemax', String(onboardTotal));
                wrap.setAttribute('aria-valuenow', String(currentOnboardStep + 1));
            }
        }

        function syncOnboardUI() {
            const track = document.getElementById('evalOnboardTrack');
            const fill = document.getElementById('evalOnboardProgress');
            const label = document.getElementById('evalOnboardLabel');
            const prevBtn = document.getElementById('evalOnboardPrev');
            const nextBtn = document.getElementById('evalOnboardNext');
            if (!track || onboardTotal === 0) return;
            track.style.transform = 'translate3d(-' + (currentOnboardStep * 100) + '%, 0, 0)';
            const pct = ((currentOnboardStep + 1) / onboardTotal) * 100;
            if (fill) fill.style.width = pct + '%';
            if (label) label.textContent = 'Question ' + (currentOnboardStep + 1) + ' of ' + onboardTotal;
            if (prevBtn) prevBtn.disabled = currentOnboardStep === 0;
            if (nextBtn) {
                const last = currentOnboardStep >= onboardTotal - 1;
                nextBtn.textContent = last ? 'Continue to comments' : 'Next';
                nextBtn.disabled = !isCurrentOnboardStepAnswered();
            }
            updateOnboardAria();
        }

        function syncOnboardNextOnly() {
            const nextBtn = document.getElementById('evalOnboardNext');
            if (!nextBtn || onboardTotal === 0) return;
            const last = currentOnboardStep >= onboardTotal - 1;
            nextBtn.textContent = last ? 'Continue to comments' : 'Next';
            nextBtn.disabled = !isCurrentOnboardStepAnswered();
        }

        function goToOnboardStep(i) {
            currentOnboardStep = Math.max(0, Math.min(onboardTotal - 1, i));
            syncOnboardUI();
        }

        function goToFirstUnansweredSlide() {
            const slides = document.querySelectorAll('#evalOnboardTrack .eval-onboard__slide');
            for (let i = 0; i < slides.length; i++) {
                const hid = slides[i].querySelector('input[type="hidden"][data-qid]');
                if (hid && !hid.value) {
                    goToOnboardStep(i);
                    return;
                }
            }
        }

        function resetOnboard() {
            currentOnboardStep = 0;
            onboardTotal = 0;
            const track = document.getElementById('evalOnboardTrack');
            if (track) {
                track.innerHTML = '';
                track.style.transform = 'translate3d(0, 0, 0)';
            }
            const fill = document.getElementById('evalOnboardProgress');
            if (fill) fill.style.width = '0%';
            const onboardEl = document.getElementById('evalOnboard');
            const emptyEl = document.getElementById('evalOnboardEmpty');
            if (onboardEl) onboardEl.hidden = true;
            if (emptyEl) emptyEl.hidden = true;
        }

        function updateProgress() {
            evalQuestionBlocks().forEach((el) => {
                const hid = el.querySelector('input[type="hidden"][data-qid]');
                el.classList.toggle('is-unanswered', !hid || hid.value === '');
            });
            syncOnboardNextOnly();
        }

        async function loadEvaluations() {
            const list = document.getElementById('evalList');
            const emptyHtml = `
                <div class="eval-empty">
                    <p class="eval-empty__title">No evaluations right now</p>
                    <p class="eval-empty__text">You may have completed them all, none are open for your courses, or the deadline has passed.</p>
                </div>`;
            try {
                const res = await fetch('/api/submissions.php?action=eligible', {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json' }
                });
                let data;
                try {
                    data = await res.json();
                } catch (e) {
                    throw new Error('not-json');
                }

                if (res.status === 401) {
                    list.innerHTML = '<p class="eval-list-error">Session expired. <a href="login.php" class="eval-list-error__link">Sign in again</a>.</p>';
                    return;
                }
                if (res.status === 403) {
                    list.innerHTML = '<p class="eval-list-error">' + escapeHtml(data.message || 'Access denied.') + '</p>';
                    return;
                }
                if (!res.ok) {
                    list.innerHTML = '<p class="eval-list-error">' + escapeHtml(data.message || 'Could not load evaluations.') + '</p>';
                    return;
                }

                const evaluations = Array.isArray(data.evaluations) ? data.evaluations : [];
                if (!data.success || evaluations.length === 0) {
                    list.innerHTML = emptyHtml;
                    return;
                }

                list.innerHTML = evaluations.map(ev => {
                    const due = ev.end_date
                        ? `<p class="eval-picker-card__due">Due ${new Date(ev.end_date).toLocaleDateString(undefined, { dateStyle: 'medium' })}</p>`
                        : '';
                    return `
                        <button type="button" class="eval-picker-card" data-sheet-id="${ev.id}">
                            <div>
                                <p class="eval-picker-card__course">${escapeHtml(ev.course_code)} · ${escapeHtml(ev.course_title)}</p>
                                <p class="eval-picker-card__meta">${escapeHtml(ev.instructor_name)} · ${escapeHtml(ev.department_name)}</p>
                            </div>
                            <div class="eval-picker-card__right">
                                <span class="eval-badge">Open</span>
                                ${due}
                            </div>
                        </button>`;
                }).join('');

                list.querySelectorAll('.eval-picker-card').forEach(btn => {
                    btn.addEventListener('click', () => selectEvaluation(parseInt(btn.dataset.sheetId, 10)));
                });
            } catch (err) {
                list.innerHTML =
                    '<p class="eval-list-error">Could not load evaluations. Check connection and try again.</p>';
            }
        }

        function escapeHtml(s) {
            if (!s) return '';
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        async function selectEvaluation(sheetId) {
            currentSheetId = sheetId;
            try {
                const res = await fetch('/api/submissions.php?action=questions&sheet_id=' + encodeURIComponent(sheetId), {
                    credentials: 'same-origin',
                    headers: { Accept: 'application/json' }
                });
                const data = await res.json();
                if (!data.success) {
                    showEvalToast(data.message || 'Unable to load this evaluation.', 'error');
                    return;
                }

                const sheet = data.sheet;
                currentQuestions = data.questions;

                document.getElementById('faculty').textContent = sheet.instructor_name || '—';
                document.getElementById('dept').textContent = sheet.department_name || '—';
                document.getElementById('course').textContent =
                    [sheet.course_code, sheet.course_title].filter(Boolean).join(' · ') || '—';
                document.getElementById('program').textContent = sheet.program || '—';
                document.getElementById('studentYear').textContent = sheet.year_level || '—';
                document.getElementById('semester').textContent = sheet.semester || '—';
                document.getElementById('year').textContent = sheet.academic_year || '—';

                const summaryLine = document.getElementById('evalCourseSummary');
                if (summaryLine) {
                    const summaryBits = [sheet.course_code, sheet.course_title, sheet.instructor_name].filter(Boolean);
                    summaryLine.textContent = summaryBits.length ? summaryBits.join(' · ') : '—';
                }

                const deadlineEl = document.getElementById('deadlineDisplay');
                const deadlineBlock = document.getElementById('evalDeadlineBlock');
                const noDeadlineNote = document.getElementById('evalNoDeadlineNote');
                if (sheet.end_date) {
                    const d = new Date(sheet.end_date);
                    deadlineEl.dateTime = sheet.end_date.replace(' ', 'T');
                    deadlineEl.textContent = d.toLocaleString(undefined, { dateStyle: 'long', timeStyle: 'short' });
                    deadlineBlock.hidden = false;
                    noDeadlineNote.hidden = true;
                } else {
                    deadlineBlock.hidden = true;
                    noDeadlineNote.hidden = false;
                    deadlineEl.textContent = '';
                    deadlineEl.removeAttribute('datetime');
                }

                const track = document.getElementById('evalOnboardTrack');
                const onboardEl = document.getElementById('evalOnboard');
                const emptyEl = document.getElementById('evalOnboardEmpty');
                track.innerHTML = '';
                onboardTotal = data.questions.length;
                currentOnboardStep = 0;

                if (onboardTotal === 0) {
                    onboardEl.hidden = true;
                    emptyEl.hidden = false;
                } else {
                    onboardEl.hidden = false;
                    emptyEl.hidden = true;
                    data.questions.forEach((q, idx) => {
                        const slide = document.createElement('div');
                        slide.className = 'eval-onboard__slide';
                        slide.setAttribute('role', 'group');
                        slide.setAttribute('aria-roledescription', 'slide');
                        slide.setAttribute('aria-label', 'Question ' + (idx + 1) + ' of ' + onboardTotal);
                        const row = document.createElement('div');
                        row.className = 'question is-unanswered';
                        row.dataset.qid = String(q.id);
                        row.innerHTML = `
                            <div class="question__text">
                                <span class="question__number">${idx + 1}.</span>
                                ${escapeHtml(q.question_text)}
                            </div>
                            <div class="rating-group" role="group" aria-label="Rating for question ${idx + 1}">
                                ${[5, 4, 3, 2, 1].map(v =>
                                    `<button type="button" class="rating-btn" data-value="${v}" data-qid="${q.id}" aria-label="Score ${v}">${v}</button>`
                                ).join('')}
                            </div>
                            <input type="hidden" name="q_${q.id}" data-qid="${q.id}" value="">`;
                        slide.appendChild(row);
                        track.appendChild(slide);
                    });

                    track.querySelectorAll('.rating-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const block = btn.closest('.question');
                            block.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('active'));
                            btn.classList.add('active');
                            const hid = block.querySelector('input[type="hidden"][data-qid]');
                            if (hid) hid.value = btn.dataset.value;
                            updateProgress();
                        });
                    });
                    syncOnboardUI();
                }

                document.getElementById('evalSelector').hidden = true;
                document.getElementById('evalFormWrapper').hidden = false;
                document.getElementById('comments').value = '';
                updateProgress();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (err) {
                showEvalToast('Failed to load questions.', 'error');
            }
        }

        document.getElementById('backToListBtn').addEventListener('click', () => {
            document.getElementById('evalFormWrapper').hidden = true;
            document.getElementById('evalSelector').hidden = false;
            currentSheetId = null;
            currentQuestions = [];
            resetOnboard();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        document.getElementById('evalForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const hiddens = document.querySelectorAll('#evalOnboardTrack input[type="hidden"][data-qid]');
            for (const h of hiddens) {
                if (!h.value) {
                    showEvalToast('Please rate every question before submitting.', 'error');
                    goToFirstUnansweredSlide();
                    return;
                }
            }

            const ratings = {};
            hiddens.forEach(h => { ratings[h.dataset.qid] = parseInt(h.value, 10); });

            btn.textContent = 'Submitting…';
            btn.disabled = true;

            try {
                const res = await fetch('/api/submissions.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        sheet_id: currentSheetId,
                        ratings: ratings,
                        comment: document.getElementById('comments').value
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showEvalToast(data.message || 'Evaluation submitted.', 'success');
                    resetOnboard();
                    document.getElementById('evalFormWrapper').hidden = true;
                    document.getElementById('evalSelector').hidden = false;
                    loadEvaluations();
                } else {
                    showEvalToast(data.message || 'Submission failed.', 'error');
                }
            } catch (err) {
                showEvalToast('Connection error. Try again.', 'error');
            }
            btn.textContent = 'Submit Evaluation';
            btn.disabled = false;
        });

        document.getElementById('resetBtn').addEventListener('click', () => {
            if (!confirm('Are you sure you want to reset the form?')) return;
            document.getElementById('evalForm').reset();
            document.querySelectorAll('.rating-btn.active').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('#evalOnboardTrack input[type="hidden"][data-qid]').forEach(h => { h.value = ''; });
            currentOnboardStep = 0;
            updateProgress();
            syncOnboardUI();
        });

        document.getElementById('evalOnboardPrev').addEventListener('click', () => {
            if (currentOnboardStep > 0) {
                currentOnboardStep--;
                syncOnboardUI();
            }
        });

        document.getElementById('evalOnboardNext').addEventListener('click', () => {
            if (!isCurrentOnboardStepAnswered()) {
                showEvalToast('Select a score for this question to continue.', 'error');
                return;
            }
            if (currentOnboardStep < onboardTotal - 1) {
                currentOnboardStep++;
                syncOnboardUI();
            } else {
                const commentsCard = document.getElementById('evalCommentsCard');
                const ta = document.getElementById('comments');
                commentsCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => ta?.focus(), 400);
            }
        });

        let touchStartX = null;
        document.getElementById('evalOnboardViewport').addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        document.getElementById('evalOnboardViewport').addEventListener('touchend', (e) => {
            if (touchStartX == null) return;
            const dx = e.changedTouches[0].screenX - touchStartX;
            touchStartX = null;
            if (Math.abs(dx) < 50) return;
            const nextBtn = document.getElementById('evalOnboardNext');
            const prevBtn = document.getElementById('evalOnboardPrev');
            if (dx < 0) {
                if (!nextBtn.disabled) nextBtn.click();
            } else if (!prevBtn.disabled) {
                prevBtn.click();
            }
        }, { passive: true });

        document.getElementById('logoutBtn').addEventListener('click', async () => {
            if (!confirm('Log out?')) return;
            try {
                const res = await fetch('/api/auth.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    body: JSON.stringify({ action: 'logout' })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false) {
                    showEvalToast(data.message || 'Could not sign out. Try again.', 'error');
                    return;
                }
            } catch (e) {
                showEvalToast('Could not sign out. Check your connection.', 'error');
                return;
            }
            window.location.replace('login.php');
        });

        loadEvaluations();
    </script>
</body>
</html>
