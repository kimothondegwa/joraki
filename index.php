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
                        Welcome to <span class="brand">Joraki Ventures</span>
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
        <h2 class="section-title">How Joraki Ventures Works</h2>
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
<!-- ============================================ -->
<!-- FLOATING CHATBOT BUTTON - Add anywhere in your page -->
<!-- ============================================ -->

<style>
/* Floating Chatbot Button */
.chatbot-float-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    z-index: 9999;
    transition: all 0.3s ease;
    border: none;
}

.chatbot-float-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 35px rgba(37, 99, 235, 0.5);
}

.chatbot-float-btn i {
    font-size: 1.8rem;
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

/* Notification Badge */
.chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    background: #ef4444;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    border: 3px solid white;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
}

/* Floating Chatbot Window */
.chatbot-window {
    position: fixed;
    bottom: 110px;
    right: 30px;
    width: 400px;
    max-height: 600px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 60px rgba(0, 0, 0, 0.3);
    z-index: 9998;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

.chatbot-window.active {
    display: flex;
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

/* Chat Header */
.chat-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.chat-avatar {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.chat-header-info h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 0;
}

.chat-status-online {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    opacity: 0.95;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
    }
}

/* Chat Body */
.chat-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8fafc;
    max-height: 400px;
}

.chat-body::-webkit-scrollbar {
    width: 6px;
}

.chat-body::-webkit-scrollbar-track {
    background: #e2e8f0;
}

.chat-body::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 10px;
}

/* Chat Messages */
.chat-message {
    margin-bottom: 15px;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-bubble {
    display: inline-block;
    padding: 12px 16px;
    border-radius: 18px;
    max-width: 75%;
    word-wrap: break-word;
    font-size: 0.95rem;
    line-height: 1.5;
}

.message-user {
    text-align: right;
}

.message-user .message-bubble {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-bot {
    text-align: left;
}

.message-bot .message-bubble {
    background: white;
    color: var(--dark);
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}

.message-time {
    font-size: 0.7rem;
    opacity: 0.6;
    margin-top: 4px;
}

/* Welcome Message */
.chat-welcome {
    text-align: center;
    padding: 30px 15px;
    color: var(--gray);
}

.chat-welcome i {
    font-size: 3rem;
    color: var(--primary);
    margin-bottom: 15px;
    opacity: 0.3;
}

.chat-welcome h4 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
}

.chat-welcome p {
    font-size: 0.9rem;
    margin: 0;
}

/* Quick Replies */
.quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 15px 20px 0;
    background: #f8fafc;
}

.quick-reply-btn {
    padding: 8px 14px;
    background: white;
    border: 2px solid var(--primary);
    border-radius: 20px;
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quick-reply-btn:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
}

/* Typing Indicator */
.typing-indicator {
    display: none;
    padding: 12px 16px;
    background: white;
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    width: fit-content;
    border: 1px solid #e2e8f0;
}

.typing-indicator.active {
    display: inline-block;
}

.typing-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: var(--gray);
    border-radius: 50%;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-8px);
    }
}

/* Chat Footer */
.chat-footer {
    padding: 20px;
    background: white;
    border-top: 2px solid #f1f5f9;
}

.chat-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
}

.chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 25px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.chat-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.chat-send-btn {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.125rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-send-btn:hover {
    transform: scale(1.1);
}

.chat-send-btn:active {
    transform: scale(0.95);
}

/* Powered By */
.chat-powered {
    text-align: center;
    padding: 10px;
    font-size: 0.75rem;
    color: var(--gray);
    background: #f8fafc;
}

/* Mobile Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 20px);
        right: 10px;
        bottom: 100px;
        max-height: 500px;
    }
    
    .chatbot-float-btn {
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
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
    <!-- Header -->
    <div class="chat-header">
        <div class="chat-avatar">
            <i class="fas fa-robot"></i>
        </div>
        <div class="chat-header-info">
            <h3>Joraki Assistant</h3>
            <div class="chat-status-online">
                <span class="status-dot"></span>
                <span>Online</span>
            </div>
        </div>
    </div>

    <!-- Quick Replies -->
    <div class="quick-replies">
        <button class="quick-reply-btn" onclick="sendQuickReply('How do I sell?')">
            How to sell?
        </button>
        <button class="quick-reply-btn" onclick="sendQuickReply('Track order')">
            Track order
        </button>
        <button class="quick-reply-btn" onclick="sendQuickReply('Pricing')">
            Pricing
        </button>
    </div>

    <!-- Chat Body -->
    <div class="chat-body" id="chatBody">
        <div class="chat-welcome">
            <i class="fas fa-robot"></i>
            <h4>Welcome to Joraki! 👋</h4>
            <p>I'm here to help. Ask me anything!</p>
        </div>
    </div>

    <!-- Footer -->
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

    <!-- Powered By -->
    <div class="chat-powered">
        Powered by Joraki Ventures
    </div>
</div>

<script>
    const API_URL = "http://localhost/joraki/chart.php"; // 🟢 CHANGE to your PHP path
    
    let isChatOpen = false;
    let isFirstMessage = true;

    // Toggle Chat Window
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

    // Add Message to Chat
    function addMessage(sender, text) {
        const chatBody = document.getElementById('chatBody');
        
        // Remove welcome message on first message
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

    // Show Typing Indicator
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

    // Remove Typing Indicator
    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    // Send Message
    async function sendMessage() {
        const userInput = document.getElementById('userInput');
        const message = userInput.value.trim();
        
        if (!message) return;

        // Add user message
        addMessage('user', message);
        userInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Send to API
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

    // Quick Reply
    function sendQuickReply(message) {
        if (!isChatOpen) {
            toggleChat();
        }
        document.getElementById('userInput').value = message;
        sendMessage();
    }

    // Show notification after 3 seconds
    setTimeout(() => {
        if (!isChatOpen) {
            document.getElementById('chatNotification').style.display = 'flex';
        }
    }, 3000);
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>