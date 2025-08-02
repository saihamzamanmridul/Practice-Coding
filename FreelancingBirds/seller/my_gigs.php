<?php
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Delete Gig
if (isset($_GET['delete'])) {
    $gig_id = $_GET['delete'];
    $conn->query("DELETE FROM gigs WHERE id=$gig_id AND seller_id=$seller_id");
    echo "<p style='color:red;'>Gig deleted successfully!</p>";
}

// Fetch gigs
$gigs = $conn->query("SELECT * FROM gigs WHERE seller_id=$seller_id");
?>

<div class="auth-container">
    <h2>My Gigs</h2>
    <a href="create_gig.php"><button>Create New Gig</button></a>
    <div class="gig-list">
        <?php while ($gig = $gigs->fetch_assoc()): ?>
            <div class="gig-card">
                <img src="../uploads/<?php echo $gig['gig_img']; ?>" width="100%">
                <h3><?php echo $gig['title']; ?></h3>
                <p><?php echo substr($gig['description'], 0, 60); ?>...</p>
                <p><b>Price:</b> $<?php echo $gig['price']; ?> | <b>Delivery:</b> <?php echo $gig['delivery_time']; ?> days</p>
                <a href="?delete=<?php echo $gig['id']; ?>" onclick="return confirm('Delete this gig?');"><button>Delete</button></a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>