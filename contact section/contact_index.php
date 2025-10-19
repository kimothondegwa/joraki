<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = "Contact Us - Joraki";
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
.contact-hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 100px 0 80px;
    color: white;
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 500px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.contact-hero::after {
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
    margin: 0 auto;
    line-height: 1.6;
}

/* Contact Section */
.contact-section {
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

/* Contact Cards */
.contact-card {
    background: var(--light);
    padding: 40px 35px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    transition: all 0.3s ease;
    text-align: center;
    height: 100%;
}

.contact-card:hover {
    border-color: var(--primary);
    transform: translateY(-8px);
    box-shadow: 0 10px 35px rgba(37, 99, 235, 0.15);
}

.contact-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 25px;
    transition: all 0.3s ease;
}

.contact-card:hover .contact-icon {
    transform: scale(1.1) rotate(5deg);
}

.contact-card h4 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 15px;
}

.contact-card p {
    color: var(--gray);
    font-size: 0.95rem;
    margin-bottom: 20px;
    line-height: 1.6;
}

.contact-link {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    font-size: 1.05rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.contact-link:hover {
    color: var(--primary-dark);
    gap: 12px;
}

/* Contact Form Section */
.form-section {
    padding: 80px 0;
    background: var(--light);
}

.form-container {
    max-width: 700px;
    margin: 0 auto;
    background: white;
    padding: 50px 45px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
}

.form-label {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.form-control, .form-select {
    border: 2px solid #e0f2fe;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    outline: none;
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
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
    width: 100%;
    justify-content: center;
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

/* FAQ Section */
.faq-section {
    padding: 80px 0;
    background: white;
}

.faq-item {
    background: var(--light);
    padding: 25px 30px;
    border-radius: 12px;
    border: 2px solid #e0f2fe;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
}

.faq-question {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.faq-question i {
    color: var(--primary);
}

.faq-answer {
    color: var(--gray);
    line-height: 1.6;
    margin: 0;
}

/* Hours Section */
.hours-card {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 50px 40px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.2);
}

.hours-card h4 {
    font-size: 1.75rem;
    font-weight: 800;
    margin-bottom: 25px;
}

.hours-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.hours-list li {
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 1.05rem;
}

.hours-list li:last-child {
    border-bottom: none;
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
    
    .form-container {
        padding: 35px 25px;
    }
    
    .cta-title {
        font-size: 2rem;
    }
}
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-envelope-open-text me-2"></i>Get In Touch
            </div>
            <h1 class="hero-title">Contact Us</h1>
            <p class="hero-text">
                Have questions, suggestions, or need assistance? We're here to help! 
                Reach out to our friendly team anytime.
            </p>
        </div>
    </div>
</section>

<!-- Contact Cards Section -->
<section class="contact-section">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-subtitle">
            Choose your preferred way to reach us
        </p>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4>Email Us</h4>
                    <p>Send us an email anytime and we'll get back to you within 24 hours.</p>
                    <a href="mailto:<?php echo SITE_EMAIL; ?>" class="contact-link">
                        <?php echo SITE_EMAIL; ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h4>Call Us</h4>
                    <p>Speak with our support team directly during business hours.</p>
                    <a href="tel:<?php echo SITE_PHONE; ?>" class="contact-link">
                        <?php echo SITE_PHONE; ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4>Visit Website</h4>
                    <p>Explore our platform and discover amazing refurbished items.</p>
                    <a href="<?php echo SITE_URL; ?>" class="contact-link">
                        Visit Site
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="form-section">
    <div class="container">
        <div class="form-container">
            <h3 class="text-center mb-2" style="font-size: 2rem; font-weight: 800; color: var(--dark);">Send Us a Message</h3>
            <p class="text-center mb-4" style="color: var(--gray);">Fill out the form below and we'll respond as soon as possible</p>
            
            <form action="<?php echo BASE_URL; ?>includes/contact_handler.php" method="POST">
                <div class="mb-4">
                    <label for="name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="john@example.com" required>
                </div>
                
                <div class="mb-4">
                    <label for="subject" class="form-label">Subject</label>
                    <select class="form-select" id="subject" name="subject" required>
                        <option value="">Select a topic...</option>
                        <option value="general">General Inquiry</option>
                        <option value="support">Technical Support</option>
                        <option value="buying">Buying Questions</option>
                        <option value="selling">Selling Questions</option>
                        <option value="feedback">Feedback</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" placeholder="Tell us how we can help..." required></textarea>
                </div>
                
                <button type="submit" class="btn-primary-clean">
                    <i class="fas fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <p class="section-subtitle">
            Quick answers to common questions
        </p>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-question-circle"></i>
                        What are your business hours?
                    </div>
                    <p class="faq-answer">
                        Our support team is available Monday to Friday, 9:00 AM - 6:00 PM EAT. 
                        Email inquiries are answered within 24 hours.
                    </p>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-question-circle"></i>
                        How quickly will I get a response?
                    </div>
                    <p class="faq-answer">
                        We typically respond to all inquiries within 24 hours during business days. 
                        Urgent matters are prioritized and handled as quickly as possible.
                    </p>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-question-circle"></i>
                        Can I visit your office?
                    </div>
                    <p class="faq-answer">
                        We operate primarily online, but appointments can be arranged for item inspections 
                        or consultations. Please contact us to schedule a visit.
                    </p>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-question-circle"></i>
                        Do you offer phone support?
                    </div>
                    <p class="faq-answer">
                        Yes! You can reach our support team via phone during business hours. 
                        For after-hours inquiries, please email us or use the contact form above.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Business Hours Card -->
<section class="contact-section" style="padding-top: 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="hours-card">
                    <h4><i class="fas fa-clock me-2"></i>Business Hours</h4>
                    <ul class="hours-list">
                        <li>Monday - Friday: 9:00 AM - 6:00 PM</li>
                        <li>Saturday: 10:00 AM - 4:00 PM</li>
                        <li>Sunday: Closed</li>
                        <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 20px;">
                            <i class="fas fa-info-circle me-2"></i>Emergency support available 24/7
                        </li>
                    </ul>
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
            Don't have a question? Browse our marketplace or list your items today!
        </p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="<?php echo BASE_URL; ?>sell_section/index.php" class="btn-primary-clean" style="width: auto;">
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

<?php include __DIR__ . '/../includes/footer.php'; ?>