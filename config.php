<?php

// only start the session if one isn’t already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = '127.0.0.1';
$DB_PORT = 3307;
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'ksuoverflow';

$conn = mysqli_connect(
  $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT
) or die('Connect error: ' . mysqli_connect_error());
?>
