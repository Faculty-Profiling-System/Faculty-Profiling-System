<?php
require_once __DIR__ . '/../../db_connection.php';
session_start();

// Validate faculty ID
if (!isset($_SESSION['faculty_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Unauthorized access']));
}

$faculty_id = $_SESSION['faculty_id'];

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required = ['position_title', 'department_or_agency', 'is_government_service', 'date_from'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Sanitize input and set defaults
        $id = $_POST['id'] ?? null;
        $position_title = trim($_POST['position_title']);
        $department_or_agency = trim($_POST['department_or_agency']);
        $salary_grade_step = !empty(trim($_POST['salary_grade_step'])) ? trim($_POST['salary_grade_step']) : 'N/A';
        $monthly_salary = !empty($_POST['monthly_salary']) ? (float)$_POST['monthly_salary'] : null;
        $appointment_status = !empty(trim($_POST['appointment_status'])) ? trim($_POST['appointment_status']) : 'N/A';
        $is_government_service = $_POST['is_government_service'];
        $date_from = $_POST['date_from'];
        $date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : null;

        // Validate dates
        if (strtotime($date_from) > time()) {
            throw new Exception("Start date cannot be in the future");
        }
        
        if ($date_to && strtotime($date_to) < strtotime($date_from)) {
            throw new Exception("End date cannot be before start date");
        }

        // Prepare SQL (insert or update)
        if (empty($id)) {
            $sql = "INSERT INTO work_experience 
                    (faculty_id, position_title, department_or_agency, salary_grade_step, 
                     monthly_salary, appointment_status, is_government_service, date_from, date_to)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssdssss", 
                $faculty_id, $position_title, $department_or_agency, 
                $salary_grade_step, $monthly_salary, $appointment_status,
                $is_government_service, $date_from, $date_to);
        } else {
            $sql = "UPDATE work_experience SET
                    position_title = ?,
                    department_or_agency = ?,
                    salary_grade_step = ?,
                    monthly_salary = ?,
                    appointment_status = ?,
                    is_government_service = ?,
                    date_from = ?,
                    date_to = ?
                    WHERE id = ? AND faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssdssssis", 
                $position_title, $department_or_agency, 
                $salary_grade_step, $monthly_salary, $appointment_status,
                $is_government_service, $date_from, $date_to,
                $id, $faculty_id);
        }

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => empty($id) ? 'Record added successfully' : 'Record updated successfully'
            ]);
        } else {
            throw new Exception("Database error: " . $conn->error);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>