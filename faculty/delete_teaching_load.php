<?php
session_start();
require_once('../db_connection.php');

// Check if faculty is logged in
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.php');
    exit();
}

// Check if load ID is provided
if (isset($_GET['id'])) {
    $load_id = $_GET['id'];
    $faculty_id = $_SESSION['faculty_id'];
    
    try {
        // First get the file path and verify ownership
        $stmt = $conn->prepare("SELECT file_path FROM teaching_load WHERE load_id = ? AND faculty_id = ?");
        $stmt->bind_param("is", $load_id, $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $file_path = $row['file_path'];
            
            // Delete the record from database
            $delete_stmt = $conn->prepare("DELETE FROM teaching_load WHERE load_id = ? AND faculty_id = ?");
            $delete_stmt->bind_param("is", $load_id, $faculty_id);
            $delete_stmt->execute();
            
            // Delete the physical file if it exists
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            // Set success alert
            $_SESSION['show_alert'] = 'success';
        } else {
            // Set error alert (record not found or not owned by user)
            $_SESSION['show_alert'] = 'error';
            $_SESSION['error_message'] = "Record not found or you don't have permission to delete it.";
        }
        
        // Close statements
        $stmt->close();
        if (isset($delete_stmt)) {
            $delete_stmt->close();
        }
    } catch (Exception $e) {
        // Set database error alert
        $_SESSION['show_alert'] = 'error';
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }
} else {
    // No ID provided
    $_SESSION['show_alert'] = 'error';
    $_SESSION['error_message'] = "No teaching load specified for deletion.";
}

// Redirect back to teaching load page
header('Location: teachingload.php');
exit();
?>