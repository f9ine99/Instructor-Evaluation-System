<?php
require_once __DIR__ . '/../../../src/services/AuthService.php';
AuthService::initSession();

// Redirect if already logged in
if (AuthService::isLoggedIn() && AuthService::hasRole('admin')) {
    header('Location: dashboard.php'); exit;
}
$assetBase = '../../assets';
$role = 'admin';
$title = 'Admin Portal';
$icon = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $title ?> | HOPE System</title>
    <link rel="stylesheet" href="<?= $assetBase ?>/css/variables.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/base.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/layout.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/components.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/forms.css">
    <style>
        body { overflow: hidden; }
        .login-card { border: 1px solid rgba(var(--accent-primary-rgb), 0.1); }
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
                            <?= $icon ?>
                        </svg>
                    </div>
                </div>
                <h1 class="login-title"><?= $title ?></h1>
                <p style="color: var(--text-secondary); font-size: 16px;">System Infrastructure Control</p>
            </div>

            <div id="errorMsg" style="display:none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; text-align: center;"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Admin Username</label>
                    <input type="text" id="username" class="form-input" placeholder="Enter admin username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <button type="submit" class="login-btn" id="loginBtn">
                    <span>Sign In</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const errEl = document.getElementById('errorMsg');
            
            errEl.style.display = 'none';
            btn.innerHTML = '<span>Processing...</span>';
            btn.disabled = true;

            try {
                const res = await fetch('/api/auth.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        action: 'login',
                        username: document.getElementById('username').value.trim(),
                        password: document.getElementById('password').value,
                        role: '<?= $role ?>'
                    })
                });
                
                const data = await res.json();
                if (data.success) {
                    btn.innerHTML = '<span>Success! Redirecting...</span>';
                    setTimeout(() => window.location.href = data.redirect || 'dashboard.php', 500);
                } else {
                    errEl.textContent = [data.message || 'Invalid credentials.', data.error].filter(Boolean).join(' — ');
                    errEl.style.display = 'block';
                    btn.innerHTML = '<span>Sign In</span>';
                    btn.disabled = false;
                }
            } catch (err) {
                errEl.textContent = 'Connection failed.';
                errEl.style.display = 'block';
                btn.innerHTML = '<span>Sign In</span>';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>