<?php
require 'database.php';
//require 'auth.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Name validation
    if (empty($name)) {
        $errors[] = "Name is required";
    } elseif (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    } elseif (strlen($name) > 50) {
        $errors[] = "Name must be less than 50 characters";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Name can only contain letters and spaces";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email must be less than 100 characters";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    } elseif (strlen($password) > 50) {
        $errors[] = "Password must be less than 50 characters";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, and one number";
    }

    // Confirm password validation
    if (empty($confirm_password)) {
        $errors[] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    // Check if email already exists
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Email already exists. Please use a different email or login.";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            // Redirect to login.php with a success message
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
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

        .password-requirements {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>

    <div class="card bg-white">
        <h2 class="mb-4">Register your account</h2>
        
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
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                <div class="password-requirements">Enter your full name (letters and spaces only)</div>
            </div>
            
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="password-requirements">
                    • Minimum 6 characters<br>
                    • Must contain uppercase, lowercase, and number
                </div>
            </div>
            
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <a href="login.php" class="btn btn-link w-100">Already have an account?</a>
        </form>
    </div>

</body>

</html>