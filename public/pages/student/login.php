<?php
require_once __DIR__ . '/../../../src/services/AuthService.php';
AuthService::initSession();

// Redirect if already logged in (bulk-import students must set a new password first)
if (AuthService::isLoggedIn() && AuthService::hasRole('student')) {
    $u = AuthService::getCurrentUser();
    $next = (!empty($u['must_change_password'])) ? 'first-login-password.php' : 'evaluate.php';
    header('Location: ' . $next);
    exit;
}
$assetBase = '../../assets';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <script>try { if (localStorage.getItem('theme') === 'light') document.documentElement.classList.add('light-mode'); } catch (e) {}</script>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Student Login | HOPE System</title>
    <link rel="stylesheet" href="<?= $assetBase ?>/css/variables.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/base.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/layout.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/components.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/forms.css">
    <style>
        body { overflow: hidden; }
        .login-card {
            border: 1px solid rgba(var(--accent-primary-rgb), 0.1);
        }
        .student-login-extra {
            margin-top: 22px;
            text-align: center;
            font-size: 14px;
            line-height: 1.5;
            color: var(--text-secondary);
        }
        .student-login-extra a {
            color: var(--accent-primary);
            font-weight: 600;
            text-decoration: none;
        }
        .student-login-extra a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div style="margin-bottom: 24px; display: flex; justify-content: center;">
                    <div style="width: 80px; height: 80px; background: var(--accent-gradient); border-radius: 24px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 10px 20px -5px rgba(var(--accent-primary-rgb), 0.5);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                            <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5" />
                        </svg>
                    </div>
                </div>
                <h1 class="login-title">Student Portal</h1>
                <p style="color: var(--text-secondary); font-size: 16px;">Sign in to evaluate your instructors</p>
            </div>

            <div id="errorMessage" style="display:none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; text-align: center;"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Student ID</label>
                    <input type="text" id="username" name="username" class="form-input" placeholder="e.g. STU12345" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="login-btn" id="submitBtn">
                    <span class="spinner"></span>
                    <span class="btn-text">Sign In</span>
                </button>
            </form>
            <p class="student-login-extra">
                After you sign in, use <strong>Password</strong> in the top bar on evaluations.
                <br>
                Already signed in on this device? <a href="change-password.php">Change password</a>.
            </p>
        </div>
    </div>

    <script>
        // Login Logic
        const loginForm = document.getElementById('loginForm');
        const loginCard = document.querySelector('.login-card');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');

        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.innerHTML = isPassword 
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        });

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            errorMessage.style.display = 'none';
            loginCard.classList.remove('error');

            const formData = new FormData(loginForm);
            formData.append('action', 'login');
            formData.append('role', 'student');

            try {
                await new Promise(resolve => setTimeout(resolve, 800));

                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(Object.fromEntries(formData))
                });

                const result = await response.json();
                if (!response.ok) {
                    const msg = [result.message || 'Login failed.', result.error].filter(Boolean).join(' — ');
                    throw new Error(msg);
                }

                if (result.success) {
                    submitBtn.querySelector('.btn-text').textContent = 'Success!';
                    submitBtn.style.background = '#10b981';
                    setTimeout(() => {
                        window.location.href = result.redirect || 'evaluate.php';
                    }, 500);
                } else {
                    const msg = [result.message || 'Invalid credentials', result.error].filter(Boolean).join(' — ');
                    throw new Error(msg);
                }
            } catch (err) {
                errorMessage.textContent = err.message || 'Connection failed. Check your network.';
                errorMessage.style.display = 'block';
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>