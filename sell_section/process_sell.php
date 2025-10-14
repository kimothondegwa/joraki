<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $condition = $_POST['condition_type'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $original_price = $_POST['original_price'] ?? null;
    $seller_name = $_POST['seller_name'];
    $seller_email = $_POST['seller_email'];
    $seller_phone = $_POST['seller_phone'];

    // Handle upload
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $filename = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('item_') . '.' . $ext;
        $target_path = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            die('Failed to upload image.');
        }
    }

    $stmt = $pdo->prepare("INSERT INTO items 
        (title, category, condition_type, description, price, original_price, image, status, seller_name, seller_email, seller_phone, created_at, updated_at)
        VALUES (:title, :category, :condition, :description, :price, :original_price, :image, 'pending', :seller_name, :seller_email, :seller_phone, NOW(), NOW())");

    $stmt->execute([
        ':title' => $title,
        ':category' => $category,
        ':condition' => $condition,
        ':description' => $description,
        ':price' => $price,
        ':original_price' => $original_price,
        ':image' => $filename,
        ':seller_name' => $seller_name,
        ':seller_email' => $seller_email,
        ':seller_phone' => $seller_phone
    ]);

    // Redirect back to form with success message
    header('Location: index.php?success=1');
    exit;
} else {
    header('Location: index.php');
    exit;
}
?>
