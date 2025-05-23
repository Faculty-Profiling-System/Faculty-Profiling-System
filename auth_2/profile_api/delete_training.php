<?php
require_once __DIR__ . '/../../db_connection.php';
session_start();

if (!isset($_SESSION['faculty_id']) || !isset($_GET['id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

$faculty_id = $_SESSION['faculty_id'];
$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("DELETE FROM training_programs WHERE id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $id, $faculty_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Training program deleted successfully']);
    } else {
        throw new Exception("Delete operation failed");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>