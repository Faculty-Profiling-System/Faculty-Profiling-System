<?php
session_start();
require_once '../db_connection.php';

if (!isset($_SESSION['faculty_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['credential_id']) || !is_numeric($_POST['credential_id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid credential ID']);
    exit();
}

$credential_id = (int)$_POST['credential_id'];
$faculty_id = $_SESSION['faculty_id'];
$credentialType = $_POST['credential_type'];
$credentialName = $_POST['credential_name'];
$issuedBy = $_POST['issued_by'];
$issuedDate = $_POST['issued_date'];
$expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

// Validate credential type against enum values
$validTypes = ['PDS', 'SALN', 'TOR', 'Diploma', 'Certificates', 'Evaluation'];
if (!in_array($credentialType, $validTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid credential type']);
    exit();
}

// Validate dates
$currentDate = date('Y-m-d');
if ($issuedDate > $currentDate) {
    echo json_encode(['success' => false, 'message' => 'Issued date cannot be in the future']);
    exit();
}

if ($expiryDate && $expiryDate < $issuedDate) {
    echo json_encode(['success' => false, 'message' => 'Expiry date must be after issued date']);
    exit();
}

try {
    // Check if file was uploaded
    if (isset($_FILES['credential_file']) && $_FILES['credential_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['credential_file'];
        $originalFileName = $file['name'];
        $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        
        if ($fileExt !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'You can only upload PDF files']);
            exit();
        }
        
        if ($file['size'] > 5000000) { // 5MB
            echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 5MB']);
            exit();
        }
        
        // Get old file path to delete later
        $stmt = $conn->prepare("SELECT file_path FROM credentials WHERE credential_id = ? AND faculty_id = ?");
        $stmt->bind_param("is", $credential_id, $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldCredential = $result->fetch_assoc();
        $oldFilePath = $oldCredential['file_path'];
        
        // Upload new file
        $newFileName = uniqid('', true) . '.pdf';
        $fileDestination = 'uploads/credentials/' . $newFileName;
        
        if (!move_uploaded_file($file['tmp_name'], $fileDestination)) {
            echo json_encode(['success' => false, 'message' => 'There was an error uploading your file']);
            exit();
        }
        
        // Update with new file
        $stmt = $conn->prepare("UPDATE credentials SET 
            credential_type = ?, 
            credential_name = ?, 
            issued_by = ?, 
            issued_date = ?, 
            expiry_date = ?, 
            file_path = ?,
            status = 'Pending',
            verified_at = NULL,
            reason = NULL
            WHERE credential_id = ? AND faculty_id = ?");
        
        $stmt->bind_param("ssssssis", 
            $credentialType, 
            $credentialName, 
            $issuedBy, 
            $issuedDate, 
            $expiryDate, 
            $fileDestination,
            $credential_id,
            $faculty_id
        );
    } else {
        // Update without changing file
        $stmt = $conn->prepare("UPDATE credentials SET 
            credential_type = ?, 
            credential_name = ?, 
            issued_by = ?, 
            issued_date = ?, 
            expiry_date = ?,
            status = 'Pending',
            verified_at = NULL,
            reason = NULL
            WHERE credential_id = ? AND faculty_id = ?");
        
        $stmt->bind_param("sssssis", 
            $credentialType, 
            $credentialName, 
            $issuedBy, 
            $issuedDate, 
            $expiryDate,
            $credential_id,
            $faculty_id
        );
    }
    
    $stmt->execute();
    
    // Delete old file if new one was uploaded
    if (isset($oldFilePath) && file_exists($oldFilePath)) {
        unlink($oldFilePath);
    }
    
    echo json_encode(['success' => true, 'message' => 'Credential updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>