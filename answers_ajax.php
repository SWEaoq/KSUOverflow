<?php
// answers_ajax.php

session_start();
include 'config.php';  // gives you $conn

$qid    = isset($_GET['question_id']) ? (int)$_GET['question_id'] : 0;
$userId = isset($_SESSION['user_id'])   ? (int)$_SESSION['user_id']   : 0;

if ($qid <= 0) {
    echo '<div class="alert alert-warning">Invalid question.</div>';
    exit;
}

// 1) Fetch all answers
$ansRes = mysqli_query($conn,
  "SELECT 
     a.id,
     a.body,
     a.created_at,
     a.user_id,
     u.username
   FROM answers a
   JOIN users   u ON u.id = a.user_id
   WHERE a.question_id = $qid
   ORDER BY a.created_at ASC"
);

if (!$ansRes || mysqli_num_rows($ansRes) === 0) {
    echo '<div class="alert alert-info">No answers yet.</div>';
    exit;
}

while ($ans = mysqli_fetch_assoc($ansRes)) {
    $aid    = (int)$ans['id'];
    $author = (int)$ans['user_id'];  // cast the author ID

    ?>
    <div class="card mb-4">
      <div class="card-body">
        <!-- Answer text -->
        <p><?= nl2br(htmlspecialchars($ans['body'], ENT_QUOTES)) ?></p>
        <p class="text-muted small mb-2">
          Answered by <strong><?= htmlspecialchars($ans['username'], ENT_QUOTES) ?></strong>
          on <?= date('F j, Y, g:i A', strtotime($ans['created_at'])) ?>
          <?php if ($userId === $author): // exact type‐safe compare ?>
            &nbsp;|&nbsp;
            <a href="edit_answer.php?answer_id=<?= $aid ?>&question_id=<?= $qid ?>" class="small">
              Edit
            </a>
          <?php endif; ?>
        </p>

        <!-- Comments on this answer -->
        <?php
        $cRes = mysqli_query($conn,
          "SELECT 
             c.id,
             c.body,
             c.created_at,
             c.user_id,
             u.username
           FROM comments c
           JOIN users    u ON u.id = c.user_id
           WHERE c.answer_id = $aid
           ORDER BY c.created_at ASC"
        );
        if (mysqli_num_rows($cRes) > 0): ?>
          <h6 class="mb-2">Comments</h6>
          <?php while ($c = mysqli_fetch_assoc($cRes)): 
            $cid    = (int)$c['id'];
            $cAuth  = (int)$c['user_id'];
          ?>
            <div class="comment mb-2 ps-3">
              <p class="mb-1"><?= nl2br(htmlspecialchars($c['body'], ENT_QUOTES)) ?></p>
              <p class="text-muted small mb-0">
                By <?= htmlspecialchars($c['username'], ENT_QUOTES) ?>
                on <?= date('F j, Y, g:i A', strtotime($c['created_at'])) ?>
                <?php if ($userId === $cAuth): ?>
                  &nbsp;|&nbsp;
                  <a href="edit_comment.php?comment_id=<?= $cid ?>&question_id=<?= $qid ?>&answer_id=<?= $aid ?>" class="small">
                    Edit
                  </a>
                <?php endif; ?>
              </p>
            </div>
          <?php endwhile;
        endif;
        ?>

        <!-- Comment form on this answer -->
        <?php if ($userId): ?>
          <form action="add_comment.php" method="POST" class="mt-3">
            <input type="hidden" name="answer_id"  value="<?= $aid ?>">
            <input type="hidden" name="origin_qid" value="<?= $qid ?>">
            <div class="mb-2">
              <textarea 
                name="body" 
                class="form-control form-control-sm" 
                rows="2" 
                placeholder="Comment on this answer…" 
                required
              ></textarea>
            </div>
            <button class="btn btn-sm btn-secondary">Comment</button>
          </form>
        <?php endif; ?>

      </div>
    </div>
    <?php
}
