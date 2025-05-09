// report.js
function searchFaculty() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.querySelector('.report-table');
    const tr = table.getElementsByTagName('tr');
  
    for (let i = 1; i < tr.length; i++) {
      const td = tr[i].getElementsByTagName('td')[1]; // Name column
      if (td) {
        const txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = '';
        } else {
          tr[i].style.display = 'none';
        }
      }
    }
  }
  
  function applyFilters() {
    const collegeFilter = document.getElementById('collegeFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const table = document.querySelector('.report-table');
    const tr = table.getElementsByTagName('tr');
  
    for (let i = 1; i < tr.length; i++) {
      const collegeTd = tr[i].getElementsByTagName('td')[2]; // College column
      const typeTd = tr[i].getElementsByTagName('td')[3]; // Type column
      
      if (collegeTd && typeTd) {
        const collegeMatch = collegeFilter === '' || collegeTd.textContent === collegeFilter;
        const typeMatch = typeFilter === '' || typeTd.textContent === typeFilter;
        
        if (collegeMatch && typeMatch) {
          tr[i].style.display = '';
        } else {
          tr[i].style.display = 'none';
        }
      }
    }
  }
  
  function approveCredential(credentialId) {
      if (confirm('Approve this credential?')) {
          fetch('process_credential.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `action=approve&credential_id=${credentialId}`
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Credential approved!');
                  location.reload();
              } else {
                  alert('Error: ' + data.message);
              }
          });
      }
  }

  function rejectCredential(credentialId) {
      const reason = prompt('Enter rejection reason:');
      if (reason !== null) {
          fetch('process_credential.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `action=reject&credential_id=${credentialId}&reason=${encodeURIComponent(reason)}`
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert('Credential rejected!');
                  location.reload();
              } else {
                  alert('Error: ' + data.message);
              }
          });
      }
  }