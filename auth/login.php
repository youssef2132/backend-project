<?php
require '../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        $sql = "SELECT * FROM managers WHERE name = :name";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':name' => $name]);
        $manager = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$manager) {
            $errors['login'] = 'Invalid name or password.';
        } elseif (!password_verify($password, $manager['password'])) {
            $errors['login'] = 'Invalid name or password.';
        } else {
            $_SESSION['managers_id'] = $manager['id'];
            header("Location: ../index.php");
            exit();
        }
    }

    // Store errors in the session
    $_SESSION['errors'] = $errors;
    $_SESSION['post_data'] = $_POST;
    header("Location: ./login.php");
    exit();
}

// Retrieve errors and post data from the session
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$post_data = isset($_SESSION['post_data']) ? $_SESSION['post_data'] : [];
unset($_SESSION['errors'], $_SESSION['post_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../public/css/all.css">
</head>
<body>

    <form action="login.php" method="POST" class="form-container">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="Name"
                value="<?php echo isset($post_data['name']) ? htmlspecialchars($post_data['name']) : ''; ?>">
            <i class="fas fa-manager input-icon"></i>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password">
            <i class="fas fa-lock input-icon"></i>
        </div>

        <div class="form-group">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>

    <?php if (!empty($errors)): ?>
        <script>
            let errorMessage = '';
            <?php foreach ($errors as $error): ?>
                errorMessage += '<?php echo addslashes($error); ?>\n';
            <?php endforeach; ?>
            alert(errorMessage);
        </script>
    <?php endif; ?>

</body>
</html>
