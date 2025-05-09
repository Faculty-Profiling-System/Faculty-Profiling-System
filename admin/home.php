<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css" />
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

    /* Stats Grid Styles */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 1rem;
      padding: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: #ffffff;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
      transition: all 0.3s ease;
    }

    .stat-card h3 {
      margin: 0 0 1rem 0;
      font-size: 1.1em;
      font-weight: bold;
    }

    .stat-value {
      font-size: 2em;
      font-weight: bold;
    }

            /* Dropdown Styles */
        nav ul li.dropdown {
      position: relative;
    }
    
    nav ul li.dropdown .dropdown-menu {
      display: none;
      position: relative;
      left: 0;
      background-color: #015f22;
      min-width: 200px;
      z-index: 1000;
      border: 1px solid #024117;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
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
      background-color: #04b032;
    }

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
          <li><a href="department.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">DEPARTMENT MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS</a>
            <ul class="dropdown-menu">
              <li><a href="files_report.php">Files</a></li>
              <li><a href="logs_report.php">Logs</a></li>
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

  <div class="dashboard-container">
    <header>
      <h1>Admin Home</h1>
      <div class="welcome-message">Welcome, <span id="adminName"></span>!</div>
    </header>

    <div class="stats-grid" id="statsContainer">
      <!-- Stats will be populated by JavaScript -->
    </div>

    <h2 class="section-title">Recent Credential & Accreditation Status</h2>


      <div class="admin-credentials-card">
        <h3><img src="../images/pendingdoc.png" alt="pendingdoc-icon" class="preview-icon">Pending Document Verifications</h3>
        <ul class="credentials-list" id="pendingDocumentsList">
          <!-- Will be populated by JavaScript -->
        </ul>
        <a href="pending_documents.php" class="view-link">View Pending Documents</a>
      </div>

      <div class="admin-credentials-card">
        <h3><img src="../images/accredit.png" alt="accredit-icon" class="preview-icon">Under Review for Accreditation</h3>
        <ul class="credentials-list" id="accreditationReviewList">
          <!-- Will be populated by JavaScript -->
        </ul>
        <a href="accreditation_review.php" class="view-link">View Review for Accreditation</a>
      </div>
    </div>
  </div>
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
  </script>
    <script src="scripts.js"></script>
</body>
</html> 