<?php
session_start();
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../login.php");
    exit;
}

$buyer_id = $_SESSION['user_id'];

// Fetch favorites
$sql = "SELECT gigs.* FROM favorites 
        JOIN gigs ON favorites.gig_id = gigs.id 
        WHERE favorites.user_id = $buyer_id";
$result = $conn->query($sql);
?>

<div class="auth-container">
    <h2>My Favorite Gigs</h2>
    <div class="gig-grid">
        <?php while ($gig = $result->fetch_assoc()): ?>
            <div class="gig-card">
                <img src="../uploads/<?php echo $gig['image']; ?>" width="200">
                <h3><?php echo $gig['title']; ?></h3>
                <p><?php echo $gig['price']; ?> Credits</p>
                <a href="gig_details.php?id=<?php echo $gig['id']; ?>">View Gig</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>