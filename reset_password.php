<?php
require 'database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];


    echo "Token: $token<br>";

    $result = $conn->query("SELECT * FROM users WHERE reset_token = '$token'");
    $row = $result->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";



    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        die("Invalid or expired token.");
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $update->bind_param("si", $new_password, $user_id);
        $update->execute();

        echo "<div class='alert alert-success text-center mt-3'>Password has been reset. <a href='login.php'>Login now</a></div>";
        exit;
    }
} else {
    die("Token missing.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Reset Password</h2>
        <form method="post" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Reset Password</button>
        </form>
    </div>
</body>

</html>