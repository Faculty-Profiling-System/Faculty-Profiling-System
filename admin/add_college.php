<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collegeName = trim($_POST['college_name'] ?? '');
    
    if (empty($collegeName)) {
        echo json_encode(['success' => false, 'message' => 'College name is required']);
        exit;
    }
    
    // Check if college already exists
    $stmt = $conn->prepare("SELECT college_id FROM colleges WHERE college_name = ?");
    $stmt->bind_param("s", $collegeName);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'College already exists']);
        exit;
    }
    
    // Insert new college
    $stmt = $conn->prepare("INSERT INTO colleges (college_name) VALUES (?)");
    $stmt->bind_param("s", $collegeName);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add college']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>