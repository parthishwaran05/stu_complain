<?php
$pageTitle = 'Change Password';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$user = current_user();
$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            $message = 'Passwords do not match.';
        } else {
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id");
            $stmt->execute(['id' => $user['id']]);
            $dbUser = $stmt->fetch();

            if ($dbUser && password_verify($current, $dbUser['password_hash'])) {
                $hash = password_hash($new, PASSWORD_BCRYPT);
                $update = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
                $update->execute(['hash' => $hash, 'id' => $user['id']]);
                $success = 'Password updated.';
            } else {
                $message = 'Current password is incorrect.';
            }
        }
    }
}
?>

<div class="card" style="max-width: 520px;">
    <h2>Change Password</h2>
    <?php if ($message): ?>
        <div class="notice"><?= e($message); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="notice success"><?= e($success); ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button class="btn primary" type="submit">Update</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
