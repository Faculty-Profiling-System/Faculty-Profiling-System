<?php
require_once '../db_connection.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../landing/index.php");
    exit();
}

// Get form data
$facultyId = $_POST['faculty_id'];
$collegeId = $_POST['college_id'];
$name = $_POST['name'];
$email = $_POST['email_address'];
$username = $_POST['username'];
$role = $_POST['role'];
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

// Start transaction
$conn->begin_transaction();

try {
    // Update faculty table
    $facultyStmt = $conn->prepare("
        UPDATE faculty 
        SET college_id = ?, full_name = ?, email_address = ?
        WHERE faculty_id = ?
    ");
    $facultyStmt->bind_param("isss", $collegeId, $name, $email, $facultyId);
    $facultyStmt->execute();

    // Update users table
    if ($password) {
        $userStmt = $conn->prepare("
            UPDATE users 
            SET username = ?, password = ?, role = ?
            WHERE faculty_id = ?
        ");
        $userStmt->bind_param("ssss", $username, $password, $role, $facultyId);
    } else {
        $userStmt = $conn->prepare("
            UPDATE users 
            SET username = ?, role = ?
            WHERE faculty_id = ?
        ");
        $userStmt->bind_param("sss", $username, $role, $facultyId);
    }
    $userStmt->execute();

    // Commit transaction
    $conn->commit();
    
    $_SESSION['success_message'] = "User updated successfully!";
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error_message'] = "Error updating user: " . $e->getMessage();
}

header("Location: user.php");
exit();
?>