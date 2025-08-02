<?php
session_start();
include("includes/db.php");

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users(name,email,password,role) VALUES('$name','$email','$pass','$role')");
    $uid = $conn->insert_id;
    $conn->query("INSERT INTO wallet(user_id,balance) VALUES($uid,0)");
    header("Location: login.php");
    exit;
}
include("includes/header.php");
?>
<div class="auth-container">
    <h2>Register</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select>
        <button type="submit" name="register">Register</button>
    </form>
</div>
<?php include("includes/footer.php"); ?>