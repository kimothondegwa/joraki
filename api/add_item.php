<?php
// joraki/api/items/add_item.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Collect and validate data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$price = isset($_POST['price']) ? trim($_POST['price']) : '';
$status = 'pending'; // Default when user submits
$imagePath = null;

// Validate required fields
if ($name === '' || $price === '') {
    echo json_encode(['success' => false, 'error' => 'Name and price are required']);
    exit;
}

// Handle image upload (optional)
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/';
    $fileName = uniqid('item_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $imagePath = $fileName;
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to upload image']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO items (name, description, price, image, status, created_at) 
        VALUES (:name, :description, :price, :image, :status, NOW())
    ");

    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':image' => $imagePath,
        ':status' => $status
    ]);

    echo json_encode(['success' => true, 'message' => 'Item added successfully']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

