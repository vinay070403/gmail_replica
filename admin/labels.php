<?php
session_start();
require_once '../config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle Add Label
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['label_name'])) {
    $label_name = $_POST['label_name'];

    if ($label_name === '') {
        $error = "Label name cannot be empty.";
    } else {
        // Check if label already exists for the user
        $stmt = $pdo->prepare("SELECT id FROM labels WHERE user_id = ? AND name = ?");
        $stmt->execute([$user_id, $label_name]);
        if ($stmt->fetch()) {
            $error = "Label already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO labels (user_id, name) VALUES (?, ?)");
            if ($stmt->execute([$user_id, $label_name])) {
                $success = "Label added successfully.";
            } else {
                $error = "Failed to add label.";
            }
        }
    }
}

// Handle Delete Label
if (isset($_GET['delete'])) {
    $label_id = $_GET['delete'];
    // Delete from label_mails first
    $pdo->prepare("DELETE FROM label_mails WHERE label_id = ?")->execute([$label_id]);
    // Then delete the label
    $stmt = $pdo->prepare("DELETE FROM labels WHERE id = ? AND user_id = ?");
    $stmt->execute([$label_id, $user_id]);
    header("Location: labels.php");
    exit;
}

// Fetch all labels for current user
$stmt = $pdo->prepare("SELECT id, name FROM labels WHERE user_id = ?");
$stmt->execute([$user_id]);
$labels = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Labels</h2>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="label_name">New Label Name</label>
        <input type="text" name="label_name" id="label_name" required>
        <button type="submit">Add Label</button>
    </form>

    <h3>Your Labels +</h3>
    <ul>
        <?php foreach ($labels as $label): ?>
            <li>
                <?php echo htmlspecialchars($label['name']); ?>
                <a href="labels.php?delete=<?php echo $label['id']; ?>" onclick="return confirm('Are you sure you want to delete this label?');">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <br><a href="inbox.php">← Back to Inbox</a>
</div>

<?php include '../includes/footer.php'; ?>