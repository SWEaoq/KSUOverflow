<?php
session_start();
session_unset();     // clear $_SESSION
session_destroy();   // remove server‐side session data
header('Location: index.php');
exit;
