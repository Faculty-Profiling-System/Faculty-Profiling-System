<?php
session_start();
header('Content-Type: application/json');
require_once '../../db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id']) && !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing credential ID']);
    exit();
}

$credential_id = intval($_GET['id'] ?? $_POST['id']);
$faculty_id = $_SESSION['faculty_id'];

try {
    // Get file path to delete the file
    $stmt = $conn->prepare("SELECT file_path FROM credentials WHERE credential_id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $credential_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $credential = $result->fetch_assoc();
    $stmt->close();

    if (!$credential) {
        echo json_encode(['success' => false, 'message' => 'Credential not found']);
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

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error deleting credential: ' . $e->getMessage()]);
}
?>