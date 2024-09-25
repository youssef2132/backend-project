<?
session_start(); 

require '../config/config.php'; 

// Define minimum lengths
define('MAX_LENGTH', 3);
define('PHONE_LENGTH', 10); // Example length for phone number

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    extract($_POST);

    // Validate inputs
    if (!isset($name) || strlen($name) < MAX_LENGTH) {
        $errors['name'] = "Name must be at least 3 characters.";
    }

    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (!isset($phone) || strlen($phone) < PHONE_LENGTH) {
        $errors['phone'] = "Phone number must be at least 10 characters.";
    }

    // Handle file upload for picture
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['picture']['type'], $allowed_types)) {
            $errors['picture'] = "Please upload a valid image (JPEG, PNG, GIF).";
        } else {
            $picture_path = "./empolyees/images//" . basename($_FILES['picture']['name']);
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $picture_path)) {
                $_SESSION['picture_path'] = $picture_path; // Store the picture path
            } else {
                $errors['picture'] = "Failed to upload picture.";
            }
        }
    } else {
        $errors['picture'] = "Picture is required.";
    }

    // Check for manager ID
    if (!isset($manager_id)) {
        $errors['manager_id'] = "Manager ID is required.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        echo '<script>window.location.href = "register.php";</script>';
        exit();
    }

    try {
        $sql = "INSERT INTO employees (name, email, phone, picture, manager_id) VALUES (:name, :email, :phone, :picture, :manager_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':picture' => $picture_path,
            ':manager_id' => $manager_id,
        ]);

        header("Location: ../index.php");
        exit();
    } catch (PDOException $e) {
        $errors['database'] = "Registration failed: " . $e->getMessage();
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        echo '<script>window.location.href = "register.php";</script>';
        exit();
    }
}

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$post_data = isset($_SESSION['post_data']) ? $_SESSION['post_data'] : [];
$picture_path = isset($_SESSION['picture_path']) ? $_SESSION['picture_path'] : null;
unset($_SESSION['errors'], $_SESSION['post_data'], $_SESSION['picture_path']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Employee</title>
    <link rel="stylesheet" href="../public/css/all.css">
</head>
<body>
    <form action="add_action.php" method="POST" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo isset($post_data['name']) ? htmlspecialchars($post_data['name']) : ''; ?>" required>
            <?php if (isset($errors['name'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['name']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo isset($post_data['email']) ? htmlspecialchars($post_data['email']) : ''; ?>" required>
            <?php if (isset($errors['email'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['email']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value="<?php echo isset($post_data['phone']) ? htmlspecialchars($post_data['phone']) : ''; ?>" required>
            <?php if (isset($errors['phone'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['phone']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <label for="picture">Profile Picture:</label>
            <input type="file" name="picture" id="picture" required>
            <?php if (isset($errors['picture'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['picture']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <label for="manager_id">Manager ID:</label>
            <input type="text" name="manager_id" id="manager_id" value="<?php echo isset($post_data['manager_id']) ? htmlspecialchars($post_data['manager_id']) : ''; ?>" required>
            <?php if (isset($errors['manager_id'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['manager_id']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <input type="submit" value="Register Employee">
        </div>
    </form>

    <?php if ($picture_path): ?>
        <h3>Uploaded Picture:</h3>
        <img src="<?php echo htmlspecialchars($picture_path); ?>" alt="Profile Picture" style="max-width: 200px; max-height: 200px;">
    <?php endif; ?>
</body>
</html> 