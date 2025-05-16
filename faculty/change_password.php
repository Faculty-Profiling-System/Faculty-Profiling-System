<?php
session_start();
require_once '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['faculty_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$faculty_id = $_SESSION['faculty_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];

// Validate password length
if (strlen($new_password) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 5 characters long!']);
    exit;
}

// Verify current password
$stmt = $conn->prepare("SELECT password_hash FROM users WHERE faculty_id = ?");
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($current_password, $user['password_hash'])) {
    echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
    exit;
}

// Hash new password
$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// Update password
$stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE faculty_id = ?");
$stmt->bind_param("ss", $new_password_hash, $faculty_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
}

$stmt->close();
$conn->close();
?> 