<?php
$pageTitle = 'New Complaint';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();
require_role(['student']);

$message = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $category = $_POST['category'] ?? '';
        $priority = $_POST['priority'] ?? '';
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $attachment = null;
        if (!empty($_FILES['attachment']['name'])) {
            $attachment = upload_file(
                $_FILES['attachment'],
                __DIR__ . '/../../uploads/complaints',
                ['image/jpeg','image/png','image/webp','application/pdf']
            );
        }

        $complaintUid = generate_complaint_uid();
        $stmt = $pdo->prepare("INSERT INTO complaints (complaint_uid, student_id, category, priority, subject, description, attachment_path) VALUES (:uid, :student_id, :category, :priority, :subject, :description, :attachment)");
        $stmt->execute([
            'uid' => $complaintUid,
            'student_id' => current_user()['id'],
            'category' => $category,
            'priority' => $priority,
            'subject' => $subject,
            'description' => $description,
            'attachment' => $attachment ? $BASE_URL . '/uploads/complaints/' . $attachment : null
        ]);

        $complaintId = (int) $pdo->lastInsertId();
        $logStmt = $pdo->prepare("INSERT INTO complaint_logs (complaint_id, status, remark, created_by) VALUES (:id, 'Submitted', 'Complaint submitted', :by)");
        $logStmt->execute(['id' => $complaintId, 'by' => current_user()['id']]);

        $success = 'Complaint submitted successfully. Your Complaint ID is ' . $complaintUid;
    }
}
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div class="card" style="border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(12px);">
        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Submit New Complaint</h2>
        <p style="margin-bottom: 2rem;">Describe the issue in detail so we can route it to the right department.</p>

        <?php if ($message): ?>
            <div class="notice" style="margin-bottom: 1.5rem;"><?= e($message); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="notice success" style="margin-bottom: 1.5rem;"><?= e($success); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
            
            <div class="grid grid-2" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label>Department Category</label>
                    <select name="category" required>
                        <option value="Hostel">Hostel & Accommodation</option>
                        <option value="Academics">Academics & Faculty</option>
                        <option value="Transport">Transport & Logistics</option>
                        <option value="Infrastructure">Infrastructure & Maintenance</option>
                        <option value="Safety">Safety & Security</option>
                        <option value="Other">Other Concerns</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Urgency Level</label>
                    <select name="priority" required>
                        <option value="Low">Low - Improvement suggestion</option>
                        <option value="Medium" selected>Medium - Standard issue</option>
                        <option value="High">High - Urgent attention needed</option>
                        <option value="Critical">Critical - Immediate action required</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Subject / Short Summary</label>
                <input type="text" name="subject" placeholder="e.g. Water shortage in Block A" required>
            </div>

            <div class="form-group">
                <label>Detailed Description</label>
                <textarea name="description" rows="6" placeholder="Provide as much detail as possible..." required></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Supporting Documents (Photos/PDf)</label>
                <input type="file" name="attachments[]" multiple style="font-size: 0.85rem;">
            </div>

            <div style="display: flex; gap: 1rem;">
                <button class="btn primary" type="submit" style="padding: 12px 32px;">Submit Case</button>
                <a href="list.php" class="btn outline" style="padding: 12px 32px;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
