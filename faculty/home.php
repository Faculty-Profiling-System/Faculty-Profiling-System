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
    $stmt->bind_param("i", $facultyId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $facultyName = strtoupper($row['full_name']);
    }
    
    // Get count of uploaded schedules
    $countQuery = "SELECT COUNT(*) as count FROM teaching_load 
                  WHERE faculty_id = ? AND status = 'successful'";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param("i", $facultyId);
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
    $latestStmt->bind_param("i", $facultyId);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css" />
    <link rel="stylesheet" href="../css/homedashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Apply theme
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      // Apply text size
      const savedTextSize = localStorage.getItem('plpTextSize') || '100';
      document.documentElement.style.fontSize = savedTextSize + '%';
    });

    function toggleMenu() {
      const menu = document.getElementById('menu');
      const body = document.body;
      const bar1 = document.getElementById('bar1');
      const bar2 = document.getElementById('bar2');
      const bar3 = document.getElementById('bar3');

      if (!bar1.style.transform) {
        bar1.style.transform = 'rotate(0) translate(0)';
        bar2.style.opacity = '1';
        bar3.style.transform = 'rotate(0) translate(0)';
      }

      if (menu.classList.contains('active')) {
        menu.classList.remove('active');
        body.classList.remove('menu-open');
        bar1.style.transform = 'rotate(0) translate(0)';
        bar2.style.opacity = '1';
        bar3.style.transform = 'rotate(0) translate(0)';
      } else {
        menu.classList.add('active');
        body.classList.add('menu-open');
        bar1.style.transform = 'rotate(45deg) translate(5px, 5px)';
        bar2.style.opacity = '0';
        bar3.style.transform = 'rotate(-45deg) translate(7px, -6px)';
      }
    }
    </script>
    <style>
    /* Light theme (default) */
    body {
      background: #f4fff4;
      color: #187436;
    }

    /* Dark theme */
    body.dark-theme {
      background: #101010 !important;
      color: #f3f3f3 !important;
    }

    body.dark-theme .dashboard-container {
      background: #101010;
    }

    body.dark-theme .dashboard-grid {
      gap: 20px;
    }

    body.dark-theme .dashboard-card {
      background: #1a1a1a;
      border: 1px solid #333;
      color: #f3f3f3;
    }

    body.dark-theme .card-header {
      border-bottom: 1px solid #333;
    }

    body.dark-theme .card-header h3 {
      color: #00d34a;
    }

    body.dark-theme .card-header i {
      color: #00d34a;
    }

    body.dark-theme .welcome-banner {
      color: #f3f3f3;
    }

    body.dark-theme .welcome-banner h2 {
      color: #00d34a;
    }

    body.dark-theme .profile-info {
      border-color: #333;
    }

    body.dark-theme .info-label {
      color: #00d34a;
    }

    body.dark-theme .info-value {
      color: #cccccc;
    }

    body.dark-theme .btn-primary {
      background: #00d34a;
      color: #101010;
      border: none;
    }

    body.dark-theme .btn-primary:hover {
      background: #00b341;
    }

    body.dark-theme .schedule-count {
      color: #f3f3f3;
    }

    body.dark-theme .count-number {
      color: #00d34a;
    }

    body.dark-theme .count-label {
      color: #f3f3f3;
    }

    body.dark-theme .schedule-info {
      color: #cccccc;
    }

    body.dark-theme .schedule-info p {
      color: #cccccc;
    }

    body.dark-theme .schedule-info .count-number {
      color: #00d34a;
      font-weight: bold;
    }

    body.dark-theme .schedule-info .schedule-text {
      color: #cccccc;
      margin-bottom: 10px;
    }

    body.dark-theme .latest-upload {
      color: #cccccc;
    }

    body.dark-theme .latest-upload p {
      margin-bottom: 10px;
    }

    /* Style for the date container */
    body.dark-theme .latest-upload div {
      background: #222222;
      border: 1px solid #333;
      padding: 10px 15px;
      border-radius: 4px;
      color: #f3f3f3;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    body.dark-theme .latest-upload a {
      color: #00d34a;
      text-decoration: none;
    }

    body.dark-theme .latest-upload a:hover {
      color: #00b341;
    }

    body.dark-theme .credentials-list {
      color: #cccccc;
    }

    body.dark-theme .action-buttons {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }

    body.dark-theme .action-btn {
      background: #1a1a1a;
      border: 1px solid #333;
      color: #f3f3f3;
      padding: 15px;
      border-radius: 4px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: all 0.3s ease;
    }

    body.dark-theme .action-btn:hover {
      background: #00d34a;
      color: #101010;
      border-color: #00d34a;
    }

    body.dark-theme .action-btn i {
      color: #00d34a;
    }

    body.dark-theme .action-btn:hover i {
      color: #101010;
    }

    body.dark-theme .navigation {
      background: #101010;
      border-right: 1px solid #333;
    }

    body.dark-theme .navigation a {
      color: #f3f3f3;
    }

    body.dark-theme .navigation a:hover,
    body.dark-theme .navigation a.active {
      background: #00d34a;
      color: #101010;
    }

    body.dark-theme .navigation-header h1,
    body.dark-theme .navigation-header h2 {
      color: #f3f3f3;
    }

    body.dark-theme .logout-section a {
      color: #f3f3f3;
    }

    body.dark-theme .logout-section a:hover {
      background: #00d34a;
      color: #101010;
    }

    /* Style for the date container in dark theme */
    body.dark-theme .schedule-info .latest-upload {
        background: #222222;
        border: 1px solid #333;
        padding: 10px 15px;
        border-radius: 4px;
        margin: 10px 0;
    }

    body.dark-theme .schedule-info .latest-upload p {
        color: #f3f3f3;
        margin: 0;
    }

    body.dark-theme .schedule-info .latest-upload a {
        color: #00d34a;
        text-decoration: none;
    }

    body.dark-theme .schedule-info .latest-upload a:hover {
        color: #00b341;
    }

    /* Specific style for the date display */
    body.dark-theme .schedule-info .latest-upload > div {
        background: #222222;
        color: #f3f3f3;
        border: 1px solid #333;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: 5px;
    }

    /* View Latest button style */
    body.dark-theme .schedule-info .latest-upload a[href*="View Latest"] {
        display: inline-block;
        padding: 5px 10px;
        background: #222222;
        color: #00d34a;
        border: 1px solid #333;
        border-radius: 4px;
        text-decoration: none;
        margin-top: 5px;
    }

    body.dark-theme .schedule-info .latest-upload a[href*="View Latest"]:hover {
        background: #00d34a;
        color: #101010;
        border-color: #00d34a;
    }

    /* Latest upload container styling */
    body.dark-theme .latest-upload-container {
        background: #222222;
        border: 1px solid #333;
        padding: 12px 15px;
        border-radius: 4px;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    body.dark-theme .latest-upload-container p {
        color: #f3f3f3;
        margin: 0;
    }

    body.dark-theme .view-latest-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #1a1a1a;
        color: #00d34a;
        border: 1px solid #333;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    body.dark-theme .view-latest-btn:hover {
        background: #00d34a;
        color: #101010;
        border-color: #00d34a;
    }

    body.dark-theme .view-latest-btn i {
        color: inherit;
    }
    </style>
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
                                <p class="schedule-text">You have successfully uploaded <span class="count-number"><?php echo $scheduleCount; ?></span> teaching schedule<?php echo $scheduleCount !== 1 ? 's' : ''; ?>.</p>
                                <?php if ($latestSchedule): ?>
                                    <div class="latest-upload-container">
                                        <p>Latest upload: <?php echo $latestSchedule['upload_date']; ?></p>
                                        <a href="<?php echo htmlspecialchars($latestSchedule['file_path']); ?>" target="_blank" class="view-latest-btn">
                                            <i class="fas fa-eye"></i> View Latest
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="schedule-text">No teaching schedules uploaded yet.</p>
                                <p class="schedule-text">Upload your teaching schedule to get started.</p>
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
                        <a href="help.php" class="action-btn">
                            <i class="fas fa-question-circle"></i>
                            <span>Get Help</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
                                
    <script>
        function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = '../landing/index.php';
        }
        }
    </script>
    <script src="../scripts.js"></script>
</body>
</html>