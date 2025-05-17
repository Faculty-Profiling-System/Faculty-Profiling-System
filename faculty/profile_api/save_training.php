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
        $required = ['training_title', 'conducted_by', 'number_of_hours', 'date_from', 'date_to'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Sanitize input and set defaults
        $id = $_POST['id'] ?? null;
        $training_title = trim($_POST['training_title']);
        $conducted_by = trim($_POST['conducted_by']);
        $learning_type = !empty(trim($_POST['learning_type'])) ? trim($_POST['learning_type']) : 'Other';
        $number_of_hours = (int)$_POST['number_of_hours'];
        $date_from = $_POST['date_from'];
        $date_to = $_POST['date_to'];

        // Validate data
        if ($number_of_hours <= 0) {
            throw new Exception("Hours must be greater than 0");
        }
        
        // Validate dates
        if (strtotime($date_from) > time()) {
            throw new Exception("Start date cannot be in the future");
        }
        
        if ($date_to && strtotime($date_to) < strtotime($date_from)) {
            throw new Exception("End date cannot be before start date");
        }

        // Prepare SQL (insert or update)
        if (empty($id)) {
            $sql = "INSERT INTO training_programs 
                    (faculty_id, training_title, conducted_by, learning_type, 
                     number_of_hours, date_from, date_to)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssiss", 
                $faculty_id, $training_title, $conducted_by, 
                $learning_type, $number_of_hours, $date_from, $date_to);
        } else {
            $sql = "UPDATE training_programs SET
                    training_title = ?,
                    conducted_by = ?,
                    learning_type = ?,
                    number_of_hours = ?,
                    date_from = ?,
                    date_to = ?
                    WHERE id = ? AND faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissii", 
                $training_title, $conducted_by, 
                $learning_type, $number_of_hours, $date_from, $date_to,
                $id, $faculty_id);
        }

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => empty($id) ? 'Training program added successfully' : 'Training program updated successfully'
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