<?php
$DB_HOST = 'localhost';
$DB_NAME = 'student_portal';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // If DB fails, we still want to see the UI if possible, 
    // but many pages depend on $pdo. Let's at least show a styled error.
    if (php_sapi_name() !== 'cli') {
        echo "<div style='font-family:sans-serif; padding:2rem; text-align:center; background:#fff1f2; color:#991b1b; border:1px solid #fecaca; border-radius:12px; margin:2rem;'>
                <h2 style='margin-bottom:0.5rem;'>Database Connection Issue</h2>
                <p>Please ensure you have a MySQL database named '<b>{$DB_NAME}</b>' running.</p>
                <p style='font-size:0.9rem; margin-top:1rem; color:#6b7280;'>Error: " . $e->getMessage() . "</p>
              </div>";
    }
    // Define a dummy PDO or exit? Most code needs $pdo.
    exit;
}

function ensure_admin(PDO $pdo): void {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role_id = 2 LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    if (!$admin) {
        $passwordHash = password_hash('Admin@123', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO users (role_id, name, email, password_hash) VALUES (2, 'System Admin', 'admin@portal.com', :hash)");
        $insert->execute(['hash' => $passwordHash]);
    }
}

ensure_admin($pdo);
