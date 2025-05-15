// Initialize text size state
let currentSize = parseInt(localStorage.getItem('plpTextSize')) || 100;

// Apply text size on page load
document.addEventListener('DOMContentLoaded', function() {
    applyTextSize();
});

// Function to apply text size
function applyTextSize() {
    const size = localStorage.getItem('plpTextSize') || '100';
    document.documentElement.style.fontSize = size + '%';
    
    // Update buttons if they exist
    updateTextSizeButtons();
}

// Function to set text size
function setTextSize(size) {
    currentSize = size;
    document.documentElement.style.fontSize = size + '%';
    localStorage.setItem('plpTextSize', size);
    updateTextSizeButtons();
}

// Function to update text size buttons if they exist
function updateTextSizeButtons() {
    [100, 150, 200].forEach(s => {
        const btn = document.getElementById('size-' + s);
        if (btn) {
            btn.classList.toggle('selected', currentSize === s);
        }
    });
} 