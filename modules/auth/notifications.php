<?php
$pageTitle = 'Notifications';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_login();

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :id ORDER BY created_at DESC");
$stmt->execute(['id' => current_user()['id']]);
$notes = $stmt->fetchAll();

$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :id")->execute(['id' => current_user()['id']]);
?>

<div class="card">
    <h2>Notifications</h2>
    <?php if (!$notes): ?>
        <p>No notifications.</p>
    <?php endif; ?>
    <?php foreach ($notes as $note): ?>
        <div class="card" style="margin-bottom: 10px;">
            <strong><?= e($note['title']); ?></strong>
            <p><?= e($note['message']); ?></p>
            <small><?= e($note['created_at']); ?></small>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
