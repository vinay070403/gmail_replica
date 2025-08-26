<?php
session_start();

require_once '../config/database.php';

// Redirect to inbox if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../admin/inbox.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only proceed if both inputs are set
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($email === '' || $password === '') {
            $error = "Please fill in all fields.";
        } else {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../admin/inbox.php");
                exit;
            } else {
                $error = "Invalid email or password.";
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
    <title>Login - Gmail Replica</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            width: 380px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            padding: 30px;
        }

        .login-card h2 {
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-custom {
            background: #4285F4;
            color: #fff;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-custom:hover {
            background: #3367d6;
        }

        .extra-links {
            margin-top: 10px;
            text-align: center;
        }

        .extra-links a {
            text-decoration: none;
            color: #4285F4;
            font-weight: 500;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-custom w-100">Login</button>
        </form>

        <div class="extra-links">
            <a href="forgot-password.php">Forgot Password?</a>
        </div>

        <div class="extra-links mt-2">
            Don’t have an account? <a href="register.php">Register here</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>