<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mail_id'], $_POST['labels'])) {
    $mail_id = $_POST['mail_id'];
    $labels = $_POST['labels'];

    foreach ($labels as $label_id) {
        // Avoid duplicate entries
        $check = $pdo->prepare("SELECT 1 FROM label_mails WHERE mail_id = ? AND label_id = ?");
        $check->execute([$mail_id, $label_id]);
        if (!$check->fetch()) {
            $insert = $pdo->prepare("INSERT INTO label_mails (mail_id, label_id) VALUES (?, ?)");
            $insert->execute([$mail_id, $label_id]);
        }
    }
}

header("Location: inbox.php");
exit;
