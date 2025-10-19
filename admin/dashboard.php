<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php?error=Please+login');
    exit;
}

// Fetch items grouped by status
try {
    $stmt = $pdo->query("SELECT * FROM items ORDER BY created_at DESC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $items = [];
}

// Helper: Filter by one or multiple statuses
function filter_by_status($items, $statuses) {
    $statuses = (array)$statuses;
    return array_filter($items, function($i) use ($statuses) {
        return isset($i['status']) && in_array($i['status'], $statuses);
    });
}

$pending = filter_by_status($items, ['pending', 'pending_purchase']);
$purchased = filter_by_status($items, 'purchased');
$refurbished = filter_by_status($items, 'refurbished');
$sold = filter_by_status($items, 'sold');

// Calculate revenue and stats
$total_revenue = array_sum(array_column($sold, 'price'));
$avg_price = count($sold) > 0 ? $total_revenue / count($sold) : 0;
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - Joraki</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
    --danger: #ef4444;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    color: var(--dark);
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    background: var(--white);
    border-right: 2px solid #e0f2fe;
    box-shadow: 4px 0 20px rgba(37, 99, 235, 0.08);
    overflow-y: auto;
    z-index: 1000;
    transition: transform 0.3s ease;
}

.sidebar-brand {
    padding: 30px 25px;
    border-bottom: 2px solid #e0f2fe;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
}

.sidebar-brand h3 {
    color: var(--white);
    font-weight: 800;
    font-size: 1.5rem;
    margin-bottom: 5px;
    letter-spacing: 0.5px;
}

.sidebar-brand p {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    margin: 0;
}

.sidebar-menu {
    padding: 20px 0;
}

.menu-item {
    padding: 14px 25px;
    color: var(--gray);
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    cursor: pointer;
    border-left: 3px solid transparent;
    position: relative;
    font-weight: 500;
}

.menu-item i {
    width: 25px;
    font-size: 1.1rem;
    margin-right: 12px;
    color: var(--gray);
    transition: all 0.3s ease;
}

.menu-item:hover,
.menu-item.active {
    background: rgba(37, 99, 235, 0.08);
    color: var(--primary);
    border-left-color: var(--primary);
    transform: translateX(4px);
}

.menu-item:hover i,
.menu-item.active i {
    color: var(--primary);
    transform: scale(1.1);
}

/* Main Content */
.main-content {
    margin-left: 260px;
    padding: 30px;
    animation: fadeIn 0.5s ease;
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

/* Top Bar */
.top-bar {
    background: var(--white);
    padding: 25px 30px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.top-bar h1 {
    color: var(--dark);
    font-weight: 800;
    font-size: 1.75rem;
    margin: 0;
}

.top-bar p {
    color: var(--gray);
    margin: 5px 0 0 0;
    font-size: 0.9rem;
}

.top-bar p strong {
    color: var(--primary);
    font-weight: 600;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--white);
    padding: 30px;
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--primary-light) 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    border-color: var(--primary);
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.15);
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 15px;
    background: rgba(37, 99, 235, 0.1);
    color: var(--primary);
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
    background: var(--primary);
    color: var(--white);
}

.stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--dark);
    margin-bottom: 5px;
}

.stat-label {
    color: var(--gray);
    font-size: 0.9rem;
    font-weight: 500;
}

/* Content Cards */
.content-card {
    background: var(--white);
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.08);
    margin-bottom: 30px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.content-card:hover {
    box-shadow: 0 8px 30px rgba(37, 99, 235, 0.12);
}

.card-header-custom {
    padding: 20px 30px;
    border-bottom: 2px solid #e0f2fe;
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
}

.card-header-custom h3 {
    margin: 0;
    color: var(--dark);
    font-weight: 700;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header-custom h3 i {
    color: var(--primary);
}

.card-body-custom {
    padding: 30px;
}

/* Modern Add Item Form */
.add-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.add-item-header h4 {
    margin: 0;
    color: var(--dark);
    font-weight: 700;
    font-size: 1.5rem;
}

#addItemForm {
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
    border-radius: 16px;
    border: 2px solid #e0f2fe;
    padding: 35px;
    display: none;
}

#addItemForm h5 {
    color: var(--dark);
    font-weight: 700;
    margin-bottom: 25px;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

#addItemForm h5 i {
    color: var(--primary);
}

/* Form Styling */
.form-control, .form-select {
    border: 2px solid #e0f2fe;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: var(--white);
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    outline: none;
    background: var(--white);
}

.form-label {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 10px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-label i {
    color: var(--primary);
    font-size: 0.9rem;
}

/* Table Styles */
.table-custom {
    width: 100%;
    border-collapse: collapse;
}

.table-custom thead {
    background: linear-gradient(135deg, #f8fafc 0%, #f0f9ff 100%);
}

.table-custom thead th {
    color: var(--dark);
    font-weight: 600;
    padding: 15px;
    border-bottom: 2px solid #e0f2fe;
    font-size: 0.85rem;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-custom tbody tr {
    border-bottom: 1px solid #f0f9ff;
    transition: all 0.2s ease;
}

.table-custom tbody tr:hover {
    background: rgba(37, 99, 235, 0.03);
    transform: scale(1.005);
}

.table-custom tbody td {
    padding: 15px;
    color: var(--gray);
    font-size: 0.95rem;
}

.table-custom tbody td strong {
    color: var(--dark);
}

.item-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid #e0f2fe;
    transition: all 0.3s ease;
    cursor: pointer;
}

.item-thumb:hover {
    transform: scale(1.8);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    z-index: 10;
    border-color: var(--primary);
}

/* Buttons */
.action-btn {
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    border: 2px solid;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    margin-right: 5px;
    margin-bottom: 5px;
}

.btn-approve {
    background: var(--white);
    color: var(--success);
    border-color: var(--success);
}

.btn-approve:hover {
    background: var(--success);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
}

.btn-refurb {
    background: var(--white);
    color: var(--primary);
    border-color: var(--primary);
}

.btn-refurb:hover {
    background: var(--primary);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
}

.btn-sold {
    background: var(--white);
    color: #8b5cf6;
    border-color: #8b5cf6;
}

.btn-sold:hover {
    background: #8b5cf6;
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
}

.btn-delete {
    background: var(--white);
    color: var(--danger);
    border-color: var(--danger);
}

.btn-delete:hover {
    background: var(--danger);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.3);
}

.btn-logout, .btn-report {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
}

.btn-logout:hover, .btn-report:hover {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
}

.btn-dark {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    border: none;
    font-weight: 600;
    padding: 12px 28px;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
}

.btn-dark:hover {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
}

/* Alert Styles */
.alert-custom {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border: 2px solid;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.alert-success-custom {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border-color: var(--success);
}

.alert-danger-custom {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border-color: var(--danger);
}

/* Badge */
.badge-count {
    background: var(--danger);
    color: var(--white);
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 700;
    margin-left: auto;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
    }
}

.badge {
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
}

.bg-info {
    background: rgba(37, 99, 235, 0.1) !important;
    color: var(--primary) !important;
}

.bg-warning {
    background: rgba(245, 158, 11, 0.1) !important;
    color: var(--accent) !important;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray);
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #cbd5e1;
    opacity: 0.6;
}

.empty-state h5 {
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.25rem;
}

.empty-state p {
    color: var(--gray);
    font-size: 1rem;
}

/* Status Tags */
.status-tag {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}

.status-tag.refurbished {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .main-content {
        margin-left: 0;
        padding: 15px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .top-bar h1 {
        font-size: 1.5rem;
    }
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #f0f9ff;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-light);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary);
}
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h3><i class="fas fa-shield-alt me-2"></i>JORAKI</h3>
        <p>Admin Control Panel</p>
    </div>
    <div class="sidebar-menu">
        <div class="menu-item active" onclick="scrollToSection('stats')">
            <i class="fas fa-chart-line"></i>
            <span>Overview</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('reports')">
            <i class="fas fa-file-chart"></i>
            <span>Reports</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('pending')">
            <i class="fas fa-clock"></i>
            <span>Pending Items</span>
            <?php if (count($pending) > 0): ?>
            <span class="badge-count"><?php echo count($pending); ?></span>
            <?php endif; ?>
        </div>
        <div class="menu-item" onclick="scrollToSection('purchased')">
            <i class="fas fa-shopping-bag"></i>
            <span>Purchased</span>
            <?php if (count($purchased) > 0): ?>
            <span class="badge-count"><?php echo count($purchased); ?></span>
            <?php endif; ?>
        </div>
        <div class="menu-item" onclick="scrollToSection('refurbished')">
            <i class="fas fa-tools"></i>
            <span>Refurbished</span>
        </div>
        <div class="menu-item" onclick="scrollToSection('sold')">
            <i class="fas fa-check-circle"></i>
            <span>Sold Items</span>
        </div>
        <a href="logout.php" class="menu-item" style="margin-top: 30px; border-top: 2px solid #e0f2fe; padding-top: 20px;">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-report" onclick="window.print()">
                <i class="fas fa-file-pdf"></i>
                Generate Report
            </button>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <!-- Messages -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert-custom alert-success-custom">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($_GET['success']) ?></span>
        </div>
    <?php elseif (!empty($_GET['error'])): ?>
        <div class="alert-custom alert-danger-custom">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div id="stats" class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value"><?php echo count($pending); ?></div>
            <div class="stat-label">Pending Items</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-value"><?php echo count($purchased); ?></div>
            <div class="stat-label">Purchased Items</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-tools"></i>
            </div>
            <div class="stat-value"><?php echo count($refurbished); ?></div>
            <div class="stat-label">Refurbished Items</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value"><?php echo count($sold); ?></div>
            <div class="stat-label">Sold Items</div>
        </div>
    </div>

    <!-- Reports Section -->
    <div id="reports" class="content-card">
        <div class="card-header-custom">
            <h3><i class="fas fa-chart-bar"></i> Business Reports</h3>
        </div>
        <div class="card-body-custom">
            <div class="row g-4">
                <div class="col-md-4">
                    <div style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; padding: 30px; border-radius: 16px; text-align: center;">
                        <i class="fas fa-dollar-sign" style="font-size: 2.5rem; margin-bottom: 15px;"></i>
                        <div style="font-size: 2rem; font-weight: 800; margin-bottom: 5px;">
                            KSh <?php echo number_format($total_revenue); ?>
                        </div>
                        <div style="font-size: 0.95rem; opacity: 0.9;">Total Revenue</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 30px; border-radius: 16px; text-align: center;">
                        <i class="fas fa-chart-line" style="font-size: 2.5rem; margin-bottom: 15px;"></i>
                        <div style="font-size: 2rem; font-weight: 800; margin-bottom: 5px;">
                            KSh <?php echo number_format($avg_price); ?>
                        </div>
                        <div style="font-size: 0.95rem; opacity: 0.9;">Average Sale Price</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background: linear-gradient(135deg, var(--accent) 0%, #d97706 100%); color: white; padding: 30px; border-radius: 16px; text-align: center;">
                        <i class="fas fa-boxes" style="font-size: 2.5rem; margin-bottom: 15px;"></i>
                        <div style="font-size: 2rem; font-weight: 800; margin-bottom: 5px;">
                            <?php echo count($items); ?>
                        </div>
                        <div style="font-size: 0.95rem; opacity: 0.9;">Total Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Item Section -->
    <div class="content-card">
        <div class="card-body-custom">
            <div class="add-item-header">
                <h4><i class="fas fa-layer-group me-2" style="color: var(--primary);"></i>Items Management</h4>
                <button class="btn btn-dark" id="toggleAddItem">
                    <i class="fas fa-plus me-2"></i> Add New Item
                </button>
            </div>

            <div id="addItemForm">
                <h5><i class="fas fa-box-open"></i> New Item Details</h5>

                <form action="admin_action/add_item.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-list"></i> Category
                            </label>
                            <select name="category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option value="Car">Car</option>
                                <option value="Bike">Bike</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-info-circle"></i> Condition
                            </label>
                            <select name="condition_type" class="form-select" required>
                                <option value="">Select Condition</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="fas fa-align-left"></i> Description
                            </label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the item in detail..." required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Seller Name
                            </label>
                            <input type="text" name="seller_name" class="form-control" placeholder="Full name" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Seller Email
                            </label>
                            <input type="email" name="seller_email" class="form-control" placeholder="email@example.com" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Seller Phone
                            </label>
                            <input type="text" name="seller_phone" class="form-control" placeholder="+254..." required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">
                                <i class="fas fa-image"></i> Upload Image
                            </label>
                            <input type="file" name="image" accept="image/*" class="form-control">
                            <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-dark w-100" style="padding: 16px;">
                                <i class="fas fa-check-circle me-2"></i>Save Item
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById("toggleAddItem").addEventListener("click", function() {
        const form = document.getElementById("addItemForm");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
            this.innerHTML = '<i class="fas fa-minus me-2"></i> Hide Form';
        } else {
            form.style.display = "none";
            this.innerHTML = '<i class="fas fa-plus me-2"></i> Add New Item';
        }
    });
    </script>

<!-- Pending Items -->
<div id="pending" class="content-card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-clock"></i>
            Pending Items
        </h3>
    </div>

    <div class="card-body-custom">
        <?php
        $stmt = $pdo->query("SELECT * FROM items WHERE status IN ('pending', 'pending_purchase') ORDER BY created_at DESC");
        $pending_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (count($pending_items) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h5>No Pending Items</h5>
                <p>All submitted items have been reviewed.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Seller</th>
                            <th>Price</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_items as $item): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($item['id']) ?></strong></td>
                            <td>
                                <?php if (!empty($item['image']) && file_exists(__DIR__ . '/../uploads/' . $item['image'])): ?>
                                    <img class="item-thumb" src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
                                    <img class="item-thumb" src="https://via.placeholder.com/60" alt="No image">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= htmlspecialchars($item['category']) ?></td>
                            <td>
                                <?= htmlspecialchars($item['seller_name']) ?><br>
                                <small style="color: #999;"><?= htmlspecialchars($item['seller_email']) ?></small>
                            </td>
                            <td><strong>KSh <?= number_format($item['price']) ?></strong></td>
                            <td>
                                <?php if ($item['status'] === 'pending_purchase'): ?>
                                    <span class="badge bg-info">Purchase</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Sell</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="admin_action/approve_purchase.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="type" value="<?= $item['status'] === 'pending_purchase' ? 'purchase' : 'sell' ?>">
                                    <button type="submit" name="action" value="approve" class="action-btn btn-approve">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="submit" name="action" value="reject" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to reject this item?');">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Purchased Items -->
<div id="purchased" class="content-card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-shopping-bag"></i>
            Purchased (Awaiting Refurbishment)
        </h3>
    </div>

    <div class="card-body-custom">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM items WHERE status = 'purchased' ORDER BY updated_at DESC");
            $purchased = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Error fetching purchased items: ' . $e->getMessage());
            $purchased = [];
        }
        ?>

        <?php if (count($purchased) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-bag"></i>
                <h5>No Purchased Items</h5>
                <p>No items are currently awaiting refurbishment.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Seller</th>
                            <th>Purchased On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchased as $it): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($it['id']) ?></strong></td>
                            <td>
                                <?php if (!empty($it['image']) && file_exists(__DIR__ . '/../uploads/' . $it['image'])): ?>
                                    <img class="item-thumb" src="../uploads/<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['title']) ?>">
                                <?php else: ?>
                                    <img class="item-thumb" src="https://via.placeholder.com/60" alt="No image">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($it['title'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($it['category'] ?? '—') ?></td>
                            <td><strong>KSh <?= number_format($it['price'] ?? 0) ?></strong></td>
                            <td>
                                <?= htmlspecialchars($it['seller_name'] ?? '—') ?><br>
                                <small style="color: #888;"><?= htmlspecialchars($it['seller_email'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($it['approved_at'] ?? '—') ?></td>
                            <td>
                                <form method="POST" action="admin_action/mark_refurbished.php" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']) ?>">
                                    <button class="action-btn btn-refurb" type="submit" onclick="return confirm('Mark this item as refurbished?');">
                                        <i class="fas fa-tools"></i> Mark Refurbished
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Refurbished Items -->
<div id="refurbished" class="content-card">
    <div class="card-header-custom">
        <h3><i class="fas fa-tools"></i> Refurbished Items</h3>
    </div>
    <div class="card-body-custom">
        <?php if (count($refurbished) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-tools"></i>
                <h5>No Refurbished Items</h5>
                <p>No items ready for sale yet</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($refurbished as $it): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($it['id']) ?></strong></td>
                            <td><?= htmlspecialchars($it['title'] ?? '—') ?></td>
                            <td><strong>KSh <?= number_format($it['price'] ?? 0) ?></strong></td>
                            <td><span class="status-tag refurbished">Refurbished</span></td>
                            <td>
                                <form method="post" action="admin_action/mark_sold.php" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']) ?>">
                                    <button class="action-btn btn-sold" type="submit">
                                        <i class="fas fa-check-circle"></i> Mark Sold
                                    </button>
                                </form>
                                <form method="post" action="admin_action/delete_item.php" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']) ?>">
                                    <button class="action-btn btn-delete" type="submit">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Sold Items -->
<div id="sold" class="content-card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-check-circle"></i>
            Sold Items
        </h3>
    </div>
    <div class="card-body-custom">
        <?php if (count($sold) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h5>No Sold Items</h5>
                <p>No items have been sold yet</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Date Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sold as $it): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($it['id']) ?></strong></td>
                            <td><?= htmlspecialchars($it['title'] ?? 'Untitled') ?></td>
                            <td><strong>KSh <?= number_format($it['price'] ?? 0) ?></strong></td>
                            <td><?= date('M d, Y', strtotime($it['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Back to Main Site -->
<div class="content-card text-center">
    <div class="card-body-custom">
        <a href="../index.php" class="btn-logout btn-lg">
            <i class="fas fa-home"></i>
            Back to Main Site
        </a>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        document.querySelectorAll('.menu-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');
    }
}

setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.3s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.remove();
        }, 300);
    });
}, 5000);
</script>

</body>
</html>