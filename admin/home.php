<?php
session_start();
require_once '../db_connection.php'; // Your database connection file

// Get admin info
$adminName = "ADMIN";
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ? and role = 'Admin'");
    $stmt->bind_param("i", $_SESSION['user_id']); // "i" means integer
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $adminName = strtoupper(str_replace('_', ' ', $user['username']));
}

// Get college_id of the logged in admin
$college_id = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT college_id FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']); // "i" for integer
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $college_id = $user['college_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
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
      const html = document.querySelector('html');
      html.style.fontSize = savedTextSize + '%';
    });
  </script>
  <style>
    /* Base font sizes for elements to scale properly */
    body {
      background: #f4fff4;
      color: #187436;
    }

    .dashboard-container h1 { font-size: 2em; }
    .dashboard-container h2 { font-size: 1.5em; }
    .dashboard-container h3 { font-size: 1.2em; }
    .welcome-message { font-size: 1.2em; }
    .admin-credentials-card { font-size: 1em; }
    .section-title { font-size: 1.5em; }
    .view-link { font-size: 0.9em; }


    /* Dark theme */
    body.dark-theme {
      background: #101010 !important;
      color: #f3f3f3 !important;
    }

    body.dark-theme .stat-card {
      background: #1a1a1a !important;
      border: 1px solid #333;
    }

    body.dark-theme .stat-card h3 {
      color: #00ff4c !important;
      text-shadow: 0 0 10px rgba(0, 255, 76, 0.3);
    }

    body.dark-theme .stat-value {
      color: #ffffff !important;
    }

    body.dark-theme .admin-credentials-card {
      background: #222;
      color: #f3f3f3;
    }

    body.dark-theme .admin-credentials-card h3 {
      color: #f3f3f3;
    }

    body.dark-theme .view-link {
      color: #00d34a;
    }

    body.dark-theme .welcome-message,
    body.dark-theme .section-title {
      color: #f3f3f3;
    }

    /* Light theme styles */
    .stat-card {
      background: #ffffff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .stat-card h3 {
      color: #187436;
    }

    .stat-value {
      color: #187436;
    }

    .admin-credentials-card {
      background: #ffffff;
      border-radius: 8px;
      padding: 20px;
      margin: 10px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: all 0.3s ease;
    }

    .view-link {
      color: #187436;
      text-decoration: none;
      font-weight: bold;
    }

    .view-link:hover {
      text-decoration: underline;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      }
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
    
    <div class="navigation" id="menu">
      <div class="navigation-header">
          <h1>ADMINISTRATOR</h1>
        <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
      </div>
    
      <nav>
        <ul>
          <li><a href="home.php" class="active"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
          <li><a href="college_management.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS<img src="../images/dropdown.png" alt="Dropdown Icon" class="down-icon"></a>
            <ul class="dropdown-menu">
              <li><a href="files_report.php">CREDENTIAL FILES</a></li>
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
  </div>

  <div class="admin-dashboard-container">
    <header>
      <h1>Admin Home</h1>
      <div class="welcome-message">WELCOME BACK, <span id="adminName"><?php echo $adminName; ?></span>!</div>
    </header>

    <div class="stats-grid" id="statsContainer">
      <?php
// Get faculty stats for the admin's college
$stats = [
    'total_faculty' => 0,
    'total_female' => 0,
    'total_male' => 0,
    'full_time' => 0,
    'part_time' => 0,
    'masters_degrees' => 0,
    'doctoral_degrees' => 0
];

if ($college_id) {
    // Total faculty
    $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ?");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $stats['total_faculty'] = $row[0];

    // Gender counts
    $stmt = $conn->prepare("SELECT p.gender, COUNT(*) 
                       FROM faculty f 
                       JOIN faculty_personal_info p ON f.faculty_id = p.faculty_id 
                       WHERE f.college_id = ? 
                       GROUP BY p.gender");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['gender'] === 'Female') $stats['total_female'] = $row['COUNT(*)'];
        if ($row['gender'] === 'Male') $stats['total_male'] = $row['COUNT(*)'];
    }

    // Employment type
    $stmt = $conn->prepare("SELECT employment_type, COUNT(*) FROM faculty WHERE college_id = ? GROUP BY employment_type");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['employment_type'] === 'Full-Time') $stats['full_time'] = $row['COUNT(*)'];
        if ($row['employment_type'] === 'Part-Time') $stats['part_time'] = $row['COUNT(*)'];
    }

    // Specialization
    $stmt = $conn->prepare("SELECT specialization, COUNT(*) FROM faculty WHERE college_id = ? GROUP BY specialization");
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['specialization'] === 'Master') $stats['masters_degrees'] = $row['COUNT(*)'];
        if ($row['specialization'] === 'Doctorate') $stats['doctoral_degrees'] = $row['COUNT(*)'];
    }
}
      ?>
      <div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/totalfaculty.png" alt="Faculty" class="stat-icon">
      <h3>Total Faculty Members</h3>
    </div>
    <div class="value"><?php echo $stats['total_faculty']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/female.png" alt="Female" class="stat-icon">
      <h3>Total Female</h3> 
    </div>
    <div class="value"><?php echo $stats['total_female']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/male.png" alt="Male" class="stat-icon">
      <h3>Total Male</h3>
    </div>
    <div class="value"><?php echo $stats['total_male']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/emptype.png" alt="Full-Time" class="stat-icon">
      <h3>Full-Time</h3>
    </div>
    <div class="value"><?php echo $stats['full_time']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/emptype.png" alt="Part-Time" class="stat-icon">
      <h3>Part-Time</h3>
    </div>
    <div class="value"><?php echo $stats['part_time']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/degree.png" alt="Master's" class="stat-icon">
      <h3>Master's Degree Holder</h3>
    </div>
    <div class="value"><?php echo $stats['masters_degrees']; ?></div>
  </div>
  
  <div class="stat-card">
    <div class="stat-header">
      <img src="../images/degree.png" alt="Doctoral" class="stat-icon">
      <h3>Doctoral Degree Holder</h3>
    </div>
    <div class="value"><?php echo $stats['doctoral_degrees']; ?></div>
  </div>
</div>

    <h2 class="section-title">Pending Credentials and Recent Faculty Status</h2>

    <div class="credentials-section">
      <div class="admin-credentials-card">
        <h3><img src="../images/pendingdoc.png" alt="pendingdoc-icon" class="preview-icon">Pending Credentials for Verification</h3>
        <ul class="credentials-list">
          <?php
          // Get pending credentials count
          $pendingCredentials = 0;
          if ($college_id) {
              $stmt = $conn->prepare("SELECT COUNT(*) FROM credentials c 
                                    JOIN faculty f ON c.faculty_id = f.faculty_id 
                                    WHERE f.college_id = ? AND c.status = 'Pending'");
              $stmt->bind_param("i", $college_id);
              $stmt->execute();
              $result = $stmt->get_result();
              $row = $result->fetch_row();
              $pendingCredentials = $row[0];
          }
          ?>
          <li>Total Pending: <?php echo $pendingCredentials; ?></li>
          <li> </li>
          <li> </li>
        </ul>
        <a href="files_report.php" class="view-link">View All Pending Documents</a>
      </div>

      <div class="admin-credentials-card">
        <h3><img src="../images/status.png" alt="status-icon" class="preview-icon">Faculty Status</h3>
        <ul class="credentials-list">
          <?php
            // Get active/inactive faculty counts - MODIFIED TO USE STATUS FIELD
            $activeFaculty = 0;
            $inactiveFaculty = 0;
            if ($college_id) {
                // Count active faculty
                $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ? AND status = 'Active'");
                $stmt->bind_param("i", $college_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_row();
                $activeFaculty = $row[0];
                
                // Count inactive faculty
                $stmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE college_id = ? AND status = 'Inactive'");
                $stmt->bind_param("i", $college_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_row();
                $inactiveFaculty = $row[0];
            }
          ?>
          <li>Currently Active Faculty: <?php echo $activeFaculty; ?></li>
          <li>Currently Inactive Faculty: <?php echo $inactiveFaculty; ?></li>
        </ul>
        <a href="college_management.php" class="view-link">View All Faculty Status</a>
      </div>
    </div>
  </div>

    
  <?php include '../faculty/help.php'; ?>

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
    
    //log-out
        function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
    }

// ====== MENU TOGGLE ==========
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
    <script src="../faculty/help.js?v=<?php echo time(); ?>"></script>
    <script src="scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html> 