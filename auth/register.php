<?php
require '../config/config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    $sql = "INSERT INTO managers (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':password' => $password,
    ]);

    echo "<script>
    alert('Registration successful!');
    window.location.href = './login.php'; 
  </script>";
}
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
    <form action="./register.php" method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
</body>
</html>
