<?php
// ask.php
include 'config.php';

// require login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// grab & clear any error message
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Ask a Question - KSUOverflow</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="fw-bold text-center text-primary">Ask a Question</h2>

            <?php if ($error): ?>
              <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
              </div>
            <?php endif; ?>

            <form id="ask-form" action="ask_process.php" method="POST">
              <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input
                  type="text"
                  class="form-control"
                  id="title"
                  name="title"
                  placeholder="Enter your question title"
                  required
                >
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea
                  class="form-control"
                  id="description"
                  name="body"
                  rows="4"
                  placeholder="Enter your question description"
                  required
                ></textarea>
              </div>

              <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <input
                  type="text"
                  class="form-control"
                  id="tags"
                  name="tags"
                  placeholder="e.g. HTML, CSS, JavaScript (optional)"
                >
              </div>

              <button type="submit" class="btn btn-primary w-100">
                Ask
              </button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
