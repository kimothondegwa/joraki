<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php?error=Please+login');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../dashboard.php?error=Invalid+ID');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    header('Location: ../dashboard.php?success=Item+Deleted');
    exit;
} catch (Exception $e) {
    header('Location: ../dashboard.php?error=Server+Error');
    exit;
}
