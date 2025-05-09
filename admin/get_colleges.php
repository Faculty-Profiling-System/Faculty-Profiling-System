<?php
require_once '../db_connection.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM colleges ORDER BY college_id";
$result = $conn->query($sql);

$colleges = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $colleges[] = [
            'college_id' => $row['college_id'],
            'college_name' => htmlspecialchars($row['college_name'])
        ];
    }
}

echo json_encode($colleges);
$conn->close();
?>