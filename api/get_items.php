<?php
// joraki/api/items/get_items.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow from any origin

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';

// Optional filter by status (?status=refurbished)
$status = isset($_GET['status']) ? trim($_GET['status']) : null;

try {
    if ($status) {
        // Fetch items by specific status
        $stmt = $pdo->prepare("SELECT * FROM items WHERE status = :status ORDER BY created_at DESC");
        $stmt->execute(['status' => $status]);
    } else {
        // Fetch all items
        $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    }

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($items),
        'data' => $items
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

