<?php
// add_comment.php
session_start();
include 'config.php';

// 1) Must be POST & logged in
if ($_SERVER['REQUEST_METHOD']!=='POST' || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$body_raw    = trim($_POST['body'] ?? '');
$question_id = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
$answer_id   = isset($_POST['answer_id'])   ? (int)$_POST['answer_id']   : 0;
$parent_id   = isset($_POST['parent_id'])   ? (int)$_POST['parent_id']   : 0;

// 2) Validate
if ($body_raw==='' || (! $question_id && ! $answer_id && ! $parent_id)) {
    $_SESSION['error'] = 'Comment cannot be empty.';
    $back = $_POST['origin_qid'] ?? $question_id;
    header("Location: view_question.php?id=$back");
    exit;
}

// 3) Insert with optional parent_id
$bodyEsc = mysqli_real_escape_string($conn, $body_raw);
$sql = "
  INSERT INTO comments
    (user_id, question_id, answer_id, parent_id, body)
  VALUES
    ($user_id,
     " . ($question_id   ? $question_id : 'NULL') . ",
     " . ($answer_id     ? $answer_id   : 'NULL') . ",
     " . ($parent_id     ? $parent_id   : 'NULL') . ",
     '$bodyEsc')
";
mysqli_query($conn, $sql) or die('DB Error: '.mysqli_error($conn));

// 4) Redirect back to question
$back = $_POST['origin_qid'] ?? $question_id;
header("Location: view_question.php?id=$back");
exit;
