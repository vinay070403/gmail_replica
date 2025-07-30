<?php
require 'auth.php'; // Checks login session
require 'database.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT m.id, m.subject, m.message, m.created_at, u.name AS sender_name
    FROM mails m
    JOIN mail_recipients r ON m.id = r.mail_id
    JOIN users u ON m.user_id = u.id
    WHERE r.receiver_id = ? AND r.type = 'inbox'
    ORDER BY m.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h2>📥 Inbox</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?> | <a href="compose.php">Compose</a> | <a
                href="logout.php">Logout</a></p>

        <table class="table table-hover bg-white shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr onclick="window.location='view_mail.php?id=<?= $row['id'] ?>'" style="cursor: pointer;">
                    <td><?= htmlspecialchars($row['sender_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= date("d M Y, H:i", strtotime($row['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No emails found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>

</html>