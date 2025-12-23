// ============================================
// INSTRUCTOR EVALUATION SYSTEM - Student Interface
// Anonymous Evaluation with Theme Support
// ============================================

const QUESTIONS = [
    "The course instructor gives course outlines at the beginning of the semester.",
    "The course instructor teaches effectively by preparation using useful instructional materials and available technology.",
    "The course instructor is knowledgeable enough on his course.",
    "The course instructor arrives on time.",
    "The course instructor leaves on time.",
    "The course instructor manages and maintains appropriate discipline in the class.",
    "The course instructor covered all chapters in the course outline.",
    "The course instructor gives immediate feedback on students' progress and performances.",
    "The course instructor encourages student's interaction/participation.",
    "Assessment covers fairly all contents and learning experiences.",
    "The course instructor uses his/her office/consultation hours to render academic support and advice to the students.",
    "The course instructor returns the graded script within reasonable time.",
    "The course instructor serves as a role model through high moral standards in class and on campus, as well as encouraging high professional standards."
];

// DOM Elements
const qContainer = document.getElementById('questions');
const form = document.getElementById('evalForm');
const resetBtn = document.getElementById('resetBtn');
const themeToggle = document.getElementById('themeToggle');

// ============================================
// THEME MANAGEMENT
// ============================================

// Load saved theme or default to dark mode
function loadTheme() {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    if (savedTheme === 'light') {
        document.body.classList.add('light-mode');
        updateThemeIcon(true);
    }
}

// Toggle theme
function toggleTheme() {
    const isLight = document.body.classList.toggle('light-mode');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    updateThemeIcon(isLight);
}

// Update theme toggle icon
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

// ============================================
// QUESTION RENDERING
// ============================================

function createQuestion(idx, text) {
    const wrapper = document.createElement('div');
    wrapper.className = 'question';

    const qText = document.createElement('div');
    qText.className = 'question__text';
    qText.innerHTML = `<span class="question__number">${idx + 1}.</span> ${text}`;

    const ratingGroup = document.createElement('div');
    ratingGroup.className = 'rating-group';

    // Create rating buttons (1-5)
    for (let i = 5; i >= 1; i--) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'rating-btn';
        btn.dataset.value = i;
        btn.dataset.q = idx;
        btn.textContent = i;

        btn.addEventListener('click', () => {
            // Remove active class from all siblings
            const siblings = ratingGroup.querySelectorAll('.rating-btn');
            siblings.forEach(s => s.classList.remove('active'));

            // Add active class to clicked button
            btn.classList.add('active');

            // Store value in hidden input
            const hidden = wrapper.querySelector('input[type=hidden]');
            hidden.value = i;
        });

        ratingGroup.appendChild(btn);
    }

    // Hidden input to store rating value
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = `q_${idx}`;
    hidden.required = true;

    wrapper.appendChild(qText);
    wrapper.appendChild(ratingGroup);
    wrapper.appendChild(hidden);

    return wrapper;
}

// Inject all questions
QUESTIONS.forEach((q, i) => qContainer.appendChild(createQuestion(i, q)));

// ============================================
// FORM HANDLING
// ============================================

form.addEventListener('submit', (e) => {
    e.preventDefault();

    // Validate all questions are answered
    const hiddenInputs = [...form.querySelectorAll('input[type=hidden]')];
    for (const h of hiddenInputs) {
        if (!h.value) {
            alert('⚠️ Please answer all evaluation questions before submitting.');
            h.closest('.question').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
    }

    // Get course information (read-only from page)
    const courseData = {
        faculty: document.getElementById('faculty').textContent,
        dept: document.getElementById('dept').textContent,
        semester: document.getElementById('semester').textContent,
        year: document.getElementById('year').textContent,
        course: document.getElementById('course').textContent,
        program: document.getElementById('program').textContent
    };

    // Build evaluation object
    const evaluation = {
        ...courseData,
        comments: document.getElementById('comments').value || '',
        scores: [],
        date: new Date().toLocaleString(),
        anonymous: true // indicator that student name was not collected
    };

    // Collect ratings (no calculations)
    for (let i = 0; i < QUESTIONS.length; i++) {
        const value = parseInt(form.querySelector(`input[name="q_${i}"]`).value, 10);
        evaluation.scores.push(value);
    }

    // Save to localStorage (simulating backend submission)
    saveEvaluation(evaluation);

    // Show success message
    showSuccessMessage(evaluation);

    // Reset form
    form.reset();
    form.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('active'));
});

// Reset button handler
resetBtn.addEventListener('click', () => {
    if (confirm('Are you sure you want to reset the form?')) {
        form.reset();
        form.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('active'));
    }
});

// ============================================
// DATA PERSISTENCE
// ============================================

function saveEvaluation(evaluation) {
    const saved = localStorage.getItem('evaluations');
    const evaluations = saved ? JSON.parse(saved) : [];
    evaluations.push(evaluation);
    localStorage.setItem('evaluations', JSON.stringify(evaluations));
}

function showSuccessMessage(evaluation) {
    const message = `✅ Thank you for submitting your evaluation!

Your feedback has been recorded successfully.

Course: ${evaluation.course}
Instructor: ${evaluation.faculty}`;

    alert(message);
}

// ============================================
// INITIALIZATION
// ============================================

// Load theme on page load
loadTheme();

// Add theme toggle event listener
themeToggle.addEventListener('click', toggleTheme);

// ============================================
// LOGOUT HANDLER
// ============================================

const logoutBtn = document.getElementById('logoutBtn');

if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        if (confirm('Are you sure you want to logout?')) {
            // Clear session
            sessionStorage.removeItem('isLoggedIn');
            sessionStorage.removeItem('studentId');

            // Redirect to login page
            window.location.href = 'login.html';
        }
    });
}

// ============================================
// INITIALIZATION
// ============================================

// Load theme on page load
loadTheme();

// Optional: Log to console for debugging
console.log('Student Evaluation System initialized');
console.log('Theme:', localStorage.getItem('theme') || 'dark');
