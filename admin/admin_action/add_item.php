<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'General';
    $condition_type = 'used';
    $status = 'refurbished'; // Makes it show in "Recently Refurbished"
    $image_name = null;

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = __DIR__ . '/../../uploads/';
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Image uploaded successfully
        } else {
            die("❌ Failed to upload image. Please try again.");
        }
    }

    // Insert item into database
    try {
        $sql = "INSERT INTO items (title, category, description, condition_type, price, original_price, image, status, created_at, updated_at)
                VALUES (:title, :category, :description, :condition_type, :price, :original_price, :image, :status, NOW(), NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':category' => $category,
            ':description' => $description,
            ':condition_type' => $condition_type,
            ':price' => $price,
            ':original_price' => $price,
            ':image' => $image_name,
            ':status' => $status
        ]);

        header("Location: ../dashboard.php?success=Item added successfully");
        exit;
    } catch (Exception $e) {
        die("❌ Error adding item: " . $e->getMessage());
    }
}
?>
