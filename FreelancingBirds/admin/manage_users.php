<?php
include("../includes/db.php");
include("../includes/header.php");

// ✅ Admin Access Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// ✅ Update user status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE users SET status = IF(status='active','inactive','active') WHERE id=$id");
}

// ✅ Add credits to wallet
if (isset($_POST['add_credit'])) {
    $uid = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    $conn->query("UPDATE wallet SET balance=balance+$amount WHERE user_id=$uid");
    echo "<p style='color:green;'>✅ $amount credits added successfully!</p>";
}

// ✅ Fetch users with wallet balances
$users = $conn->query("SELECT users.*, wallet.balance FROM users JOIN wallet ON users.id=wallet.user_id ORDER BY users.id DESC");
?>
<div class="auth-container">
    <h2>Manage Users</h2>
    <table border="1" width="100%">
        <tr style="background:#4169E1; color:white;">
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Wallet</th>
            <th>Action</th>
        </tr>
        <?php while ($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo $u['role']; ?></td>
                <td><?php echo $u['status']; ?></td>
                <td><?php echo $u['balance']; ?> Credits</td>
                <td>
                    <a href="?toggle=<?php echo $u['id']; ?>" style="color:#4169E1;">Toggle Status</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <input type="number" name="amount" placeholder="Amount" step="0.1" required>
                        <button type="submit" name="add_credit">Add Credits</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include("../includes/footer.php"); ?>