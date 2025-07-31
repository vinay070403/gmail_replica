<?php
session_start();
require '../database.php';
// require 'auth2.php'; // If needed

 // Just in case it's missing
//echo "Logged in as user ID: " . ($_SESSION['user_id'] ?? 'Not set');

$user_id = $_SESSION['user_id'] ?? null;

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

<?php include('../includes/header.php'); ?>

<div class="main-container">
    <?php include('../includes/sidebar.php'); ?>

    <div class="content-area">
        <div class="container mt-3">
            <h4 class="mb-3">Sent Emails</h4>

            <?php if ($result->num_rows > 0): ?>
                <div class="list-group">
                    <?php while ($email = $result->fetch_assoc()): ?>
                        <a href="view_mail.php?id=<?= $email['mail_id'] ?>" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex w-100 justify-content-between">
                                <div class="fw-bold text-muted">
                                    To: <?= htmlspecialchars($email['recipients']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= date('g:i A', strtotime($email['created_at'])) ?>
                                </small>
                            </div>
                            <div class="fw-semibold text-dark mt-1">
                                <?= htmlspecialchars($email['subject']) ?>
                            </div>
                            <div class="text-muted small mt-1">
                                <?= htmlspecialchars(substr($email['message'], 0, 80)) ?>...
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>


            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-paper-plane fa-3x mb-3"></i>
                    <h4>No sent emails</h4>
                    <p>You haven't sent any emails yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>