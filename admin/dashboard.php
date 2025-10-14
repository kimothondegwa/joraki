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
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        background: #f5f5f5;
        color: #333;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 260px;
        height: 100vh;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar-brand {
        padding: 30px 25px;
        border-bottom: 1px solid #e0e0e0;
    }

    .sidebar-brand h3 {
        color: #333;
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .sidebar-brand p {
        color: #666;
        font-size: 0.85rem;
        margin: 0;
    }

    .sidebar-menu {
        padding: 20px 0;
    }

    .menu-item {
        padding: 12px 25px;
        color: #666;
        text-decoration: none;
        display: flex;
        align-items: center;
        transition: all 0.2s;
        cursor: pointer;
        border-left: 3px solid transparent;
    }

    .menu-item i {
        width: 25px;
        font-size: 1.1rem;
        margin-right: 12px;
        color: #999;
    }

    .menu-item:hover,
    .menu-item.active {
        background: #f8f8f8;
        color: #333;
        border-left-color: #333;
    }

    .menu-item:hover i,
    .menu-item.active i {
        color: #333;
    }

    /* Main Content */
    .main-content {
        margin-left: 260px;
        padding: 30px;
    }

    /* Top Bar */
    .top-bar {
        background: #fff;
        padding: 25px 30px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .top-bar h1 {
        color: #333;
        font-weight: 700;
        font-size: 1.75rem;
        margin: 0;
    }

    .top-bar p {
        color: #666;
        margin: 5px 0 0 0;
        font-size: 0.9rem;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: #fff;
        padding: 25px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        transition: all 0.2s;
    }

    .stat-card:hover {
        border-color: #ccc;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
        background: #f5f5f5;
        color: #666;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }

    /* Content Card */
    .content-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-header-custom {
        padding: 20px 30px;
        border-bottom: 1px solid #e0e0e0;
        background: #fafafa;
    }

    .card-header-custom h3 {
        margin: 0;
        color: #333;
        font-weight: 700;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-body-custom {
        padding: 30px;
    }

    /* Table Styles */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom thead {
        background: #f5f5f5;
    }

    .table-custom thead th {
        color: #333;
        font-weight: 600;
        padding: 15px;
        border-bottom: 2px solid #e0e0e0;
        font-size: 0.85rem;
        text-align: left;
    }

    .table-custom tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .table-custom tbody tr:hover {
        background: #fafafa;
    }

    .table-custom tbody td {
        padding: 15px;
        color: #555;
    }

    .item-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
    }

    /* Buttons */
    .action-btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.85rem;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .btn-approve {
        background: #fff;
        color: #2e7d32;
        border-color: #2e7d32;
    }

    .btn-approve:hover {
        background: #2e7d32;
        color: #fff;
    }

    .btn-refurb {
        background: #fff;
        color: #1976d2;
        border-color: #1976d2;
    }

    .btn-refurb:hover {
        background: #1976d2;
        color: #fff;
    }

    .btn-sold {
        background: #fff;
        color: #7b1fa2;
        border-color: #7b1fa2;
    }

    .btn-sold:hover {
        background: #7b1fa2;
        color: #fff;
    }

    .btn-delete {
        background: #fff;
        color: #d32f2f;
        border-color: #d32f2f;
    }

    .btn-delete:hover {
        background: #d32f2f;
        color: #fff;
    }

    .btn-logout {
        background: #333;
        color: #fff;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
    }

    .btn-logout:hover {
        background: #555;
        color: #fff;
    }

    /* Alert Styles */
    .alert-custom {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        border: 1px solid;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success-custom {
        background: #e8f5e9;
        color: #2e7d32;
        border-color: #2e7d32;
    }

    .alert-danger-custom {
        background: #ffebee;
        color: #d32f2f;
        border-color: #d32f2f;
    }

    /* Badge */
    .badge-count {
        background: #d32f2f;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: auto;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        color: #999;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .empty-state h5 {
        color: #666;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .empty-state p {
        color: #999;
        font-size: 0.9rem;
    }

    /* Form Styling */
    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px 12px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #333;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.05);
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .top-bar h1 {
            font-size: 1.5rem;
        }
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
        <a href="logout.php" class="menu-item" style="margin-top: 30px; border-top: 1px solid #e0e0e0; padding-top: 20px;">
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
        <div>
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

    <!-- Add New Item Section -->
    <div class="content-card mb-4">
        <div class="card-body-custom">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Items Management</h4>
                <button class="btn btn-dark btn-sm" id="toggleAddItem">
                    <i class="fas fa-plus me-2"></i> Add New Item
                </button>
            </div>

            <div id="addItemForm" class="mt-4 p-4" style="display:none; background:#fafafa; border-radius: 8px; border: 1px solid #e0e0e0;">
                <h5 class="mb-3">New Item Details</h5>

                <form action="admin_action/add_item.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Price (KSh)</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category</label>
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
                            <label class="form-label">Condition</label>
                            <select name="condition_type" class="form-select" required>
                                <option value="">Select Condition</option>
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Seller Name</label>
                            <input type="text" name="seller_name" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Seller Email</label>
                            <input type="email" name="seller_email" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Seller Phone</label>
                            <input type="text" name="seller_phone" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image" accept="image/*" class="form-control">
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-dark w-100">
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
                                    <?php if ($item['status'] === 'pending_purchase'): ?>
                                        <a href="admin_action/approve_purchase.php?id=<?= $item['id'] ?>" class="action-btn btn-approve">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                    <?php else: ?>
                                        <a href="admin_action/approve_item.php?id=<?= $item['id'] ?>" class="action-btn btn-approve">
                                            <i class="fas fa-check"></i> Approve
                                        </a>
                                    <?php endif; ?>
                                    <a href="admin_action/reject_item.php?id=<?= $item['id'] ?>" class="action-btn btn-delete" onclick="return confirm('Reject this item?');">
                                        <i class="fas fa-times"></i> Reject
                                    </a>
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
            <?php if (count($purchased) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h5>No Purchased Items</h5>
                    <p>No items awaiting refurbishment</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchased as $it): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($it['id']) ?></strong></td>
                                <td><?= htmlspecialchars($it['title'] ?? '—') ?></td>
                                <td><strong>KSh <?= number_format($it['price'] ?? 0) ?></strong></td>
                                <td>
                                    <form method="post" action="admin_action/mark_refurbished.php" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']) ?>">
                                        <button class="action-btn btn-refurb" type="submit">
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
            <h3>
                <i class="fas fa-tools"></i>
                Refurbished Items
            </h3>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($refurbished as $it): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($it['id']) ?></strong></td>
                                <td><?= htmlspecialchars($it['title'] ?? '—') ?></td>
                                <td><strong>KSh <?= number_format($it['price'] ?? 0) ?></strong></td>
                                <td>
                                    <form method="post" action="admin_action/mark_sold.php" style="display: inline;">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($it['id']) ?>">
                                        <button class="action-btn btn-sold" type="submit">
                                            <i class="fas fa-check-circle"></i> Mark Sold
                                        </button>
                                    </form>
                                    <form method="post" action="admin_action/delete_item.php" onsubmit="return confirm('Delete this item?');" style="display: inline;">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scroll to sections
    function scrollToSection(sectionId) {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Update active menu item
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
        }
    }

    // Auto-hide success/error messages after 5 seconds
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