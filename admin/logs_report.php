<?php
require_once '../db_connection.php';
session_start();

// Check if user is logged in and has a college_id
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing/index.php");
    exit();
}

// Get the logged-in user's college_id
$current_user_id = $_SESSION['user_id'];
$user_query = "SELECT college_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$current_user = $user_result->fetch_assoc();

if (!$current_user || !isset($current_user['college_id'])) {
    die("Error: Unable to determine user's college");
}

$current_college_id = $current_user['college_id'];

// Fetch the college name for the current user
$college_query = "SELECT college_name FROM colleges WHERE college_id = ?";
$stmt = $conn->prepare($college_query);
$stmt->bind_param("i", $current_college_id);
$stmt->execute();
$college_result = $stmt->get_result();
$college_row = $college_result->fetch_assoc();
$current_college_name = $college_row['college_name'];

// Modified query to better handle login/logout pairs
$login_logs_query = "SELECT 
                        u.faculty_id, 
                        f.full_name AS name, 
                        l.login_time AS login_time,
                        l.logout_time AS logout_time,
                        CASE 
                            WHEN l.session_status = 'active' THEN 'LOG IN (Active)'
                            WHEN l.session_status = 'completed' THEN 'LOG IN'
                            WHEN l.session_status = 'timeout' THEN 'LOG IN (Timeout)'
                            ELSE 'LOG IN'
                        END AS login_action,
                        CASE 
                            WHEN l.session_status = 'completed' THEN 'LOG OUT'
                            WHEN l.session_status = 'timeout' THEN 'TIMEOUT'
                            ELSE NULL
                        END AS logout_action,
                        TIMESTAMPDIFF(MINUTE, l.login_time, IFNULL(l.logout_time, NOW())) AS session_duration,
                        l.ip_address,
                        l.session_status
                    FROM user_logins l
                    JOIN users u ON l.user_id = u.user_id
                    JOIN faculty f ON u.faculty_id = f.faculty_id
                    WHERE f.college_id = ?
                    ORDER BY l.login_time DESC";

$stmt = $conn->prepare($login_logs_query);
$stmt->bind_param("i", $current_college_id);
$stmt->execute();
$login_logs_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Activity Logs | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>"/>
  <link rel="stylesheet" href="../css/report.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="report-page">

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
        <h1>ADMINISTRATOR</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>

    <nav>
        <ul>
          <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
          <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown" class="active">
                <img src="../images/reports.png" alt="Reports Icon" class="menu-icon">
                REPORTS
                <i class="fas fa-chevron-down down-icon" id="dropdownArrow"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="files_report.php">
                        <i class="fas fa-file-alt"></i> CREDENTIAL FILES
                    </a>
                </li>
                <li>
                    <a href="logs_report.php" class="active">
                        <i class="fas fa-user-clock"></i> USER LOGS
                    </a>
                </li>
            </ul>
          </li>
          <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
        </ul>
    </nav>

        <div class="logout-section">
        <a href="javascript:void(0)" onclick="confirmLogout()">
            <img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT
        </a>
        </div>
  </div>

  <div id="main" class="main-content">
    <div class="report-header">
      <h1>User Activity Logs - <?= htmlspecialchars($current_college_name) ?></h1>
    </div>

<div class="report-container">
  <div class="log-tabs">
    <div class="log-tab active" onclick="switchTab('login-logs')">Login/Logout Logs</div>
    <div class="log-tab" onclick="switchTab('credential-logs')">Credentials Logs</div>
    <div class="search-box log-search-box">
      <i class="fas fa-search"></i>
      <input type="text" id="searchInput" placeholder="Search faculty...">
    </div>
  </div>
      
      <!-- Login/Logout Logs -->
      <div id="login-logs" class="log-content active">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Faculty ID</th>
                    <th>Name</th>
                    <th>Lgin Time</th>
                    <th>Logtime Time</th>
                    <th>Action</th>
                    <th>Session Duration (Minutes)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $login_logs_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['faculty_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y h:iA', strtotime($row['login_time']))) ?></td>
                    <td>
                        <?= $row['logout_time'] ? 
                            htmlspecialchars(date('d/m/Y h:iA', strtotime($row['logout_time']))) : 
                            'Still logged in' ?>
                    </td>
                    <td>
                        <?= $row['logout_time'] ? 'LOG OUT' : $row['login_action'] ?>
                    </td>
                    <td class="session-duration">
                        <?= htmlspecialchars($row['session_duration']) ?>
                    </td>
                    <td>
                      <span class="session-status-badge 
                        <?php
                          if ($row['session_status'] === 'active') echo 'session-status-active';
                          elseif ($row['session_status'] === 'completed') echo 'session-status-completed';
                          elseif ($row['session_status'] === 'timeout') echo 'session-status-timeout';
                        ?>">
                        <?= htmlspecialchars(strtoupper($row['session_status'])) ?>
                      </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
      </div>

      <!-- Credentials Status Logs (Placeholder) -->
      <div id="credential-logs" class="log-content">
        <div class="coming-soon">
          <p>Credential Logs feature is coming soon!</p>
          <p>This section will display detailed records of credentials status logs.</p>
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
          <div class="faq-question">Q: How do I filter the logs by date?</div>
          <p>A: Currently, logs are displayed in chronological order. Date filtering will be added in a future update.</p>
      </div>
      <div class="faq-item">
          <div class="faq-question">Q: Why are some session durations missing?</div>
          <p>A: Session duration is only calculated for login events when the user has logged out. Active sessions show the current duration.</p>
      </div>
      <div class="faq-item">
          <div class="faq-question">Q: Can I export these logs to a file?</div>
          <p>A: Export functionality will be available in the next system update.</p>
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

  <script src="report.js?v=<?php echo time(); ?>"></script>
  <script src="scripts.js?v=<?php echo time(); ?>"></script>
  <script>
    // Reports dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('reportsDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        const dropdown = this.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        dropdown.classList.toggle('open');
        // Toggle menu display
        if (menu.style.display === 'block') {
          menu.style.display = 'none';
        } else {
          // Close all other dropdowns first
          document.querySelectorAll('.dropdown-menu').forEach(item => {
            if (item !== menu) {
              item.style.display = 'none';
              item.closest('.dropdown').classList.remove('open');
            }
          });
          menu.style.display = 'block';
        }
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
          document.querySelectorAll('.dropdown-menu').forEach(item => {
            item.style.display = 'none';
            item.closest('.dropdown').classList.remove('open');
          });
        }
      });
    });
    
function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        // Show loading indicator
        document.body.style.cursor = 'wait';
        
        // Create a hidden iframe to ensure logout completes
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '../admin/process_logout.php';
        
        iframe.onload = function() {
            // Redirect after logout completes
            window.location.href = "../landing/index.php";
        };
        
        document.body.appendChild(iframe);
    }
}
    
    // Tab switching functionality
    function switchTab(tabId) {
      // Hide all content
      document.querySelectorAll('.log-content').forEach(content => {
        content.classList.remove('active');
      });
      
      // Deactivate all tabs
      document.querySelectorAll('.log-tab').forEach(tab => {
        tab.classList.remove('active');
      });
      
      // Activate selected tab and content
      document.getElementById(tabId).classList.add('active');
      event.currentTarget.classList.add('active');
    }
  </script>
  <script src="../faculty/help.js?v=<?php echo time(); ?>"></script>
</body>
</html>