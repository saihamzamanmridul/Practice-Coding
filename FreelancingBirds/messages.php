<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    echo "<p>Invalid request!</p>";
    include("includes/footer.php");
    exit;
}

$order_id = $_GET['order_id'];

// Get order participants
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();
if (!$order) {
    die("Order not found.");
}

$other_user = ($order['buyer_id'] == $user_id) ? $order['seller_id'] : $order['buyer_id'];

// Send Message
if (isset($_POST['send_message'])) {
    $msg = $_POST['message'];
    $conn->query("INSERT INTO messages (order_id, sender_id, receiver_id, message) VALUES ('$order_id','$user_id','$other_user','$msg')");
}

// Fetch Messages
$sql = "SELECT messages.*, users.name FROM messages JOIN users ON messages.sender_id=users.id WHERE order_id=$order_id ORDER BY timestamp ASC";
$msgs = $conn->query($sql);
?>

<div class="chat-container">
    <h2>Chat with <?php echo $conn->query("SELECT name FROM users WHERE id=$other_user")->fetch_assoc()['name']; ?></h2>

    <div class="chat-box">
        <?php while ($m = $msgs->fetch_assoc()): ?>
            <div class="msg <?php echo ($m['sender_id'] == $user_id) ? 'me' : 'them'; ?>">
                <p><b><?php echo $m['name']; ?>:</b> <?php echo $m['message']; ?></p>
                <span><?php echo $m['timestamp']; ?></span>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="post" class="chat-form">
        <textarea name="message" required placeholder="Type your message..."></textarea>
        <button type="submit" name="send_message">Send</button>
    </form>
</div>

<?php include("includes/footer.php"); ?>