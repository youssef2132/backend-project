<?php
session_start();
require '../config/config.php';

// Check if employee ID is set
if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    try {
        // Delete the employee from the database
        $sql = "DELETE FROM employees WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $employee_id]);

        // Set success message
        $_SESSION['message'] = "Employee deleted successfully.";
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error'] = "Failed to delete employee: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid employee ID.";
}

// Redirect to the employee list
header("Location: ../index.php");
exit();
