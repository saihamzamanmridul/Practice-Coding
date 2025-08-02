<?php
include("../includes/db.php");
include("../includes/header.php");

// ✅ Admin Access Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// ✅ Delete gig
if (isset($_GET['delete'])) {
    $gid = (int)$_GET['delete'];
    $conn->query("DELETE FROM gigs WHERE id=$gid");
    echo "<p style='color:green;'>✅ Gig deleted successfully!</p>";
}

// ✅ Fetch all gigs with seller info
$gigs = $conn->query("SELECT gigs.*, users.name as seller FROM gigs JOIN users ON gigs.seller_id=users.id ORDER BY gigs.id DESC");
?>
<div class="auth-container">
    <h2>Manage Gigs</h2>
    <table border="1" width="100%">
        <tr style="background:#4169E1; color:white;">
            <th>Gig</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($g = $gigs->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($g['title']); ?></td>
                <td><?php echo htmlspecialchars($g['seller']); ?></td>
                <td><?php echo $g['price']; ?> Credits</td>
                <td><a href="?delete=<?php echo $g['id']; ?>" style="color:red;">Delete</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include("../includes/footer.php"); ?>