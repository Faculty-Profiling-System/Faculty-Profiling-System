<?php
session_start();
if (!isset($_SESSION['faculty_id'])) {
    header('Location: ../landing/index.html');
    exit();
}

// Database connection
require_once('../db_connection.php');

// Get faculty ID from session
$faculty_id = $_SESSION['faculty_id'];

// Fetch faculty data
$faculty_query = "SELECT f.*, c.college_name 
                 FROM faculty f 
                 JOIN colleges c ON f.college_id = c.college_id 
                 WHERE f.faculty_id = ?";
$stmt = $conn->prepare(query: $faculty_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Faculty not found
    header('Location: ../landing/index.html');
    exit();
}

$faculty_data = $result->fetch_assoc();

// Assign variables for use in HTML
$full_name = $faculty_data['full_name'];
$birthday = $faculty_data['birthdate'];
$gender = $faculty_data['gender'];
$email = $faculty_data['email'];
$employment_type = $faculty_data['employment_type'];
$specialization = $faculty_data['specialization'];
$contact_number = $faculty_data['contact_number'];
$address = $faculty_data['address'];
$profile_picture = $faculty_data['profile_picture'];
$college_name = $faculty_data['college_name'];

// Fetch academic background
$academic_backgrounds = [];
$academic_query = "SELECT * FROM academic_background WHERE faculty_id = ? ORDER BY degree_level DESC, end_year DESC";
$stmt = $conn->prepare($academic_query);
$stmt->bind_param("s", $faculty_id);
$stmt->execute();
$academic_result = $stmt->get_result();

if ($academic_result->num_rows > 0) {
    while ($row = $academic_result->fetch_assoc()) {
        $academic_backgrounds[] = $row;
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="theme.js?v=<?php echo time(); ?>"></script>
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
        <h1>FACULTY</h1>
      <h2>| PLP FACULTY PROFILING SYSTEM |</h2>
    </div>
        
    <nav>
      <ul>
        <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">HOME</a></li>
        <li><a href="profile.php" class="active"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
            <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-picture-container">
                <img id="profile-picture" src="<?php echo $profile_picture ? $profile_picture : '../images/profile.jpg'; ?>" alt="Profile Picture">
                <div class="upload-overlay" onclick="document.getElementById('profile-upload').click()">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="profile-upload" accept="image/*" style="display: none;">
                </div>
            </div>
            <div class="profile-info">
                <h1 id="faculty-name"><?php echo htmlspecialchars($full_name); ?></h1>
                <p id="specialization"><?php echo htmlspecialchars($specialization); ?></p>
                <p id="faculty-id">Faculty ID: <?php echo htmlspecialchars($faculty_id); ?></p>
                <p id="college-name"><?php echo htmlspecialchars($college_name); ?></p>
                <p id="employment-type"><?php echo htmlspecialchars($employment_type); ?></p>
            </div>
        </div>

        <div class="profile-sections">
            <!-- Personal Information Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h2>Personal Information</h2>
                    <button class="edit-section-btn" onclick="toggleSectionEdit('personal-info')">Edit</button>
                </div>
                <div class="section-content" id="personal-info">
                    <div class="info-row">
                        <span class="info-label">Birthday:</span>
                        <span class="info-value" id="birthday-display"><?php echo date('F j, Y', strtotime($birthday)); ?></span>
                        <input type="date" class="info-edit" id="birthday-edit" value="<?php echo $birthday; ?>" style="display: none;">
                    </div>
                    <div class="info-row">
                        <span class="info-label">Gender:</span>
                        <span class="info-value" id="gender-display"><?php echo $gender; ?></span>
                        <select class="info-edit" id="gender-edit" style="display: none;">
                            <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value" id="email-display"><?php echo htmlspecialchars($email); ?></span>
                        <input type="email" class="info-edit" id="email-edit" value="<?php echo htmlspecialchars($email); ?>" style="display: none;">
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact Number:</span>
                        <span class="info-value" id="contact-display"><?php echo $contact_number ? htmlspecialchars($contact_number) : 'Not provided'; ?></span>
                        <input type="tel" class="info-edit" id="contact-edit" value="<?php echo htmlspecialchars($contact_number); ?>" style="display: none;">
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value" id="address-display"><?php echo $address ? htmlspecialchars($address) : 'Not provided'; ?></span>
                        <textarea class="info-edit" id="address-edit" style="display: none;"><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                    <div class="section-actions" style="display: none;">
                        <button class="save-btn" onclick="saveSection('personal-info')">Save</button>
                        <button class="cancel-btn" onclick="cancelSectionEdit('personal-info')">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Academic Background Section -->
            <div class="profile-section">
                <div class="section-header">
                    <h2>Academic Background</h2>
                    <button class="add-btn" onclick="addAcademicBackground()">Add Degree</button>
                </div>
                <div class="section-content" id="academic-background">
                    <?php if (!empty($academic_backgrounds)): ?>
                        <?php foreach ($academic_backgrounds as $degree): ?>
                            <div class="degree-item" data-id="<?php echo $degree['id']; ?>">
                                <div class="degree-header">
                                    <h3><?php echo htmlspecialchars($degree['degree_title']); ?> in <?php echo htmlspecialchars($degree['field_of_study']); ?></h3>
                                    <div class="degree-actions">
                                        <button class="edit-btn" onclick="editDegree(this)"><i class="fas fa-edit"></i></button>
                                        <button class="delete-btn" onclick="deleteDegree(<?php echo $degree['id']; ?>)"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="degree-details">
                                    <p><strong>Degree Level:</strong> <?php echo $degree['degree_level']; ?></p>
                                    <p><strong>Institution:</strong> <?php echo htmlspecialchars($degree['institution_name']); ?></p>
                                    <?php if ($degree['start_year'] && $degree['end_year']): ?>
                                        <p><strong>Years:</strong> <?php echo $degree['start_year']; ?> - <?php echo $degree['end_year']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($degree['thesis_title']): ?>
                                        <p><strong>Thesis/Dissertation:</strong> <?php echo htmlspecialchars($degree['thesis_title']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($degree['honors']): ?>
                                        <p><strong>Honors:</strong> <?php echo htmlspecialchars($degree['honors']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">No academic background information added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Academic Background Modal -->
        <div id="degree-modal" class="modal">
            <div class="modal-content">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <h2 id="modal-title">Add Academic Degree</h2>
                <form id="degree-form">
                    <input type="hidden" id="degree-id" value="">
                    <div class="form-group">
                        <label for="degree-level">Degree Level*</label>
                        <select id="degree-level" required>
                            <option value="">Select degree level</option>
                            <option value="Bachelor">Bachelor's Degree</option>
                            <option value="Master">Master's Degree</option>
                            <option value="Doctor">Doctoral Degree</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="degree-title">Degree Title*</label>
                        <input type="text" id="degree-title" required>
                    </div>
                    <div class="form-group">
                        <label for="field-of-study">Field of Study*</label>
                        <input type="text" id="field-of-study" required>
                    </div>
                    <div class="form-group">
                        <label for="institution-name">Institution Name*</label>
                        <input type="text" id="institution-name" required>
                    </div>
                    <div class="form-group">
                        <label for="thesis-title">Thesis/Dissertation Title</label>
                        <textarea id="thesis-title"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="honors">Honors/Awards</label>
                        <input type="text" id="honors">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start-year">Start Year</label>
                            <input type="number" id="start-year" min="1900" max="<?php echo date('Y'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="end-year">End Year</label>
                            <input type="number" id="end-year" min="1900" max="<?php echo date('Y'); ?>">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save</button>
                        <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
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
        function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            // If user confirms, redirect to logout page
            window.location.href = '../landing/index.php';
        }
        // If user cancels, do nothing
        }
    </script>
    <script src="help.js"></script>
    <script src="profile.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>