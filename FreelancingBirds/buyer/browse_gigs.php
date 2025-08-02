<?php
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../login.php");
    exit;
}

// Search & Filter
$where = "WHERE 1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $keyword = $_GET['search'];
    $where .= " AND (title LIKE '%$keyword%' OR tags LIKE '%$keyword%')";
}
if (isset($_GET['category']) && $_GET['category'] != '') {
    $cat = $_GET['category'];
    $where .= " AND category='$cat'";
}
if (isset($_GET['min_price']) && isset($_GET['max_price'])) {
    $min = $_GET['min_price'];
    $max = $_GET['max_price'];
    if ($min !== '' && $max !== '') {
        $where .= " AND price BETWEEN $min AND $max";
    }
}
if (isset($_GET['delivery_time']) && $_GET['delivery_time'] != '') {
    $delivery = $_GET['delivery_time'];
    $where .= " AND delivery_time <= $delivery";
}

// Fetch gigs
$sql = "SELECT gigs.*, users.name as seller_name FROM gigs JOIN users ON gigs.seller_id=users.id $where ORDER BY gigs.id DESC";
$gigs = $conn->query($sql);
?>

<div class="auth-container">
    <h2>Browse Gigs</h2>

    <!-- Search & Filter Form -->
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search gigs..." value="<?php echo $_GET['search'] ?? ''; ?>">
        <input type="text" name="category" placeholder="Category" value="<?php echo $_GET['category'] ?? ''; ?>">
        <input type="number" name="min_price" placeholder="Min Price" value="<?php echo $_GET['min_price'] ?? ''; ?>">
        <input type="number" name="max_price" placeholder="Max Price" value="<?php echo $_GET['max_price'] ?? ''; ?>">
        <select name="delivery_time">
            <option value="">Delivery Time</option>
            <option value="1">1 Day</option>
            <option value="3">3 Days</option>
            <option value="7">7 Days</option>
            <option value="14">14 Days</option>
        </select>
        <button type="submit">Filter</button>
    </form>

    <!-- Display Gigs -->
    <div class="gig-list">
        <?php if ($gigs->num_rows > 0): ?>
            <?php while ($gig = $gigs->fetch_assoc()): ?>
                <div class="gig-card">
                    <a href="../gig_details.php?id=<?php echo $gig['id']; ?>">
                        <img src="../uploads/<?php echo $gig['gig_img']; ?>" width="100%">
                        <h3><?php echo $gig['title']; ?></h3>
                    </a>
                    <p><?php echo substr($gig['description'], 0, 60); ?>...</p>
                    <p><b>By:</b> <?php echo $gig['seller_name']; ?></p>
                    <p><b>Price:</b> $<?php echo $gig['price']; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No gigs found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>