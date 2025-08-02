<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user data
$sql = "SELECT * FROM users WHERE id=$user_id";
$user = $conn->query($sql)->fetch_assoc();

// ✅ Fetch wallet balance
$wallet_balance = $conn->query("SELECT balance FROM wallet WHERE user_id=$user_id")->fetch_assoc()['balance'];

// ✅ Handle profile update
if (isset($_POST['update_profile'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $bio = $conn->real_escape_string($_POST['bio']);
    $skills = $conn->real_escape_string($_POST['skills']);
    $languages = $conn->real_escape_string($_POST['languages']);
    $portfolio = $conn->real_escape_string($_POST['portfolio']);

    // ✅ Image upload
    if (!empty($_FILES['profile_img']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . basename($_FILES["profile_img"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file);
        $img_sql = ", profile_img='$file_name'";
    } else {
        $img_sql = "";
    }

    $update = "UPDATE users SET name='$name', bio='$bio', skills='$skills', languages='$languages', portfolio='$portfolio' $img_sql WHERE id=$user_id";

    if ($conn->query($update)) {
        echo "<p style='color:green;'>✅ Profile updated successfully!</p>";
        header("Refresh:1");
        exit;
    } else {
        echo "<p style='color:red;'>❌ Error updating profile: " . $conn->error . "</p>";
    }
}
?>

<div class="auth-container">
    <h2>My Profile</h2>

    <!-- ✅ Profile Image -->
    <img src="uploads/<?php echo htmlspecialchars($user['profile_img']); ?>" width="100" height="100" style="border-radius:50%;" alt="Profile Image">

    <!-- ✅ Wallet Balance Display -->
    <div class="wallet-box" style="background:#fff; border:1px solid #ccc; padding:10px; margin:10px 0; border-radius:5px; font-size:16px;">
        Your Wallet Balance: <b style="color:#4169E1;"><?php echo $wallet_balance; ?> Credits</b>
    </div>

    <!-- ✅ Profile Update Form -->
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required placeholder="Name">
        <textarea name="bio" placeholder="Bio"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        <input type="text" name="skills" value="<?php echo htmlspecialchars($user['skills']); ?>" placeholder="Skills (comma separated)">
        <input type="text" name="languages" value="<?php echo htmlspecialchars($user['languages']); ?>" placeholder="Languages">
        <textarea name="portfolio" placeholder="Portfolio Links"><?php echo htmlspecialchars($user['portfolio']); ?></textarea>
        <input type="file" name="profile_img" accept="image/*">
        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

<?php
// ✅ Display Seller Reviews (Only if role is seller)
if ($user['role'] === 'seller') {
    $seller_id = $user['id'];

    $reviews = $conn->query("SELECT reviews.*, users.name as reviewer 
                             FROM reviews 
                             JOIN users ON reviews.reviewer_id=users.id 
                             WHERE seller_id=$seller_id ORDER BY created_at DESC");

    $avg_q = $conn->query("SELECT AVG(rating) as avg_rating FROM reviews WHERE seller_id=$seller_id")->fetch_assoc();
    $avg_rating = round($avg_q['avg_rating'], 1);
?>

    <h3>Seller Rating: ⭐ <?php echo $avg_rating ?: 'No ratings yet'; ?></h3>

    <div class="reviews-section">
        <?php while ($r = $reviews->fetch_assoc()): ?>
            <div class="review-card" style="border:1px solid #ddd; padding:8px; margin:5px 0; border-radius:4px;">
                <p><b><?php echo htmlspecialchars($r['reviewer']); ?></b> - ⭐ <?php echo $r['rating']; ?></p>
                <p><?php echo nl2br(htmlspecialchars($r['review'])); ?></p>
                <span style="font-size:12px; color:gray;"><?php echo $r['created_at']; ?></span>
            </div>
        <?php endwhile; ?>
    </div>

<?php } ?>

<?php include("includes/footer.php"); ?>