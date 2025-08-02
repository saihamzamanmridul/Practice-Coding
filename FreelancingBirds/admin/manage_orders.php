<?php
include("../includes/db.php");
include("../includes/header.php");

// ✅ Admin Access Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// ✅ Update order status
if (isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id=$oid");
    echo "<p style='color:green;'>✅ Order status updated successfully!</p>";
}

// ✅ Fetch all orders
$orders = $conn->query("SELECT orders.*, gigs.title, users.name as buyer 
                        FROM orders 
                        JOIN gigs ON orders.gig_id=gigs.id 
                        JOIN users ON orders.buyer_id=users.id 
                        ORDER BY orders.id DESC");
?>
<div class="auth-container">
    <h2>Manage Orders</h2>
    <table border="1" width="100%">
        <tr style="background:#4169E1; color:white;">
            <th>Order ID</th>
            <th>Gig</th>
            <th>Buyer</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><?php echo $o['id']; ?></td>
                <td><?php echo htmlspecialchars($o['title']); ?></td>
                <td><?php echo htmlspecialchars($o['buyer']); ?></td>
                <td><?php echo $o['status']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <select name="status">
                            <option>Pending</option>
                            <option>Accepted</option>
                            <option>In Progress</option>
                            <option>Delivered</option>
                            <option>Completed</option>
                            <option>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include("../includes/footer.php"); ?>