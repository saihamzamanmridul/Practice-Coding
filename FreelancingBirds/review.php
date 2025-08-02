<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    echo "<p>Invalid request!</p>";
    include("includes/footer.php");
    exit;
}

$order_id = $_GET['order_id'];

// Check if the order exists and belongs to this buyer
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND buyer_id=" . $_SESSION['user_id'] . " AND status='Completed'")->fetch_assoc();
if (!$order) {
    die("<p>Order not found or not completed.</p>");
}

// Check if review already exists
$check = $conn->query("SELECT id FROM reviews WHERE order_id=$order_id")->num_rows;
if ($check > 0) {
    die("<p>You have already reviewed this order.</p>");
}

if (isset($_POST['submit_review'])) {
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $reviewer_id = $_SESSION['user_id'];
    $seller_id = $order['seller_id'];

    $sql = "INSERT INTO reviews (order_id, reviewer_id, seller_id, rating, review) 
            VALUES ('$order_id', '$reviewer_id', '$seller_id', '$rating', '$review')";

    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Thank you! Your review has been submitted.</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<div class="auth-container">
    <h2>Leave a Review</h2>
    <form method="post">
        <label>Rating (1 to 5):</label>
        <select name="rating" required>
            <option value="">Select Rating</option>
            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
        </select>

        <textarea name="review" placeholder="Write your review..." required></textarea>
        <button type="submit" name="submit_review">Submit Review</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>