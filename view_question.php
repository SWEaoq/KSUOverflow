<?php
// view_question.php

session_start();
include 'config.php';   // gives you $conn

// 1) Get and validate the question ID
$qid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($qid <= 0) {
    die('Invalid question ID.');
}

// 2) Load the question and its owner
$qRes = mysqli_query($conn,
    "SELECT 
        q.id,
        q.title,
        q.description,
        q.created_at,
        q.user_id,
        u.username
     FROM questions AS q
     JOIN users   AS u ON u.id = q.user_id
     WHERE q.id = $qid
     LIMIT 1"
);
if (!$qRes || mysqli_num_rows($qRes) === 0) {
    die('Question not found.');
}
$question = mysqli_fetch_assoc($qRes);

// 3) Load tags
$tags = [];
$tRes = mysqli_query($conn,
    "SELECT t.name
     FROM question_tags qt
     JOIN tags        t ON t.id = qt.tag_id
     WHERE qt.question_id = $qid"
);
while ($row = mysqli_fetch_assoc($tRes)) {
    $tags[] = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= htmlspecialchars($question['title'], ENT_QUOTES) ?> – KSUOverflow</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- jQuery and your AJAX loader -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/js/script.js" defer></script>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5" style="max-width:900px;">

    <!-- Question Card -->
    <div class="card mb-4">
      <div class="card-body">
        <h2 class="card-title">
          <?= htmlspecialchars($question['title'], ENT_QUOTES) ?>
        </h2>
        <p class="card-text">
          <?= nl2br(htmlspecialchars($question['description'], ENT_QUOTES)) ?>
        </p>

        <?php if (count($tags)): ?>
          <p>
            <?php foreach ($tags as $tag): ?>
              <a 
                href="search.php?tag=<?= urlencode($tag) ?>" 
                class="badge bg-secondary text-decoration-none"
              >
                <?= htmlspecialchars($tag, ENT_QUOTES) ?>
              </a>
            <?php endforeach; ?>
          </p>
        <?php endif; ?>

        <p class="text-muted mb-0">
          Asked by <strong><?= htmlspecialchars($question['username'], ENT_QUOTES) ?></strong>
          on <?= date('F j, Y, g:i A', strtotime($question['created_at'])) ?>
        </p>

        <?php if (
          isset($_SESSION['user_id']) && 
          $_SESSION['user_id'] == $question['user_id']
        ): ?>
          <a 
            href="edit_question.php?id=<?= $question['id'] ?>" 
            class="btn btn-sm btn-outline-secondary mt-2"
          >
            Edit Question
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- AJAX-Loaded Answers -->
    <h4 class="mb-3">Answers</h4>
    <div id="answers-list">
      <!-- script.js will .load('answers_ajax.php?question_id=…') into here -->
    </div>

    <!-- Answer Form -->
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="card mt-4" id="answer-form">
        <div class="card-body">
          <h5 class="card-title">Your Answer</h5>
          <form action="add_answer.php" method="POST">
            <input 
              type="hidden" 
              name="question_id" 
              value="<?= $question['id'] ?>"
            >
            <div class="mb-3">
              <textarea
                name="body"
                rows="6"
                class="form-control"
                required
                placeholder="Type your answer here..."
              ></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
              Post Answer
            </button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <p class="mt-4">
        <a href="login.php">Log in</a> or 
        <a href="register.php">register</a> to post an answer.
      </p>
    <?php endif; ?>

  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
