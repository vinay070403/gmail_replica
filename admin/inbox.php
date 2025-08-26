<<<<<<< HEAD
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
=======
<?php include '../includes/auth.php'; ?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$user_id = $_SESSION['user_id'];

//echo "Logged in as user ID: " . $_SESSION['user_id'];

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $mail_id = $_GET['delete'];
    $stmt = $pdo->prepare("UPDATE mail_recipients SET type = 1 WHERE mail_id = ? AND receiver_id = ?");
    //echo "Updated rows: " . $stmt->rowCount();
    $stmt->execute([$mail_id, $user_id]);
    header("Location: inbox.php");
    exit;
}

// Handle favorite toggle request
if (isset($_GET['fav']) && is_numeric($_GET['fav'])) {
    $mail_id = $_GET['fav'];
    $stmt = $pdo->prepare("SELECT is_favorite FROM mail_recipients WHERE mail_id = ? AND receiver_id = ?");
    $stmt->execute([$mail_id, $user_id]);
    $row = $stmt->fetch();

    $new_status = ($row && $row['is_favorite']) ? 0 : 1;
    $update = $pdo->prepare("UPDATE mail_recipients SET is_favorite = ? WHERE mail_id = ? AND receiver_id = ?");
    $update->execute([$new_status, $mail_id, $user_id]);

    header("Location: inbox.php");
    exit;
}

// Fetch inbox mails
$stmt = $pdo->prepare("
    SELECT m.id, m.subject, m.message, m.created_at, u.email AS sender_email, mr.is_favorite
    FROM mail_recipients mr
    JOIN mails m ON m.id = mr.mail_id
    JOIN users u ON u.id = m.user_id
    WHERE mr.receiver_id = ? AND mr.type = 0 AND mr.deleted_at IS NULL
    ORDER BY m.created_at DESC
");

$stmt->execute([$user_id]);
$inboxMails = $stmt->fetchAll();
?>                                                                      


<?php if (empty($inboxMails)): ?>
    <p>No inbox mails found.</p>
<?php else: ?>
    <div class="inbox-list">
    <?php foreach ($inboxMails as $mail): ?>
        <div class="mail-row">

            <!-- Checkbox -->
            <input type="checkbox" class="select-mail" name="selected_mails[]" value="<?php echo $mail['id']; ?>">

            <!-- Star/Favorite -->
            <a href="?fav=<?php echo $mail['id']; ?>" class="mail-star">
                <?php echo $mail['is_favorite'] ? '★' : '☆'; ?>
            </a>

            <!-- Sender -->
            <span class="mail-sender">
                <?php echo htmlspecialchars($mail['sender_email']); ?>
            </span>

            <!-- Subject + Snippet -->
            <div class="mail-subject-snippet">
                <strong><?php echo htmlspecialchars($mail['subject']); ?></strong>
                - <?php echo htmlspecialchars(substr(strip_tags($mail['message']), 0, 50)); ?>...
            </div>

            <!-- Date -->
            <div class="mail-date">
                <?php echo date('M d', strtotime($mail['created_at'])); ?>
            </div>

            <!-- Delete Icon -->
            <a href="?delete=<?php echo $mail['id']; ?>" onclick="return confirm('Move to trash?');" title="Delete" class="mail-delete">
                🗑️
            </a>
        </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php include '../includes/footer.php'; ?>
>>>>>>> 139356b (add)
