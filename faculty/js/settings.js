// Initialize states
let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

// Check and apply theme and text size on page load
document.addEventListener('DOMContentLoaded', function() {
    // Apply theme
    const savedTheme = localStorage.getItem('plpTheme') || 'light';
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
    // Apply text size
    document.documentElement.style.fontSize = currentSize + '%';
    if (document.querySelector('.settings-section')) {
        updateSelected();
    }

    // Initialize collapsible sections if they exist
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
        coll[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var content = this.nextElementSibling;
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
            }
        });
    }
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
    document.documentElement.style.fontSize = size + '%';
    localStorage.setItem('plpTextSize', size);
    if (document.querySelector('.settings-section')) {
        updateSelected();
    }
}

function setTheme(theme) {
    applyTheme(theme);
    if (document.querySelector('.settings-section')) {
        updateSelected();
    }
}

function updateSelected() {
    // Text size buttons
    [100, 150, 200].forEach(s => {
        const btn = document.getElementById('size-' + s);
        if (btn) {
            btn.classList.toggle('selected', currentSize === s);
        }
    });
    // Theme buttons
    const currentTheme = localStorage.getItem('plpTheme') || 'light';
    const lightBtn = document.getElementById('theme-light');
    const darkBtn = document.getElementById('theme-dark');
    if (lightBtn) lightBtn.classList.toggle('selected', currentTheme === 'light');
    if (darkBtn) darkBtn.classList.toggle('selected', currentTheme === 'dark');
} 