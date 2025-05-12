// Profile Picture Upload
document.getElementById('profile-upload').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('profile-picture').src = e.target.result;
            // Here you would typically upload the image to the server
            uploadProfilePicture(file);
        }
        
        reader.readAsDataURL(file);
    }
});

function uploadProfilePicture(file) {
    const formData = new FormData();
    formData.append('profile_picture', file);
    
    fetch('../api/upload_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Profile picture updated successfully');
        } else {
            showToast('Error updating profile picture: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Network error: ' + error, 'error');
    });
}

// Edit Mode Toggle
let editMode = false;

function toggleEditMode() {
    editMode = !editMode;
    const btn = document.querySelector('.edit-profile-btn');
    if (editMode) {
        btn.textContent = 'Cancel Editing';
        document.querySelectorAll('.edit-section-btn').forEach(btn => {
            btn.style.display = 'none';
        });
        document.querySelectorAll('.section-content').forEach(section => {
            enableSectionEdit(section.id);
        });
    } else {
        btn.textContent = 'Edit Profile';
        document.querySelectorAll('.edit-section-btn').forEach(btn => {
            btn.style.display = 'block';
        });
        document.querySelectorAll('.section-content').forEach(section => {
            cancelSectionEdit(section.id);
        });
    }
}

// Section Editing
function toggleSectionEdit(sectionId) {
    const section = document.getElementById(sectionId);
    const isEditing = section.querySelector('.info-edit').style.display !== 'none';
    
    if (isEditing) {
        cancelSectionEdit(sectionId);
    } else {
        enableSectionEdit(sectionId);
    }
}

function enableSectionEdit(sectionId) {
    const section = document.getElementById(sectionId);
    section.querySelectorAll('.info-value').forEach(el => {
        el.style.display = 'none';
    });
    section.querySelectorAll('.info-edit').forEach(el => {
        el.style.display = 'block';
    });
    section.querySelector('.section-actions').style.display = 'flex';
}

function cancelSectionEdit(sectionId) {
    const section = document.getElementById(sectionId);
    section.querySelectorAll('.info-value').forEach(el => {
        el.style.display = 'inline';
    });
    section.querySelectorAll('.info-edit').forEach(el => {
        el.style.display = 'none';
    });
    section.querySelector('.section-actions').style.display = 'none';
}

function saveSection(sectionId) {
    const section = document.getElementById(sectionId);
    const data = {};
    
    section.querySelectorAll('.info-edit').forEach(input => {
        const field = input.id.replace('-edit', '');
        data[field] = input.value;
    });
    
    // Send data to server
    fetch('../api/update_profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update displayed values
            section.querySelectorAll('.info-edit').forEach(input => {
                const field = input.id.replace('-edit', '');
                const displayEl = document.getElementById(field + '-display');
                if (displayEl) {
                    displayEl.textContent = input.value;
                    
                    // Special formatting for certain fields
                    if (field === 'birthday') {
                        displayEl.textContent = formatDate(input.value);
                    }
                }
            });
            
            cancelSectionEdit(sectionId);
            showToast('Profile updated successfully');
        } else {
            showToast('Error updating profile: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Network error: ' + error, 'error');
    });
}

// Academic Background Functions
function addAcademicBackground() {
    document.getElementById('degree-id').value = '';
    document.getElementById('degree-form').reset();
    document.getElementById('modal-title').textContent = 'Add Academic Degree';
    openModal();
}

function editDegree(button) {
    const degreeItem = button.closest('.degree-item');
    const degreeId = degreeItem.dataset.id;
    
    // In a real app, you would fetch this data from the server or store it in data attributes
    // For this example, we'll simulate getting the data
    const degreeData = {
        degree_level: degreeItem.querySelector('p:nth-child(1)').textContent.replace('Degree Level: ', ''),
        degree_title: degreeItem.querySelector('h3').textContent.split(' in ')[0],
        field_of_study: degreeItem.querySelector('h3').textContent.split(' in ')[1],
        institution_name: degreeItem.querySelector('p:nth-child(2)').textContent.replace('Institution: ', ''),
        thesis_title: degreeItem.querySelector('p:nth-child(4)') ? 
            degreeItem.querySelector('p:nth-child(4)').textContent.replace('Thesis/Dissertation: ', '') : '',
        honors: degreeItem.querySelector('p:nth-child(5)') ? 
            degreeItem.querySelector('p:nth-child(5)').textContent.replace('Honors: ', '') : '',
        start_year: degreeItem.querySelector('p:nth-child(3)') ? 
            degreeItem.querySelector('p:nth-child(3)').textContent.split(' - ')[0].replace('Years: ', '') : '',
        end_year: degreeItem.querySelector('p:nth-child(3)') ? 
            degreeItem.querySelector('p:nth-child(3)').textContent.split(' - ')[1] : ''
    };
    
    document.getElementById('degree-id').value = degreeId;
    document.getElementById('degree-level').value = degreeData.degree_level;
    document.getElementById('degree-title').value = degreeData.degree_title;
    document.getElementById('field-of-study').value = degreeData.field_of_study;
    document.getElementById('institution-name').value = degreeData.institution_name;
    document.getElementById('thesis-title').value = degreeData.thesis_title;
    document.getElementById('honors').value = degreeData.honors;
    document.getElementById('start-year').value = degreeData.start_year;
    document.getElementById('end-year').value = degreeData.end_year;
    
    document.getElementById('modal-title').textContent = 'Edit Academic Degree';
    openModal();
}

function deleteDegree(degreeId) {
    if (confirm('Are you sure you want to delete this academic record?')) {
        fetch(`../api/delete_degree.php?id=${degreeId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`.degree-item[data-id="${degreeId}"]`).remove();
                showToast('Academic record deleted successfully');
            } else {
                showToast('Error deleting record: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showToast('Network error: ' + error, 'error');
        });
    }
}

// Modal Functions
function openModal() {
    document.getElementById('degree-modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('degree-modal').style.display = 'none';
}

// Handle form submission
document.getElementById('degree-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const degreeId = document.getElementById('degree-id').value;
    const isEdit = degreeId !== '';
    
    const formData = {
        degree_level: document.getElementById('degree-level').value,
        degree_title: document.getElementById('degree-title').value,
        field_of_study: document.getElementById('field-of-study').value,
        institution_name: document.getElementById('institution-name').value,
        thesis_title: document.getElementById('thesis-title').value,
        honors: document.getElementById('honors').value,
        start_year: document.getElementById('start-year').value,
        end_year: document.getElementById('end-year').value
    };
    
    const url = isEdit ? `../api/update_degree.php?id=${degreeId}` : '../api/add_degree.php';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            showToast(isEdit ? 'Degree updated successfully' : 'Degree added successfully');
            
            // Refresh the academic background section
            if (isEdit) {
                // In a real app, you would update the existing item
                loadAcademicBackground();
            } else {
                // Add the new degree to the list
                loadAcademicBackground();
            }
        } else {
            showToast('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Network error: ' + error, 'error');
    });
});

// Utility Functions
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function loadAcademicBackground() {
    fetch('../api/get_degrees.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const container = document.getElementById('academic-background');
            container.innerHTML = '';
            
            if (data.degrees.length === 0) {
                container.innerHTML = '<p class="no-data">No academic background information added yet.</p>';
                return;
            }
            
            data.degrees.forEach(degree => {
                const degreeItem = document.createElement('div');
                degreeItem.className = 'degree-item';
                degreeItem.dataset.id = degree.id;
                
                degreeItem.innerHTML = `
                    <div class="degree-header">
                        <h3>${escapeHtml(degree.degree_title)} in ${escapeHtml(degree.field_of_study)}</h3>
                        <div class="degree-actions">
                            <button class="edit-btn" onclick="editDegree(this)"><i class="fas fa-edit"></i></button>
                            <button class="delete-btn" onclick="deleteDegree(${degree.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="degree-details">
                        <p><strong>Degree Level:</strong> ${degree.degree_level}</p>
                        <p><strong>Institution:</strong> ${escapeHtml(degree.institution_name)}</p>
                        ${degree.start_year && degree.end_year ? 
                            `<p><strong>Years:</strong> ${degree.start_year} - ${degree.end_year}</p>` : ''}
                        ${degree.thesis_title ? 
                            `<p><strong>Thesis/Dissertation:</strong> ${escapeHtml(degree.thesis_title)}</p>` : ''}
                        ${degree.honors ? 
                            `<p><strong>Honors:</strong> ${escapeHtml(degree.honors)}</p>` : ''}
                    </div>
                `;
                
                container.appendChild(degreeItem);
            });
        } else {
            showToast('Error loading academic background: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showToast('Network error: ' + error, 'error');
    });
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === document.getElementById('degree-modal')) {
            closeModal();
        }
    });
    
    // Load academic background data
    loadAcademicBackground();
});