<?php
require_once '../db_connection.php';
require_once '../vendor/autoload.php';  // For PHPMailer

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$load_id = isset($_POST['load_id']) ? intval($_POST['load_id']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : null;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

if (!$load_id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$allowed_statuses = ['Pending', 'Verified', 'Rejected'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit();
}

try {
    if (strtolower($status) === 'rejected') {
        $stmt = $conn->prepare("UPDATE teaching_load SET verified_at = NOW(), status = ?, reason = ? WHERE load_id = ?");
        $stmt->bind_param("ssi", $status, $reason, $load_id);
    } else {
        $stmt = $conn->prepare("UPDATE teaching_load SET verified_at = NOW(), status = ?, reason = NULL WHERE load_id = ?");
        $stmt->bind_param("si", $status, $load_id);
    }
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        if (strtolower($status) === 'rejected') {
            // Fetch faculty email for notification
            $stmt = $conn->prepare("SELECT f.email FROM faculty f JOIN teaching_load t ON f.faculty_id = t.faculty_id WHERE t.load_id = ?");
            $stmt->bind_param("i", $load_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $faculty = $result->fetch_assoc();
                $facultyEmail = $faculty['email'];

                $mail = new \PHPMailer\PHPMailer\PHPMailer;
                $mail->isSMTP();    
                $mail->Host = 'smtp-relay.brevo.com';
                $mail->SMTPAuth = true;
                $mail->Username = '8cc35a002@smtp-brevo.com';
                $mail->Password = 'JSh1qV4zbR7DWaI0';
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('plp.no.reply1@gmail.com', 'NO REPLY');
                $mail->addAddress($facultyEmail);

                $mail->Subject = 'Teaching Load Rejection Notification';
                $mail->Body = "Dear Faculty,\n\nYour teaching load document has been rejected due to the following reason:\n\n" . $reason . "\n\nPlease contact support for further assistance.";

                if ($mail->send()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Faculty email not found']);
                exit();
            }
        } else {
            echo json_encode(['success' => true]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or teaching load not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>