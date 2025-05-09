<?php
require_once '../db_connection.php';

// Handle filtering
$collegeFilter = $_GET['college'] ?? '';

// Fetch distinct colleges for filter dropdown
$collegeQuery = "SELECT DISTINCT college FROM vw_faculty_users ORDER BY college";
$collegeResult = $conn->query($collegeQuery);

// Fetch users (with optional college filter)
$sql = "SELECT * FROM vw_faculty_users";
if (!empty($collegeFilter)) {
    $stmt = $conn->prepare("SELECT * FROM vw_faculty_users WHERE college = ?");
    $stmt->bind_param("s", $collegeFilter);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    $users = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/user.css?v=<?php echo time(); ?>" />
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

    <div class="navigation" id="menu">
      <div class="navigation-header">
        <h1>ADMINISTRATOR</h1>
        <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
      </div>

    <nav>
        <ul>
          <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
          <li><a href="department.php"><img src="../images/department.png" alt="Department Icon" class="menu-icon">DEPARTMENT MANAGEMENT</a></li>
          <li><a href="user.php"class="active"><img src="../images/user.png" alt="User Icon" class="menu-icon">USER MANAGEMENT</a></li>
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
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" class="menu-icon">LOGOUT</a>
      </div>
    </div>
  </div>

  <div id="main" class="main-content">
      <div class="user-management-container">
          <h1>User Management</h1>

          <div class="filter-section">
              <form method="GET" action="user.php" class="filter-form">
                  <label for="college">Filter by College:</label>
                  <select name="college" id="college" onchange="this.form.submit()">
                      <option value="">-- All Colleges --</option>
                      <?php while ($row = $collegeResult->fetch_assoc()): ?>
                          <option value="<?= htmlspecialchars($row['college']) ?>" 
                              <?= $collegeFilter == $row['college'] ? 'selected' : '' ?>>
                              <?= htmlspecialchars($row['college']) ?>
                          </option>
                      <?php endwhile; ?>
                  </select>
              </form>
              
              <button class="add-user-btn" onclick="openAddModal()">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M12 5v14M5 12h14"></path>
                  </svg>
                  Add User
              </button>
          </div>

          <div class="table-container">
              <table class="user-table">
                  <thead>
                      <tr>
                          <th>Faculty ID</th>
                          <th>College</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Username</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php while ($user = $users->fetch_assoc()): ?>
                          <tr>
                              <td><?= htmlspecialchars($user['faculty_id']) ?></td>
                              <td><?= htmlspecialchars($user['college']) ?></td>
                              <td><?= htmlspecialchars($user['name']) ?></td>
                              <td><?= htmlspecialchars($user['email_address']) ?></td>
                              <td><?= htmlspecialchars($user['username']) ?></td>
                              <td>
                                  <button class="action-btn edit-btn" onclick="openEditModal(
                                      '<?= htmlspecialchars($user['faculty_id']) ?>',
                                      '<?= htmlspecialchars($user['college']) ?>',
                                      '<?= htmlspecialchars($user['name']) ?>',
                                      '<?= htmlspecialchars($user['email_address']) ?>',
                                      '<?= htmlspecialchars($user['username']) ?>'
                                  )">
                                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                      </svg>
                                      Edit
                                  </button>
                                  <button class="action-btn delete-btn" onclick="confirmDelete('<?= htmlspecialchars($user['faculty_id']) ?>')">
                                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                          <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                      </svg>
                                      Delete
                                  </button>
                              </td>
                          </tr>
                      <?php endwhile; ?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>

  <!-- ADD NEW FACULTY USER MODAL -->
  <div id="addModal" class="modal">
      <div class="modal-content">
          <h2>Add New Faculty User</h2>
          <form id="addUserForm" method="POST">
              <!-- Faculty Table Fields -->
              <input type="text" name="faculty_id" placeholder="Faculty ID" required>
              <select name="college_id" required>
                  <option value="">Select College</option>
                  <?php
                  $colleges = $conn->query("SELECT college_id, college_name FROM colleges");
                  while ($c = $colleges->fetch_assoc()):
                  ?>
                      <option value="<?= $c['college_id'] ?>"><?= htmlspecialchars($c['college_name']) ?></option>
                  <?php endwhile; ?>
              </select>
              <input type="text" name="full_name" placeholder="Full Name" required>
              <input type="date" name="birthday" placeholder="Birthday" required>
              
              <select name="gender" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
              </select>

              <input type="email" name="email" placeholder="Email" required>

              <select name="employment_type" required>
                  <option value="">Employment Type</option>
                  <option value="Full-Time">Full-Time</option>
                  <option value="Part-Time">Part-Time</option>
              </select>

              <input type="text" name="contact_number" placeholder="Contact Number">
              <input type="text" name="address" placeholder="Address">
              
              <!-- Users Table Fields -->
              <input type="text" name="username" placeholder="Username" required>
              
              <div class="password-container">
                  <input type="password" name="password" id="password" placeholder="Password" required>
                  <span class="toggle-password" onclick="togglePassword('password')">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                  </span>
              </div>
              
              <div class="password-container">
                  <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                  <span class="toggle-password" onclick="togglePassword('confirm_password')">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                  </span>
              </div>
              <small id="password-match-message" style="color: #dc3545; display: none;">Passwords do not match!</small>

              <select name="role" required>
                  <option value="">Select Role</option>
                  <option value="Admin">Admin</option>
                  <option value="Faculty">Faculty</option>
                  <option value="Head">Head</option>
              </select>

              <div class="modal-buttons">
                  <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                  <button type="submit" class="submit-btn">Add User</button>
              </div>
          </form>
      </div>
  </div>

  <!-- EDIT USER MODAL -->
  <div id="editModal" class="modal">
      <div class="modal-content">
          <h2>Edit User</h2>
          <form id="editUserForm" method="POST">
              <input type="text" name="faculty_id" id="edit_faculty_id" readonly>
              <select name="college_id" required>
                  <option value="">Select College</option>
                  <?php
                  $colleges = $conn->query("SELECT college_id, college_name FROM colleges");
                  while ($c = $colleges->fetch_assoc()):
                      $selected = ($c['college_id'] == $current_college_id) ? 'selected' : '';
                  ?>
                      <option value="<?= $c['college_id'] ?>" <?= $selected ?>><?= htmlspecialchars($c['college_name']) ?></option>
                  <?php endwhile; ?>
              </select>
              <input type="text" name="name" id="edit_name" required>
              <input type="email" name="email_address" id="edit_email" required>
              <input type="text" name="username" id="edit_username">
              <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
              
              <div class="modal-buttons">
                  <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                  <button type="submit" class="submit-btn">Update User</button>
              </div>
          </form>
      </div>
  </div>

  <form id="deleteForm" method="POST" style="display:none;">
      <input type="hidden" name="faculty_id" id="delete_faculty_id">
  </form>

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
  <script src="users.js"></script>
  <?php if (isset($conn)) $conn->close(); ?>
</body>
</html>