<?php
require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_id = $_POST['faculty_id'];
    $college_id = $_POST['college_id'];
    $name = $_POST['name'];
    $email = $_POST['email_address'];
    $username = $_POST['username'];
    $new_password = $_POST['password'];

    // 1. Update faculty table
    $stmt1 = $conn->prepare("UPDATE faculty SET college_id=?, full_name=?, email=? WHERE faculty_id=?");
    $stmt1->bind_param("ssss", $college_id, $name, $email, $faculty_id);

    // 2. Prepare second query (users table)
    if (!empty($new_password)) {
        // If new password is provided, update it
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET username=?, password_hash=? WHERE faculty_id=?");
        $stmt2->bind_param("sss", $username, $hashed_password, $faculty_id);
    } else {
        // If password not changed, skip it
        $stmt2 = $conn->prepare("UPDATE users SET username=? WHERE faculty_id=?");
        $stmt2->bind_param("ss", $username, $faculty_id);
    }

    if ($stmt1->execute() && $stmt2->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>