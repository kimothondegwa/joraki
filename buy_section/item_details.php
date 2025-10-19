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

// Handle multiple images
$images = [];
if (!empty($item['images'])) {
    $decoded = json_decode($item['images'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $img) {
            $images[] = '../uploads/' . $img;
        }
    }
}

// Fallback to single image if no multiple images
if (empty($images) && !empty($item['image'])) {
    $images[] = '../uploads/' . $item['image'];
}

// If still no images, use placeholder
if (empty($images)) {
    $images[] = 'https://via.placeholder.com/800x600/f0f9ff/2563eb?text=' . urlencode($item['title']);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($item['title']) ?> — <?= htmlspecialchars(SITE_NAME) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1e40af;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
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
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Hero Section */
        .details-hero {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 40px 0 30px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .details-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .breadcrumb-custom a {
            color: var(--gray);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .breadcrumb-custom a:hover {
            color: var(--primary);
        }

        .breadcrumb-custom .separator {
            color: var(--gray);
            margin: 0 12px;
        }

        .breadcrumb-custom .current {
            color: var(--dark);
            font-weight: 700;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: white;
            color: var(--gray);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
            border: 2px solid #e2e8f0;
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        .btn-back:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateX(-5px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        /* Image Gallery Section */
        .gallery-section {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            position: relative;
            border: 2px solid rgba(37, 99, 235, 0.1);
        }

        .main-image-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
            margin-bottom: 20px;
        }

        .main-product-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            display: block;
            transition: transform 0.6s ease;
        }

        .main-image-container:hover .main-product-image {
            transform: scale(1.05);
        }

        .status-badge-overlay {
            position: absolute;
            top: 30px;
            right: 30px;
            background: var(--success);
            color: white;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 10;
            backdrop-filter: blur(10px);
            animation: slideInRight 0.6s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .wishlist-badge {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            z-index: 10;
            animation: slideInLeft 0.6s ease;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .wishlist-badge:hover {
            background: var(--danger);
            color: white;
            transform: scale(1.1);
        }

        .wishlist-badge i {
            font-size: 1.5rem;
        }

        /* Gallery Navigation */
        .gallery-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            pointer-events: none;
            z-index: 10;
        }

        .gallery-nav-btn {
            width: 50px;
            height: 50px;
            background: white;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            pointer-events: all;
            color: var(--dark);
            font-size: 1.25rem;
        }

        .gallery-nav-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .gallery-nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Thumbnail Gallery */
        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .thumbnail-item {
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
            position: relative;
        }

        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-item:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
        }

        .thumbnail-item.active {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
        }

        /* Product Info Section */
        .product-info-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            border: 2px solid rgba(37, 99, 235, 0.1);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-title {
            font-size: 2rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .meta-badges {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .badge-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-category {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border: 2px solid rgba(37, 99, 235, 0.2);
        }

        .badge-condition {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 2px solid rgba(16, 185, 129, 0.2);
        }

        /* Price Box */
        .price-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 35px;
            border-radius: 20px;
            text-align: center;
            margin: 30px 0;
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.3);
            position: relative;
            overflow: hidden;
        }

        .price-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .price-label {
            color: rgba(255,255,255,0.95);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .price-value {
            color: white;
            font-size: 2.5rem;
            font-weight: 900;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            z-index: 1;
        }

        .currency-symbol {
            font-size: 1.5rem;
            font-weight: 700;
        }

        /* Description */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--dark);
            margin: 40px 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--light);
        }

        .section-header i {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .description-content {
            color: var(--gray);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        /* Features Grid */
        .features-showcase {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 40px 0;
        }

        .feature-card {
            background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
            padding: 30px;
            border-radius: 16px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .feature-card:hover {
            background: white;
            border-color: var(--primary);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.15);
        }

        .feature-icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
        }

        .feature-icon-wrapper i {
            font-size: 2.5rem;
            color: white;
        }

        .feature-title {
            font-weight: 800;
            font-size: 1.125rem;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .feature-description {
            color: var(--gray);
            font-size: 0.9375rem;
        }

        /* Info List */
        .info-highlights {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 20px;
            padding: 30px;
            margin: 30px 0;
        }

        .highlight-item {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: white;
            border-radius: 14px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .highlight-item:last-child {
            margin-bottom: 0;
        }

        .highlight-item:hover {
            border-color: var(--primary);
            transform: translateX(10px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15);
        }

        .highlight-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            flex-shrink: 0;
        }

        .highlight-content h4 {
            font-size: 1.125rem;
            font-weight: 800;
            color: var(--dark);
            margin: 0 0 6px 0;
        }

        .highlight-content p {
            color: var(--gray);
            font-size: 0.9375rem;
            margin: 0;
        }

        /* Purchase Form */
        .purchase-section {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(37, 99, 235, 0.1);
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .form-heading {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .form-heading i {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .form-subheading {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 35px;
        }

        .form-group-enhanced {
            margin-bottom: 25px;
        }

        .form-label-enhanced {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .form-label-enhanced i {
            color: var(--primary);
            font-size: 1.125rem;
        }

        .required-mark {
            color: var(--danger);
            margin-left: 4px;
        }

        .form-input-enhanced,
        .form-select-enhanced {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            background: var(--light);
            transition: all 0.3s;
            color: var(--dark);
        }

        .form-input-enhanced:focus,
        .form-select-enhanced:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
        }

        .btn-checkout {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.125rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 30px;
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.35);
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 45px rgba(37, 99, 235, 0.45);
        }

        .btn-checkout:active {
            transform: translateY(-1px);
        }

        .security-notice {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid rgba(37, 99, 235, 0.2);
            border-radius: 14px;
            padding: 20px;
            margin-top: 25px;
            display: flex;
            align-items: start;
            gap: 15px;
        }

        .security-notice i {
            font-size: 1.75rem;
            color: var(--primary);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .security-notice-content {
            color: var(--primary-dark);
            font-size: 0.9375rem;
            line-height: 1.6;
        }

        .security-notice-content strong {
            display: block;
            font-size: 1.0625rem;
            margin-bottom: 4px;
        }

        /* Loading Spinner */
        .spinner-rotate {
            animation: rotate 1s linear infinite;
        }

        @keyframes rotate {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-product-image {
                height: 380px;
            }
        }

        @media (max-width: 992px) {
            .product-title {
                font-size: 1.75rem;
            }

            .price-value {
                font-size: 2.25rem;
            }

            .currency-symbol {
                font-size: 1.375rem;
            }

            .features-showcase {
                grid-template-columns: 1fr;
            }

            .main-product-image {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .product-title {
                font-size: 1.5rem;
            }

            .price-value {
                font-size: 2rem;
            }

            .currency-symbol {
                font-size: 1.25rem;
            }

            .gallery-section,
            .product-info-card,
            .purchase-section {
                padding: 25px;
            }

            .main-product-image {
                height: 300px;
            }

            .status-badge-overlay,
            .wishlist-badge {
                top: 20px;
            }

            .status-badge-overlay {
                right: 20px;
                padding: 10px 18px;
                font-size: 0.875rem;
            }

            .wishlist-badge {
                left: 20px;
                width: 50px;
                height: 50px;
            }

            .thumbnail-gallery {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
                gap: 10px;
            }

            .section-header {
                font-size: 1.125rem;
            }
        }

        @media (max-width: 576px) {
            .product-title {
                font-size: 1.375rem;
            }

            .price-value {
                font-size: 1.75rem;
            }

            .currency-symbol {
                font-size: 1.125rem;
            }

            .main-product-image {
                height: 280px;
            }

            .btn-checkout {
                font-size: 1rem;
                padding: 16px;
            }

            .form-heading {
                font-size: 1.25rem;
            }

            .details-hero {
                padding: 30px 0 20px;
            }

            .btn-back {
                padding: 10px 20px;
                font-size: 0.9375rem;
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Hero Section with Breadcrumb -->
<div class="details-hero">
    <div class="container">
        <div class="breadcrumb-custom">
            <a href="../index.php">
                <i class="fas fa-home"></i>
                Home
            </a>
            <span class="separator">›</span>
            <a href="index.php">Shop</a>
            <span class="separator">›</span>
            <span class="current"><?= htmlspecialchars(substr($item['title'], 0, 50)) ?><?= strlen($item['title']) > 50 ? '...' : '' ?></span>
        </div>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Shop
        </a>
    </div>
</div>

<main style="padding: 0 0 80px;">
    <div class="container">
        <div class="row g-4">
            <!-- Left: Image Gallery -->
            <div class="col-lg-6">
                <div class="gallery-section">
                    <div class="main-image-container">
                        <img src="<?= htmlspecialchars($images[0]) ?>" 
                             alt="<?= htmlspecialchars($item['title']) ?>" 
                             class="main-product-image"
                             id="mainImage"
                             onerror="this.src='https://via.placeholder.com/800x600/f0f9ff/2563eb?text=<?= urlencode($item['title']) ?>'">
                        
                        <span class="status-badge-overlay">
                            <i class="fas fa-check-circle"></i>
                            Refurbished
                        </span>

                        <button class="wishlist-badge" id="wishlistBtn" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>

                        <?php if (count($images) > 1): ?>
                        <div class="gallery-nav">
                            <button class="gallery-nav-btn" id="prevBtn" onclick="changeImage(-1)">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="gallery-nav-btn" id="nextBtn" onclick="changeImage(1)">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (count($images) > 1): ?>
                    <div class="thumbnail-gallery">
                        <?php foreach ($images as $index => $image): ?>
                        <div class="thumbnail-item <?= $index === 0 ? 'active' : '' ?>" 
                             onclick="selectImage(<?= $index ?>)"
                             data-index="<?= $index ?>">
                            <img src="<?= htmlspecialchars($image) ?>" 
                                 alt="Thumbnail <?= $index + 1 ?>"
                                 onerror="this.src='https://via.placeholder.com/150/f0f9ff/2563eb?text=<?= $index + 1 ?>'">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="features-showcase">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="feature-title">Quality Assured</div>
                        <div class="feature-description">Expert certified products</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="feature-title">Fast Shipping</div>
                        <div class="feature-description">2-3 business days</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <div class="feature-title">Easy Returns</div>
                        <div class="feature-description">30-day return policy</div>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="feature-title">24/7 Support</div>
                        <div class="feature-description">Always here to help</div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Details & Form -->
            <div class="col-lg-6">
                <!-- Product Info -->
                <div class="product-info-card">
                    <h1 class="product-title"><?= htmlspecialchars($item['title']) ?></h1>
                    
                    <div class="meta-badges">
                        <?php if (!empty($item['category'])): ?>
                        <span class="badge-custom badge-category">
                            <i class="fas fa-tag"></i>
                            <?= htmlspecialchars($item['category']) ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['condition_type'])): ?>
                        <span class="badge-custom badge-condition">
                            <i class="fas fa-certificate"></i>
                            <?= ucfirst(htmlspecialchars($item['condition_type'])) ?> Condition
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="price-section">
                        <div class="price-label">Your Price</div>
                        <div class="price-value">
                            <span class="currency-symbol"><?= htmlspecialchars(CURRENCY_SYMBOL) ?></span>
                            <?= number_format($item['price'], 0) ?>
                        </div>
                    </div>

                    <div class="section-header">
                        <i class="fas fa-align-left"></i>
                        <span>Product Description</span>
                    </div>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($item['description'] ?? 'No description available.')) ?>
                    </div>

                    <div class="info-highlights">
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="fas fa-award"></i>
                            </div>
                            <div class="highlight-content">
                                <h4>Warranty Included</h4>
                                <p>1-year manufacturer warranty for peace of mind</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="highlight-content">
                                <h4>Secure Payment</h4>
                                <p>All transactions are encrypted and secure</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="highlight-content">
                                <h4>Top Rated Product</h4>
                                <p>Highly rated by our customers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Form -->
                <div class="purchase-section">
                    <h2 class="form-heading">
                        <i class="fas fa-shopping-cart"></i>
                        Complete Your Purchase
                    </h2>
                    <p class="form-subheading">Fill in your details below to place your order securely</p>

                    <form method="post" action="process_buy.php" id="buyForm">
                        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">

                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-user"></i>
                                Full Name
                                <span class="required-mark">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="buyer_name" 
                                required 
                                class="form-input-enhanced" 
                                placeholder="Enter your full name">
                        </div>

                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-envelope"></i>
                                Email Address
                                <span class="required-mark">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="buyer_email" 
                                required 
                                class="form-input-enhanced" 
                                placeholder="your.email@example.com">
                        </div>

                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-phone"></i>
                                Phone Number
                                <span class="required-mark">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="buyer_phone" 
                                required 
                                class="form-input-enhanced" 
                                placeholder="+254 700 000 000">
                        </div>

                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="fas fa-wallet"></i>
                                Payment Method
                                <span class="required-mark">*</span>
                            </label>
                            <select name="payment_method" required class="form-select-enhanced">
                                <option value="">Choose your payment option</option>
                                <option value="cod">💵 Cash on Delivery</option>
                                <option value="mpesa">📱 M-Pesa</option>
                                <option value="paypal">💳 PayPal</option>
                                <option value="card">💳 Credit/Debit Card</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-checkout">
                            <i class="fas fa-lock"></i>
                            Secure Checkout — <?= htmlspecialchars(CURRENCY_SYMBOL) ?><?= number_format($item['price'], 0) ?>
                        </button>

                        <div class="security-notice">
                            <i class="fas fa-shield-check"></i>
                            <div class="security-notice-content">
                                <strong>Safe & Secure Transaction</strong>
                                Your personal information is protected with 256-bit SSL encryption. We never store your payment details.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image Gallery Functionality
const images = <?= json_encode($images) ?>;
let currentImageIndex = 0;

function changeImage(direction) {
    currentImageIndex += direction;
    
    if (currentImageIndex < 0) {
        currentImageIndex = images.length - 1;
    } else if (currentImageIndex >= images.length) {
        currentImageIndex = 0;
    }
    
    updateMainImage();
}

function selectImage(index) {
    currentImageIndex = index;
    updateMainImage();
}

function updateMainImage() {
    const mainImage = document.getElementById('mainImage');
    mainImage.style.opacity = '0';
    
    setTimeout(() => {
        mainImage.src = images[currentImageIndex];
        mainImage.style.opacity = '1';
    }, 200);
    
    // Update thumbnails
    document.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
    
    // Update navigation buttons
    updateNavigationButtons();
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn && nextBtn) {
        prevBtn.disabled = false;
        nextBtn.disabled = false;
        
        if (currentImageIndex === 0) {
            prevBtn.style.opacity = '0.5';
        } else {
            prevBtn.style.opacity = '1';
        }
        
        if (currentImageIndex === images.length - 1) {
            nextBtn.style.opacity = '0.5';
        } else {
            nextBtn.style.opacity = '1';
        }
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (images.length > 1) {
        if (e.key === 'ArrowLeft') {
            changeImage(-1);
        } else if (e.key === 'ArrowRight') {
            changeImage(1);
        }
    }
});

// Smooth transition for main image
document.getElementById('mainImage').style.transition = 'opacity 0.3s ease';

// Wishlist functionality
const wishlistBtn = document.getElementById('wishlistBtn');
wishlistBtn.addEventListener('click', function() {
    const icon = this.querySelector('i');
    
    if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        this.style.background = 'var(--danger)';
        this.style.color = 'white';
        
        // Show notification
        showNotification('Added to wishlist!', 'success');
    } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        this.style.background = 'white';
        this.style.color = '';
        
        showNotification('Removed from wishlist', 'info');
    }
});

// Form submission handling
document.getElementById('buyForm').addEventListener('submit', function(e) {
    const btn = this.querySelector('.btn-checkout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner spinner-rotate"></i> Processing Your Order...';
});

// Phone number formatting
const phoneInput = document.querySelector('input[name="buyer_phone"]');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.startsWith('254')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+254' + value.substring(1);
        } else if (value.startsWith('7') || value.startsWith('1')) {
            value = '+254' + value;
        }
        
        e.target.value = value;
    });
}

// Form validation feedback
const formInputs = document.querySelectorAll('.form-input-enhanced, .form-select-enhanced');
formInputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value.trim() === '' && this.hasAttribute('required')) {
            this.style.borderColor = 'var(--danger)';
        } else if (this.value.trim() !== '') {
            this.style.borderColor = 'var(--success)';
        }
    });
    
    input.addEventListener('focus', function() {
        this.style.borderColor = 'var(--primary)';
    });
});

// Email validation
const emailInput = document.querySelector('input[name="buyer_email"]');
if (emailInput) {
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value && !emailRegex.test(this.value)) {
            this.style.borderColor = 'var(--danger)';
            showNotification('Please enter a valid email address', 'error');
        } else if (this.value) {
            this.style.borderColor = 'var(--success)';
        }
    });
}

// Notification function
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : 'var(--primary)'};
        color: white;
        border-radius: 12px;
        font-weight: 700;
        z-index: 10000;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        animation: slideInRight 0.4s ease;
    `;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => {
            notification.remove();
        }, 400);
    }, 3000);
}

// Add notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOutRight {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
document.head.appendChild(style);

// Smooth scroll to purchase form if hash present
if (window.location.hash === '#buy') {
    setTimeout(() => {
        document.querySelector('.purchase-section').scrollIntoView({ 
            behavior: 'smooth',
            block: 'center'
        });
    }, 300);
}

// Image zoom on hover (optional)
const mainImage = document.getElementById('mainImage');
mainImage.addEventListener('mousemove', function(e) {
    const rect = this.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    
    this.style.transformOrigin = `${x}% ${y}%`;
});

// Lazy loading for thumbnails
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('.thumbnail-item img').forEach(img => {
        imageObserver.observe(img);
    });
}

// Auto-advance slideshow (optional - uncomment to enable)
/*
let autoAdvanceInterval;
function startAutoAdvance() {
    if (images.length > 1) {
        autoAdvanceInterval = setInterval(() => {
            changeImage(1);
        }, 5000);
    }
}

function stopAutoAdvance() {
    clearInterval(autoAdvanceInterval);
}

// Start auto-advance
startAutoAdvance();

// Stop on user interaction
document.querySelector('.main-image-container').addEventListener('mouseenter', stopAutoAdvance);
document.querySelector('.main-image-container').addEventListener('mouseleave', startAutoAdvance);
*/

// Initialize
updateNavigationButtons();

console.log('Item details page initialized with', images.length, 'images');
</script>

</body>
</html>