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

    // Get user data including login attempts
    $query = "SELECT u.*, f.status as faculty_status, f.college_id 
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

    // Verify password
    $authenticated = $user && password_verify($password, $user['password_hash'] ?? $dummy_hash);

    if ($authenticated && $user) {
        // Check if account is active
        if ($user['faculty_status'] !== 'Active') {
            header("Location: index.php?error=Your account is inactive. Please contact your administrator.");
            exit();
        }

        // Reset login attempts on successful login
        $reset_attempts = $conn->prepare("UPDATE users SET login_attempts = 0 WHERE user_id = ?");
        $reset_attempts->bind_param("i", $user['user_id']);
        $reset_attempts->execute();
        $reset_attempts->close();

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['faculty_id'] = $user['faculty_id'];

        if (isset($user['college_id'])) {
            $_SESSION['college_id'] = $user['college_id'];
        }

        // Record login for faculty users
        if ($user['role'] === 'Faculty' && isset($user['college_id'])) {
            $insert_login = "INSERT INTO user_logins (user_id, college_id, login_time, ip_address, user_agent, session_status) 
                            VALUES (?, ?, NOW(), ?, ?, 'active')";
            $login_stmt = $conn->prepare($insert_login);

            if ($login_stmt) {
                $college_id = $user['college_id'] ?? null;
                $login_stmt->bind_param("iiss", 
                    $user['user_id'], 
                    $college_id,
                    $_SERVER['REMOTE_ADDR'], 
                    $_SERVER['HTTP_USER_AGENT']
                );

                if ($login_stmt->execute()) {
                    $_SESSION['current_login_id'] = $conn->insert_id;
                }
                $login_stmt->close();
            }
        }

        $user['login_attempts'] = 0; // Reset login attempts in session

        // Redirect based on role
        if ($user['role'] === 'Admin' || $user['role'] === 'Head') {
            header("Location: ../admin/home.php");
        } else { 
            header("Location: ../faculty/home.php");
        }
        exit();

    } else {
        // Handle failed login attempt
        if ($user) {
            $new_attempts = $user['login_attempts'] + 1;
            $max_attempts = 3;
            
            $update_stmt = $conn->prepare("UPDATE users SET login_attempts = ? WHERE user_id = ?");
            $update_stmt->bind_param("ii", $new_attempts, $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Custom messages based on attempt count
            $error_message = "Invalid username or password";
            
            if ($new_attempts == 1) {
                $error_message = "Invalid credentials. 2 attempts remaining.";
            } elseif ($new_attempts == 2) {
                $error_message = "Invalid credentials. 1 attempt remaining. Next failed attempt will lock your account.";
            } elseif ($new_attempts >= $max_attempts) {
                // Lock the account
                $deactivate_stmt = $conn->prepare("UPDATE faculty SET status = 'Inactive' WHERE faculty_id = ?");
                $deactivate_stmt->bind_param("s", $user['faculty_id']);
                $deactivate_stmt->execute();
                $deactivate_stmt->close();
                
                $error_message = "Account locked due to 3 failed attempts. Please contact your administrator.";
            }
            
            header("Location: index.php?error=" . urlencode($error_message));
            exit();
        }
        
        header("Location: index.php?error=Invalid username or password");
        exit();
    }
} else {
    header("Location: index.php?error=Invalid request method");
    exit();
}
?>