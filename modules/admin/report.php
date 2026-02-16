<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_login();
require_role(['admin','staff']);

$stmt = $pdo->query("SELECT complaint_uid, category, priority, status, submitted_at FROM complaints ORDER BY submitted_at DESC LIMIT 100");
$rows = $stmt->fetchAll();

$content = "Complaint Report\n\n";
foreach ($rows as $row) {
    $content .= "{$row['complaint_uid']} | {$row['category']} | {$row['priority']} | {$row['status']} | {$row['submitted_at']}\n";
}

$stream = "BT /F1 10 Tf 50 750 Td (" . str_replace(["\\", "(", ")", "\n"], ["\\\\", "\\(", "\\)", ") Tj\n0 -14 Td ("], $content) . ") Tj ET";

$objects = [];
$objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R>>endobj\n";
$objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1>>endobj\n";
$objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources<< /Font<< /F1 5 0 R>>>>>>endobj\n";
$objects[] = "4 0 obj<< /Length " . strlen($stream) . ">>stream\n" . $stream . "\nendstream endobj\n";
$objects[] = "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica>>endobj\n";

$pdf = "%PDF-1.4\n";
$offsets = [0];
foreach ($objects as $obj) {
    $offsets[] = strlen($pdf);
    $pdf .= $obj;
}

$xrefPos = strlen($pdf);
$pdf .= "xref\n0 6\n0000000000 65535 f \n";
for ($i = 1; $i <= 5; $i++) {
    $pdf .= str_pad((string) $offsets[$i], 10, '0', STR_PAD_LEFT) . " 00000 n \n";
}
$pdf .= "trailer<< /Size 6 /Root 1 0 R>>\nstartxref\n{$xrefPos}\n%%EOF";

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="complaint-report.pdf"');
echo $pdf;
