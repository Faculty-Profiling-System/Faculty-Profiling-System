<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collegeId = $_POST['college_id'] ?? 0;
    
    // Check if college exists
    $stmt = $conn->prepare("SELECT college_id FROM colleges WHERE college_id = ?");
    $stmt->bind_param("i", $collegeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'College not found']);
        exit;
    }
    
    // Delete college
    $stmt = $conn->prepare("DELETE FROM colleges WHERE college_id = ?");
    $stmt->bind_param("i", $collegeId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete college']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>