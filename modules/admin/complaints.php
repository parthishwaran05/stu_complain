<?php
$pageTitle = 'Manage Complaints';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();
require_role(['admin','staff']);

$user = current_user();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid request token.';
    } else {
        $complaintId = (int) ($_POST['complaint_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $remark = trim($_POST['remark'] ?? '');
        $assignedStaff = !empty($_POST['assigned_staff']) ? (int) $_POST['assigned_staff'] : null;

        $update = $pdo->prepare("UPDATE complaints SET status = :status, assigned_staff_id = :staff, 
            under_review_at = IF(:status = 'Under Review', NOW(), under_review_at),
            in_progress_at = IF(:status = 'In Progress', NOW(), in_progress_at),
            resolved_at = IF(:status = 'Resolved', NOW(), resolved_at),
            rejected_at = IF(:status = 'Rejected', NOW(), rejected_at)
            WHERE id = :id");
        $update->execute([
            'status' => $status,
            'staff' => $assignedStaff,
            'id' => $complaintId
        ]);

        $log = $pdo->prepare("INSERT INTO complaint_logs (complaint_id, status, remark, created_by) VALUES (:id, :status, :remark, :by)");
        $log->execute(['id' => $complaintId, 'status' => $status, 'remark' => $remark, 'by' => $user['id']]);

        $studentStmt = $pdo->prepare("SELECT student_id FROM complaints WHERE id = :id");
        $studentStmt->execute(['id' => $complaintId]);
        $student = $studentStmt->fetch();
        if ($student) {
            create_notification($pdo, (int) $student['student_id'], 'Complaint Update', "Your complaint status has been updated to {$status}.");
        }

        log_audit($pdo, $user['id'], 'Update Complaint', 'complaints', $complaintId, $remark);
        $message = 'Complaint updated.';
    }
}

$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = '1=1';
$params = [];
if ($search) {
    $where .= " AND (complaint_uid LIKE :search OR subject LIKE :search)";
    $params['search'] = '%' . $search . '%';
}
if ($statusFilter) {
    $where .= " AND complaints.status = :status";
    $params['status'] = $statusFilter;
}
if ($categoryFilter) {
    $where .= " AND complaints.category = :category";
    $params['category'] = $categoryFilter;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM complaints WHERE {$where}");
$countStmt->execute($params);
$total = (int) $countStmt->fetch()['c'];

$query = "SELECT complaints.*, users.name AS student_name FROM complaints JOIN users ON users.id = complaints.student_id WHERE {$where} ORDER BY submitted_at DESC LIMIT {$limit} OFFSET {$offset}";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

$staffStmt = $pdo->query("SELECT id, name FROM users WHERE role_id = 3");
$staffList = $staffStmt->fetchAll();

foreach ($complaints as $row) {
    if ($row['priority'] === 'High' || strtotime($row['submitted_at']) < strtotime('-7 days')) {
        $pdo->prepare("UPDATE complaints SET escalated = 1 WHERE id = :id")->execute(['id' => $row['id']]);
    }
}
?>

<div class="card">
    <h2>Manage Complaints</h2>
    <?php if ($message): ?>
        <div class="notice success"><?= e($message); ?></div>
    <?php endif; ?>
    <form method="GET" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
        <input type="text" name="search" placeholder="Search" value="<?= e($search); ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="Submitted">Submitted</option>
            <option value="Under Review">Under Review</option>
            <option value="In Progress">In Progress</option>
            <option value="Resolved">Resolved</option>
            <option value="Rejected">Rejected</option>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            <option value="Academics">Academics</option>
            <option value="Hostel">Hostel</option>
            <option value="Transport">Transport</option>
            <option value="Infrastructure">Infrastructure</option>
            <option value="Others">Others</option>
        </select>
        <button class="btn primary" type="submit">Filter</button>
    </form>

    <table class="table" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Escalated</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($complaints as $c): ?>
                <tr>
                    <td><?= e($c['complaint_uid']); ?></td>
                    <td><?= e($c['student_name']); ?></td>
                    <td><?= e($c['subject']); ?></td>
                    <td><?= e($c['priority']); ?></td>
                    <?php $statusClass = 'status-' . strtolower(str_replace(' ', '-', $c['status'])); ?>
                    <td><span class="badge <?= e($statusClass); ?>"><?= e($c['status']); ?></span></td>
                    <td><?= $c['escalated'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <form method="POST" style="display: grid; gap: 6px;">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                            <input type="hidden" name="complaint_id" value="<?= e((string) $c['id']); ?>">
                            <select name="status">
                                <option <?= $c['status'] === 'Submitted' ? 'selected' : ''; ?>>Submitted</option>
                                <option <?= $c['status'] === 'Under Review' ? 'selected' : ''; ?>>Under Review</option>
                                <option <?= $c['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option <?= $c['status'] === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option <?= $c['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <select name="assigned_staff">
                                <option value="">Assign Staff</option>
                                <?php foreach ($staffList as $staff): ?>
                                    <option value="<?= e((string) $staff['id']); ?>" <?= $c['assigned_staff_id'] == $staff['id'] ? 'selected' : ''; ?>>
                                        <?= e($staff['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="remark" placeholder="Remark">
                            <button class="btn outline" type="submit">Update</button>
                            <a class="btn outline" href="<?= e($BASE_URL); ?>/modules/complaints/view.php?id=<?= e((string) $c['id']); ?>">View</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php $totalPages = ceil($total / $limit); ?>
    <div style="margin-top: 10px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="btn outline" href="?page=<?= $i; ?>&search=<?= e($search); ?>&status=<?= e($statusFilter); ?>&category=<?= e($categoryFilter); ?>"><?= $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h3>Download Reports</h3>
    <a class="btn primary" href="<?= e($BASE_URL); ?>/modules/admin/report.php">Download PDF Report</a>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
