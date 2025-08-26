<<<<<<< HEAD <?php
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
?> <div class="container py-4">
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
    =======
    <?php include '../includes/auth.php'; ?>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    <?php require_once '../config/database.php'; ?>

    <?php
// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['receiver_email']) &&
        isset($_POST['subject']) &&
        isset($_POST['message'])
    ) {
        $receiver_email = $_POST['receiver_email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $user_id = $_SESSION['user_id'];

        // Find receiver by email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$receiver_email]);
        $receiver = $stmt->fetch();

        if ($receiver) {
            $receiver_id = $receiver['id'];

            // Insert into mails table
            $stmt = $pdo->prepare("INSERT INTO mails (user_id, subject, message, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$user_id, $subject, $message]);
            $mail_id = $pdo->lastInsertId();

            echo "Mail inserted with ID: " . $mail_id . "<br>";

            // Insert into mail_recipients table
            $stmt = $pdo->prepare("INSERT INTO mail_recipients (mail_id, receiver_id, type, is_read, is_favorite, created_at) VALUES (?, ?, 'inbox', 0, 0, NOW())");
            $stmt->execute([$mail_id, $receiver_id]);

            echo "Receiver ID: " . $receiver_id . "<br>";

            // Optional: Handle attachment
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['attachment']['tmp_name'];
                $file_name = basename($_FILES['attachment']['name']);
                $upload_dir = '../assets/uploads/';
                $target_file = $upload_dir . $file_name;

                // Create folder if not exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $stmt = $pdo->prepare("INSERT INTO mail_attachments (mail_id, file_name) VALUES (?, ?)");
                    $stmt->execute([$mail_id, $file_name]);
                }
            }

            $success = "Mail sent successfully.";
        } else {
            $error = "Receiver email not found.";
        }
    } else {
        $error = "Please fill all required fields.";
    }
}
?>

    <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
    <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="compose-container">
        <form method="post" enctype="multipart/form-data">
            <label>To (Receiver Email):</label>
            <input type="email" name="receiver_email" required>

            <label>Subject:</label>
            <input type="text" name="subject" required>

            <label>Message:</label>
            <textarea name="message" rows="6" required></textarea>

            <label>Attachment (optional):</label>
            <input type="file" name="attachment">

            <button type="submit">Send</button>
        </form>
    </div>


    <?php include '../includes/footer.php'; ?>
    >>>>>>> 139356b (add)