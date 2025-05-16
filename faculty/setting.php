<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css??v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">PROFILE</a></li>
        <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">TEACHING LOAD</a></li>
        <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">CREDENTIALS</a></li>
        <li><a href="setting.php" class="active"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">SETTINGS</a></li>
      </ul>
    </nav>

    <div class="logout-section">
          <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
        </div>
    </div> 

    <div id="main" class="main-content">
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
    <script src="../scripts.js"></script>
</body>
</html>