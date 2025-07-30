<?php
session_start();

// If user is logged in, destroy session
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    header("Location: login.php?message=logged_out");
    exit;
}

// If already logged out, redirect anyway
header("Location: login.php");
exit;
?>
