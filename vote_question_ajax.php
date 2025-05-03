<?php
// vote_question_ajax.php
header('Content-Type: application/json');
include 'config.php';

// 1) Only accept POST & logged-in users
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Please login']);
    exit;
}

$uid  = (int) $_SESSION['user_id'];
$qid  = isset($_POST['question_id']) ? (int)$_POST['question_id'] : 0;
$vote = isset($_POST['vote'])        ? (int)$_POST['vote']        : 0;

if ($qid <= 0 || ($vote !== 1 && $vote !== -1)) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// 2) Check existing vote
$res = mysqli_query($conn,
  "SELECT value
   FROM question_votes
   WHERE question_id = $qid
     AND user_id     = $uid
   LIMIT 1"
);

if ($row = mysqli_fetch_assoc($res)) {
    if ((int)$row['value'] === $vote) {
        // same vote → remove it
        mysqli_query($conn,
          "DELETE FROM question_votes
           WHERE question_id = $qid
             AND user_id     = $uid"
        );
        $userVote = 0;
    } else {
        // opposite vote → update
        mysqli_query($conn,
          "UPDATE question_votes
           SET value = $vote
           WHERE question_id = $qid
             AND user_id     = $uid"
        );
        $userVote = $vote;
    }
} else {
    // no prior vote → insert
    mysqli_query($conn,
      "INSERT INTO question_votes (question_id, user_id, value)
       VALUES ($qid, $uid, $vote)"
    );
    $userVote = $vote;
}

// 3) Recompute total score
$vr = mysqli_query($conn,
  "SELECT COALESCE(SUM(value),0) AS score
   FROM question_votes
   WHERE question_id = $qid"
);
$score = (int)mysqli_fetch_assoc($vr)['score'];

// 4) Return JSON
echo json_encode([
  'success'  => true,
  'score'    => $score,
  'userVote' => $userVote
]);
exit;
