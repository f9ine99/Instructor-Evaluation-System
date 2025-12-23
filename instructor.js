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
        'overview': 'Instructor Overview',
        'reports': 'Detailed Reports',
        'feedback': 'My Reflection'
    };
    document.getElementById('pageTitle').textContent = titles[sectionId];
}

// Reflection Form Logic
function handleReflectionSubmit(e) {
    e.preventDefault();

    // In a real app, we would collect the data and send it to a server
    // const formData = new FormData(e.target);

    // For prototype, show success message
    const btn = e.target.querySelector('.btn-submit');
    const originalText = btn.textContent;

    btn.textContent = 'Saved Successfully!';
    btn.style.background = '#10b981';

    setTimeout(() => {
        btn.textContent = originalText;
        btn.style.background = '';
        e.target.reset();
    }, 2000);

    alert('Your reflection has been saved successfully.');
}
// Semester Selection Logic
document.getElementById('semesterSelect').addEventListener('change', (e) => {
    const semester = e.target.value;
    console.log(`Switching to semester: ${semester}`);
    // In a real app, this would trigger a data fetch for the selected semester
    // For prototype, we'll just show a brief loading state or alert
    const mainContent = document.querySelector('.main-content');
    mainContent.style.opacity = '0.5';
    setTimeout(() => {
        mainContent.style.opacity = '1';
    }, 300);
});
