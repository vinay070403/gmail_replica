<?php
session_start();
require '../database.php'; // connection to database

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch sent emails
$stmt = $conn->prepare("
    SELECT 
        m.id AS mail_id,
        m.subject,
        m.message,
        m.created_at,
        GROUP_CONCAT(u.email) AS recipients
    FROM mails m
    JOIN mail_recipients mr ON m.id = mr.mail_id
    JOIN users u ON mr.receiver_id = u.id
    WHERE m.user_id = ?
    GROUP BY m.id
    ORDER BY m.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- HTML Start -->
<div class="email-list">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($email = $result->fetch_assoc()): ?>
            <div class="email-item">
                <div class="email-sender">
                    To: <?= htmlspecialchars($email['recipients']) ?>
                </div>
                <div class="email-subject">
                    <?= htmlspecialchars($email['subject']) ?>
                </div>
                <div class="email-preview">
                    <?= htmlspecialchars(substr($email['message'], 0, 100)) ?>...
                </div>
                <div class="email-time">
                    <?= date('M j', strtotime($email['created_at'])) ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-emails">
            <i class="fas fa-paper-plane fa-3x mb-3"></i>
            <h4>No sent emails</h4>
            <p>You haven't sent any emails yet.</p>
        </div>
    <?php endif; ?>
</div>
