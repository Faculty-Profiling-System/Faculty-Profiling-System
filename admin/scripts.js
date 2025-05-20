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
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    const messageBox = document.getElementById('addFacultyMessage');
    
    try {
      // Show loading state
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
      messageBox.style.display = 'none';

      // Validate form
      const requiredFields = ['faculty_id', 'full_name', 'email', 'password'];
      const missingFields = requiredFields.filter(field => !form.elements[field].value.trim());
      
      if (missingFields.length > 0) {
        throw new Error(`Please fill in all required fields: ${missingFields.join(', ')}`);
      }

      // Validate email format
      const email = form.elements['email'].value;
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        throw new Error('Please enter a valid email address');
      }

      // Validate faculty ID format (XX-XXXXX)
      const facultyId = form.elements['faculty_id'].value;
      if (!/^\d{2}-\d{5}$/.test(facultyId)) {
        throw new Error('Faculty ID must be in format XX-XXXXX');
      }

      // Get college_id from session or form data
      let college_id = form.dataset.collegeId;
      if (!college_id) {
        // Try to get it from a hidden input if not in dataset
        const collegeIdInput = form.querySelector('input[name="college_id"]');
        if (collegeIdInput) {
          college_id = collegeIdInput.value;
        }
      }

      if (!college_id) {
        throw new Error('College_id is required');
      }

      // Prepare form data
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      data.college_id = college_id; // Ensure college_id is included

      // Submit form
      const response = await fetch('add_faculty.php', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
      });

      const result = await response.json();
      
      if (!response.ok) {
        throw new Error(result.message || 'Server error occurred');
      }

      if (result.success) {
        showFormMessage('Faculty added successfully!', 'success');
        form.reset();
        
        // Redirect after delay
        setTimeout(() => {
          window.location.href = 'college_management.php';
        }, 1500);
      } else {
        throw new Error(result.message || 'Unknown error occurred');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      showFormMessage(error.message, 'error');
      
      // Highlight problematic fields
      if (error.message.includes('Faculty ID')) {
        form.elements['faculty_id'].classList.add('error-field');
      }
      if (error.message.includes('Email')) {
        form.elements['email'].classList.add('error-field');
      }
      if (error.message.includes('College_id')) {
        // You might want to highlight something if college_id is missing
        console.error('College ID is required but missing');
      }
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    }
  });
}

// ====== UTILITY FUNCTIONS ==========
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = '../landing/index.php';
  }
}
