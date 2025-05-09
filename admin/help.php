<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Help</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
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
        <li><a href="department.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
        <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
        <li><a href="faculty.php"><img src="../images/faculty.png" alt="Faculty Icon" class="menu-icon">FACULTY</a></li>
        <li><a href="faculty_management.php"><img src="../images/addfaculty.png" alt="Add Faculty Icon" class="menu-icon">FACULTY MANAGEMENT</a></li>
        <li><a href="reports.php" class="active"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS</a></li>
        <li><a href="help.php"><img src="../images/help.png" alt="Help Icon" class="menu-icon">HELP</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    
    <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
  </div>

  <div id="main" class="main-content">
  </div>
  
  <script>
    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        // If user confirms, redirect to logout page
        window.location.href = '../landing/index.php';
      }
      // If user cancels, do nothing
    }
  </script>

  <script src="scripts.js"></script>

</body>
</html>