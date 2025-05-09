<?php
session_start();
require_once '../db_connection.php';

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Content-Type: application/json");

// Configure upload directory (secure location)
$uploadDir = __DIR__ . '/uploads/';
$publicDir = 'uploads/';

// Create directory if needed with strict permissions
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0750, true)) { // Restrict permissions
        http_response_code(500);
        die(json_encode(['success' => false, 'message' => 'Failed to create upload directory']));
    }
}

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'filepath' => null,
    'db_record_id' => null
];

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

// Validate faculty session
if (!isset($_SESSION['faculty_id'])) {
    http_response_code(401);
    $response['message'] = 'Unauthorized access';
    echo json_encode($response);
    exit;
}

/**
 * Get human-readable upload error message
 */
function getUploadError($errorCode) {
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server size limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file selected',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
        UPLOAD_ERR_EXTENSION => 'File type not allowed',
    ];
    return $errors[$errorCode] ?? 'Unknown upload error';
}

try {
    // Validate file upload
    if (!isset($_FILES['scheduleFile']) || $_FILES['scheduleFile']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException(getUploadError($_FILES['scheduleFile']['error'] ?? UPLOAD_ERR_NO_FILE));
    }

    $file = $_FILES['scheduleFile'];
    $facultyId = $_SESSION['faculty_id'];

    // Security checks
    if ($file['size'] === 0) {
        throw new RuntimeException('Empty file uploaded');
    }

    // Verify PDF using multiple methods
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedMimeTypes = ['application/pdf', 'application/x-pdf'];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        throw new RuntimeException('Only PDF files are allowed');
    }

    // Check file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($fileExt !== 'pdf') {
        throw new RuntimeException('Invalid file extension');
    }

    // Check file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('File exceeds 5MB limit');
    }

    // Generate secure filename
    $filename = sprintf(
        'schedule_%d_%d_%s.pdf',
        $facultyId,
        time(),
        bin2hex(random_bytes(4))
    );
    $destination = $uploadDir . $filename;

    // Sanitize filename
    $filename = preg_replace("/[^a-zA-Z0-9._-]/", "", $filename);
    $destination = $uploadDir . $filename;

    // Check for existing file
    if (file_exists($destination)) {
        throw new RuntimeException('File already exists');
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Failed to save file');
    }

    // Verify file was saved
    if (!file_exists($destination)) {
        throw new RuntimeException('File verification failed');
    }

    // Clean up previous file
    if (!empty($_SESSION['last_pdf']) && file_exists($uploadDir . basename($_SESSION['last_pdf']))) {
        @unlink($uploadDir . basename($_SESSION['last_pdf']));
    }

    // Store public path
    $publicPath = $publicDir . $filename;
    $_SESSION['last_pdf'] = $publicPath;

    // Database transaction
    $conn->begin_transaction();

    try {
        // Insert into teaching_loads table
        $stmt = $conn->prepare("
            INSERT INTO teaching_load 
            (faculty_id, file_path, status, created_at) 
            VALUES (?, ?, 'successful', NOW())
        ");
        $stmt->bind_param("is", $facultyId, $publicPath);
        
        if (!$stmt->execute()) {
            throw new RuntimeException('Database insert failed');
        }

        $recordId = $conn->insert_id;
        $conn->commit();

        // Successful response
        $response = [
            'success' => true,
            'message' => 'Schedule uploaded successfully',
            'filepath' => $publicPath,
            'db_record_id' => $recordId
        ];

    } catch (Exception $e) {
        $conn->rollback();
        throw $e; // Re-throw for outer catch block
    }

} catch (RuntimeException $e) {
    // Log failed attempt
    if (isset($facultyId)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO teaching_load 
                (faculty_id, file_path, status, created_at) 
                VALUES (?, ?, 'failed', NOW())
            ");
            $path = isset($publicPath) ? $publicPath : 'upload_failed';
            $stmt->bind_param("is", $facultyId, $path);
            $stmt->execute();
        } catch (Exception $dbError) {
            error_log("Failed to log error: " . $dbError->getMessage());
        }
    }

    http_response_code(400);
    $response['message'] = $e->getMessage();
    error_log("Upload Error [Faculty {$facultyId}]: " . $e->getMessage());
}

// Clean output buffer and send response
ob_end_clean();
echo json_encode($response);
exit;