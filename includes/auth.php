<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$SESSION_TIMEOUT = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();

function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        global $BASE_URL;
        if (!isset($BASE_URL)) {
            require_once __DIR__ . '/config.php';
        }
        header('Location: ' . $BASE_URL . '/login.php');
        exit;
    }
}

function require_role(array $roles): void {
    $user = current_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        include __DIR__ . '/forbidden.php';
        exit;
    }
}
