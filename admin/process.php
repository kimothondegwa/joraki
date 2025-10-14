<?php
// admin/process.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// ===============================
// LOGIN HANDLER
// ===============================
if (isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin' LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];

            // Update last login
            $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $update->execute([':id' => $admin['id']]);

            header("Location: dashboard.php");
            exit;
        } else {
            header("Location: index.php?error=Invalid+credentials");
            exit;
        }
    } catch (Exception $e) {
        header("Location: index.php?error=Server+error");
        exit;
    }
}

// ===============================
// AUTHENTICATION CHECK
// ===============================
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php?error=Please+login');
    exit;
}

// ===============================
// REQUEST VALIDATION
// ===============================
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
}

if (!$action || !$id) {
    header('Location: dashboard.php?error=Invalid+request');
    exit;
}

// ===============================
// ACTION HANDLING
// ===============================
try {
    switch ($action) {
        case 'approve_purchase':
            $stmt = $pdo->prepare("UPDATE items SET status = 'purchased', updated_at = NOW() WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $msg = "Item approved as purchased successfully.";
            break;

        case 'mark_refurbished':
            $stmt = $pdo->prepare("UPDATE items SET status = 'refurbished', updated_at = NOW() WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $msg = "Item marked as refurbished successfully.";
            break;

        case 'mark_sold':
            $stmt = $pdo->prepare("UPDATE items SET status = 'sold', updated_at = NOW() WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $msg = "Item marked as sold successfully.";
            break;

        case 'delete_item':
            $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $msg = "Item deleted successfully.";
            break;

        default:
            header('Location: dashboard.php?error=Unknown+action');
            exit;
    }

    header('Location: dashboard.php?success=' . urlencode($msg));
    exit;

} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Error: " . $e->getMessage());
    } else {
        header('Location: dashboard.php?error=Server+error');
        exit;
    }
}
