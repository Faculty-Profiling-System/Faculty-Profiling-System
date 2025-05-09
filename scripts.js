document.addEventListener('DOMContentLoaded', () => {
    toggleMenu(); 
    });
  
function resetLink() {
  const email = document.getElementById("email").value;
  if (email.trim() === "") {
    alert("Please enter a valid email address.");
  } else {
    alert("Password reset link has been sent to " + email);
  }
}
  
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