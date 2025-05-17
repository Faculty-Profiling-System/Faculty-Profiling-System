<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

if (!isset($_GET['faculty_id'])) {
    echo json_encode(['success' => false, 'message' => 'Faculty ID not provided']);
    exit();
}

$facultyId = $_GET['faculty_id'];

$sql = "SELECT * FROM credentials 
        WHERE faculty_id = ? AND status = 'Verified'
        ORDER BY credential_type, credential_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $facultyId);
$stmt->execute();
$result = $stmt->get_result();

$credentials = [];
while ($row = $result->fetch_assoc()) {
    $credentials[] = $row;
}

echo json_encode([
    'success' => true,
    'credentials' => $credentials
]);
?>