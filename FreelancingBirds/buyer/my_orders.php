<?php
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../login.php");
    exit;
}

$buyer_id = $_SESSION['user_id'];

// ✅ Handle Order Completion & Credit Seller Wallet
if (isset($_POST['mark_completed'])) {
    $order_id = (int)$_POST['order_id'];

    // Fetch order details (ensure the order belongs to this buyer)
    $order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND buyer_id=$buyer_id")->fetch_assoc();

    if ($order && $order['status'] != 'Completed') {
        $seller_id = $order['seller_id'];
        $gig_price = $conn->query("SELECT price FROM gigs WHERE id=" . $order['gig_id'])->fetch_assoc()['price'];

        // ✅ Update order status
        $conn->query("UPDATE orders SET status='Completed' WHERE id=$order_id");

        // ✅ Credit seller wallet
        $conn->query("UPDATE wallet SET balance=balance+$gig_price WHERE user_id=$seller_id");

        echo "<p style='color:green;'>✅ Order marked as Completed! Seller credited $gig_price credits.</p>";

        // Refresh page to update order table
        header("Refresh:1");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Invalid order or already completed.</p>";
    }
}

// ✅ Fetch buyer orders
$sql = "SELECT orders.*, gigs.title, gigs.price, users.name as seller_name 
        FROM orders 
        JOIN gigs ON orders.gig_id=gigs.id 
        JOIN users ON orders.seller_id=users.id 
        WHERE orders.buyer_id=$buyer_id 
        ORDER BY orders.id DESC";
$orders = $conn->query($sql);
?>

<div class="auth-container">
    <h2>My Orders</h2>

    <table border="1" width="100%" style="border-collapse:collapse;">
        <tr style="background:#4169E1; color:white;">
            <th>Gig</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Status / Action</th>
            <th>Date</th>
        </tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($o['title']); ?></td>
                <td><?php echo htmlspecialchars($o['seller_name']); ?></td>
                <td><?php echo $o['price']; ?> Credits</td>
                <td>
                    <?php if ($o['status'] != 'Completed'): ?>
                        <?php echo $o['status']; ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <button type="submit" name="mark_completed" style="background:#4169E1; color:white; padding:5px 10px; border:none; border-radius:4px; cursor:pointer;">Mark Completed</button>
                        </form>
                    <?php else: ?>
                        ✅ Completed
                    <?php endif; ?>
                </td>
                <td><?php echo $o['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>