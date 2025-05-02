<?php
// questions_ajax.php

session_start();
include 'config.php';  // defines $conn

// Fetch latest questions
$sql = "
  SELECT 
    id,
    title,
    description,
    created_at
  FROM questions
  ORDER BY created_at DESC
";
$res = mysqli_query($conn, $sql);

// If no questions yet
if (!$res || mysqli_num_rows($res) === 0) {
    echo '<div class="alert alert-info">No questions yet. Be the first to ask one!</div>';
    exit;
}

// Render each question as a Bootstrap card
while ($q = mysqli_fetch_assoc($res)) {
    ?>
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title">
          <a 
            href="view_question.php?id=<?= $q['id'] ?>" 
            class="text-decoration-none"
          >
            <?= htmlspecialchars($q['title'], ENT_QUOTES, 'UTF-8') ?>
          </a>
        </h5>
        <?php if (trim($q['description']) !== ''): ?>
          <p class="card-text text-truncate" style="max-width:100%;">
            <?= nl2br(htmlspecialchars(substr($q['description'], 0, 150), ENT_QUOTES, 'UTF-8')) ?>…
          </p>
        <?php endif; ?>
        <p class="text-muted mb-0">
          Asked on <?= date('F j, Y, g:i A', strtotime($q['created_at'])) ?>
        </p>
      </div>
    </div>
    <?php
}
