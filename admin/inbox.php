<?php
//require 'auth2.php';
require '../database.php';
session_start();

$stmt = $conn->prepare("
    SELECT 
        m.id as mail_id,
        m.subject,
        m.message,
        m.created_at,
        u.name as sender_name,
        u.email as sender_email,
        mr.is_read,
        mr.is_favorite,
        mr.type,
        mr.deleted_at,
        mr.receiver_id,
        mr.id as recipient_id
    FROM mails m 
    JOIN mail_recipients mr ON m.id = mr.mail_id 
    JOIN users u ON m.user_id = u.id 
    WHERE mr.receiver_id = ? 
    ORDER BY m.created_at DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();


// $page = $_GET['page'] ?? '';
// $allowed = ['sent', 'favorite', 'trash'];

// if (in_array($page, $allowed)) {
//     include("$page.php");
// }



//echo "<pre>";
//while ($row = $result->fetch_assoc()) {
//    print_r($row);
//}
//echo "</pre>";


?>

<?php include('../includes/header.php'); ?>

<div class="main-container">
    <?php include('../includes/sidebar.php'); ?>

    <div class="content-area">
        <div id="main-content">
            <?php if (isset($_GET['compose'])): ?>
                <?php include 'compose.php'; ?>
            <?php else: ?>
                <div class="email-list">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($email = $result->fetch_assoc()): ?>
                            <div class="email-item <?= $email['is_read'] ? '' : 'unread' ?>">
                                <div class="email-sender">
                                    <?= htmlspecialchars($email['sender_name'] ?? 'Unknown') ?>
                                </div>
                                <div class="email-subject">
                                    <a href="view_mail.php?id=<?= $email['mail_id'] ?>">
                                        <?= htmlspecialchars($email['subject']) ?>
                                    </a>
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
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h4>Your inbox is empty</h4>
                            <p>No emails to display</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>