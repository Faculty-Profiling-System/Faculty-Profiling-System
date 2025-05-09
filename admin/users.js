function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
      window.location.href = '../landing/index.php';
    }
  }

  function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
  }

  function openEditModal(id, college_id, name, email, username) {
      document.getElementById('edit_faculty_id').value = id;
      document.getElementById('edit_name').value = name;
      document.getElementById('edit_email').value = email;
      document.getElementById('edit_username').value = username;

      // Pre-select the current college_id in the dropdown
      var collegeSelect = document.querySelector('[name="college_id"]');
      for (var i = 0; i < collegeSelect.options.length; i++) {
          if (collegeSelect.options[i].value == college_id) {
              collegeSelect.options[i].selected = true;
              break;
          }
      }

      document.getElementById('editModal').style.display = 'block';
  }

  function closeModal() {
    document.getElementById('addModal').style.display = 'none';
    document.getElementById('editModal').style.display = 'none';
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
      .then(response => {
          if (!response.ok) {
              throw new Error('Network response was not ok');
          }
          return response.json(); // Parse as JSON
      })
      .then(data => {
          if (data.status === 'success') {
              alert(data.message);
              window.location.reload(); // Reload the page
          } else {
              alert('Error: ' + data.message);
          }
      })
      .catch(error => {
          console.error('Error:', error);
          alert('An error occurred: ' + error.message);
      });
  });

  document.getElementById('editUserForm').addEventListener('submit', function(e) {
      e.preventDefault(); // Prevent default form submission

      const formData = new FormData(this);

      fetch('edit_user.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.text())
      .then(data => {
          if (data.trim() === "success") {
              alert("User updated successfully!");
              location.reload(); // Reload page on success
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
      
      if (field.type === "password") {
          field.type = "text";
          toggleIcon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
      } else {
          field.type = "password";
          toggleIcon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
      }
  }

  // Password matching validation
  document.getElementById('addUserForm').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      const message = document.getElementById('password-match-message');
      
      if (password !== confirmPassword) {
          e.preventDefault();
          message.style.display = 'block';
          document.getElementById('confirm_password').focus();
      } else {
          message.style.display = 'none';
      }
  });

  // Real-time password matching check
  document.getElementById('confirm_password').addEventListener('input', function() {
      const password = document.getElementById('password').value;
      const confirmPassword = this.value;
      const message = document.getElementById('password-match-message');
      
      if (confirmPassword && password !== confirmPassword) {
          message.style.display = 'block';
      } else {
          message.style.display = 'none';
      }
  });