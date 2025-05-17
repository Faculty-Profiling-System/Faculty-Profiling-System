<?php
session_start();
require '../db_connection.php';

// Debug: Log session data
error_log("Starting logout - Session: ".print_r($_SESSION, true));

// Verify session exists
if (empty($_SESSION['user_id'])) {
    error_log("No active session during logout");
    header("Location: ../landing/index.php");
    exit();
}

// Update login record if possible
if (!empty($_SESSION['current_login_id'])) {
    $update_query = "UPDATE user_logins 
                    SET logout_time = NOW(), 
                        session_status = 'completed'
                    WHERE login_id = ? AND user_id = ? AND logout_time IS NULL";
    
    $stmt = $conn->prepare($update_query);
    
    if ($stmt) {
        $stmt->bind_param("ii", $_SESSION['current_login_id'], $_SESSION['user_id']);
        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            error_log("Logout updated - Rows affected: $affected");
        } else {
            error_log("Logout update failed: ".$stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Prepare failed: ".$conn->error);
    }
} else {
    error_log("No current_login_id in session");
}

// Force immediate write to database
$conn->commit();

// Completely destroy session
session_unset();
session_destroy();
session_write_close();

// Debug confirmation
error_log("Logout completed successfully");

// Redirect with cache prevention
header("Cache-Control: no-cache, must-revalidate");
header("Location: ../landing/index.php");
exit();
?>