<?php
session_start();
require_once('../db_connection.php');

if (!isset($_SESSION['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['scheduleFile']) || $_FILES['scheduleFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit();
}

// Validate file type and size
$file = $_FILES['scheduleFile'];
$allowedTypes = ['application/pdf'];
$maxSize = 5 * 1024 * 1024; // 5MB

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed']);
    exit();
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit();
}

// Validate form data
$requiredFields = ['fileName', 'semester', 'startYear', 'endYear', 'totalLoad'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
}

$semester = $_POST['semester'];

// Validate year format
$startYear = (int)$_POST['startYear'];
$endYear = (int)$_POST['endYear'];
if ($startYear < 2000 || $startYear > 2099 || $endYear < 2000 || $endYear > 2099) {
    echo json_encode(['success' => false, 'message' => 'Year must be between 2000-2099']);
    exit();
}

// Validate total loads is numeric
if (!is_numeric($_POST['totalLoad'])) {
    echo json_encode(['success' => false, 'message' => 'Total loads must be a number']);
    exit();
}

// Create upload directory if it doesn't exist
$uploadDir = 'uploads/teaching_loads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique filename
$fileName = preg_replace('/[^a-zA-Z0-9\-\._]/', '', $_POST['fileName']);
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = 'teachingload_' . $_SESSION['faculty_id'] . '_' . time() . '.' . $extension;
$filePath = $uploadDir . $newFilename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    exit();
}

// Insert into database
try {
    $stmt = $conn->prepare("INSERT INTO teaching_load 
        (faculty_id, file_name, semester, start_year, end_year, total_loads, file_path, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    
    $stmt->bind_param("ssssiis", 
        $_SESSION['faculty_id'],
        $_POST['fileName'],
        $semester,
        $startYear,
        $endYear,
        $_POST['totalLoad'],
        $filePath
    );
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Teaching load uploaded successfully',
            'filepath' => $filePath,
            'fileName' => $_POST['fileName'],
            'semester' => $semester,
            'startYear' => $startYear,
            'endYear' => $endYear,
            'totalLoad' => $_POST['totalLoad']
        ]);
    } else {
        // Delete the uploaded file if database insert failed
        unlink($filePath);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Delete the uploaded file if there was an error
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>