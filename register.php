<?php
// register.php
require_once 'config.php';   // starts session

// Grab any error message set by register_process.php
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Register – KSUOverflow</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>

  <!-- Navbar -->
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="fw-bold text-center text-primary">Register</h2>
            <form id="register-form" action="register_process.php" method="POST">
              <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input name="first_name" id="first_name" type="text"
                       class="form-control" placeholder="Enter your first name" required>
              </div>
              <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input name="last_name" id="last_name" type="text"
                       class="form-control" placeholder="Enter your last name" required>
              </div>
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input name="username" id="username" type="text"
                       class="form-control" placeholder="Choose a public username" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input name="email" id="email" type="email"
                       class="form-control" placeholder="Enter your email" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input name="password" id="password" type="password"
                       class="form-control" placeholder="Create a password" required>
              </div>
              <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input name="confirm_password" id="confirm_password" type="password"
                       class="form-control" placeholder="Confirm your password" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="mt-3 text-center">
              Already have an account? <a href="login.php" class="text-primary">Login</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
