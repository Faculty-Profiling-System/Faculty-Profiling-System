<?php
require_once '../db_connection.php';
require_once 'email_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_POST['faculty_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $college_id = $_POST['college_id'] ?? '';

    try {
        // Get the faculty email first
        $email_query = "SELECT email FROM faculty WHERE faculty_id = ?";
        $stmt = $conn->prepare($email_query);
        $stmt->bind_param("s", $faculty_id);
        $stmt->execute();
        $email_result = $stmt->get_result();
        
        if ($email_result->num_rows === 0) {
            die("Error: Faculty not found");
        }
        
        $faculty_data = $email_result->fetch_assoc();
        $email = $faculty_data['email'];

        // Prepare the update query
        $update_query = "UPDATE users SET username = ?";
        $params = [$username];
        $types = "s";
        
        // Only update password if a new one was provided
        $password_updated = false;
        if (!empty($new_password)) {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query .= ", password_hash = ?";
            $params[] = $password_hash;
            $types .= "s";
            $password_updated = true;
        }
        
        $update_query .= " WHERE faculty_id = ? AND college_id = ?";
        $params[] = $faculty_id;
        $params[] = $college_id;
        $types .= "si";
        
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            // Send email only if password was updated
            if ($password_updated) {
                $email_sent = sendTemporaryPasswordEmail($email, $username, $new_password);
                
                if (!$email_sent) {
                    // Log this error but don't show to user
                    error_log("Failed to send email to $email");
                }
            }
            
            echo "success";
        } else {
            echo "No changes made or user not found";
        }
    } catch (Exception $e) {
        error_log("Error updating user: " . $e->getMessage());
        echo "Error updating user: " . $e->getMessage();
    }
} else {
    header("Location: user.php");
    exit();
}
?>