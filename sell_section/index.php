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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-lg border-0 mx-auto" style="max-width: 700px;">
    <div class="card-header bg-primary text-white text-center">
      <h3>Sell Your Item</h3>
    </div>

    <div class="card-body p-4">

      <!-- ✅ Success message -->
      <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div id="successMsg" class="alert alert-success text-center fw-bold">
          ✅ Your item has been submitted successfully and is pending review.
        </div>
      <?php endif; ?>

      <!-- 📝 Sell Item Form -->
      <form action="process_sell.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label fw-bold">Item Title</label>
          <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Category</label>
          <select name="category" class="form-select" required>
            <option value="">Select Category</option>
            <option>Car</option>
            <option>Bike</option>
            <option>Electronics</option>
            <option>Furniture</option>
            <option>Other</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Condition</label>
          <select name="condition_type" class="form-select" required>
            <option value="new">New</option>
            <option value="used">Used</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Description</label>
          <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Price (KSh)</label>
            <input type="number" name="price" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Original Price (optional)</label>
            <input type="number" name="original_price" class="form-control">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Upload Image</label>
          <input type="file" name="image" class="form-control" accept="image/*" required>
        </div>

        <hr class="my-4">

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label fw-bold">Your Name</label>
            <input type="text" name="seller_name" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label fw-bold">Email</label>
            <input type="email" name="seller_email" class="form-control" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label fw-bold">Phone</label>
            <input type="text" name="seller_phone" class="form-control" required>
          </div>
        </div>

        <!-- ✅ Submit and Back to Home Buttons -->
        <div class="text-center mt-4 d-flex justify-content-center gap-3">
          <button type="submit" class="btn btn-success px-4 py-2 fw-bold">
            <i class="fas fa-upload me-2"></i>Submit Item
          </button>
          <a href="../index.php" class="btn btn-primary px-4 py-2 fw-bold">
            <i class="fas fa-home me-2"></i>Back to Home
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ Auto-hide success message after 4 seconds -->
<script>
  setTimeout(() => {
    const msg = document.getElementById('successMsg');
    if (msg) msg.style.display = 'none';
  }, 4000);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
