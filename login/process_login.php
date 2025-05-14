process_login.php
<?php
session_start();
require '../db_connection.php'; // Make sure this path is correct

// Debug: Check if connection exists
if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Database connection failed. Check db_connection.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query to find the user
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    
    // Debug: Check if prepare worked
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Login successful - set sessions
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['faculty_id'] = $user['faculty_id'];

            // Redirect based on role
            if ($user['role'] === 'Admin') {
                header("Location: ../admin/home.php");
            } else {
                header("Location: ../faculty/home.php");
            }
            exit();
        } else {
            header("Location: index.php?error=Incorrect password");
            exit();
        }
    } else {
        header("Location: index.php?error=User not found");
        exit();
    }
}
?>