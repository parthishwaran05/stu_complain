<?php
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

function generate_complaint_uid(): string {
    return 'CMP-' . strtoupper(bin2hex(random_bytes(4)));
}

function upload_file(array $file, string $targetDir, array $allowedTypes, int $maxSize = 5242880): ?string {
    if (!isset($file['name']) || empty($file['name'])) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > $maxSize) {
        return null;
    }

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowedTypes, true)) {
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('upload_', true) . '.' . $ext;
    $path = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return null;
    }

    return $filename;
}

function log_audit(PDO $pdo, int $adminId, string $action, string $targetType, ?int $targetId, string $details = ''): void {
    $stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action, target_type, target_id, details) VALUES (:admin_id, :action, :target_type, :target_id, :details)");
    $stmt->execute([
        'admin_id' => $adminId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'details' => $details
    ]);
}

function create_notification(PDO $pdo, int $userId, string $title, string $message): void {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)");
    $stmt->execute([
        'user_id' => $userId,
        'title' => $title,
        'message' => $message
    ]);
}
