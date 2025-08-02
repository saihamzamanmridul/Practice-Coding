<?php
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Handle status updates
if (isset($_GET['order_id']) && isset($_GET['action'])) {
    $order_id = $_GET['order_id'];
    $action = $_GET['action'];
    $conn->query("UPDATE orders SET status='$action' WHERE id=$order_id AND seller_id=$seller_id");
    echo "<p style='color:green;'>Order status updated to $action.</p>";
}

// Fetch orders for this seller
$sql = "SELECT orders.*, gigs.title, users.name as buyer_name 
        FROM orders 
        JOIN gigs ON orders.gig_id=gigs.id 
        JOIN users ON orders.buyer_id=users.id 
        WHERE orders.seller_id=$seller_id ORDER BY orders.id DESC";
$orders = $conn->query($sql);
?>

<div class="auth-container">
    <h2>Orders from Buyers</h2>
    <table border="1" width="100%">
        <tr>
            <th>Gig</th>
            <th>Buyer</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while ($o = $orders->fetch_assoc()): ?>
            <tr>
                <td><?php echo $o['title']; ?></td>
                <td><?php echo $o['buyer_name']; ?></td>
                <td><?php echo $o['status']; ?></td>
                <td>
                    <?php if ($o['status'] == 'Pending'): ?>
                        <a href="?order_id=<?php echo $o['id']; ?>&action=Accepted"><button>Accept</button></a>
                        <a href="?order_id=<?php echo $o['id']; ?>&action=Cancelled"><button>Decline</button></a>
                    <?php elseif ($o['status'] == 'Accepted'): ?>
                        <a href="?order_id=<?php echo $o['id']; ?>&action=In Progress"><button>Start Work</button></a>
                    <?php elseif ($o['status'] == 'In Progress'): ?>
                        <a href="?order_id=<?php echo $o['id']; ?>&action=Delivered"><button>Deliver</button></a>
                    <?php elseif ($o['status'] == 'Delivered'): ?>
                        <span>Waiting for Buyer Confirmation</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>