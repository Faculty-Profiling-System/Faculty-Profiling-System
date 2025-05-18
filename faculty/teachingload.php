<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.php');
    exit();
}

if (isset($_SESSION['upload_success'])) {
    $success = $_SESSION['upload_success'];
    unset($_SESSION['upload_success']);
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}   

// Database connection
require_once('../db_connection.php');

// Get all teaching loads for the faculty
$teachingLoads = [];
$error = null;
$success = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['teaching_file'])) {
    $displayName = $_POST['display_name'];
    $semester = $_POST['semester'];
    $startYear = $_POST['start_year'];
    $endYear = $_POST['end_year'];
    $totalLoads = $_POST['total_loads'];
    
    $file = $_FILES['teaching_file'];
    $originalFileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    // Validate academic year range
    if ($endYear < $startYear) {
         $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "End year cannot be less than start year";
        header('Location: teachingload.php');
        exit();
    } elseif (($endYear - $startYear) > 1) {
        $_SESSION['upload_alert'] = 'error';
        $_SESSION['error_message'] = "End year must not be ahead of start year by more than 1 year";
        header('Location: teachingload.php');
        exit();;
    }
    
    // Validate file (PDF only, max 5MB)
    $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $allowed = ['pdf'];
    
    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $fileDestination = 'uploads/teaching_loads/' . $newFileName;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    try {
                        $stmt = $conn->prepare("INSERT INTO teaching_load (faculty_id, file_name, semester, start_year, end_year, total_loads, file_path) VALUES ( ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("sssssis", $_SESSION['faculty_id'], $displayName, $semester, $startYear, $endYear, $totalLoads, $fileDestination);
                        $stmt->execute();
                        $stmt->close();
                        
                        // Set success flag and redirect
                        $_SESSION['upload_alert'] = 'success';
                        header('Location: teachingload.php');
                        exit();
                    } catch (Exception $e) {
                        // Set error flag and redirect
                        $_SESSION['upload_alert'] = 'error';
                        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
                        unlink($fileDestination);
                        header('Location: teachingload.php');
                        exit();
                    }
                } else {
                    $_SESSION['upload_alert'] = 'error';
                    $_SESSION['error_message'] = "There was an error uploading your file.";
                    header('Location: teachingload.php');
                    exit();
                }
            } else {
                $error = "File is too large. Maximum size is 5MB.";
            }
        } else {
            $error = "There was an error uploading your file.";
        }
    } else {
        $error = "You can only upload PDF files.";
    }
}

// Build the base query
$query = "SELECT * FROM teaching_load WHERE faculty_id = ?";
$params = [$_SESSION['faculty_id']];
$types = "s";

// Add filters if they exist in GET parameters
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $query .= " AND status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

if (isset($_GET['semester']) && !empty($_GET['semester'])) {
    $query .= " AND semester = ?";
    $params[] = $_GET['semester'];
    $types .= "s";
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $query .= " AND start_year = ?";
    $params[] = $_GET['year'];
    $types .= "s";
}

// Add sorting
$query .= " ORDER BY created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $teachingLoads = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teaching Schedule | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../css/teachingload.css?v=<?php echo time(); ?>">
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
                <li><a href="teachingload.php" class="active"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
                <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
                <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
            </ul>
        </nav>

        <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>   

    <div class="main-content">
        <div class="header-content">
            <h2><i class="fas fa-calendar-alt"></i> My Teaching Load</h2>
        </div>

        <!-- Upload Card -->
        <div class="card upload-card">
            <h2><i class="fas fa-upload"></i> Upload Teaching Load</h2>
            <form action="teachingload.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="display_name">Document Name:</label>
                    <input type="text" id="display_name" name="display_name" placeholder="e.g. Teaching Load AY 2023-2024" required>
                </div>
                
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <select id="semester" name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="First Semester">First Semester</option>
                        <option value="Second Semester">Second Semester</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_year">Start Year:</label>
                        <select id="start_year" name="start_year" required>
                            <option value="">Select Year</option>
                            <?php 
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2016; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_year">End Year:</label>
                        <select id="end_year" name="end_year" required>
                            <option value="">Select Year</option>
                            <?php 
                            for ($year = $currentYear + 1; $year >= 2017; $year--) {
                                echo "<option value='$year'>$year</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="total_loads">Total Loads (units):</label>
                    <input type="number" id="total_loads" name="total_loads" placeHolder="e.g 25 (Maximum of 50 units)" min="1" max="50" required>
                </div>
                
                <div class="form-group file-upload">
                    <label for="teaching_file">Upload PDF File (max 5MB):</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="teaching_file" name="teaching_file" accept=".pdf" required>
                        <label for="teaching_file" class="file-upload-label">
                            <i class="fas fa-cloud-upload"></i>
                            <span class="file-upload-text">Choose a file</span>
                            <span class="file-upload-filename" id="file-name">No file chosen</span>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload"></i> Upload</button>
            </form>
        </div>
        
        <!-- List of Uploaded Files Card -->
        <div class="card list-card">
            <h2><i class="fas fa-list"></i> Uploaded Teaching Loads</h2>
            
            <!-- Filter Section -->
            <div class="filters">
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status_filter">Status:</label>
                            <select id="status_filter" name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="Verified" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Verified') ? 'selected' : ''; ?>>Verified</option>
                                <option value="Rejected" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="semester_filter">Semester:</label>
                            <select id="semester_filter" name="semester" onchange="this.form.submit()">
                                <option value="">All Semesters</option>
                                <option value="First Semester" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'First Semester') ? 'selected' : ''; ?>>First Semester</option>
                                <option value="Second Semester" <?php echo (isset($_GET['semester']) && $_GET['semester'] == 'Second Semester') ? 'selected' : ''; ?>>Second Semester</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="year_filter">School Year:</label>
                            <select id="year_filter" name="year" onchange="this.form.submit()">
                                <option value="">All Years</option>
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= 2017; $year--) {
                                    $yearRange = $year . '-' . ($year + 1);
                                    $selected = (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : '';
                                    echo "<option value='$year' $selected>$yearRange</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if (empty($teachingLoads)): ?>
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>No teaching loads found matching your criteria.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Semester</th>
                                <th>Academic Year</th>
                                <th>Total Loads</th>
                                <th>Status</th>
                                <th>Date Uploaded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachingLoads as $load): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($load['file_name']); ?></td>
                                    <td><?php echo htmlspecialchars($load['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($load['start_year']) . ' - ' . htmlspecialchars($load['end_year']); ?></td>
                                    <td><?php echo htmlspecialchars($load['total_loads']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($load['status']); ?>">
                                            <?php echo htmlspecialchars($load['status']); ?>
                                        </span>
                                        <?php if ($load['status'] === 'Rejected' && !empty($load['reason'])): ?>
                                            <span class="reason-tooltip" title="<?php echo htmlspecialchars($load['reason']); ?>">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($load['created_at'])); ?></td>
                                    <td class="actions">
                                        <a href="<?php echo $load['file_path']; ?>" target="_blank" class="btn-view" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="<?php echo $load['file_path']; ?>" download class="btn-download" title="Download"><i class="fas fa-download"></i></a>
                                        <?php if ($load['status'] === 'Pending' || $load['status'] === 'Rejected'): ?>
                                            <a href="#" class="btn-delete" title="Delete" onclick="confirmDelete(<?php echo $load['load_id']; ?>)"><i class="fas fa-trash-alt"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'help.php'; ?>

    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['show_alert'])): ?>
                <?php if ($_SESSION['show_alert'] === 'success'): ?>
                    alert('Teaching load deleted successfully!');
                <?php elseif ($_SESSION['show_alert'] === 'error'): ?>
                    alert('Error: Could not delete teaching load');
                <?php endif; ?>
                <?php unset($_SESSION['show_alert']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['upload_alert'])): ?>
                <?php if ($_SESSION['upload_alert'] === 'success'): ?>
                    alert('Teaching load uploaded successfully!');
                <?php elseif ($_SESSION['upload_alert'] === 'error'): ?>
                    alert('<?php echo isset($_SESSION['error_message']) ? addslashes($_SESSION['error_message']) : "Error uploading file"; ?>');
                <?php endif; ?>
                <?php 
                unset($_SESSION['upload_alert']);
                unset($_SESSION['error_message']);
                ?>
            <?php endif; ?>
        };

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        function confirmDelete(loadId) {
            if (confirm('Are you sure you want to delete this teaching load?')) {
                window.location.href = 'delete_teaching_load.php?id=' + loadId;
            }
        }

        document.querySelector('.main-content').addEventListener('click', function() {
            if (document.querySelector('.navigation.active')) {
                document.querySelector('.hamburger').click();
            }
        });

        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../landing/index.php';
            }
        }
        
        function confirmDelete(loadId) {
            if (confirm('Are you sure you want to delete this teaching load?')) {
                window.location.href = 'delete_teaching_load.php?id=' + loadId;
            }
        }
    </script>
    <script src="help.js?v=<?php echo time(); ?>"></script>
    <script src="../scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>