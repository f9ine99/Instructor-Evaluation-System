<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('hr');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"/><script>try{localStorage.getItem('theme')==='light'&&document.documentElement.classList.add('light-mode')}catch(e){}</script><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>HR Dashboard - HOPE Evaluation System</title>
<link rel="stylesheet" href="<?=$ab?>/css/variables.css"><link rel="stylesheet" href="<?=$ab?>/css/base.css">
<link rel="stylesheet" href="<?=$ab?>/css/layout.css"><link rel="stylesheet" href="<?=$ab?>/css/components.css">
<link rel="stylesheet" href="<?=$ab?>/css/dashboards.css"></head>
<body class="hr-dashboard"><div class="admin-layout" id="dashboardLayout">
<aside class="sidebar" id="dashboardSidebar" aria-label="HR navigation"><div class="sidebar-header"><div class="sidebar-logo" title="HOPE">HOPE</div><button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-expanded="true" aria-controls="dashboardSidebar" title="Collapse sidebar"><svg class="sidebar-collapse-btn__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg></button></div>
<nav class="nav-links">
<a class="nav-item active" onclick="switchSection('overview')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg><span>Overview</span></a>
<a class="nav-item" onclick="switchSection('reports')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>Reports & Trends</span></a>
<a class="nav-item" onclick="switchSection('decisions')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg><span>Administrative Decisions</span></a>
</nav>
<div class="sidebar-footer">
<button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg><svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></button>
<a href="#" onclick="event.preventDefault();fetch('/api/auth.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>window.location.href='login.php')" class="nav-item" style="color:#ef4444;"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></a>
</div></aside>

<main class="main-content">
<div class="top-bar"><h1 class="page-title" id="pageTitle">HR Overview</h1>
<div style="display:flex;gap:16px;align-items:center;">
<span style="color:var(--text-secondary);font-size:14px;">Welcome, <?= htmlspecialchars($user['full_name']) ?></span>
<div style="width:40px;height:40px;border-radius:50%;background:var(--accent-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">HR</div>
</div></div>

<!-- OVERVIEW -->
<section id="overview" class="section-content active">
<div class="stats-grid" id="statsGrid"><p style="color:var(--text-secondary);">Loading...</p></div>
<div class="card" style="padding:32px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:16px;">
<h2 style="font-size:20px;color:var(--text-primary);margin-bottom:24px;">Recent Evaluation Activity</h2>
<div id="recentActivity"><p style="color:var(--text-secondary);">Loading...</p></div>
</div>
<!-- Performance Alerts -->
<div class="card" style="padding:32px;margin-top:24px;background:var(--card-bg);border:1px solid var(--card-border);border-radius:16px;">
<h2 style="font-size:20px;color:var(--text-primary);margin-bottom:24px;">⚠️ Performance Alerts</h2>
<div id="alertsSection"><p style="color:var(--text-secondary);">Loading...</p></div>
</div>
</section>

<!-- REPORTS -->
<section id="reports" class="section-content">
<div class="data-table-container"><table class="data-table"><thead><tr><th>Instructor Name</th><th>Department</th><th>Avg Rating</th><th>Submissions</th></tr></thead>
<tbody id="reportsTableBody"><tr><td colspan="4" style="text-align:center;color:var(--text-secondary);">Loading...</td></tr></tbody></table></div>
</section>

<!-- DECISIONS -->
<section id="decisions" class="section-content">
<div class="hr-decision-card">
<header class="hr-decision-card__header">
<div class="hr-decision-card__glyph" aria-hidden="true">
<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
</div>
<div class="hr-decision-card__head-text">
<h2 class="hr-decision-card__title">Record Administrative Decision</h2>
<p class="hr-decision-card__lead">Log HR actions linked to evaluation outcomes. Decisions are stored on this device until a server endpoint is connected.</p>
</div>
</header>
<div id="decisionSuccess" class="hr-decision-toast" role="status" aria-live="polite" hidden>
<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
<span id="decisionSuccessText">Decision saved.</span>
</div>
<form id="decisionForm" class="hr-decision-form" novalidate>
<div class="hr-decision-form__row">
<div class="form-group hr-decision-form__field">
<label class="form-label" for="instructorSelect">Instructor</label>
<div class="hr-decision-field">
<svg class="hr-decision-field__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
<select class="form-select hr-decision-select" id="instructorSelect" required>
<option value="">Choose instructor…</option>
</select>
</div>
</div>
</div>
<fieldset class="hr-decision-types">
<legend class="form-label">Decision type</legend>
<p class="hr-decision-types__hint">Select the action that best reflects the evaluation review.</p>
<div class="hr-decision-type-grid" role="radiogroup" aria-label="Decision type">
<label class="hr-decision-type-option hr-decision-type-option--commendation">
<input type="radio" name="decisionType" value="Commendation" checked>
<span class="hr-decision-type-option__icon" aria-hidden="true">★</span>
<span class="hr-decision-type-option__label">Commendation</span>
<span class="hr-decision-type-option__desc">Recognize strong performance</span>
</label>
<label class="hr-decision-type-option hr-decision-type-option--training">
<input type="radio" name="decisionType" value="Training Recommended">
<span class="hr-decision-type-option__icon" aria-hidden="true">📚</span>
<span class="hr-decision-type-option__label">Training</span>
<span class="hr-decision-type-option__desc">Recommend development</span>
</label>
<label class="hr-decision-type-option hr-decision-type-option--promotion">
<input type="radio" name="decisionType" value="Promotion Review">
<span class="hr-decision-type-option__icon" aria-hidden="true">↗</span>
<span class="hr-decision-type-option__label">Promotion</span>
<span class="hr-decision-type-option__desc">Advancement review</span>
</label>
<label class="hr-decision-type-option hr-decision-type-option--warning">
<input type="radio" name="decisionType" value="Performance Warning">
<span class="hr-decision-type-option__icon" aria-hidden="true">!</span>
<span class="hr-decision-type-option__label">Warning</span>
<span class="hr-decision-type-option__desc">Below expectations</span>
</label>
<label class="hr-decision-type-option hr-decision-type-option--contract">
<input type="radio" name="decisionType" value="Contract Renewal">
<span class="hr-decision-type-option__icon" aria-hidden="true">✓</span>
<span class="hr-decision-type-option__label">Contract</span>
<span class="hr-decision-type-option__desc">Renewal decision</span>
</label>
</div>
</fieldset>
<div class="form-group hr-decision-form__field hr-decision-form__field--notes">
<label class="form-label" for="decisionNotes">Justification / notes</label>
<textarea class="form-textarea hr-decision-textarea" id="decisionNotes" rows="5" maxlength="2000" placeholder="Summarize evaluation data, meetings held, and the rationale for this decision…" required></textarea>
<p class="hr-decision-notes-meta"><span id="decisionNotesCount">0</span> / 2000 characters</p>
</div>
<footer class="hr-decision-form__footer">
<button type="reset" class="btn--secondary hr-decision-form__clear">Clear form</button>
<button type="submit" class="btn-submit hr-decision-form__submit">
<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
Save decision
</button>
</footer>
</form>
</div>
<div class="hr-decisions-history">
<div class="hr-decisions-history__head">
<div>
<h2 class="hr-decisions-history__title">Recent decisions</h2>
<p class="hr-decisions-history__lead">Latest administrative actions recorded in this browser.</p>
</div>
</div>
<div class="data-table-container hr-decisions-history__table">
<table class="data-table"><thead><tr><th>Instructor</th><th>Decision</th><th>Date</th><th>HR staff</th></tr></thead>
<tbody id="decisionsTableBody"><tr><td colspan="4" class="hr-decisions-empty">Loading…</td></tr></tbody></table>
</div>
</div>

</section>
</main></div>

<script>
function loadTheme(){const s=localStorage.getItem('theme')||'dark';const r=document.documentElement;r.classList.toggle('light-mode',s==='light');document.body.classList.remove('light-mode');document.querySelector('.sun-icon').style.display=s==='light'?'none':'block';document.querySelector('.moon-icon').style.display=s==='light'?'block':'none';}
function toggleTheme(){const l=document.documentElement.classList.toggle('light-mode');document.body.classList.remove('light-mode');localStorage.setItem('theme',l?'light':'dark');document.querySelector('.sun-icon').style.display=l?'none':'block';document.querySelector('.moon-icon').style.display=l?'block':'none';}
loadTheme(); document.getElementById('themeToggle').addEventListener('click',toggleTheme);

function switchSection(id){document.querySelectorAll('.nav-item').forEach(i=>{i.classList.remove('active');if(i.getAttribute('onclick')?.includes(id))i.classList.add('active');});
document.querySelectorAll('.section-content').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active');
const t={'overview':'HR Overview','reports':'Reports & Trends','decisions':'Administrative Decisions'};document.getElementById('pageTitle').textContent=t[id];}

function getScoreClass(s){return s>=4.5?'score-high':s>=3.5?'score-med':'score-low';}

async function loadDashboard(){
try{
// Stats
const sRes = await fetch('/api/analytics.php?action=system_stats',{credentials:'same-origin',headers:{Accept:'application/json'}});
const sData = await sRes.json();
if(sData.success){const s=sData.stats;
document.getElementById('statsGrid').innerHTML = `
<div class="stat-card"><div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><div class="stat-info"><h3>Total Instructors</h3><div class="value">${s.total_instructors}</div></div></div>
<div class="stat-card"><div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><div class="stat-info"><h3>System Avg Score</h3><div class="value">${s.system_avg_score}</div></div></div>
<div class="stat-card"><div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><div class="stat-info"><h3>Total Submissions</h3><div class="value">${s.total_submissions}</div></div></div>`;}

// Recent activity
const aRes = await fetch('/api/analytics.php?action=recent_submissions&limit=5',{credentials:'same-origin',headers:{Accept:'application/json'}});
const aData = await aRes.json();
if(aData.success && aData.submissions.length){
document.getElementById('recentActivity').innerHTML = aData.submissions.map(a=>`
<div style="display:flex;justify-content:space-between;padding:16px;border-bottom:1px solid var(--card-border);">
<div><div style="font-weight:600;color:var(--text-primary);">Evaluation Submitted</div>
<div style="font-size:13px;color:var(--text-secondary);">Anonymous for ${a.instructor_name} (${a.course_code})</div></div>
<div style="font-size:12px;color:var(--text-secondary);">${new Date(a.submitted_at).toLocaleDateString()}</div></div>`).join('');
} else { document.getElementById('recentActivity').innerHTML='<p style="color:var(--text-muted);text-align:center;">No recent activity.</p>'; }

// Alerts
const alRes = await fetch('/api/analytics.php?action=performance_alerts',{credentials:'same-origin',headers:{Accept:'application/json'}});
const alData = await alRes.json();
if(alData.success && alData.alerts.length){
document.getElementById('alertsSection').innerHTML = alData.alerts.map(a=>`
<div style="padding:16px;margin-bottom:12px;background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.2);border-radius:12px;">
<div style="font-weight:600;color:var(--text-primary);">${a.instructor_name} — ${a.department}</div>
<div style="font-size:13px;color:#ef4444;margin-top:4px;">${a.flags.join(' • ')}</div></div>`).join('');
} else { document.getElementById('alertsSection').innerHTML='<p style="color:var(--text-muted);text-align:center;">No performance alerts. All instructors are performing well.</p>'; }

// Instructor table
const iRes = await fetch('/api/analytics.php?action=all_instructors',{credentials:'same-origin',headers:{Accept:'application/json'}});
const iData = await iRes.json();
if(iData.success){
const select = document.getElementById('instructorSelect');
document.getElementById('reportsTableBody').innerHTML = iData.instructors.map(i=>`<tr>
<td style="font-weight:600;">${i.full_name}</td><td>${i.department_name}</td>
<td><span class="score-badge ${getScoreClass(i.avg_rating)}">${i.avg_rating?parseFloat(i.avg_rating).toFixed(1):'N/A'} / 5.0</span></td>
<td>${i.total_submissions}</td></tr>`).join('');
iData.instructors.forEach(i=>{const o=document.createElement('option');o.value=i.id;o.textContent=i.full_name;select.appendChild(o);});
}

// Load decisions from DB
const dRes = await fetch('/api/analytics.php?action=recent_submissions&limit=0',{credentials:'same-origin',headers:{Accept:'application/json'}});
// For now render decisions from localStorage (will migrate to DB)
renderDecisions();
}catch(e){console.error('Dashboard error:',e);}
}

const HR_DECISION_BADGE={'Commendation':'hr-decision-badge--commendation','Training Recommended':'hr-decision-badge--training','Promotion Review':'hr-decision-badge--promotion','Performance Warning':'hr-decision-badge--warning','Contract Renewal':'hr-decision-badge--contract'};
function escapeHtml(s){const d=document.createElement('div');d.textContent=s;return d.innerHTML;}
function renderDecisions(){
const decisions=JSON.parse(localStorage.getItem('hr_decisions')||'[]');
const tbody=document.getElementById('decisionsTableBody');
if(!decisions.length){tbody.innerHTML='<tr><td colspan="4" class="hr-decisions-empty">No decisions recorded yet.</td></tr>';return;}
tbody.innerHTML=decisions.map(d=>`<tr><td class="hr-decisions-instructor">${escapeHtml(d.instructor)}</td><td><span class="hr-decision-badge ${HR_DECISION_BADGE[d.type]||'hr-decision-badge--default'}">${escapeHtml(d.type)}</span></td><td>${escapeHtml(d.date)}</td><td>${escapeHtml(d.staff)}</td></tr>`).join('');
}

function getSelectedDecisionType(){const r=document.querySelector('input[name="decisionType"]:checked');return r?r.value:'';}
function showDecisionToast(msg){const t=document.getElementById('decisionSuccess');document.getElementById('decisionSuccessText').textContent=msg||'Decision saved.';t.hidden=false;t.classList.add('is-visible');clearTimeout(showDecisionToast._t);showDecisionToast._t=setTimeout(()=>{t.classList.remove('is-visible');setTimeout(()=>{t.hidden=true;},300);},4200);}
const notesEl=document.getElementById('decisionNotes');const notesCount=document.getElementById('decisionNotesCount');
function syncNotesCount(){if(notesCount&&notesEl)notesCount.textContent=String(notesEl.value.length);}
if(notesEl){notesEl.addEventListener('input',syncNotesCount);syncNotesCount();}
document.getElementById('decisionForm').addEventListener('reset',()=>{setTimeout(()=>{const ft=document.querySelector('input[name="decisionType"][value="Commendation"]');if(ft)ft.checked=true;syncNotesCount();},0);});
document.getElementById('decisionForm').addEventListener('submit',(e)=>{
e.preventDefault();
const sel=document.getElementById('instructorSelect');
if(!sel.value){sel.focus();return;}
const notes=notesEl.value.trim();
if(notes.length<10){notesEl.focus();showDecisionToast('Please add at least 10 characters of justification.');return;}
const decision={instructor:sel.options[sel.selectedIndex].text,type:getSelectedDecisionType(),notes:notes,
date:new Date().toLocaleDateString(),staff:'<?= htmlspecialchars($user['full_name']) ?>'};
const decisions=JSON.parse(localStorage.getItem('hr_decisions')||'[]');decisions.unshift(decision);
localStorage.setItem('hr_decisions',JSON.stringify(decisions));
e.target.reset();
const firstType=document.querySelector('input[name="decisionType"][value="Commendation"]');
if(firstType)firstType.checked=true;
syncNotesCount();renderDecisions();showDecisionToast('Administrative decision recorded successfully.');
});

loadDashboard();
</script>
<script src="<?= htmlspecialchars($ab) ?>/js/dashboard-sidebar.js"></script>
</body></html>