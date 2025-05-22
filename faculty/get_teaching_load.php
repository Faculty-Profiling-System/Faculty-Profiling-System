<?php
session_start();
require_once('../db_connection.php');

if (!isset($_SESSION['faculty_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Load ID is required']);
    exit();
}

$loadId = $_GET['id'];
$facultyId = $_SESSION['faculty_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM teaching_load WHERE load_id = ? AND faculty_id = ?");
    $stmt->bind_param("ss", $loadId, $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Teaching load not found or access denied']);
        exit();
    }
    
    $load = $result->fetch_assoc();
    echo json_encode(['success' => true, 'load' => $load]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>