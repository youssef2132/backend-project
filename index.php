<?php
require 'config/config.php';
session_start();

if (!isset($_SESSION['managers_id'])) {
    header(header: "Location:./auth/login.php", response_code: 404);
    exit();
}

// Fetch the manager's data
$sql = "SELECT * FROM managers WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $_SESSION['managers_id']]);
$manager = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all employees
$allEmployees = $conn->query("SELECT * FROM employees")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background-color: #f4f4f4;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            background-color: #fff;
        }

        a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .delete {
    background-color: orangered;
    color: #fff;
    padding: 12px 15px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.delete:hover {
    background-color: darkred;
    color: #fff;
}


    </style>
</head>

<body>
    <div class="container" style="position: relative">
        <h1>Welcome, <?=htmlspecialchars($manager['name']);?>!</h1>
        <h2 style="text-align: center; margin: 28px auto; font-size: xx-large;">Employees List</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Picture</th>
                    <th>Manager ID</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allEmployees as $employee): ?>
                    <tr>
                        <td><?=htmlspecialchars($employee->id);?></td>
                        <td><?=htmlspecialchars($employee->name);?></td>
                        <td><a href="mailto:<?=htmlspecialchars($employee->email);?>"><?=htmlspecialchars($employee->email);?></a></td>
                        <td><a href="tel:<?= htmlspecialchars(strrev($employee->phone)); ?>"><?= htmlspecialchars(strrev($employee->phone)); ?></a></td>
                        <td>
                            <img src="<?=htmlspecialchars($employee->picture);?>" alt="Profile Picture" style="max-width: 100px; max-height: 100px;">
                        </td>
                        <td><?=htmlspecialchars($employee->manager_id);?></td>
                        <td>
                            <a href="./empolyees/edit.php?id=<?=htmlspecialchars($employee->id);?>">
                            <img src="public/assets/pencil-line.svg" alt="Edit" style="width: 20px; height: 20px;">
                        </a>
                        </td>
                        <td >
                        <a class="delete"  href="./empolyees/delete.php?id=<?=htmlspecialchars($employee->id);?>" onclick="return confirm('Are you sure you want to delete this employee?');">
                        <img src="public/assets/delete-bin-6-line.svg" alt="Delete" style="width: 20px; height: 20px;">
                        </a>
                        </td>

                    </tr>

                <?php endforeach;?>
            </tbody>
        </table>

        <a class="delete" href="./auth/logout.php">Logout</a>
        <a style="right: 20px; position: absolute;" href="./empolyees/add.php">Insert</a>
    </div>
</body>

</html>
