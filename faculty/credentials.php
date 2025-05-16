<?php

session_start();
require_once '../db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../landing/index.php');
    exit();
}

$faculty_id = $_SESSION['faculty_id'];
$credentials = [];
$error = null;

try {
    $stmt = $conn->prepare("SELECT * FROM credentials WHERE faculty_id = ? ORDER BY issued_date DESC");
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $credentials = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credentials | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/credentials.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="Logo">
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
        <h1>FACULTY</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>
        
    <nav>
      <ul>
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php" class="active"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
        <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
      </div>
  </div>        

    
    <div class="dashboard-container">
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h1 class="dashboard-title">
                <i class="fas fa-certificate"></i> My Credentials
            </h1>
            <h2 class="card-title">
                <i class="fas fa-cloud-upload-alt"></i> Upload New Credential
            </h2>
            
            <form id="uploadForm" class="upload-form" enctype="multipart/form-data" action="upload_credential.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="credentialType">Credential Type</label>
                        <select id="credentialType" name="credentialType" class="form-control" required>
                            <option value="">Select credential type</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Certificate">Certificate</option>
                            <option value="Professional License">Professional License</option>
                            <option value="Training Certificate">Training Certificate</option>
                            <option value="Award">Award</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="credentialName">Credential Name</label>
                        <input type="text" id="credentialName" name="credentialName" class="form-control" 
                               placeholder="e.g., Bachelor of Science in Education" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="issuedBy">Issued By</label>
                        <input type="text" id="issuedBy" name="issuedBy" class="form-control" 
                               placeholder="e.g., Pamantasan ng Lungsod ng Pasig" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="issuedDate">Issue Date</label>
                        <input type="date" id="issuedDate" name="issuedDate" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryDate">Expiry Date (if applicable)</label>
                        <input type="date" id="expiryDate" name="expiryDate" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="credentialFile">Upload File</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="credentialFile" name="credentialFile" 
                                   class="file-input" accept=".pdf,.jpg,.jpeg,.png" required>
                            <label for="credentialFile" class="file-upload-label">
                                <i class="fas fa-file-upload"></i> Choose File
                            </label>
                            <span class="file-name">No file chosen</span>
                        </div>
                        <small class="form-text">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Credential
                </button>
                
                <div id="loadingIndicator" class="loading">
                    <div class="spinner"></div>
                    <p>Processing your credential...</p>
                </div>
                
                <div id="statusMessage" class="status-message"></div>
            </form>
        </div>
        
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-folder-open"></i> My Credentials
            </h2>
            
            <div class="credentials-controls">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchCredentials" placeholder="Search credentials...">
                </div>
                
                <select id="filterType" class="form-control">
                    <option value="all">All Types</option>
                    <option value="Diploma">Diplomas</option>
                    <option value="Certificate">Certificates</option>
                    <option value="Professional License">Licenses</option>
                    <option value="Training Certificate">Trainings</option>
                    <option value="Award">Awards</option>
                </select>
            </div>
            
            <div class="credentials-list">
                <?php if (empty($credentials)): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No credentials found</p>
                        <p class="empty-state-help">
                            You haven't uploaded any credentials yet. Use the form above to add your first credential.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($credentials as $credential): ?>
                        <div class="credential-item" 
                        data-id="<?= $credential['credential_id'] ?>" 
                        data-type="<?= htmlspecialchars($credential['credential_type']) ?>">
                            <div class="credential-icon">
                                <?php switch($credential['credential_type']):
                                    case 'Diploma': ?>
                                        <i class="fas fa-graduation-cap"></i>
                                        <?php break; ?>
                                    <?php case 'Professional License': ?>
                                        <i class="fas fa-id-card"></i>
                                        <?php break; ?>
                                    <?php case 'Certificate': ?>
                                        <i class="fas fa-certificate"></i>
                                        <?php break; ?>
                                    <?php case 'Training Certificate': ?>
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        <?php break; ?>
                                    <?php case 'Award': ?>
                                        <i class="fas fa-trophy"></i>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <i class="fas fa-file-alt"></i>
                                <?php endswitch; ?>
                            </div>
                            
                            <div class="credential-details">
                                <h3><?= htmlspecialchars($credential['credential_name']) ?></h3>
                                <p class="credential-meta">
                                    <span><i class="fas fa-building"></i> <?= htmlspecialchars($credential['issued_by']) ?></span>
                                    <span><i class="fas fa-calendar-alt"></i> <?= date('F j, Y', strtotime($credential['issued_date'])) ?></span>
                                    <?php if ($credential['expiry_date']): ?>
                                        <span><i class="fas fa-clock"></i> Expires: <?= date('F j, Y', strtotime($credential['expiry_date'])) ?></span>
                                    <?php endif; ?>
                                    <span class="status-badge <?= strtolower($credential['status']) ?>">
                                        <i class="fas fa-<?= 
                                            $credential['status'] === 'Verified' ? 'check-circle' : 
                                            ($credential['status'] === 'Rejected' ? 'times-circle' : 'clock') 
                                        ?>"></i> 
                                        <?= htmlspecialchars($credential['status']) ?>
                                    </span>
                                </p>
                            </div>
                            
                            <div class="credential-actions">
                                <button class="btn btn-sm btn-view" onclick="viewCredential(<?= $credential['credential_id'] ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="<?= htmlspecialchars($credential['file_path']) ?>" download class="btn btn-sm btn-download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <button class="btn btn-sm btn-delete" onclick="confirmDelete(<?= $credential['credential_id'] ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Credential View Modal -->
    <div id="credentialModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Credential Details</h2>
            <div id="modalContent">
            </div>
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

    <script>
    // Menu toggle
    function toggleMenu() {
        const navigation = document.querySelector('.navigation');
        const hamburger = document.querySelector('.hamburger');
        navigation.classList.toggle('active');
        hamburger.classList.toggle('open');
    }

    // File input display
    document.getElementById('credentialFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
        document.querySelector('.file-name').textContent = fileName;
    });

    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchCredentials');
        const filterSelect = document.getElementById('filterType');
        
        searchInput.addEventListener('input', filterCredentials);
        filterSelect.addEventListener('change', filterCredentials);
        
        function filterCredentials() {
            const searchTerm = searchInput.value.toLowerCase();
            const filterValue = filterSelect.value;
            
            document.querySelectorAll('.credential-item').forEach(item => {
                const itemType = item.getAttribute('data-type').toLowerCase();
                const itemText = item.textContent.toLowerCase();
                
                const matchesSearch = searchTerm === '' || itemText.includes(searchTerm);
                const matchesFilter = filterValue === 'all' || itemType === filterValue.toLowerCase();
                
                item.style.display = (matchesSearch && matchesFilter) ? 'flex' : 'none';
            });
        }
    });

    // View credential modal
    function viewCredential(id) {
        fetch(`get_credential.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').textContent = data.credential_name;
                
                let html = `
                    <div class="modal-credential-info">
                        <p><strong>Type:</strong> ${data.credential_type}</p>
                        <p><strong>Issued by:</strong> ${data.issued_by}</p>
                        <p><strong>Issue Date:</strong> ${new Date(data.issued_date).toLocaleDateString()}</p>
                        ${data.expiry_date ? `<p><strong>Expiry Date:</strong> ${new Date(data.expiry_date).toLocaleDateString()}</p>` : ''}
                        <p><strong>Status:</strong> <span class="status-badge ${data.status.toLowerCase()}">${data.status}</span></p>
                    </div>
                    <div class="modal-preview">
                `;
                
                // Check if file exists
                if (data.file_path) {
                    if (data.file_path.toLowerCase().endsWith('.pdf')) {
                        html += `<iframe src="${data.file_path}#toolbar=0&navpanes=0" frameborder="0"></iframe>`;
                    } else {
                        html += `<img src="${data.file_path}" alt="Credential Document" style="max-width:100%;">`;
                    }
                } else {
                    html += `
                        <div class="file-missing">
                            <i class="fas fa-file-exclamation"></i>
                            <p>File not found</p>
                            <small>The uploaded file could not be located.</small>
                        </div>
                    `;
                }
                
                html += `
                        <div class="modal-actions">
                            ${data.file_path ? `<a href="${data.file_path}" download class="btn btn-primary">
                                <i class="fas fa-download"></i> Download
                            </a>` : ''}
                            <button class="btn btn-accent" onclick="toggleFullscreen()">
                                <i class="fas fa-expand"></i> Fullscreen
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('modalContent').innerHTML = html;
                document.getElementById('credentialModal').style.display = 'block';
            })
            .catch(error => {
                alert('Error loading credential: ' + error.message);
            });
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this credential? This action cannot be undone.')) {
            fetch(`delete_credential.php?id=${id}`, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from UI
                        document.querySelector(`.credential-item[data-id="${id}"]`).remove();
                        // Show success message
                        alert('Credential deleted successfully');
                        // Reload if no credentials left
                        if (document.querySelectorAll('.credential-item').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error: ' + error.message);
                });
        }
    }

    function closeModal() {
        document.getElementById('credentialModal').style.display = 'none';
    }

    function toggleFullscreen() {
        const elem = document.querySelector('#credentialModal iframe, #credentialModal img');
        if (!document.fullscreenElement) {
            elem?.requestFullscreen().catch(err => {
                alert(`Fullscreen error: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }
    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('credentialModal')) {
            closeModal();
        }
    }
    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        // If user confirms, redirect to logout page
        window.location.href = '../landing/index.php';
      }
      // If user cancels, do nothing
    }
    </script>
    <script src="help.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>