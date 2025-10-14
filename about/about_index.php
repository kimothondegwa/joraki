<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>About Us - Joraki</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      background-color: #f8fafc;
    }
    .about-card {
      max-width: 800px;
      margin: 60px auto;
      border-radius: 15px;
    }
    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="card shadow about-card">
    <div class="card-header bg-primary text-white text-center py-3">
      <h2>About <span class="fw-bold">Joraki</span></h2>
    </div>

    <div class="card-body p-4">
      <p class="lead text-center">
        Welcome to <strong>Joraki</strong> — your trusted platform for buying and selling 
        high-quality refurbished items. We make sustainability affordable and reliable.
      </p>

      <hr>

      <h5 class="fw-bold">Our Mission</h5>
      <p>
        To empower users to buy and sell refurbished products with confidence, 
        ensuring transparency, quality assurance, and convenience for everyone.
      </p>

      <h5 class="fw-bold mt-4">Why Choose Us?</h5>
      <ul>
        <li>Verified refurbished items</li>
        <li>Secure payments and user protection</li>
        <li>Eco-friendly shopping and resale</li>
      </ul>

      <div class="text-center mt-4">
        <!-- ✅ Fixed Back Button using BASE_URL -->
        <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-outline-primary px-4 btn-back">
          <i class="fas fa-arrow-left"></i> Back to Home
        </a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
