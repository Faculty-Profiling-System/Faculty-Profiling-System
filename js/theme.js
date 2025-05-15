// Theme initialization
document.addEventListener('DOMContentLoaded', function() {
    // Apply saved theme on page load
    const savedTheme = localStorage.getItem('plpTheme') || 'light';
    applyTheme(savedTheme);

    // Listen for theme changes from other pages
    window.addEventListener('themeChanged', function(event) {
        applyTheme(event.detail.theme);
    });

    // Listen for storage changes (when theme is changed in another tab)
    window.addEventListener('storage', function(event) {
        if (event.key === 'plpTheme') {
            applyTheme(event.newValue);
        }
    });
});

function applyTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
}

// Export for use in other files
window.themeUtils = {
    applyTheme: applyTheme
}; 