<?php
// joraki/api/get_report.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

try {
    // Get total items
    $total_stmt = $pdo->query("SELECT COUNT(*) AS total_items FROM items");
    $total_items = $total_stmt->fetch(PDO::FETCH_ASSOC)['total_items'];

    // Get total refurbished items
    $refurbished_stmt = $pdo->query("SELECT COUNT(*) AS refurbished FROM items WHERE status = 'refurbished'");
    $refurbished = $refurbished_stmt->fetch(PDO::FETCH_ASSOC)['refurbished'];

    // Get total sold items
    $sold_stmt = $pdo->query("SELECT COUNT(*) AS sold FROM items WHERE status = 'sold'");
    $sold = $sold_stmt->fetch(PDO::FETCH_ASSOC)['sold'];

    // Get total pending items
    $pending_stmt = $pdo->query("SELECT COUNT(*) AS pending FROM items WHERE status = 'pending'");
    $pending = $pending_stmt->fetch(PDO::FETCH_ASSOC)['pending'];

    // Combine all results
    echo json_encode([
        'success' => true,
        'data' => [
            'total_items' => $total_items,
            'refurbished' => $refurbished,
            'sold' => $sold,
            'pending' => $pending
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
