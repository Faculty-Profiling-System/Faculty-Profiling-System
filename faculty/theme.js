// Theme initialization
document.addEventListener('DOMContentLoaded', function() {
    // Apply theme
    const savedTheme = localStorage.getItem('plpTheme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
    
    // Apply text size if it exists
    const savedTextSize = localStorage.getItem('plpTextSize');
    if (savedTextSize) {
        document.body.style.fontSize = savedTextSize + '%';
    }
}); 