<?php
session_start();
include '../db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$facultyName = "USER";
$scheduleCount = 0;
$latestSchedule = null;

if (isset($_SESSION['faculty_id'])) {
    $facultyId = $_SESSION['faculty_id'];
    
    // Get faculty name
    $query = "SELECT full_name FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $facultyName = strtoupper($row['full_name']);
    }
    
    // Get count of uploaded schedules
    $countQuery = "SELECT COUNT(*) as count FROM teaching_load 
                  WHERE faculty_id = ? AND status = 'successful'";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("s", $facultyId);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    
    if ($countRow = $countResult->fetch_assoc()) {
        $scheduleCount = $countRow['count'];
    }
    
    // Get latest uploaded schedule
    $latestQuery = "SELECT file_path, created_at FROM teaching_load
                   WHERE faculty_id = ? AND status = 'successful'
                   ORDER BY created_at DESC LIMIT 1";
    $latestStmt = $conn->prepare($latestQuery);
    $latestStmt->bind_param("s", $facultyId);
    $latestStmt->execute();
    $latestResult = $latestStmt->get_result();
    
    if ($latestRow = $latestResult->fetch_assoc()) {
        $latestSchedule = [
            'file_path' => $latestRow['file_path'],
            'upload_date' => date('M d, Y', strtotime($latestRow['created_at']))
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/homedashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
</head>
<body>
<div class="header">
    <div class="logo-section">
      <img src="../images/logo.png" alt="Logo" />
      <div class="title">PAMANTASAN NG LUNGSOD NG PASIG</div>
      <button class="hamburger" onclick="toggleMenu()">
        <div id="bar1" class="bar"></div>
        <div id="bar2" class="bar"></div>
        <div id="bar3" class="bar"></div>
      </button>
    </div>
  </div>

  <div class="navigation" id="menu">
    <div class="navigation-header">
        <h1>FACULTY</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>
        
    <nav>
      <ul>
        <li><a href="home.php" class="active"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
  </div>

    <div class="dashboard-container">
        <div class="welcome-banner">
            <h2>Welcome back, <?php echo htmlspecialchars($facultyName); ?>!</h2>
            <p>Here's your dashboard overview</p>
        </div>

        <div class="dashboard-grid">
            <!-- Profile Information Card -->
            <div class="dashboard-card profile-card">
                <div class="card-header">
                    <i class="fas fa-user-circle"></i>
                    <h3>Profile Information</h3>
                </div>
                <div class="card-body">
                    <div class="profile-info">
                        <div class="info-item">
                            <span class="info-label">Position:</span>
                            <span class="info-value">Associate Professor</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">College:</span>
                            <span class="info-value">College of Computer Studies</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value">fajardo_rebecca@plpasig.edu.ph</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Contact:</span>
                            <span class="info-value">+63 912 345 6789</span>
                        </div>
                    </div>
                    <a href="profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                </div>
            </div>

            <!-- Teaching Schedule Card -->
            <div class="dashboard-card schedule-card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Teaching Schedules</h3>
                </div>
                <div class="card-body">
                    <div class="schedule-summary">
                        <div class="schedule-count">
                            <i class="fas fa-file-upload"></i>
                            <span class="count-number"><?php echo $scheduleCount; ?></span>
                            <span class="count-label">Uploaded Schedule<?php echo $scheduleCount !== 1 ? 's' : ''; ?></span>
                        </div>
                        <div class="schedule-info">
                            <?php if ($scheduleCount > 0): ?>
                                <p>You have successfully uploaded <?php echo $scheduleCount; ?> teaching schedule<?php echo $scheduleCount !== 1 ? 's' : ''; ?>.</p>
                                <?php if ($latestSchedule): ?>
                                    <div class="latest-upload">
                                        <p>Latest upload: <?php echo $latestSchedule['upload_date']; ?></p>
                                        <a href="<?php echo htmlspecialchars($latestSchedule['file_path']); ?>" target="_blank">
                                            <i class="fas fa-eye"></i> View Latest
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>No teaching schedules uploaded yet.</p>
                                <p>Upload your teaching schedule to get started.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="teachingload.php" class="btn btn-primary">
                        <i class="fas <?php echo $scheduleCount > 0 ? 'fa-calendar-check' : 'fa-calendar-plus'; ?>"></i> 
                        <?php echo $scheduleCount > 0 ? 'Manage Schedules' : 'Upload Schedule'; ?>
                    </a>
                </div>
            </div>

            <!-- Uploaded Credentials Card -->
            <div class="dashboard-card credentials-card">
                <div class="card-header">
                    <i class="fas fa-file-certificate"></i>
                    <h3>Uploaded Credentials</h3>
                </div>
                <div class="card-body">
                    <div class="credentials-list">
                        <p>No Credentials uploaded yet.</p>
                        <p>Upload your Credentials to get started.</p>
                    </div>
                    <a href="credentials.php" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload More
                    </a>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="dashboard-card quick-actions-card">
                <div class="card-header">
                    <i class="fas fa-bolt"></i>
                    <h3>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="action-buttons">
                        <a href="profile.php" class="action-btn">
                            <i class="fas fa-user-edit"></i>
                            <span>Update Profile</span>
                        </a>
                        <a href="teachingload.php" class="action-btn">
                            <i class="fas fa-tasks"></i>
                            <span>View Load</span>
                        </a>
                        <a href="credentials.php" class="action-btn">
                            <i class="fas fa-file-upload"></i>
                            <span>Upload Files</span>
                        </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Button -->
    <div class="help-button" onclick="toggleHelpPopout()">
        <i class="fas fa-question"></i>
    </div>

    <!-- Main Help Popout -->
    <div id="helpPopout" class="popout">
        <div class="popout-header">
            <h3>Need Help?</h3>
            <span class="popout-close" onclick="closeHelpPopout()">&times;</span>
        </div>
        <div class="help-option" onclick="openFaqPopout()">
            <i class="fas fa-question-circle"></i> FAQ's
        </div>
        <div class="help-option" onclick="openContactPopout()">
            <i class="fas fa-headset"></i> Still need help?
        </div>
    </div>

    <!-- FAQ Popout -->
    <div id="faqPopout" class="content-popout">
        <div class="popout-header">
            <h3>Frequently Asked Questions</h3>
            <span class="popout-close" onclick="closeFaqPopout()">&times;</span>
        </div>
        <div class="faq-item">
            <div class="faq-question">Q: How do I update my profile information?</div>
            <p>A: Go to the Profile section and click on the "Edit Profile" button.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">Q: How do I upload my teaching schedule?</div>
            <p>A: Navigate to Teaching Load section and use the "Upload Schedule" button.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">Q: What file formats are accepted?</div>
            <p>A: We accept PDF, JPG, and PNG files for credential uploads.</p>
        </div>
        <div class="faq-item">
            <div class="faq-question">Q: How do I change my password?</div>
            <p>A: Go to Settings and use the "Change Password" option.</p>
        </div>
    </div>

    <!-- Contact Popout -->
    <div id="contactPopout" class="content-popout">
        <div class="popout-header">
            <h3>Contact Support</h3>
            <span class="popout-close" onclick="closeContactPopout()">&times;</span>
        </div>
        <p>If you need further assistance:</p>
        <div class="contact-info">
            <p><i class="fas fa-envelope"></i> support@plpasig.edu.ph</p>
            <p><i class="fas fa-phone"></i> +63 2 123 4567</p>
            <p><i class="fas fa-clock"></i> Mon-Fri, 8:00 AM - 5:00 PM</p>
            <p><i class="fas fa-map-marker-alt"></i> Admin Building, Room 101</p>
        </div>
    </div>
                                
    <script>
        function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../landing/index.php';
        }}
    </script>
    <script src="help.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>