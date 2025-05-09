<?php
session_start();
header('Content-Type: application/json');
require_once '../db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing credential ID']);
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$credential_id = intval($_GET['id']);

try {
    $stmt = $conn->prepare("SELECT * FROM credentials WHERE credential_id = ? AND faculty_id = ?");
    $stmt->bind_param("ii", $credential_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $credential = $result->fetch_assoc();
    $stmt->close();

    if ($credential) {
        echo json_encode($credential);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credential not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>