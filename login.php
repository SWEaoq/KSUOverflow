<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login – KSUOverflow</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <?php if ($error): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($error, ENT_QUOTES) ?>
      </div>
    <?php endif; ?>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <form action="login_process.php" method="POST" class="card p-4 shadow-sm">
          <h2 class="text-center text-primary mb-4">Login</h2>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input name="email" id="email" type="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input name="password" id="password" type="password" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>

          <p class="mt-3 text-center">
            Don’t have an account? <a href="register.php">Register</a>
          </p>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
