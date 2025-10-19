<?php
// buy/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $condition_type = trim($_POST['condition'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $contact_name = trim($_POST['contact_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    
    // Validation
    if (empty($title) || empty($description) || empty($category) || empty($condition_type) || $price <= 0) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Handle multiple image uploads
        $uploaded_images = [];
        $upload_dir = __DIR__ . '/../uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if (!empty($_FILES['images']['name'][0])) {
            $max_images = 5;
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            $max_file_size = 5 * 1024 * 1024; // 5MB
            
            for ($i = 0; $i < min(count($_FILES['images']['name']), $max_images); $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file_type = $_FILES['images']['type'][$i];
                    $file_size = $_FILES['images']['size'][$i];
                    
                    if (!in_array($file_type, $allowed_types)) {
                        $error_message = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
                        break;
                    }
                    
                    if ($file_size > $max_file_size) {
                        $error_message = 'File size too large. Maximum 5MB per image.';
                        break;
                    }
                    
                    $extension = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                    $filename = uniqid('item_') . '_' . $i . '.' . $extension;
                    $filepath = $upload_dir . $filename;
                    
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $filepath)) {
                        $uploaded_images[] = $filename;
                    }
                }
            }
        }
        
        if (empty($error_message)) {
            try {
                // Store as JSON for multiple images
                $images_json = json_encode($uploaded_images);
                $main_image = !empty($uploaded_images) ? $uploaded_images[0] : null;
                
                $sql = "INSERT INTO items (title, description, category, condition_type, price, image, images, contact_name, contact_email, contact_phone, status, created_at) 
                        VALUES (:title, :description, :category, :condition_type, :price, :image, :images, :contact_name, :contact_email, :contact_phone, 'pending', NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':category' => $category,
                    ':condition_type' => $condition_type,
                    ':price' => $price,
                    ':image' => $main_image,
                    ':images' => $images_json,
                    ':contact_name' => $contact_name,
                    ':contact_email' => $contact_email,
                    ':contact_phone' => $contact_phone
                ]);
                
                $success_message = 'Your item has been submitted successfully! We will review it shortly.';
                
                // Clear form
                $_POST = [];
            } catch (Exception $e) {
                $error_message = 'An error occurred. Please try again.';
            }
        }
    }
}

// Get categories for dropdown
try {
    $categories = $pdo->query("SELECT DISTINCT category FROM items WHERE category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $categories = ['Electronics', 'Furniture', 'Appliances', 'Tools', 'Sports', 'Other'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sell Your Item - <?= htmlspecialchars(SITE_NAME) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1e40af;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Hero Section */
        .sell-hero {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 100px 0 60px;
            margin-bottom: 60px;
            position: relative;
            overflow: hidden;
        }

        .sell-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%232563eb' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.4;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(37, 99, 235, 0.1);
            border: 2px solid rgba(37, 99, 235, 0.2);
            color: var(--primary);
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 24px;
            animation: fadeInDown 0.6s ease;
        }

        .sell-title {
            font-size: 4rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.2;
            animation: fadeInUp 0.6s ease;
        }

        .sell-title i {
            color: var(--primary);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .sell-subtitle {
            font-size: 1.5rem;
            color: var(--gray);
            font-weight: 500;
            max-width: 700px;
            margin: 0 auto 40px;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        .hero-features {
            display: flex;
            gap: 40px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
            animation: fadeInUp 0.6s ease 0.4s backwards;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 28px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .feature-text {
            text-align: left;
        }

        .feature-text h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0;
        }

        .feature-text p {
            font-size: 0.875rem;
            color: var(--gray);
            margin: 0;
        }

        /* Main Form Container */
        .form-container {
            max-width: 900px;
            margin: 0 auto 80px;
            padding: 0 15px;
        }

        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: slideUp 0.6s ease;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 40px;
            text-align: center;
            color: white;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .form-header p {
            font-size: 1.125rem;
            opacity: 0.95;
        }

        .form-body {
            padding: 50px 40px;
        }

        .form-section {
            margin-bottom: 50px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--light);
        }

        .section-title i {
            color: var(--primary);
            font-size: 1.75rem;
        }

        .section-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 1.125rem;
        }

        .form-group-modern {
            margin-bottom: 28px;
        }

        .form-label-modern {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .form-label-modern i {
            color: var(--primary);
        }

        .required-star {
            color: var(--danger);
            margin-left: 2px;
        }

        .form-control-modern,
        .form-select-modern {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            background: var(--light);
            transition: all 0.3s;
            color: var(--dark);
        }

        .form-control-modern:focus,
        .form-select-modern:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            background: white;
        }

        textarea.form-control-modern {
            min-height: 140px;
            resize: vertical;
        }

        /* Image Upload Section */
        .image-upload-container {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 40px;
            border-radius: 16px;
            border: 2px dashed var(--primary);
            text-align: center;
            transition: all 0.3s;
            position: relative;
        }

        .image-upload-container:hover {
            border-color: var(--primary-dark);
            background: linear-gradient(135deg, #e0f2fe 0%, #bfdbfe 100%);
        }

        .upload-icon-wrapper {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15);
        }

        .upload-icon-wrapper i {
            font-size: 3rem;
            color: var(--primary);
        }

        .upload-text h4 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .upload-text p {
            font-size: 1rem;
            color: var(--gray);
            margin-bottom: 8px;
        }

        .upload-text .upload-limit {
            font-size: 0.875rem;
            color: var(--primary);
            font-weight: 600;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-top: 24px;
        }

        .btn-upload {
            background: var(--primary);
            color: white;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.125rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }

        .btn-upload:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* Image Preview Grid */
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .preview-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            background: var(--light);
            border: 2px solid #e2e8f0;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-item .remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 32px;
            height: 32px;
            background: var(--danger);
            border: 2px solid white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 0.875rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .preview-item .remove-btn:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        .preview-item .main-badge {
            position: absolute;
            bottom: 8px;
            left: 8px;
            background: var(--success);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Alert Messages */
        .alert-modern {
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 16px;
            font-weight: 600;
            animation: slideDown 0.4s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid var(--success);
            color: #065f46;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid var(--danger);
            color: #991b1b;
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .alert-success .alert-icon {
            background: var(--success);
            color: white;
        }

        .alert-danger .alert-icon {
            background: var(--danger);
            color: white;
        }

        /* Form Grid */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* Submit Button */
        .btn-submit-modern {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.25rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s;
            margin-top: 40px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
        }

        .btn-submit-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.4);
        }

        .btn-submit-modern:active {
            transform: translateY(-1px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sell-title {
                font-size: 2.5rem;
            }

            .sell-subtitle {
                font-size: 1.125rem;
            }

            .hero-features {
                gap: 20px;
            }

            .form-body {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .image-upload-container {
                padding: 30px 20px;
            }

            .image-preview-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 576px) {
            .sell-hero {
                padding: 70px 0 40px;
            }

            .sell-title {
                font-size: 2rem;
            }

            .form-header {
                padding: 30px 20px;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .btn-upload {
                padding: 14px 28px;
                font-size: 1rem;
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Hero Section -->
<div class="sell-hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-tags"></i>
                <span>Start Selling Today</span>
            </div>
            <h1 class="sell-title">
                <i class="fas fa-hand-holding-usd"></i> Sell Your Item
            </h1>
            <p class="sell-subtitle">Turn your unused items into cash quickly and easily. List your item in minutes!</p>
            
            <div class="hero-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Quick Process</h4>
                        <p>List in 5 minutes</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Secure Platform</h4>
                        <p>Safe transactions</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Wide Reach</h4>
                        <p>1000+ buyers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<main class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h2>List Your Item</h2>
            <p>Fill in the details below to get started</p>
        </div>

        <div class="form-body">
            <?php if (!empty($success_message)): ?>
                <div class="alert-modern alert-success">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div><?= htmlspecialchars($success_message) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert-modern alert-danger">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div><?= htmlspecialchars($error_message) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="sellForm">
                
                <!-- Section 1: Item Details -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-number">1</div>
                        <i class="fas fa-info-circle"></i>
                        <span>Item Details</span>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-heading"></i>
                            Item Title
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            class="form-control-modern" 
                            placeholder="e.g., iPhone 13 Pro Max 256GB"
                            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-align-left"></i>
                            Description
                            <span class="required-star">*</span>
                        </label>
                        <textarea 
                            name="description" 
                            class="form-control-modern" 
                            placeholder="Describe your item in detail..."
                            required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-tag"></i>
                                Category
                                <span class="required-star">*</span>
                            </label>
                            <select name="category" class="form-select-modern" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-check-circle"></i>
                                Condition
                                <span class="required-star">*</span>
                            </label>
                            <select name="condition" class="form-select-modern" required>
                                <option value="">Select Condition</option>
                                <option value="excellent" <?= ($_POST['condition'] ?? '') === 'excellent' ? 'selected' : '' ?>>Excellent</option>
                                <option value="good" <?= ($_POST['condition'] ?? '') === 'good' ? 'selected' : '' ?>>Good</option>
                                <option value="fair" <?= ($_POST['condition'] ?? '') === 'fair' ? 'selected' : '' ?>>Fair</option>
                                <option value="poor" <?= ($_POST['condition'] ?? '') === 'poor' ? 'selected' : '' ?>>Poor</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-money-bill-wave"></i>
                            Price (KSh)
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="price" 
                            class="form-control-modern" 
                            placeholder="Enter price"
                            value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                            min="0"
                            step="0.01"
                            required>
                    </div>
                </div>

                <!-- Section 2: Images -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-number">2</div>
                        <i class="fas fa-images"></i>
                        <span>Upload Images (Up to 5)</span>
                    </div>

                    <div class="image-upload-container">
                        <div class="upload-icon-wrapper">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            <h4>Add Product Images</h4>
                            <p>Upload up to 5 high-quality images of your item</p>
                            <p class="upload-limit">Maximum 5MB per image • JPG, PNG, GIF, WebP</p>
                        </div>
                        <div class="file-input-wrapper">
                            <button type="button" class="btn-upload">
                                <i class="fas fa-plus-circle"></i>
                                Choose Images
                            </button>
                            <input 
                                type="file" 
                                name="images[]" 
                                id="imageInput" 
                                accept="image/*" 
                                multiple>
                        </div>
                    </div>

                    <div class="image-preview-grid" id="imagePreview"></div>
                </div>

                <!-- Section 3: Contact Information -->
                <div class="form-section">
                    <div class="section-title">
                        <div class="section-number">3</div>
                        <i class="fas fa-address-card"></i>
                        <span>Contact Information</span>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-user"></i>
                            Your Name
                            <span class="required-star">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="contact_name" 
                            class="form-control-modern" 
                            placeholder="Enter your full name"
                            value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>"
                            required>
                    </div>

                    <div class="form-row">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-envelope"></i>
                                Email Address
                                <span class="required-star">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="contact_email" 
                                class="form-control-modern" 
                                placeholder="your.email@example.com"
                                value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-phone"></i>
                                Phone Number
                                <span class="required-star">*</span>
                            </label>
                            <input 
                                type="tel" 
                                name="contact_phone" 
                                class="form-control-modern" 
                                placeholder="+254 7XX XXX XXX"
                                value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>"
                                required>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit-modern">
                    <i class="fas fa-paper-plane"></i>
                    Submit Your Item
                </button>

            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image Preview Functionality
const imageInput = document.getElementById('imageInput');
const imagePreview = document.getElementById('imagePreview');
const maxImages = 5;
let selectedFiles = [];

imageInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    
    // Limit to max images
    if (selectedFiles.length + files.length > maxImages) {
        alert(`You can only upload up to ${maxImages} images`);
        return;
    }
    
    files.forEach((file, index) => {
        if (selectedFiles.length >= maxImages) return;
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('Please select only image files');
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Each image must be less than 5MB');
            return;
        }
        
        selectedFiles.push(file);
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${event.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${selectedFiles.length - 1})">
                    <i class="fas fa-times"></i>
                </button>
                ${selectedFiles.length === 1 ? '<span class="main-badge"><i class="fas fa-star"></i> Main</span>' : ''}
            `;
            imagePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
    
    updateFileInput();
});

function removeImage(index) {
    selectedFiles.splice(index, 1);
    renderPreviews();
    updateFileInput();
}

function renderPreviews() {
    imagePreview.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${event.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${index})">
                    <i class="fas fa-times"></i>
                </button>
                ${index === 0 ? '<span class="main-badge"><i class="fas fa-star"></i> Main</span>' : ''}
            `;
            imagePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    imageInput.files = dataTransfer.files;
}

// Form Animation on Submit
const form = document.getElementById('sellForm');
form.addEventListener('submit', function(e) {
    const submitBtn = form.querySelector('.btn-submit-modern');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
});

// Smooth scroll to form on page load if there's an error
window.addEventListener('load', function() {
    const alerts = document.querySelectorAll('.alert-modern');
    if (alerts.length > 0) {
        alerts[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Real-time price formatting
const priceInput = document.querySelector('input[name="price"]');
if (priceInput) {
    priceInput.addEventListener('input', function(e) {
        let value = e.target.value;
        // Remove non-numeric characters except decimal point
        value = value.replace(/[^\d.]/g, '');
        // Ensure only one decimal point
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        e.target.value = value;
    });
}

// Character counter for description
const descriptionTextarea = document.querySelector('textarea[name="description"]');
if (descriptionTextarea) {
    const counterDiv = document.createElement('div');
    counterDiv.style.cssText = 'text-align: right; margin-top: 8px; font-size: 0.875rem; color: var(--gray);';
    descriptionTextarea.parentElement.appendChild(counterDiv);
    
    function updateCounter() {
        const length = descriptionTextarea.value.length;
        counterDiv.innerHTML = `<i class="fas fa-keyboard"></i> ${length} characters`;
        if (length < 50) {
            counterDiv.style.color = 'var(--danger)';
            counterDiv.innerHTML += ' (minimum 50 recommended)';
        } else if (length > 500) {
            counterDiv.style.color = 'var(--accent)';
        } else {
            counterDiv.style.color = 'var(--success)';
        }
    }
    
    descriptionTextarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Phone number formatting
const phoneInput = document.querySelector('input[name="contact_phone"]');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('254')) {
            value = '+' + value;
        } else if (value.startsWith('0')) {
            value = '+254' + value.substring(1);
        } else if (value.startsWith('7') || value.startsWith('1')) {
            value = '+254' + value;
        }
        e.target.value = value;
    });
}

// Form validation feedback
const formInputs = document.querySelectorAll('.form-control-modern, .form-select-modern');
formInputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value.trim() === '' && this.hasAttribute('required')) {
            this.style.borderColor = 'var(--danger)';
        } else {
            this.style.borderColor = 'var(--success)';
        }
    });
    
    input.addEventListener('focus', function() {
        this.style.borderColor = 'var(--primary)';
    });
});

// Drag and drop for image upload
const uploadContainer = document.querySelector('.image-upload-container');

uploadContainer.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--primary-dark)';
    this.style.background = 'linear-gradient(135deg, #bfdbfe 0%, #93c5fd 100%)';
});

uploadContainer.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--primary)';
    this.style.background = 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)';
});

uploadContainer.addEventListener('drop', function(e) {
    e.preventDefault();
    this.style.borderColor = 'var(--primary)';
    this.style.background = 'linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%)';
    
    const files = Array.from(e.dataTransfer.files);
    
    if (selectedFiles.length + files.length > maxImages) {
        alert(`You can only upload up to ${maxImages} images`);
        return;
    }
    
    files.forEach(file => {
        if (selectedFiles.length >= maxImages) return;
        
        if (!file.type.match('image.*')) {
            alert('Please drop only image files');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            alert('Each image must be less than 5MB');
            return;
        }
        
        selectedFiles.push(file);
        
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${event.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${selectedFiles.length - 1})">
                    <i class="fas fa-times"></i>
                </button>
                ${selectedFiles.length === 1 ? '<span class="main-badge"><i class="fas fa-star"></i> Main</span>' : ''}
            `;
            imagePreview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
    
    updateFileInput();
});

// Success message auto-hide
const successAlert = document.querySelector('.alert-success');
if (successAlert) {
    setTimeout(() => {
        successAlert.style.animation = 'fadeOut 0.5s ease';
        setTimeout(() => {
            successAlert.style.display = 'none';
        }, 500);
    }, 5000);
}

// Add fade out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-20px);
        }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>