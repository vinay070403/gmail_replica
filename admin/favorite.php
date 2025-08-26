<?php include '../includes/auth.php'; ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$user_id = $_SESSION['user_id'];

if (isset($_GET['unfav']) && is_numeric($_GET['unfav'])) {
    $mail_id = $_GET['unfav'];
    $stmt = $pdo->prepare("UPDATE mail_recipients SET is_favorite = 0 WHERE mail_id = ? AND receiver_id = ?");
    $stmt->execute([$mail_id, $user_id]);
    header("Location: favorite.php");
    exit;
}

// Fetch favorite mails for this user
$stmt = $pdo->prepare("
    SELECT m.id, m.subject, m.message, m.created_at, u.email AS sender_email
    FROM mail_recipients mr
    JOIN mails m ON m.id = mr.mail_id
    JOIN users u ON u.id = m.user_id
    WHERE mr.receiver_id = ? AND mr.is_favorite = 1 AND mr.type = 0
    ORDER BY m.created_at DESC
");
$stmt->execute([$user_id]);
$favMails = $stmt->fetchAll();
?>

<?php if (empty($favMails)): ?>
    <p>No favorite mails.</p>
<?php else: ?>
    <div class="inbox-list">
        <?php foreach ($favMails as $mail): ?>
            <div class="mail-row">

                <input type="checkbox" class="select-mail" name="selected_mails[]" value="<?php echo $mail['id']; ?>">


                <a href="?unfav=<?php echo $mail['id']; ?>" class="mail-star" title="Unmark Favorite">
                    ⭐
                </a>

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
