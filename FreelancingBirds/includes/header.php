<?php
// ✅ Safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/db.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Freelancing Birds</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <header class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Freelancing Birds" height="40" />
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>

                <?php if (isset($_SESSION['user_id'])):
                    $user_id = $_SESSION['user_id'];
                    $role = $_SESSION['role'];

                    // ✅ Check if notifications table exists
                    $checkTable = $conn->query("SHOW TABLES LIKE 'notifications'");
                    $notifCount = 0;
                    $notifResult = false;

                    if ($checkTable->num_rows > 0) {
                        $notifCountResult = $conn->query("SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id=$user_id AND is_read=0");
                        $notifCount = ($notifCountResult) ? $notifCountResult->fetch_assoc()['unread_count'] : 0;

                        $notifResult = $conn->query("SELECT * FROM notifications WHERE user_id=$user_id ORDER BY created_at DESC LIMIT 5");
                    }
                ?>
                    <li><a href="profile.php">Profile</a></li>

                    <?php if ($role === 'buyer'): ?>
                        <li><a href="buyer/favorites.php"><i class="fas fa-heart"></i> Favorites</a></li>
                    <?php endif; ?>

                    <?php if ($checkTable->num_rows > 0): ?>
                        <!-- Notifications dropdown -->
                        <li class="nav-notifications">
                            <a href="#" id="notifToggle" title="Notifications">
                                <i class="fas fa-bell"></i>
                                <?php if ($notifCount > 0): ?>
                                    <span class="notif-badge"><?php echo $notifCount; ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="notif-dropdown" id="notifDropdown" style="display:none;">
                                <?php if ($notifCount == 0): ?>
                                    <p>No new notifications.</p>
                                <?php else: ?>
                                    <?php while ($notif = $notifResult->fetch_assoc()): ?>
                                        <div class="notif-item">
                                            <?php echo htmlspecialchars($notif['message']); ?><br />
                                            <small><?php echo date("M d, Y H:i", strtotime($notif['created_at'])); ?></small>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endif; ?>

                    <li><a href="logout.php">Logout</a></li>

                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>