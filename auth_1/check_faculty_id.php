<?php
require_once '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faculty_id = $_POST['faculty_id'];

    // Query to check if the faculty_id exists in the faculty table
    $query = "SELECT faculty_id FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "found"; // Faculty ID exists
    } else {
        echo "not_found"; // Faculty ID does not exist
    }
}
?>