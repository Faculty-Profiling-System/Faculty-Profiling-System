<?php
require_once '../db_connection.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get and validate JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
$required = ['faculty_id', 'full_name', 'email', 'password'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst($field).' is required']);
        exit();
    }
}

// Prepare variables for binding
$faculty_id = $data['faculty_id'];
$full_name = $data['full_name'];
$email = $data['email'];
$password = $data['password'];
$college_id = isset($data['college_id']) ? $data['college_id'] : null;
$employment_type = isset($data['employment_type']) ? $data['employment_type'] : 'Full-Time';
$specialization = isset($data['specialization']) ? $data['specialization'] : null;
$contact_number = isset($data['contact_number']) ? $data['contact_number'] : null;
$status = isset($data['status']) ? $data['status'] : 'Active';

try {
    // Begin transaction
    $conn->begin_transaction();

    // Insert faculty
    $facultySql = "INSERT INTO faculty (faculty_id, college_id, full_name, email, employment_type, specialization, contact_number, status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $facultyStmt = $conn->prepare($facultySql);
    
    if (!$facultyStmt) {
        throw new Exception("Prepare failed: ".$conn->error);
    }

    // Bind parameters - note we pass the variables we prepared earlier
    $facultyStmt->bind_param(
        "sissssss",
        $faculty_id,
        $college_id,
        $full_name,
        $email,
        $employment_type,
        $specialization,
        $contact_number,
        $status
    );

    if (!$facultyStmt->execute()) {
        throw new Exception("Execute failed: ".$facultyStmt->error);
    }

    // Insert user
    $userSql = "INSERT INTO users (college_id, faculty_id, username, password_hash, role) 
                VALUES (?, ?, ?, ?, 'Faculty')";
    $userStmt = $conn->prepare($userSql);
    
    if (!$userStmt) {
        throw new Exception("Prepare failed: ".$conn->error);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $userStmt->bind_param(
        "isss",
        $college_id,
        $faculty_id,
        $email,
        $passwordHash
    );

    if (!$userStmt->execute()) {
        throw new Exception("Execute failed: ".$userStmt->error);
    }

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Faculty added successfully',
        'faculty_id' => $faculty_id
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: '.$e->getMessage()
    ]);
    error_log("Faculty addition error: ".$e->getMessage());
}
?>