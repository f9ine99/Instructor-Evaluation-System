// Theme Management
function loadTheme() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'light') {
        document.body.classList.add('light-mode');
        updateThemeIcon(true);
    }
}

function toggleTheme() {
    const isLight = document.body.classList.toggle('light-mode');
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

// Navigation Logic
function switchSection(sectionId) {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('onclick')?.includes(sectionId)) {
            item.classList.add('active');
        }
    });

    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionId).classList.add('active');

    const titles = {
        'overview': 'HR Overview',
        'reports': 'Reports & Trends',
        'decisions': 'Administrative Decisions'
    };
    document.getElementById('pageTitle').textContent = titles[sectionId];
}

// Mock Data for HR
let instructors = [
    { id: 1, name: 'Dr. Sarah Johnson', dept: 'Computer Science', avgRating: 4.8, trend: '+0.2', status: 'Excellent' },
    { id: 2, name: 'Prof. Michael Chen', dept: 'English Literature', avgRating: 4.5, trend: '+0.1', status: 'Good' },
    { id: 3, name: 'Dr. Emily Davis', dept: 'Architecture', avgRating: 3.9, trend: '-0.3', status: 'Average' },
    { id: 4, name: 'Mr. Robert Wilson', dept: 'Mathematics', avgRating: 4.2, trend: '0.0', status: 'Good' },
    { id: 5, name: 'Dr. Lisa Brown', dept: 'Physics', avgRating: 3.5, trend: '-0.5', status: 'Needs Review' }
];

let recentActivity = [
    { type: 'Evaluation Submitted', user: 'Student #2021001', instructor: 'Dr. Sarah Johnson', time: '2 mins ago' },
    { type: 'Report Generated', user: 'System', instructor: 'Full Faculty (CS)', time: '1 hour ago' },
    { type: 'Evaluation Submitted', user: 'Student #2021045', instructor: 'Prof. Michael Chen', time: '3 hours ago' }
];

// Initialize Dashboard
function init() {
    loadTheme();
    document.getElementById('themeToggle').addEventListener('click', toggleTheme);

    renderOverview();
    renderReports();
    populateInstructorSelect();
    renderDecisions();

    // Decision Form Handler
    document.getElementById('decisionForm').addEventListener('submit', handleDecisionSubmit);
}

function renderOverview() {
    const activityContainer = document.getElementById('recentActivity');
    activityContainer.innerHTML = recentActivity.map(act => `
        <div style="display: flex; justify-content: space-between; padding: 16px; border-bottom: 1px solid var(--card-border);">
            <div>
                <div style="font-weight: 600; color: var(--text-primary);">${act.type}</div>
                <div style="font-size: 13px; color: var(--text-secondary);">${act.user} for ${act.instructor}</div>
            </div>
            <div style="font-size: 12px; color: var(--text-secondary);">${act.time}</div>
        </div>
    `).join('');
}

function getScoreClass(score) {
    if (score >= 4.5) return 'score-high';
    if (score >= 4.0) return 'score-med';
    return 'score-low';
}

function renderReports() {
    const tableBody = document.getElementById('reportsTableBody');
    tableBody.innerHTML = instructors.map(ins => `
        <tr>
            <td>
                <div style="font-weight: 600;">${ins.name}</div>
            </td>
            <td>${ins.dept}</td>
            <td>
                <span class="score-badge ${getScoreClass(ins.avgRating)}">${ins.avgRating} / 5.0</span>
            </td>
            <td style="color: ${ins.trend.startsWith('+') ? '#10b981' : (ins.trend.startsWith('-') ? '#ef4444' : 'var(--text-secondary)')}; font-weight: 700;">
                ${ins.trend}
            </td>
            <td>${ins.status}</td>
        </tr>
    `).join('');
}

function populateInstructorSelect() {
    const select = document.getElementById('instructorSelect');
    instructors.forEach(ins => {
        const option = document.createElement('option');
        option.value = ins.name;
        option.textContent = ins.name;
        select.appendChild(option);
    });
}

function handleDecisionSubmit(e) {
    e.preventDefault();
    
    const instructor = document.getElementById('instructorSelect').value;
    const type = document.getElementById('decisionType').value;
    const notes = document.getElementById('decisionNotes').value;
    
    const decision = {
        instructor,
        type,
        notes,
        date: new Date().toLocaleDateString(),
        hrStaff: 'HR Officer A'
    };
    
    // Save to localStorage
    const decisions = JSON.parse(localStorage.getItem('admin_decisions') || '[]');
    decisions.unshift(decision);
    localStorage.setItem('admin_decisions', JSON.stringify(decisions));
    
    // Clear form
    e.target.reset();
    
    // Refresh table
    renderDecisions();
    alert('Administrative decision recorded successfully!');
}

function renderDecisions() {
    const decisions = JSON.parse(localStorage.getItem('admin_decisions') || '[]');
    const tableBody = document.getElementById('decisionsTableBody');
    
    if (decisions.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-secondary);">No decisions recorded yet.</td></tr>';
        return;
    }
    
    tableBody.innerHTML = decisions.map(d => `
        <tr>
            <td style="font-weight: 600;">${d.instructor}</td>
            <td><span class="score-badge score-med" style="background: rgba(var(--accent-primary-rgb), 0.1); color: var(--accent-primary);">${d.type}</span></td>
            <td>${d.date}</td>
            <td>${d.hrStaff}</td>
        </tr>
    `).join('');
}

// Run init
init();
