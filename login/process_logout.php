<?php
session_start();
require '../db_connection.php';

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
    try {
        $update_query = "UPDATE user_logins 
                        SET logout_time = NOW(), 
                            session_status = 'completed'
                        WHERE login_id = ? AND user_id = ? AND logout_time IS NULL";
        
        $stmt = $conn->prepare($update_query);
        
        if ($stmt) {
            $stmt->bind_param("ii", $_SESSION['current_login_id'], $_SESSION['user_id']);
            if (!$stmt->execute()) {
                error_log("Logout update failed: ".$stmt->error);
            }
            $stmt->close();
        } else {
            error_log("Prepare failed: ".$conn->error);
        }
    } catch (Exception $e) {
        error_log("Exception during logout update: ".$e->getMessage());
    }
} else {
    error_log("No current_login_id in session");
}

// Force immediate write to database
$conn->commit();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Clear all session data
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Completely destroy session
session_unset();
session_destroy();

// Debug confirmation
error_log("Logout completed successfully");

// Redirect with cache prevention
header("Location: ../landing/index.php");
exit();
?>