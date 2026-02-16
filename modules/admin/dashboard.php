<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/config.php';
require_login();
require_role(['admin','staff']);

$total = (int) $pdo->query("SELECT COUNT(*) AS c FROM complaints")->fetch()['c'];
$resolved = (int) $pdo->query("SELECT COUNT(*) AS c FROM complaints WHERE status = 'Resolved'")->fetch()['c'];
$pending = (int) $pdo->query("SELECT COUNT(*) AS c FROM complaints WHERE status IN ('Submitted','Under Review','In Progress')")->fetch()['c'];
$breakdownStmt = $pdo->query("SELECT category, COUNT(*) AS c FROM complaints GROUP BY category");
$breakdown = $breakdownStmt->fetchAll();

$resolvedPercent = $total ? round(($resolved / $total) * 100) : 0;
?>

<div class="grid grid-3" style="gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--primary); background: var(--glass-bg); backdrop-filter: blur(10px);">
        <p style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Cases</p>
        <h2 style="font-size: 2.5rem; margin: 0;"><?= e((string) $total); ?></h2>
    </div>
    <div class="card" style="border-left: 4px solid var(--secondary); background: var(--glass-bg); backdrop-filter: blur(10px);">
        <p style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 0.5rem;">Resolved</p>
        <h2 style="font-size: 2.5rem; margin: 0;"><?= e((string) $resolved); ?></h2>
    </div>
    <div class="card" style="border-left: 4px solid var(--accent); background: var(--glass-bg); backdrop-filter: blur(10px);">
        <p style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 1px; margin-bottom: 0.5rem;">Pending Action</p>
        <h2 style="font-size: 2.5rem; margin: 0;"><?= e((string) $pending); ?></h2>
    </div>
</div>

<div class="grid grid-2" style="grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Resolution Progress</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem;">
            <span>Overall Success Rate</span>
            <span style="font-weight: 600;"><?= e((string) $resolvedPercent); ?>%</span>
        </div>
        <div class="chart-bar" style="height: 12px; background: var(--bg-soft); border-radius: 99px; overflow: hidden;">
            <span style="display: block; height: 100%; width: <?= e((string) $resolvedPercent); ?>%; background: linear-gradient(to right, var(--primary), var(--secondary)); border-radius: 99px;"></span>
        </div>
    </div>

    <div class="card">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Department Breakdown</h3>
        <div style="display: grid; gap: 1rem;">
            <?php foreach ($breakdown as $row): ?>
                <?php $percent = $total ? round(($row['c'] / $total) * 100) : 0; ?>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem; font-size: 0.85rem;">
                        <span style="font-weight: 500;"><?= e($row['category']); ?></span>
                        <span style="color: var(--text-muted);"><?= e((string) $row['c']); ?> cases</span>
                    </div>
                    <div class="chart-bar" style="height: 6px; background: var(--bg-soft); border-radius: 99px; overflow: hidden;">
                        <span style="display: block; height: 100%; width: <?= e((string) $percent); ?>%; background: var(--primary); border-radius: 99px; opacity: 0.8;"></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <script src="<?= e($BASE_URL); ?>/assets/js/admin.js"></script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
