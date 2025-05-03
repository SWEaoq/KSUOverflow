<?php
// header.php

// 1) Only start a session if none exists
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';      // gives you $conn

// 2) If the user is logged in, grab their username
$userName = null;
if (!empty($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
    $res = mysqli_query(
        $conn,
        "SELECT username FROM users WHERE id = {$uid} LIMIT 1"
    );
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $userName = $row['username'];
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">KSUOverflow</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Search form -->
      <form class="d-flex me-auto" action="search.php" method="GET">
        <input
          class="form-control form-control-sm me-2"
          type="search"
          name="q"
          placeholder="Search questions…"
          aria-label="Search"
          value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
          required
        >
        <input
          type="hidden"
          name="sort"
          value="<?= htmlspecialchars($_GET['sort'] ?? 'newest', ENT_QUOTES, 'UTF-8') ?>"
        >
        <button class="btn btn-sm btn-light" type="submit">Search</button>
      </form>

      <ul class="navbar-nav ms-auto align-items-center">
        <?php if ($userName): ?>
          <li class="nav-item">
            <span class="nav-link">Hi, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="ask.php">Ask Question</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
