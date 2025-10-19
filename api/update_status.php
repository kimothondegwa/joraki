<?php
// joraki/api/update_status.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/config.php';

try {
    // Get inputs (can come from GET or POST)
    $id = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
    $status = isset($_POST['status']) ? trim($_POST['status']) : (isset($_GET['status']) ? trim($_GET['status']) : '');

    // Validation
    if (!$id || empty($status)) {
        echo json_encode([
            'success' => false,
            'message' => 'Item ID and status are required.'
        ]);
        exit;
    }

    // Update the status in DB
    $stmt = $pdo->prepare("UPDATE items SET status = :status, updated_at = NOW() WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => "Item #$id updated successfully to status '$status'."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "No item found with ID $id or status unchanged."
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
