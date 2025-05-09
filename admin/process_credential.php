<?php
require_once '../db_connection.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credentialId = $_POST['credential_id'] ?? null;
    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';  // This is currently not being used in the process.
    $reason = $_POST['reason'] ?? '';

    if (!$credentialId || !in_array($action, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    // Update credential status
    $status = $action === 'approve' ? 'Verified' : 'Rejected';
    $stmt = $conn->prepare("UPDATE credentials SET status = ?, verified_at = NOW() WHERE credential_id = ?");
    $stmt->bind_param("si", $status, $credentialId);

    if ($stmt->execute()) {
        // Log the action if needed
        /*$logQuery = "INSERT INTO document_logs (user_id, action, file_name) VALUES (?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $fileName = "Credential ID: $credentialId";
        $logStmt->bind_param("iss", $_SESSION['user_id'], $action, $fileName);
        $logStmt->execute();*/
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
?>