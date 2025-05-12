<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - PLP</title>
  <link rel="stylesheet" href="../css/styleFP.css" />
</head>
<body>
  <div class="container">
    <div class="left">
      <img src="../images/log_in_page.png" alt="PLP Building" class="bg-image" />
    </div>
    <div class="right">
        <div class="content-wrapper">
      <div class="logo-header">
        <img src="../images/PLP_logo.png" alt="PLP Logo" class="logo" />
        <h2>PAMANTASAN NG LUNGSOD NG PASIG</h2>
      </div>
      <div class="form-box">
        <h1>Forgot your password</h1>
        <p>Please enter the email address you’d like your password reset information sent to</p>
        <label for="email">Enter email address</label>
        <input type="email" id="email" placeholder="faculty@plpasig.edu.ph" />
        <button onclick="resetLink()">Request reset link</button>
        <a href="../login/index.php" class="back-link">Back To Login</a>
      </div>
    </div>
  </div>
  <script src="scriptFP.js"></script>
</body>
</html>
