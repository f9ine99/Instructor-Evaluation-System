<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('instructor');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"/><script>try{localStorage.getItem('theme')==='light'&&document.documentElement.classList.add('light-mode')}catch(e){}</script><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Instructor Dashboard - HOPE Evaluation System</title>
<link rel="stylesheet" href="<?=$ab?>/css/variables.css"><link rel="stylesheet" href="<?=$ab?>/css/base.css">
<link rel="stylesheet" href="<?=$ab?>/css/layout.css"><link rel="stylesheet" href="<?=$ab?>/css/components.css">
<link rel="stylesheet" href="<?=$ab?>/css/dashboards.css"></head>
<body><div class="admin-layout" id="dashboardLayout">
<aside class="sidebar" id="dashboardSidebar" aria-label="Instructor navigation"><div class="sidebar-header"><div class="sidebar-logo" title="HOPE">HOPE</div><button type="button" class="sidebar-collapse-btn" id="sidebarCollapseBtn" aria-expanded="true" aria-controls="dashboardSidebar" title="Collapse sidebar"><svg class="sidebar-collapse-btn__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg></button></div>
<nav class="nav-links">
<a class="nav-item active" onclick="switchSection('overview')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg><span>Overview</span></a>
<a class="nav-item" onclick="switchSection('reports')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span>Detailed Reports</span></a>
<a class="nav-item" onclick="switchSection('feedback')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg><span>My Reflection</span></a>
</nav>
<div class="sidebar-footer">
<button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg><svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></button>
<a href="/api/auth.php" onclick="event.preventDefault();fetch('/api/auth.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>window.location.href='login.php')" class="nav-item" style="color:#ef4444;"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></a>
</div></aside>

<main class="main-content">
<div class="top-bar"><h1 class="page-title" id="pageTitle">Instructor Overview</h1>
<div style="display:flex;gap:16px;align-items:center;">
<span style="color:var(--text-secondary);font-size:14px;"><?= htmlspecialchars($user['full_name']) ?></span>
<div style="width:40px;height:40px;border-radius:50%;background:var(--accent-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;"><?= strtoupper(substr($user['full_name'],0,2)) ?></div>
</div></div>

<!-- OVERVIEW -->
<section id="overview" class="section-content active">
<div class="stats-grid" id="statsGrid"><p style="color:var(--text-secondary);">Loading stats...</p></div>
<div class="analytics-grid">
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Performance Breakdown</h3><div id="performanceBreakdown"><p style="color:var(--text-secondary);">Loading...</p></div></div>
<div class="analytics-card"><h3 style="color:var(--text-primary);margin-bottom:24px;">Student Comments</h3><div id="commentsSection" style="display:flex;flex-direction:column;gap:16px;"><p style="color:var(--text-secondary);">Loading...</p></div></div>
</div></section>

<!-- REPORTS -->
<section id="reports" class="section-content">
<h2 style="font-size:20px;color:var(--text-primary);margin-bottom:24px;">Course Performance</h2>
<div class="analytics-card"><div id="courseTable"><p style="color:var(--text-secondary);">Loading...</p></div></div>
</section>

<!-- REFLECTION -->
<section id="feedback" class="section-content">
<h2 style="font-size:20px;color:var(--text-primary);margin-bottom:16px;">Self-Reflection & Action Plan</h2>
<p style="color:var(--text-secondary);margin-bottom:32px;max-width:700px;">Based on the student feedback, provide your reflection and outline changes for next term.</p>
<div class="feedback-form"><form id="reflectionForm">
<div class="form-group"><label class="form-label">Evaluation Agreement</label>
<div style="display:flex;gap:24px;align-items:center;margin-bottom:8px;">
<label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:var(--text-primary);font-weight:500;"><input type="radio" name="agreement" value="agree" checked style="accent-color:var(--accent-primary);width:18px;height:18px;">I agree with this result</label>
<label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:var(--text-primary);font-weight:500;"><input type="radio" name="agreement" value="disagree" style="accent-color:var(--accent-primary);width:18px;height:18px;">I disagree with this result</label>
</div></div>
<div class="form-group"><label class="form-label">What went well this semester?</label><textarea class="form-textarea" id="wentWell" placeholder="Highlight successful teaching strategies..."></textarea></div>
<div class="form-group"><label class="form-label">Areas for Improvement</label><textarea class="form-textarea" id="areasImprovement" placeholder="Identify areas where student feedback suggests change..."></textarea></div>
<div class="form-group"><label class="form-label">Action Plan for Next Term</label><textarea class="form-textarea" id="actionPlan" placeholder="Specific steps to address improvements..."></textarea></div>
<div style="display:flex;justify-content:flex-end;"><button type="submit" class="btn-submit">Save Reflection</button></div>
</form></div></section>
</main></div>

<script>
function loadTheme(){const s=localStorage.getItem('theme')||'dark';const r=document.documentElement;r.classList.toggle('light-mode',s==='light');document.body.classList.remove('light-mode');document.querySelector('.sun-icon').style.display=s==='light'?'none':'block';document.querySelector('.moon-icon').style.display=s==='light'?'block':'none';}
function toggleTheme(){const l=document.documentElement.classList.toggle('light-mode');document.body.classList.remove('light-mode');localStorage.setItem('theme',l?'light':'dark');document.querySelector('.sun-icon').style.display=l?'none':'block';document.querySelector('.moon-icon').style.display=l?'block':'none';}
loadTheme(); document.getElementById('themeToggle').addEventListener('click',toggleTheme);

function switchSection(id){document.querySelectorAll('.nav-item').forEach(i=>{i.classList.remove('active');if(i.getAttribute('onclick')?.includes(id))i.classList.add('active');});
document.querySelectorAll('.section-content').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active');
const t={'overview':'Instructor Overview','reports':'Detailed Reports','feedback':'My Reflection'};document.getElementById('pageTitle').textContent=t[id];}

async function loadDashboard(){
try{
// Load averages
const avgRes = await fetch('/api/analytics.php?action=instructor_averages',{credentials:'same-origin',headers:{Accept:'application/json'}});
const avgData = await avgRes.json();
if(avgData.success && avgData.data){
const d = avgData.data;
document.getElementById('statsGrid').innerHTML = `
<div class="stat-card"><div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:28px;height:28px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
<div class="stat-info"><h3>Overall Rating</h3><div class="value">${d.overall_average}</div><div class="score-bar" style="margin-top:8px;width:100px;"><div class="score-fill ${d.overall_average>=4?'score-high':d.overall_average>=3?'score-med':'score-low'}" style="width:${d.overall_average*20}%;"></div></div></div></div>
<div class="stat-card"><div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:28px;height:28px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
<div class="stat-info"><h3>Total Responses</h3><div class="value">${d.total_responses}</div></div></div>`;

// Performance breakdown
if(d.breakdown.length){
document.getElementById('performanceBreakdown').innerHTML = d.breakdown.map(q=>`
<div style="margin-bottom:16px;"><div style="display:flex;justify-content:space-between;color:var(--text-secondary);font-size:14px;margin-bottom:4px;">
<span style="max-width:70%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${q.question_text.substring(0,60)}...</span><span>${parseFloat(q.avg_rating).toFixed(1)}/5.0</span></div>
<div class="score-bar"><div class="score-fill ${q.avg_rating>=4?'score-high':q.avg_rating>=3?'score-med':'score-low'}" style="width:${q.avg_rating*20}%;"></div></div></div>`).join('');
}}

// Load comments
const cRes = await fetch('/api/analytics.php?action=instructor_comments',{credentials:'same-origin',headers:{Accept:'application/json'}});
const cData = await cRes.json();
if(cData.success && cData.comments.length){
document.getElementById('commentsSection').innerHTML = cData.comments.map(c=>`
<div style="padding:16px;background:var(--input-bg);border-radius:12px;">
<p style="color:var(--text-primary);font-size:14px;margin-bottom:8px;">"${c.comment_text}"</p>
<span style="color:var(--text-secondary);font-size:12px;">${c.course_code} • ${new Date(c.submitted_at).toLocaleDateString()}</span></div>`).join('');
} else { document.getElementById('commentsSection').innerHTML = '<p style="color:var(--text-muted);text-align:center;">No comments yet.</p>'; }

// Load course performance table
const evRes = await fetch('/api/evaluations.php?action=list',{credentials:'same-origin',headers:{Accept:'application/json'}});
const evData = await evRes.json();
if(evData.success && evData.sheets.length){
document.getElementById('courseTable').innerHTML = `<table style="width:100%;border-collapse:collapse;"><thead><tr style="border-bottom:1px solid var(--card-border);">
<th style="text-align:left;padding:16px;color:var(--text-secondary);">Course</th>
<th style="text-align:left;padding:16px;color:var(--text-secondary);">Submissions</th>
<th style="text-align:left;padding:16px;color:var(--text-secondary);">Status</th></tr></thead><tbody>
${evData.sheets.map(s=>`<tr style="border-bottom:1px solid var(--card-border);">
<td style="padding:16px;color:var(--text-primary);">${s.course_code} - ${s.course_title}</td>
<td style="padding:16px;color:var(--text-primary);">${s.submission_count}</td>
<td style="padding:16px;"><span class="status-badge ${s.status==='open'?'status-active':'status-pending'}">${s.status}</span></td></tr>`).join('')}
</tbody></table>`;
}
}catch(e){console.error('Dashboard load error:',e);}
}

// Reflection form
document.getElementById('reflectionForm').addEventListener('submit',async(e)=>{
e.preventDefault();const btn=e.target.querySelector('.btn-submit');const orig=btn.textContent;
btn.textContent='Saved Successfully!';btn.style.background='#10b981';
setTimeout(()=>{btn.textContent=orig;btn.style.background='';},2000);
});

loadDashboard();
</script>
<script src="<?= htmlspecialchars($ab) ?>/js/dashboard-sidebar.js"></script>
</body></html>