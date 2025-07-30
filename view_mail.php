<?php
require 'auth.php';
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: inbox.php");
    exit;
}

$mail_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT m.subject, m.message, m.created_at, u.name AS sender_name, u.email AS sender_email
    FROM mails m
    JOIN mail_recipients r ON m.id = r.mail_id
    JOIN users u ON m.user_id = u.id
    WHERE m.id = ? AND r.receiver_id = ? AND r.type = 'inbox'
");
$stmt->bind_param("ii", $mail_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Mail not found or access denied.";
    exit;
}

$mail = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($mail['subject']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <a href="inbox.php" class="btn btn-sm btn-secondary mb-3">← Back to Inbox</a>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5><?= htmlspecialchars($mail['subject']) ?></h5>
                <small>From: <?= htmlspecialchars($mail['sender_name']) ?> (<?= htmlspecialchars($mail['sender_email']) ?>)</small><br>
                <small>Sent: <?= date("d M Y, H:i", strtotime($mail['created_at'])) ?></small>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($mail['message'])) ?></p>
            </div>
        </div>
    </div>
</body>

</html>