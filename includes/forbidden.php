<?php
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <?php require_once __DIR__ . '/config.php'; ?>
    <link rel="stylesheet" href="<?= e($BASE_URL); ?>/assets/css/style.css">
</head>
<body class="page-center">
    <div class="card">
        <h1>Access Denied</h1>
        <p>You don't have permission to access this page.</p>
        <a class="btn" href="<?= e($BASE_URL); ?>/index.php">Go Home</a>
    </div>
</body>
</html>
