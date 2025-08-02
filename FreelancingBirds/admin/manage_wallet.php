<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include("../includes/header.php");

// Handle balance update
if (isset($_POST['update_wallet'])) {
    $uid = $_POST['user_id'];
    $amount = $_POST['amount'];
    $conn->query("UPDATE wallet SET balance = $amount WHERE user_id = $uid");
    echo "<p style='color:green;'>Wallet updated successfully!</p>";
}

// Fetch all users & balances
$sql = "SELECT users.id, users.name, wallet.balance FROM users 
        LEFT JOIN wallet ON users.id = wallet.user_id";
$res = $conn->query($sql);
?>

<div class="admin-dashboard">
    <h2>Manage Wallet Balances</h2>
    <table border="1" width="100%">
        <tr>
            <th>User</th>
            <th>Balance</th>
            <th>Action</th>
        </tr>
        <?php while ($u = $res->fetch_assoc()): ?>
            <tr>
                <td><?php echo $u['name']; ?></td>
                <td><?php echo $u['balance'] ?? 0; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <input type="number" name="amount" value="<?php echo $u['balance'] ?? 0; ?>">
                        <button type="submit" name="update_wallet">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>