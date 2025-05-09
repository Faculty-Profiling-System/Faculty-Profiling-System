// department.js
document.addEventListener('DOMContentLoaded', () => {
    toggleMenu(); 
    updateDashboardCounts();
    fetchAdminData();
    fetchStatsData();
    fetchRecentCredentials();
    });
  
  
  // ====== MENU TOGGLE ==========
  function toggleMenu() {
    const menu = document.getElementById('menu');
    const body = document.body;
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
  
    if (!bar1.style.transform) {
      bar1.style.transform = 'rotate(0) translate(0)';
      bar2.style.opacity = '1';
      bar3.style.transform = 'rotate(0) translate(0)';
    }
  
    if (menu.classList.contains('active')) {
      menu.classList.remove('active');
      body.classList.remove('menu-open');
      bar1.style.transform = 'rotate(0) translate(0)';
      bar2.style.opacity = '1';
      bar3.style.transform = 'rotate(0) translate(0)';
    } else {
      menu.classList.add('active');
      body.classList.add('menu-open');
      bar1.style.transform = 'rotate(45deg) translate(5px, 5px)';
      bar2.style.opacity = '0';
      bar3.style.transform = 'rotate(-45deg) translate(7px, -6px)';
    }
  }
  
  document.addEventListener('DOMContentLoaded', function() {
    const menu = document.getElementById('menu');
    const bar1 = document.getElementById('bar1');
    const bar2 = document.getElementById('bar2');
    const bar3 = document.getElementById('bar3');
    
    menu.classList.remove('active');
    bar1.style.transform = 'rotate(0) translate(0)';
    bar2.style.opacity = '1';
    bar3.style.transform = 'rotate(0) translate(0)';
  });

  //COLLEGE MANAGEMENT

// department.js
function showAddCollegeModal() {
  document.getElementById('addCollegeModal').style.display = 'block';
}

function hideAddCollegeModal() {
  document.getElementById('addCollegeModal').style.display = 'none';
  document.getElementById('collegeName').value = '';
}

function showEditCollegeModal() {
  document.getElementById('editCollegeModal').style.display = 'block';
}

function hideEditCollegeModal() {
  document.getElementById('editCollegeModal').style.display = 'none';
}

function addCollege() {
  const collegeName = document.getElementById('collegeName').value.trim();
  
  if (!collegeName) {
    alert('Please enter a college name');
    return;
  }

  fetch('add_college.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `college_name=${encodeURIComponent(collegeName)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      hideAddCollegeModal();
      loadColleges();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to add college. Please try again.');
  });
}

function editCollege(id, name) {
  document.getElementById('editCollegeId').value = id;
  document.getElementById('editCollegeName').value = name;
  showEditCollegeModal();
}

function updateCollege() {
  const id = document.getElementById('editCollegeId').value;
  const collegeName = document.getElementById('editCollegeName').value.trim();
  
  if (!collegeName) {
    alert('Please enter a college name');
    return;
  }

  fetch('update_college.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `college_id=${id}&college_name=${encodeURIComponent(collegeName)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      hideEditCollegeModal();
      loadColleges();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to update college. Please try again.');
  });
}

function deleteCollege(id) {
  if (!confirm('Are you sure you want to delete this college?')) {
    return;
  }

  fetch('delete_college.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `college_id=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      loadColleges();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to delete college. Please try again.');
  });
}

function loadColleges() {
  fetch('get_colleges.php')
    .then(response => response.json())
    .then(data => {
      const tbody = document.querySelector('#collegesTable tbody');
      tbody.innerHTML = '';

      if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">No colleges found</td></tr>';
        return;
      }

      data.forEach(college => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${college.college_id}</td>
          <td>${college.college_name}</td>
          <td class="actions">
            <button class="edit-btn" onclick="editCollege(${college.college_id}, '${college.college_name.replace(/'/g, "\\'")}')">EDIT</button>
            <button class="delete-btn" onclick="deleteCollege(${college.college_id})">DELETE</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    })
    .catch(error => {
      console.error('Error loading colleges:', error);
      const tbody = document.querySelector('#collegesTable tbody');
      tbody.innerHTML = '<tr><td colspan="3">Error loading colleges</td></tr>';
    });
}

// Load colleges when page loads
document.addEventListener('DOMContentLoaded', loadColleges);