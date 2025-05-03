<?php
// edit_answer.php
session_start();
include 'config.php';

// 1) Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$uid = (int)$_SESSION['user_id'];
$aid = isset($_GET['answer_id'])    ? (int)$_GET['answer_id']    : 0;
$qid = isset($_GET['question_id'])  ? (int)$_GET['question_id']  : 0;

// 2) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body_raw = trim($_POST['body'] ?? '');
    if ($body_raw === '') {
        $_SESSION['error'] = 'Answer cannot be empty.';
        header("Location: edit_answer.php?answer_id=$aid&question_id=$qid");
        exit;
    }
    $body = mysqli_real_escape_string($conn, $body_raw);
    $sql  = "
      UPDATE answers
      SET body = '$body'
      WHERE id = $aid
        AND user_id = $uid
    ";
    if (!mysqli_query($conn, $sql)) {
        die('Database error: ' . mysqli_error($conn));
    }
    header("Location: view_question.php?id=$qid");
    exit;
}

// 3) Fetch existing answer
$res = mysqli_query($conn,
  "SELECT body
   FROM answers
   WHERE id = $aid
     AND user_id = $uid
   LIMIT 1"
);
if (!$res || mysqli_num_rows($res) === 0) {
    die('Answer not found or you do not have permission to edit it.');
}
$answer = mysqli_fetch_assoc($res);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Answer – KSUOverflow</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5" style="max-width:700px;">
    <h2>Edit Your Answer</h2>

    <?php if ($err = ($_SESSION['error'] ?? '')): 
      unset($_SESSION['error']); ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form action="edit_answer.php?answer_id=<?= $aid ?>&question_id=<?= $qid ?>" method="POST">
      <div class="mb-3">
        <textarea
          name="body"
          rows="6"
          class="form-control"
          required
        ><?= htmlspecialchars($answer['body'], ENT_QUOTES) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="view_question.php?id=<?= $qid ?>" class="btn btn-secondary">Cancel</a>
    </form>
  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
