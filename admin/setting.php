<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Settings | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script>
    // Initialize states
    let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      // Apply saved text size
      document.body.style.fontSize = currentSize + '%';
      updateSelected();
    });

    function applyTheme(theme) {
      if (theme === 'dark') {
        document.body.classList.add('dark-theme');
      } else {
        document.body.classList.remove('dark-theme');
      }
      localStorage.setItem('plpTheme', theme);
    }

    function setTextSize(size) {
      currentSize = size;
      document.body.style.fontSize = size + '%';
      localStorage.setItem('plpTextSize', size);
      updateSelected();
    }

    function setTheme(theme) {
      applyTheme(theme);
      updateSelected();
    }

    function updateSelected() {
      // Text size buttons
      [100, 150, 200].forEach(s => {
        document.getElementById('size-' + s).classList.toggle('selected', currentSize === s);
      });
      // Theme buttons
      const currentTheme = localStorage.getItem('plpTheme') || 'light';
      document.getElementById('theme-light').classList.toggle('selected', currentTheme === 'light');
      document.getElementById('theme-dark').classList.toggle('selected', currentTheme === 'dark');
    }
  </script>
  <style>
    /* Light theme (default) */
    body {
      background: #f4fff4;
      color: #187436;
    }

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

    /* Dark theme */
    body.dark-theme {
      background: #101010 !important;
      color: #f3f3f3 !important;
    }

    body.dark-theme .settings-label {
      color: #f3f3f3;
    }

    body.dark-theme .settings-btn {
      background: #222;
      color: #ccc;
    }

    body.dark-theme .settings-btn.selected {
      background: #00d34a;
      color: #101010;
    }

    body.dark-theme hr {
      border-top: 6px solid #333;
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
            <a href="javascript:void(0)" id="reportsDropdown"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS<img src="../images/dropdown.png" alt="Dropdown Icon" class="down-icon"></a>
            <ul class="dropdown-menu">
              <li><a href="files_report.php">CREDENTIAL FILES</a></li>
              <li><a href="logs_report.php">USER LOGS</a></li>
            </ul>
          </li>
          <li><a href="setting.php" class="active"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
        </ul>
      </nav>

    <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
  </div>

  <div id="main" class="main-content">
    <h2>Admin Settings</h2>
    <hr>
    <div class="settings-section">
      <label class="settings-label">Text Size</label>
      <div class="settings-options">
        <button class="settings-btn" id="size-100" onclick="setTextSize(100)">100%</button>
        <button class="settings-btn" id="size-150" onclick="setTextSize(150)">150%</button>
        <button class="settings-btn" id="size-200" onclick="setTextSize(200)">200%</button>
      </div>
    </div>
    <hr>
    <div class="settings-section">
      <label class="settings-label">Theme</label>
      <div class="settings-options">
        <button class="settings-btn" id="theme-light" onclick="setTheme('light')">Light</button>
        <button class="settings-btn" id="theme-dark" onclick="setTheme('dark')">Dark</button>
      </div>
    </div>
    <hr>
    <style>
      body, .main-content {
        background: #101010 !important;
        color: #f3f3f3 !important;
      }
      .settings-section {
        margin: 2em 0;
      }
      .settings-label {
        display: block;
        font-size: 2em;
        font-weight: bold;
        margin-bottom: 1em;
        color: #f3f3f3;
      }
      .settings-options {
        display: flex;
        gap: 1em;
      }
      .settings-btn {
        background: #222;
        border: none;
        border-radius: 2em;
        padding: 0.7em 2em;
        font-size: 1.5em;
        font-family: inherit;
        color: #ccc;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        font-weight: 500;
      }
      .settings-btn.selected {
        background: #00d34a;
        color: #101010;
      }
      .settings-btn:focus {
        outline: 2px solid #00d34a;
      }
      hr {
        border: none;
        border-top: 6px solid #333;
        margin: 2em 0;
      }
      /* For light theme */
      body:not(.dark-theme) .main-content,
      body:not(.dark-theme) {
        background: #f4fff4 !important;
        color: #187436 !important;
      }
      body:not(.dark-theme) .settings-label {
        color: #187436;
      }
      body:not(.dark-theme) .settings-btn {
        background: #e3f3e3;
        color: #187436;
      }
      body:not(.dark-theme) .settings-btn.selected {
        background: #00723F; /* Deeper green */
        color: #ffffff;
      }
      body:not(.dark-theme) hr {
        border-top: 6px solid #187436;
      }
    </style>
  </div>

  <?php include '../faculty/help.php'; ?>

  <script src="scripts.js?v=<?php echo time(); ?>"></script>
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
  <script src="../faculty/help.js?v=<?php echo time(); ?>"></script>
</body>
</html> 