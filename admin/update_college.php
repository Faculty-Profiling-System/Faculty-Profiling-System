<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collegeId = $_POST['college_id'] ?? 0;
    $collegeName = trim($_POST['college_name'] ?? '');
    
    if (empty($collegeName)) {
        echo json_encode(['success' => false, 'message' => 'College name is required']);
        exit;
    }
    
    // Check if college exists
    $stmt = $conn->prepare("SELECT college_id FROM colleges WHERE college_id = ?");
    $stmt->bind_param("i", $collegeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'College not found']);
        exit;
    }
    
    // Check if another college already has this name
    $stmt = $conn->prepare("SELECT college_id FROM colleges WHERE college_name = ? AND college_id != ?");
    $stmt->bind_param("si", $collegeName, $collegeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Another college already has this name']);
        exit;
    }
    
    // Update college
    $stmt = $conn->prepare("UPDATE colleges SET college_name = ? WHERE college_id = ?");
    $stmt->bind_param("si", $collegeName, $collegeId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update college']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>