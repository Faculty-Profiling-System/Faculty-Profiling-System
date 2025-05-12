function resetLink() {
    const email = document.getElementById("email").value;
    if (email.trim() === "") {
      alert("Please enter a valid email address.");
    } else {
      alert("Password reset link has been sent to " + email);
    }
  }
  