<?php
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Invalid request";
    $_SESSION['msg_type'] = "danger";
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch user to get photo path
$stmt = $conn->prepare("SELECT photo FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    try {
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            // Delete photo if exists
            if (!empty($user['photo']) && file_exists("../uploads/{$user['photo']}")) {
                unlink("../uploads/{$user['photo']}");
            }

            $_SESSION['message'] = "User deleted successfully!";
            $_SESSION['msg_type'] = "success";
        }
    } catch (PDOException $e) {
        error_log("Delete user error: " . $e->getMessage());
        $_SESSION['message'] = "An error occurred while deleting user";
        $_SESSION['msg_type'] = "danger";
    }
} else {
    $_SESSION['message'] = "User not found";
    $_SESSION['msg_type'] = "danger";
}

header("Location: index.php");
exit();
