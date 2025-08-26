<?php
session_start();
<<<<<<< HEAD

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
=======
session_unset();      // Remove all session variables
session_destroy();    // Destroy the session

header("Location: auth/login.php"); // Redirect to login
exit;
>>>>>>> 139356b (add)
