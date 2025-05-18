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
            <a href="javascript:void(0)" id="reportsDropdown">
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
                    <a href="logs_report.php">
                        <i class="fas fa-user-clock"></i> USER LOGS
                    </a>
                </li>
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
        <div class="management-tabs">
            <button class="tab-btn active" onclick="switchTab('facultyTab')">List of Faculty</button>
            <button class="tab-btn" id="credentialsTabBtn" onclick="switchTab('credentialsTab')" style="display:none;">Approved Credentials</button>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search faculty...">
            </div>
        </div>
        
        <div id="facultyTab" class="tab-content active">
            <div class="table-controls">
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
        
        <div id="credentialsTab" class="tab-content">
            <div class="credentials-header">
                <h2 id="credentialsTableTitle">Credentials for Faculty Member</h2>
            </div>
            
                <div id="credentialsList"></div>
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

    // Tab switching function
    function switchTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Deactivate all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activate the selected tab
        document.getElementById(tabId).classList.add('active');
        document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`).classList.add('active');
        
        // Special handling for credentials tab
        if (tabId === 'facultyTab') {
            document.getElementById('credentialsTabBtn').style.display = 'none';
        }
    }

  function showFacultyCredentials(facultyId, facultyName) {
    // Update the title
    document.getElementById('credentialsTableTitle').textContent = `Credentials for ${facultyName}`;
    
    // Show loading state
    document.getElementById('credentialsList').innerHTML = `
        <div class="loading-message">Loading credentials...</div>
    `;
    
    // Show the credentials tab button
    document.getElementById('credentialsTabBtn').style.display = 'inline-block';
    
    // Switch to the credentials tab
    switchTab('credentialsTab');
    
    // Fetch credentials via AJAX
    fetch(`get_faculty_credentials.php?faculty_id=${facultyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.credentials.length > 0) {
                    let html = `
                        <div class="credentials-table-container">
                            <table class="credentials-table">
                                <thead>
                                    <tr>
                                        <th>Credential Name</th>
                                        <th>Credential Type</th>
                                        <th>Issue Date</th>
                                        <th>Expiry Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.credentials.forEach(credential => {
                        const issueDate = credential.issued_date ? 
                            new Date(credential.issued_date).toLocaleDateString() : 'N/A';
                        const expiryDate = credential.expiry_date ? 
                            new Date(credential.expiry_date).toLocaleDateString() : 'No expiration';
                        
                        html += `
                            <tr>
                                <td>${credential.credential_name || 'N/A'}</td>
                                <td>${credential.credential_type || 'N/A'}</td>
                                <td>${issueDate}</td>
                                <td>${expiryDate}</td>
                                <td>
                                    <div class="credential-actions">
                                        <button class="btn btn-sm btn-view" onclick="window.open('${credential.file_path}', '_blank')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <a href="${credential.file_path}" download class="btn btn-sm btn-download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    document.getElementById('credentialsList').innerHTML = html;
                } else {
                    document.getElementById('credentialsList').innerHTML = `
                        <div class="no-credentials">No credentials found for this faculty member</div>
                    `;
                }
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('credentialsList').innerHTML = `
                <div class="error-message">Error loading credentials</div>
            `;
        });
}

    // Modify the faculty table rows to be clickable
    document.addEventListener('DOMContentLoaded', function() {
        const facultyRows = document.querySelectorAll('#facultyTable tbody tr');
        facultyRows.forEach(row => {
            const facultyId = row.cells[0].textContent;
            const facultyName = row.cells[1].textContent;
            
            row.style.cursor = 'pointer';
            row.addEventListener('click', (e) => {
                // Don't trigger if clicking on action buttons
                if (!e.target.closest('.action-btn')) {
                    showFacultyCredentials(facultyId, facultyName);
                }
            });
        });
    });

  </script>
  <script src="../faculty/help.js?v=<?php echo time(); ?>"></script>
</body>
</html>