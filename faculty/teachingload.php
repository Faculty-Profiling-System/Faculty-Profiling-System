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
                    <object class="pdf-embed" data="<?= htmlspecialchars($currentPdf) ?>#view=FitH&embedded=true&toolbar=0&navpanes=0" type="application/pdf">
                        <div class="no-pdf">
                            <i class="fas fa-file-pdf"></i>
                            <p>Unable to display PDF. Click download to view the file.</p>
                        </div>
                    </object>
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
                        pdfViewer.data = data.filepath + '?t=' + Date.now() + '#view=FitH&embedded=true&toolbar=0&navpanes=0';
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

    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Apply theme
        const savedTheme = localStorage.getItem('plpTheme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
        // Apply text size
        const savedTextSize = localStorage.getItem('plpTextSize') || '100';
        document.documentElement.style.fontSize = savedTextSize + '%';
    });
    </script>
    <script src="../scripts.js"></script>
</body>
</html>

<style>
/* Light theme (default) */
body {
  background: #f4fff4;
  color: #187436;
}

/* Dark theme */
body.dark-theme {
    background: #101010 !important;
    color: #f3f3f3 !important;
}

body.dark-theme .dashboard-container {
    background: #101010;
}

body.dark-theme .card {
    background: #1a1a1a;
    border: 1px solid #333;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 4px;
}

body.dark-theme .dashboard-title,
body.dark-theme .card-title {
    color: #00d34a;
    margin-bottom: 15px;
}

body.dark-theme .card-title i {
    color: #00d34a;
}

body.dark-theme .upload-form {
    background: #1a1a1a;
    padding: 20px;
    border-radius: 4px;
}

body.dark-theme .form-group label {
    color: #00d34a;
    margin-bottom: 8px;
    display: block;
}

body.dark-theme .file-input {
    background: #222;
    border: 1px solid #333;
    color: #f3f3f3;
    padding: 8px;
    width: 100%;
    border-radius: 4px;
}

body.dark-theme .btn-primary {
    background: #00d34a;
    color: #101010;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
}

body.dark-theme .btn-primary:hover {
    background: #00b341;
}

body.dark-theme .pdf-viewer-container {
    background: #1a1a1a;
    border: 1px solid #333;
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
}

body.dark-theme .no-pdf {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #cccccc;
}

body.dark-theme .no-pdf i {
    color: #00d34a;
    font-size: 48px;
    margin-bottom: 15px;
}

body.dark-theme .no-pdf p {
    color: #cccccc;
    margin: 10px 0 0 0;
    font-size: 16px;
}

body.dark-theme .pdf-controls {
    margin-top: 15px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

body.dark-theme .btn-accent {
    background: #222;
    color: #f3f3f3;
    border: 1px solid #333;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
}

body.dark-theme .btn-accent:hover {
    background: #333;
}

body.dark-theme .loading {
    background: rgba(26, 26, 26, 0.9);
    color: #f3f3f3;
}

body.dark-theme .status-message {
    background: #222;
    border: 1px solid #333;
    color: #f3f3f3;
    padding: 10px;
    margin-top: 15px;
    border-radius: 4px;
}

body.dark-theme .status-message.success {
    border-color: #00d34a;
    color: #00d34a;
}

body.dark-theme .status-message.error {
    border-color: #ff4444;
    color: #ff4444;
}

/* File input styling */
body.dark-theme input[type="file"] {
    background: #222;
    color: #f3f3f3;
    border: 1px solid #333;
    padding: 8px;
    border-radius: 4px;
    width: 100%;
}

body.dark-theme input[type="file"]::-webkit-file-upload-button {
    background: #00d34a;
    color: #101010;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10px;
}

body.dark-theme input[type="file"]::-webkit-file-upload-button:hover {
    background: #00b341;
}

/* Description text */
body.dark-theme p {
    color: #cccccc;
}

body.dark-theme .teaching-container {
  background: #1a1a1a;
  border: 1px solid #333;
}

body.dark-theme .teaching-header {
  color: #f3f3f3;
  border-bottom: 1px solid #333;
}

body.dark-theme .teaching-table {
  color: #f3f3f3;
  border-color: #333;
}

body.dark-theme .teaching-table th {
  background: #222;
  color: #00d34a;
  border-color: #333;
}

body.dark-theme .teaching-table td {
  border-color: #333;
  color: #cccccc;
}

body.dark-theme .upload-section {
  background: #1a1a1a;
  border: 1px solid #333;
}

body.dark-theme .upload-header {
  color: #f3f3f3;
  border-bottom: 1px solid #333;
}

body.dark-theme .upload-form label {
  color: #f3f3f3;
}

body.dark-theme .upload-form input[type="submit"] {
  background: #00d34a;
  color: #101010;
}

body.dark-theme .upload-form input[type="submit"]:hover {
  background: #00b341;
}

body.dark-theme .status-badge {
  background: #222;
  color: #f3f3f3;
  border: 1px solid #333;
}

body.dark-theme .status-badge.success {
  background: #00d34a;
  color: #101010;
}

body.dark-theme .status-badge.pending {
  background: #d3a500;
  color: #101010;
}

body.dark-theme .status-badge.failed {
  background: #d30000;
  color: #f3f3f3;
}

body.dark-theme .action-btn {
  background: #222;
  color: #f3f3f3;
  border: 1px solid #333;
}

body.dark-theme .action-btn:hover {
  background: #00d34a;
  color: #101010;
  border-color: #00d34a;
}

body.dark-theme .navigation {
  background: #101010;
  border-right: 1px solid #333;
}

body.dark-theme .navigation a {
  color: #f3f3f3;
}

body.dark-theme .navigation a:hover,
body.dark-theme .navigation a.active {
  background: #00d34a;
  color: #101010;
}

body.dark-theme .navigation-header h1,
body.dark-theme .navigation-header h2 {
  color: #f3f3f3;
}

body.dark-theme .logout-section a {
  color: #f3f3f3;
}

body.dark-theme .logout-section a:hover {
  background: #00d34a;
  color: #101010;
}

/* Additional dark theme styles for specific elements */
body.dark-theme .fas {
  color: #00d34a;
}

body.dark-theme .file-preview {
  background: #222;
  border: 1px solid #333;
}

body.dark-theme .preview-header {
  border-bottom: 1px solid #333;
  color: #f3f3f3;
}

body.dark-theme .preview-content {
  color: #cccccc;
}

body.dark-theme .empty-state {
  color: #cccccc;
}

body.dark-theme .upload-instructions {
  color: #cccccc;
}

body.dark-theme .file-requirements {
  background: #222;
  border: 1px solid #333;
  color: #cccccc;
}

body.dark-theme .file-requirements h4 {
  color: #00d34a;
}

body.dark-theme .pdf-embed {
    width: 100%;
    height: 600px;
    border: none;
}

body.dark-theme .pdf-viewer-container object {
    background: #1a1a1a;
    width: 100%;
    height: 100%;
    min-height: 600px;
}
</style>