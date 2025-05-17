<?php
session_start();
require_once __DIR__ . '/../../db_connection.php';

// Set header for JSON response
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal'])) {
    
    $faculty_id = $_SESSION['faculty_id'];
    
    try {
        // Validate birthdate (must be in the past)
        if (!empty($_POST['birthdate'])) {
            $birthdate = new DateTime($_POST['birthdate']);
            $today = new DateTime();
            
            if ($birthdate > $today) {
                throw new Exception("Birthdate cannot be in the future");
            }
            
            // Validation for Birthyear
            $minBirthYear = date('Y') - 100; // 100 years ago
            $maxBirthYear = date('Y') - 18;  // At least 18 years old
            $birthYear = $birthdate->format('Y');
            
            if ($birthYear < $minBirthYear || $birthYear > $maxBirthYear) {
                throw new Exception("Birth year must be between $minBirthYear and $maxBirthYear");
            }
        }

        // Validate ID number lengths
        $idValidations = [
            'gsis_id_no' => ['min' => 11, 'max' => 11, 'name' => 'GSIS ID'],
            'pagibig_id_no' => ['min' => 12, 'max' => 12, 'name' => 'Pag-IBIG ID'],
            'philhealth_no' => ['min' => 12, 'max' => 12, 'name' => 'PhilHealth Number'],
            'sss_no' => ['min' => 10, 'max' => 10, 'name' => 'SSS Number'],
            'tin_no' => ['min' => 9, 'max' => 12, 'name' => 'TIN']
        ];

        foreach ($idValidations as $field => $rules) {
            if (!empty($_POST[$field])) {
                $rawValue = $_POST[$field];
                $numericValue = preg_replace('/\D/', '', $rawValue); // remove non-digits
                $length = strlen($numericValue);

                // Length check
                if ($length < $rules['min'] || $length > $rules['max']) {
                    if ($rules['min'] === $rules['max']) {
                        throw new Exception("{$rules['name']} must be exactly {$rules['max']} digits long.");
                    } else {
                        throw new Exception("{$rules['name']} must be between {$rules['min']} and {$rules['max']} digits long.");
                    }
                }

                // Allowed characters check (numbers and hyphens only)
                if (!preg_match('/^[0-9-]+$/', $rawValue)) {
                    throw new Exception("{$rules['name']} can only contain numbers and hyphens.");
                }
            }
        }
        
        // Validate other fields
        if (!empty($_POST['contact_number'])) {
            $cleanedNumber = preg_replace('/[^0-9+]/', '', $_POST['contact_number']);
            if (strlen($cleanedNumber) < 11 || strlen($cleanedNumber) > 13) {
                throw new Exception("Contact number must be 11-13 digits (after removing special characters)");
            }
        }
        
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (!empty($_POST['height_cm']) && ($_POST['height_cm'] < 100 || $_POST['height_cm'] > 250)) {
            throw new Exception("Height must be between 100cm and 250cm");
        }
        
        if (!empty($_POST['weight_kg']) && ($_POST['weight_kg'] < 30 || $_POST['weight_kg'] > 300)) {
            throw new Exception("Weight must be between 30kg and 300kg");
        }

        // Begin transaction
        $conn->begin_transaction();
        
        // 1. Update faculty table
        $faculty_sql = "UPDATE faculty SET 
                        employment_type = ?,
                        full_name = ?,
                        email = ?,
                        specialization = ?,
                        contact_number = ?
                        WHERE faculty_id = ?";
        
        $faculty_stmt = $conn->prepare($faculty_sql);
        $faculty_stmt->bind_param("ssssss", 
            $_POST['employment_type'],
            $_POST['full_name'],
            $_POST['email'],
            $_POST['specialization'],
            $_POST['contact_number'],
            $faculty_id
        );
        
        if (!$faculty_stmt->execute()) {
            throw new Exception("Faculty update failed: " . $faculty_stmt->error);
        }
        
        // 2. Update personal info
        $personal_sql = "INSERT INTO faculty_personal_info (
                            faculty_id, birthdate, birthplace, gender, civil_status,
                            height_cm, weight_kg, blood_type, citizenship, address,
                            gsis_id_no, pagibig_id_no,
                            philhealth_no, sss_no, tin_no
                         ) VALUES (
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                         )
                         ON DUPLICATE KEY UPDATE
                            birthdate = VALUES(birthdate),
                            birthplace = VALUES(birthplace),
                            gender = VALUES(gender),
                            civil_status = VALUES(civil_status),
                            height_cm = VALUES(height_cm),
                            weight_kg = VALUES(weight_kg),
                            blood_type = VALUES(blood_type),
                            citizenship = VALUES(citizenship),
                            address = VALUES(address),
                            gsis_id_no = VALUES(gsis_id_no),
                            pagibig_id_no = VALUES(pagibig_id_no),
                            philhealth_no = VALUES(philhealth_no),
                            sss_no = VALUES(sss_no),
                            tin_no = VALUES(tin_no)";
        
        $personal_stmt = $conn->prepare($personal_sql);
        $personal_stmt->bind_param("sssssddssssssss",
            $faculty_id,
            $_POST['birthdate'],
            $_POST['birthplace'],
            $_POST['gender'],
            $_POST['civil_status'],
            $_POST['height_cm'],
            $_POST['weight_kg'],
            $_POST['blood_type'],
            $_POST['citizenship'],
            $_POST['address'],
            $_POST['gsis_id_no'],
            $_POST['pagibig_id_no'],
            $_POST['philhealth_no'],
            $_POST['sss_no'],
            $_POST['tin_no']
        );
        
        if (!$personal_stmt->execute()) {
            throw new Exception("Personal info update failed: " . $personal_stmt->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        $response['success'] = true;
        $response['message'] = "Personal information updated successfully!";
        
    } catch (Exception $e) {
        if (isset($conn) && $conn instanceof mysqli) {
            $conn->rollback();
        }
        $response['message'] = $e->getMessage();
        error_log("Update error: " . $e->getMessage());
    }
}

echo json_encode($response);
exit();
?>