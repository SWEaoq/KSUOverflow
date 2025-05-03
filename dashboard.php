<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
include 'config.php';

// get current user ID
$uid = (int) $_SESSION['user_id'];

// fetch totals
$qCountRes = mysqli_query($conn,
  "SELECT COUNT(*) AS cnt FROM questions WHERE user_id = $uid"
);
$qCount = mysqli_fetch_assoc($qCountRes)['cnt'];

$aCountRes = mysqli_query($conn,
  "SELECT COUNT(*) AS cnt FROM answers WHERE user_id = $uid"
);
$aCount = mysqli_fetch_assoc($aCountRes)['cnt'];

// fetch recent questions
$qRes = mysqli_query($conn,
  "SELECT id, title, created_at
   FROM questions
   WHERE user_id = $uid
   ORDER BY created_at DESC
   LIMIT 5"
);

// fetch recent answers (with question titles)
$aRes = mysqli_query($conn,
  "SELECT ans.question_id, ans.body, ans.created_at, q.title AS qtitle
   FROM answers AS ans
   JOIN questions AS q ON q.id = ans.question_id
   WHERE ans.user_id = $uid
   ORDER BY ans.created_at DESC
   LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dashboard – KSUOverflow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-4">
    <h1>Your Dashboard</h1>

    <div class="row my-4">
      <div class="col-md-6 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Questions Asked</h5>
            <p class="display-4"><?= $qCount ?></p>
            <a href="my_questions.php" class="btn btn-outline-primary">View All</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <h5 class="card-title">Answers Given</h5>
            <p class="display-4"><?= $aCount ?></p>
            <a href="my_answers.php" class="btn btn-outline-primary">View All</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <h4>Recent Questions</h4>
        <ul class="list-group">
          <?php while ($row = mysqli_fetch_assoc($qRes)): ?>
            <li class="list-group-item">
              <a href="view_question.php?id=<?= $row['id'] ?>">
                <?= htmlspecialchars($row['title']) ?>
              </a>
              <br>
              <small class="text-muted">Asked on <?= $row['created_at'] ?></small>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
      <div class="col-md-6">
        <h4>Recent Answers</h4>
        <ul class="list-group">
          <?php while ($row = mysqli_fetch_assoc($aRes)): ?>
            <li class="list-group-item">
              On <a href="view_question.php?id=<?= $row['question_id'] ?>">
                <?= htmlspecialchars($row['qtitle']) ?>
              </a>:
              <?= htmlspecialchars(substr($row['body'], 0, 100)) ?>…
              <br>
              <small class="text-muted">Answered on <?= $row['created_at'] ?></small>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
</body>
</html>
