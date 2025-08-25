<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../includes/sidebar.php';
require_once '../config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='main-content'><p>Invalid mail request.</p></div>";
    include '../includes/footer.php';
    exit;
}

$mailId = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'inbox';

if ($type === 'inbox') {
    $stmt = $pdo->prepare("
        SELECT m.id, m.subject, m.message, m.created_at,
               u.email AS sender_email, mr.is_favorite
        FROM mail_recipients mr
        JOIN mails m ON m.id = mr.mail_id
        JOIN users u ON u.id = m.user_id
        WHERE m.id = ? AND mr.receiver_id = ?
        LIMIT 1
    ");
    $stmt->execute([$mailId, $user_id]);
} elseif ($type === 'sent') {
    $stmt = $pdo->prepare("
        SELECT m.id, m.subject, m.message, m.created_at,
               GROUP_CONCAT(u.email SEPARATOR ', ') AS recipients
        FROM mails m
        LEFT JOIN mail_recipients mr ON m.id = mr.mail_id
        LEFT JOIN users u ON u.id = mr.receiver_id
        WHERE m.id = ? AND m.user_id = ?
        GROUP BY m.id
        LIMIT 1
    ");
    $stmt->execute([$mailId, $user_id]);
}

$mail = $stmt->fetch();
if (!$mail) {
    echo "<div class='main-content'><p>Mail is not found.</p></div>";
    include '../includes/footer.php';
    exit;
}

?>

<div class="main-content view-mail">


    <!-- Subject -->
    <h2 class="view-mail-subject"><?php echo htmlspecialchars($mail['subject']); ?></h2>

    <!-- Sender + Date -->
    <div class="view-mail-meta">
        <?php if ($type === 'inbox'): ?>
            <span><strong>From:</strong> <?php echo htmlspecialchars($mail['sender_email']); ?></span>
        <?php else: // sent mail 
        ?>
            <span><strong>To:</strong> <?php echo htmlspecialchars($mail['recipients']); ?></span>
        <?php endif; ?>
        <span class="mail-date"><?php echo date('M d, Y H:i', strtotime($mail['created_at'])); ?></span>
    </div>


    <!-- Body -->
    <div class="view-mail-body">
        <?php echo nl2br(htmlspecialchars($mail['message'])); ?>
    </div>

    <!-- Top bar -->
    <div class="view-mail-header">
        <a href="inbox.php" class="back-link">⬅ Back</a>

        <!-- Actions -->
        <div class="mail-actions">
            <?php if ($type === 'inbox'): ?>
                <a href="inbox.php?fav=<?php echo $mail['id']; ?>" class="mail-star">
                    <?php echo $mail['is_favorite'] ? '★' : '☆'; ?>
                </a>
            <?php endif; ?>

            <a href="<?php echo $type === 'inbox' ? 'inbox.php' : 'sent.php'; ?>?delete=<?php echo $mail['id']; ?>"
                onclick="return confirm('Move to trash?');" class="mail-delete" title="Delete">🗑️</a>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>