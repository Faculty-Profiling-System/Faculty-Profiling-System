// Function to apply filters to the table
function applyFilters() {
    const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
    const table = document.querySelector('.report-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const typeCell = rows[i].cells[2].textContent.toLowerCase();
        
        // Show row only if it matches all filters
        if ((typeFilter === '' || typeCell.includes(typeFilter))) {
            rows[i].style.display = '';
        } else {
            rows[i].style.display = 'none';
        }
    }
}

function viewCredentialFile(relativePath) {
    const fullPath = '../faculty/' + relativePath;
    window.open(fullPath, '_blank');
}

// Function to approve a credential
function approveCredential(credentialId) {
    if (confirm('Are you sure you want to approve this credential?')) {
        updateCredentialStatus(credentialId, 'Verified');
    }
}

// Function to reject a credential
function rejectCredential(credentialId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason !== null && reason.trim() !== '') {
        updateCredentialStatus(credentialId, 'Rejected', reason);
    } else if (reason !== null) {
        alert('Please provide a reason for rejection.');
        rejectCredential(credentialId); // Recursively call until valid reason or cancel
    }
}

// Function to update credential status via AJAX
function updateCredentialStatus(credentialId, status, reason = '') {
    console.log('Sending request with:', { credentialId, status, reason }); // Debug log
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_credential.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        console.log('Response received:', this.responseText); // Debug log
        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    alert(`Credential ${status} successfully!`);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Error processing response. See console for details.');
            }
        } else {
            alert(`Error: Server returned status ${this.status}`);
        }
    };
    
    xhr.onerror = function() {
        console.error('Request failed');
        alert('Request failed. Please check your connection.');
    };
    
    const params = new URLSearchParams();
    params.append('credential_id', credentialId);
    params.append('status', status);
    if (reason) params.append('reason', reason);
    
    console.log('Sending params:', params.toString()); // Debug log
    xhr.send(params.toString());
}

// Function to approve a teaching load
function approveTeachingLoad(loadId) {
    if (confirm('Are you sure you want to approve this teaching load?')) {
        updateTeachingLoadStatus(loadId, 'Verified');
    }
}

// Function to reject a teaching load
function rejectTeachingLoad(loadId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason !== null && reason.trim() !== '') {
        updateTeachingLoadStatus(loadId, 'Rejected', reason);
    } else if (reason !== null) {
        alert('Please provide a reason for rejection.');
        rejectTeachingLoad(loadId); // Recursively ask until valid or cancelled
    }
}

// Function to update teaching load status via AJAX
function updateTeachingLoadStatus(loadId, status, reason = '') {
    console.log('Sending request with:', { loadId, status, reason }); // Debug log
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_teachingload.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        console.log('Response received:', this.responseText); // Debug log
        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                    alert(`Teaching Load ${status} successfully!`);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Error processing response. See console for details.');
            }
        } else {
            alert(`Error: Server returned status ${this.status}`);
        }
    };
    
    xhr.onerror = function() {
        console.error('Request failed');
        alert('Request failed. Please check your connection.');
    };
    
    const params = new URLSearchParams();
    params.append('load_id', loadId);
    params.append('status', status);
    if (reason) params.append('reason', reason);
    
    console.log('Sending params:', params.toString()); // Debug log
    xhr.send(params.toString());
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    applyFilters();
});

