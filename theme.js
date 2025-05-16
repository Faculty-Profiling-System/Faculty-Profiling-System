// Initialize states
let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

// Check and apply theme and text size on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('plpTheme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
    // Apply saved text size
    document.body.style.fontSize = currentSize + '%';
});

function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
    localStorage.setItem('plpTheme', theme);
}

function setTextSize(size) {
    currentSize = size;
    document.body.style.fontSize = size + '%';
    localStorage.setItem('plpTextSize', size);
    if (typeof updateSelected === 'function') {
        updateSelected();
    }
}

function setTheme(theme) {
    applyTheme(theme);
    if (typeof updateSelected === 'function') {
        updateSelected();
    }
} 