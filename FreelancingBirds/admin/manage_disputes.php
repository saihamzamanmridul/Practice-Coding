<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include("../includes/header.php");

// Fetch disputes
$sql = "SELECT disputes.*, users.name AS buyer_name, u2.name AS seller_name 
        FROM disputes 
        JOIN users ON disputes.buyer_id = users.id 
        JOIN users u2 ON disputes.seller_id = u2.id";
$res = $conn->query($sql);
?>

<div class="admin-dashboard">
    <h2>Manage Disputes</h2>
    <table border="1" width="100%">
        <tr>
            <th>ID</th>
            <th>Order</th>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Reason</th>
            <th>Status</th>
        </tr>
        <?php while ($d = $res->fetch_assoc()): ?>
            <tr>
                <td><?php echo $d['id']; ?></td>
                <td><?php echo $d['order_id']; ?></td>
                <td><?php echo $d['buyer_name']; ?></td>
                <td><?php echo $d['seller_name']; ?></td>
                <td><?php echo $d['reason']; ?></td>
                <td><?php echo $d['status']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include("../includes/footer.php"); ?>