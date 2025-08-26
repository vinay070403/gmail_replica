<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        if ($email === '') {
            $error = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $token = bin2hex(random_bytes(16));

                // Save token to database
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);

                // Show token as reset link (for now, just as a success message)
                $success = "Reset link: <a href='reset-password.php?token=$token'>Click here to reset password</a>";
            } else {
                $error = "No account found with that email.";
            }
        }
    } else {
        $error = "Invalid request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Gmail Replica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Forgot Password</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="email">Enter your registered email</label>
            <input type="email" name="email" id="email" required>

            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
