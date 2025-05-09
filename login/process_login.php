<?php
session_start();
include '../db_connection.php';    

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize user input
    $username = trim($_POST['username']);  // Trim any extra spaces
    $password = trim($_POST['password']);  // Trim any extra spaces

    // Query to find the user
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password matches using password_verify
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['faculty_id'] = $user['faculty_id'];

            // Redirect to the appropriate dashboard based on role
            if ($user['role'] === 'Admin') {
                header("Location: ../admin/home.php"); // Correct path to admin dashboard
            }else{
                header("Location: ../faculty/home.php");
            }
            exit();
        } else {
            // Password is incorrect
            header("Location: index.php?error=Incorrect password");
            exit();
        }
    } else {
        // User not found
        header("Location: index.php?error=User not found");
        exit();
    }
}
?>