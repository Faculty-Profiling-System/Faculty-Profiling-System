<?php
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['department_id']);
    $department_name = trim($_POST['department_name']);

    if (empty($department_name)) {
        echo json_encode(['success' => false, 'message' => 'Department name is required.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
    $stmt->bind_param("si", $department_name, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
        header("Location: ../admin/department.php");
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update department.']);
    }

    $stmt->close();
    $conn->close();
}
?>