<?php
require '../database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$mail_id = $_GET['id'] ?? null;

if (!$mail_id) {
    echo "Invalid request";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        m.subject, 
        m.message, 
        m.created_at, 
        GROUP_CONCAT(u.email) AS recipients,
        s.email AS sender_email
    FROM mails m
    JOIN mail_recipients mr ON m.id = mr.mail_id
    JOIN users u ON mr.receiver_id = u.id
    JOIN users s ON m.user_id = s.id
    WHERE m.id = ?
    GROUP BY m.id
");

$stmt->bind_param("i", $mail_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Mail not found";
    exit;
}

$mail = $result->fetch_assoc();
?>

<?php include('../includes/header.php'); ?>
<div class="main-container">
<?php include('../includes/sidebar.php'); ?>

<div class="content-area p-4">
    <h3><?= htmlspecialchars($mail['subject']) ?></h3>
    <p><strong>From:</strong> <?= htmlspecialchars($mail['sender_email']) ?></p>
    <p><strong>To:</strong> <?= htmlspecialchars($mail['recipients']) ?></p>
    <p><strong>Sent:</strong> <?= $mail['created_at'] ?></p>
    <hr>
    <p><?= nl2br(htmlspecialchars($mail['message'])) ?></p>
</div>

<?php include('../includes/footer.php'); ?>
</div>
