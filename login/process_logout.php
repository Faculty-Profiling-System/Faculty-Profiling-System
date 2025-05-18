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

// Start transaction
$conn->begin_transaction();
try {
    // Update login record if possible
    if (!empty($_SESSION['current_login_id'])) {
        $update_query = "UPDATE user_logins 
                        SET logout_time = NOW(), 
                            session_status = 'completed'
                        WHERE login_id = ? AND user_id = ?";
        
        $stmt = $conn->prepare($update_query);
        
        if ($stmt) {
            $stmt->bind_param("ii", $_SESSION['current_login_id'], $_SESSION['user_id']);
            if ($stmt->execute()) {
                $affected = $stmt->affected_rows;
                error_log("Logout updated - Rows affected: $affected");
                
                if ($affected === 0) {
                    error_log("Warning: No rows updated - login_id may not exist");
                }
            } else {
                throw new Exception("Logout update failed: ".$stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Prepare failed: ".$conn->error);
        }
    } else {
        error_log("No current_login_id in session");
    }
    
    // Commit transaction if everything succeeded
    $conn->commit();
    error_log("Database changes committed successfully");

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    
    // Continue with logout even if DB update failed
    error_log("Proceeding with session destruction despite database error");
}

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