<?php
// my_questions.php

session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

include 'config.php';   // defines $conn

// 1) Fetch only the logged-in user’s questions
$uid  = (int) $_SESSION['user_id'];
$sqlQ = "
    SELECT 
      id,
      title,
      description,
      created_at
    FROM questions
    WHERE user_id = $uid
    ORDER BY created_at DESC
";
$qRes = mysqli_query($conn, $sqlQ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>My Questions – KSUOverflow</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- 2) Include header/nav  -->
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <h1 class="mb-4">My Questions</h1>

    <?php if (!$qRes || mysqli_num_rows($qRes) === 0): ?>
      <div class="alert alert-info">
        You haven’t asked any questions yet.
        <a href="ask.php" class="alert-link">Ask your first question</a>.
      </div>
    <?php else: ?>
      <ul class="list-group">
        <?php while ($question = mysqli_fetch_assoc($qRes)): ?>
          <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
              <div class="fw-bold">
                <a 
                  href="view_question.php?id=<?= $question['id'] ?>" 
                  class="text-decoration-none"
                >
                  <?= htmlspecialchars($question['title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </div>
              <small class="text-muted">
                Asked on <?= date('F j, Y, g:i A', strtotime($question['created_at'])) ?>
              </small>
              <?php if (trim($question['description']) !== ''): ?>
                <p class="mt-2 mb-0 text-truncate" style="max-width:75%;">
                  <?= nl2br(htmlspecialchars(substr($question['description'], 0, 150), ENT_QUOTES, 'UTF-8')) ?>…
                </p>
              <?php endif; ?>
            </div>
            <a 
              href="edit_question.php?id=<?= $question['id'] ?>" 
              class="btn btn-sm btn-outline-secondary"
            >
              Edit
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php endif; ?>
  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
