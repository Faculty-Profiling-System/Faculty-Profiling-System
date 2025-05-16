document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('changePasswordForm');
    const messageDiv = document.getElementById('passwordMessage');
    
    function showMessage(message, isError = false) {
        messageDiv.style.display = 'block';
        messageDiv.style.backgroundColor = isError ? '#ffebee' : '#e8f5e9';
        messageDiv.style.color = isError ? '#c62828' : '#2e7d32';
        messageDiv.style.border = `1px solid ${isError ? '#ffcdd2' : '#c8e6c9'}`;
        messageDiv.textContent = message;
    }
    
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        // Reset message
        messageDiv.style.display = 'none';
        
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
        submitButton.textContent = 'Changing Password...';
        
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
                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.reload();
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
            submitButton.textContent = originalText;
        });
    });
}); 