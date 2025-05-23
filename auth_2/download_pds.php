<?php
require '../vendor/autoload.php';
require_once __DIR__ . '/../db_connection.php';
session_start();

use Dompdf\Dompdf;

// Get faculty ID from session
if (!isset($_SESSION['faculty_id'])) {
    die("Faculty ID not found in session");
}
$faculty_id = $_SESSION['faculty_id'];

// Fetch all required data
$data = [];

// 1. Fetch personal information
$query = "SELECT 
            f.*, 
            c.college_name, 
            p.*
          FROM faculty f 
          JOIN colleges c ON f.college_id = c.college_id 
          LEFT JOIN faculty_personal_info p ON f.faculty_id = p.faculty_id
          WHERE f.faculty_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$data['faculty_data'] = $result->fetch_assoc();

// 2. Fetch educational background
$academic_query = "SELECT * FROM academic_background WHERE faculty_id = ? ORDER BY end_year DESC";
$stmt = $conn->prepare($academic_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$data['academic_data'] = $stmt->get_result();

// 3. Fetch civil service eligibility
$civil_query = "SELECT * FROM civil_service_eligibility WHERE faculty_id = ?";
$stmt = $conn->prepare($civil_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$data['civil_data'] = $stmt->get_result();

// 4. Fetch work experience
$work_query = "SELECT * FROM work_experience WHERE faculty_id = ? ORDER BY date_from DESC";
$stmt = $conn->prepare($work_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$data['work_data'] = $stmt->get_result();

// 5. Fetch training programs
$training_query = "SELECT * FROM training_programs WHERE faculty_id = ? ORDER BY date_from DESC";
$stmt = $conn->prepare($training_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$data['training_data'] = $stmt->get_result();

// Extract variables for the template
extract($data);

// Generate PDF
$dompdf = new Dompdf();
ob_start();
include 'pds-template.php';
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output the generated PDF
$dompdf->stream("Personal_Data_Sheet_{$faculty_id}.pdf", ["Attachment" => 1]);
?>