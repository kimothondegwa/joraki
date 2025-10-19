<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = "About Us - Joraki";
include __DIR__ . '/../includes/header.php';
?>

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
    --white: #ffffff;
}

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    color: var(--dark);
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    min-height: 100vh;
}

/* Hero Section */
.about-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 100px 0 80px;
    color: white;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.about-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

.hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    animation: fadeIn 0.8s ease;
}

.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
    backdrop-filter: blur(10px);
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.hero-text {
    font-size: 1.25rem;
    opacity: 0.95;
    max-width: 700px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

/* Mission Section */
.mission-section {
    padding: 80px 0;
    background: white;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 20px;
    text-align: center;
}

.section-subtitle {
    font-size: 1.125rem;
    color: var(--gray);
    text-align: center;
    margin-bottom: 60px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.mission-card {
    background: var(--light);
    padding: 50px 40px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.mission-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.15);
}

.mission-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 25px;
}

.mission-card h4 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 15px;
}

.mission-card p {
    color: var(--gray);
    font-size: 1.05rem;
    line-height: 1.7;
    margin: 0;
}

/* Values Section */
.values-section {
    padding: 80px 0;
    background: var(--light);
}

.value-card {
    background: white;
    padding: 40px 30px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    transition: all 0.3s ease;
    text-align: center;
    height: 100%;
}

.value-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.15);
}

.value-icon {
    width: 60px;
    height: 60px;
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin: 0 auto 20px;
    transition: all 0.3s ease;
}

.value-card:hover .value-icon {
    background: var(--primary);
    color: white;
    transform: scale(1.1) rotate(5deg);
}

.value-card h5 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 12px;
}

.value-card p {
    color: var(--gray);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
}

/* Stats Section */
.stats-section {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
}

.stat-box {
    text-align: center;
    padding: 30px 20px;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 10px;
    display: block;
}

.stat-label {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 500;
}

/* Team Section */
.team-section {
    padding: 80px 0;
    background: white;
}

.team-card {
    background: var(--light);
    padding: 40px 30px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    text-align: center;
    transition: all 0.3s ease;
}

.team-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.15);
}

.team-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    margin: 0 auto 20px;
}

.team-card h5 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
}

.team-card .role {
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 15px;
    display: block;
}

.team-card p {
    color: var(--gray);
    font-size: 0.95rem;
    line-height: 1.6;
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
    font-size: 1.05rem;
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
    font-size: 1.05rem;
}

.btn-outline-clean:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

/* Animations */
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

.fade-in {
    animation: fadeIn 0.8s ease;
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
</style>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-heart me-2"></i>About Joraki
            </div>
            <h1 class="hero-title">Your Trusted Platform for<br>Refurbished Quality</h1>
            <p class="hero-text">
                We're revolutionizing how people buy and sell refurbished items. 
                Making sustainability accessible, affordable, and reliable for everyone.
            </p>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="mission-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4>Our Mission</h4>
                    <p>
                        To empower users to buy and sell refurbished products with complete confidence, 
                        ensuring transparency, quality assurance, and convenience at every step. We believe 
                        in creating a marketplace where sustainability meets affordability.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4>Our Vision</h4>
                    <p>
                        To become the leading platform for refurbished goods in Kenya and beyond, 
                        fostering a circular economy where quality products get a second life, 
                        reducing waste while making premium items accessible to everyone.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values-section">
    <div class="container">
        <h2 class="section-title">Why Choose Joraki?</h2>
        <p class="section-subtitle">
            We're committed to delivering excellence in every aspect of our service
        </p>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Verified Quality</h5>
                    <p>Every item is professionally inspected and refurbished to meet our strict quality standards.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h5>Secure Payments</h5>
                    <p>Your transactions are protected with industry-leading security measures and buyer protection.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h5>Eco-Friendly</h5>
                    <p>Reduce waste and carbon footprint by giving quality products a second life.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p>Our dedicated team is always here to help you with any questions or concerns.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number" data-count="5000">0</span>
                    <span class="stat-label">Items Sold</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number" data-count="3500">0</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number" data-count="98">0</span>
                    <span class="stat-label">Satisfaction Rate</span>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-box">
                    <span class="stat-number" data-count="24">0</span>
                    <span class="stat-label">Hour Support</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section (Optional) -->
<section class="team-section">
    <div class="container">
        <h2 class="section-title">Meet Our Team</h2>
        <p class="section-subtitle">
            Passionate people working to make sustainable shopping easy
        </p>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5>Operations Team</h5>
                    <span class="role">Quality Control</span>
                    <p>Ensuring every item meets our high standards through rigorous testing and refurbishment.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5>Customer Support</h5>
                    <span class="role">Always Here to Help</span>
                    <p>Dedicated to providing exceptional service and support to all our customers.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <div class="team-avatar">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h5>Technical Team</h5>
                    <span class="role">Platform Development</span>
                    <p>Building and maintaining a secure, user-friendly platform for seamless transactions.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Ready to Get Started?</h2>
        <p class="cta-text">
            Join thousands of satisfied customers buying and selling refurbished items with complete confidence.
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="<?php echo BASE_URL; ?>sell_section/index.php" class="btn-primary-clean">
                <i class="fas fa-tag"></i>
                Sell Your Item
            </a>
            <a href="<?php echo BASE_URL; ?>buy_section/index.php" class="btn-outline-clean">
                <i class="fas fa-shopping-cart"></i>
                Start Shopping
            </a>
        </div>
        
        <div class="mt-5">
            <a href="<?php echo BASE_URL; ?>index.php" class="btn-outline-clean">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
        </div>
    </div>
</section>

<script>
// Counter animation for stats
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = Math.floor(target) + (element.textContent.includes('%') ? '%' : '+');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 30);
}

// Trigger animations on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !entry.target.dataset.animated) {
            const target = parseInt(entry.target.dataset.count);
            animateCounter(entry.target, target);
            entry.target.dataset.animated = 'true';
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('.stat-number').forEach(stat => observer.observe(stat));
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>