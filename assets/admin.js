// Theme Management (Shared Logic)
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

// Initialize theme
loadTheme();
document.getElementById('themeToggle').addEventListener('click', toggleTheme);

// Navigation Logic
function switchSection(sectionId) {
    // Update Sidebar Active State
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('onclick')?.includes(sectionId)) {
            item.classList.add('active');
        }
    });

    // Show Content
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionId).classList.add('active');

    // Update Page Title
    const titles = {
        'overview': 'Dashboard Overview',
        'manage': 'Manage Data',
        'analytics': 'Analytics & Reports'
    };
    document.getElementById('pageTitle').textContent = titles[sectionId];
}

// Mock Data
const mockData = {
    faculty: [
        { name: 'Dr. Sarah Johnson', dept: 'Computer Science', designation: 'Professor', status: 'Active' },
        { name: 'Prof. Michael Chen', dept: 'English Literature', designation: 'Associate Professor', status: 'Active' },
        { name: 'Dr. Emily Davis', dept: 'Architecture', designation: 'Assistant Professor', status: 'On Leave' }
    ],
    courses: [
        { name: 'CS101 - Intro to Programming', instructor: 'Dr. Sarah Johnson', dept: 'Computer Science', program: 'BSc CS', year: '1st Year', semester: 'I', academicYear: '2024', status: 'Active' },
        { name: 'ENG202 - Advanced Composition', instructor: 'Prof. Michael Chen', dept: 'English Literature', program: 'BA English', year: '2nd Year', semester: 'II', academicYear: '2024', status: 'Active' },
        { name: 'MATH301 - Linear Algebra', instructor: 'Dr. Emily Davis', dept: 'Mathematics', program: 'BSc Math', year: '3rd Year', semester: 'I', academicYear: '2024', status: 'Active' },
        { name: 'Web Programming', instructor: 'Mr X', dept: 'Computer Science', program: 'BSc CS', year: '3rd Year', semester: 'I', academicYear: '2025', status: 'Active' }
    ],
    departments: [
        { name: 'Computer Science', head: 'Dr. Alan Turing', facultyCount: 12, status: 'Active' },
        { name: 'English Literature', head: 'Dr. Jane Austen', facultyCount: 8, status: 'Active' }
    ],
    programs: [
        { name: 'B.Sc. Computer Science', duration: '4 Years', dept: 'Computer Science', status: 'Active' },
        { name: 'B.A. English', duration: '3 Years', dept: 'English Literature', status: 'Active' }
    ],
    users: [
        { username: 'admin', name: 'System Admin', role: 'Admin', status: 'Active' },
        { username: '2021001', name: 'John Doe', role: 'Student', status: 'Active' },
        { username: 'sarah.j', name: 'Dr. Sarah Johnson', role: 'Instructor', status: 'Active' }
    ]
};

let currentTab = 'faculty';

// Tab Logic for Manage Section
function switchTab(btn, tabId) {
    currentTab = tabId;

    // Update Tab Buttons
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    renderTable();
}

function renderTable() {
    const tableHead = document.querySelector('#manageTable thead');
    const tableBody = document.querySelector('#manageTable tbody');
    const data = mockData[currentTab];

    // Clear existing
    tableHead.innerHTML = '';
    tableBody.innerHTML = '';

    // Define columns based on tab
    let columns = [];
    if (currentTab === 'faculty') {
        columns = ['Name', 'Department', 'Designation', 'Status', 'Actions'];
    } else if (currentTab === 'courses') {
        columns = ['Course Title', 'Faculty Member', 'Department', 'Program', 'Year', 'Semester', 'Academic Year', 'Status', 'Actions'];
    } else if (currentTab === 'departments') {
        columns = ['Department Name', 'Head of Dept', 'Faculty Count', 'Status', 'Actions'];
    } else if (currentTab === 'programs') {
        columns = ['Program Name', 'Duration', 'Department', 'Status', 'Actions'];
    } else if (currentTab === 'users') {
        columns = ['Username', 'Full Name', 'Role', 'Status', 'Actions'];
    }

    // Render Headers
    const headerRow = document.createElement('tr');
    columns.forEach(col => {
        const th = document.createElement('th');
        th.textContent = col;
        headerRow.appendChild(th);
    });
    tableHead.appendChild(headerRow);

    // Render Rows
    data.forEach(item => {
        const tr = document.createElement('tr');

        if (currentTab === 'faculty') {
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.dept}</td>
                <td>${item.designation}</td>
                <td><span class="status-badge ${item.status === 'Active' ? 'status-active' : 'status-pending'}">${item.status}</span></td>
                <td>${getActionButtons()}</td>
            `;
        } else if (currentTab === 'courses') {
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.instructor}</td>
                <td>${item.dept}</td>
                <td>${item.program}</td>
                <td>${item.year}</td>
                <td>${item.semester}</td>
                <td>${item.academicYear}</td>
                <td><span class="status-badge ${item.status === 'Active' ? 'status-active' : 'status-pending'}">${item.status}</span></td>
                <td>${getActionButtons()}</td>
            `;
        } else if (currentTab === 'departments') {
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.head}</td>
                <td>${item.facultyCount}</td>
                <td><span class="status-badge ${item.status === 'Active' ? 'status-active' : 'status-pending'}">${item.status}</span></td>
                <td>${getActionButtons()}</td>
            `;
        } else if (currentTab === 'programs') {
            tr.innerHTML = `
                <td>${item.name}</td>
                <td>${item.duration}</td>
                <td>${item.dept}</td>
                <td><span class="status-badge ${item.status === 'Active' ? 'status-active' : 'status-pending'}">${item.status}</span></td>
                <td>${getActionButtons()}</td>
            `;
        } else if (currentTab === 'users') {
            tr.innerHTML = `
                <td>${item.username}</td>
                <td>${item.name}</td>
                <td>${item.role}</td>
                <td><span class="status-badge ${item.status === 'Active' ? 'status-active' : 'status-pending'}">${item.status}</span></td>
                <td>${getActionButtons(true)}</td>
            `;
        }

        tableBody.appendChild(tr);
    });
}

function getActionButtons(isUser = false) {
    return `
        <button style="background: none; border: none; color: var(--text-secondary); cursor: pointer; margin-right: 8px;" title="Edit">✏️</button>
        ${isUser ? `<button onclick="resetPassword()" style="background: none; border: none; color: var(--accent-primary); cursor: pointer; margin-right: 8px;" title="Reset Password">🔑</button>` : ''}
        <button style="background: none; border: none; color: #ef4444; cursor: pointer;" title="Delete">🗑️</button>
    `;
}

function resetPassword() {
    alert('Password reset link has been generated and sent to the user.');
}

// Modal Logic
function openModal() {
    const modal = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('addForm');

    // Reset form
    form.reset();

    // Customize based on tab
    if (currentTab === 'courses') {
        title.textContent = 'Add New Course';
        document.getElementById('courseFields').style.display = 'block';
        document.getElementById('userFields').style.display = 'none';
    } else if (currentTab === 'users') {
        title.textContent = 'Add New User';
        document.getElementById('courseFields').style.display = 'none';
        document.getElementById('userFields').style.display = 'block';
    } else {
        title.textContent = `Add New ${currentTab.slice(0, -1)}`;
        document.getElementById('courseFields').style.display = 'none';
        document.getElementById('userFields').style.display = 'none';
    }

    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalOverlay').style.display = 'none';
}

function handleFormSubmit(e) {
    e.preventDefault();

    if (currentTab === 'courses') {
        const courseName = document.getElementById('courseNameInput').value;
        const facultyMember = document.getElementById('facultyMemberInput').value;
        const department = document.getElementById('departmentInput').value;
        const program = document.getElementById('programInput').value;
        const year = document.getElementById('yearInput').value;
        const semester = document.getElementById('semesterInput').value;
        const academicYear = document.getElementById('academicYearInput').value;

        mockData.courses.push({
            name: courseName,
            instructor: facultyMember,
            dept: department,
            program: program,
            year: year,
            semester: semester,
            academicYear: academicYear,
            status: 'Active'
        });

        renderTable();
        closeModal();
    } else if (currentTab === 'users') {
        const username = document.getElementById('userInput').value;
        const name = document.getElementById('userNameInput').value;
        const role = document.getElementById('userRoleInput').value;

        mockData.users.push({
            username: username,
            name: name,
            role: role,
            status: 'Active'
        });

        renderTable();
        closeModal();
    } else {
        alert('This feature is only fully implemented for Courses and Users in this prototype.');
        closeModal();
    }
}

// Initial Render
document.addEventListener('DOMContentLoaded', () => {
    // Render initial table (Faculty)
    renderTable();
});
// Semester Selection Logic
document.getElementById('semesterSelect').addEventListener('change', (e) => {
    const semester = e.target.value;
    console.log(`Switching to semester: ${semester}`);
    // In a real app, this would trigger a data fetch for the selected semester
    const mainContent = document.querySelector('.main-content');
    mainContent.style.opacity = '0.5';
    setTimeout(() => {
        mainContent.style.opacity = '1';
        renderTable(); // Re-render table to simulate data refresh
    }, 300);
});
