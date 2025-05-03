<?php
include 'config.php';   // starts session and gives you $conn

// 1) Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// 2) Grab & sanitize inputs
$email = mysqli_real_escape_string($conn, $_POST['email']);
$pass  = $_POST['password'];

// 3) Fetch the user by email
$sql = "
    SELECT id, password_hash
    FROM users
    WHERE email = '$email'
    LIMIT 1
";
$res = mysqli_query($conn, $sql);

// 4) Check credentials
if ($row = mysqli_fetch_assoc($res)) {
    if (password_verify($pass, $row['password_hash'])) {
        // Login successful
        $_SESSION['user_id'] = $row['id'];
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['error'] = 'Incorrect password.';
    }
} else {
    $_SESSION['error'] = 'No account found with that email.';
}

// 5) On failure, redirect back to the form
header('Location: login.php');
exit;
