<?php
require_once __DIR__ . '/../../../src/middleware/auth.php';
requireAuth('student');

AuthService::getSanitizedUserHydratedFromDb();
$user = AuthService::getCurrentUser();
if ($user && !empty($user['must_change_password'])) {
    header('Location: first-login-password.php');
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
    <title>Change password | Student Portal</title>
    <link rel="stylesheet" href="<?= $assetBase ?>/css/variables.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/base.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/layout.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/components.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/forms.css">
    <style>
        body { overflow: hidden; }
        .login-card { border: 1px solid rgba(var(--accent-primary-rgb), 0.1); }
        .pwd-back-row { margin-top: 22px; text-align: center; }
        .pwd-back-row a {
            font-size: 14px;
            color: var(--text-secondary);
            text-decoration: none;
        }
        .pwd-back-row a:hover { color: var(--accent-primary); text-decoration: underline; }
    </style>
</head>
<body>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">Change password</h1>
                <p style="color: var(--text-secondary); font-size: 15px; line-height: 1.45;">Enter your current password once, then pick a strong new password. You stay signed in after saving.</p>
            </div>

            <div id="changePwdError" style="display:none; background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; text-align: center;"></div>

            <form id="changePwdForm">
                <div class="form-group">
                    <label class="form-label">Current password</label>
                    <input type="password" id="currentPassword" class="form-input" required maxlength="128" autocomplete="current-password" placeholder="Current password">
                </div>
                <div class="form-group">
                    <label class="form-label">New password</label>
                    <input type="password" id="newPassword" class="form-input" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="At least 8 characters">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm new password</label>
                    <input type="password" id="newPasswordConfirm" class="form-input" required minlength="8" maxlength="128" autocomplete="new-password" placeholder="Repeat new password">
                </div>
                <button type="submit" class="login-btn" id="submitBtn">
                    <span class="spinner"></span>
                    <span class="btn-text">Update password</span>
                </button>
                <div class="pwd-back-row">
                    <a href="evaluate.php">Back to evaluations</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('changePwdForm');
            var err = document.getElementById('changePwdError');
            var submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                err.style.display = 'none';

                var current = document.getElementById('currentPassword').value;
                var next = document.getElementById('newPassword').value;
                var again = document.getElementById('newPasswordConfirm').value;

                if (next !== again) {
                    err.textContent = 'New passwords do not match.';
                    err.style.display = 'block';
                    return;
                }
                if (next.length < 8) {
                    err.textContent = 'New password must be at least 8 characters.';
                    err.style.display = 'block';
                    return;
                }

                submitBtn.classList.add('loading');
                submitBtn.disabled = true;

                try {
                    var response = await fetch('/api/auth.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            action: 'change_password',
                            current_password: current,
                            new_password: next
                        })
                    });
                    var result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Could not update password.');
                    }
                    submitBtn.querySelector('.btn-text').textContent = 'Saved';
                    submitBtn.style.background = '#10b981';
                    var signAgain = !!(result.sign_in_again === true || result.sign_in_again === 1 || result.sign_in_again === '1');
                    window.setTimeout(function () {
                        if (signAgain) {
                            window.location.href = 'login.php';
                            return;
                        }
                        window.location.href = 'evaluate.php';
                    }, 400);
                } catch (x) {
                    err.textContent = (x && x.message) ? x.message : 'Something went wrong.';
                    err.style.display = 'block';
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            });
        })();
    </script>
</body>
</html>
