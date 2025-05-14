<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - PLP</title>
  <link rel="stylesheet" href="../css/ResetPassword.css">
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <img src="../images/log_in_page.png" alt="PLP Building" class="bg-image">
    </div>
    <div class="right-panel">
      <div class="header">
        <img src="../images/PLP_logo.png" alt="PLP Logo" class="logo">
        <h1>PAMANTASAN NG LUNGSOD NG PASIG</h1>
      </div>
      <div class="form-section">
        <h2>Reset your password</h2>
        <p class="description">
          Strong passwords include numbers, letters, and punctuation marks.
        </p>
        <form>
          <label for="new-password">Enter new password</label>
          <div class="input-group">
            <input type="password" id="new-password" placeholder="New Password">
            <span class="toggle-visibility">👁️</span>
          </div>

          <label for="confirm-password">Confirm new password</label>
          <div class="input-group">
            <input type="password" id="confirm-password" placeholder="New Password">
            <span class="toggle-visibility">👁️</span>
          </div>

          <button type="submit" class="reset-button">RESET PASSWORD</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
