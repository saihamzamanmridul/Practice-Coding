<?php
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Invalid request";
    $_SESSION['msg_type'] = "danger";
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = "User not found";
    $_SESSION['msg_type'] = "danger";
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_photo = $user['photo'];

    // Validate inputs
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Handle file upload
    $photo = $current_photo;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
        } elseif ($_FILES['photo']['size'] > 500000) { // 500KB
            $errors[] = "File size must be less than 500KB";
        } else {
            $photo = uniqid() . '.' . $ext;
            $destination = '../uploads/' . $photo;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                // Delete old photo if exists
                if (!empty($current_photo) && file_exists("../uploads/$current_photo")) {
                    unlink("../uploads/$current_photo");
                }
            } else {
                $errors[] = "Failed to upload file";
                $photo = $current_photo;
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET name = :name, email = :email, photo = :photo WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':photo', $photo);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User updated successfully!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate email
                $errors[] = "Email already exists";
            } else {
                error_log("Update user error: " . $e->getMessage());
                $errors[] = "An error occurred. Please try again.";
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['msg_type'] = "danger";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <?php if (!empty($user['photo'])): ?>
                            <div class="mb-2">
                                <img src="../uploads/<?= htmlspecialchars($user['photo']) ?>" width="100" class="img-thumbnail">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>