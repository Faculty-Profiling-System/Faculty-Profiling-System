<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css??v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script>
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

    /* Profile Container Dark Theme */
    body.dark-theme .profile-container {
      background: #1a1a1a;
      border: 1px solid #333;
      color: #f3f3f3;
    }

    /* Input Fields Dark Theme */
    body.dark-theme input[type="text"],
    body.dark-theme input[type="email"],
    body.dark-theme input[type="tel"],
    body.dark-theme input[type="date"],
    body.dark-theme textarea,
    body.dark-theme select {
      background: #1a1a1a !important;
      border: 1px solid #333 !important;
      color: #f3f3f3 !important;
      padding: 8px 12px;
    }

    body.dark-theme input[type="text"]:focus,
    body.dark-theme input[type="email"]:focus,
    body.dark-theme input[type="tel"]:focus,
    body.dark-theme input[type="date"]:focus,
    body.dark-theme textarea:focus,
    body.dark-theme select:focus {
      border-color: #00d34a !important;
      outline: none;
    }

    /* Labels Dark Theme */
    body.dark-theme label,
    body.dark-theme .info-label {
      color: #00d34a !important;
      font-weight: 500;
    }

    /* Section Headers Dark Theme */
    body.dark-theme .section-header,
    body.dark-theme h2 {
      color: #00d34a !important;
      border-bottom: 1px solid #333;
    }

    /* Basic Information Section Dark Theme */
    body.dark-theme .info-section {
      background: #1a1a1a;
      border: 1px solid #333;
      padding: 20px;
      margin-bottom: 20px;
    }

    body.dark-theme .info-grid {
      display: grid;
      gap: 20px;
    }

    /* Employment Details Section Dark Theme */
    body.dark-theme .employment-section {
      background: #1a1a1a;
      border: 1px solid #333;
      padding: 20px;
    }

    /* Tab Navigation Dark Theme */
    body.dark-theme .profile-tabs {
      background: #1a1a1a;
      border-bottom: 1px solid #333;
    }

    body.dark-theme .tab-btn {
      background: #1a1a1a;
      color: #f3f3f3;
      border: 1px solid #333;
      padding: 10px 20px;
      cursor: pointer;
    }

    body.dark-theme .tab-btn.active {
      background: #00d34a;
      color: #101010;
      border-color: #00d34a;
    }

    /* Edit Profile Button Dark Theme */
    body.dark-theme .edit-profile-btn {
      background: #00d34a;
      color: #101010;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: 500;
    }

    body.dark-theme .edit-profile-btn:hover {
      background: #00b341;
    }

    /* Profile Stats Dark Theme */
    body.dark-theme .profile-stats {
      background: #1a1a1a;
      border: 1px solid #333;
      padding: 15px;
      margin-top: 20px;
    }

    body.dark-theme .stat-number {
      color: #00d34a;
      font-size: 24px;
      font-weight: bold;
    }

    body.dark-theme .stat-label {
      color: #cccccc;
      font-size: 14px;
    }

    /* Basic Information Title Dark Theme */
    body.dark-theme .basic-info-title {
      color: #00d34a;
      font-size: 1.2em;
      font-weight: 500;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    body.dark-theme .basic-info-title i {
      color: #00d34a;
    }

    /* Employment Details Title Dark Theme */
    body.dark-theme .employment-title {
      color: #00d34a;
      font-size: 1.2em;
      font-weight: 500;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    body.dark-theme .employment-title i {
      color: #00d34a;
    }

    /* Form Fields Container Dark Theme */
    body.dark-theme .form-group {
      margin-bottom: 15px;
    }

    body.dark-theme .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #00d34a;
    }

    body.dark-theme .form-control {
      width: 100%;
      background: #1a1a1a;
      border: 1px solid #333;
      color: #f3f3f3;
      padding: 8px 12px;
      border-radius: 4px;
    }

    body.dark-theme .form-control:focus {
      border-color: #00d34a;
      outline: none;
    }

    /* Read-only Fields Dark Theme */
    body.dark-theme .form-control[readonly] {
      background: #222;
      color: #cccccc;
      cursor: not-allowed;
    }

    /* Profile Header Dark Theme */
    body.dark-theme .profile-header {
      background: #1a1a1a;
      border-bottom: 1px solid #333;
    }

    body.dark-theme .profile-info h1 {
      color: #f3f3f3;
    }

    body.dark-theme .faculty-title,
    body.dark-theme .faculty-dept {
      color: #cccccc;
    }

    /* Stats Dark Theme */
    body.dark-theme .profile-stats {
      background: #222;
      border: 1px solid #333;
    }

    body.dark-theme .info-section {
      border-bottom: 1px solid #333;
    }

    body.dark-theme .info-section h2 {
      color: #00d34a;
    }

    body.dark-theme .info-section h2 i {
      color: #00d34a;
    }

    body.dark-theme .info-grid {
      color: #f3f3f3;
    }

    body.dark-theme .info-item label {
      color: #00d34a;
    }

    body.dark-theme .info-item p {
      color: #cccccc;
    }

    /* Education Items Dark Theme */
    body.dark-theme .education-item,
    body.dark-theme .experience-item,
    body.dark-theme .publication-item {
      border-bottom: 1px solid #333;
    }

    body.dark-theme .edu-header h3,
    body.dark-theme .exp-header h3,
    body.dark-theme .pub-header h3 {
      color: #f3f3f3;
    }

    body.dark-theme .edu-year,
    body.dark-theme .exp-year,
    body.dark-theme .pub-year {
      color: #00d34a;
    }

    body.dark-theme .edu-school,
    body.dark-theme .exp-company,
    body.dark-theme .pub-journal,
    body.dark-theme .edu-details,
    body.dark-theme .exp-details,
    body.dark-theme .pub-authors {
      color: #cccccc;
    }

    /* Certifications and Roles Dark Theme */
    body.dark-theme .cert-item,
    body.dark-theme .role-item,
    body.dark-theme .grant-item {
      background: #222;
      border: 1px solid #333;
    }

    body.dark-theme .cert-item i,
    body.dark-theme .role-item i,
    body.dark-theme .grant-item i {
      color: #00d34a;
    }

    body.dark-theme .cert-details h3,
    body.dark-theme .role-details h3,
    body.dark-theme .grant-details h3 {
      color: #f3f3f3;
    }

    body.dark-theme .cert-details p,
    body.dark-theme .role-details p,
    body.dark-theme .grant-details p {
      color: #cccccc;
    }

    /* Modal Dark Theme */
    body.dark-theme .modal-content {
      background: #1a1a1a;
      border: 1px solid #333;
    }

    body.dark-theme .modal-content h2 {
      color: #f3f3f3;
    }

    body.dark-theme .form-group label {
      color: #f3f3f3;
    }

    body.dark-theme .form-group input {
      background: #222;
      border: 1px solid #333;
      color: #f3f3f3;
    }

    body.dark-theme .form-group input:focus {
      border-color: #00d34a;
    }

    /* Navigation Dark Theme */
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

    /* Profile Image Dark Theme */
    body.dark-theme .profile-image {
      border: 2px solid #333;
    }

    body.dark-theme .edit-btn {
      background: #222;
      border: 1px solid #333;
      color: #f3f3f3;
    }

    body.dark-theme .edit-btn:hover {
      background: #00d34a;
      color: #101010;
    }

    /* Personal Information Tab Dark Theme */
    body.dark-theme #personal-info {
      background: #1a1a1a;
    }

    body.dark-theme .info-section {
      border-bottom: 1px solid #333;
    }

    /* Academic Background Tab Dark Theme */
    body.dark-theme #academic-background {
      background: #1a1a1a;
    }

    /* Professional Experience Tab Dark Theme */
    body.dark-theme #professional-experience {
      background: #1a1a1a;
    }

    /* Publications Tab Dark Theme */
    body.dark-theme #publications {
      background: #1a1a1a;
    }

    /* Academic Background Dark Theme */
    body.dark-theme #academic-background {
      background: #1a1a1a;
    }

    body.dark-theme .education-item {
      border-bottom: 1px solid #333;
      padding: 15px 0;
    }

    body.dark-theme .edu-header h3 {
      color: #00d34a;
      margin: 0;
    }

    body.dark-theme .edu-year {
      color: #cccccc;
      background: #222;
      padding: 4px 12px;
      border-radius: 4px;
    }

    body.dark-theme .edu-school {
      color: #f3f3f3;
      font-style: italic;
      margin: 5px 0;
    }

    body.dark-theme .edu-details {
      color: #cccccc;
    }

    /* Professional Experience Dark Theme */
    body.dark-theme #professional-experience {
      background: #1a1a1a;
    }

    body.dark-theme .experience-item {
      border-bottom: 1px solid #333;
      padding: 15px 0;
    }

    body.dark-theme .exp-header h3 {
      color: #00d34a;
    }

    body.dark-theme .exp-year {
      color: #cccccc;
      background: #222;
      padding: 4px 12px;
      border-radius: 4px;
    }

    body.dark-theme .exp-company {
      color: #f3f3f3;
      font-style: italic;
    }

    body.dark-theme .exp-details {
      color: #cccccc;
    }

    body.dark-theme .exp-details li {
      color: #cccccc;
    }

    /* Publications Dark Theme */
    body.dark-theme #publications {
      background: #1a1a1a;
    }

    body.dark-theme .publication-item {
      border-bottom: 1px solid #333;
      padding: 15px 0;
    }

    body.dark-theme .pub-header h3 {
      color: #00d34a;
    }

    body.dark-theme .pub-year {
      color: #cccccc;
      background: #222;
      padding: 4px 12px;
      border-radius: 4px;
    }

    body.dark-theme .pub-journal {
      color: #f3f3f3;
      font-style: italic;
    }

    body.dark-theme .pub-authors {
      color: #cccccc;
    }

    /* Certifications Dark Theme */
    body.dark-theme .certification-list {
      background: #1a1a1a;
    }

    body.dark-theme .cert-item {
      border: 1px solid #333;
      background: #222;
      margin: 10px 0;
      padding: 15px;
    }

    body.dark-theme .cert-item i {
      color: #00d34a;
    }

    body.dark-theme .cert-details h3 {
      color: #f3f3f3;
    }

    body.dark-theme .cert-details p {
      color: #cccccc;
    }

    /* Roles Dark Theme */
    body.dark-theme .role-list {
      background: #1a1a1a;
    }

    body.dark-theme .role-item {
      border: 1px solid #333;
      background: #222;
      margin: 10px 0;
      padding: 15px;
    }

    body.dark-theme .role-item i {
      color: #00d34a;
    }

    body.dark-theme .role-details h3 {
      color: #f3f3f3;
    }

    body.dark-theme .role-details p {
      color: #cccccc;
    }

    /* Grants Dark Theme */
    body.dark-theme .grant-list {
      background: #1a1a1a;
    }

    body.dark-theme .grant-item {
      border: 1px solid #333;
      background: #222;
      margin: 10px 0;
      padding: 15px;
    }

    body.dark-theme .grant-item i {
      color: #00d34a;
    }

    body.dark-theme .grant-details h3 {
      color: #f3f3f3;
    }

    body.dark-theme .grant-details p {
      color: #cccccc;
    }

    /* Tab Content Dark Theme */
    body.dark-theme .tab-content {
      background: #1a1a1a;
    }

    /* Educational Attainment Title */
    body.dark-theme .educational-attainment-title {
      color: #00d34a;
      border-bottom: 1px solid #333;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    body.dark-theme .educational-attainment-title i {
      color: #00d34a;
    }

    /* Year Badge Dark Theme */
    body.dark-theme .year-badge {
      background: #222;
      color: #cccccc;
      border: 1px solid #333;
      padding: 4px 12px;
      border-radius: 4px;
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
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-image">
                <img src="../images/profile.jpg" alt="Faculty Profile" id="profile-pic" />
                <button class="edit-btn" onclick="document.getElementById('profile-upload').click()">
                    <i class="fas fa-camera"></i>
                </button>
                <input type="file" id="profile-upload" accept="image/*" style="display: none;" />
            </div>
            <div class="profile-info">
                <h1 id="faculty-name">Prof. Rebecca Fajardo</h1>
                <p class="faculty-title">Associate Professor</p>
                <p class="faculty-dept">College of Computer Studies</p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number">10</span>
                        <span class="stat-label">Years</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">15</span>
                        <span class="stat-label">Courses</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">4.9</span>
                        <span class="stat-label">Rating</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Content Tabs -->
        <div class="profile-tabs">
            <button class="tab-btn active" onclick="openTab('personal-info')">Personal Information</button>
            <button class="tab-btn" onclick="openTab('academic-background')">Academic Background</button>
            <button class="tab-btn" onclick="openTab('professional-experience')">Professional Experience</button>
            <button class="tab-btn" onclick="openTab('publications')">Publications</button>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Personal Information Tab -->
            <div id="personal-info" class="tab-pane active">
                <div class="info-section">
                    <h2 class="basic-info-title">
                        <i class="fas fa-user-circle"></i> Basic Information
                    </h2>
                    <div class="info-grid">
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" class="form-control" value="Rebecca Fajardo" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth:</label>
                            <input type="text" class="form-control" value="January 15, 1980" readonly>
                        </div>
                        <div class="form-group">
                            <label>Gender:</label>
                            <input type="text" class="form-control" value="Female" readonly>
                        </div>
                        <div class="form-group">
                            <label>PLP Email:</label>
                            <input type="email" class="form-control" value="fajardo_rebecca@plpasig.edu.ph" readonly>
                        </div>
                        <div class="form-group">
                            <label>Contact Number:</label>
                            <input type="tel" class="form-control" value="+63 912 345 6789" readonly>
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <input type="text" class="form-control" value="123 Alkalde St. Kapasigan, Pasig City" readonly>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h2 class="employment-title">
                        <i class="fas fa-id-card"></i> Employment Details
                    </h2>
                    <div class="info-grid">
                        <div class="form-group">
                            <label>Faculty ID:</label>
                            <input type="text" class="form-control" value="PLP-FAC-2020-001" readonly>
                        </div>
                        <div class="form-group">
                            <label>Position:</label>
                            <input type="text" class="form-control" value="Associate Professor" readonly>
                        </div>
                        <div class="form-group">
                            <label>College:</label>
                            <input type="text" class="form-control" value="College of Computer Studies" readonly>
                        </div>
                        <div class="form-group">
                            <label>Date Hired:</label>
                            <input type="text" class="form-control" value="June 15, 2010" readonly>
                        </div>
                        <div class="form-group">
                            <label>Employment Status:</label>
                            <input type="text" class="form-control" value="Regular" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Background Tab -->
            <div id="academic-background" class="tab-pane">
                <div class="info-section">
                    <h2><i class="fas fa-graduation-cap"></i> Educational Attainment</h2>
                    
                    <div class="education-item">
                        <div class="edu-header">
                            <h3>Doctor of Philosophy in Computer Science</h3>
                            <span class="edu-year">2015 - 2018</span>
                        </div>
                        <p class="edu-school">University of the Philippines, Diliman</p>
                        <p class="edu-details">Dissertation: "Advanced Algorithms for Machine Learning Applications"</p>
                    </div>
                    
                    <div class="education-item">
                        <div class="edu-header">
                            <h3>Master of Science in Computer Science</h3>
                            <span class="edu-year">2010 - 2013</span>
                        </div>
                        <p class="edu-school">Ateneo de Manila University</p>
                        <p class="edu-details">Thesis: "Data Mining Techniques for Educational Systems"</p>
                    </div>
                    
                    <div class="education-item">
                        <div class="edu-header">
                            <h3>Bachelor of Science in Computer Science</h3>
                            <span class="edu-year">2000 - 2004</span>
                        </div>
                        <p class="edu-school">Pamantasan ng Lungsod ng Pasig</p>
                        <p class="edu-details">Magna Cum Laude</p>
                    </div>
                </div>

                <div class="info-section">
                    <h2><i class="fas fa-certificate"></i> Certifications</h2>
                    <div class="certification-list">
                        <div class="cert-item">
                            <i class="fas fa-award"></i>
                            <div class="cert-details">
                                <h3>Certified Data Professional</h3>
                                <p>Institute for Certification of Computing Professionals • 2019</p>
                            </div>
                        </div>
                        <div class="cert-item">
                            <i class="fas fa-award"></i>
                            <div class="cert-details">
                                <h3>Microsoft Certified Azure AI Engineer</h3>
                                <p>Microsoft • 2020</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Experience Tab -->
            <div id="professional-experience" class="tab-pane">
                <div class="info-section">
                    <h2><i class="fas fa-briefcase"></i> Work Experience</h2>
                    
                    <div class="experience-item">
                        <div class="exp-header">
                            <h3>Associate Professor</h3>
                            <span class="exp-year">2018 - Present</span>
                        </div>
                        <p class="exp-company">Pamantasan ng Lungsod ng Pasig</p>
                        <ul class="exp-details">
                            <li>Teaches advanced courses in algorithms and machine learning</li>
                            <li>Department Research Coordinator</li>
                            <li>Adviser for Computer Science majors</li>
                        </ul>
                    </div>
                    
                    <div class="experience-item">
                        <div class="exp-header">
                            <h3>Assistant Professor</h3>
                            <span class="exp-year">2010 - 2018</span>
                        </div>
                        <p class="exp-company">Pamantasan ng Lungsod ng Pasig</p>
                        <ul class="exp-details">
                            <li>Taught core Computer Science subjects</li>
                            <li>Developed new curriculum for Data Science track</li>
                        </ul>
                    </div>
                </div>

                <div class="info-section">
                    <h2><i class="fas fa-users"></i> Administrative Roles</h2>
                    <div class="role-list">
                        <div class="role-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <div class="role-details">
                                <h3>Department Chair</h3>
                                <p>Computer Science Department • 2020 - Present</p>
                            </div>
                        </div>
                        <div class="role-item">
                            <i class="fas fa-clipboard-list"></i>
                            <div class="role-details">
                                <h3>Curriculum Committee Member</h3>
                                <p>College of Computer Studies • 2016 - 2020</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publications Tab -->
            <div id="publications" class="tab-pane">
                <div class="info-section">
                    <h2><i class="fas fa-book"></i> Research Publications</h2>
                    
                    <div class="publication-item">
                        <div class="pub-header">
                            <h3>Machine Learning Approaches to Student Performance Prediction</h3>
                            <span class="pub-year">2021</span>
                        </div>
                        <p class="pub-journal">Journal of Educational Technology • Volume 12, Issue 3</p>
                        <p class="pub-authors">Dela Cruz, J., Santos, M., Reyes, A.</p>
                    </div>
                    
                    <div class="publication-item">
                        <div class="pub-header">
                            <h3>Optimizing Neural Networks for Edge Devices</h3>
                            <span class="pub-year">2019</span>
                        </div>
                        <p class="pub-journal">International Conference on Artificial Intelligence Proceedings</p>
                        <p class="pub-authors">Dela Cruz, J., Tan, R.</p>
                    </div>
                </div>

                <div class="info-section">
                    <h2><i class="fas fa-trophy"></i> Research Grants</h2>
                    <div class="grant-list">
                        <div class="grant-item">
                            <i class="fas fa-graduation-cap"></i>
                            <div class="grant-details">
                                <h3>DOST PCIEERD Grant</h3>
                                <p>AI Applications in Education • 2020-2022 • ₱2,500,000</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Button -->
        <div class="profile-actions">
            <button class="edit-profile-btn" onclick="openEditModal()">
                <i class="fas fa-edit"></i> Edit Profile
            </button>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditModal()">&times;</span>
            <h2>Edit Profile Information</h2>
            <form id="profile-form">
                <!-- Form content would go here -->
                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" value="Juan Santos Dela Cruz">
                </div>
                <!-- More form fields... -->
                <div class="form-actions">
                    <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab functionality
        function openTab(tabName) {
            const tabPanes = document.getElementsByClassName('tab-pane');
            for (let i = 0; i < tabPanes.length; i++) {
                tabPanes[i].classList.remove('active');
            }
            
            const tabBtns = document.getElementsByClassName('tab-btn');
            for (let i = 0; i < tabBtns.length; i++) {
                tabBtns[i].classList.remove('active');
            }
            
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Modal functionality
        function openEditModal() {
            document.getElementById('edit-modal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('edit-modal').style.display = 'none';
        }
        
        // Profile picture upload preview
        document.getElementById('profile-upload').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profile-pic').src = event.target.result;
                };
                reader.readAsDataURL(e.target.files[0]);
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
    <script src="../scripts.js"></script>
</body>
</html>