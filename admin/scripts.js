document.addEventListener('DOMContentLoaded', () => {
  toggleMenu(); 
  updateDashboardCounts();
  fetchAdminData();
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

// Initialize menu state
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

// Dashboard Functions
function fetchAdminData() {
  fetch('get_admin_data.php')
    .then(response => response.json())
    .then(data => {
      document.getElementById('adminName').textContent = data.name || 'Admin';
    })
    .catch(error => {
      console.error('Error loading admin data:', error);
      document.getElementById('adminName').textContent = 'Admin';
    });
}

function fetchStatsData() {
  fetch('get_stats_data.php')
    .then(response => response.json())
    .then(data => {
      const statsContainer = document.getElementById('statsContainer');
      statsContainer.innerHTML = '';

      const stats = [
        { title: 'Total Faculty Members', value: data.total_faculty || 0 },
        { title: 'Total Female', value: data.total_female || 0 },
        { title: 'Total Male', value: data.total_male || 0 },
        { title: 'Full-Time', value: data.full_time || 0 },
        { title: 'Part-Time', value: data.part_time || 0 },
        { title: "Master's Degrees", value: data.masters_degrees || 0 },
        { title: 'Doctoral Degrees', value: data.doctoral_degrees || 0 }
      ];

      stats.forEach(stat => {
        const statCard = document.createElement('div');
        statCard.className = 'stat-card';
        statCard.innerHTML = `
          <h3>${stat.title}</h3>
          <div class="stat-value">${stat.value}</div>
        `;
        statsContainer.appendChild(statCard);
      });
    })
    .catch(error => {
      console.error('Error loading stats:', error);
      const statsContainer = document.getElementById('statsContainer');
      statsContainer.innerHTML = '';
      
      const defaultStats = [
        { title: 'Total Faculty Members', value: 0 },
        { title: 'Total Female', value: 0 },
        { title: 'Total Male', value: 0 },
        { title: 'Full-Time', value: 0 },
        { title: 'Part-Time', value: 0 },
        { title: "Master's Degrees", value: 0 },
        { title: 'Doctoral Degrees', value: 0 }
      ];

      defaultStats.forEach(stat => {
        const statCard = document.createElement('div');
        statCard.className = 'stat-card';
        statCard.innerHTML = `
          <h3>${stat.title}</h3>
          <div class="stat-value">${stat.value}</div>
        `;
        statsContainer.appendChild(statCard);
      });
    });
}

function fetchRecentCredentials() {
  fetch('get_recent_credentials.php')
    .then(response => response.json())
    .then(data => {
      populateCredentialList('documentUploadsList', data.new_uploads || []);
      populateCredentialList('pendingDocumentsList', data.pending_verifications || []);
      document.querySelectorAll('.credential-card .stat-value').forEach((el, index) => {
        const counts = [
          data.new_uploads?.length || 0,
          data.pending_verifications?.length || 0,
          data.accreditation_reviews?.length || 0
        ];
        el.textContent = counts[index];
      });
    })
    .catch(error => {
      console.error('Error loading credentials:', error);
      populateCredentialList('documentUploadsList', []);
      populateCredentialList('pendingDocumentsList', []);
    });
}


function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = '../landing/index.php';
  }
}

