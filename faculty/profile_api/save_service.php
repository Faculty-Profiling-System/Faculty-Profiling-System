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
        $required = ['eligibility_type', 'date_of_examination', 'place_of_examination'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Sanitize input and set defaults
        $id = $_POST['id'] ?? null;
        $eligibility_type = trim($_POST['eligibility_type']);
        $rating = !empty(trim($_POST['rating'])) ? trim($_POST['rating']) : 'N/A';
        $date_of_examination = $_POST['date_of_examination'];
        $place_of_examination = trim($_POST['place_of_examination']);
        $license_number = !empty(trim($_POST['license_number'])) ? trim($_POST['license_number']) : 'N/A';
        $license_validity = !empty($_POST['license_validity']) ? $_POST['license_validity'] : null;

        // Validate dates
        if (strtotime($date_of_examination) > time()) {
            throw new Exception("Examination date cannot be in the future");
        }
        
        if ($license_validity && strtotime($license_validity) < strtotime($date_of_examination)) {
            throw new Exception("License validity cannot be before examination date");
        }

        // Prepare SQL (insert or update)
        if (empty($id)) {
            $sql = "INSERT INTO civil_service_eligibility 
                    (faculty_id, eligibility_type, rating, date_of_examination, 
                     place_of_examination, license_number, license_validity)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", 
                $faculty_id, $eligibility_type, $rating, 
                $date_of_examination, $place_of_examination,
                $license_number, $license_validity);
        } else {
            $sql = "UPDATE civil_service_eligibility SET
                    eligibility_type = ?,
                    rating = ?,
                    date_of_examination = ?,
                    place_of_examination = ?,
                    license_number = ?,
                    license_validity = ?
                    WHERE id = ? AND faculty_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssis", 
                $eligibility_type, $rating, 
                $date_of_examination, $place_of_examination,
                $license_number, $license_validity,
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