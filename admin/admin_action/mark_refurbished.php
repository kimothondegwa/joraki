<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

// 🔐 Ensure admin is logged in
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php?error=Please+login');
    exit;
}

// 🆔 Validate item ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../dashboard.php?error=Invalid+Item+ID');
    exit;
}

try {
    // ✅ Update item status to 'refurbished'
    $stmt = $pdo->prepare("
        UPDATE items 
        SET status = 'refurbished',
            refurbished_by = :admin_id,
            refurbished_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute([
        'admin_id' => $_SESSION['admin_id'] ?? null,
        'id' => $id
    ]);

    // ✅ Redirect with success message
    header('Location: ../dashboard.php?success=Item+Marked+as+Refurbished');
    exit;

} catch (Exception $e) {
    // 🧯 Log error for debugging
    error_log("Refurbish error: " . $e->getMessage());
    header('Location: ../dashboard.php?error=Server+Error');
    exit;
}
?>
