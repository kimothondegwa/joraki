<?php
// joraki/api/export_report.php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="joraki_report.csv"');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

try {
    // Optional filter (?status=sold or ?status=refurbished)
    $status = isset($_GET['status']) ? trim($_GET['status']) : null;

    if ($status) {
        $stmt = $pdo->prepare("SELECT * FROM items WHERE status = :status ORDER BY created_at DESC");
        $stmt->execute(['status' => $status]);
    } else {
        $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    }

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Open output stream for CSV
    $output = fopen('php://output', 'w');

    // Write CSV header
    fputcsv($output, ['ID', 'Item Name', 'Description', 'Price', 'Status', 'Created At', 'Updated At']);

    // Write data rows
    foreach ($items as $item) {
        fputcsv($output, [
            $item['id'],
            $item['name'] ?? '',
            $item['description'] ?? '',
            $item['price'] ?? '',
            $item['status'] ?? '',
            $item['created_at'] ?? '',
            $item['updated_at'] ?? ''
        ]);
    }

    fclose($output);

} catch (PDOException $e) {
    echo "Error generating report: " . $e->getMessage();
}
