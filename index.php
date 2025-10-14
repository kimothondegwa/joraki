<?php
// index.php - Joraki Homepage Clean & Modern
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
/* Clean Modern Design System */
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
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    color: var(--dark);
    line-height: 1.6;
    overflow-x: hidden;
}

/* Hero Section - Clean & Modern */
.hero-section {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 120px 0 80px;
    position: relative;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(135deg, transparent 0%, rgba(37, 99, 235, 0.05) 100%);
    z-index: 0;
}

.hero-content {
    position: relative;
    z-index: 1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: var(--dark);
    line-height: 1.2;
    margin-bottom: 1.5rem;
}

.hero-title .brand {
    color: var(--primary);
}

.hero-text {
    font-size: 1.25rem;
    color: var(--gray);
    margin-bottom: 2.5rem;
    max-width: 600px;
}

.hero-image {
    max-width: 100%;
    height: auto;
}

/* Buttons - Clean Style */
.btn-primary-clean {
    background: var(--primary);
    color: white;
    padding: 16px 40px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary-clean:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
    color: white;
}

.btn-outline-clean {
    background: white;
    color: var(--primary);
    padding: 16px 40px;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    border: 2px solid var(--primary);
}

.btn-outline-clean:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

/* Stats Section - Simple Cards */
.stats-section {
    padding: 80px 0;
    background: white;
}

.stat-card {
    background: var(--light);
    padding: 40px 30px;
    border-radius: 16px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.stat-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}

.stat-icon.blue { color: var(--primary); }
.stat-icon.orange { color: var(--accent); }
.stat-icon.green { color: var(--success); }

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1rem;
    color: var(--gray);
    font-weight: 500;
}

/* Process Section - Clean Steps */
.process-section {
    padding: 80px 0;
    background: var(--light);
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    text-align: center;
    margin-bottom: 15px;
}

.section-subtitle {
    font-size: 1.125rem;
    color: var(--gray);
    text-align: center;
    margin-bottom: 60px;
}

.process-card {
    background: white;
    padding: 40px 30px;
    border-radius: 16px;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
}

.process-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.process-number {
    width: 50px;
    height: 50px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 auto 25px;
}

.process-icon {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 20px;
}

.process-card h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 15px;
}

.process-card p {
    color: var(--gray);
    font-size: 1rem;
    line-height: 1.6;
}

/* Product Cards - Modern & Clean */
.products-section {
    padding: 80px 0;
    background: white;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid #e2e8f0;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.product-image {
    width: 100%;
    height: 280px;
    object-fit: cover;
    background: var(--light);
}

.product-content {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-badges {
    display: flex;
    gap: 8px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.badge-custom {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
}

.badge-primary {
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.badge-gray {
    background: rgba(100, 116, 139, 0.1);
    color: var(--gray);
}

.product-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 12px;
}

.product-description {
    color: var(--gray);
    font-size: 0.95rem;
    margin-bottom: 20px;
    flex-grow: 1;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 2px solid var(--light);
}

.product-price {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--primary);
}

.product-price-label {
    font-size: 0.75rem;
    color: var(--gray);
    display: block;
    margin-bottom: 5px;
}

/* Features Section */
.features-section {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
}

.feature-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 35px;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.feature-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-5px);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 20px;
}

.feature-card h5 {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 12px;
}

.feature-card p {
    font-size: 1rem;
    opacity: 0.9;
    line-height: 1.6;
}

/* Categories Section */
.categories-section {
    padding: 80px 0;
    background: var(--light);
}

.category-card {
    background: white;
    padding: 60px 30px;
    border-radius: 16px;
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    border: 2px solid transparent;
}

.category-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.category-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.category-card h5 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

/* CTA Section */
.cta-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    text-align: center;
}

.cta-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 20px;
}

.cta-text {
    font-size: 1.25rem;
    color: var(--gray);
    margin-bottom: 40px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state i {
    font-size: 5rem;
    color: #cbd5e1;
    margin-bottom: 25px;
}

.empty-state h4 {
    font-size: 1.5rem;
    color: var(--gray);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--gray);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-text {
        font-size: 1.125rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
}

/* Smooth Animations */
.fade-in {
    animation: fadeIn 0.8s ease-in;
}

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
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="hero-content fade-in">
                    <h1 class="hero-title">
                        Welcome to <span class="brand">Joraki</span>
                    </h1>
                    <p class="hero-text">
                        Buy and sell professionally refurbished items with confidence. Quality products, fair prices, and sustainable shopping.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="buy_section/index.php" class="btn-primary-clean">
                            <i class="fas fa-shopping-cart"></i>
                            Browse Items
                        </a>
                        <a href="sell_section/index.php" class="btn-outline-clean">
                            <i class="fas fa-tag"></i>
                            Sell Your Item
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/hero-illustration.svg" alt="Joraki" class="hero-image" onerror="this.src='https://via.placeholder.com/600x400/e0f2fe/2563eb?text=Joraki+Platform'">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($total_sold); ?>+</div>
                    <div class="stat-label">Items Successfully Sold</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($active_listings); ?></div>
                    <div class="stat-label">Active Listings</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?php echo number_format($total_users); ?>+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="process-section">
    <div class="container">
        <h2 class="section-title">How Joraki Works</h2>
        <p class="section-subtitle">Simple, secure, and sustainable</p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">1</div>
                    <div class="process-icon"><i class="fas fa-upload"></i></div>
                    <h4>Submit Your Item</h4>
                    <p>Fill out our simple form with details, upload photos, and set your price.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">2</div>
                    <div class="process-icon"><i class="fas fa-clipboard-check"></i></div>
                    <h4>We Review</h4>
                    <p>Our team reviews within 24-48 hours and arranges pickup and payment.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">3</div>
                    <div class="process-icon"><i class="fas fa-tools"></i></div>
                    <h4>Refurbishment</h4>
                    <p>Expert technicians inspect, repair, and certify every item.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="process-card">
                    <div class="process-number">4</div>
                    <div class="process-icon"><i class="fas fa-shopping-bag"></i></div>
                    <h4>Ready to Sell</h4>
                    <p>Listed on marketplace at competitive prices for buyers.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <h2 class="section-title">Recently Refurbished</h2>
        <p class="section-subtitle">Quality products at unbeatable prices</p>
        
        <?php if (!empty($featured_items)): ?>
        <div class="row g-4 mb-5">
            <?php foreach ($featured_items as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="product-card">
                    <?php $image_path = !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : 'assets/images/placeholder.jpg'; ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="product-image" onerror="this.src='https://via.placeholder.com/400x280/f8fafc/2563eb?text=No+Image'">
                    
                    <div class="product-content">
                        <div class="product-badges">
                            <span class="badge-custom badge-success">
                                <i class="fas fa-check-circle me-1"></i>Refurbished
                            </span>
                            <span class="badge-custom badge-primary"><?php echo htmlspecialchars($item['category']); ?></span>
                            <span class="badge-custom badge-gray"><?php echo ucfirst(htmlspecialchars($item['condition_type'])); ?></span>
                        </div>
                        
                        <h3 class="product-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="product-description"><?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...</p>
                        
                        <div class="product-footer">
                            <div>
                                <span class="product-price-label">Price</span>
                                <div class="product-price">KSh <?php echo number_format($item['price'], 0); ?></div>
                            </div>
                            <a href="buy_section/item_details.php?id=<?php echo $item['id']; ?>" class="btn-primary-clean">
                                View <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="buy_section/item_details.php" class="btn-primary-clean">
                View All Items <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h4>No items available at the moment</h4>
            <p>Check back soon for amazing refurbished products!</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title text-white mb-4">Why Choose Joraki?</h2>
        <p class="section-subtitle text-white mb-5" style="opacity: 0.9;">Your trusted partner for quality refurbished items</p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-shield-alt"></i></div>
                    <h5>Secure Transactions</h5>
                    <p>All payments processed securely with full buyer protection.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-certificate"></i></div>
                    <h5>Quality Guaranteed</h5>
                    <p>Professional inspection and refurbishment by certified technicians.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-hand-holding-usd"></i></div>
                    <h5>Best Prices</h5>
                    <p>Fair pricing for sellers, unbeatable deals for buyers.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto"><i class="fas fa-leaf"></i></div>
                    <h5>Eco-Friendly</h5>
                    <p>Sustainable shopping that reduces waste and protects our planet.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <p class="section-subtitle">Find exactly what you need</p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/item_details.php?category=Electronics" class="category-card">
                    <div class="category-icon">💻</div>
                    <h5>Electronics</h5>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/item_details.php?category=Furniture" class="category-card">
                    <div class="category-icon">🛋️</div>
                    <h5>Furniture</h5>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/item_details.php?category=Appliances" class="category-card">
                    <div class="category-icon">🏠</div>
                    <h5>Appliances</h5>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="buy_section/item_details.php?category=Other" class="category-card">
                    <div class="category-icon">📦</div>
                    <h5>Other</h5>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Ready to Get Started?</h2>
        <p class="cta-text">Join thousands of satisfied customers buying and selling refurbished items with complete confidence.</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="sell_section/index.php" class="btn-primary-clean">
                <i class="fas fa-tag"></i>
                Sell Your Item
            </a>
            <a href="buy_section/index.php" class="btn-outline-clean">
                <i class="fas fa-shopping-cart"></i>
                Start Shopping
            </a>
        </div>
    </div>
</section>

<script>
// Counter animation
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = Math.floor(target).toLocaleString() + (element.textContent.includes('+') ? '+' : '');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 30);
}

// Trigger animations on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
            const numberElement = entry.target.querySelector('.stat-number');
            if (numberElement) {
                const target = parseInt(numberElement.textContent.replace(/[^0-9]/g, ''));
                animateCounter(numberElement, target);
                entry.target.dataset.animated = 'true';
            }
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-card').forEach(card => observer.observe(card));
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>