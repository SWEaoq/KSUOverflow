<?php
header('Content-Type: application/json');
require_once 'config.php';  // starts session & gives $conn

$q = [];
if (!empty($_GET['username'])) {
    $username = mysqli_real_escape_string($conn, $_GET['username']);
    $res = mysqli_query($conn,
      "SELECT 1 FROM users WHERE username = '$username' LIMIT 1"
    );
    $q['usernameTaken'] = mysqli_num_rows($res) > 0;
}
if (!empty($_GET['email'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $res = mysqli_query($conn,
      "SELECT 1 FROM users WHERE email = '$email' LIMIT 1"
    );
    $q['emailTaken'] = mysqli_num_rows($res) > 0;
}

// If neither param supplied, error
if (empty($q)) {
    http_response_code(400);
    echo json_encode(['error' => 'No username or email specified']);
    exit;
}

echo json_encode($q);
