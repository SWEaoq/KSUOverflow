<?php
// add_answer.php

session_start();
include 'config.php';  // defines $conn

// Only accept POST from logged-in users
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id     = (int) $_SESSION['user_id'];
$question_id = (int) ($_POST['question_id'] ?? 0);
$body_raw    = trim($_POST['body'] ?? '');

if ($question_id <= 0 || $body_raw === '') {
    // Invalid data; send back with an error
    $_SESSION['error'] = 'Your answer cannot be empty.';
    header("Location: view_question.php?id={$question_id}");
    exit;
}

// Ensure the question exists
$qCheck = mysqli_query(
    $conn,
    "SELECT id FROM questions WHERE id = $question_id LIMIT 1"
);
if (!$qCheck || mysqli_num_rows($qCheck) === 0) {
    die('Question not found.');
}

// Insert the new answer
$body = mysqli_real_escape_string($conn, $body_raw);
$sql = "
    INSERT INTO answers (question_id, user_id, body, created_at)
    VALUES ($question_id, $user_id, '$body', NOW())
";
if (!mysqli_query($conn, $sql)) {
    die('Database error: ' . mysqli_error($conn));
}

// Redirect back to the question view
header("Location: view_question.php?id={$question_id}");
exit;
?>
