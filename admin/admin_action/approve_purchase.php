<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

// Ensure admin logged in
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../index.php?error=Please+login');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';
$type = $_POST['type'] ?? '';

if (!$id || !in_array($action, ['approve', 'reject'])) {
    header('Location: ../dashboard.php?error=Invalid+Request');
    exit;
}

try {
    if ($action === 'approve') {
        // ✅ Approve item and mark as refurbished directly
        $stmt = $pdo->prepare("
            UPDATE items 
            SET status = 'refurbished',
                approved_by = :admin_id,
                approved_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'id' => $id
        ]);

        header('Location: ../dashboard.php?success=Item+Approved+and+Moved+to+Refurbished');
        exit;
    } 
    elseif ($action === 'reject') {
        // ❌ Reject item
        $stmt = $pdo->prepare("
            UPDATE items 
            SET status = 'rejected',
                rejected_by = :admin_id,
                rejected_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ");
        $stmt->execute([
            'admin_id' => $_SESSION['admin_id'] ?? null,
            'id' => $id
        ]);

        header('Location: ../dashboard.php?success=Item+Rejected');
        exit;
    }

} catch (Exception $e) {
    error_log("Approval error: " . $e->getMessage());
    header('Location: ../dashboard.php?error=Server+Error');
    exit;
}
?>
