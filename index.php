<?php
$pageTitle = 'Student Complaint Portal';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect logged-in users to their dashboard
if (is_logged_in()) {
    redirect($BASE_URL . '/dashboard.php');
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db.php';

$stats = [
    'total' => 0,
    'resolved' => 0,
    'pending' => 0
];

try {
    $totalStmt = $pdo->query("SELECT COUNT(*) AS total FROM complaints");
    $resolvedStmt = $pdo->query("SELECT COUNT(*) AS resolved FROM complaints WHERE status = 'Resolved'");
    $pendingStmt = $pdo->query("SELECT COUNT(*) AS pending FROM complaints WHERE status IN ('Submitted','Under Review','In Progress')");

    $stats['total'] = (int) $totalStmt->fetch()['total'];
    $stats['resolved'] = (int) $resolvedStmt->fetch()['resolved'];
    $stats['pending'] = (int) $pendingStmt->fetch()['pending'];
} catch (Exception $e) {
}
?>

<section class="hero card animate-up" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 5rem 2rem; border: none; background: transparent; box-shadow: none;">
    <div style="max-width: 800px;">
        <h1 class="hero-title">Empowering Students, <br>Improving Campus Life.</h1>
        <p class="hero-subtitle">Submit, track, and resolve campus issues with our streamlined, secure, and transparent complaint management system.</p>
        <div class="btn-group" style="justify-content: center; gap: 1rem;">
            <a class="btn primary" href="<?= e($BASE_URL); ?>/register.php">Get Started</a>
            <a class="btn outline" href="<?= e($BASE_URL); ?>/login.php">Member Login</a>
        </div>
    </div>
</section>

<section class="grid grid-3 animate-up delay-1" style="max-width: 1100px; margin: 0 auto 4rem auto;">
    <div class="stat glass-card">
        <h2 style="font-size: 2.5rem; color: var(--primary);"><?= e((string) $stats['total']); ?></h2>
        <p style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.7rem; color: var(--text-muted);">Total Cases</p>
    </div>
    <div class="stat glass-card">
        <h2 style="font-size: 2.5rem; color: var(--secondary);"><?= e((string) $stats['resolved']); ?></h2>
        <p style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.7rem; color: var(--text-muted);">Resolved</p>
    </div>
    <div class="stat glass-card">
        <h2 style="font-size: 2.5rem; color: var(--accent);"><?= e((string) $stats['pending']); ?></h2>
        <p style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.7rem; color: var(--text-muted);">In Progress</p>
    </div>
</section>

<section class="grid grid-3 animate-up delay-2" style="max-width: 1100px; margin: 0 auto;">
    <div class="card" style="text-align: center;">
        <div style="width: 56px; height: 56px; background: var(--primary-soft); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; color: var(--primary); box-shadow: 0 8px 20px -6px hsla(var(--primary-h), var(--primary-s), var(--primary-l), 0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; font-weight: 700;">Trusted Priority</h3>
        <p style="font-size: 0.95rem;">Your concerns are encrypted and prioritized based on urgency and impact.</p>
    </div>
    <div class="card" style="text-align: center;">
        <div style="width: 56px; height: 56px; background: hsla(var(--secondary-h), var(--secondary-s), var(--secondary-l), 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; color: var(--secondary); box-shadow: 0 8px 20px -6px hsla(var(--secondary-h), var(--secondary-s), var(--secondary-l), 0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; font-weight: 700;">Real-time Tracking</h3>
        <p style="font-size: 0.95rem;">Get instant updates on your dashboard as your complaint moves through stages.</p>
    </div>
    <div class="card" style="text-align: center;">
        <div style="width: 56px; height: 56px; background: hsla(var(--accent-h), var(--accent-s), var(--accent-l), 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto; color: var(--accent); box-shadow: 0 8px 20px -6px hsla(var(--accent-h), var(--accent-s), var(--accent-l), 0.3);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; font-weight: 700;">Community Driven</h3>
        <p style="font-size: 0.95rem;">Help us improve university infrastructure by reporting issues directly.</p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
