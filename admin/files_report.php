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

// Fetch credentials from the same college
$sql = "SELECT * FROM vw_credentials_report WHERE college_name = ? ORDER BY full_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_college_name);
$stmt->execute();
$credentials_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Credentials Report | PLP</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>"/>
  <link rel="stylesheet" href="../css/report.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
                    /* Dropdown Styles */
        nav ul li.dropdown {
      position: relative;
    }
    
    nav ul li.dropdown .dropdown-menu {
      display: none;
      position: relative;
      left: 0;
      min-width: 200px;
      z-index: 1000;
      padding: 0;
      margin: 0;
    }
    
    nav ul li.dropdown .dropdown-menu li {
      padding: 0;
      list-style: none;
    }
    
    nav ul li.dropdown .dropdown-menu a {
      color: white;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      font-size: 14px;
      font-family: 'Trebuchet MS';
    }
    
    nav ul li.dropdown .dropdown-menu a:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    border-right: 3px solid #04b032; /* Color accent */
    border-left: 3px solid #04b032; /* Color accent */
    margin-right: 15px;
    padding-top: 10px;
    padding-bottom: 10px;
    background-color: #0e4301;
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
        <h1>ADMINISTRATOR</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>

    <nav>
        <ul>
          <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
          <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown" class="active"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS<img src="../images/dropdown.png" alt="Dropdown Icon" class="down-icon"></a>
            <ul class="dropdown-menu">
              <li><a href="files_report.php" class="active">CREDENTIAL FILES</a></li>
              <li><a href="logs_report.php">USER LOGS</a></li>
            </ul>
          </li>
          <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
        </ul>
    </nav>

    <div class="logout-section">
      <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
    </div>
  </div>

  <div id="main" class="main-content">
    <div class="report-header">
      <h1>Credentials Pending Verification Report - <?= htmlspecialchars($current_college_name) ?></h1>
      <h3>List of Pending Faculty Credentials for Verification</h3>
    </div>

    <div class="report-container">
      <div class="report-controls">
        <div class="filter-box">
          <select id="typeFilter" onchange="applyFilters()">
            <option value="">All Types</option>
            <option value="Diploma">Diploma</option>
            <option value="Certificate">Certificate</option>
            <option value="Professional License">Professional License</option>
            <option value="Training Certificate">Training Certificate</option>
            <option value="Award">Award</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>
      
      <table class="report-table">
        <thead>
          <tr>
            <th>FACULTY ID</th>
            <th>NAME</th>
            <th>CREDENTIAL TYPE</th>
            <th>CREDENTIAL NAME</th>
            <th>FILE</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $credentials_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['faculty_id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['credential_type']) ?></td>
            <td><?= htmlspecialchars($row['credential_name']) ?></td>
            <td>
              <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="view-file">VIEW FILE</a>
            </td>
            <td class="actions">
              <button onclick="approveCredential('<?= $row['credential_id'] ?>')" class="approve-btn">Approve</button>
              <button onclick="rejectCredential('<?= $row['credential_id'] ?>')" class="reject-btn">Reject</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
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

  <script src="report.js?v=<?php echo time(); ?>"></script>
  <script src="scripts.js"></script>
  <script>
      // Reports dropdown functionality
      document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('reportsDropdown').addEventListener('click', function(e) {
      e.preventDefault();
      const dropdown = this.parentElement;
      const menu = dropdown.querySelector('.dropdown-menu');
      
      // Toggle only the clicked dropdown
      if (menu.style.display === 'block') {
        menu.style.display = 'none';
      } else {
        // Close all other dropdowns first
        document.querySelectorAll('.dropdown-menu').forEach(item => {
          if (item !== menu) {
            item.style.display = 'none';
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
        });
      }
    });
        })
    
    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
    }
  </script>
  <script src="../faculty/help.js"></script>
</body>
</html>