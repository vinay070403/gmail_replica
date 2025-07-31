<?php
require 'database.php';
//require 'auth.php';

$errors = [];
$email = '';





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If no validation errors, attempt login
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header("Location: admin/inbox.php");
                exit;
            } else {
                $errors[] = "Invalid password. Please try again.";
            }
        } else {
            $errors[] = "Email not found. Please check your email or register.";
        }
    }
}
?>
<!-- Login Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #1DB954, #191414);
            /* Spotify style */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #1DB954;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1aa34a;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body class="bg-light">

    <div class="card bg-white">
        <h2 class="mb-4">Login</h2>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class='alert alert-success'>Registration successful! Please log in.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <a href="register.php" class="btn btn-link w-100">Don't have an account?</a>
            <a href="forgot_password.php" class="btn btn-link w-100">Forgot password?</a>
        </form>
    </div>

</body>

</html>