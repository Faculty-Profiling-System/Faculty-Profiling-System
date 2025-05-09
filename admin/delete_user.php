<?php
require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $faculty_id = $_POST['faculty_id'];

    $stmt = $conn->prepare("DELETE FROM faculty WHERE faculty_id=?");
    $stmt->bind_param("s", $faculty_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>