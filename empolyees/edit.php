<?php
session_start();
require '../config/config.php';

// Check if employee ID is set
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$employee_id = $_GET['id'];

// Fetch the employee data
$sql = "SELECT * FROM employees WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $manager_id = $_POST['manager_id'];

    $picture_path = $employee['picture']; // Default to existing picture

    if (isset($_FILES['picture']) && $_FILES['picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['picture']['type'], $allowed_types)) {
            $picture_path = './empolyees/images//' . basename($_FILES['picture']['name']);
            move_uploaded_file($_FILES['picture']['tmp_name'], $picture_path);
        } else {
            $errors['picture'] = "Please upload a valid image (JPEG, PNG, GIF).";
        }
    }

    // Update the employee data
    $sql = "UPDATE employees SET name = :name, email = :email, phone = :phone, picture = :picture, manager_id = :manager_id WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':picture' => $picture_path,
        ':manager_id' => $manager_id,
        ':id' => $employee_id,
    ]);

    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="../public/css/all.css">
</head>
<body>
    <form action="edit.php?id=<?=htmlspecialchars($employee_id);?>" method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?=htmlspecialchars($employee['name']);?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?=htmlspecialchars($employee['email']);?>" required><br>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" value="<?=htmlspecialchars($employee['phone']);?>" required><br>

        <label for="picture">Profile Picture:</label>
        <input type="file" name="picture" accept="image/*"><br>
        <img src="<?=htmlspecialchars($employee['picture']);?>" alt="Profile Picture" style="max-width: 100px; max-height: 100px;"><br>

        <label for="manager_id">Manager ID:</label>
        <input type="text" name="manager_id" value="<?=htmlspecialchars($employee['manager_id']);?>" required><br><br>

        <input type="submit" value="Update Employee">
    </form>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="success"><?=htmlspecialchars($_SESSION['message']);?></div>
        <?php unset($_SESSION['message']);?>
    <?php endif;?>
</body>
</html>
