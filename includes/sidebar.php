<<<<<<< HEAD
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
=======
<div class="sidebar">
    <button class="compose-btn" onclick="window.location.href='compose.php'">✉️ Compose</button>

    <ul class="nav-links">
        <li><a href="inbox.php">📥 Inbox</a></li>
        <li><a href="sent.php">📤 Sent</a></li>
        <li><a href="favorite.php">⭐ Starred</a></li>
        <li><a href="trash.php">🗑️ Trash</a></li>
        <li><a href="labels.php">🏷️ Manage Labels</a></li>
        <li><a href="../logout.php">🚪 Logout</a></li>
    </ul>

    <div class="labels-section">
        <strong>📌 Your Labels</strong>
        <ul>
            <?php
            require_once '../config/database.php';
            $user_id = $_SESSION['user_id'];
            $labelStmt = $pdo->prepare("SELECT id, name FROM labels WHERE user_id = ?");
            $labelStmt->execute([$user_id]);
            $labels = $labelStmt->fetchAll();

            if ($labels):
                foreach ($labels as $label): ?>
                    <li>
                        <a href="inbox.php?label=<?php echo $label['id']; ?>">
                            🏷️ <?php echo htmlspecialchars($label['name']); ?>
                        </a>
                    </li>
                <?php endforeach;
            else: ?>
                <li><em>No labels</em></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
>>>>>>> 139356b (add)
