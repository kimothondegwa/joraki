<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Contact Us - Joraki</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    body {
      background-color: #f8fafc;
    }
    .contact-card {
      max-width: 700px;
      margin: 80px auto;
      border-radius: 15px;
      overflow: hidden;
    }
    .contact-header {
      background-color: #0d6efd;
      color: white;
      text-align: center;
      padding: 20px;
    }
    .contact-info i {
      color: #0d6efd;
      margin-right: 10px;
    }
    .contact-box {
      background: #f1f5f9;
      border-radius: 10px;
      padding: 20px;
      margin-top: 25px;
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

<div class="container">
  <div class="card shadow contact-card">
    <div class="contact-header">
      <h2><i class="fas fa-envelope-open-text me-2"></i>Contact Us</h2>
    </div>

    <div class="card-body">
      <p class="text-center lead">
        Have a question, suggestion, or need help? We’d love to hear from you.
      </p>

      <div class="contact-box">
        <h5 class="fw-bold mb-3">Reach Us At</h5>
        <ul class="list-unstyled contact-info">
          <li class="mb-2">
            <i class="fas fa-envelope"></i>
            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
          </li>
          <li class="mb-2">
            <i class="fas fa-phone"></i>
            <a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a>
          </li>
          <li class="mb-2">
            <i class="fas fa-globe"></i>
            <a href="<?php echo SITE_URL; ?>"><?php echo SITE_URL; ?></a>
          </li>
        </ul>
      </div>

      <div class="text-center mt-4">
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
