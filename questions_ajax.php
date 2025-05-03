<?php
// questions_ajax.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// Fetch latest 10 questions (for example)
$sql = "
  SELECT 
    q.id,
    COALESCE(q.title, '')       AS title,
    COALESCE(q.description, '') AS description,
    q.created_at,
    u.username,
    COALESCE(v.score, 0)        AS score,
    COALESCE(tg.tag_list, '')   AS tag_list
  FROM questions q
  LEFT JOIN users u       ON u.id = q.user_id
  LEFT JOIN (
    SELECT question_id, SUM(value) AS score
    FROM question_votes
    GROUP BY question_id
  ) v ON v.question_id = q.id
  LEFT JOIN (
    SELECT qt.question_id,
           GROUP_CONCAT(t.name ORDER BY t.name ASC SEPARATOR ',') AS tag_list
    FROM question_tags qt
    JOIN tags t ON t.id = qt.tag_id
    GROUP BY qt.question_id
  ) tg ON tg.question_id = q.id
  ORDER BY q.created_at DESC
  LIMIT 10
";

$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));

while ($row = mysqli_fetch_assoc($res)) {
    // build snippet
    $desc    = $row['description'];
    $snippet = htmlspecialchars(mb_substr($desc, 0, 200), ENT_QUOTES)
             . (mb_strlen($desc) > 200 ? '…' : '');

    // tags array
    $tags = $row['tag_list'] !== '' 
          ? explode(',', $row['tag_list']) 
          : [];

    // render card
    ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-body position-relative">
        <!-- vote total -->
        <div class="position-absolute" style="top:1rem; right:1rem; font-weight:bold;">
          <?= (int)$row['score'] ?> votes
        </div>

        <h5 class="card-title">
          <a href="view_question.php?id=<?= $row['id'] ?>"
             class="stretched-link text-decoration-none">
            <?= htmlspecialchars($row['title'], ENT_QUOTES) ?>
          </a>
        </h5>

        <p class="card-text"><?= nl2br($snippet) ?></p>

        <!-- tags -->
        <?php if ($tags): ?>
          <div class="mt-3">
            <?php foreach ($tags as $tag): ?>
              <a href="search.php?q=<?= urlencode($tag) ?>" 
                 class="badge bg-light text-primary me-1">
                <?= htmlspecialchars($tag, ENT_QUOTES) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <p class="text-muted small mt-2 mb-0">
          Asked by <strong><?= htmlspecialchars($row['username'] ?? 'Unknown', ENT_QUOTES) ?></strong>
          on <?= date('F j, Y, g:i A', strtotime($row['created_at'])) ?>
        </p>
      </div>
    </div>
    <?php
}
