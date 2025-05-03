<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cid = isset($_GET['comment_id']) ? (int)$_GET['comment_id'] : 0;
$qid = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
$aid = isset($_GET['answer_id'])   ? (int)$_GET['answer_id']   : 0;
$uid = (int)$_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = trim($_POST['body'] ?? '');
    if ($body === '') {
        $_SESSION['error'] = 'Comment cannot be empty.';
        header("Location: edit_comment.php?comment_id=$cid&question_id=$qid&answer_id=$aid");
        exit;
    }
    $bodyEsc = mysqli_real_escape_string($conn, $body);
    $sql = "
      UPDATE comments
      SET body = '$bodyEsc'
      WHERE id = $cid
        AND user_id = $uid
    ";
    mysqli_query($conn, $sql) or die('DB Error: ' . mysqli_error($conn));
    header("Location: view_question.php?id=$qid");
    exit;
}

// Fetch existing comment
$res = mysqli_query($conn,
  "SELECT body
   FROM comments
   WHERE id = $cid
     AND user_id = $uid
   LIMIT 1"
);
if (!$res || mysqli_num_rows($res) === 0) {
    die('Comment not found or permission denied.');
}
$comment = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Comment – KSUOverflow</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5" style="max-width:600px;">
    <h2>Edit Comment</h2>
    <?php if ($err = ($_SESSION['error'] ?? '')): unset($_SESSION['error']); ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form action="edit_comment.php?comment_id=<?= $cid ?>&question_id=<?= $qid ?>&answer_id=<?= $aid ?>" method="POST">
      <div class="mb-3">
        <textarea
          name="body"
          class="form-control"
          rows="4"
          required
        ><?= htmlspecialchars($comment['body'], ENT_QUOTES) ?></textarea>
      </div>
      <button class="btn btn-primary">Save Changes</button>
      <a href="view_question.php?id=<?= $qid ?>" class="btn btn-secondary">Cancel</a>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
