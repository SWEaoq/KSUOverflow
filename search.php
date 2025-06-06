<?php
session_start();
include 'config.php';  // provides $conn

// 1) Get & sanitize inputs
$q   = trim($_GET['q'] ?? '');
$tag = trim($_GET['tag'] ?? '');
$raw = $_GET['sort'] ?? 'newest';

// Determine sort mode and SQL order clause
switch ($raw) {
    case 'oldest':
        $orderBy = 'q.created_at ASC';
        break;
    case 'votes':
        // Most voted first
        $orderBy = 'COALESCE(v.score,0) DESC';
        break;
    case 'newest':
    default:
        $orderBy = 'q.created_at DESC';
        $raw     = 'newest';
        break;
}

if ($q === '' && $tag === '') {
    header('Location: index.php');
    exit;
}

$qEsc = mysqli_real_escape_string($conn, $q);
$tagEsc = mysqli_real_escape_string($conn, $tag);

// 2) Query: include vote totals and tag list
$sql = "
  SELECT
    q.id,
    q.title,
    q.description,
    q.created_at,
    u.username,
    COALESCE(v.score, 0)      AS score,
    COALESCE(tg.tag_list, '') AS tag_list
  FROM questions q
  LEFT JOIN users u
    ON u.id = q.user_id

  /* votes subquery */
  LEFT JOIN (
    SELECT question_id, SUM(value) AS score
    FROM question_votes
    GROUP BY question_id
  ) v ON v.question_id = q.id

  /* tags subquery */
  LEFT JOIN (
    SELECT qt.question_id,
           GROUP_CONCAT(t.name ORDER BY t.name ASC SEPARATOR ',') AS tag_list
    FROM question_tags qt
    JOIN tags t ON t.id = qt.tag_id
    GROUP BY qt.question_id
  ) tg ON tg.question_id = q.id
";

if ($tag !== '') {
    $sql .= "
  JOIN question_tags qt2 ON qt2.question_id = q.id
  JOIN tags t2 ON t2.id = qt2.tag_id
  WHERE t2.name = '{$tagEsc}'
";
} else {
    $sql .= "
  WHERE q.title       LIKE '%{$qEsc}%'
     OR q.description LIKE '%{$qEsc}%'
";
}

$sql .= "  ORDER BY {$orderBy}";

$res = mysqli_query($conn, $sql);
if (!$res) {
    die("SQL Error: " . mysqli_error($conn));
}

// collect
$questions = [];
while ($row = mysqli_fetch_assoc($res)) {
    $questions[] = $row;
}
$count = count($questions);
$displayTerm = $tag !== '' ? $tag : $q;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Search “<?= htmlspecialchars($displayTerm, ENT_QUOTES) ?>” – KSUOverflow</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h4 mb-1">Search Results</h1>
        <p class="text-muted mb-0">
          <?= $count ?> result<?= $count === 1 ? '' : 's' ?> for
          “<strong><?= htmlspecialchars($displayTerm, ENT_QUOTES) ?></strong>”
        </p>
      </div>
      <form class="d-flex" action="search.php" method="GET">
        <?php if ($tag !== ''): ?>
          <input type="hidden" name="tag" value="<?= htmlspecialchars($tag, ENT_QUOTES) ?>">
        <?php else: ?>
          <input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>">
        <?php endif; ?>
        <select name="sort" class="form-select form-select-sm me-2" onchange="this.form.submit()">
          <option value="newest" <?= $raw === 'newest' ? 'selected' : '' ?>>Newest</option>
          <option value="oldest" <?= $raw === 'oldest'  ? 'selected' : '' ?>>Oldest</option>
          <option value="votes"  <?= $raw === 'votes'   ? 'selected' : '' ?>>Highest Votes</option>
        </select>
      </form>
    </div>

    <?php if ($count === 0): ?>
      <div class="alert alert-warning">
        No questions matched “<?= htmlspecialchars($displayTerm, ENT_QUOTES) ?>”.
      </div>
    <?php else: ?>
      <?php foreach ($questions as $row): ?>
        <?php
          $desc    = $row['description'];
          $snippet = htmlspecialchars(mb_substr($desc, 0, 200), ENT_QUOTES)
                   . (mb_strlen($desc) > 200 ? '…' : '');
          $tags    = $row['tag_list'] !== '' 
                   ? explode(',', $row['tag_list']) 
                   : [];
        ?>
        <div class="card mb-3 shadow-sm">
          <div class="card-body position-relative">
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
            <?php if ($tags): ?>
              <div class="mt-3">
                <?php foreach ($tags as $tag): ?>
                  <a href="search.php?tag=<?= urlencode($tag) ?>"
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
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
