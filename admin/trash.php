<?php include '../includes/auth.php'; ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$user_id = $_SESSION['user_id'];

// Fetch trashed mails for current user
$stmt = $pdo->prepare("
    SELECT m.id, m.subject, m.message, m.created_at, u.email AS sender_email
    FROM mail_recipients mr
    JOIN mails m ON m.id = mr.mail_id
    JOIN users u ON u.id = m.user_id
    WHERE mr.receiver_id = ? AND mr.type = 1
    ORDER BY m.created_at DESC
");
$stmt->execute([$user_id]);
$trashedMails = $stmt->fetchAll();
?>

<?php if (empty($trashedMails)): ?>
    <p>No mails in trash.</p>
<?php else: ?>
    <div class="inbox-list">
        <?php foreach ($trashedMails as $mail): ?>
            <div class="mail-row">

                <input type="checkbox" class="select-mail" name="selected_mails[]" value="<?php echo $mail['id']; ?>">

                <!-- <div class="mail-star muted-icon" title="Trash (read only)">
                    🗑️
                </div> -->

                <div class="mail-sender">
                    <?php echo htmlspecialchars($mail['sender_email']); ?>
                </div>

                <div class="mail-subject-snippet">
                    <strong><?php echo htmlspecialchars($mail['subject']); ?></strong>
                    – <?php echo htmlspecialchars(substr($mail['message'], 0, 60)); ?>
                </div>

                <div class="mail-date">
                    <?php echo date('M d', strtotime($mail['created_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>