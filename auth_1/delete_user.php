<?php
require_once '../db_connection.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../landing/index.php");
    exit();
}

// Get faculty ID to delete
$facultyId = $_POST['faculty_id'];

// Start transaction
$conn->begin_transaction();

try {
    // Delete from users table first (due to foreign key constraint)
    $userStmt = $conn->prepare("DELETE FROM users WHERE faculty_id = ?");
    $userStmt->bind_param("s", $facultyId);
    $userStmt->execute();

    // Delete from faculty table
    $facultyStmt = $conn->prepare("DELETE FROM faculty WHERE faculty_id = ?");
    $facultyStmt->bind_param("s", $facultyId);
    $facultyStmt->execute();

    // Commit transaction
    $conn->commit();
    
    $_SESSION['success_message'] = "User deleted successfully!";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
}

header("Location: user.php");
exit();
?>