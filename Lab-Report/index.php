<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

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
    $photo = null;
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

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $errors[] = "Failed to upload file";
                $photo = null;
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, photo) VALUES (:name, :email, :photo)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':photo', $photo);

            if ($stmt->execute()) {
                $_SESSION['message'] = "User created successfully!";
                $_SESSION['msg_type'] = "success";
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate email
                $errors[] = "Email already exists";
            } else {
                error_log("Create user error: " . $e->getMessage());
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
                <h4>Add New User</h4>
            </div>
            <div class="card-body">
                <form action="create.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>