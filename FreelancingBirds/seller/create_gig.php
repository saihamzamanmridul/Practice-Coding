<?php
include("../includes/db.php");
include("../includes/header.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['create_gig'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $delivery = $_POST['delivery_time'];
    $tags = $_POST['tags'];
    $seller_id = $_SESSION['user_id'];

    // Image upload
    $file_name = "default_gig.png";
    if (!empty($_FILES['gig_img']['name'])) {
        $file_name = time() . basename($_FILES["gig_img"]["name"]);
        $target = "../uploads/" . $file_name;
        move_uploaded_file($_FILES["gig_img"]["tmp_name"], $target);
    }

    $sql = "INSERT INTO gigs (seller_id, title, description, category, price, delivery_time, tags, gig_img) 
            VALUES ('$seller_id', '$title', '$description', '$category', '$price', '$delivery', '$tags', '$file_name')";

    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Gig created successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<div class="auth-container">
    <h2>Create a New Gig</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Gig Title" required>
        <textarea name="description" placeholder="Gig Description" required></textarea>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="price" placeholder="Price (USD)" required>
        <input type="number" name="delivery_time" placeholder="Delivery Time (days)" required>
        <input type="text" name="tags" placeholder="Tags (comma separated)">
        <input type="file" name="gig_img">
        <button type="submit" name="create_gig">Publish Gig</button>
    </form>
</div>

<?php include("../includes/footer.php"); ?>