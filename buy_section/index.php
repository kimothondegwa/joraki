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

    /* Header Section */
    .shop-header {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        padding: 60px 0 40px;
        margin-bottom: 40px;
    }

    .shop-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 15px;
    }

    .shop-subtitle {
        font-size: 1.25rem;
        color: var(--gray);
    }

    /* Filter Section */
    .filter-section {
        background: white;
        padding: 25px 30px;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .filter-row {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 14px 45px 14px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .search-box button {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .search-box button:hover {
        background: var(--primary-dark);
    }

    .filter-select {
        padding: 14px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all 0.3s;
        min-width: 200px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: var(--primary);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .clear-filters {
        padding: 10px 20px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .clear-filters:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }

    /* Results Info */
    .results-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 0 5px;
    }

    .results-count {
        font-size: 1.125rem;
        color: var(--gray);
        font-weight: 600;
    }

    .results-count strong {
        color: var(--dark);
    }

    /* Product Grid */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    /* Product Card */
    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        border-color: var(--primary);
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    }

    .product-image-container {
        position: relative;
        overflow: hidden;
        background: var(--light);
    }

    .product-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--success);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .quick-view-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        background: white;
        color: var(--primary);
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        opacity: 0;
        transition: all 0.4s;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .product-card:hover .quick-view-btn {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .product-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-meta {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .meta-tag {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .meta-tag.category {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary);
    }

    .meta-tag.condition {
        background: rgba(100, 116, 139, 0.1);
        color: var(--gray);
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-description {
        color: var(--gray);
        font-size: 0.95rem;
        margin-bottom: 20px;
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
        padding-top: 20px;
        border-top: 2px solid var(--light);
        margin-top: auto;
    }

    .product-price {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--primary);
    }

    .product-price-label {
        font-size: 0.75rem;
        color: var(--gray);
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .buy-btn {
        background: var(--primary);
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .buy-btn:hover {
        background: var(--primary-dark);
        transform: translateX(5px);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 100px 20px;
    }

    .empty-state i {
        font-size: 6rem;
        color: #cbd5e1;
        margin-bottom: 30px;
    }

    .empty-state h3 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 15px;
    }

    .empty-state p {
        font-size: 1.125rem;
        color: var(--gray);
        margin-bottom: 30px;
    }

    .back-home-btn {
        background: var(--primary);
        color: white;
        padding: 16px 40px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .back-home-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
        color: white;
    }

    /* Loading Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card {
        animation: fadeIn 0.5s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .shop-title {
            font-size: 2rem;
        }

        .filter-row {
            flex-direction: column;
        }

        .search-box,
        .filter-select {
            width: 100%;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .results-info {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
    }
  </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Shop Header -->
<div class="shop-header">
    <div class="container">
        <h1 class="shop-title">
            <i class="fas fa-store me-3"></i>Refurbished Items
        </h1>
        <p class="shop-subtitle">Discover quality products at unbeatable prices</p>
    </div>
</div>

<main class="container" style="padding: 0 15px 60px;">
    
    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="">
            <div class="filter-row">
                <!-- Search Box -->
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search items..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Category Filter -->
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Sort Filter -->
                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>

                <!-- Active Filters -->
                <?php if (!empty($category) || !empty($search)): ?>
                    <a href="index.php" class="clear-filters">
                        <i class="fas fa-times me-2"></i>Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Results Info -->
    <div class="results-info">
        <div class="results-count">
            Found <strong><?= count($items) ?></strong> item<?= count($items) !== 1 ? 's' : '' ?>
            <?php if (!empty($category)): ?>
                in <strong><?= htmlspecialchars($category) ?></strong>
            <?php endif; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No Items Found</h3>
            <p>We couldn't find any items matching your search. Try adjusting your filters.</p>
            <a href="index.php" class="back-home-btn">
                <i class="fas fa-arrow-left"></i>
                View All Items
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($items as $item): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <?php 
                            $image_path = !empty($item['image']) ? '../uploads/' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/300x280/f8fafc/2563eb?text=No+Image';
                        ?>
                        <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="product-image" onerror="this.src='https://via.placeholder.com/300x280/f8fafc/2563eb?text=No+Image'">
                        
                        <span class="product-badge">
                            <i class="fas fa-check-circle"></i>
                            Refurbished
                        </span>

                        <a href="item_details.php?id=<?= $item['id'] ?>" class="quick-view-btn">
                            <i class="fas fa-eye me-2"></i>Quick View
                        </a>
                    </div>

                    <div class="product-content">
                        <div class="product-meta">
                            <?php if (!empty($item['category'])): ?>
                                <span class="meta-tag category"><?= htmlspecialchars($item['category']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($item['condition_type'])): ?>
                                <span class="meta-tag condition"><?= ucfirst(htmlspecialchars($item['condition_type'])) ?></span>
                            <?php endif; ?>
                        </div>

                        <h3 class="product-title"><?= htmlspecialchars($item['title']) ?></h3>
                        
                        <p class="product-description">
                            <?= htmlspecialchars(substr($item['description'] ?? 'No description available.', 0, 100)) ?>...
                        </p>

                        <div class="product-footer">
                            <div>
                                <span class="product-price-label">Price</span>
                                <div class="product-price">KSh <?= number_format($item['price'], 0) ?></div>
                            </div>
                            <a href="item_details.php?id=<?= $item['id'] ?>" class="buy-btn">
                                View
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

<script>
// Animate cards on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, index * 100);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.product-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease-out';
    observer.observe(card);
});
</script>

</body>
</html>