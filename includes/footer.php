<?php
// includes/footer.php - Common Footer for all pages

// Determine base URL dynamically
$base_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$base_url .= rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\') . '/';

$current_year = date('Y');
?>

</main>
<!-- End Main Content -->

<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4">
            <!-- About Section -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-recycle me-2"></i><?php echo SITE_NAME; ?>
                </h5>
                <p class="text-white-50">
                    Your trusted marketplace for buying professionally refurbished items and selling pre-loved goods. 
                    We believe in sustainability, quality, and fair prices for everyone.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="text-white me-3" title="Facebook">
                        <i class="fab fa-facebook fa-lg"></i>
                    </a>
                    <a href="#" class="text-white me-3" title="Twitter">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="#" class="text-white me-3" title="Instagram">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="#" class="text-white me-3" title="LinkedIn">
                        <i class="fab fa-linkedin fa-lg"></i>
                    </a>
                    <a href="#" class="text-white" title="YouTube">
                        <i class="fab fa-youtube fa-lg"></i>
                    </a>
                </div>
            </div>
            
    <!-- Quick Links -->
<div class="col-lg-2 col-md-6 mb-4">
    <h6 class="fw-bold mb-3">Quick Links</h6>
    <ul class="list-unstyled">
        <li class="mb-2">
            <a href="<?php echo BASE_URL; ?>" class="text-white-50 text-decoration-none">
                <i class="fas fa-angle-right me-2"></i>Home
            </a>
        </li>
        <li class="mb-2">
            <a href="<?php echo BASE_URL; ?>buy_section/item_details.php" class="text-white-50 text-decoration-none">
                <i class="fas fa-angle-right me-2"></i>Browse Items
            </a>
        </li>
        <li class="mb-2">
            <a href="<?php echo BASE_URL; ?>sell_section/index.php" class="text-white-50 text-decoration-none">
                <i class="fas fa-angle-right me-2"></i>Sell Item
            </a>
        </li>
        <li class="mb-2">
            <a href="<?php echo BASE_URL; ?>about/about_index.php" class="text-white-50 text-decoration-none">
                <i class="fas fa-angle-right me-2"></i>About Us
            </a>
        </li>
        <li class="mb-2">
            <a href="<?php echo BASE_URL; ?>contact section/contact_index.php" class="text-white-50 text-decoration-none">
                <i class="fas fa-angle-right me-2"></i>Contact
            </a>
        </li>
    </ul>
</div>

            
            <!-- Categories -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Categories</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="<?php echo $base_url; ?>buy/?category=Car" class="text-white-50 text-decoration-none">
                            <i class="fas fa-car me-2"></i>Cars
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo $base_url; ?>buy_section/item_details.php?category=Bike" class="text-white-50 text-decoration-none">
                            <i class="fas fa-motorcycle me-2"></i>Bikes
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo $base_url; ?>buy_section/item_details.php?category=Electronics" class="text-white-50 text-decoration-none">
                            <i class="fas fa-laptop me-2"></i>Electronics
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo $base_url; ?>buy_section/item_details.php?category=Furniture" class="text-white-50 text-decoration-none">
                            <i class="fas fa-couch me-2"></i>Furniture
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo $base_url; ?>buy_section/item_details.php?category=Appliances" class="text-white-50 text-decoration-none">
                            <i class="fas fa-blender me-2"></i>Appliances
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="fw-bold mb-3">Contact Us</h6>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>Nairobi, Kenya</span>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-white-50 text-decoration-none">
                            <?php echo SITE_EMAIL; ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:<?php echo str_replace(' ', '', SITE_PHONE); ?>" class="text-white-50 text-decoration-none">
                            <?php echo SITE_PHONE; ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-clock me-2"></i>
                        <span>Mon - Fri: 8:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <hr class="my-4 bg-white opacity-10">
        
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0 text-white-50">
                    &copy; <?php echo $current_year; ?> <?php echo SITE_NAME; ?>. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="<?php echo $base_url; ?>privacy.php" class="text-white-50 text-decoration-none small">
                            Privacy Policy
                        </a>
                    </li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item">
                        <a href="<?php echo $base_url; ?>terms.php" class="text-white-50 text-decoration-none small">
                            Terms of Service
                        </a>
                    </li>
                    <li class="list-inline-item">|</li>
                    <li class="list-inline-item">
                        <a href="<?php echo $base_url; ?>faq.php" class="text-white-50 text-decoration-none small">
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="<?php echo $base_url; ?>assets/js/script.js"></script>

<!-- Additional Page-Specific Scripts -->
<?php if (isset($additional_scripts)): ?>
    <?php echo $additional_scripts; ?>
<?php endif; ?>

</body>
</html>