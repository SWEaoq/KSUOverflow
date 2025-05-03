<?php
// register_process.php
require_once 'config.php';   // starts session

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// 1) Sanitize & grab inputs
$first     = mysqli_real_escape_string($conn, $_POST['first_name']      ?? '');
$last      = mysqli_real_escape_string($conn, $_POST['last_name']       ?? '');
$username  = mysqli_real_escape_string($conn, $_POST['username']        ?? '');
$email     = mysqli_real_escape_string($conn, $_POST['email']           ?? '');
$pass1     = $_POST['password']        ?? '';
$pass2     = $_POST['confirm_password']?? '';

// 2) validation
if (!$first || !$last || !$username || !$email || !$pass1 || !$pass2) {
    $_SESSION['error'] = 'All fields are required.';
    header('Location: register.php');
    exit;
}

// 3) Password match check
if ($pass1 !== $pass2) {
    $_SESSION['error'] = 'Passwords do not match.';
    header('Location: register.php');
    exit;
}

// 4) Unique-username check
$res = mysqli_query(
    $conn,
    "SELECT id FROM users WHERE username = '$username' LIMIT 1"
);
if (mysqli_num_rows($res) > 0) {
    $_SESSION['error'] = 'That username is already taken.';
    header('Location: register.php');
    exit;
}

// 5) Duplicate-email check
$res = mysqli_query(
    $conn,
    "SELECT id FROM users WHERE email = '$email' LIMIT 1"
);
if (mysqli_num_rows($res) > 0) {
    $_SESSION['error'] = 'That email is already registered.';
    header('Location: register.php');
    exit;
}

// 6) Hash + insert
$hash = password_hash($pass1, PASSWORD_DEFAULT);

$insSql = "
    INSERT INTO users
      (first_name, last_name, username, email, password_hash)
    VALUES
      ('$first', '$last', '$username', '$email', '$hash')
";

if (mysqli_query($conn, $insSql)) {
    // Log in the new user
    $_SESSION['user_id']    = mysqli_insert_id($conn);
    $_SESSION['username']   = $username;
    $_SESSION['first_name'] = $first;
    $_SESSION['last_name']  = $last;
    
    header('Location: index.php');
    exit;
} else {
    $_SESSION['error'] = 'Registration failed: ' . mysqli_error($conn);
    header('Location: register.php');
    exit;
}
