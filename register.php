<?php
$pageTitle = 'Register';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect($BASE_URL . '/dashboard.php');
}

require_once __DIR__ . '/includes/header.php';
$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($password !== $confirm) {
            $message = 'Passwords do not match.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $message = 'Email is already registered.';
            } else {
                $avatarName = null;
                if (!empty($_FILES['avatar']['name'])) {
                    $avatarName = upload_file(
                        $_FILES['avatar'],
                        __DIR__ . '/uploads/avatars',
                        ['image/jpeg','image/png','image/webp']
                    );
                }

                $hash = password_hash($password, PASSWORD_BCRYPT);
                $insert = $pdo->prepare("INSERT INTO users (role_id, name, email, phone, password_hash, avatar) VALUES (1, :name, :email, :phone, :hash, :avatar)");
                $insert->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'hash' => $hash,
                    'avatar' => $avatarName ? $BASE_URL . '/uploads/avatars/' . $avatarName : null
                ]);

                $success = 'Registration successful. Please login.';
            }
        }
    }
}
?>

<div style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 160px); padding: 2rem 0;">
    <div class="card animate-up" style="width: 100%; max-width: 600px; border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(12px);">
        <h2 style="font-size: 1.75rem; margin-bottom: 0.5rem; text-align: center;">Create Account</h2>
        <p style="text-align: center; margin-bottom: 2rem;">Join the Student Portal to start resolving concerns.</p>
        
        <?php if ($message): ?>
            <div class="notice" style="margin-bottom: 1.5rem;"><?= e($message); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="notice success" style="margin-bottom: 1.5rem;"><?= e($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Phone</label>
                    <input type="text" name="phone" placeholder="9876543210">
                </div>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="name@university.edu" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Profile Picture</label>
                <input type="file" name="avatar" accept="image/*" style="font-size: 0.85rem;">
            </div>
            
            <button class="btn primary" type="submit" style="width: 100%; justify-content: center; padding: 12px;">Create Account</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
            Already have an account? <a href="<?= e($BASE_URL); ?>/login.php" style="color: var(--primary); font-weight: 600;">Sign In</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
