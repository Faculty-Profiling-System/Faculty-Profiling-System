<?php
require_once __DIR__ . '/../../db_connection.php';
session_start();

// Validate inputs
if (!isset($_SESSION['faculty_id']) || !isset($_GET['id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$faculty_id = $_SESSION['faculty_id'];
$id = (int)$_GET['id']; // Ensure ID is integer

try {
    // Get specific education record that belongs to this faculty
    $stmt = $conn->prepare("SELECT * FROM academic_background WHERE id = ? AND faculty_id = ?");
    $stmt->bind_param("is", $id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['error' => 'Record not found']));
    }

    header('Content-Type: application/json');
    echo json_encode($result->fetch_assoc());

} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error']));
}
?>