<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/theme.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    // Initialize states
    let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

    // Check and apply theme and text size on page load
    document.addEventListener('DOMContentLoaded', function() {
      // Apply theme
      const savedTheme = localStorage.getItem('plpTheme') || 'light';
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
      }
      // Apply text size
      document.documentElement.style.fontSize = currentSize + '%';
      updateSelected();

      // Initialize collapsible sections
      var coll = document.getElementsByClassName("collapsible");
      for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
          this.classList.toggle("active");
          var content = this.nextElementSibling;
          if (content.style.maxHeight) {
            content.style.maxHeight = null;
          } else {
            content.style.maxHeight = content.scrollHeight + "px";
          }
        });
      }
    });

    function applyTheme(theme) {
      if (theme === 'dark') {
        document.body.classList.add('dark-theme');
      } else {
        document.body.classList.remove('dark-theme');
      }
      localStorage.setItem('plpTheme', theme);
    }

    function setTextSize(size) {
      currentSize = size;
      document.documentElement.style.fontSize = size + '%';
      localStorage.setItem('plpTextSize', size);
      updateSelected();
    }

    function setTheme(theme) {
      applyTheme(theme);
      updateSelected();
    }

    function updateSelected() {
      // Text size buttons
      [100, 150, 200].forEach(s => {
        document.getElementById('size-' + s).classList.toggle('selected', currentSize === s);
      });
      // Theme buttons
      const currentTheme = localStorage.getItem('plpTheme') || 'light';
      document.getElementById('theme-light').classList.toggle('selected', currentTheme === 'light');
      document.getElementById('theme-dark').classList.toggle('selected', currentTheme === 'dark');
    }
    </script>
    <style>
    .main-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .settings-section {
        margin: 2rem 0;
    }

    .collapsible {
        background: none;
        color: inherit;
        cursor: pointer;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #187436;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .collapsible:hover {
        background: rgba(0, 211, 74, 0.1);
    }

    .collapsible:after {
        content: '\002B';
        font-weight: bold;
        float: right;
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .collapsible.active:after {
        content: '\2212';
    }

    .content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 0 0 8px 8px;
        padding: 0 1rem;
    }

    .content.active {
        max-height: 500px;
        padding: 1rem;
    }

    .settings-options {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .settings-btn {
        background: #e3f3e3;
        border: none;
        border-radius: 8px;
        padding: 0.7rem 1.5rem;
        font-size: 1rem;
        color: #187436;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .settings-btn.selected {
        background: #00723F;
        color: #ffffff;
    }

    .settings-input {
        width: 100%;
        padding: 0.7rem;
        margin-bottom: 1rem;
        border: 1px solid #187436;
        border-radius: 8px;
        background: #f4fff4;
        color: #187436;
    }

    hr {
        border: none;
        border-top: 1px solid #187436;
        margin: 2rem 0;
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

        <h2>Settings</h2>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Text Size</button>
            <div class="content">
                <div class="settings-options">
                    <button class="settings-btn" id="size-100" onclick="setTextSize(100)">100%</button>
                    <button class="settings-btn" id="size-150" onclick="setTextSize(150)">150%</button>
                    <button class="settings-btn" id="size-200" onclick="setTextSize(200)">200%</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Theme</button>
            <div class="content">
                <div class="settings-options">
                    <button class="settings-btn" id="theme-light" onclick="setTheme('light')">Light</button>
                    <button class="settings-btn" id="theme-dark" onclick="setTheme('dark')">Dark</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="settings-section">
            <button type="button" class="collapsible">Change Password</button>
            <div class="content">
                <form id="changePasswordForm" class="settings-options" style="flex-direction: column;">
                    <input type="password" id="currentPassword" placeholder="Current Password" class="settings-input" required />
                    <input type="password" id="newPassword" placeholder="New Password" class="settings-input" required />
                    <input type="password" id="confirmPassword" placeholder="Confirm New Password" class="settings-input" required />
                    <button type="submit" class="settings-btn" style="align-self: flex-start;">Change Password</button>
                </form>
            </div>
        </div>
        <hr>
    </div>
    
    <script>
      function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
      }
    </script>
    <script src="js/settings.js"></script>
    <script src="js/change_password.js"></script>
    <script src="help.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>