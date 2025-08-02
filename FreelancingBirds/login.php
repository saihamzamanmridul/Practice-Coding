<?php
session_start();
include("includes/db.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND status='active'");
    if ($res->num_rows > 0) {
        $u = $res->fetch_assoc();
        if (password_verify($pass, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['name'] = $u['name'];
            $_SESSION['role'] = $u['role'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "Invalid credentials or inactive account!";
}
include("includes/header.php");
?>
<div class="auth-container">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
<?php include("includes/footer.php"); ?>