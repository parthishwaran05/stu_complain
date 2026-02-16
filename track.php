<?php
$pageTitle = 'Track Complaint';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/functions.php';

$complaint = null;
$logs = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $cid = trim($_POST['complaint_uid'] ?? '');
        $stmt = $pdo->prepare("SELECT * FROM complaints WHERE complaint_uid = :uid");
        $stmt->execute(['uid' => $cid]);
        $complaint = $stmt->fetch();
        if ($complaint) {
            $logStmt = $pdo->prepare("SELECT * FROM complaint_logs WHERE complaint_id = :id ORDER BY created_at ASC");
            $logStmt->execute(['id' => $complaint['id']]);
            $logs = $logStmt->fetchAll();
        } else {
            $message = 'Complaint ID not found.';
        }
    }
}
?>

<div class="card" style="max-width: 520px; margin: 40px auto;">
    <h2>Track Complaint</h2>
    <?php if ($message): ?>
        <div class="notice"><?= e($message); ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
        <div class="form-group">
            <label>Complaint ID</label>
            <input type="text" name="complaint_uid" required>
        </div>
        <button class="btn primary" type="submit">Track</button>
    </form>

    <?php if ($complaint): ?>
        <hr style="margin: 20px 0;">
        <?php $statusClass = 'status-' . strtolower(str_replace(' ', '-', $complaint['status'])); ?>
        <h3>Status: <span class="badge <?= e($statusClass); ?>"><?= e($complaint['status']); ?></span></h3>
        <p><strong>Category:</strong> <?= e($complaint['category']); ?></p>
        <p><strong>Priority:</strong> <?= e($complaint['priority']); ?></p>
        <div class="timeline">
            <?php foreach ($logs as $log): ?>
                <div class="timeline-item">
                    <strong><?= e($log['status']); ?></strong> — <?= e($log['created_at']); ?>
                    <?php if (!empty($log['remark'])): ?>
                        <p><?= e($log['remark']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
