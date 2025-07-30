<?php
require 'auth.php';
require 'database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>

  <!-- ✅ Bootstrap 5.3.7 CDN (your provided links) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <style>
    body {
      background-color: #f4f6f8;
    }

    .sidebar {
      border-right: 1px solid #dee2e6;
      min-height: 100vh;
      background-color: #fff;
    }

    .list-group-item {
      border: none;
      border-left: 4px solid transparent;
      transition: all 0.2s;
    }

    .list-group-item:hover {
      background-color: #f1f1f1;
      border-left: 4px solid #1DB954;
    }

    .list-group-item.active {
      background-color: #e9f7ef;
      border-left: 4px solid #1DB954;
    }

    .main-content {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">

      <!-- ✅ Sidebar -->
      <div class="col-md-3 sidebar p-4">
        <h4 class="mb-4">📬 Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></h4>
        <div class="list-group">
          <a href="inbox.php" class="list-group-item list-group-item-action">📥 Inbox</a>
          <a href="favorites.php" class="list-group-item list-group-item-action">⭐ Favorites</a>
          <a href="sent.php" class="list-group-item list-group-item-action">📤 Sent</a>
          <a href="labels.php" class="list-group-item list-group-item-action">🏷️ Labels</a>
          <a href="trash.php" class="list-group-item list-group-item-action text-danger">🗑️ Trash</a>
          <a href="compose.php" class="list-group-item list-group-item-action">✉️ Compose</a>
          <a href="logout.php" class="list-group-item list-group-item-action text-secondary">🚪 Logout</a>
        </div>
      </div>

      <!-- ✅ Main Content -->
      <div class="col-md-9 p-4">
        <div class="main-content">
          <h5 class="mb-3">📌 Dashboard</h5>
          <p class="text-muted">Select an option from the menu to get started.</p>
          <!-- You can show user info, recent activity, etc. here -->
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>
