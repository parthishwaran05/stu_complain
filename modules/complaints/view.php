<?php
$pageTitle = 'Complaint Details';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();
require_login();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT complaints.*, users.name AS student_name FROM complaints JOIN users ON users.id = complaints.student_id WHERE complaints.id = :id");
$stmt->execute(['id' => $id]);
$complaint = $stmt->fetch();

if (!$complaint) {
    http_response_code(404);
    echo '<div class="card">Complaint not found.</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if (current_user()['role'] === 'student' && $complaint['student_id'] !== current_user()['id']) {
    http_response_code(403);
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

$logsStmt = $pdo->prepare("SELECT complaint_logs.*, users.name AS actor FROM complaint_logs LEFT JOIN users ON users.id = complaint_logs.created_by WHERE complaint_id = :id ORDER BY created_at ASC");
$logsStmt->execute(['id' => $id]);
$logs = $logsStmt->fetchAll();

$feedbackStmt = $pdo->prepare("SELECT * FROM feedback WHERE complaint_id = :id AND student_id = :student_id");
$feedbackStmt->execute(['id' => $id, 'student_id' => current_user()['id']]);
$feedback = $feedbackStmt->fetch();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user()['role'] === 'student') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $rating = (int) ($_POST['rating'] ?? 0);
        $comments = trim($_POST['comments'] ?? '');
        if ($rating >= 1 && $rating <= 5) {
            $insert = $pdo->prepare("INSERT INTO feedback (complaint_id, student_id, rating, comments) VALUES (:complaint_id, :student_id, :rating, :comments)");
            $insert->execute([
                'complaint_id' => $id,
                'student_id' => current_user()['id'],
                'rating' => $rating,
                'comments' => $comments
            ]);
            $message = 'Thank you for your feedback.';
        }
    }
}
?>

<div class="card">
    <h2>Complaint <?= e($complaint['complaint_uid']); ?></h2>
    <?php if ($message): ?>
        <div class="notice success"><?= e($message); ?></div>
    <?php endif; ?>
    <p><strong>Student:</strong> <?= e($complaint['student_name']); ?></p>
    <p><strong>Category:</strong> <?= e($complaint['category']); ?></p>
    <p><strong>Priority:</strong> <?= e($complaint['priority']); ?></p>
    <?php $statusClass = 'status-' . strtolower(str_replace(' ', '-', $complaint['status'])); ?>
    <p><strong>Status:</strong> <span class="badge <?= e($statusClass); ?>"><?= e($complaint['status']); ?></span></p>
    <p><strong>Subject:</strong> <?= e($complaint['subject']); ?></p>
    <p><strong>Description:</strong> <?= e($complaint['description']); ?></p>
    <?php if (!empty($complaint['attachment_path'])): ?>
        <p><strong>Attachment:</strong> <a href="<?= e($complaint['attachment_path']); ?>" target="_blank">View</a></p>
    <?php endif; ?>

    <h3>Timeline</h3>
    <div class="timeline">
        <?php foreach ($logs as $log): ?>
            <div class="timeline-item">
                <strong><?= e($log['status']); ?></strong> — <?= e($log['created_at']); ?>
                <?php if (!empty($log['remark'])): ?>
                    <p><?= e($log['remark']); ?></p>
                <?php endif; ?>
                <?php if (!empty($log['actor'])): ?>
                    <small>by <?= e($log['actor']); ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (current_user()['role'] === 'student' && $complaint['status'] === 'Resolved' && !$feedback): ?>
        <hr style="margin: 20px 0;">
        <h3>Rate Resolution</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            <div class="form-group">
                <label>Rating (1-5)</label>
                <select name="rating" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Bad</option>
                </select>
            </div>
            <div class="form-group">
                <label>Comments</label>
                <textarea name="comments" rows="3"></textarea>
            </div>
            <button class="btn primary" type="submit">Submit Feedback</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
