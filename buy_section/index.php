<?php
// buy/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Get filter parameters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build query
$sql = "SELECT id, title, price, image, description, category, condition_type FROM items WHERE status = 'refurbished'";
$params = [];

if (!empty($category)) {
    $sql .= " AND category = :category";
    $params[':category'] = $category;
}

if (!empty($search)) {
    $sql .= " AND (title LIKE :search OR description LIKE :search)";
    $params[':search'] = "%$search%";
}

// Add sorting
switch($sort) {
    case 'price_low':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price DESC";
        break;
    case 'oldest':
        $sql .= " ORDER BY created_at ASC";
        break;
    default: // newest
        $sql .= " ORDER BY created_at DESC";
}

// Fetch items
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $items = [];
}

// Get categories for filter
try {
    $categories = $pdo->query("SELECT DISTINCT category FROM items WHERE status = 'refurbished' AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $categories = [];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Browse Items - <?= htmlspecialchars(SITE_NAME) ?></title>
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

    /* Hero Header Section */
    .shop-hero {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 80px 0 50px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .shop-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.4;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 99, 235, 0.1);
        border: 2px solid rgba(37, 99, 235, 0.2);
        color: var(--primary);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 20px;
        animation: fadeInDown 0.6s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .shop-title {
        font-size: 3.5rem;
        font-weight: 900;
        color: var(--dark);
        margin-bottom: 20px;
        line-height: 1.2;
        animation: fadeInUp 0.6s ease;
    }

    .shop-title i {
        color: var(--primary);
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .shop-subtitle {
        font-size: 1.375rem;
        color: var(--gray);
        font-weight: 500;
        animation: fadeInUp 0.6s ease 0.2s backwards;
    }

    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 30px;
        flex-wrap: wrap;
        animation: fadeInUp 0.6s ease 0.4s backwards;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        background: var(--primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .stat-text h4 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--dark);
        margin: 0;
    }

    .stat-text p {
        font-size: 0.875rem;
        color: var(--gray);
        margin: 0;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        margin-bottom: 40px;
        border: 1px solid rgba(37, 99, 235, 0.1);
        animation: slideUp 0.6s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .filter-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--light);
    }

    .filter-header i {
        font-size: 1.5rem;
        color: var(--primary);
    }

    .filter-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 15px;
        align-items: end;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label-custom {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label-custom i {
        color: var(--primary);
        font-size: 0.875rem;
    }

    .input-group-search {
        position: relative;
    }

    .input-group-search input {
        width: 100%;
        padding: 14px 110px 14px 45px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
        font-weight: 500;
        background: var(--light);
    }

    .input-group-search input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        background: white;
    }

    .input-group-search .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
        font-size: 1.125rem;
        pointer-events: none;
    }

    .input-group-search .btn-search {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .input-group-search .btn-search:hover {
        background: var(--primary-dark);
        transform: translateY(-50%) scale(1.05);
    }

    .form-select-custom {
        padding: 14px 40px 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        background: var(--light);
        cursor: pointer;
        transition: all 0.3s;
        color: var(--dark);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
    }

    .form-select-custom:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        background-color: white;
    }

    .btn-clear-filters {
        padding: 14px 24px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        height: fit-content;
    }

    .btn-clear-filters:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    /* Active Filters Display */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 2px solid var(--light);
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(37, 99, 235, 0.1);
        border: 1px solid rgba(37, 99, 235, 0.2);
        border-radius: 10px;
        color: var(--primary);
        font-weight: 600;
        font-size: 0.875rem;
    }

    /* Results Info Bar */
    .results-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px;
        background: white;
        border-radius: 16px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
    }

    .results-count {
        font-size: 1.125rem;
        color: var(--gray);
        font-weight: 600;
    }

    .results-count strong {
        color: var(--primary);
        font-size: 1.375rem;
    }

    .view-toggle {
        display: flex;
        gap: 8px;
        background: var(--light);
        padding: 6px;
        border-radius: 10px;
    }

    .view-btn {
        padding: 8px 16px;
        border: none;
        background: transparent;
        border-radius: 8px;
        cursor: pointer;
        color: var(--gray);
        transition: all 0.3s;
        font-size: 1rem;
    }

    .view-btn.active {
        background: white;
        color: var(--primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* Products Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    /* Enhanced Product Card */
    .product-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 20px;
        padding: 2px;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s;
    }

    .product-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 60px rgba(37, 99, 235, 0.15);
    }

    .product-card:hover::before {
        opacity: 1;
    }

    .product-image-wrapper {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
        padding: 20px;
    }

    .product-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
    }

    .product-card:hover .product-image {
        transform: scale(1.08) rotate(2deg);
    }

    .status-badge {
        position: absolute;
        top: 30px;
        right: 30px;
        background: var(--success);
        color: white;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 0.875rem;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        display: flex;
        align-items: center;
        gap: 6px;
        backdrop-filter: blur(10px);
    }

    .wishlist-btn {
        position: absolute;
        top: 30px;
        left: 30px;
        width: 45px;
        height: 45px;
        background: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .wishlist-btn:hover {
        background: var(--primary);
        color: white;
        transform: scale(1.1);
    }

    .quick-view-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(37, 99, 235, 0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s;
        border-radius: 12px;
        margin: 20px;
    }

    .product-card:hover .quick-view-overlay {
        opacity: 1;
    }

    .quick-view-btn {
        background: white;
        color: var(--primary);
        padding: 16px 32px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transform: translateY(20px);
        transition: all 0.4s;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        font-size: 1.05rem;
    }

    .product-card:hover .quick-view-btn {
        transform: translateY(0);
    }

    .quick-view-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        color: var(--primary);
    }

    .product-body {
        padding: 28px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-tags {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .product-tag {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-tag.category {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary);
    }

    .product-tag.condition {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gray);
    }

    .product-title {
        font-size: 1.375rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 14px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.3s;
    }

    .product-card:hover .product-title {
        color: var(--primary);
    }

    .product-description {
        color: var(--gray);
        font-size: 0.9375rem;
        margin-bottom: 22px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 22px;
        border-top: 2px solid var(--light);
        margin-top: auto;
    }

    .price-wrapper {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .price-label {
        font-size: 0.75rem;
        color: var(--gray);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .product-price {
        font-size: 2rem;
        font-weight: 900;
        color: var(--primary);
        display: flex;
        align-items: baseline;
        gap: 4px;
    }

    .currency {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .btn-view-item {
        background: var(--primary);
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .btn-view-item:hover {
        background: var(--primary-dark);
        transform: translateX(5px);
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 120px 20px;
        background: white;
        border-radius: 20px;
        margin: 40px 0;
    }

    .empty-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
    }

    .empty-icon i {
        font-size: 4rem;
        color: var(--primary);
    }

    .empty-state h3 {
        font-size: 2.25rem;
        font-weight: 900;
        color: var(--dark);
        margin-bottom: 16px;
    }

    .empty-state p {
        font-size: 1.125rem;
        color: var(--gray);
        margin-bottom: 35px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-back-home {
        background: var(--primary);
        color: white;
        padding: 18px 45px;
        border-radius: 14px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s;
        font-size: 1.125rem;
    }

    .btn-back-home:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(37, 99, 235, 0.35);
        color: white;
    }

    /* Animations */
    @keyframes cardFadeIn {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .product-card {
        animation: cardFadeIn 0.6s ease-out backwards;
    }

    .product-card:nth-child(1) { animation-delay: 0.1s; }
    .product-card:nth-child(2) { animation-delay: 0.2s; }
    .product-card:nth-child(3) { animation-delay: 0.3s; }
    .product-card:nth-child(4) { animation-delay: 0.1s; }
    .product-card:nth-child(5) { animation-delay: 0.2s; }
    .product-card:nth-child(6) { animation-delay: 0.3s; }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
    }

    @media (max-width: 992px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }

        .hero-stats {
            gap: 25px;
        }

        .stat-item {
            flex: 1;
            min-width: 200px;
        }
    }

    @media (max-width: 768px) {
        .shop-title {
            font-size: 2.25rem;
        }

        .shop-subtitle {
            font-size: 1.125rem;
        }

        .products-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .results-bar {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .filter-section {
            padding: 20px;
        }

        .product-image {
            height: 250px;
        }

        .hero-stats {
            justify-content: center;
        }

        .input-group-search input {
            padding-right: 50px;
        }

        .input-group-search .btn-search {
            padding: 10px 16px;
        }

        .input-group-search .btn-search span {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .shop-hero {
            padding: 60px 0 40px;
        }

        .shop-title {
            font-size: 1.875rem;
        }

        .product-body {
            padding: 20px;
        }

        .product-title {
            font-size: 1.125rem;
        }

        .product-price {
            font-size: 1.625rem;
        }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Hero Section -->
<div class="shop-hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-sparkles"></i>
                <span>Premium Quality Products</span>
            </div>
            <h1 class="shop-title">
                <i class="fas fa-store"></i> Refurbished Items
            </h1>
            <p class="shop-subtitle">Discover quality products at unbeatable prices</p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-text">
                        <h4><?= count($items) ?>+</h4>
                        <p>Products Available</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="stat-text">
                        <h4>100%</h4>
                        <p>Quality Assured</p>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="stat-text">
                        <h4>Fast</h4>
                        <p>Delivery Service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="container" style="padding: 0 15px 80px;">
    
    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-sliders-h"></i>
            <h3>Filter & Search</h3>
        </div>

        <form method="GET" action="">
            <div class="filter-grid">
                <!-- Search Box -->
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="fas fa-search"></i>
                        Search Products
                    </label>
                    <div class="input-group-search">
                        <i class="search-icon fas fa-search"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by name or description..." 
                            value="<?= htmlspecialchars($search) ?>"
                            class="form-control">
                        <button type="submit" class="btn-search">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="fas fa-tag"></i>
                        Category
                    </label>
                    <select name="category" class="form-select-custom" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        <i class="fas fa-sort"></i>
                        Sort By
                    </label>
                    <select name="sort" class="form-select-custom" onchange="this.form.submit()">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <?php if (!empty($category) || !empty($search) || $sort !== 'newest'): ?>
                    <div class="form-group-custom">
                        <label class="form-label-custom" style="opacity: 0;">Clear</label>
                        <a href="index.php" class="btn-clear-filters">
                            <i class="fas fa-times-circle"></i>
                            Clear All
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Active Filters Display -->
            <?php if (!empty($category) || !empty($search)): ?>
                <div class="active-filters">
                    <span style="color: var(--gray); font-weight: 600; font-size: 0.875rem;">Active Filters:</span>
                    <?php if (!empty($category)): ?>
                        <span class="filter-tag">
                            <i class="fas fa-tag"></i>
                            Category: <?= htmlspecialchars($category) ?>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($search)): ?>
                        <span class="filter-tag">
                            <i class="fas fa-search"></i>
                            Search: "<?= htmlspecialchars($search) ?>"
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Results Info Bar -->
    <div class="results-bar">
        <div class="results-count">
            Found <strong><?= count($items) ?></strong> item<?= count($items) !== 1 ? 's' : '' ?>
            <?php if (!empty($category)): ?>
                in <strong style="color: var(--primary);"><?= htmlspecialchars($category) ?></strong>
            <?php endif; ?>
        </div>
        
        <div class="view-toggle">
            <button class="view-btn active" title="Grid View">
                <i class="fas fa-th"></i>
            </button>
            <button class="view-btn" title="List View">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <!-- Products Grid or Empty State -->
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-box-open"></i>
            </div>
            <h3>No Items Found</h3>
            <p>We couldn't find any items matching your search criteria. Try adjusting your filters or browse all products.</p>
            <a href="index.php" class="btn-back-home">
                <i class="fas fa-arrow-left"></i>
                View All Items
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($items as $index => $item): ?>
                <div class="product-card">
                    <div class="product-image-wrapper">
                        <?php 
                            $image_path = !empty($item['image']) ? '../uploads/' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/320x300/f0f9ff/2563eb?text=No+Image';
                        ?>
                        <img 
                            src="<?= $image_path ?>" 
                            alt="<?= htmlspecialchars($item['title']) ?>" 
                            class="product-image" 
                            onerror="this.src='https://via.placeholder.com/320x300/f0f9ff/2563eb?text=No+Image'">
                        
                        <span class="status-badge">
                            <i class="fas fa-check-circle"></i>
                            Refurbished
                        </span>

                        <button class="wishlist-btn" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>

                        <div class="quick-view-overlay">
                            <a href="item_details.php?id=<?= $item['id'] ?>" class="quick-view-btn">
                                <i class="fas fa-eye"></i>
                                Quick View
                            </a>
                        </div>
                    </div>

                    <div class="product-body">
                        <div class="product-tags">
                            <?php if (!empty($item['category'])): ?>
                                <span class="product-tag category">
                                    <?= htmlspecialchars($item['category']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($item['condition_type'])): ?>
                                <span class="product-tag condition">
                                    <?= ucfirst(htmlspecialchars($item['condition_type'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="product-title">
                            <?= htmlspecialchars($item['title']) ?>
                        </h3>
                        
                        <p class="product-description">
                            <?= htmlspecialchars(substr($item['description'] ?? 'No description available.', 0, 100)) ?><?= strlen($item['description'] ?? '') > 100 ? '...' : '' ?>
                        </p>

                        <div class="product-footer">
                            <div class="price-wrapper">
                                <span class="price-label">Price</span>
                                <div class="product-price">
                                    <span class="currency">KSh</span>
                                    <?= number_format($item['price'], 0) ?>
                                </div>
                            </div>
                            <a href="item_details.php?id=<?= $item['id'] ?>" class="btn-view-item">
                                View Details
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Wishlist functionality
document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const icon = this.querySelector('i');
        
        if (icon.classList.contains('far')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            this.style.background = '#ef4444';
            this.style.color = 'white';
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            this.style.background = 'white';
            this.style.color = '';
        }
    });
});

// View toggle functionality
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // You can add list view functionality here if needed
        if (this.querySelector('.fa-list')) {
            // Switch to list view
            console.log('Switching to list view');
        } else {
            // Switch to grid view
            console.log('Switching to grid view');
        }
    });
});

// Smooth scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, index * 100);
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe product cards on scroll
document.querySelectorAll('.product-card').forEach((card, index) => {
    if (index >= 6) { // Only animate cards after the first 6
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease-out';
        observer.observe(card);
    }
});

// Add loading animation to form submissions
document.querySelectorAll('form select').forEach(select => {
    select.addEventListener('change', function() {
        this.style.opacity = '0.6';
        this.style.pointerEvents = 'none';
    });
});

// Search input animation
const searchInput = document.querySelector('.input-group-search input');
if (searchInput) {
    searchInput.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
        this.parentElement.style.transition = 'transform 0.3s ease';
    });
    
    searchInput.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
    });
}
</script>

</body>
</html>