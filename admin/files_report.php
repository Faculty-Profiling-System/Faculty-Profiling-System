<?php
require_once '../db_connection.php';
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../landing/index.php");
    exit();
}

$query = "SELECT * FROM vw_credentials_report;";

$result = $conn->query($query);

// Check for query errors
if (!$result) {
    die("Database query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Credentials Report | PLP</title>
  <link rel="stylesheet" href="../css/admin_style.css" />
<!-- Change this in files_report.php -->
<link rel="stylesheet" href="../css/report.css?v=<?php echo time(); ?>">
<style>
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
          <li><a href="department.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">DEPARTMENT MANAGEMENT</a></li>
          <li><a href="user.php"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
          <li class="dropdown">
            <a href="javascript:void(0)" id="reportsDropdown" class="active"><img src="../images/reports.png" alt="Reports Icon" class="menu-icon">REPORTS</a>
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
      <h1>Credentials Pending Verification Report</h1>
      <h3>List of Pending Faculty Credentials for Verification</h3>
    </div>

    <div class="report-container">
      
      <div class="report-controls">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search Faculty...">
          <button onclick="searchFaculty()">Search</button>
        </div>
        <div class="filter-box">
          <select id="collegeFilter">
            <option value="">All Colleges</option>
            <?php
            $collegesQuery = "SELECT college_name FROM colleges";
            $collegesResult = $conn->query($collegesQuery);
            while ($college = $collegesResult->fetch_assoc()) {
              echo '<option value="'.htmlspecialchars($college['college_name']).'">'.htmlspecialchars($college['college_name']).'</option>';
            }
            ?>
          </select>
          <select id="typeFilter">
            <option value="">All Types</option>
            <option value="Diploma">Diploma</option>
            <option value="Certificate">Certificate</option>
            <option value="Professional License">Professional License</option>
            <option value="Training Certificate">Training Certificate</option>
            <option value="Award">Award</option>
            <option value="Other">Other</option>
          </select>
          <button onclick="applyFilters()">Filter</button>
        </div>
      </div>
      
      <table class="report-table">
        <thead>
          <tr>
            <th>FACULTY ID</th>
            <th>NAME</th>
            <th>COLLEGE</th>
            <th>CREDENTIAL TYPE</th>
            <th>CREDENTIAL NAME</th>
            <th>FILE</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['faculty_id']) ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['college_name']) ?></td>
            <td><?= htmlspecialchars($row['credential_type'] ?? 'Not specified') ?></td>
            <td><?= htmlspecialchars($row['credential_name'] ?? 'Not specified') ?></td>
            <td>
              <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" class="view-file">VIEW FILE</a>
            </td>
            <td class="actions">
              <button onclick="approveCredential('<?= $row['credential_id'] ?>')">Approve</button>
              <button onclick="rejectCredential('<?= $row['credential_id'] ?>')">Reject</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="report.js"></script>
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
</body>
</html>