<?php
require '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // 1. Check if the faculty_id exists in the faculty table
    $checkFaculty = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE faculty_id = ?");
    $checkFaculty->bind_param("s", $faculty_id);
    $checkFaculty->execute();
    $checkFaculty->bind_result($facultyExists);
    $checkFaculty->fetch();
    $checkFaculty->close();

    if ($facultyExists == 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid faculty ID. No matching record found in faculty table.']);
        exit;
    }

    // 2. Check if the college_id exists in the colleges table
    $college_id = $_POST['college_id'] ?? '';
    $checkCollege = $conn->prepare("SELECT COUNT(*) FROM colleges WHERE college_id = ?");
    $checkCollege->bind_param("i", $college_id);
    $checkCollege->execute();
    $checkCollege->bind_result($collegeExists);
    $checkCollege->fetch();
    $checkCollege->close();

    if ($collegeExists == 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid college ID. No matching record found in colleges table.']);
        exit;
    }

    // 3. Hash the password before inserting into the database
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert the user into the users table
    $stmt = $conn->prepare("INSERT INTO users (faculty_id, college_id, username, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $faculty_id, $college_id, $username, $passwordHash, $role);

    try {
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'User added successfully.']);
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>