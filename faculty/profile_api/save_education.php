<?php
require_once __DIR__ . '/../../db_connection.php';
session_start();

// Validate faculty ID
if (!isset($_SESSION['faculty_id'])) {
    die(json_encode(['success' => false, 'error' => 'Unauthorized access']));
}

$faculty_id = $_SESSION['faculty_id'];

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required = ['level', 'institution_name', 'start_year', 'end_year'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Sanitize input and set defaults
        $id = $_POST['id'] ?? null;
        $degree_level = trim($_POST['level']);
        $institution_name = trim($_POST['institution_name']);
        
        // Set "N/A" if empty for these fields
        $degree_title = !empty(trim($_POST['degree_course'])) ? trim($_POST['degree_course']) : 'N/A';
        $honors = !empty(trim($_POST['honors'])) ? trim($_POST['honors']) : 'N/A';
        
        $start_year = (int)$_POST['start_year'];
        $end_year = (int)$_POST['end_year'];

        // Validate years
        if ($start_year > $end_year) {
            throw new Exception("End year must be greater than start year");
        }

        // Prepare SQL (insert or update)
        if (empty($id)) {
            $sql = "INSERT INTO academic_background 
                    (faculty_id, level, institution_name, degree_course, start_year, end_year, honors)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssiis", 
                $faculty_id, $degree_level, 
                $institution_name, $degree_title,
                $start_year, 
                $end_year, $honors);
        } else {
            $sql = "UPDATE academic_background SET
                    level = ?,
                    institution_name = ?,
                    degree_course = ?,
                    start_year = ?,
                    end_year = ?,
                    honors = ?
                    WHERE id = ? AND faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiisii", 
                $degree_level, $institution_name, $degree_title,
                $start_year, $end_year, $honors,
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