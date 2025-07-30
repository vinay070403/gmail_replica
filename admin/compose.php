<?php
//require 'auth2.php';
require '../database.php';



// Fetch all users except current for dropdown
$users_stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id != ?");
$users_stmt->bind_param("i", $_SESSION['user_id']);
$users_stmt->execute();
$users_result = $users_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $recipients = $_POST['recipients']; // array of user IDs
    $user_id = $_SESSION['user_id'];

    // Insert into mails table
    $stmt = $conn->prepare("INSERT INTO mails (user_id, subject, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $subject, $message);
    $stmt->execute();
    $mail_id = $stmt->insert_id;

    // Insert into mail_recipients
    $rec_stmt = $conn->prepare("INSERT INTO mail_recipients (mail_id, receiver_id, type) VALUES (?, ?, ?)");
    $type = 'inbox';
    foreach ($recipients as $receiver_id) {
        $rec_stmt->bind_param("iis", $mail_id, $receiver_id, $type);
        $rec_stmt->execute();
    }


   // header("Location: sent.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Compose - Gmail Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <h2>Compose New Email</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="recipients" class="form-label">To:</label>
            <select name="recipients[]" id="recipients" class="form-select" multiple required>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message:</label>
            <textarea name="message" id="message" class="form-control" rows="8" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
        <a href="inbox.php" class="btn btn-secondary">Cancel</a>
    </form>
</body>

</html>