
// Help Popout Functions
function toggleHelpPopout() {
const helpPopout = document.getElementById('helpPopout');
    if (helpPopout.style.display === 'block') {
        helpPopout.style.display = 'none';
    } else {
        closeAllPopouts();
        helpPopout.style.display = 'block';
    }
}

function closeHelpPopout() {
    document.getElementById('helpPopout').style.display = 'none';
}
    
function openFaqPopout() {
    closeHelpPopout();
    document.getElementById('faqPopout').style.display = 'block';
}

function closeFaqPopout() {
    document.getElementById('faqPopout').style.display = 'none';
}

function openContactPopout() {
    closeHelpPopout();
    document.getElementById('contactPopout').style.display = 'block';
}

function closeContactPopout() {
    document.getElementById('contactPopout').style.display = 'none';
}

function closeAllPopouts() {
    closeHelpPopout();
    closeFaqPopout();
    closeContactPopout();
}

// Close popouts when clicking outside
document.addEventListener('click', function(event) {
const helpButton = document.querySelector('.help-button');
const helpPopout = document.getElementById('helpPopout');
const faqPopout = document.getElementById('faqPopout');
const contactPopout = document.getElementById('contactPopout');

if (!event.target.closest('.help-button') && 
    !event.target.closest('.popout') && 
    !event.target.closest('.content-popout')) {
    closeAllPopouts();
    }
});