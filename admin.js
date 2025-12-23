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
        { name: 'CS101 - Intro to Programming', instructor: 'Dr. Sarah Johnson', status: 'Active' },
        { name: 'ENG202 - Advanced Composition', instructor: 'Prof. Michael Chen', status: 'Active' },
        { name: 'MATH301 - Linear Algebra', instructor: 'Dr. Emily Davis', status: 'Active' }
    ],
    departments: [
        { name: 'Computer Science', head: 'Dr. Alan Turing', facultyCount: 12, status: 'Active' },
        { name: 'English Literature', head: 'Dr. Jane Austen', facultyCount: 8, status: 'Active' }
    ],
    programs: [
        { name: 'B.Sc. Computer Science', duration: '4 Years', dept: 'Computer Science', status: 'Active' },
        { name: 'B.A. English', duration: '3 Years', dept: 'English Literature', status: 'Active' }
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
        columns = ['Course Name', 'Instructor', 'Status', 'Actions'];
    } else if (currentTab === 'departments') {
        columns = ['Department Name', 'Head of Dept', 'Faculty Count', 'Status', 'Actions'];
    } else if (currentTab === 'programs') {
        columns = ['Program Name', 'Duration', 'Department', 'Status', 'Actions'];
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
        }

        tableBody.appendChild(tr);
    });
}

function getActionButtons() {
    return `
        <button style="background: none; border: none; color: var(--text-secondary); cursor: pointer; margin-right: 8px;">✏️</button>
        <button style="background: none; border: none; color: #ef4444; cursor: pointer;">🗑️</button>
    `;
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
        document.getElementById('courseNameInput').parentElement.style.display = 'block';
        document.getElementById('instructorNameInput').parentElement.style.display = 'block';
    } else {
        // For prototype, we only implemented Course adding fully as requested
        title.textContent = `Add New ${currentTab.slice(0, -1)}`; // Remove 's'
        // In a real app, we would dynamically show/hide inputs here
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
        const instructorName = document.getElementById('instructorNameInput').value;

        mockData.courses.push({
            name: courseName,
            instructor: instructorName,
            status: 'Active'
        });

        renderTable();
        closeModal();

        // Show success message (optional)
        // alert('Course added successfully!');
    } else {
        alert('This feature is only fully implemented for Courses in this prototype.');
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
