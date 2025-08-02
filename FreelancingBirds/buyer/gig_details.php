<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_GET['id'])) {
    echo "<p>Invalid gig ID.</p>";
    include("includes/footer.php");
    exit;
}

$id = $_GET['id'];
$sql = "SELECT gigs.*, users.name as seller_name, users.profile_img FROM gigs 
        JOIN users ON gigs.seller_id=users.id WHERE gigs.id=$id";
$gig = $conn->query($sql)->fetch_assoc();
?>

<div class="auth-container">
    <h2><?php echo $gig['title']; ?></h2>
    <img src="uploads/<?php echo $gig['gig_img']; ?>" width="100%">
    <p><?php echo $gig['description']; ?></p>
    <p><b>Category:</b> <?php echo $gig['category']; ?></p>
    <p><b>Price:</b> $<?php echo $gig['price']; ?> | <b>Delivery:</b> <?php echo $gig['delivery_time']; ?> Days</p>
    <p><b>Tags:</b> <?php echo $gig['tags']; ?></p>

    <hr>
    <h3>Seller Info</h3>
    <img src="uploads/<?php echo $gig['profile_img']; ?>" width="60" style="border-radius:50%;">
    <p><?php echo $gig['seller_name']; ?></p>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'buyer'): ?>
        <a href="order.php?gig_id=<?php echo $gig['id']; ?>"><button>Order Now</button></a>
    <?php endif; ?>
</div>

<?php include("includes/footer.php"); ?>