<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['faculty_id']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$faculty_id = $data['faculty_id'];
$status = $data['status'];

try {
    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE faculty SET status = ? WHERE faculty_id = ?");
    $stmt->bind_param("ss", $status, $faculty_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>