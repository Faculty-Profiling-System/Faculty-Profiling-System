<?php
require '../db_connection.php';

$orphaned = $conn->query("SELECT COUNT(*) FROM user_logins 
                         WHERE logout_time IS NULL 
                         AND session_status = 'active'
                         AND login_time < NOW() - INTERVAL 4 HOUR");

echo "Orphaned sessions found: " . $orphaned->fetch_row()[0];