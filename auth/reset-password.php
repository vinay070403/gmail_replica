<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

// 1. Make sure token is present in URL
if (!isset($_GET['token'])) {
    $error = "Invalid or missing token.";
} else {
    $token = $_GET['token'];

    // 2. Check if token exists in DB
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid token.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 3. Handle password update
        if (isset($_POST['password'])) {
            $password = $_POST['password'];

            if ($password === '') {
                $error = "Password cannot be empty.";
            } else {
                // 4. Update password (plain password as requested)
                $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE id = ?");
                $stmt->execute([$password, $user['id']]);

                $success = "Password updated successfully. <a href='login.php'>Login now</a>";
            }
        } else {
            $error = "Invalid request.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Gmail Replica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Reset Password</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php elseif (isset($_GET['token'])): ?>
            <form method="post">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
