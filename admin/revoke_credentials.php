<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['credential_id']) || !isset($data['reason'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$credentialId = $data['credential_id'];
$reason = $data['reason'];

// Update credential status to Rejected
$sql = "UPDATE credentials SET status = 'Rejected', rejection_reason = ?, verified_at = NULL 
        WHERE credential_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $reason, $credentialId);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>