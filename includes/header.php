<?php
<<<<<<< HEAD
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Gmail Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/bootstrap.min.css">

    <!-- ✅ Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- ✅ jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f6f8fa;
            margin: 0;
            padding: 0;
        }

        .gmail-header {
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 64px;
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
            padding: 10px 20px;
            border-radius: 24px;
            background-color: #eef3f8;
            border: none;
            font-size: 15px;
            transition: all 0.2s;
        }

        .search-box:focus {
            outline: none;
            background-color: white;
            box-shadow: 0 0 0 2px #c2dbff;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-container {
            display: flex;
            height: calc(100vh - 64px);
        }

        .sidebar {
            width: 256px;
            background: whitesmoke;
            border-right: 1px solid #959191ff;
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
            background-color: #f8fafc;
        }

        .email-item {
            display: grid;
            grid-template-columns: 180px 1fr 1fr 80px;
            align-items: center;
            padding: 10px 16px;
            border-bottom: 1px solid #f1f3f4;
            background-color: #ffffff;
            font-size: 14px;
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .email-item:hover {
            background-color: #f1f3f4;
            box-shadow: inset 0 0 0 1px #e0e0e0;
        }

        .email-item.unread {
            background-color: #eef3fb;
            font-weight: 600;
        }

        .email-sender {
            color: #202124;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .email-subject {
            color: #202124;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding: 0 10px;
        }

        .email-preview {
            color: #5f6368;
            font-size: 13px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            padding-right: 10px;
        }

        .email-time {
            color: #5f6368;
            font-size: 12px;
            text-align: right;
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

        .user-name {
            font-weight: 500;
            color: #333;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #eee;
        }

        .list-group-item:hover {
            background-color: #f9f9f9;
            cursor: pointer;
        }

        .list-group-item .fw-bold {
            font-weight: 500;
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
    <div class="gmail-header shadow-sm">
        <button class="menu-btn" id="menuBtn" aria-label="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>

        <div class="search-container">
            <input type="text" class="search-box" placeholder="Search mail">
        </div>

        <div class="user-info">
            <?php if (isset($_SESSION['user_name'])): ?>
                <?php
                $initial = strtoupper(substr($_SESSION['user_name'], 0, 1));
                ?>
                <span class="user-avatar"><?= $initial ?></span>
                <span class="user-name d-none d-md-inline"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <?php else: ?>
                <span class="text-danger">[No name found]</span>
            <?php endif; ?>

            <a href="../logout.php" class="ms-3 logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </div>
    </div>
=======
// Make sure session is started and user info is fetched before this
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gmail Replica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>


</head>

<body>

    <!-- TOP HEADER BAR -->
    <div class="topbar">
        <div class="left">
            <i class="fas fa-bars menu-icon"></i>
            <img src="../assets/images/gmail-logo.png" alt="Gmail Logo" class="gmail-logo">
            <span class="gmail-title">Gmail</span>
        </div>
        <div class="center">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search mail">
                <i class="fas fa-sliders-h filter-icon"></i>
            </div>
        </div>
        <div class="right">
            <i class="fas fa-question-circle"></i>
            <i class="fas fa-cog"></i>
            <i class="fas fa-moon"></i>
            <i class="fas fa-th"></i>
            <div class="user-menu-wrapper">
                <div class="user-avatar" onclick="toggleMenu()"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAACXBIWXMAAAsTAAALEwEAmpwYAAABoUlEQVR4nO2WsUoDQRCGP7BSyyiiJiqClT5EsBQNES30FdTE1xCsLIOawiiIhaaJsdHXMAqCCGJttDAxIbIwkSVcdid3Ihb+MLAs9+1/M7e7c/CvP6gJIANcARXgXaIic1tA4icNx4Ec0ABanmgCZ8BUVNM08KYw7IwqkAprui0Z9GpqZ58Nk2kzgqltrs487invPbAADAIjwLGi7GMa40PHInVgJoCZB2oObl9zZFy7t+hgiw6uIZXsqoynbLsOdsfDbrqMyx4472DzHrbkMr7zwAUHW/CwFZdx1QNfA30BnJm7Uezu0MYmVgK4VQVXjVJqEw9AzGJiMteKUuqyYgETSYtJKpmSy3hLscAjMGQxZvyk4DZcxokuF4i5lU7kDu8P4MzcsjxTD+A/fReI0UEH9AzMotcc8NKxRk7b+O3dHaavpi3+FRjVgimrLZprtFdlhTVrLIWB2+bn0kB8mgQuLNMwL/2debvsH8ApsAZMAwMSZrwu/1o1q7yLRNQwsCc703dkTJZHvXxTjeLS2i6BW/lLeZNxSc6p98j8i9/WF3EwRr6LVHl0AAAAAElFTkSuQmCC" alt="user-male-circle"></div>
                <div class="user-dropdown" id="userDropdown">
                    <span><?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="profile.php">Profile</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Action Bar -->
    <!-- <div class="top-action-bar">
    <input type="checkbox" id="select-all">

    <button class="icon-btn" title="Refresh" onclick="location.reload();">🔄</button>

    <form method="post" action="bulk-delete.php" style="display:inline;">
        <button class="icon-btn" type="submit" name="delete" title="Delete Selected">🗑️</button>
    </form>

    <div class="dropdown">
        <button class="icon-btn" title="More options">⋮</button>
        <div class="dropdown-content"> -->
    <!-- <button type="button" onclick="markAllRead()">Mark all as read</button>
        </div>
    </div>
</div> -->


    <div class="container">
>>>>>>> 139356b (add)
