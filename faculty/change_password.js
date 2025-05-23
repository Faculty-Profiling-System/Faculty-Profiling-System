document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('changePasswordForm');
    const messageDiv = document.getElementById('passwordMessage');
    const newPasswordInput = document.getElementById('newPassword');
    const strengthBar = document.querySelector('.strength-bar');
    const passwordTooltip = document.getElementById('passwordTooltip');
    
    // Show tooltip when new password field is focused
    newPasswordInput.addEventListener('focus', function() {
        passwordTooltip.classList.add('show');
    });

    // Hide tooltip when new password field loses focus
    newPasswordInput.addEventListener('blur', function() {
        passwordTooltip.classList.remove('show');
    });
    
    function showMessage(message, isError = false) {
        messageDiv.className = 'message-box ' + (isError ? 'error' : 'success') + ' show';
        messageDiv.innerHTML = `<i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i> ${message}`;
    }

    function hideMessage() {
        messageDiv.className = 'message-box';
    }
    
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;
        
        // Complexity checks
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        strengthBar.className = 'strength-bar';
        if (strength < 3) {
            strengthBar.classList.add('weak');
        } else if (strength < 5) {
            strengthBar.classList.add('medium');
        } else {
            strengthBar.classList.add('strong');
        }
    }

    // Password strength check on input
    newPasswordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
    });
    
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        hideMessage();
        
        // Basic validation
        if (newPassword !== confirmPassword) {
            showMessage('New passwords do not match!', true);
            return;
        }
        
        if (newPassword.length < 8) {
            showMessage('Password must be at least 8 characters long!', true);
            return;
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        
        // Disable submit button and show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
        
        // Send request to change password
        fetch('change_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showMessage('Password changed successfully!');
                passwordForm.reset();
                strengthBar.className = 'strength-bar';
                // Redirect after 2 seconds with a force refresh
                setTimeout(() => {
                    window.location.href = window.location.href;
                }, 2000);
            } else {
                showMessage(data.message || 'Failed to change password', true);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while changing the password. Please try again.', true);
        })
        .finally(() => {
            // Re-enable submit button and restore text
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-key"></i> Change Password';
        });
    });
}); 

// Password visibility toggle function
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye';
        button.setAttribute('aria-label', `Hide ${input.previousElementSibling.textContent}`);
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye-slash';
        button.setAttribute('aria-label', `Show ${input.previousElementSibling.textContent}`);
    }
} 