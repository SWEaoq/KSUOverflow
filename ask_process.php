<?php
// ask_process.php
session_start();
include 'config.php';  // defines $conn

// only allow logged-in POSTs
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: ask.php');
    exit;
}

$user_id    = (int) $_SESSION['user_id'];
$title      = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
$description= mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$rawTags    = $_POST['tags'] ?? '';  // comma-separated tag names

// 1) insert question (uses 'description' not 'body')
$sql = "
  INSERT INTO questions (user_id, title, description)
  VALUES ($user_id, '$title', '$description')
";
if (!mysqli_query($conn, $sql)) {
    $_SESSION['error'] = 'Could not post question: ' . mysqli_error($conn);
    header('Location: ask.php');
    exit;
}

// 2) process tags
$qid = mysqli_insert_id($conn);
$tags = array_filter(array_map('trim', explode(',', $rawTags)));
foreach ($tags as $tagName) {
    $tagEsc = mysqli_real_escape_string($conn, $tagName);

    // find existing tag
    $r = mysqli_query($conn,
        "SELECT id FROM tags WHERE name = '$tagEsc' LIMIT 1"
    );
    if (mysqli_num_rows($r)) {
        $tid = mysqli_fetch_assoc($r)['id'];
    } else {
        // or create it
        mysqli_query($conn,
           "INSERT INTO tags (name) VALUES ('$tagEsc')"
        );
        $tid = mysqli_insert_id($conn);
    }

    // link question ↔ tag
    mysqli_query($conn,
      "INSERT IGNORE INTO question_tags (question_id, tag_id)
       VALUES ($qid, $tid)"
    );
}

// 3) redirect back to list
header('Location: index.php');
exit;
?>
