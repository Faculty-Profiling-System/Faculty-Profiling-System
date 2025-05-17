<?php
session_start();
require '../db_connection.php';

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection failed. Check db_connection.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("Location: index.php?error=Username and password are required");
        exit();
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use a dummy password hash for non-existent users to prevent timing attacks
    $dummy_hash = password_hash('dummy_password', PASSWORD_DEFAULT);
    $authenticated = false;

    // Modified query to get both user and faculty/college info if available
    $query = "SELECT u.*, f.college_id 
              FROM users u 
              LEFT JOIN faculty f ON u.faculty_id = f.faculty_id 
              WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        header("Location: index.php?error=System error");
        exit();
    }
    
    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        header("Location: index.php?error=System error");
        exit();
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify password (works with dummy hash for non-existent users)
    $authenticated = $user && password_verify($password, $user['password_hash'] ?? $dummy_hash);

    if ($authenticated && $user) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        
        // Set session variables (original logic)
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['faculty_id'] = $user['faculty_id'];
        
        // Add college_id to session if available (for logging)
        if (isset($user['college_id'])) {
            $_SESSION['college_id'] = $user['college_id'];
        }

        // Only record login for faculty users (not admins)
        if ($user['role'] === 'Faculty' && isset($user['college_id'])) {
            // Record login
            $insert_login = "INSERT INTO user_logins (user_id, college_id, login_time, ip_address, user_agent, session_status) 
                            VALUES (?, ?, NOW(), ?, ?, 'active')";
            $login_stmt = $conn->prepare($insert_login);
            
            if ($login_stmt) {
                $college_id = $user['college_id'] ?? null; // Handle null for admins
                $login_stmt->bind_param("iiss", 
                    $user['user_id'], 
                    $college_id,
                    $_SERVER['REMOTE_ADDR'], 
                    $_SERVER['HTTP_USER_AGENT']
                );
                
                if ($login_stmt->execute()) {
                    $_SESSION['current_login_id'] = $conn->insert_id;
                    error_log("Login recorded - ID: ".$conn->insert_id); // Debug
                }
                $login_stmt->close();
            }
        }

        // Redirect based on role (original logic)
        if ($user['role'] === 'Admin') {
            header("Location: ../admin/home.php");
        } else {
            header("Location: ../faculty/home.php");
        }
        exit();
    } else {
        header("Location: index.php?error=Invalid credentials");
        exit();
    }
} else {
    header("Location: index.php?error=Invalid request method");
    exit();
}
?>