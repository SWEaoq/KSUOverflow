<?php
// edit_question.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'config.php';   // defines $conn

$uid = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // —————————————————————————————
    // 1) PROCESS THE FORM SUBMISSION
    // —————————————————————————————
    $qid         = (int) $_POST['id'];
    $title       = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $rawTags     = $_POST['tags'] ?? '';

    // ensure this question belongs to the logged-in user
    $check = mysqli_query(
        $conn,
        "SELECT id FROM questions WHERE id = $qid AND user_id = $uid LIMIT 1"
    );
    if (mysqli_num_rows($check) === 0) {
        die('Invalid question or access denied.');
    }

    // update title & description
    mysqli_query(
        $conn,
        "UPDATE questions
            SET title = '$title',
                description = '$description'
          WHERE id = $qid"
    );

    // reset tags
    mysqli_query(
        $conn,
        "DELETE FROM question_tags
          WHERE question_id = $qid"
    );

    // re-insert tags
    $tags = array_filter(array_map('trim', explode(',', $rawTags)));
    foreach ($tags as $tagName) {
        $t = mysqli_real_escape_string($conn, $tagName);

        // find or create tag
        $r = mysqli_query($conn,
            "SELECT id FROM tags WHERE name = '$t' LIMIT 1"
        );
        if (mysqli_num_rows($r)) {
            $tid = mysqli_fetch_assoc($r)['id'];
        } else {
            mysqli_query($conn,
                "INSERT INTO tags (name) VALUES ('$t')"
            );
            $tid = mysqli_insert_id($conn);
        }

        // link it
        mysqli_query($conn,
            "INSERT IGNORE INTO question_tags (question_id, tag_id)
             VALUES ($qid, $tid)"
        );
    }

    // done—go back to the question view
    header("Location: view_question.php?id=$qid");
    exit;
}

// —————————————————————————————
// 2) DISPLAY THE EDIT FORM
// —————————————————————————————
$qid = (int) ($_GET['id'] ?? 0);
$res = mysqli_query(
    $conn,
    "SELECT id, title, description
       FROM questions
      WHERE id = $qid
        AND user_id = $uid
      LIMIT 1"
);
if (!$res || mysqli_num_rows($res) === 0) {
    die('Question not found or access denied.');
}
$question = mysqli_fetch_assoc($res);

// fetch existing tags
$tRes = mysqli_query(
    $conn,
    "SELECT t.name
       FROM question_tags qt
       JOIN tags t ON t.id = qt.tag_id
      WHERE qt.question_id = $qid"
);
$tagList = '';
if ($tRes) {
    $names = [];
    while ($row = mysqli_fetch_assoc($tRes)) {
        $names[] = $row['name'];
    }
    $tagList = implode(', ', $names);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Question – KSUOverflow</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5" style="max-width:800px;">
    <h1 class="mb-4">Edit Your Question</h1>
    <form method="POST" action="edit_question.php">
      <input type="hidden" name="id" value="<?= $question['id'] ?>">

      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input
          type="text"
          id="title"
          name="title"
          class="form-control"
          required
          value="<?= htmlspecialchars($question['title'], ENT_QUOTES, 'UTF-8') ?>"
        >
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea
          id="description"
          name="description"
          rows="8"
          class="form-control"
          required
        ><?= htmlspecialchars($question['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div class="mb-3">
        <label for="tags" class="form-label">Tags <small class="text-muted">(comma-separated)</small></label>
        <input
          type="text"
          id="tags"
          name="tags"
          class="form-control"
          placeholder="e.g. php, mysql, javascript"
          value="<?= htmlspecialchars($tagList, ENT_QUOTES, 'UTF-8') ?>"
        >
      </div>

      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="view_question.php?id=<?= $question['id'] ?>" class="btn btn-outline-secondary ms-2">
        Cancel
      </a>
    </form>
  </div>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
  ></script>
</body>
</html>
