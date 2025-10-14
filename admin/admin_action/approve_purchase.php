<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php?error=Please+login');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$type = $_POST['type'] ?? 'purchase'; // Default to purchase if not provided

if (!$id) {
    header('Location: ../dashboard.php?error=Invalid+ID');
    exit;
}

try {
    if ($type === 'sell') {
        // ✅ Approve a user-submitted sell item
        $stmt = $pdo->prepare("
            UPDATE items 
            SET status = 'approved', 
                approved_by = :admin_id, 
                approved_at = NOW(),
                updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute([
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'id' => $id
        ]);
        header('Location: ../dashboard.php?success=Sell+Item+Approved');
        exit;

    } else {
        // ✅ Approve a purchased item
        $stmt = $pdo->prepare("
            UPDATE items 
            SET status = 'purchased', 
                updated_at = NOW() 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        header('Location: ../dashboard.php?success=Item+Purchase+Approved');
        exit;
    }
} catch (Exception $e) {
    error_log("Approval error: " . $e->getMessage());
    header('Location: ../dashboard.php?error=Server+Error');
    exit;
}
