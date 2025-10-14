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

  <!-- ✅ Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #e0e7ff, #f8fafc);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Poppins", sans-serif;
    }
    .login-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      padding: 2rem 2.5rem;
      width: 380px;
      text-align: center;
      animation: fadeIn 0.6s ease;
    }
    .login-card h2 {
      color: #1e293b;
      margin-bottom: 1rem;
    }
    .form-control {
      border-radius: 8px;
      padding: 10px;
    }
    .btn-primary {
      background-color: #2563eb;
      border: none;
      width: 100%;
      padding: 10px;
      border-radius: 8px;
    }
    .btn-primary:hover {
      background-color: #1d4ed8;
    }
    .btn-outline-secondary {
      border-radius: 8px;
    }
    .error {
      color: #b91c1c;
      background: #fee2e2;
      padding: 8px;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 0.9rem;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <div class="login-card">
    <h2>Admin Login</h2>

    <?php if (!empty($_GET['error'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="process.php" method="POST" class="text-start">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
      
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
      </div>

      <button type="submit" class="btn btn-primary mb-3">Login</button>
    </form>

    <a href="../index.php" class="btn btn-outline-secondary w-100">← Back to Homepage</a>
  </div>

  <!-- ✅ Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
