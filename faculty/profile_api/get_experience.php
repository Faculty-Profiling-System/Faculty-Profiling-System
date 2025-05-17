<?php
require_once __DIR__ . '/../../db_connection.php';
session_start();

if (!isset($_SESSION['faculty_id']) || !isset($_GET['id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

$faculty_id = $_SESSION['faculty_id'];
$id = (int)$_GET['id'];

try {
    $stmt = $conn->prepare("SELECT * FROM work_experience WHERE id = ? AND faculty_id = ?");
    $stmt->bind_param("is", $id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        die(json_encode(['error' => 'Record not found']));
    }
    
    $data = $result->fetch_assoc();
    
    // Convert NULL values to empty strings for form inputs
    $data['salary_grade_step'] = $data['salary_grade_step'] === 'N/A' ? '' : $data['salary_grade_step'];
    $data['monthly_salary'] = $data['monthly_salary'] ?? '';
    $data['appointment_status'] = $data['appointment_status'] === 'N/A' ? '' : $data['appointment_status'];
    $data['date_to'] = $data['date_to'] ?? '';
    
    header('Content-Type: application/json');
    echo json_encode($data);
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error']));
}
?>