<?php
// index.php - Joraki Homepage Premium Modern Design
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Fetch latest refurbished items (limit 6)
try {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE status = 'refurbished' ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $featured_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $featured_items = [];
}

// Get stats
try {
    $total_sold = $pdo->query("SELECT COUNT(*) FROM items WHERE status = 'sold'")->fetchColumn();
    $active_listings = $pdo->query("SELECT COUNT(*) FROM items WHERE status = 'refurbished'")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch(PDOException $e) {
    $total_sold = 0;
    $active_listings = 0;
    $total_users = 0;
}

$page_title = "Home - Buy & Sell Refurbished Items";
include __DIR__ . '/includes/header.php';
?>

<style>
/* Premium Modern Design System - Original Joraki Colors */
:root {
    --primary: #2563eb;
    --primary-light: #3b82f6;
    --primary-dark: #1e40af;
    --accent: #f59e0b;
    --success: #10b981;
    --dark: #1e293b;
    --gray: #64748b;
    --light: #f8fafc;
    --white: #ffffff;
    --gradient-1: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    --gradient-2: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    --gradient-3: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: var(--dark);
    line-height: 1.6;
    overflow-x: hidden;
    background: var(--white);
}

/* Animated Background Blobs */
.hero-section {
    position: relative;
    padding: 140px 0 100px;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    overflow: hidden;
}

.hero-section::before,
.hero-section::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.15;
    animation: float 20s infinite ease-in-out;
}

.hero-section::before {
    width: 500px;
    height: 500px;
    background: var(--gradient-1);
    top: -200px;
    right: -100px;
}

.hero-section::after {
    width: 400px;
    height: 400px;
    background: var(--gradient-3);
    bottom: -150px;
    left: -100px;
    animation-delay: -10s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 24px;
    animation: slideDown 0.8s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.hero-title {
    font-size: 4rem;
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 24px;
    background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: fadeInUp 1s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.hero-text {
    font-size: 1.25rem;
    color: var(--gray);
    margin-bottom: 40px;
    max-width: 600px;
    line-height: 1.8;
    animation: fadeInUp 1.2s ease;
}

/* Premium Buttons with Hover Effects */
.btn-hero-primary {
    background: var(--gradient-1);
    color: white;
    padding: 18px 40px;
    border-radius: 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-hero-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.btn-hero-primary:hover::before {
    left: 100%;
}

.btn-hero-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 50px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-hero-secondary {
    background: white;
    color: var(--primary);
    padding: 18px 40px;
    border-radius: 16px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.4s ease;
    border: 2px solid rgba(99, 102, 241, 0.2);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.btn-hero-secondary:hover {
    background: var(--light);
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-color: var(--primary);
}

/* 3D Floating Hero Image */
.hero-image-wrapper {
    position: relative;
    animation: floatImage 6s ease-in-out infinite;
}

@keyframes floatImage {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.hero-image {
    max-width: 100%;
    filter: drop-shadow(0 20px 60px rgba(0, 0, 0, 0.15));
}

/* Glassmorphism Stats */
.stats-section {
    padding: 0;
    margin-top: -60px;
    position: relative;
    z-index: 10;
}

.stat-card-glass {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    padding: 50px 30px;
    border-radius: 24px;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card-glass::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--gradient-1);
    transform: scaleX(0);
    transition: transform 0.5s ease;
}

.stat-card-glass:hover::before {
    transform: scaleX(1);
}

.stat-card-glass:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
    background: rgba(255, 255, 255, 1);
}

.stat-icon-modern {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    position: relative;
}

.stat-icon-modern.gradient-1 {
    background: var(--gradient-1);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.stat-icon-modern.gradient-2 {
    background: var(--gradient-2);
    box-shadow: 0 10px 30px rgba(245, 87, 108, 0.3);
}

.stat-icon-modern.gradient-3 {
    background: var(--gradient-3);
    box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
}

.stat-number-modern {
    font-size: 3.5rem;
    font-weight: 900;
    background: var(--gradient-1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 12px;
}

.stat-label-modern {
    font-size: 1.125rem;
    color: var(--gray);
    font-weight: 600;
}

/* Modern Section Headers */
.section-modern {
    padding: 120px 0;
}

.section-header-modern {
    text-align: center;
    margin-bottom: 80px;
}

.section-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 16px;
}

.section-title-modern {
    font-size: 3rem;
    font-weight: 900;
    color: var(--dark);
    margin-bottom: 16px;
    line-height: 1.2;
}

.section-subtitle-modern {
    font-size: 1.25rem;
    color: var(--gray);
    max-width: 700px;
    margin: 0 auto;
}

/* Premium Process Cards with Icons */
.process-card-modern {
    background: white;
    padding: 50px 35px;
    border-radius: 24px;
    text-align: center;
    height: 100%;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.process-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--gradient-1);
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: 0;
}

.process-card-modern:hover::before {
    opacity: 1;
}

.process-card-modern:hover {
    transform: translateY(-15px);
    border-color: var(--primary);
    box-shadow: 0 25px 60px rgba(99, 102, 241, 0.25);
}

.process-card-modern:hover * {
    color: white !important;
    position: relative;
    z-index: 1;
}

.process-number-modern {
    width: 60px;
    height: 60px;
    background: var(--gradient-1);
    color: white;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    font-weight: 900;
    margin: 0 auto 28px;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
    position: relative;
    z-index: 1;
}

.process-icon-modern {
    font-size: 3.5rem;
    color: var(--primary);
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.process-card-modern h4 {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 16px;
    position: relative;
    z-index: 1;
}

.process-card-modern p {
    color: var(--gray);
    font-size: 1rem;
    line-height: 1.7;
    position: relative;
    z-index: 1;
}

/* Premium Product Cards with Gradient Borders */
.product-card-modern {
    background: white;
    border-radius: 24px;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    position: relative;
}

.product-card-modern::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 24px;
    padding: 2px;
    background: var(--gradient-1);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.product-card-modern:hover::before {
    opacity: 1;
}

.product-card-modern:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15);
}

.product-image-modern {
    width: 100%;
    height: 300px;
    object-fit: cover;
    background: var(--light);
    transition: transform 0.5s ease;
}

.product-card-modern:hover .product-image-modern {
    transform: scale(1.05);
}

.product-content-modern {
    padding: 32px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-badges-modern {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.badge-modern {
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-gradient-1 {
    background: var(--gradient-1);
    color: white;
}

.badge-gradient-2 {
    background: var(--gradient-2);
    color: white;
}

.badge-gradient-3 {
    background: var(--gradient-3);
    color: white;
}

.product-title-modern {
    font-size: 1.375rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 14px;
    line-height: 1.3;
}

.product-description-modern {
    color: var(--gray);
    font-size: 1rem;
    margin-bottom: 24px;
    flex-grow: 1;
    line-height: 1.6;
}

.product-footer-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 24px;
    border-top: 2px solid var(--light);
}

.product-price-modern {
    font-size: 2rem;
    font-weight: 900;
    background: var(--gradient-1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.btn-product-modern {
    background: var(--gradient-1);
    color: white;
    padding: 14px 32px;
    border-radius: 14px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.4s ease;
    border: none;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
}

.btn-product-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(99, 102, 241, 0.4);
    color: white;
}

/* Features with Icons */
.features-modern {
    background: var(--dark);
    color: white;
    position: relative;
    overflow: hidden;
}

.features-modern::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    background: var(--gradient-1);
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.1;
    top: -300px;
    right: -300px;
}

.feature-card-modern {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    padding: 45px 35px;
    border-radius: 24px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.5s ease;
    text-align: center;
    height: 100%;
}

.feature-card-modern:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-10px);
    border-color: rgba(255, 255, 255, 0.2);
}

.feature-icon-modern {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    margin: 0 auto 24px;
}

.feature-card-modern h5 {
    font-size: 1.375rem;
    font-weight: 800;
    margin-bottom: 16px;
}

.feature-card-modern p {
    opacity: 0.9;
    line-height: 1.7;
}

/* CTA Section with Gradient */
.cta-modern {
    padding: 120px 0;
    background: var(--gradient-1);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.cta-modern::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    filter: blur(80px);
    top: -250px;
    left: -250px;
}

.cta-title-modern {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 24px;
    position: relative;
}

.cta-text-modern {
    font-size: 1.375rem;
    margin-bottom: 48px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0.95;
    position: relative;
}

.btn-cta-white {
    background: white;
    color: var(--primary);
    padding: 20px 50px;
    border-radius: 16px;
    font-weight: 800;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.4s ease;
    border: none;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
    font-size: 1.125rem;
}

.btn-cta-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    color: var(--primary);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .section-title-modern { font-size: 2rem; }
    .cta-title-modern { font-size: 2.25rem; }
    .stat-number-modern { font-size: 2.5rem; }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-star"></i>
                        <span>Trusted by 10,000+ Customers</span>
                    </div>
                    <h1 class="hero-title">
                        Discover Quality Refurbished Items
                    </h1>
                    <p class="hero-text">
                        Buy and sell professionally refurbished products with complete confidence. Premium quality, unbeatable prices, sustainable shopping for a better tomorrow.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="buy_section/index.php" class="btn-hero-primary">
                            <i class="fas fa-shopping-cart"></i>
                            Explore Marketplace
                        </a>
                        <a href="sell_section/index.php" class="btn-hero-secondary">
                            <i class="fas fa-tag"></i>
                            Sell Now
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <img src="assets/images/hero-illustration.svg" alt="Joraki" class="hero-image" onerror="this.src='https://via.placeholder.com/600x400/667eea/ffffff?text=Premium+Marketplace'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card-glass">
                    <div class="stat-icon-modern gradient-1">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number-modern"><?php echo number_format($total_sold); ?>+</div>
                    <div class="stat-label-modern">Items Successfully Sold</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-glass">
                    <div class="stat-icon-modern gradient-2">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-number-modern"><?php echo number_format($active_listings); ?></div>
                    <div class="stat-label-modern">Active Listings</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-glass">
                    <div class="stat-icon-modern gradient-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number-modern"><?php echo number_format($total_users); ?>+</div>
                    <div class="stat-label-modern">Happy Customers</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section-modern">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-badge">
                <i class="fas fa-rocket"></i>
                <span>Simple Process</span>
            </div>
            <h2 class="section-title-modern">How Joraki Works</h2>
            <p class="section-subtitle-modern">From submission to sale in 4 easy steps</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="process-card-modern">
                    <div class="process-number-modern">1</div>
                    <div class="process-icon-modern"><i class="fas fa-upload"></i></div>
                    <h4>Submit Item</h4>
                    <p>Upload photos and details through our simple form</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card-modern">
                    <div class="process-number-modern">2</div>
                    <div class="process-icon-modern"><i class="fas fa-clipboard-check"></i></div>
                    <h4>Quick Review</h4>
                    <p>Our team reviews within 24-48 hours</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card-modern">
                    <div class="process-number-modern">3</div>
                    <div class="process-icon-modern"><i class="fas fa-tools"></i></div>
                    <h4>Professional Refurbishment</h4>
                    <p>Expert certification and quality check</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card-modern">
                    <div class="process-number-modern">4</div>
                    <div class="process-icon-modern"><i class="fas fa-shopping-bag"></i></div>
                    <h4>Ready to Sell</h4>
                    <p>Listed at competitive market prices</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section-modern" style="background: var(--light);">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-badge">
                <i class="fas fa-fire"></i>
                <span>Hot Deals</span>
            </div>
            <h2 class="section-title-modern">Recently Refurbished</h2>
            <p class="section-subtitle-modern">Premium quality products at unbeatable prices</p>
        </div>
        
        <?php if (!empty($featured_items)): ?>
        <div class="row g-4 mb-5">
            <?php foreach ($featured_items as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card-modern">
                    <?php $image_path = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : 'assets/images/placeholder.jpg'; ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="product-image-modern" onerror="this.src='https://via.placeholder.com/400x300/667eea/ffffff?text=Product'">
                    
                    <div class="product-content-modern">
                        <div class="product-badges-modern">
                            <span class="badge-modern badge-gradient-1">Refurbished</span>
                            <span class="badge-modern badge-gradient-2"><?php echo htmlspecialchars($item['category']); ?></span>
                        </div>
                        
                        <h3 class="product-title-modern"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="product-description-modern"><?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...</p>
                        
                        <div class="product-footer-modern">
                            <div class="product-price-modern">KSh <?php echo number_format($item['price'], 0); ?></div>
                            <a href="buy_section/index.php<?php echo $item['id']; ?>" class="btn-product-modern">
                                View <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="buy_section/index.php" class="btn-hero-primary">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box-open" style="font-size: 5rem; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h4 style="color: var(--gray);">No items available</h4>
            <p style="color: var(--gray);">Check back soon for amazing deals!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="section-modern features-modern">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-badge">
                <i class="fas fa-award"></i>
                <span>Why Choose Us</span>
            </div>
            <h2 class="section-title-modern text-white">Why Choose Joraki?</h2>
            <p class="section-subtitle-modern text-white" style="opacity: 0.9;">Your trusted partner for quality refurbished items</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Secure Transactions</h5>
                    <p>All payments processed securely with full buyer protection and encryption.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h5>Quality Guaranteed</h5>
                    <p>Professional inspection and refurbishment by certified technicians.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h5>Best Prices</h5>
                    <p>Fair pricing for sellers, unbeatable deals for buyers. Win-win.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon-modern">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h5>Eco-Friendly</h5>
                    <p>Sustainable shopping that reduces waste and protects our planet.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section-modern">
    <div class="container">
        <div class="section-header-modern">
            <div class="section-badge">
                <i class="fas fa-th"></i>
                <span>Browse</span>
            </div>
            <h2 class="section-title-modern">Shop by Category</h2>
            <p class="section-subtitle-modern">Find exactly what you're looking for</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/index.php?category=Electronics" class="process-card-modern" style="text-decoration: none;">
                    <div class="process-icon-modern">💻</div>
                    <h4>Electronics</h4>
                    <p>Laptops, phones, tablets & more</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/index.php?category=Furniture" class="process-card-modern" style="text-decoration: none;">
                    <div class="process-icon-modern">🛋️</div>
                    <h4>Furniture</h4>
                    <p>Chairs, tables, storage & more</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/index.php?category=Appliances" class="process-card-modern" style="text-decoration: none;">
                    <div class="process-icon-modern">🏠</div>
                    <h4>Appliances</h4>
                    <p>Kitchen, laundry & home essentials</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/index.php?category=Other" class="process-card-modern" style="text-decoration: none;">
                    <div class="process-icon-modern">📦</div>
                    <h4>Other Items</h4>
                    <p>Tools, sports, books & more</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-modern">
    <div class="container">
        <h2 class="cta-title-modern">Ready to Get Started?</h2>
        <p class="cta-text-modern">Join thousands of satisfied customers buying and selling refurbished items with complete confidence and peace of mind.</p>
        <div class="d-flex flex-wrap gap-4 justify-content-center">
            <a href="sell_section/index.php" class="btn-cta-white">
                <i class="fas fa-tag"></i>
                Sell Your Item
            </a>
            <a href="buy_section/index.php" class="btn-cta-white">
                <i class="fas fa-shopping-cart"></i>
                Start Shopping
            </a>
        </div>
    </div>
</section>

<script>
// Counter animation with intersection observer
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 60;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = Math.floor(target).toLocaleString() + (element.textContent.includes('+') ? '+' : '');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 25);
}

// Trigger animations on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
            const numberElement = entry.target.querySelector('.stat-number-modern');
            if (numberElement) {
                const target = parseInt(numberElement.textContent.replace(/[^0-9]/g, ''));
                animateCounter(numberElement, target);
                entry.target.dataset.animated = 'true';
            }
        }
    });
}, { threshold: 0.3 });

document.querySelectorAll('.stat-card-glass').forEach(card => observer.observe(card));

// Smooth scroll for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<!-- ============================================ -->
<!-- FLOATING CHATBOT BUTTON - Premium Design -->
<!-- ============================================ -->

<style>
/* Premium Chatbot Button */
.chatbot-float-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 70px;
    height: 70px;
    background: var(--gradient-1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 10px 40px rgba(99, 102, 241, 0.5);
    z-index: 9999;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
}

.chatbot-float-btn:hover {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 15px 50px rgba(99, 102, 241, 0.6);
}

.chatbot-float-btn i {
    font-size: 2rem;
    color: white;
}

.chatbot-float-btn .close-icon {
    display: none;
}

.chatbot-float-btn.active .chat-icon {
    display: none;
}

.chatbot-float-btn.active .close-icon {
    display: block;
}

.chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 26px;
    height: 26px;
    background: var(--gradient-2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
    color: white;
    border: 3px solid white;
    animation: bounceNotif 2s infinite;
}

@keyframes bounceNotif {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.25); }
}

/* Premium Chatbot Window */
.chatbot-window {
    position: fixed;
    bottom: 120px;
    right: 30px;
    width: 420px;
    max-height: 650px;
    background: white;
    border-radius: 28px;
    box-shadow: 0 20px 80px rgba(0, 0, 0, 0.25);
    z-index: 9998;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUpChat 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(99, 102, 241, 0.1);
}

.chatbot-window.active {
    display: flex;
}

@keyframes slideUpChat {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.chat-header {
    background: var(--gradient-1);
    color: white;
    padding: 25px 30px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.chat-avatar {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    backdrop-filter: blur(10px);
}

.chat-header-info h3 {
    font-size: 1.25rem;
    font-weight: 800;
    margin: 0;
}

.chat-status-online {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    opacity: 0.95;
}

.status-dot {
    width: 10px;
    height: 10px;
    background: #10b981;
    border-radius: 50%;
    animation: pulseStatus 2s infinite;
}

@keyframes pulseStatus {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
}

.chat-body {
    flex: 1;
    padding: 25px;
    overflow-y: auto;
    background: #f8fafc;
    max-height: 450px;
}

.chat-body::-webkit-scrollbar {
    width: 8px;
}

.chat-body::-webkit-scrollbar-track {
    background: #e2e8f0;
    border-radius: 10px;
}

.chat-body::-webkit-scrollbar-thumb {
    background: var(--gradient-1);
    border-radius: 10px;
}

.chat-message {
    margin-bottom: 18px;
    animation: fadeInMessage 0.4s ease;
}

@keyframes fadeInMessage {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-bubble {
    display: inline-block;
    padding: 14px 20px;
    border-radius: 20px;
    max-width: 80%;
    word-wrap: break-word;
    font-size: 0.975rem;
    line-height: 1.6;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.message-user {
    text-align: right;
}

.message-user .message-bubble {
    background: var(--gradient-1);
    color: white;
    border-bottom-right-radius: 6px;
}

.message-bot {
    text-align: left;
}

.message-bot .message-bubble {
    background: white;
    color: var(--dark);
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 6px;
}

.message-time {
    font-size: 0.7rem;
    opacity: 0.6;
    margin-top: 6px;
    font-weight: 500;
}

.chat-welcome {
    text-align: center;
    padding: 40px 20px;
    color: var(--gray);
}

.chat-welcome i {
    font-size: 4rem;
    background: var(--gradient-1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
    opacity: 0.6;
}

.chat-welcome h4 {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 10px;
}

.chat-welcome p {
    font-size: 0.975rem;
    margin: 0;
}

.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 18px 25px 0;
    background: #f8fafc;
}

.quick-reply-btn {
    padding: 10px 18px;
    background: white;
    border: 2px solid var(--primary);
    border-radius: 24px;
    color: var(--primary);
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.quick-reply-btn:hover {
    background: var(--gradient-1);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(99, 102, 241, 0.3);
}

.typing-indicator {
    display: none;
    padding: 14px 20px;
    background: white;
    border-radius: 20px;
    border-bottom-left-radius: 6px;
    width: fit-content;
    border: 1px solid #e2e8f0;
}

.typing-indicator.active {
    display: inline-block;
}

.typing-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: var(--primary);
    border-radius: 50%;
    margin: 0 3px;
    animation: typingAnim 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typingAnim {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
    30% { transform: translateY(-10px); opacity: 1; }
}

.chat-footer {
    padding: 25px;
    background: white;
    border-top: 2px solid #f1f5f9;
}

.chat-input-wrapper {
    display: flex;
    gap: 12px;
    align-items: center;
}

.chat-input {
    flex: 1;
    padding: 14px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 28px;
    font-size: 0.975rem;
    transition: all 0.3s ease;
}

.chat-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.chat-send-btn {
    width: 50px;
    height: 50px;
    background: var(--gradient-1);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 20px rgba(99, 102, 241, 0.3);
}

.chat-send-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 8px 30px rgba(99, 102, 241, 0.4);
}

.chat-send-btn:active {
    transform: scale(0.95);
}

.chat-powered {
    text-align: center;
    padding: 12px;
    font-size: 0.75rem;
    color: var(--gray);
    background: #f8fafc;
    font-weight: 600;
}

@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 20px);
        right: 10px;
        bottom: 110px;
        max-height: 550px;
    }
    
    .chatbot-float-btn {
        bottom: 20px;
        right: 20px;
        width: 65px;
        height: 65px;
    }
}
</style>

<!-- Floating Chatbot Button -->
<button class="chatbot-float-btn" onclick="toggleChat()" id="chatBtn">
    <i class="fas fa-comments chat-icon"></i>
    <i class="fas fa-times close-icon"></i>
    <span class="chat-notification" id="chatNotification">1</span>
</button>

<!-- Chatbot Window -->
<div class="chatbot-window" id="chatWindow">
    <div class="chat-header">
        <div class="chat-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="chat-header-info">
            <h3>Joraki Assistant</h3>
            <div class="chat-status-online">
                <span class="status-dot"></span>
                <span>Online Now</span>
            </div>
        </div>
    </div>

    <div class="quick-replies">
        <button class="quick-reply-btn" onclick="sendQuickReply('How do I sell?')">
            How to sell?
        </button>
        <button class="quick-reply-btn" onclick="sendQuickReply('Track order')">
            Track order
        </button>
        <button class="quick-reply-btn" onclick="sendQuickReply('Pricing')">
            Pricing info
        </button>
    </div>

    <div class="chat-body" id="chatBody">
        <div class="chat-welcome">
            <i class="fas fa-robot"></i>
            <h4>Welcome to Joraki! 👋</h4>
            <p>I'm here to help. Ask me anything!</p>
        </div>
    </div>

    <div class="chat-footer">
        <div class="chat-input-wrapper">
            <input 
                type="text" 
                class="chat-input" 
                id="userInput" 
                placeholder="Type your message..."
                onkeypress="if(event.key === 'Enter') sendMessage()"
            />
            <button class="chat-send-btn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <div class="chat-powered">
        Powered by Joraki Ventures
    </div>
</div>

<script>
    const API_URL = "http://localhost/joraki/chart.php";
    
    let isChatOpen = false;
    let isFirstMessage = true;

    function toggleChat() {
        isChatOpen = !isChatOpen;
        const chatWindow = document.getElementById('chatWindow');
        const chatBtn = document.getElementById('chatBtn');
        const notification = document.getElementById('chatNotification');
        
        if (isChatOpen) {
            chatWindow.classList.add('active');
            chatBtn.classList.add('active');
            notification.style.display = 'none';
            document.getElementById('userInput').focus();
        } else {
            chatWindow.classList.remove('active');
            chatBtn.classList.remove('active');
        }
    }

    function addMessage(sender, text) {
        const chatBody = document.getElementById('chatBody');
        
        if (isFirstMessage) {
            chatBody.innerHTML = '';
            isFirstMessage = false;
        }

        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message message-${sender}`;
        
        const time = new Date().toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        messageDiv.innerHTML = `
            <div class="message-bubble">
                ${text}
                <div class="message-time">${time}</div>
            </div>
        `;
        
        chatBody.appendChild(messageDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function showTypingIndicator() {
        const chatBody = document.getElementById('chatBody');
        const indicator = document.createElement('div');
        indicator.className = 'chat-message message-bot';
        indicator.id = 'typingIndicator';
        indicator.innerHTML = `
            <div class="typing-indicator active">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        `;
        chatBody.appendChild(indicator);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    async function sendMessage() {
        const userInput = document.getElementById('userInput');
        const message = userInput.value.trim();
        
        if (!message) return;

        addMessage('user', message);
        userInput.value = '';

        showTypingIndicator();

        const formData = new FormData();
        formData.append('message', message);

        try {
            const res = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();

            removeTypingIndicator();

            if (data.success) {
                addMessage('bot', data.response);
            } else {
                addMessage('bot', '⚠️ Sorry, something went wrong. Please try again.');
            }
        } catch (err) {
            removeTypingIndicator();
            addMessage('bot', '❌ Connection error. Please check your internet and try again.');
            console.error(err);
        }
    }

    function sendQuickReply(message) {
        if (!isChatOpen) {
            toggleChat();
        }
        document.getElementById('userInput').value = message;
        sendMessage();
    }

    setTimeout(() => {
        if (!isChatOpen) {
            document.getElementById('chatNotification').style.display = 'flex';
        }
    }, 3000);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>