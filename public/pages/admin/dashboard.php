<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('admin');
$user = AuthService::getCurrentUser();
$ab = '../../assets';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"/><script>try{localStorage.getItem('theme')==='light'&&document.documentElement.classList.add('light-mode')}catch(e){}</script><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>System Admin - HOPE Evaluation System</title>
<link rel="stylesheet" href="<?=$ab?>/css/variables.css"><link rel="stylesheet" href="<?=$ab?>/css/base.css">
<link rel="stylesheet" href="<?=$ab?>/css/layout.css"><link rel="stylesheet" href="<?=$ab?>/css/components.css">
<link rel="stylesheet" href="<?=$ab?>/css/dashboards.css"></head>
<body><div class="admin-layout">
<aside class="sidebar"><div class="sidebar-header"><div class="sidebar-logo">HOPE</div></div>
<nav class="nav-links">
<a class="nav-item active" onclick="switchSection('overview')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg><span>Overview</span></a>
<a class="nav-item" onclick="switchSection('manage')"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg><span>Manage Data</span></a>
</nav>
<div class="sidebar-footer">
<button class="theme-toggle" id="themeToggle" aria-label="Toggle theme"><svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg><svg class="moon-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></button>
<a href="#" onclick="event.preventDefault();fetch('/api/auth.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'logout'})}).then(()=>window.location.href='login.php')" class="nav-item" style="color:#ef4444;"><svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg></a>
</div></aside>

<main class="main-content">
<div class="top-bar"><h1 class="page-title" id="pageTitle">System Overview</h1>
<div style="display:flex;gap:16px;align-items:center;">
<span style="color:var(--text-secondary);font-size:14px;"><?= htmlspecialchars($user['full_name']) ?></span>
<div style="width:40px;height:40px;border-radius:50%;background:var(--accent-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;">SYS</div>
</div></div>

<section id="overview" class="section-content active">
<div class="stats-grid" id="statsGrid"><p style="color:var(--text-secondary);">Loading...</p></div>
</section>

<section id="manage" class="section-content">
<div class="tab-group" style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
<button class="tab-btn active" onclick="switchTab('users')">Users</button>
<button class="tab-btn" onclick="switchTab('courses')">Courses</button>
<button class="tab-btn" onclick="switchTab('departments')">Departments</button>
</div>
<div class="data-table-container"><table class="data-table"><thead id="tableHead"></thead><tbody id="tableBody"><tr><td colspan="5" style="text-align:center;">Loading...</td></tr></tbody></table></div>
</section>
</main></div>

<script>
function loadTheme(){const s=localStorage.getItem('theme')||'dark';const r=document.documentElement;r.classList.toggle('light-mode',s==='light');document.body.classList.remove('light-mode');document.querySelector('.sun-icon').style.display=s==='light'?'none':'block';document.querySelector('.moon-icon').style.display=s==='light'?'block':'none';}
function toggleTheme(){const l=document.documentElement.classList.toggle('light-mode');document.body.classList.remove('light-mode');localStorage.setItem('theme',l?'light':'dark');document.querySelector('.sun-icon').style.display=l?'none':'block';document.querySelector('.moon-icon').style.display=l?'block':'none';}
loadTheme(); document.getElementById('themeToggle').addEventListener('click',toggleTheme);

function switchSection(id){document.querySelectorAll('.nav-item').forEach(i=>{i.classList.remove('active');if(i.getAttribute('onclick')?.includes(id))i.classList.add('active');});
document.querySelectorAll('.section-content').forEach(s=>s.classList.remove('active'));document.getElementById(id).classList.add('active');
const t={'overview':'System Overview','manage':'Manage Data'};document.getElementById('pageTitle').textContent=t[id];}

let currentTab='users';
function switchTab(tab){currentTab=tab;document.querySelectorAll('.tab-btn').forEach(b=>{b.classList.remove('active');if(b.textContent.toLowerCase().includes(tab.slice(0,-1)))b.classList.add('active');});loadTable();}

async function loadDashboard(){
const err=(m)=>{document.getElementById('statsGrid').innerHTML='<p style="color:var(--error);padding:12px;">'+m+'</p>';};
try{const r=await fetch('/api/analytics.php?action=system_stats',{credentials:'same-origin',headers:{Accept:'application/json'}});let d;try{d=await r.json();}catch(x){err('Invalid response from server.');loadTable();return;}
if(r.status===401){err('Session expired. Refresh and sign in again.');loadTable();return;}
if(!r.ok||!d.success){err(d.message||('HTTP '+r.status));loadTable();return;}
const s=d.stats;
document.getElementById('statsGrid').innerHTML=`
<div class="stat-card"><div class="stat-info"><h3>Total Users</h3><div class="value">${s.total_instructors+s.total_students}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Total Departments</h3><div class="value">${s.total_departments}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Active Evaluations</h3><div class="value">${s.open_evaluations}</div></div></div>
<div class="stat-card"><div class="stat-info"><h3>Total Submissions</h3><div class="value">${s.total_submissions}</div></div></div>`;
}catch(e){err('Could not load dashboard.');}
loadTable();
}

async function loadTable(){
const body=document.getElementById('tableBody');const head=document.getElementById('tableHead');
try{const r=await fetch(`/api/admin.php?action=list&entity=${currentTab}`,{credentials:'same-origin',headers:{Accept:'application/json'}});let d;try{d=await r.json();}catch(x){body.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--error);">Invalid response</td></tr>';return;}
if(r.status===401){body.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--error);">Session expired</td></tr>';return;}
if(!r.ok||!d.success){body.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--error);">'+(d.message||('HTTP '+r.status))+'</td></tr>';return;}
const data=Array.isArray(d.data)?d.data:[];
if(currentTab==='users'){
head.innerHTML='<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th><th>Status</th></tr>';
body.innerHTML=data.map(u=>`<tr><td>${u.id}</td><td>${u.username}</td><td>${u.full_name}</td><td><span class="status-badge">${u.role}</span></td><td><span class="status-badge ${u.status==='active'?'status-active':'status-pending'}">${u.status}</span></td></tr>`).join('')||'<tr><td colspan="5">No rows</td></tr>';
}else if(currentTab==='courses'){
head.innerHTML='<tr><th>Code</th><th>Title</th><th>Dept</th><th>Instructor</th><th>Semester</th></tr>';
body.innerHTML=data.map(c=>`<tr><td>${c.code}</td><td>${c.title}</td><td>${c.department_name}</td><td>${c.instructor_name}</td><td>${c.semester} ${c.academic_year}</td></tr>`).join('')||'<tr><td colspan="5">No rows</td></tr>';
}else if(currentTab==='departments'){
head.innerHTML='<tr><th>ID</th><th>Name</th><th>Head</th><th>Faculty</th><th>Status</th></tr>';
body.innerHTML=data.map(d=>`<tr><td>${d.id}</td><td>${d.name}</td><td>${d.head_name||'-'}</td><td>${d.faculty_count}</td><td><span class="status-badge status-active">${d.status}</span></td></tr>`).join('')||'<tr><td colspan="5">No rows</td></tr>';
}}catch(e){body.innerHTML='<tr><td colspan="5" style="text-align:center;color:var(--error);">Could not load table</td></tr>';}
}
loadDashboard();
</script></body></html>