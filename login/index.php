<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PLP Faculty Login</title>
  <link rel="stylesheet" href="../css/login.css?v=<?php echo time(); ?>" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <div class="container">
    <div class="left-side">
      <img src="../images/log_in_page.png" alt="PLP Campus" class="background-img" />
    </div>
    <div class="right-side">
      <div class="header">
        <img src="../images/PLP_logo.png" alt="PLP Logo" class="logo" />
        <h2>PAMANTASAN NG LUNGSOD NG PASIG</h2>
      </div>
      <form class="login-form" action="process_login.php" method="POST" id="loginForm">
        <h1>Login</h1>
        
      <?php if (isset($_GET['error'])): ?>
        <div class="floating-error">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

        <input type="text" name="username" placeholder="USERNAME" class="input-field" id="username" required />
        
        <div class="password-wrapper">
          <input type="password" name="password" placeholder="PASSWORD" class="input-field" id="password" required />
        </div>
        
        <a href="../forgot_password/forgotpassword.php" class="forgot-password">Forgot password?</a>
        
        <!-- Add reCAPTCHA widget -->
        <div class="g-recaptcha" data-sitekey="6Lc2ZUArAAAAAIvfTsk94VYy9hG5e9Yb1Ge7Dln3"></div>
        
        <button type="submit" class="login-button">LOG IN</button>
        <a href="../landing/index.php" class="cancel-button" onclick="return confirm('Are you sure you want to cancel?')">CANCEL</a>
      </form>
    </div>
  </div>
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    
    togglePassword.addEventListener('click', function (e) {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Optional: Form validation before submitting
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      if(grecaptcha.getResponse().length === 0) {
        e.preventDefault();
        alert("Please complete the reCAPTCHA verification");
      }
    });
  </script>
</body>
</html>