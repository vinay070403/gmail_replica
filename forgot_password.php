<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(16));
    date_default_timezone_set('Asia/Kolkata'); // Or your actual zone
    $expiry = date('Y/m/d H:i:s', strtotime('+5 hour'));

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $update->bind_param("ssi", $token, $expiry, $user_id);
        $update->execute();

        // Simulate sending email (show link for now)
        echo "<div class='alert alert-success text-center mt-3'>
                Password reset link: <a href='reset_password.php?token=$token'>Click here to reset</a>
              </div>";
    } else {
        echo "<div class='alert alert-danger text-center mt-3'>Email not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Forgot Password</h2>
        <form method="post" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label>Enter your email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
            <a href="login.php" class="btn btn-link">Back to login</a>
        </form>
    </div>
</body>

</html>