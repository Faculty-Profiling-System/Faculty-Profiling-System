<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Faculty</title>
    <link rel="stylesheet" href="../css/faculty_style.css?v=<?php echo time(); ?>"/>
    <link rel="stylesheet" href="../css/help.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/themes.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="theme.js?v=<?php echo time(); ?>"></script>
    <script>
    // Function to remove selected class from all buttons
    function clearSelectedButtons() {
        document.querySelectorAll('.settings-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
    }

    function updateSelected() {
        // Clear all selected states first
        clearSelectedButtons();
        
        // Text size buttons
        const currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;
        const sizeBtn = document.getElementById('size-' + currentSize);
        if (sizeBtn) {
            sizeBtn.classList.add('selected');
        }
        
        // Theme buttons
        const currentTheme = localStorage.getItem('plpTheme') || 'light';
        const themeBtn = document.getElementById('theme-' + currentTheme);
        if (themeBtn) {
            themeBtn.classList.add('selected');
        }
    }
    
    // Initialize theme highlighting on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateSelected();
    });
    </script>
    
    <!-- Separate script for collapsible functionality -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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

    body.dark-theme .collapsible {
      border-color: #333;
      color: #f3f3f3;
    }

    .collapsible:hover {
      background: rgba(0, 211, 74, 0.1);
    }

    body.dark-theme .collapsible:hover {
      background: rgba(0, 211, 74, 0.2);
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

    body.dark-theme .settings-btn {
      background: #222;
      color: #f3f3f3;
    }

    .settings-btn.selected {
      background: #00723F;
      color: #ffffff;
    }

    body.dark-theme .settings-btn.selected {
      background: #00d34a;
      color: #101010;
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

    body.dark-theme .settings-input {
      background: #222;
      color: #f3f3f3;
      border-color: #333;
    }

    .settings-input:focus {
      outline: none;
      border-color: #00d34a;
    }

    hr {
      border: none;
      border-top: 1px solid #187436;
      margin: 2rem 0;
    }

    body.dark-theme hr {
      border-color: #333;
    }

    body.dark-theme .logout-section a {
      color: #f3f3f3;
    }

    body.dark-theme .logout-section a:hover {
      background: #00d34a;
      color: #101010;
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
        <?php include 'help.php'; ?>
    </div>
    
    <script>
      function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
      }
    </script>
    <script src="js/settings.js"></script>
    <script src="help.js"></script>
    <script src="change_password.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>