<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css??v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
                    <h2><i class="fas fa-user-circle"></i> Basic Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Full Name:</label>
                            <p>Rebecca Fajardo</p>
                        </div>
                        <div class="info-item">
                            <label>Date of Birth:</label>
                            <p>January 15, 1980</p>
                        </div>
                        <div class="info-item">
                            <label>Gender:</label>
                            <p>Female</p>
                        </div>
                        <div class="info-item">
                            <label>PLP Email:</label>
                            <p>fajardo_rebecca@plpasig.edu.ph</p>
                        </div>
                        <div class="info-item">
                            <label>Contact Number:</label>
                            <p>+63 912 345 6789</p>
                        </div>
                        <div class="info-item">
                            <label>Address:</label>
                            <p>123 Alkalde St. Kapasigan, Pasig City</p>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h2><i class="fas fa-id-card"></i> Employment Details</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Faculty ID:</label>
                            <p>PLP-FAC-2020-001</p>
                        </div>
                        <div class="info-item">
                            <label>Position:</label>
                            <p>Associate Professor</p>
                        </div>
                        <div class="info-item">
                            <label>College:</label>
                            <p>College of Computer Studies</p>
                        </div>
                        <div class="info-item">
                            <label>Date Hired:</label>
                            <p>June 15, 2010</p>
                        </div>
                        <div class="info-item">
                            <label>Employment Status:</label>
                            <p>Regular</p>
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
                    <input type="text" id="full-name" value="Rebecca Fajardo">
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