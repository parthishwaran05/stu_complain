<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';
require_login();

$user = current_user();
if ($user['role'] === 'student') {
    header('Location: ' . $BASE_URL . '/modules/complaints/list.php');
    exit;
}

header('Location: ' . $BASE_URL . '/modules/admin/dashboard.php');
exit;
