<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name']; // <<< Yeh line honi chahiye

