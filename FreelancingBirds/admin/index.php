<?php
include("../includes/db.php");
include("../includes/header.php");

// ✅ Admin Access Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<div class="auth-container">
    <h2>Admin Dashboard</h2>
    <ul>
        <li><a href="manage_users.php">👤 Manage Users</a></li>
        <li><a href="manage_gigs.php">🎨 Manage Gigs</a></li>
        <li><a href="manage_orders.php">📦 Manage Orders</a></li>
        <li><a href="manage_wallet.php">💰 Manage Wallet</a></li>
    </ul>
</div>
<?php include("../includes/footer.php"); ?>