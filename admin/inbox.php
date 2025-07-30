<?php
require 'auth2.php';
require '../database.php';

$stmt = $conn->prepare("
    SELECT 
        m.id as mail_id,
        m.subject,
        m.message,
        m.created_at,
        u.name as sender_name,
        u.email as sender_email,
        mr.is_read,
        mr.is_favorite,
        mr.type,
        mr.deleted_at,
        mr.receiver_id,
        mr.id as recipient_id
    FROM mails m 
    JOIN mail_recipients mr ON m.id = mr.mail_id 
    JOIN users u ON m.user_id = u.id 
    WHERE mr.receiver_id = ? 
    ORDER BY m.created_at DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

//echo "<pre>";
//while ($row = $result->fetch_assoc()) {
//    print_r($row);
//}
//echo "</pre>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Gmail Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f6f8fa;
            margin: 0;
            padding: 0;
        }

        .gmail-header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .menu-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #5f6368;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
        }

        .menu-btn:hover {
            background-color: #f1f3f4;
        }

        .search-container {
            flex: 1;
            max-width: 600px;
            margin: 0 16px;
        }

        .search-box {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f1f3f4;
            font-size: 16px;
        }

        .search-box:focus {
            outline: none;
            background-color: white;
            border-color: #1a73e8;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #1a73e8;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .main-container {
            display: flex;
            height: calc(100vh - 64px);
        }

        .sidebar {
            width: 256px;
            background: white;
            border-right: 1px solid #e0e0e0;
            padding: 16px 0;
            overflow-y: auto;
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 64px;
        }

        .sidebar.collapsed .sidebar-item span,
        .sidebar.collapsed .compose-btn span {
            display: none;
        }

        .sidebar.collapsed .sidebar-item {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .compose-btn {
            margin: 0 8px 16px;
            padding: 12px;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar.collapsed .compose-btn i {
            margin: 0;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: #5f6368;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .sidebar-item:hover {
            background-color: #f1f3f4;
            color: #202124;
        }

        .sidebar-item.active {
            background-color: #e8f0fe;
            color: #1a73e8;
            font-weight: 500;
        }

        .sidebar-item i {
            margin-right: 16px;
            width: 20px;
        }

        .compose-btn {
            background-color: #c2e7ff;
            color: #001d35;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            margin: 0 16px 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .compose-btn:hover {
            background-color: #a8dadc;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .compose-btn i {
            margin-right: 8px;
        }

        .content-area {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
            background: white;
        }

        .tab {
            padding: 16px 24px;
            border: none;
            background: none;
            color: #5f6368;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab.active {
            color: #1a73e8;
            border-bottom-color: #1a73e8;
        }

        .tab:hover {
            background-color: #f1f3f4;
        }

        .email-list {
            flex: 1;
            overflow-y: auto;
        }

        .email-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f1f3f4;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .email-item:hover {
            background-color: #f8f9fa;
        }

        .email-item.unread {
            background-color: #f2f6fc;
            font-weight: 500;
        }

        .email-checkbox {
            margin-right: 16px;
        }

        .email-star {
            margin-right: 16px;
            color: #5f6368;
            cursor: pointer;
        }

        .email-star.favorite {
            color: #f4b400;
        }

        .email-sender {
            width: 200px;
            font-weight: 500;
            color: #202124;
        }

        .email-subject {
            flex: 1;
            color: #202124;
            margin: 0 16px;
        }

        .email-preview {
            flex: 1;
            color: #5f6368;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email-time {
            color: #5f6368;
            font-size: 12px;
            white-space: nowrap;
        }

        .no-emails {
            text-align: center;
            padding: 40px;
            color: #5f6368;
        }

        .logout-btn {
            color: #dc3545;
            text-decoration: none;
        }

        .logout-btn:hover {
            color: #c82333;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -256px;
                top: 64px;
                height: calc(100vh - 64px);
                z-index: 1000;
                transition: left 0.3s ease;
                background: white;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            }

            .sidebar.sidebar-open {
                left: 0;
            }

            .search-container {
                margin: 0 16px;
            }

            .email-sender {
                width: 120px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="gmail-header">
        <button class="menu-btn" id="menuBtn">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-container">
            <input type="text" class="search-box" placeholder="Search mail">
        </div>

        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
            <span><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="admin/logout.php" class="logout-btn ms-2">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <a href="inbox.php?compose=true" class="compose-btn">
                <i class="fas fa-plus"></i>
                <span>Compose</span>
            </a>

            <a href="inbox.php" class="sidebar-item active">
                <i class="fas fa-inbox"></i>
                <span>Inbox</span>
            </a>

            <a href="sent.php" class="sidebar-item">
                <i class="fas fa-paper-plane"></i>
                <span>Sent</span>
            </a>

            <a href="favorite.php" class="sidebar-item">
                <i class="fas fa-file-alt"></i>
                <span>Favorite</span>
            </a>

            <a href="trash.php" class="sidebar-item">
                <i class="fas fa-trash"></i>
                <span>Trash</span>
            </a>

            <div style="border-top: 1px solid #e0e0e0; margin: 16px 0;"></div>

            <div class="sidebar-item">
                <i class="fas fa-tag"></i>
                <span>Labels</span>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div id="main-content">
                <?php if (isset($_GET['compose'])): ?>
                    <?php include 'compose.php'; ?>
                <?php else: ?>
                    <!-- Email List -->
                    <div class="email-list">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($email = $result->fetch_assoc()): ?>
                                <div class="email-item <?= $email['is_read'] ? '' : 'unread' ?>">
                                    <div class="email-sender">
                                        <strong>From:</strong> <?= htmlspecialchars($email['sender_name'] ?? 'Unknown') ?>
                                    </div>
                                    <div class="email-subject">
                                        <?= htmlspecialchars($email['subject']) ?>
                                    </div>
                                    <div class="email-preview">
                                        <?= htmlspecialchars(substr($email['message'], 0, 100)) ?>...
                                    </div>
                                    <div class="email-time">
                                        <?= date('M j', strtotime($email['created_at'])) ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="no-emails">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h4>Your inbox is empty</h4>
                                <p>No emails to display</p>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile and desktop
        document.addEventListener('DOMContentLoaded', function() {
            const menuBtn = document.getElementById('menuBtn');
            const sidebar = document.getElementById('sidebar');

            menuBtn.addEventListener('click', function() {
                // Toggle collapsed state for desktop
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                } else {
                    // Toggle sidebar open/close for mobile
                    sidebar.classList.toggle('sidebar-open');
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
                        sidebar.classList.remove('sidebar-open');
                    }
                }
            });
        });

        function viewEmail(mailId) {
            window.location.href = 'view_mail.php?id=' + mailId;
        }

        function toggleFavorite(recipientId, event) {
            event.stopPropagation();
            // AJAX call to toggle favorite status
            fetch('toggle_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        recipient_id: recipientId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const star = event.target.closest('.email-star');
                        star.classList.toggle('favorite');
                    }
                });
        }
    </script>
</body>

</html>