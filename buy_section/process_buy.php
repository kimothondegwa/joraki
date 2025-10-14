<?php
// buy/process_buy.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// CSRF
if (empty($_POST['csrf']) || empty($_SESSION['buy_csrf']) || !hash_equals($_SESSION['buy_csrf'], $_POST['csrf'])) {
    header('Location: index.php?error=Invalid+request');
    exit;
}

// sanitize
$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$buyer_name = trim($_POST['buyer_name'] ?? '');
$buyer_email = trim($_POST['buyer_email'] ?? '');
$buyer_phone = trim($_POST['buyer_phone'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');

if (!$item_id || $buyer_name === '' || $buyer_email === '' || $buyer_phone === '' || $payment_method === '') {
    header('Location: item_details.php?id=' . urlencode($item_id) . '&error=Missing+fields');
    exit;
}

try {
    // check item exists and is still available (refurbished)
    $stmt = $pdo->prepare("SELECT id, title, price, status FROM items WHERE id = :id LIMIT 1 FOR UPDATE");
    $pdo->beginTransaction();
    $stmt->execute([':id' => $item_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $pdo->rollBack();
        header('Location: index.php?error=Item+not+found');
        exit;
    }

    if ($item['status'] !== 'refurbished') {
        // item not available
        $pdo->rollBack();
        header('Location: item_details.php?id=' . urlencode($item_id) . '&error=Item+not+available');
        exit;
    }

    $amount = (float) $item['price'];

    // Decide behavior by payment method
    $tx_status = 'pending';
    $item_new_status = null;
    $provider_ref = null;

    if ($payment_method === 'cod') {
        // Cash on delivery: create transaction completed and mark item as 'purchased'
        $tx_status = 'completed';
        $item_new_status = 'purchased';
    } elseif ($payment_method === 'mpesa') {
        // M-Pesa: create pending_payment and mock provider reference
        $tx_status = 'pending_payment';
        $item_new_status = 'reserved'; // temporary reserved
        // Here you could call an actual M-Pesa API and set provider_ref accordingly.
        $provider_ref = 'MPESA-' . time() . '-' . rand(1000,9999);
    } elseif ($payment_method === 'paypal') {
        $tx_status = 'pending_payment';
        $item_new_status = 'reserved';
        $provider_ref = 'PAYPAL-' . time() . '-' . rand(1000,9999);
    } else {
        $pdo->rollBack();
        header('Location: item_details.php?id=' . urlencode($item_id) . '&error=Invalid+payment+method');
        exit;
    }

    // insert transaction
    $ins = $pdo->prepare("INSERT INTO transactions (item_id, buyer_name, buyer_email, buyer_phone, payment_method, amount, status, provider_reference, created_at) VALUES (:item_id, :buyer_name, :buyer_email, :buyer_phone, :payment_method, :amount, :status, :provider_reference, NOW())");
    $ins->execute([
        ':item_id' => $item_id,
        ':buyer_name' => $buyer_name,
        ':buyer_email' => $buyer_email,
        ':buyer_phone' => $buyer_phone,
        ':payment_method' => $payment_method,
        ':amount' => $amount,
        ':status' => $tx_status,
        ':provider_reference' => $provider_ref
    ]);

    // update item status if needed
    if ($item_new_status !== null) {
        $upd = $pdo->prepare("UPDATE items SET status = :status, updated_at = NOW() WHERE id = :id LIMIT 1");
        $upd->execute([':status' => $item_new_status, ':id' => $item_id]);
    }

    $pdo->commit();

    // cleanup CSRF token for buy
    unset($_SESSION['buy_csrf']);

    // Redirect to confirmation page or item page with success message
    if ($payment_method === 'cod') {
        header('Location: ../admin/index.php?message=Purchase+placed.+Admin+will+process+it'); // or a user confirmations page
        exit;
    } else {
        // For mock payments show provider ref and pending message
        header('Location: item_details.php?id=' . urlencode($item_id) . '&success=' . urlencode('Payment+initiated+(reference:' . $provider_ref . ')'));
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        die('Error: ' . $e->getMessage());
    } else {
        header('Location: item_details.php?id=' . urlencode($item_id) . '&error=Server+error');
        exit;
    }
}
