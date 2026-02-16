<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();

$user = current_user();
$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $avatarName = null;

        if (!empty($_FILES['avatar']['name'])) {
            $avatarName = upload_file(
                $_FILES['avatar'],
                __DIR__ . '/../../uploads/avatars',
                ['image/jpeg','image/png','image/webp']
            );
            if (!$avatarName) {
                $message = 'Failed to upload avatar. Please try again.';
            }
        }

        if (!$message) {
            $avatarPath = $avatarName ? $BASE_URL . '/uploads/avatars/' . $avatarName : null;
            $stmt = $pdo->prepare("UPDATE users SET name = :name, phone = :phone, avatar = COALESCE(:avatar, avatar) WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'phone' => $phone,
                'avatar' => $avatarPath,
                'id' => $user['id']
            ]);

            $_SESSION['user']['name'] = $name;
            if ($avatarName) {
                $_SESSION['user']['avatar'] = $avatarPath;
            }
            $success = 'Profile updated.';
        }
    }
}

$stmt = $pdo->prepare("SELECT name, email, phone, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $user['id']]);
$profile = $stmt->fetch();
?>

<div class="card" style="max-width: 560px;">
    <h2>Profile</h2>
    <?php if ($message): ?>
        <div class="notice"><?= e($message); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="notice success"><?= e($success); ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= e($profile['name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" value="<?= e($profile['email']); ?>" disabled>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= e($profile['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Profile Picture</label>
            <input type="file" name="avatar" accept="image/*">
        </div>
        <button class="btn primary" type="submit">Save</button>
        <a class="btn outline" href="/modules/auth/change_password.php">Change Password</a>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
