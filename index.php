<?php
session_start();         
include 'config.php';    
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>KSUOverflow</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <!-- jQuery + AJAX loader -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/script.js" defer></script>
  <!-- Google Fonts & favicon… -->
</head>
<body>
  <?php include 'header.php'; ?>

  <header class="hero bg-light text-center py-5">
    <div class="container">
      <h1 class="fw-bold text-primary">Welcome to KSUOverflow</h1>
      <p class="lead text-secondary">
        A place where KSU students ask, answer, and grow together!
      </p>
      <a href="ask.php" class="btn btn-primary btn-lg">Ask a Question</a>
    </div>
  </header>

  <div class="container mt-4">
    <h2 class="fw-bold text-primary">Latest Questions</h2>
    <div id="questions-list" class="mt-3">
      <!-- questions_ajax.php will inject cards here -->
    </div>
  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
