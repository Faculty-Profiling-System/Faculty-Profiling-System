<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>College Management | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/department.css?v=<?php echo time(); ?>" />
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
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      
      const savedTextSize = localStorage.getItem('plpTextSize') || '100';
      document.querySelector('html').style.fontSize = savedTextSize + '%';
    });
  </script>
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
        <li><a href="department.php" class="active"><img src="../images/department.png" alt="Department Icon" class="menu-icon">DEPARTMENT MANAGEMENT</a></li>
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

  <div class="college-management-container">
    <h1>Admin College Management</h1>
    <button class="add-college-btn" onclick="showAddCollegeModal()">+ Add New College</button>
  
    <div class="college-table-container">
      <h2>List of Colleges</h2>
      <table id="collegesTable">
        <thead>
          <tr>
            <th>COLLEGE ID</th>
            <th>COLLEGE NAME</th>
            <th> </th>
          </tr>
        </thead>
        <tbody>
          <?php
          require_once '../db_connection.php';
          $sql = "SELECT * FROM colleges ORDER BY college_id";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '<tr>';
                  echo '<td>' . $row['college_id'] . '</td>';
                  echo '<td>' . htmlspecialchars($row['college_name']) . '</td>';
                  echo '<td class="actions">';
                  echo '<button class="edit-btn" onclick="editCollege(' . $row['college_id'] . ', \'' . htmlspecialchars($row['college_name']) . '\')">EDIT</button>';
                  echo '<button class="delete-btn" onclick="deleteCollege(' . $row['college_id'] . ')">DELETE</button>';
                  echo '</td>';
                  echo '</tr>';
              }
          } else {
              echo '<tr><td colspan="3">No colleges found</td></tr>';
          }
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add College Modal -->
  <div id="addCollegeModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="hideAddCollegeModal()">&times;</span>
      <h2>Add New College</h2>
      <div class="form-section">
        <label>College Name</label>
        <input type="text" id="collegeName" placeholder="Enter College Name">
      </div>
      <button class="submit-btn" onclick="addCollege()">ADD COLLEGE</button>
    </div>
  </div>

  <!-- Edit College Modal -->
  <div id="editCollegeModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="hideEditCollegeModal()">&times;</span>
      <h2>Edit College</h2>
      <div class="form-section">
        <label>College Name:</label>
        <input type="text" id="editCollegeName" placeholder="Enter College Name:">
        <input type="hidden" id="editCollegeId">
      </div>
      <button class="submit-btn" onclick="updateCollege()">UPDATE COLLEGE</button>
    </div>
  </div>

  <script src="scripts.js"></script>
  <script src="department.js"></script>
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
    });

    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
    }
  </script>
</body>
</html>