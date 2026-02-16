<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_role(['student']);

$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$query = "SELECT id, complaint_uid, subject, category, priority, status, DATE_FORMAT(updated_at, '%Y-%m-%d %H:%i') AS updated_at FROM complaints WHERE student_id = :student_id";
$params = ['student_id' => current_user()['id']];

if ($status) {
    $query .= " AND status = :status";
    $params['status'] = $status;
}
if ($category) {
    $query .= " AND category = :category";
    $params['category'] = $category;
}
if ($from) {
    $query .= " AND DATE(submitted_at) >= :from";
    $params['from'] = $from;
}
if ($to) {
    $query .= " AND DATE(submitted_at) <= :to";
    $params['to'] = $to;
}

$query .= " ORDER BY submitted_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);

header('Content-Type: application/json');
echo json_encode($stmt->fetchAll());
