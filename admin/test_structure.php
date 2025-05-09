<?php
require_once '../db_connection.php';

$result = $conn->query("DESCRIBE colleges");
if ($result) {
    echo "<pre>";
    print_r($result->fetch_all(MYSQLI_ASSOC));
    echo "</pre>";
} else {
    echo "Error: " . $conn->error;
}
?>