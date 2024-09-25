<?php
session_start(); 

require '../config/config.php'; 

// Define minimum length
define('MAX_LENGTH', 3);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    extract($_POST);

    if (!isset($name) || strlen($name) < MAX_LENGTH) { 
        $errors['name'] = "Name must be at least 3 characters.";
    }

    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    if (!isset($password) || strlen($password) < 8) {
        $errors["password"] = "The password must be at least 8 characters.";
    } elseif (isset($password_confirmation) && $password !== $password_confirmation) {
        $errors["password_confirmation"] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        echo '<script>window.location.href = "../auth/register.php";</script>';
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO employees (name, email, password) VALUES (:name, :email, :password)"; // تعديل هنا
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name, 
            ':email' => $email,
            ':password' => $hashed_password,
        ]);

        header("Location: ../auth/login.php");
        exit();
    } catch (PDOException $e) {
        $errors['database'] = "Registration failed: " . $e->getMessage();
        $_SESSION['errors'] = $errors;
        $_SESSION['post_data'] = $_POST;
        echo '<script>window.location.href = "../auth/register.php";</script>';
        exit();
    }
}

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$post_data = isset($_SESSION['post_data']) ? $_SESSION['post_data'] : [];
unset($_SESSION['errors'], $_SESSION['post_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../public/css/all.css">
</head>
<body>
    <form action="../auth/register.php" method="POST">
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
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <?php if (isset($errors['password'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['password']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
            <?php if (isset($errors['password_confirmation'])) : ?>
                <small class="error">* <?php echo htmlspecialchars($errors['password_confirmation']); ?></small>
            <?php endif; ?>
        </div>
        <div>
            <input type="submit" value="Register">
        </div>
    </form>
</body>
</html>
