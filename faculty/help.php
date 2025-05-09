<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Profiling System</title>
    <link rel="stylesheet" href="../css/mains.css" />
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <img src="../images/logo.png" alt="Logo" />
            <div class="title">PAMANTASAN NG LUNGSOD NG PASIG</div>
            <button class="hamburger" onclick="show()">
                <div id="bar1" class="bar"></div>
                <div id="bar2" class="bar"></div>
                <div id="bar3" class="bar"></div>
            </button>
        </div>
        
         <div class="navigation">
            <nav>
                <ul>
                    <li><a href="home.php"><img src="../images/home.png" alt="Home Icon" class="menu-icon">Home</a></li>
                    <li><a href="profile.php"><img src="../images/profile.png" alt="Profile Icon" class="menu-icon">Profile</a></li>
                    <li><a href="faculty.php"><img src="../images/faculty.png" alt="Profile Icon" class="menu-icon">View Faculty</a></li>
                    <li><a href="teachingload.php"><img src="../images/teachingload.png" alt="Teaching Icon" class="menu-icon">Teaching Load</a></li>
                    <li><a href="credentials.php"><img src="../images/credentials.png" alt="Credentials Icon" class="menu-icon">Credentials</a></li>
                    <li><a href="help.php"><img src="../images/help.png" alt="Help Icon" class="menu-icon">Help/Support</a></li>
                    <li><a href="setting.php"><img src="../images/setting.png" alt="Settings Icon" class="menu-icon">Settings</a></li>
                </ul>
            </nav>
            <div class="logout-section">
                <a href="#" onclick="confirmLogout()"><img src="../images/logout.png" alt="Logout Icon" class="menu-icon">LOGOUT</a>
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
    <script src="../scripts.js"></script>
</body>
</html>