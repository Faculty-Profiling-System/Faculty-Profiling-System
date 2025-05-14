<?php
require_once '../db_connection.php';
require_once '../vendor/autoload.php';  // Include Composer's autoload

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access', 'received_data' => $_POST]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method', 
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
    exit();
}

$credential_id = isset($_POST['credential_id']) ? intval($_POST['credential_id']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : null;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

if (!$credential_id || !$status) {
    echo json_encode([ 
        'success' => false, 
        'message' => 'Missing required fields',
        'received_data' => [
            'credential_id' => $credential_id,
            'status' => $status,
            'reason' => $reason
        ]
    ]);
    exit();
}

$allowed_statuses = ['Pending', 'Verified', 'Rejected'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit();
}

try {
    // Update the credential status
    if (strtolower($status) === 'rejected') {
        // Prepare the query for rejection
        $stmt = $conn->prepare("UPDATE credentials SET verified_at = NOW(), status = ?, reason = ? WHERE credential_id = ?");
        $stmt->bind_param("ssi", $status, $reason, $credential_id);
    } else {
        // Prepare the query for other statuses
        $stmt = $conn->prepare("UPDATE credentials SET verified_at = NOW(), status = ?, reason = NULL WHERE credential_id = ?");
        $stmt->bind_param("si", $status, $credential_id); // FIXED: removed extra param, correct binding
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // If the credential was updated successfully, fetch the faculty's email if rejected
        if (strtolower($status) === 'rejected') {
            // Fetch the email of the faculty linked to the credential
            $stmt = $conn->prepare("SELECT f.email FROM faculty f JOIN credentials c ON f.faculty_id = c.faculty_id WHERE c.credential_id = ?");
            $stmt->bind_param("i", $credential_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $faculty = $result->fetch_assoc();
                $facultyEmail = $faculty['email'];

                // Create a PHPMailer instance with correct namespace
                $mail = new \PHPMailer\PHPMailer\PHPMailer;
                $mail->isSMTP();  // Set mailer to use SMTP
                $mail->Host = 'smtp-relay.brevo.com';  // Brevo SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = '8cc35a002@smtp-brevo.com';  // Your Brevo SMTP Username
                $mail->Password = 'xsmtpsib-437784d0788f1667e9fd368e2c8ffed56846c7f464b08f405f8fb1d1622f46e8-XMqnFNrSp1GahgzD';  // Your Brevo SMTP Password
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;  // Use TLS
                $mail->Port = 587;  // TLS Port

                // Set the email details
                $mail->setFrom('plp.no.reply1@gmail.com', 'FACULTY PROFILING SYSTEM');
                $mail->addAddress($facultyEmail);  // Add the faculty's email

                $mail->Subject = 'Credential Rejection Notification';
                $mail->Body    = "Dear Faculty,\n\nYour credential has been rejected due to the following reason:\n\n" . $reason . "\n\nPlease contact support for further assistance.";

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
        echo json_encode([
            'success' => false,
            'message' => 'No changes made or credential not found',
            'affected_rows' => $stmt->affected_rows
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'error' => $e->getTraceAsString()
    ]);
}
?>