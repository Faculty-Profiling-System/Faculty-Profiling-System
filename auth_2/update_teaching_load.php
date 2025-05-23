<?php
session_start();
require_once('../db_connection.php');

if (!isset($_SESSION['faculty_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_POST['load_id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Load ID is required']);
    exit();
}

$loadId = $_POST['load_id'];
$facultyId = $_SESSION['faculty_id'];
$displayName = $_POST['display_name'];
$semester = $_POST['semester'];
$startYear = $_POST['start_year'];
$endYear = $_POST['end_year'];
$regularLoads = $_POST['regular_loads'];
$overloadUnits = $_POST['overload_units'];
$totalLoads = $regularLoads + $overloadUnits;

// Validate loads
if ($regularLoads < 0 || $overloadUnits < 0) {
    echo json_encode(['success' => false, 'message' => 'Load values cannot be negative']);
    exit();
}

if ($totalLoads > 50) {
    echo json_encode(['success' => false, 'message' => 'Total loads cannot exceed 50 units']);
    exit();
}

// Validate academic year range
if ($endYear < $startYear) {
    echo json_encode(['success' => false, 'message' => 'End year cannot be less than start year']);
    exit();
} elseif (($endYear - $startYear) > 1) {
    echo json_encode(['success' => false, 'message' => 'End year must not be ahead of start year by more than 1 year']);
    exit();
}

try {
    // Check if the load belongs to the faculty
    $checkStmt = $conn->prepare("SELECT * FROM teaching_load WHERE load_id = ? AND faculty_id = ?");
    $checkStmt->bind_param("ss", $loadId, $facultyId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Teaching load not found or access denied']);
        exit();
    }
    
    $currentLoad = $result->fetch_assoc();
    
    // Handle file upload if a new file was provided
    $filePath = $currentLoad['file_path'];
    if (isset($_FILES['teaching_file']) && $_FILES['teaching_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['teaching_file'];
        $originalFileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        
        // Validate file (PDF only, max 5MB)
        $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        $allowed = ['pdf'];
        
        if (!in_array($fileExt, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'You can only upload PDF files']);
            exit();
        }
        
        if ($fileSize > 5000000) { // 5MB
            echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 5MB']);
            exit();
        }
        
        $newFileName = uniqid('', true) . '.' . $fileExt;
        $newFilePath = 'uploads/teaching_loads/' . $newFileName;
        
        if (move_uploaded_file($fileTmpName, $newFilePath)) {
            // Delete the old file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $filePath = $newFilePath;
        } else {
            echo json_encode(['success' => false, 'message' => 'There was an error uploading your file']);
            exit();
        }
    }
    
    // Update the record
    $updateStmt = $conn->prepare("UPDATE teaching_load SET 
        file_name = ?, 
        semester = ?, 
        start_year = ?, 
        end_year = ?, 
        regular_loads = ?, 
        overload_units = ?, 
        total_loads = ?, 
        file_path = ?,
        status = 'Pending' 
        WHERE load_id = ?");
    
    $updateStmt->bind_param("sssiissss", 
        $displayName, 
        $semester, 
        $startYear, 
        $endYear, 
        $regularLoads, 
        $overloadUnits, 
        $totalLoads, 
        $filePath, 
        $loadId);
    
    $updateStmt->execute();
    $updateStmt->close();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>