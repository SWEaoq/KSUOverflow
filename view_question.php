<?php
// view_question.php
session_start();
include 'config.php';

// Validate question ID
$qid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($qid <= 0) die('Invalid question ID.');

// Fetch question + author
$qRes = mysqli_query($conn,
  "SELECT q.*, u.username
   FROM questions q
   JOIN users u ON u.id=q.user_id
   WHERE q.id=$qid
   LIMIT 1"
);
if (!$question = mysqli_fetch_assoc($qRes)) {
    die('Question not found.');
}

// Fetch tags
$tags = [];
$tRes = mysqli_query($conn,
  "SELECT t.name
   FROM question_tags qt
   JOIN tags t ON t.id=qt.tag_id
   WHERE qt.question_id=$qid"
);
while ($r = mysqli_fetch_assoc($tRes)) {
    $tags[] = $r['name'];
}

// Fetch vote total
$vr = mysqli_query($conn,
  "SELECT COALESCE(SUM(value),0) AS score
   FROM question_votes
   WHERE question_id=$qid"
);
$score = (int)mysqli_fetch_assoc($vr)['score'];

// Fetch this user’s vote
$userVote = 0;
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $uv = mysqli_query($conn,
      "SELECT value
       FROM question_votes
       WHERE question_id=$qid AND user_id=$uid
       LIMIT 1"
    );
    if ($v = mysqli_fetch_assoc($uv)) {
        $userVote = (int)$v['value'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= htmlspecialchars($question['title'], ENT_QUOTES) ?> – KSUOverflow</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container mt-5" style="max-width:800px;">

    <!-- Vote Controls + Question -->
    <div class="card mb-4">
      <div class="card-body">

        <div class="vote-controls mb-3 text-center">
          <button id="upvote-btn" 
                  data-vote="1" 
                  class="btn btn-link<?= $userVote===1 ? ' active' : '' ?>">
            ▲
          </button>
          <span id="vote-score" class="vote-score"><?= $score ?></span>
          <button id="downvote-btn" 
                  data-vote="-1" 
                  class="btn btn-link<?= $userVote===-1 ? ' active' : '' ?>">
            ▼
          </button>
        </div>

        <h2><?= htmlspecialchars($question['title'], ENT_QUOTES) ?></h2>
        <p><?= nl2br(htmlspecialchars($question['description'], ENT_QUOTES)) ?></p>

        <?php if ($tags): ?>
          <p>
            <?php foreach ($tags as $tag): ?>
              <a href="search.php?tag=<?= urlencode($tag) ?>"
                 class="badge bg-secondary text-decoration-none">
                <?= htmlspecialchars($tag, ENT_QUOTES) ?>
              </a>
            <?php endforeach; ?>
          </p>
        <?php endif; ?>

        <p class="text-muted">
          Asked by <strong><?= htmlspecialchars($question['username'], ENT_QUOTES) ?></strong>
          on <?= date('F j, Y, g:i A', strtotime($question['created_at'])) ?>
        </p>

      </div>
    </div>

    <!-- Answers and answer form go here... -->

  </div>

  <script>
  $(function(){
    function doVote(vote) {
      $.post('vote_question_ajax.php', {
        question_id: <?= $qid ?>,
        vote: vote
      }, function(resp){
        if (!resp.success) {
          alert(resp.error || 'Vote failed');
          return;
        }
        // update score
        $('#vote-score').text(resp.score);
        // highlight buttons
        $('#upvote-btn').toggleClass('active', resp.userVote === 1);
        $('#downvote-btn').toggleClass('active', resp.userVote === -1);
      }, 'json');
    }

    $('#upvote-btn').click(function(e){
      e.preventDefault();
      doVote(1);
    });
    $('#downvote-btn').click(function(e){
      e.preventDefault();
      doVote(-1);
    });
  });
  </script>
</body>
</html>
