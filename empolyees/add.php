<?php
session_start();
require '../config/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $manager_id = $_POST['manager_id'];
    $picture_path = "./empolyees/images//"; // Initialize picture_path variable

    // Handle file upload for picture
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['picture']['type'], $allowed_types)) {
            die("Invalid file type. Please upload a JPEG, PNG, or GIF.");
        } else {
            // Ensure the directory exists
            $uploadDir = "./empolyees/images//";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $picture_path = $uploadDir . basename($_FILES['picture']['name']);
            if (!move_uploaded_file($_FILES['picture']['tmp_name'], $picture_path)) {
                die("Failed to upload picture.");
            }
        }
    } else {
        die("Picture is required.");
    }

    // Insert into the employees table
    $sql = "INSERT INTO employees (name, email, phone, picture, manager_id) VALUES (:name, :email, :phone, :picture, :manager_id)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':picture' => $picture_path,
        ':manager_id' => $manager_id,
    ]);

    echo "<script>
    alert('Adding successful!');
    window.location.href = '../index.php'; 
    </script>";
}

// Clear the session variable after use
$picture_path = isset($_SESSION['picture_path']) ? $_SESSION['picture_path'] : null;
unset($_SESSION['picture_path']); 
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
    <form action="add.php" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" required><br>

        <label for="picture">Profile Picture:</label>
        <input type="file" name="picture" accept="image/*" required><br>

        <label for="manager_id">Manager ID:</label>
        <input type="text" name="manager_id" required><br><br>

        <input type="submit" value="Register Employee">
    </form>

    <?php if ($picture_path): ?>
        <h3>Uploaded Picture:</h3>
        <img src="<?php echo htmlspecialchars($picture_path); ?>" alt="Profile Picture" style="max-width: 200px; max-height: 200px;">
    <?php endif; ?>
</body>
</html>
