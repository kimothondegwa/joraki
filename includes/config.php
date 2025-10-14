<?php
// includes/config.php - ReSellX Configuration File

// ============================================
// DATABASE CONFIGURATION
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'joraki');
define('DB_USER', 'root');
define('DB_PASS', '');  // Change this for production
define('DB_CHARSET', 'utf8mb4');

// ============================================
// SITE CONFIGURATION
// ============================================
define('SITE_NAME', 'joraki');
define('SITE_URL', 'http://localhost/joraki');  // Change for production
define('SITE_EMAIL', 'info@joraki.com');
define('SITE_PHONE', '+254 70 023 02241');

// ============================================
// PATH CONFIGURATION
// ============================================
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('ASSETS_PATH', BASE_PATH . '/assets/');

// ============================================
// URL CONFIGURATION
// ============================================
define('BASE_URL', SITE_URL . '/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('ASSETS_URL', BASE_URL . 'assets/');

// ============================================
// UPLOAD SETTINGS
// ============================================
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// ============================================
// SECURITY SETTINGS
// ============================================
define('SESSION_LIFETIME', 3600);  // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 8);
define('BCRYPT_COST', 10);

// ============================================
// PAGINATION SETTINGS
// ============================================
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// ============================================
// BUSINESS SETTINGS
// ============================================
define('COMMISSION_RATE', 15);  // Percentage
define('CURRENCY_SYMBOL', 'KSh');
define('CURRENCY_CODE', 'KES');

// ============================================
// RESTRICTED CATEGORIES
// These categories are not allowed
// ============================================
define('RESTRICTED_CATEGORIES', [
    'Yacht',
    'Jet',
    'Train',
    'Heavy Machinery',
    'Aircraft',
    'Weapons',
    'Explosives'
]);

// ============================================
// EMAIL SETTINGS (for future implementation)
// ============================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', SITE_EMAIL);
define('SMTP_FROM_NAME', SITE_NAME);

// ============================================
// PAYMENT GATEWAY SETTINGS (Placeholder)
// ============================================
// M-Pesa
define('MPESA_CONSUMER_KEY', 'your_consumer_key');
define('MPESA_CONSUMER_SECRET', 'your_consumer_secret');
define('MPESA_SHORTCODE', 'your_shortcode');
define('MPESA_PASSKEY', 'your_passkey');

// Stripe
define('STRIPE_PUBLIC_KEY', 'your_stripe_public_key');
define('STRIPE_SECRET_KEY', 'your_stripe_secret_key');

// PayPal
define('PAYPAL_CLIENT_ID', 'your_paypal_client_id');
define('PAYPAL_SECRET', 'your_paypal_secret');
define('PAYPAL_MODE', 'sandbox');  // sandbox or live

// ============================================
// TIMEZONE SETTINGS
// ============================================
date_default_timezone_set('Africa/Nairobi');

// ============================================
// ERROR REPORTING
// ============================================
// Development Mode
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('DEBUG_MODE', true);
} else {
    // Production Mode
    error_reporting(0);
    ini_set('display_errors', 0);
    define('DEBUG_MODE', false);
}

// ============================================
// SESSION CONFIGURATION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);  // Set to 1 if using HTTPS
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();
}

// ============================================
// HELPER CONSTANTS
// ============================================
define('STATUS_PENDING', 'pending');
define('STATUS_PURCHASED', 'purchased');
define('STATUS_UNDER_MODIFICATION', 'under_modification');
define('STATUS_REFURBISHED', 'refurbished');
define('STATUS_SOLD', 'sold');
define('STATUS_REJECTED', 'rejected');

define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

define('TRANSACTION_PURCHASE', 'purchase_from_seller');
define('TRANSACTION_SALE', 'sale_to_buyer');

// ============================================
// AUTO-CREATE UPLOAD DIRECTORY
// ============================================
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// ============================================
// MAINTENANCE MODE CHECK
// ============================================
define('MAINTENANCE_MODE', false);

if (MAINTENANCE_MODE && !isset($_SESSION['is_admin'])) {
    header('HTTP/1.1 503 Service Temporarily Unavailable');
    header('Status: 503 Service Temporarily Unavailable');
    header('Retry-After: 3600');
    die('
        <!DOCTYPE html>
        <html>
        <head>
            <title>Maintenance Mode - ' . SITE_NAME . '</title>
            <style>
                body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
                h1 { color: #333; }
                p { color: #666; }
            </style>
        </head>
        <body>
            <h1>🔧 We\'ll be back soon!</h1>
            <p>Sorry for the inconvenience. We\'re performing maintenance and will be back shortly.</p>
            <p>— The ' . SITE_NAME . ' Team</p>
        </body>
        </html>
    ');
}