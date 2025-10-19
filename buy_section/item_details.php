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

// Fix image path - check both 'image' and 'image_path' fields
$imagePath = '';
if (!empty($item['image'])) {
    // Use the 'image' field (same as index.php)
    $imagePath = '../uploads/' . htmlspecialchars($item['image']);
} elseif (!empty($item['image_path'])) {
    // Fallback to 'image_path' field if it exists
    $cleanPath = ltrim($item['image_path'], './');
    $cleanPath = str_replace('../', '', $cleanPath);
    
    if (file_exists(__DIR__ . '/../' . $cleanPath)) {
        $imagePath = '../' . $cleanPath;
    } elseif (file_exists(__DIR__ . '/' . $cleanPath)) {
        $imagePath = $cleanPath;
    }
}
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

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f1f5f9;
        color: var(--dark);
        line-height: 1.6;
    }

    /* Hero Section with Image */
    .product-hero {
        background: linear-gradient(to bottom, white 0%, #f8fafc 100%);
        padding: 2rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .breadcrumb-nav {
        background: transparent;
        padding: 1rem 0;
        margin: 0;
    }

    .breadcrumb {
        background: transparent;
        margin: 0;
        padding: 0;
    }

    .breadcrumb-item a {
        color: var(--gray);
        text-decoration: none;
        transition: color 0.2s;
    }

    .breadcrumb-item a:hover {
        color: var(--primary);
    }

    .breadcrumb-item.active {
        color: var(--dark);
        font-weight: 600;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: var(--gray);
    }

    /* Image Gallery */
    .image-gallery {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        position: relative;
        margin-bottom: 2rem;
    }

    .main-product-image {
        width: 100%;
        height: 550px;
        object-fit: cover;
        border-radius: 12px;
        background: #f8fafc;
        display: block;
    }

    .status-ribbon {
        position: absolute;
        top: 3rem;
        left: 3rem;
        background: var(--success);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        z-index: 10;
    }

    /* Product Info Section */
    .product-info-section {
        background: white;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    .product-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .product-name {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .meta-tags {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .tag-category {
        background: #dbeafe;
        color: #1e40af;
    }

    .tag-condition {
        background: #d1fae5;
        color: #065f46;
    }

    .price-box {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 2rem;
        border-radius: 12px;
        margin: 1.5rem 0;
        text-align: center;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
    }

    .price-label {
        color: rgba(255,255,255,0.9);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .price-amount {
        color: white;
        font-size: 3.5rem;
        font-weight: 900;
        line-height: 1;
    }

    .description-section {
        margin-top: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary);
    }

    .description-text {
        color: var(--gray);
        font-size: 1rem;
        line-height: 1.8;
    }

    /* Features Grid */
    .features-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-top: 2rem;
    }

    .feature-box {
        background: #f8fafc;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .feature-box:hover {
        background: white;
        border-color: var(--primary);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .feature-icon {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 0.75rem;
    }

    .feature-name {
        font-weight: 700;
        color: var(--dark);
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .feature-desc {
        color: var(--gray);
        font-size: 0.875rem;
    }

    /* Purchase Form */
    .purchase-card {
        background: white;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border: 2px solid #e2e8f0;
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-subtitle {
        color: var(--gray);
        margin-bottom: 2rem;
    }

    .input-group-custom {
        margin-bottom: 1.5rem;
    }

    .input-label {
        display: block;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .input-label i {
        color: var(--primary);
        margin-right: 0.5rem;
        width: 20px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
        background: white;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .btn-purchase {
        width: 100%;
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.125rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1.5rem;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
    }

    .btn-purchase:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.4);
    }

    .btn-purchase:active {
        transform: translateY(0);
    }

    .security-note {
        background: #f0f9ff;
        border: 2px solid #bae6fd;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1.5rem;
        display: flex;
        align-items: start;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: var(--primary-dark);
    }

    .security-note i {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }

    /* Info List */
    .info-list {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--primary);
        flex-shrink: 0;
    }

    .info-content h5 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .info-content p {
        color: var(--gray);
        font-size: 0.875rem;
        margin: 0;
    }

    /* Back Button */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: white;
        color: var(--gray);
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        border: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .btn-back:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: translateX(-4px);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .product-name {
            font-size: 1.75rem;
        }

        .price-amount {
            font-size: 2.5rem;
        }

        .main-product-image {
            height: 400px;
        }

        .status-ribbon {
            top: 2rem;
            left: 2rem;
            font-size: 0.875rem;
            padding: 0.625rem 1.25rem;
        }
    }

    @media (max-width: 576px) {
        .product-name {
            font-size: 1.5rem;
        }

        .price-amount {
            font-size: 2rem;
        }

        .main-product-image {
            height: 300px;
        }

        .features-grid {
            grid-template-columns: 1fr;
        }

        .product-info-section,
        .purchase-card,
        .image-gallery {
            padding: 1.5rem;
        }

        .status-ribbon {
            top: 1.5rem;
            left: 1.5rem;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
        }
    }

    .loading-spinner {
        animation: rotate 1s linear infinite;
    }

    @keyframes rotate {
        to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="product-hero">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="index.php">Shop</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($item['title']) ?>
                </li>
            </ol>
        </nav>
    </div>
</div>

<main style="padding: 2rem 0 4rem;">
    <div class="container">
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Shop
        </a>

        <div class="row g-4">
            <!-- Left: Image Gallery -->
            <div class="col-lg-6">
                <div class="image-gallery">
                    <span class="status-ribbon">
                        <i class="fas fa-check-circle me-2"></i>Refurbished
                    </span>
                    <?php if (!empty($imagePath)): ?>
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>" 
                             class="main-product-image"
                             onerror="this.src='https://via.placeholder.com/600x550/e2e8f0/2563eb?text=<?= urlencode($item['title']) ?>'">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/600x550/e2e8f0/2563eb?text=<?= urlencode($item['title']) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>" 
                             class="main-product-image">
                    <?php endif; ?>
                </div>

                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-name">Quality Assured</div>
                        <div class="feature-desc">Expert certified</div>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="feature-name">Fast Shipping</div>
                        <div class="feature-desc">2-3 business days</div>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <div class="feature-name">Easy Returns</div>
                        <div class="feature-desc">30-day policy</div>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="feature-name">24/7 Support</div>
                        <div class="feature-desc">Always available</div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Details & Form -->
            <div class="col-lg-6">
                <!-- Product Info -->
                <div class="product-info-section">
                    <div class="product-header">
                        <h1 class="product-name"><?= htmlspecialchars($item['title']) ?></h1>
                        <div class="meta-tags">
                            <?php if (!empty($item['category'])): ?>
                            <span class="tag tag-category">
                                <i class="fas fa-tag"></i>
                                <?= htmlspecialchars($item['category']) ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['condition_type'])): ?>
                            <span class="tag tag-condition">
                                <i class="fas fa-certificate"></i>
                                <?= ucfirst(htmlspecialchars($item['condition_type'])) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="price-box">
                        <div class="price-label">Price</div>
                        <div class="price-amount">
                            <?= htmlspecialchars(CURRENCY_SYMBOL) ?><?= number_format($item['price'], 2) ?>
                        </div>
                    </div>

                    <div class="description-section">
                        <h3 class="section-title">
                            <i class="fas fa-align-left"></i>
                            Product Description
                        </h3>
                        <div class="description-text">
                            <?= nl2br(htmlspecialchars($item['description'] ?? 'No description available.')) ?>
                        </div>
                    </div>

                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="info-content">
                                <h5>Warranty Included</h5>
                                <p>1-year manufacturer warranty</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="info-content">
                                <h5>Secure Payment</h5>
                                <p>All transactions are encrypted</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="info-content">
                                <h5>Top Rated</h5>
                                <p>Highly rated by customers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Form -->
                <div class="purchase-card">
                    <h2 class="form-title">
                        <i class="fas fa-shopping-cart"></i>
                        Complete Your Purchase
                    </h2>
                    <p class="form-subtitle">Enter your details below to place your order</p>

                    <form method="post" action="process_buy.php" id="buyForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">

                        <div class="input-group-custom">
                            <label class="input-label">
                                <i class="fas fa-user"></i>
                                Full Name
                            </label>
                            <input type="text" name="buyer_name" required class="form-input" placeholder="John Doe">
                        </div>

                        <div class="input-group-custom">
                            <label class="input-label">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" name="buyer_email" required class="form-input" placeholder="john@example.com">
                        </div>

                        <div class="input-group-custom">
                            <label class="input-label">
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </label>
                            <input type="tel" name="buyer_phone" required class="form-input" placeholder="+254 700 000 000">
                        </div>

                        <div class="input-group-custom">
                            <label class="input-label">
                                <i class="fas fa-wallet"></i>
                                Payment Method
                            </label>
                            <select name="payment_method" required class="form-select">
                                <option value="">Choose payment option</option>
                                <option value="cod">Cash on Delivery</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="paypal">PayPal</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-purchase">
                            <i class="fas fa-lock"></i>
                            Secure Checkout - <?= htmlspecialchars(CURRENCY_SYMBOL) ?><?= number_format($item['price'], 2) ?>
                        </button>

                        <div class="security-note">
                            <i class="fas fa-shield-check"></i>
                            <div>
                                <strong>Safe & Secure</strong><br>
                                Your data is protected with 256-bit SSL encryption. We never store your payment information.
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
document.getElementById('buyForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('.btn-purchase');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner loading-spinner"></i> Processing Order...';
});

// Smooth scroll if hash present
if (window.location.hash === '#buy') {
    setTimeout(() => {
        document.querySelector('.purchase-card').scrollIntoView({ 
            behavior: 'smooth',
            block: 'center'
        });
    }, 100);
}

// Image error handling
document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
        console.log('Image failed to load:', this.src);
    });
});
</script>

</body>
</html>