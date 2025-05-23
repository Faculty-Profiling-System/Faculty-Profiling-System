<?php
session_start();
require_once '../db_connection.php';

if (!isset($_SESSION['faculty_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid credential ID']);
    exit();
}

$credential_id = (int)$_GET['id'];
$faculty_id = $_SESSION['faculty_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM credentials WHERE credential_id = ? AND faculty_id = ?");
    $stmt->bind_param("is", $credential_id, $faculty_id); // i for integer, s for string
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Credential not found']);
        exit();
    }
    
    $credential = $result->fetch_assoc();
    
    // Convert dates to proper format for the form
    $credential['issued_date'] = date('Y-m-d', strtotime($credential['issued_date']));
    if ($credential['expiry_date']) {
        $credential['expiry_date'] = date('Y-m-d', strtotime($credential['expiry_date']));
    } else {
        $credential['expiry_date'] = '';
    }
    
    echo json_encode(['success' => true, 'credential' => $credential]);
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>