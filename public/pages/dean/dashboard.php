<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('dean');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"/><script>try{localStorage.getItem('theme')==='light'&&document.documentElement.classList.add('light-mode')}catch(e){}</script><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Dean Dashboard - HOPE Evaluation System</title>
<link rel="stylesheet" href="<?=$ab?>/css/variables.css"><link rel="stylesheet" href="<?=$ab?>/css/base.css">
<link rel="stylesheet" href="<?=$ab?>/css/layout.css"><link rel="stylesheet" href="<?=$ab?>/css/components.css">
<link rel="stylesheet" href="<?=$ab?>/css/dashboards.css"></head>
<body><div class="admin-layout">
<aside class="sidebar"><div class="sidebar-header"><div class="sidebar-logo">HOPE</div></div>
<nav class="nav-links">
<a class="nav-item active" onclick="switchSection('overview')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg><span>Overview</span></a>
<a class="nav-item" onclick="switchSection('evaluations')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg><span>Manage Evaluations</span></a>
<a class="nav-item" onclick="switchSection('results')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>Results Viewer</span></a>
</nav>
<div class="sidebar-footer">
<button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg><svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></button>
<a href="#" onclick="event.preventDefault();fetch('/api/auth.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>window.location.href='login.php')" class="nav-item" style="color:#ef4444;"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></a>
</div></aside>

<main class="main-content">
<div class="top-bar"><h1 class="page-title" id="pageTitle">Dean Overview</h1>
<div style="display:flex;gap:16px;align-items:center;">
<span style="color:var(--text-secondary);font-size:14px;"><?= htmlspecialchars($user['full_name']) ?></span>
<div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--accent-primary),var(--accent-secondary));color:white;display:flex;align-items:center;justify-content:center;font-weight:700;"><?= strtoupper(substr($user['full_name'],0,2)) ?></div>
</div></div>

<!-- OVERVIEW -->
<section id="overview" class="section-content active">
<div class="stats-grid" id="statsGrid"><p style="color:var(--text-secondary);">Loading...</p></div>
<div class="analytics-grid">
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Department Instructor Performance</h3>
<div id="deptInstructors"><p style="color:var(--text-secondary);">Loading...</p></div></div>
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Recent Submissions</h3>
<div id="recentSubs"><p style="color:var(--text-secondary);">Loading...</p></div></div>
</div></section>

<!-- MANAGE EVALUATIONS -->
<section id="evaluations" class="section-content">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
<h2 style="font-size:20px;color:var(--text-primary);">Evaluation Sheets</h2>
<button class="btn-submit" onclick="document.getElementById('createModal').style.display='flex'">+ Create Evaluation</button>
</div>
<div class="data-table-container"><table class="data-table"><thead><tr>
<th>Title</th><th>Course</th><th>Instructor</th><th>Status</th><th>Submissions</th><th>Actions</th></tr></thead>
<tbody id="evalTableBody"><tr><td colspan="6" style="text-align:center;">Loading...</td></tr></tbody></table></div>
</section>

<!-- RESULTS VIEWER -->
<section id="results" class="section-content">
<h2 style="font-size:20px;color:var(--text-primary);margin-bottom:24px;">Full Results Browser</h2>
<div style="margin-bottom:24px;"><select class="form-select" id="resultSheetSelect"><option value="">Select an evaluation...</option></select></div>
<div id="resultView" style="display:none;">
<div class="analytics-grid">
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Score Distribution</h3><div id="scoreDistribution"></div></div>
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Student Comments</h3><div id="resultComments"></div></div>
</div></div>
</section>

<!-- CREATE MODAL -->
<div id="createModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);z-index:100;align-items:center;justify-content:center;">
<div style="background:var(--card-bg);border:1px solid var(--card-border);border-radius:20px;padding:40px;max-width:600px;width:90%;max-height:90vh;overflow-y:auto;">
<h2 style="color:var(--text-primary);margin-bottom:24px;">Create Evaluation Sheet</h2>
<form id="createForm">
<div class="form-group"><label class="form-label">Title</label><input class="form-input" id="createTitle" required placeholder="e.g., CS101 Evaluation - Semester I 2025"></div>
<div class="form-group"><label class="form-label">Description</label><textarea class="form-textarea" id="createDesc" placeholder="Brief description..." rows="2"></textarea></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
<div class="form-group"><label class="form-label">Academic Year</label><input class="form-input" id="createYear" required placeholder="2025"></div>
<div class="form-group"><label class="form-label">Semester</label><select class="form-select" id="createSemester" required><option value="I">Semester I</option><option value="II">Semester II</option><option value="Summer">Summer</option></select></div>
</div>
<div class="form-group"><label class="form-label">Course ID</label><input type="number" class="form-input" id="createCourseId" required placeholder="Course ID"></div>
<div class="form-group"><label class="form-label">Instructor ID</label><input type="number" class="form-input" id="createInstructorId" required placeholder="Instructor user ID"></div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
<div class="form-group"><label class="form-label">Start Date</label><input type="datetime-local" class="form-input" id="createStart"></div>
<div class="form-group"><label class="form-label">End Date</label><input type="datetime-local" class="form-input" id="createEnd"></div>
</div>
<div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
<button type="button" class="btn btn--secondary" onclick="document.getElementById('createModal').style.display='none'">Cancel</button>
<button type="submit" class="btn-submit">Create</button>
</div></form></div></div>
</main></div>

<script>
function loadTheme(){const s=localStorage.getItem('theme')||'dark';const r=document.documentElement;r.classList.toggle('light-mode',s==='light');document.body.classList.remove('light-mode');document.querySelector('.sun-icon').style.display=s==='light'?'none':'block';document.querySelector('.moon-icon').style.display=s==='light'?'block':'none';}
function toggleTheme(){const l=document.documentElement.classList.toggle('light-mode');document.body.classList.remove('light-mode');localStorage.setItem('theme',l?'light':'dark');document.querySelector('.sun-icon').style.display=l?'none':'block';document.querySelector('.moon-icon').style.display=l?'block':'none';}
loadTheme(); document.getElementById('themeToggle').addEventListener('click',toggleTheme);

function switchSection(id){document.querySelectorAll('.nav-item').forEach(i=>{i.classList.remove('active');if(i.getAttribute('onclick')?.includes(id))i.classList.add('active');});
document.querySelectorAll('.section-content').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active');
const t={'overview':'Dean Overview','evaluations':'Manage Evaluations','results':'Results Viewer'};document.getElementById('pageTitle').textContent=t[id];}

const statusColors = {draft:'#6b7280',scheduled:'#3b82f6',open:'#10b981',closed:'#f59e0b',reviewed:'#8b5cf6',archived:'#6b7280'};
const nextAction = {draft:'Publish',scheduled:'Open',open:'Close',closed:'Review',reviewed:'Archive'};
const nextState = {draft:'open',scheduled:'open',open:'closed',closed:'reviewed',reviewed:'archived'};

async function loadDashboard(){
try{
// Stats
const sRes = await fetch('/api/analytics.php?action=system_stats',{credentials:'same-origin',headers:{Accept:'application/json'}});
const sData = await sRes.json();
if(sData.success){const s=sData.stats;
document.getElementById('statsGrid').innerHTML=`
<div class="stat-card"><div class="stat-info"><h3>Active Evaluations</h3><div class="value">${s.open_evaluations}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Total Submissions</h3><div class="value">${s.total_submissions}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Pending Reviews</h3><div class="value">${s.pending_reviews}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Avg Score</h3><div class="value">${s.system_avg_score}</div></div></div>`;}

// Instructor performance
const iRes = await fetch('/api/analytics.php?action=all_instructors',{credentials:'same-origin',headers:{Accept:'application/json'}});
const iData = await iRes.json();
if(iData.success){
document.getElementById('deptInstructors').innerHTML = iData.instructors.length ? iData.instructors.map(i=>`
<div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--card-border);">
<span style="color:var(--text-primary);font-weight:500;">${i.full_name}</span>
<span class="score-badge ${i.avg_rating>=4?'score-high':i.avg_rating>=3?'score-med':'score-low'}">${i.avg_rating?parseFloat(i.avg_rating).toFixed(1):'N/A'}</span>
</div>`).join(''):'<p style="color:var(--text-muted);text-align:center;">No data yet.</p>';}

// Recent submissions
const rRes = await fetch('/api/analytics.php?action=recent_submissions&limit=5',{credentials:'same-origin',headers:{Accept:'application/json'}});
const rData = await rRes.json();
if(rData.success){
document.getElementById('recentSubs').innerHTML = rData.submissions.length ? rData.submissions.map(s=>`
<div style="padding:12px 0;border-bottom:1px solid var(--card-border);">
<div style="font-weight:500;color:var(--text-primary);">${s.course_code} - ${s.instructor_name}</div>
<div style="font-size:12px;color:var(--text-secondary);">${new Date(s.submitted_at).toLocaleString()}</div></div>`).join(''):'<p style="color:var(--text-muted);text-align:center;">No submissions yet.</p>';}

// Load evaluation sheets
loadEvalTable();
}catch(e){console.error(e);}
}

async function loadEvalTable(){
const r = await fetch('/api/evaluations.php?action=list',{credentials:'same-origin',headers:{Accept:'application/json'}});
const d = await r.json();
const sel = document.getElementById('resultSheetSelect');
if(d.success){
document.getElementById('evalTableBody').innerHTML = d.sheets.map(s=>`<tr>
<td style="font-weight:600;">${s.title}</td>
<td>${s.course_code}</td><td>${s.instructor_name}</td>
<td><span class="status-badge" style="background:${statusColors[s.status]}22;color:${statusColors[s.status]};border:1px solid ${statusColors[s.status]}44;">${s.status}</span></td>
<td>${s.submission_count}</td>
<td>${nextAction[s.status]?`<button class="btn-submit" style="padding:6px 16px;font-size:13px;" onclick="transitionSheet(${s.id},'${nextState[s.status]}')">${nextAction[s.status]}</button>`:'-'}</td></tr>`).join('');
// Populate results selector
sel.innerHTML='<option value="">Select an evaluation...</option>';
d.sheets.forEach(s=>{sel.innerHTML+=`<option value="${s.id}">${s.title} (${s.status})</option>`;});
}}

async function transitionSheet(id,state){
if(!confirm(`Are you sure you want to transition this evaluation to "${state}"?`))return;
const r = await fetch('/api/evaluations.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'transition',sheet_id:id,new_state:state})});
const d = await r.json();
alert(d.message);loadEvalTable();
}

// Create evaluation
document.getElementById('createForm').addEventListener('submit',async(e)=>{
e.preventDefault();
const body = {action:'create',title:document.getElementById('createTitle').value,description:document.getElementById('createDesc').value,
academic_year:document.getElementById('createYear').value,semester:document.getElementById('createSemester').value,
course_id:parseInt(document.getElementById('createCourseId').value),instructor_id:parseInt(document.getElementById('createInstructorId').value),
department_id:<?= $user['department_id'] ?>,start_date:document.getElementById('createStart').value||null,end_date:document.getElementById('createEnd').value||null};
const r=await fetch('/api/evaluations.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
const d=await r.json();alert(d.message);
if(d.success){document.getElementById('createModal').style.display='none';e.target.reset();loadEvalTable();}
});

// Results viewer
document.getElementById('resultSheetSelect').addEventListener('change',async function(){
const id=this.value;if(!id){document.getElementById('resultView').style.display='none';return;}
document.getElementById('resultView').style.display='block';
// Load scores
const r=await fetch(`/api/analytics.php?action=instructor_averages&instructor_id=0&sheet_id=${id}`,{credentials:'same-origin',headers:{Accept:'application/json'}});
const d=await r.json();
// We need to query by sheet, use a different approach
const qr = await fetch(`/api/submissions.php?action=questions&sheet_id=${id}`,{credentials:'same-origin',headers:{Accept:'application/json'}});
const qd = await qr.json();
if(qd.success){
document.getElementById('scoreDistribution').innerHTML = qd.questions.map(q=>`
<div style="margin-bottom:12px;"><div style="font-size:14px;color:var(--text-secondary);margin-bottom:4px;">${q.question_text.substring(0,50)}...</div>
<div class="score-bar"><div class="score-fill score-high" style="width:75%;"></div></div></div>`).join('');}
// Comments
const cr = await fetch(`/api/analytics.php?action=sheet_comments&sheet_id=${id}`,{credentials:'same-origin',headers:{Accept:'application/json'}});
const cd = await cr.json();
if(cd.success){
document.getElementById('resultComments').innerHTML = cd.comments.length ? cd.comments.map(c=>`
<div style="padding:16px;margin-bottom:12px;background:var(--input-bg);border-radius:12px;">
<p style="color:var(--text-primary);font-size:14px;">"${c.comment_text}"</p>
<span style="color:var(--text-secondary);font-size:12px;">${new Date(c.submitted_at).toLocaleDateString()}</span></div>`).join('')
:'<p style="color:var(--text-muted);">No comments.</p>';}
});

loadDashboard();
</script></body></html>
