<?php
session_start();
header('Content-Type: application/json');
require_once '../../db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing credential ID']);
    exit();
}

$credential_id = intval($_GET['id']);
$faculty_id = $_SESSION['faculty_id'];

try {
    // Get file path and status to verify
    $stmt = $conn->prepare("SELECT file_path, status FROM credentials WHERE credential_id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $credential_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $credential = $result->fetch_assoc();
    $stmt->close();

    if (!$credential) {
        echo json_encode(['success' => false, 'message' => 'Credential not found']);
        exit();
    }

    // Check if credential can be deleted (only Pending or Rejected)
    if ($credential['status'] !== 'Pending' && $credential['status'] !== 'Rejected') {
        echo json_encode(['success' => false, 'message' => 'Only Pending or Rejected credentials can be deleted']);
        exit();
    }

    // Delete from DB
    $stmt = $conn->prepare("DELETE FROM credentials WHERE credential_id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $credential_id, $faculty_id);
    $stmt->execute();
    $stmt->close();

    // Delete file from server
    if (!empty($credential['file_path']) && file_exists($credential['file_path'])) {
        unlink($credential['file_path']);
    }

    echo json_encode(['success' => true, 'message' => 'Credential deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting credential: ' . $e->getMessage()]);
}
?>