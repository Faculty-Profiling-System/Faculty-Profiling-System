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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>College Management | Admin</title>
  <link rel="stylesheet" href="../css/admin_style.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="../css/department.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
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
        <li><a href="college_management.php" class="active"><img src="../images/department.png" alt="Department Icon" class="menu-icon">COLLEGE MANAGEMENT</a></li>
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
      
    <div class="page-header">
      <h1>College Management</h1>
    </div>
    
    <div class="faculty-management-container">
    <div class="table-controls">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search faculty...">
      </div>
    </div>
    
    <div class="faculty-table-container">
      <table id="facultyTable">
        <thead>
          <tr>
            <th>Faculty ID</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Employment Type</th>
            <th>Specialization</th>
            <th>Contact Number</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ($current_college_id) {
              $sql = "SELECT faculty_id, full_name, gender, email, employment_type, specialization, contact_number, status 
                      FROM faculty 
                      WHERE college_id = ? 
                      ORDER BY faculty_id";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("i", $current_college_id);
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $statusClass = $row['status'] === 'Active' ? 'status-active' : 'status-inactive';
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['faculty_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['full_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['employment_type']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['specialization']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['contact_number']) . '</td>';
                    echo '<td><span class="status-badge ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>';
                    echo '<td class="actions">';
                      if ($row['status'] === 'Active') {
                        echo '<button class="action-btn deactivate-btn" onclick="changeStatus(\'' . $row['faculty_id'] . '\', \'Inactive\')">
                              <i class="fas fa-times-circle"></i> Deactivate
                              </button>';
                        } else {
                            echo '<button class="action-btn activate-btn" onclick="changeStatus(\'' . $row['faculty_id'] . '\', \'Active\')">
                                  <i class="fas fa-check-circle"></i> Activate
                                  </button>';
                        }
                        echo '</td>';
                        echo '</tr>';
                }
              } else {
                    echo '<tr><td colspan="9" class="text-center">No faculty members found</td></tr>';
                }
                $stmt->close();
            } else {
                echo '<tr><td colspan="9" class="text-center">Error: College ID not found</td></tr>';
          }
            $conn->close();
          ?>
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

      // Search functionality
      document.getElementById('searchInput').addEventListener('keyup', function() {
        const input = this.value.toLowerCase();
        const rows = document.querySelectorAll('#facultyTable tbody tr');
        
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(input) ? '' : 'none';
        });
      });
    });

    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
    }

    function changeStatus(facultyId, newStatus) {
      if (confirm(`Are you sure you want to ${newStatus === 'Active' ? 'activate' : 'deactivate'} this faculty member?`)) {
        fetch('change_status.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            faculty_id: facultyId,
            status: newStatus
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(`Faculty member ${newStatus === 'Active' ? 'activated' : 'deactivated'} successfully`);
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error updating faculty status');
        });
      }
    }
  </script>
  <script src="../faculty/help.js"></script>
</body>
</html>