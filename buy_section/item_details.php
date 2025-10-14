<?php 
// buy/item_details.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) {
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    die('Error retrieving item: ' . $e->getMessage());
}

// create CSRF token for buy form
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['buy_csrf'])) $_SESSION['buy_csrf'] = bin2hex(random_bytes(24));
$csrf = $_SESSION['buy_csrf'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($item['title']) ?> — <?= htmlspecialchars(SITE_NAME) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root {
        --primary: #2563eb;
        --primary-light: #3b82f6;
        --primary-dark: #1e40af;
        --accent: #f59e0b;
        --success: #10b981;
        --dark: #1e293b;
        --gray: #64748b;
        --light: #f8fafc;
    }

    body {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        background: var(--light);
        color: var(--dark);
    }

    /* Breadcrumb */
    .breadcrumb-custom {
        background: white;
        padding: 20px 0;
        margin-bottom: 30px;
    }

    .breadcrumb-custom a {
        color: var(--gray);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }

    .breadcrumb-custom a:hover {
        color: var(--primary);
    }

    .breadcrumb-custom .current {
        color: var(--dark);
        font-weight: 600;
    }

    /* Main Container */
    .details-container {
        padding: 40px 0;
    }

    /* Image Section */
    .image-section {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        position: sticky;
        top: 100px;
    }

    .main-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 20px;
    }

    .status-badge {
        position: absolute;
        top: 50px;
        right: 50px;
        background: var(--success);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Info Section */
    .info-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .item-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .item-price {
        font-size: 3rem;
        font-weight: 900;
        color: var(--primary);
        margin-bottom: 20px;
        display: flex;
        align-items: baseline;
        gap: 10px;
    }

    .price-label {
        font-size: 1rem;
        color: var(--gray);
        font-weight: 600;
    }

    .item-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .meta-badge {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .meta-badge.category {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary);
    }

    .meta-badge.condition {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .divider {
        height: 2px;
        background: var(--light);
        margin: 30px 0;
    }

    .item-description {
        font-size: 1.125rem;
        line-height: 1.8;
        color: var(--gray);
        margin-bottom: 30px;
    }

    /* Buy Form Section */
    .buy-form-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h3 {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-header p {
        opacity: 0.9;
        font-size: 1rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 10px;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control-custom {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.3s;
        backdrop-filter: blur(10px);
    }

    .form-control-custom::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-control-custom:focus {
        outline: none;
        border-color: white;
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
    }

    .form-control-custom option {
        background: var(--primary-dark);
        color: white;
    }

    .btn-buy-now {
        width: 100%;
        padding: 18px;
        background: white;
        color: var(--primary);
        border: none;
        border-radius: 12px;
        font-size: 1.125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-buy-now:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        background: var(--light);
    }

    .btn-buy-now:active {
        transform: translateY(-1px);
    }

    /* Additional Info Cards */
    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-card {
        background: var(--light);
        padding: 25px;
        border-radius: 16px;
        text-align: center;
        border: 2px solid transparent;
        transition: all 0.3s;
    }

    .info-card:hover {
        border-color: var(--primary);
        transform: translateY(-3px);
    }

    .info-card-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 15px;
    }

    .info-card h4 {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .info-card p {
        color: var(--gray);
        font-size: 0.95rem;
        margin: 0;
    }

    /* Back Button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--gray);
        text-decoration: none;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 10px;
        transition: all 0.3s;
        background: white;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        background: var(--primary);
        color: white;
        transform: translateX(-5px);
    }

    /* Security Notice */
    .security-notice {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 20px;
        margin-top: 25px;
        font-size: 0.95rem;
        display: flex;
        align-items: start;
        gap: 12px;
    }

    .security-notice i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .item-title {
            font-size: 2rem;
        }

        .item-price {
            font-size: 2.5rem;
        }

        .image-section {
            position: static;
            margin-bottom: 30px;
        }

        .main-image {
            height: 350px;
        }

        .status-badge {
            top: 40px;
            right: 40px;
            padding: 10px 20px;
            font-size: 0.875rem;
        }

        .info-cards {
            grid-template-columns: 1fr;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .btn-buy-now.loading {
        pointer-events: none;
        animation: pulse 1.5s ease-in-out infinite;
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb-custom">
    <div class="container">
        <a href="../index.php">
            <i class="fas fa-home"></i> Home
        </a>
        <span class="mx-2">/</span>
        <a href="index.php">Shop</a>
        <span class="mx-2">/</span>
        <span class="current"><?= htmlspecialchars($item['title']) ?></span>
    </div>
</div>

<main class="details-container">
    <div class="container">
        <!-- Back Button -->
        <a href="index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Shop
        </a>

        <div class="row g-4">
            <!-- Image Section -->
            <div class="col-lg-5">
                <div class="image-section">
                    <?php if (!empty($item['image_path']) && file_exists(__DIR__ . '/../' . $item['image_path'])): ?>
                        <img src="../<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="main-image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x500/f8fafc/2563eb?text=<?= urlencode($item['title']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="main-image">
                    <?php endif; ?>
                    
                    <span class="status-badge">
                        <i class="fas fa-check-circle"></i>
                        Refurbished
                    </span>

                    <!-- Additional Info Cards -->
                    <div class="info-cards mt-4">
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="fas fa-shield-check"></i>
                            </div>
                            <h4>Quality Checked</h4>
                            <p>Certified by experts</p>
                        </div>
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h4>Fast Delivery</h4>
                            <p>2-3 business days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info & Form Section -->
            <div class="col-lg-7">
                <!-- Item Information -->
                <div class="info-section">
                    <h1 class="item-title"><?= htmlspecialchars($item['title']) ?></h1>
                    
                    <div class="item-meta">
                        <?php if (!empty($item['category'])): ?>
                        <span class="meta-badge category">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($item['category']) ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['condition_type'])): ?>
                        <span class="meta-badge condition">
                            <i class="fas fa-star"></i>
                            <?= ucfirst(htmlspecialchars($item['condition_type'])) ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="item-price">
                        <span class="price-label">Price:</span>
                        <?= htmlspecialchars(CURRENCY_SYMBOL) ?> <?= number_format($item['price'], 2) ?>
                    </div>

                    <div class="divider"></div>

                    <div class="item-description">
                        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; color: var(--dark);">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Description
                        </h3>
                        <?= nl2br(htmlspecialchars($item['description'] ?? 'No description available.')) ?>
                    </div>
                </div>

                <!-- Buy Form -->
                <div class="buy-form-section">
                    <div class="form-header">
                        <h3>
                            <i class="fas fa-shopping-cart"></i>
                            Purchase This Item
                        </h3>
                        <p>Fill in your details to complete your order</p>
                    </div>

                    <form method="post" action="process_buy.php" id="buyForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">

                        <div class="form-group">
                            <label>
                                <i class="fas fa-user"></i>
                                Full Name
                            </label>
                            <input type="text" name="buyer_name" required class="form-control-custom" placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" name="buyer_email" required class="form-control-custom" placeholder="you@example.com">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </label>
                            <input type="tel" name="buyer_phone" required class="form-control-custom" placeholder="+254 7XX XXX XXX">
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-credit-card"></i>
                                Payment Method
                            </label>
                            <select name="payment_method" required class="form-control-custom">
                                <option value="">Select payment method</option>
                                <option value="cod">Cash on Delivery</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-buy-now">
                            <i class="fas fa-shopping-bag"></i>
                            Buy Now - <?= htmlspecialchars(CURRENCY_SYMBOL) ?> <?= number_format($item['price'], 2) ?>
                        </button>

                        <div class="security-notice">
                            <i class="fas fa-lock"></i>
                            <div>
                                <strong>Secure Transaction</strong><br>
                                Your information is protected with industry-standard encryption.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
// Form validation and loading state
document.getElementById('buyForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('.btn-buy-now');
    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
});

// Smooth scroll to form on page load if coming from product card
if (window.location.hash === '#buy') {
    document.querySelector('.buy-form-section').scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>