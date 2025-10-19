<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sell Your Item - Joraki</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #667eea;
      --primary-dark: #5568d3;
      --secondary: #764ba2;
      --success: #38ef7d;
      --success-dark: #11998e;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 3rem 0;
      position: relative;
      overflow-x: hidden;
    }

    /* Animated background pattern */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
    }

    /* Main Card */
    .main-card {
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
      background: white;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px) scale(0.96);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    /* Card Header */
    .card-header-custom {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      padding: 3.5rem 2.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      border: none;
    }

    .card-header-custom::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
      animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .header-icon {
      width: 90px;
      height: 90px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      border: 3px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      position: relative;
      z-index: 1;
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-15px); }
    }

    .header-icon i {
      font-size: 2.75rem;
      color: white;
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .card-header-custom h3 {
      margin: 0;
      font-weight: 900;
      font-size: 2.25rem;
      text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      color: white;
      position: relative;
      z-index: 1;
      letter-spacing: -0.5px;
    }

    .card-header-custom p {
      margin: 0.75rem 0 0 0;
      opacity: 0.95;
      font-size: 1.05rem;
      color: rgba(255, 255, 255, 0.95);
      position: relative;
      z-index: 1;
      font-weight: 500;
    }

    /* Form Body */
    .card-body-custom {
      padding: 3rem 2.5rem;
    }

    /* Success Alert */
    .alert-success-custom {
      border-radius: 16px;
      border: none;
      background: linear-gradient(135deg, var(--success-dark) 0%, var(--success) 100%);
      color: white;
      font-weight: 600;
      padding: 1.5rem;
      animation: slideInDown 0.6s ease;
      display: flex;
      align-items: center;
      gap: 1rem;
      box-shadow: 0 8px 24px rgba(17, 153, 142, 0.3);
      margin-bottom: 2rem;
    }

    .alert-success-custom i {
      font-size: 1.75rem;
      flex-shrink: 0;
    }

    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Section Headers */
    .section-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 1.75rem;
      padding-bottom: 1rem;
      border-bottom: 3px solid #f1f5f9;
    }

    .section-header i {
      font-size: 1.5rem;
      color: var(--primary);
    }

    .section-header h5 {
      margin: 0;
      font-weight: 800;
      font-size: 1.375rem;
      color: #1e293b;
    }

    /* Section Divider */
    .section-divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 3rem 0 2.5rem;
      color: var(--primary);
      font-weight: 700;
      font-size: 1.125rem;
    }

    .section-divider::before,
    .section-divider::after {
      content: '';
      flex: 1;
      border-bottom: 3px solid #e2e8f0;
    }

    .section-divider span {
      padding: 0 1.5rem;
      background: white;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Form Groups */
    .form-group-custom {
      margin-bottom: 1.75rem;
    }

    .form-label-custom {
      color: #334155;
      font-weight: 700;
      margin-bottom: 0.75rem;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9375rem;
      letter-spacing: 0.02em;
    }

    .form-label-custom i {
      color: var(--primary);
      font-size: 1.125rem;
    }

    .form-label-custom .required {
      color: #ef4444;
      margin-left: 4px;
    }

    /* Form Controls */
    .form-control-custom,
    .form-select-custom {
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      padding: 0.875rem 1rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      font-weight: 500;
      background: #f8fafc;
      color: #1e293b;
    }

    .form-control-custom:focus,
    .form-select-custom:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
      background: white;
      outline: none;
    }

    .form-control-custom::placeholder {
      color: #94a3b8;
      font-weight: 400;
    }

    textarea.form-control-custom {
      resize: vertical;
      min-height: 140px;
    }

    /* Price Input */
    .price-input-wrapper {
      position: relative;
    }

    .currency-prefix {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
      font-weight: 800;
      font-size: 1.125rem;
      pointer-events: none;
      z-index: 2;
    }

    .price-input {
      padding-left: 3.5rem;
      font-weight: 700;
      font-size: 1.125rem;
    }

    /* Helper Text */
    .form-helper {
      display: flex;
      align-items: center;
      gap: 6px;
      color: #64748b;
      font-size: 0.8125rem;
      margin-top: 0.5rem;
      font-weight: 500;
    }

    .form-helper i {
      color: var(--primary);
      font-size: 0.875rem;
    }

    /* Image Upload Area */
    .image-upload-container {
      border: 3px dashed var(--primary);
      border-radius: 16px;
      padding: 3rem 2rem;
      text-align: center;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .image-upload-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .image-upload-container:hover {
      border-color: var(--secondary);
      background: linear-gradient(135deg, #e0f2fe 0%, #ddd6fe 100%);
      transform: scale(1.02);
    }

    .image-upload-container:hover::before {
      opacity: 1;
    }

    .upload-icon {
      width: 80px;
      height: 80px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.25rem;
      box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
      position: relative;
      z-index: 1;
    }

    .upload-icon i {
      font-size: 2.25rem;
      color: var(--primary);
    }

    .upload-text {
      position: relative;
      z-index: 1;
    }

    .upload-text h6 {
      font-weight: 800;
      color: #1e293b;
      margin-bottom: 0.5rem;
      font-size: 1.125rem;
    }

    .upload-text p {
      color: #64748b;
      margin: 0;
      font-weight: 500;
    }

    .file-uploaded {
      background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
      border-color: var(--success);
    }

    .file-uploaded .upload-icon {
      background: var(--success);
    }

    .file-uploaded .upload-icon i {
      color: white;
    }

    /* Info Badge */
    .info-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
      color: var(--primary);
      padding: 0.625rem 1.25rem;
      border-radius: 50px;
      font-size: 0.875rem;
      margin-top: 1rem;
      font-weight: 700;
      border: 2px solid rgba(102, 126, 234, 0.2);
    }

    .info-badge i {
      font-size: 1rem;
    }

    /* Buttons */
    .btn-submit {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border: none;
      border-radius: 14px;
      padding: 1.125rem 3rem;
      font-weight: 800;
      font-size: 1.125rem;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
      position: relative;
      overflow: hidden;
    }

    .btn-submit::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .btn-submit:hover::before {
      opacity: 1;
    }

    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
      color: white;
    }

    .btn-submit span {
      position: relative;
      z-index: 1;
    }

    .btn-home {
      background: white;
      color: var(--primary);
      border: 3px solid var(--primary);
      border-radius: 14px;
      padding: 1.0625rem 3rem;
      font-weight: 800;
      font-size: 1.125rem;
      transition: all 0.3s ease;
    }

    .btn-home:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    /* Bottom Info */
    .bottom-info {
      margin-top: 2.5rem;
      padding: 1.25rem;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      border-radius: 12px;
      text-align: center;
      border: 2px solid rgba(102, 126, 234, 0.1);
    }

    .bottom-info p {
      margin: 0;
      color: #475569;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .bottom-info i {
      color: var(--primary);
      font-size: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
      body {
        padding: 2rem 0;
      }

      .card-header-custom {
        padding: 2.5rem 1.5rem;
      }

      .card-header-custom h3 {
        font-size: 1.75rem;
      }

      .card-body-custom {
        padding: 2rem 1.5rem;
      }

      .header-icon {
        width: 75px;
        height: 75px;
      }

      .header-icon i {
        font-size: 2.25rem;
      }

      .image-upload-container {
        padding: 2rem 1.5rem;
      }

      .btn-submit,
      .btn-home {
        width: 100%;
        padding: 1rem 2rem;
      }
    }
  </style>
</head>

<body>
<div class="container py-4">
  <div class="card border-0 mx-auto main-card" style="max-width: 850px;">
    
    <!-- Card Header -->
    <div class="card-header-custom">
      <div class="header-icon">
        <i class="fas fa-store"></i>
      </div>
      <h3>Sell Your Item</h3>
      <p>List your item and reach thousands of potential buyers</p>
    </div>

    <!-- Card Body -->
    <div class="card-body-custom">

      <!-- Success Message -->
      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div id="successMsg" class="alert-success-custom">
          <i class="fas fa-check-circle"></i>
          <div>
            <strong>Success!</strong> Your item has been submitted and is pending review.
          </div>
        </div>
      <?php endif; ?>

      <!-- Sell Item Form -->
      <form action="process_sell.php" method="POST" enctype="multipart/form-data" id="sellForm">
        
        <!-- Item Details Section -->
        <div class="section-header">
          <i class="fas fa-box"></i>
          <h5>Item Details</h5>
        </div>

        <div class="form-group-custom">
          <label class="form-label-custom">
            <i class="fas fa-tag"></i>
            Item Title
            <span class="required">*</span>
          </label>
          <input 
            type="text" 
            name="title" 
            class="form-control form-control-custom" 
            placeholder="e.g., iPhone 13 Pro Max 256GB" 
            required>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-list"></i>
                Category
                <span class="required">*</span>
              </label>
              <select name="category" class="form-select form-select-custom" required>
                <option value="">Select Category</option>
                <option value="Car">🚗 Car</option>
                <option value="Bike">🏍️ Bike</option>
                <option value="Electronics">📱 Electronics</option>
                <option value="Furniture">🛋️ Furniture</option>
                <option value="Other">📦 Other</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-star"></i>
                Condition
                <span class="required">*</span>
              </label>
              <select name="condition_type" class="form-select form-select-custom" required>
                <option value="new">✨ Brand New</option>
                <option value="used">🔄 Used</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group-custom">
          <label class="form-label-custom">
            <i class="fas fa-align-left"></i>
            Description
            <span class="required">*</span>
          </label>
          <textarea 
            name="description" 
            class="form-control form-control-custom" 
            rows="5" 
            placeholder="Describe your item in detail - condition, features, reason for selling, any defects, etc." 
            required></textarea>
          <div class="form-helper">
            <i class="fas fa-info-circle"></i>
            Detailed descriptions attract more buyers and build trust
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-money-bill-wave"></i>
                Selling Price
                <span class="required">*</span>
              </label>
              <div class="price-input-wrapper">
                <span class="currency-prefix">KSh</span>
                <input 
                  type="number" 
                  name="price" 
                  class="form-control form-control-custom price-input" 
                  placeholder="0" 
                  min="1"
                  required>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-receipt"></i>
                Original Price
                <small class="text-muted">(Optional)</small>
              </label>
              <div class="price-input-wrapper">
                <span class="currency-prefix">KSh</span>
                <input 
                  type="number" 
                  name="original_price" 
                  class="form-control form-control-custom price-input" 
                  placeholder="0"
                  min="0">
              </div>
              <div class="form-helper">
                <i class="fas fa-percentage"></i>
                Helps buyers see the discount
              </div>
            </div>
          </div>
        </div>

        <div class="form-group-custom">
          <label class="form-label-custom">
            <i class="fas fa-camera"></i>
            Item Photo
            <span class="required">*</span>
          </label>
          <div class="image-upload-container" id="uploadArea" onclick="document.getElementById('fileInput').click();">
            <div class="upload-icon">
              <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div class="upload-text">
              <h6>Click to upload image</h6>
              <p>PNG, JPG or JPEG • Max 5MB</p>
            </div>
          </div>
          <input 
            type="file" 
            id="fileInput" 
            name="image" 
            class="d-none" 
            accept="image/*" 
            required>
          <span class="info-badge">
            <i class="fas fa-lightbulb"></i>
            Clear, well-lit photos get 3x more views!
          </span>
        </div>

        <!-- Contact Information Section -->
        <div class="section-divider">
          <span>
            <i class="fas fa-user-circle"></i>
            Your Contact Information
          </span>
        </div>

        <div class="row">
          <div class="col-md-4">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-user"></i>
                Full Name
                <span class="required">*</span>
              </label>
              <input 
                type="text" 
                name="seller_name" 
                class="form-control form-control-custom" 
                placeholder="John Doe" 
                required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-envelope"></i>
                Email
                <span class="required">*</span>
              </label>
              <input 
                type="email" 
                name="seller_email" 
                class="form-control form-control-custom" 
                placeholder="john@example.com" 
                required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group-custom">
              <label class="form-label-custom">
                <i class="fas fa-phone"></i>
                Phone
                <span class="required">*</span>
              </label>
              <input 
                type="tel" 
                name="seller_phone" 
                class="form-control form-control-custom" 
                placeholder="0712345678" 
                pattern="[0-9]{10}"
                required>
            </div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="text-center mt-5 d-flex flex-column flex-md-row justify-content-center gap-3">
          <button type="submit" class="btn btn-submit">
            <span>
              <i class="fas fa-paper-plane me-2"></i>
              Submit Item for Review
            </span>
          </button>
          <a href="../index.php" class="btn btn-home">
            <i class="fas fa-home me-2"></i>
            Back to Home
          </a>
        </div>

        <!-- Bottom Info -->
        <div class="bottom-info">
          <p>
            <i class="fas fa-shield-alt"></i>
            Your listing will be reviewed within 24 hours
          </p>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Auto-hide success message
  setTimeout(() => {
    const msg = document.getElementById('successMsg');
    if (msg) {
      msg.style.opacity = '0';
      msg.style.transition = 'opacity 0.5s ease';
      setTimeout(() => msg.remove(), 500);
    }
  }, 5000);

  // File upload preview
  const fileInput = document.getElementById('fileInput');
  const uploadArea = document.getElementById('uploadArea');

  fileInput.addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
      const file = e.target.files[0];
      const fileName = file.name;
      const fileSize = (file.size / 1024 / 1024).toFixed(2);
      
      uploadArea.classList.add('file-uploaded');
      uploadArea.innerHTML = `
        <div class="upload-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="upload-text">
          <h6 style="color: var(--success-dark);">File Uploaded Successfully!</h6>
          <p><strong>${fileName}</strong> (${fileSize} MB)</p>
        </div>
      `;
    }
  });

  // Form validation feedback
  const form = document.getElementById('sellForm');
  form.addEventListener('submit', function(e) {
    const submitBtn = form.querySelector('.btn-submit');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin me-2"></i>Submitting...</span>';
  });

  // Add floating animation to inputs on focus
  document.querySelectorAll('.form-control-custom, .form-select-custom').forEach(input => {
    input.addEventListener('focus', function() {
      this.style.transform = 'scale(1.01)';
      this.style.transition = 'transform 0.2s ease';
    });
    
    input.addEventListener('blur', function() {
      this.style.transform = 'scale(1)';
    });
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>