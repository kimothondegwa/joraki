<?php
// includes/header.php - Common Header for all pages

// Ensure config is loaded
require_once __DIR__ . '/config.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use BASE_URL directly from config.php
$base_url = BASE_URL;

// Site name (already defined in config, but fallback just in case)
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Joraki Ventures');
}

// Default page title
if (!isset($page_title)) {
    $page_title = SITE_NAME . ' - Buy & Sell Refurbished Items';
}

// Auth/session variables
$is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$is_admin = !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$user_name = $_SESSION['user_name'] ?? '';

// Current page for active menu highlighting
$current_page = basename($_SERVER['SCRIPT_NAME']);
$current_dir = basename(dirname($_SERVER['SCRIPT_NAME']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(SITE_NAME); ?> - Your trusted marketplace">
    <link rel="icon" href="<?php echo $base_url; ?>joraki.jpg">

    <!-- Bootstrap & Font Awesome (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo $base_url; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>">
            <img src="<?php echo $base_url; ?>Joraki Ventures.png" alt="Joraki Ventures Logo" height="40" class="d-inline-block align-text-top me-2">
            <?php echo htmlspecialchars(SITE_NAME); ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>"
                       href="<?php echo $base_url; ?>">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_dir === 'buy') ? 'active' : ''; ?>"
                       href="<?php echo $base_url; ?>buy_section/index.php">
                        <i class="fas fa-shopping-cart me-1"></i> Browse Items
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_dir === 'sell_section') ? 'active' : ''; ?>"
                      href="<?php echo $base_url; ?>sell_section/index.php">
                     <i class="fas fa-tag me-1"></i> Sell Item
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_dir === 'about') ? 'active' : ''; ?>"
                       href="<?php echo $base_url; ?>about/about_index.php ">
                        <i class="fas fa-info-circle me-1"></i> About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_dir === 'contact') ? 'active' : ''; ?>"
                       href="<?php echo $base_url; ?>contact section/contact_index.php">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>

                <?php if ($is_admin): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_dir === 'admin') ? 'active' : ''; ?>"
                           href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-shield me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>admin/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php elseif ($is_logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($user_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>profile.php">
                                <i class="fas fa-user-circle me-2"></i> My Profile
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>my-items.php">
                                <i class="fas fa-list me-2"></i> My Items
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>admin/index.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Admin Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Alerts and main content -->
<main class="main-content">
<!-- Page-specific content starts here -->
