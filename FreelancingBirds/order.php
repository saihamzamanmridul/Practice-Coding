<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['gig_id'])) {
    echo "<p>Invalid request!</p>";
    include("includes/footer.php");
    exit;
}

$gig_id = $_GET['gig_id'];

// Fetch gig
$gig = $conn->query("SELECT * FROM gigs WHERE id=$gig_id")->fetch_assoc();
$seller_id = $gig['seller_id'];
$buyer_id = $_SESSION['user_id'];

if (isset($_POST['place_order'])) {
    $sql = "INSERT INTO orders (gig_id, buyer_id, seller_id) VALUES ('$gig_id', '$buyer_id', '$seller_id')";
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Order placed successfully! <a href='buyer/my_orders.php'>View Orders</a></p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<div class="auth-container">
    <h2>Order Confirmation</h2>
    <p>You are ordering: <b><?php echo $gig['title']; ?></b></p>
    <p>Price: <b>$<?php echo $gig['price']; ?></b></p>
    <form method="post">
        <button type="submit" name="place_order">Confirm Order</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>


<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['gig_id'])) {
    echo "<p>Invalid request!</p>";
    include("includes/footer.php");
    exit;
}

$gig_id = $_GET['gig_id'];
$gig = $conn->query("SELECT * FROM gigs WHERE id=$gig_id")->fetch_assoc();
if (!$gig) {
    die("Gig not found.");
}

$buyer_id = $_SESSION['user_id'];
$seller_id = $gig['seller_id'];
$price = $gig['price'];

// ✅ Check wallet balance
$wallet = $conn->query("SELECT balance FROM wallet WHERE user_id=$buyer_id")->fetch_assoc()['balance'];

if (isset($_POST['place_order'])) {
    if ($wallet >= $price) {
        // Deduct balance from buyer
        $conn->query("UPDATE wallet SET balance=balance-$price WHERE user_id=$buyer_id");

        // Insert order
        $conn->query("INSERT INTO orders (gig_id,buyer_id,seller_id,status,payment_status) 
                      VALUES ($gig_id,$buyer_id,$seller_id,'Pending','Paid')");

        echo "<p style='color:green;'>Order placed successfully! Credits deducted.</p>";
    } else {
        echo "<p style='color:red;'>Insufficient credits! Please contact admin to top-up.</p>";
    }
}
?>

<div class="auth-container">
    <h2>Order Gig: <?php echo $gig['title']; ?></h2>
    <p>Price: <b><?php echo $price; ?> Credits</b></p>
    <form method="post">
        <button type="submit" name="place_order">Confirm Order</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>