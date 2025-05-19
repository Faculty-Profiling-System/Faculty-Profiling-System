document.addEventListener('DOMContentLoaded', () => {
  // Initialize menu state
  const menu = document.getElementById('menu');
  const bar1 = document.getElementById('bar1');
  const bar2 = document.getElementById('bar2');
  const bar3 = document.getElementById('bar3');
  
  if (menu && bar1 && bar2 && bar3) {
    menu.classList.remove('active');
    bar1.style.transform = 'rotate(0) translate(0)';
    bar2.style.opacity = '1';
    bar3.style.transform = 'rotate(0) translate(0)';
  }

  // Load initial data
  updateDashboardCounts();
  fetchAdminData();

  // Initialize form submission if form exists
  const addFacultyForm = document.getElementById('addFacultyForm');
  if (addFacultyForm) {
    initializeFacultyForm(addFacultyForm);
  }
});

// ====== MENU TOGGLE ==========
function toggleMenu() {
  const menu = document.getElementById('menu');
  const body = document.body;
  const bar1 = document.getElementById('bar1');
  const bar2 = document.getElementById('bar2');
  const bar3 = document.getElementById('bar3');

  if (!menu || !body || !bar1 || !bar2 || !bar3) return;

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

// ====== DASHBOARD FUNCTIONS ==========
function fetchAdminData() {
  fetch('get_admin_data.php')
    .then(response => {
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    })
    .then(data => {
      const adminNameElement = document.getElementById('adminName');
      if (adminNameElement) {
        adminNameElement.textContent = data.name || 'Admin';
      }
    })
    .catch(error => {
      console.error('Error loading admin data:', error);
      const adminNameElement = document.getElementById('adminName');
      if (adminNameElement) {
        adminNameElement.textContent = 'Admin';
      }
    });
}

function updateDashboardCounts() {
  fetchStatsData();
  fetchRecentCredentials();
}

function fetchStatsData() {
  fetch('get_stats_data.php')
    .then(response => {
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    })
    .then(data => {
      const statsContainer = document.getElementById('statsContainer');
      if (!statsContainer) return;

      renderStatsCards(statsContainer, [
        { title: 'Total Faculty Members', value: data.total_faculty || 0 },
        { title: 'Total Female', value: data.total_female || 0 },
        { title: 'Total Male', value: data.total_male || 0 },
        { title: 'Full-Time', value: data.full_time || 0 },
        { title: 'Part-Time', value: data.part_time || 0 },
        { title: "Master's Degrees", value: data.masters_degrees || 0 },
        { title: 'Doctoral Degrees', value: data.doctoral_degrees || 0 }
      ]);
    })
    .catch(error => {
      console.error('Error loading stats:', error);
      const statsContainer = document.getElementById('statsContainer');
      if (!statsContainer) return;
      
      renderStatsCards(statsContainer, [
        { title: 'Total Faculty Members', value: 0 },
        { title: 'Total Female', value: 0 },
        { title: 'Total Male', value: 0 },
        { title: 'Full-Time', value: 0 },
        { title: 'Part-Time', value: 0 },
        { title: "Master's Degrees", value: 0 },
        { title: 'Doctoral Degrees', value: 0 }
      ]);
    });
}

function renderStatsCards(container, stats) {
  container.innerHTML = '';
  stats.forEach(stat => {
    const statCard = document.createElement('div');
    statCard.className = 'stat-card';
    statCard.innerHTML = `
      <h3>${stat.title}</h3>
      <div class="stat-value">${stat.value}</div>
    `;
    container.appendChild(statCard);
  });
}

function populateCredentialList(elementId, items) {
  const listElement = document.getElementById(elementId);
  if (!listElement) return;

  listElement.innerHTML = '';

  if (!items || items.length === 0) {
    const noItemsElement = document.createElement('li');
    noItemsElement.className = 'no-pending';
    noItemsElement.textContent = 'No items found';
    listElement.appendChild(noItemsElement);
    return;
  }

  items.forEach(item => {
    const listItem = document.createElement('li');
    listItem.innerHTML = `
      <span class="credential-type">${item.type || 'Document'}</span>
      <span class="credential-name">${item.name || 'Untitled'}</span>
      <span class="credential-date">${item.date || 'No date'}</span>
    `;
    listElement.appendChild(listItem);
  });
}

function fetchRecentCredentials() {
  fetch('get_recent_credentials.php')
    .then(response => {
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    })
    .then(data => {
      populateCredentialList('documentUploadsList', data.new_uploads || []);
      populateCredentialList('pendingDocumentsList', data.pending_verifications || []);
      
      updateCredentialCounts([
        data.new_uploads?.length || 0,
        data.pending_verifications?.length || 0,
        data.accreditation_reviews?.length || 0
      ]);
    })
    .catch(error => {
      console.error('Error loading credentials:', error);
      populateCredentialList('documentUploadsList', []);
      populateCredentialList('pendingDocumentsList', []);
      updateCredentialCounts([0, 0, 0]);
    });
}

function updateCredentialCounts(counts) {
  document.querySelectorAll('.credential-card .stat-value').forEach((el, index) => {
    if (index < counts.length) {
      el.textContent = counts[index];
    }
  });
}

// ====== FORM HANDLING ==========
function initializeFacultyForm(form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    const requiredFields = ['faculty_id', 'full_name', 'email', 'password'];
    const missingFields = requiredFields.filter(field => !form.elements[field].value.trim());
    
    if (missingFields.length > 0) {
      showFormError(`Please fill in all required fields: ${missingFields.join(', ')}`);
      return;
    }

    // Prepare form data
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Add college_id
    if (typeof CURRENT_COLLEGE_ID !== 'undefined' && CURRENT_COLLEGE_ID !== null) {
      data.college_id = CURRENT_COLLEGE_ID;
    } else {
      showFormError('College ID is missing');
      return;
    }

    // Submit form
    submitFacultyForm(data)
      .then(handleFormSuccess)
      .catch(handleFormError);
  });
}

function submitFacultyForm(data) {
  return fetch('add_faculty.php', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
  })
  .then(async response => {
    const text = await response.text();
    
    try {
      const data = text ? JSON.parse(text) : {};
      if (!response.ok) {
        throw new Error(data.message || 'Server error occurred');
      }
      return data;
    } catch (e) {
      console.error('Failed to parse JSON:', text);
      throw new Error('Invalid server response');
    }
  });
}

function handleFormSuccess(data) {
  const messageBox = document.getElementById('addFacultyMessage');
  if (!messageBox) return;
  
  messageBox.style.display = 'block';
  
  if (data.success) {
    messageBox.style.backgroundColor = '#d4edda';
    messageBox.style.color = '#155724';
    messageBox.textContent = data.message || 'Faculty added successfully!';
    
    // Clear form
    document.getElementById('addFacultyForm').reset();
    
    // Redirect after delay
    setTimeout(() => {
      window.location.href = 'college_management.php';
    }, 1500);
  } else {
    handleFormError(new Error(data.message || 'Unknown error occurred'));
  }
}

function handleFormError(error) {
  console.error('Form submission error:', error);
  const messageBox = document.getElementById('addFacultyMessage');
  if (messageBox) {
    messageBox.style.display = 'block';
    messageBox.style.backgroundColor = '#f8d7da';
    messageBox.style.color = '#721c24';
    messageBox.textContent = error.message || 'An error occurred while adding faculty.';
    
    // Show more details in console for debugging
    console.log('Full error:', {
      name: error.name,
      message: error.message,
      stack: error.stack
    });
  }
}

function showFormError(message) {
  const messageBox = document.getElementById('addFacultyMessage');
  if (messageBox) {
    messageBox.style.display = 'block';
    messageBox.style.backgroundColor = '#f8d7da';
    messageBox.style.color = '#721c24';
    messageBox.textContent = message;
  }
}

// ====== UTILITY FUNCTIONS ==========
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = '../landing/index.php';
  }
}
