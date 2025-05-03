<?php
session_start();
include 'config.php';  // defines $conn

// 1) Only allow POST from logged-in users
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: ask.php');
    exit;
}

$user_id     = (int) $_SESSION['user_id'];
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');  // now matches ask.php
$rawTags     = $_POST['tags'] ?? '';

// 2) Validation
if ($title === '' || $description === '') {
    $_SESSION['error'] = 'Both title and description are required.';
    header('Location: ask.php');
    exit;
}

// 3) Insert question
$titleEsc = mysqli_real_escape_string($conn, $title);
$descEsc  = mysqli_real_escape_string($conn, $description);

$sql = "
  INSERT INTO questions (user_id, title, description)
  VALUES ($user_id, '$titleEsc', '$descEsc')
";
if (!mysqli_query($conn, $sql)) {
    $_SESSION['error'] = 'Could not post question: ' . mysqli_error($conn);
    header('Location: ask.php');
    exit;
}
$qid = mysqli_insert_id($conn);

// 4) Process tags
$tags = array_filter(array_map('trim', explode(',', $rawTags)));
foreach ($tags as $tagName) {
    $tagEsc = mysqli_real_escape_string($conn, $tagName);

    // find or create tag
    $r = mysqli_query(
        $conn,
        "SELECT id FROM tags WHERE name = '$tagEsc' LIMIT 1"
    );
    if (mysqli_num_rows($r)) {
        $tid = (int) mysqli_fetch_assoc($r)['id'];
    } else {
        mysqli_query(
          $conn,
          "INSERT INTO tags (name) VALUES ('$tagEsc')"
        );
        $tid = mysqli_insert_id($conn);
    }

    // link question ↔ tag
    mysqli_query(
      $conn,
      "INSERT IGNORE INTO question_tags (question_id, tag_id)
       VALUES ($qid, $tid)"
    );
}

// 5) Success!
header('Location: index.php');
exit;
