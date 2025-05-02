<?php
// answers_ajax.php

session_start();
include 'config.php';  // defines $conn

$qid = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
if ($qid <= 0) {
    echo '<div class="alert alert-warning">Invalid question.</div>';
    exit;
}

$sql = "
  SELECT a.body, a.created_at, u.username
    FROM answers AS a
    JOIN users   AS u ON u.id = a.user_id
   WHERE a.question_id = $qid
   ORDER BY a.created_at ASC
";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) === 0) {
    echo '<div class="alert alert-info">No answers yet. Be the first to answer!</div>';
    exit;
}

while ($ans = mysqli_fetch_assoc($res)) {
    ?>
    <div class="card mb-3">
      <div class="card-body">
        <?= nl2br(htmlspecialchars($ans['body'], ENT_QUOTES)) ?>
        <p class="text-muted mt-2 mb-0">
          answered by <strong><?= htmlspecialchars($ans['username'], ENT_QUOTES) ?></strong>
          on <?= date('F j, Y, g:i A', strtotime($ans['created_at'])) ?>
        </p>
      </div>
    </div>
    <?php
}
