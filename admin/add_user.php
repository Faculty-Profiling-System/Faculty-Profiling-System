<?php
require_once '../db_connection.php';

// Set header for JSON response
header('Content-Type: application/json');

// Initialize response array
$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception('Invalid request method');
    }

    // Validate and sanitize inputs
    $required_fields = [
        'faculty_id' => 'Faculty ID',
        'college_id' => 'College',
        'full_name' => 'Full Name',
        'birthday' => 'Birthday',
        'gender' => 'Gender',
        'email' => 'Email',
        'employment_type' => 'Employment Type',
        'username' => 'Username',
        'password' => 'Password',
        'role' => 'Role'
    ];

    $data = [];
    foreach ($required_fields as $field => $name) {
        if (empty($_POST[$field])) {
            throw new Exception("$name is required");
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Optional fields
    $data['contact_number'] = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : null;
    $data['address'] = isset($_POST['address']) ? trim($_POST['address']) : null;

    // Validate email format
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Validate college_id is numeric
    if (!is_numeric($data['college_id'])) {
        throw new Exception('Invalid college selection');
    }
    $data['college_id'] = (int)$data['college_id'];

    // Check for existing records
    $checks = [
        ['table' => 'faculty', 'field' => 'email', 'value' => $data['email'], 'message' => 'Email already exists'],
        ['table' => 'users', 'field' => 'username', 'value' => $data['username'], 'message' => 'Username already taken'],
        ['table' => 'faculty', 'field' => 'faculty_id', 'value' => $data['faculty_id'], 'message' => 'Faculty ID already exists']
    ];

    foreach ($checks as $check) {
        $stmt = $conn->prepare("SELECT {$check['field']} FROM {$check['table']} WHERE {$check['field']} = ?");
        $stmt->bind_param("s", $check['value']);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            throw new Exception($check['message']);
        }
    }

    // Hash password
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Begin transaction
    $conn->begin_transaction();

    // Insert into faculty table
    $stmt1 = $conn->prepare("INSERT INTO faculty 
        (faculty_id, college_id, full_name, birthday, gender, email, employment_type, contact_number, address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt1->bind_param("sisssssss", 
        $data['faculty_id'],
        $data['college_id'],
        $data['full_name'],
        $data['birthday'],
        $data['gender'],
        $data['email'],
        $data['employment_type'],
        $data['contact_number'],
        $data['address']
    );
    
    if (!$stmt1->execute()) {
        throw new Exception('Failed to save faculty data: ' . $stmt1->error);
    }

    // Insert into users table
    $stmt2 = $conn->prepare("INSERT INTO users (faculty_id, username, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("ssss", 
        $data['faculty_id'],
        $data['username'],
        $password_hash,
        $data['role']
    );
    
    if (!$stmt2->execute()) {
        throw new Exception('Failed to create user account: ' . $stmt2->error);
    }

    // Commit transaction
    $conn->commit();

    $response['status'] = 'success';
    $response['message'] = 'User registered successfully';

} catch (Exception $e) {
    // Rollback transaction if there was an error
    if (isset($conn)) {
        $conn->rollback();
    }
    $response['message'] = $e->getMessage();
} finally {
    echo json_encode($response);
    exit;
}
?>