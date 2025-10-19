<?php
// admin/index.php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login - ResellX</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --primary-light: #818cf8;
      --secondary: #8b5cf6;
      --success: #10b981;
      --danger: #ef4444;
      --dark: #1e293b;
      --light: #f8fafc;
      --border: #e2e8f0;
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
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Animated gradient background */
    body::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.12) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
      animation: drift 20s ease-in-out infinite;
    }

    @keyframes drift {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      33% { transform: translate(30px, -50px) rotate(120deg); }
      66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    .login-wrapper {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 480px;
      padding: 1.5rem;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.3),
        0 0 0 1px rgba(255, 255, 255, 0.3) inset;
      overflow: hidden;
      animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideUp {
      from { 
        opacity: 0; 
        transform: translateY(30px) scale(0.96);
      }
      to { 
        opacity: 1; 
        transform: translateY(0) scale(1);
      }
    }

    .card-header-custom {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      padding: 3rem 2.5rem 2.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .card-header-custom::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
    }

    .logo-icon {
      width: 90px;
      height: 90px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 0 0 10px rgba(255, 255, 255, 0.1);
      animation: float 3s ease-in-out infinite;
      position: relative;
      z-index: 1;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    .logo-icon i {
      font-size: 2.75rem;
      color: white;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .card-header-custom h1 {
      color: white;
      font-weight: 700;
      font-size: 1.95rem;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 1;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .card-header-custom p {
      color: rgba(255, 255, 255, 0.9);
      font-size: 0.95rem;
      margin: 0;
      position: relative;
      z-index: 1;
      font-weight: 400;
    }

    .card-body-custom {
      padding: 2.5rem;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
      border: 1px solid #fecaca;
      border-left: 4px solid var(--danger);
      border-radius: 12px;
      color: #991b1b;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: start;
      gap: 0.75rem;
      animation: shake 0.4s ease;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-8px); }
      75% { transform: translateX(8px); }
    }

    .alert-danger i {
      font-size: 1.25rem;
      margin-top: 0.1rem;
      flex-shrink: 0;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      color: var(--dark);
      font-weight: 600;
      margin-bottom: 0.6rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      letter-spacing: 0.01em;
    }

    .form-label i {
      color: var(--primary);
      font-size: 0.875rem;
    }

    .input-wrapper {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 1.1rem;
      z-index: 2;
      pointer-events: none;
      transition: color 0.3s ease;
    }

    .form-control {
      border: 2px solid var(--border);
      border-radius: 12px;
      padding: 0.875rem 1rem 0.875rem 3rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: var(--light);
      font-weight: 500;
      color: var(--dark);
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
      background: white;
      outline: none;
    }

    .form-control:focus ~ .input-icon {
      color: var(--primary);
    }

    .form-control::placeholder {
      color: #cbd5e1;
      font-weight: 400;
    }

    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 0.5rem;
      z-index: 2;
      transition: all 0.3s ease;
      border-radius: 6px;
    }

    .password-toggle:hover {
      color: var(--primary);
      background: rgba(99, 102, 241, 0.1);
    }

    .options-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 1.25rem 0 1.75rem;
      font-size: 0.875rem;
    }

    .form-check {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-check-input {
      width: 20px;
      height: 20px;
      border: 2px solid var(--border);
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .form-check-input:checked {
      background-color: var(--primary);
      border-color: var(--primary);
    }

    .form-check-input:focus {
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-check-label {
      color: #64748b;
      cursor: pointer;
      margin: 0;
      font-weight: 500;
      user-select: none;
    }

    .forgot-link {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      padding: 0.25rem;
      border-radius: 4px;
    }

    .forgot-link:hover {
      color: var(--primary-dark);
      background: rgba(99, 102, 241, 0.05);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      border: none;
      width: 100%;
      padding: 1rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
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

    .btn-primary:hover::before {
      opacity: 1;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(99, 102, 241, 0.5);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-primary span {
      position: relative;
      z-index: 1;
    }

    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 1.75rem 0;
      color: #94a3b8;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid var(--border);
    }

    .divider span {
      padding: 0 1rem;
      background: white;
    }

    .btn-outline-secondary {
      border: 2px solid var(--border);
      border-radius: 12px;
      color: #64748b;
      background: white;
      padding: 0.875rem 1rem;
      font-weight: 600;
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    .btn-outline-secondary:hover {
      background: var(--light);
      border-color: #cbd5e1;
      color: var(--dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .security-footer {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.625rem;
      margin-top: 1.75rem;
      padding: 1rem;
      background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
      border-radius: 12px;
      font-size: 0.8125rem;
      color: #166534;
      font-weight: 500;
      border: 1px solid #bbf7d0;
    }

    .security-footer i {
      color: var(--success);
      font-size: 1.125rem;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
      .login-wrapper {
        padding: 1rem;
      }

      .card-header-custom {
        padding: 2.5rem 1.5rem 2rem;
      }

      .card-body-custom {
        padding: 2rem 1.5rem;
      }
      
      .logo-icon {
        width: 80px;
        height: 80px;
      }
      
      .logo-icon i {
        font-size: 2.25rem;
      }

      .card-header-custom h1 {
        font-size: 1.75rem;
      }

      .options-row {
        flex-direction: column;
        gap: 0.75rem;
        align-items: stretch;
      }
    }

    /* Loading state */
    .btn-primary:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

  <div class="login-wrapper">
    <div class="login-card">
      <div class="card-header-custom">
        <div class="logo-icon">
          <i class="fas fa-shield-halved"></i>
        </div>
        <h1>Admin Portal</h1>
        <p>Secure access to your dashboard</p>
      </div>

      <div class="card-body-custom">
        <?php if (!empty($_GET['error'])): ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($_GET['error']) ?></span>
          </div>
        <?php endif; ?>

        <form action="process.php" method="POST" id="loginForm">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
          
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-envelope"></i>
              Email Address
            </label>
            <div class="input-wrapper">
              <i class="input-icon fas fa-user"></i>
              <input 
                type="email" 
                name="email" 
                class="form-control" 
                placeholder="admin@example.com" 
                required 
                autofocus
                autocomplete="email">
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">
              <i class="fas fa-lock"></i>
              Password
            </label>
            <div class="input-wrapper">
              <i class="input-icon fas fa-key"></i>
              <input 
                type="password" 
                id="password" 
                name="password" 
                class="form-control" 
                placeholder="Enter your password" 
                required
                autocomplete="current-password">
              <button type="button" class="password-toggle" onclick="togglePassword()" aria-label="Toggle password visibility">
                <i class="fas fa-eye" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <div class="options-row">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
              <label class="form-check-label" for="rememberMe">
                Remember me
              </label>
            </div>
            <a href="#" class="forgot-link">Forgot password?</a>
          </div>

          <button type="submit" class="btn btn-primary">
            <span>
              <i class="fas fa-sign-in-alt me-2"></i>Login to Dashboard
            </span>
          </button>

          <div class="divider">
            <span>OR</span>
          </div>

          <a href="../index.php" class="btn btn-outline-secondary w-100">
            <i class="fas fa-arrow-left me-2"></i>Back to Homepage
          </a>

          <div class="security-footer">
            <i class="fas fa-shield-alt"></i>
            <span>Protected by 256-bit SSL encryption</span>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.getElementById('toggleIcon');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }

    // Smooth form submission
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const submitBtn = this.querySelector('.btn-primary');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span><i class="fas fa-spinner fa-spin me-2"></i>Logging in...</span>';
    });

    // Add focus animation to inputs
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.01)';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });
  </script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>