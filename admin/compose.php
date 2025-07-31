<?php
//require 'auth2.php';
require '../database.php';
//include('../includes/header.php');

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

    // if ($stmt->affected_rows > 0) {
    //     echo "Mail inserted successfully. Mail ID: " . $mail_id . "<br>";
    // } else {
    //     echo "Failed to insert mail into database. Error: " . $stmt->error;
    // }

    // Insert into mail_recipients
    $rec_stmt = $conn->prepare("INSERT INTO mail_recipients (mail_id, receiver_id, type) VALUES (?, ?, ?)");
    $type = 'inbox';
    foreach ($recipients as $receiver_id) {
        $rec_stmt->bind_param("iis", $mail_id, $receiver_id, $type);
        $rec_stmt->execute();
    }
    // if ($rec_stmt->affected_rows > 0) {
    //     echo "Recipient (User ID: $receiver_id) added successfully.<br>";
    // } else {
    //     echo "Failed to add recipient ID $receiver_id. Error: " . $rec_stmt->error;
    // }

    // echo "Mail inserted. ID: $mail_id<br>";
    // echo "Recipients:<br>";
    // print_r($recipients);
    // die(); // Stop here to check output


    //header("Location: sent.php");
    exit;
}
?>


<div class="container py-4">
    <h2>New Email</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="recipients">To:</label>
            <select name="recipients[]" id="recipients" class="form-select" multiple required>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <option value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- CC/BCC toggles -->
            <div style="margin-top: 10px;">
                <a href="#" onclick="toggleField('ccField'); return false;">Cc</a> |
                <a href="#" onclick="toggleField('bccField'); return false;">Bcc</a>
            </div>

            <!-- CC Field -->
            <div id="ccField" style="display: none; margin-top: 10px;">
                <label for="cc">Cc:</label>
                <select name="cc[]" id="cc" class="select-user" multiple>
                    <?php mysqli_data_seek($users_result, 0);
                    while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- BCC Field -->
            <div id="bccField" style="display: none; margin-top: 10px;">
                <label for="bcc">Bcc:</label>
                <select name="bcc[]" id="bcc" class="form-select" multiple>
                    <?php mysqli_data_seek($users_result, 0);
                    while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?= $user['id'] ?>">
                            <?= htmlspecialchars($user['name']) ?> (<?= $user['email'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

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
</div>
<?php include('../includes/footer.php'); ?>