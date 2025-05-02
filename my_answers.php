<?php
// my_answers.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';   // brings in $conn

$uid    = (int) $_SESSION['user_id'];
$sqlA   = "
    SELECT
        a.id AS answer_id,
        a.question_id,
        a.body,
        a.created_at,
        q.title AS question_title
    FROM answers AS a
    JOIN questions AS q ON q.id = a.question_id
    WHERE a.user_id = $uid
    ORDER BY a.created_at DESC
";
$ansRes = mysqli_query($conn, $sqlA);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>My Answers – KSUOverflow</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <h1 class="mb-4">My Answers</h1>

    <?php if (!$ansRes || mysqli_num_rows($ansRes) === 0): ?>
      <div class="alert alert-info">
        You haven’t posted any answers yet.
        <a href="index.php" class="alert-link">Browse questions</a> to answer.
      </div>
    <?php else: ?>
      <ul class="list-group">
        <?php while ($ans = mysqli_fetch_assoc($ansRes)): ?>
          <li class="list-group-item d-flex justify-content-between align-items-start">
            <div class="ms-2 me-auto">
              <div>
                On question:
                <a
                  href="view_question.php?id=<?= $ans['question_id'] ?>"
                  class="fw-bold text-decoration-none"
                >
                  <?= htmlspecialchars($ans['question_title'], ENT_QUOTES, 'UTF-8') ?>
                </a>
              </div>
              <small class="text-muted">
                Answered on <?= date('F j, Y, g:i A', strtotime($ans['created_at'])) ?>
              </small>
              <p class="mt-2 mb-0 text-truncate" style="max-width:75%;">
                <?= nl2br(htmlspecialchars(substr($ans['body'], 0, 150), ENT_QUOTES, 'UTF-8')) ?>…
              </p>
            </div>
            <a
              href="edit_answer.php?id=<?= $ans['answer_id'] ?>"
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
