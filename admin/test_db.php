<?php
require_once '../db_connection.php';

// Test connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connection successful!<br>";
}

// Test query
$result = $conn->query("SELECT * FROM colleges");
if (!$result) {
    echo "Query error: " . $conn->error;
} else {
    echo "Query executed successfully. Found " . $result->num_rows . " rows.";
}
?>