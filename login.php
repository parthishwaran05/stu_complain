<?php
$pageTitle = 'Login';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect($BASE_URL . '/dashboard.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT users.*, roles.name AS role FROM users JOIN roles ON roles.id = users.role_id WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'avatar' => $user['avatar'] ?: $BASE_URL . '/assets/images/avatar-placeholder.svg'
            ];

            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);
            redirect($BASE_URL . '/dashboard.php');
        } else {
            $message = 'Invalid email or password.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 160px); padding: 2rem 0;">
    <div class="card animate-up" style="width: 100%; max-width: 420px; border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(12px);">
        <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem; text-align: center;">Welcome Back</h2>
        <p style="text-align: center; margin-bottom: 2rem;">Please enter your details to sign in.</p>
        
        <?php if ($message): ?>
            <div class="notice" style="margin-bottom: 1.5rem;"><?= e($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="name@university.edu" required>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button class="btn primary" type="submit" style="width: 100%; justify-content: center; padding: 12px;">Sign In</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
            Don't have an account? <a href="<?= e($BASE_URL); ?>/register.php" style="color: var(--primary); font-weight: 600;">Create one</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
