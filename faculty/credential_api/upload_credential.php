<?php
session_start();
require_once '../../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

// Validate inputs
$required = ['credentialType', 'credentialName', 'issuedBy', 'issuedDate'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        die(json_encode(['success' => false, 'message' => "Field $field is required"]));
    }
}

// File upload handling
if (empty($_FILES['credentialFile'])) {
    die(json_encode(['success' => false, 'message' => 'No file uploaded']));
}

$file = $_FILES['credentialFile'];
$allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
$maxSize = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowedTypes)) {
    die(json_encode(['success' => false, 'message' => 'Invalid file type']));
}

if ($file['size'] > $maxSize) {
    die(json_encode(['success' => false, 'message' => 'File too large (max 5MB)']));
}

// Create upload directory if not exists
$uploadDir = "../uploads/credentials/{$_SESSION['user_id']}/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $ext;
$filepath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    die(json_encode(['success' => false, 'message' => 'File upload failed']));
}

// Prepare the expiry date value
$expiryDate = !empty($_POST['expiryDate']) ? $_POST['expiryDate'] : null;

// Insert into database using MySQLi
try {
    $stmt = $conn->prepare("INSERT INTO credentials (
        faculty_id, credential_type, credential_name, 
        issued_by, issued_date, expiry_date, file_path, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    
    $stmt->bind_param("sssssss", 
    $_SESSION['faculty_id'],
    $_POST['credentialType'],
    $_POST['credentialName'],
    $_POST['issuedBy'],
    $_POST['issuedDate'],
    $expiryDate,
    $filepath
    );
    
    // At the end of successful upload (replace the JSON response):
    if ($stmt->execute()) {
        header('Location: ../credentials.php?upload=success');
        exit();
    } else {
        header('Location: ../credentials.php?upload=error&message=' . urlencode($conn->error));
        exit();
    }
    
} catch (Exception $e) {
    // Clean up the uploaded file if there was an error
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]));
}
?>