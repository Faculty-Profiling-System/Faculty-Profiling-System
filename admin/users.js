function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../landing/index.php';
    }
}

function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function openEditModal(id, username) {
    console.log('Opening edit modal for:', id, username); // Debug log
    
    // Set the form values
    document.getElementById('edit_faculty_id').value = id;
    document.getElementById('edit_username').value = username;
    
    // Reset password change fields
    document.getElementById('new_password').value = '';
    
    // Show the modal
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('addModal').style.display = 'none';
    document.getElementById('editModal').style.display = 'none';
    
    // Reset forms
    document.getElementById('addUserForm').reset();
    document.getElementById('editUserForm').reset();
    
    // Hide validation messages
    document.getElementById('password-match-message').style.display = 'none';
    document.getElementById('faculty-id-error').style.display = 'none';
}

function confirmDelete(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        const formData = new FormData();
        formData.append('faculty_id', id);

        fetch('delete_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("User deleted successfully!");
                location.reload();
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => {
            console.error("Delete AJAX error:", error);
            alert("An unexpected error occurred while deleting.");
        });
    }
}

document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('add_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                alert('User added successfully!');
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        } catch (e) {
            console.error('Server did not return valid JSON:', text);
            alert('Server error:\n' + text);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    });
});

function handlePasswordChange() {
    const newPassword = document.getElementById('new_password').value;
    
    if (newPassword && !confirm('Are you sure you want to change this user\'s password? A temporary password will be emailed to them.')) {
        return false;
    }
    return true;
}

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const newPassword = document.getElementById('new_password').value;
    const username = document.getElementById('edit_username').value;
    const originalUsername = document.getElementById('edit_faculty_id').value;
    
    // Check if username or password have changed
    if (username === originalUsername && !newPassword) {
        alert('Please edit the username or password first');
        return false;
    }
    
    // If password is empty, generate a random one
    if (newPassword === '') {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('new_password').value = password;
        formData.set('new_password', password);
    }

    if (newPassword && !confirm('Are you sure you want to change this user\'s password? A temporary password will be emailed to them.')) {
        return false;
    }

    fetch('edit_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "success") {
            alert(newPassword ? "User updated successfully! A temporary password has been emailed to the user." : "User updated successfully!");
            location.reload();
        } else {
            alert("Error updating user: " + data);
        }
    })
    .catch(error => {
        console.error("AJAX error:", error);
        alert("An unexpected error occurred.");
    });
});

// Password toggle functionality
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = field.nextElementSibling.querySelector('svg');
    
    field.type = field.type === "password" ? "text" : "password";
    toggleIcon.innerHTML = field.type === "password" ?
        `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>` :
        `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
}

// Password matching validation
function validatePasswordMatch(form) {
    const password = form.password.value;
    const confirmPassword = form.confirm_password.value;
    const message = document.getElementById('password-match-message');

    if (password !== confirmPassword) {
        message.style.display = 'block';
        form.confirm_password.focus();
        return false;
    } else {
        message.style.display = 'none';
        return true;
    }
}

document.getElementById('addUserForm').addEventListener('submit', function(e) {
    if (!validatePasswordMatch(this)) {
        e.preventDefault();
    }
});

// Real-time password matching check
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    const message = document.getElementById('password-match-message');
    
    message.style.display = confirmPassword && password !== confirmPassword ? 'block' : 'none';
});

function validateFacultyID() {
    var facultyId = document.getElementById('faculty_id').value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "check_faculty_id.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = xhr.responseText.trim();

            if (response === "found") {
                document.getElementById('faculty-id-error').style.display = 'none';
            } else if (response === "not_found") {
                document.getElementById('faculty-id').innerText = facultyId;
                document.getElementById('faculty-id-error').style.display = 'block';
            } else {
                alert("An unexpected error occurred.");
            }
        }
    };

    xhr.send("faculty_id=" + encodeURIComponent(facultyId));
    return false; // Prevent default form submission
}

function generateRandomPassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    const passwordField = document.getElementById('new_password');
    passwordField.value = password;
    passwordField.type = 'text'; // Show the generated password
}

function clearPassword() {
    document.getElementById('new_password').value = '';
}