<?php
// search.php

session_start();
include 'config.php';  // gives you $conn

// 1) Get & sanitize inputs
$q    = trim($_GET['q'] ?? '');
$sort = ($_GET['sort'] ?? '') === 'oldest' ? 'oldest' : 'newest';

if ($q === '') {
    header('Location: index.php');
    exit;
}
$qEsc  = mysqli_real_escape_string($conn, $q);
$order = $sort === 'oldest' ? 'ASC' : 'DESC';

// 2) Run the search query
$sql   = "
  SELECT q.id, q.title, q.description, q.created_at, u.username
  FROM questions q
  JOIN users    u ON u.id = q.user_id
  WHERE q.title       LIKE '%$qEsc%'
     OR q.description LIKE '%$qEsc%'
  ORDER BY q.created_at $order
";
$res   = mysqli_query($conn, $sql);
$count = $res ? mysqli_num_rows($res) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Search “<?= htmlspecialchars($q, ENT_QUOTES) ?>” – KSUOverflow</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <!-- Your custom styles -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 mb-0">Search Results</h1>
        <p class="text-muted mb-0">
          <?= $count ?> result<?= $count === 1 ? '' : 's' ?> for
          “<strong><?= htmlspecialchars($q, ENT_QUOTES) ?></strong>”
        </p>
      </div>
      <form class="d-flex" action="search.php" method="GET">
        <input type="hidden" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>">
        <select name="sort" class="form-select form-select-sm me-2" onchange="this.form.submit()">
          <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
          <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
        </select>
      </form>
    </div>

    <?php if (!$res || $count === 0): ?>
      <div class="alert alert-warning">
        No questions matched “<?= htmlspecialchars($q, ENT_QUOTES) ?>”.
      </div>
    <?php else: ?>
      <?php while ($row = mysqli_fetch_assoc($res)): ?>
        <?php 
          // create a 200‐char snippet
          $snippet = substr($row['description'], 0, 200)
                   . (strlen($row['description']) > 200 ? '…' : '');
        ?>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title mb-2">
              <a href="view_question.php?id=<?= $row['id'] ?>">
                <?= htmlspecialchars($row['title'], ENT_QUOTES) ?>
              </a>
            </h5>
            <p class="card-text mb-2"><?= nl2br(htmlspecialchars($snippet, ENT_QUOTES)) ?></p>
            <p class="text-muted mb-0">
              Asked by <strong><?= htmlspecialchars($row['username'], ENT_QUOTES) ?></strong>
              on <?= date('F j, Y, g:i A', strtotime($row['created_at'])) ?>
            </p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
