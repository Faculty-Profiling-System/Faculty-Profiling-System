<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.html');
    exit();
}

// Database connection
require_once('../db_connection.php');

// Check for existing PDF in database
$currentPdf = null;
$stmt = $conn->prepare("SELECT file_path FROM teaching_load WHERE faculty_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->bind_param("i", $_SESSION['faculty_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentPdf = $row['file_path'];
}

$pdfExists = $currentPdf && file_exists(__DIR__ . '/' . $currentPdf);
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Schedule | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css??v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../css/teachingload.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        <li><a href="teachingload.php" class="active"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>   

    
    <div class="dashboard-container">
        <h1 class="dashboard-title">
            <i class="fas fa-calendar-alt"></i> Teaching Schedule
        </h1>
        
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-cloud-upload-alt"></i> Upload Your Schedule
            </h2>
            <p style="color: var(--text-medium); margin-bottom: 1.5rem;">Upload your teaching schedule in PDF format (maximum 5MB)</p>
            
            <form id="uploadForm" class="upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="scheduleFile">Select PDF File</label>
                    <input type="file" id="scheduleFile" name="scheduleFile" class="file-input" accept=".pdf" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Schedule
                </button>
                <div id="loadingIndicator" class="loading">
                    <div class="spinner"></div>
                    <p>Processing your file...</p>
                </div>
                <div id="statusMessage" class="status-message"></div>
            </form>
        </div>
        
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-file-pdf"></i> Current Schedule
            </h2>
            <div class="pdf-viewer-container">
                <?php if ($pdfExists): ?>
                    <iframe class="pdf-embed" src="<?= htmlspecialchars($currentPdf) ?>#toolbar=0&navpanes=0&zoom=fit"></iframe>
                <?php else: ?>
                    <div class="no-pdf">
                        <i class="fas fa-file-pdf"></i>
                        <p>No schedule uploaded yet</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if ($pdfExists): ?>
                <div class="pdf-controls">
                    <button class="btn btn-accent" onclick="toggleFullscreen()">
                        <i class="fas fa-expand"></i> Fullscreen
                    </button>
                    <a href="<?= htmlspecialchars($currentPdf) ?>" download class="btn btn-primary">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            <?php endif; ?>
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

    // Fullscreen toggle
    function toggleFullscreen() {
        const container = document.querySelector('.pdf-viewer-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => {
                alert(`Fullscreen error: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // File upload handling
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('uploadForm');
        const statusMsg = document.getElementById('statusMessage');
        const loading = document.getElementById('loadingIndicator');
        
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('scheduleFile');
            const file = fileInput.files[0];
            
            // Validate file
            if (!file) {
                showStatus('Please select a PDF file', 'error');
                return;
            }
            
            if (!file.name.toLowerCase().endsWith('.pdf')) {
                showStatus('Only PDF files are accepted', 'error');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                showStatus('File size exceeds 5MB limit', 'error');
                return;
            }
            
            // Show loading state
            loading.style.display = 'block';
            statusMsg.style.display = 'none';
            form.querySelector('button').disabled = true;
            
            try {
                const formData = new FormData();
                formData.append('scheduleFile', file);
                
                const response = await fetch('upload_pdf.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showStatus('Upload successful! Your schedule has been updated.', 'success');
                    
                    // Update preview dynamically
                    const pdfViewer = document.querySelector('.pdf-embed');
                    const noPdfMsg = document.querySelector('.no-pdf');
                    const pdfControls = document.querySelector('.pdf-controls');
                    
                    if (pdfViewer) {
                        pdfViewer.src = data.filepath + '?t=' + Date.now() + '#toolbar=0&navpanes=0&zoom=fit';
                        pdfViewer.style.display = 'block';
                    }
                    if (noPdfMsg) noPdfMsg.style.display = 'none';
                    if (!pdfControls) {
                        const viewerCard = document.querySelector('.card:last-child');
                        const controlsHTML = `
                            <div class="pdf-controls">
                                <button class="btn btn-accent" onclick="toggleFullscreen()">
                                    <i class="fas fa-expand"></i> Fullscreen
                                </button>
                                <a href="${data.filepath}" download class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        `;
                        viewerCard.insertAdjacentHTML('beforeend', controlsHTML);
                    } else {
                        const downloadBtn = document.querySelector('.pdf-controls a[download]');
                        if (downloadBtn) {
                            downloadBtn.href = data.filepath;
                        }
                    }
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'error');
            } finally {
                loading.style.display = 'none';
                form.querySelector('button').disabled = false;
            }
        });
        
        function showStatus(msg, type) {
            statusMsg.textContent = msg;
            statusMsg.className = 'status-message ' + type;
            statusMsg.style.display = 'block';
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    statusMsg.style.display = 'none';
                }, 5000);
            }
        }
    });
    
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