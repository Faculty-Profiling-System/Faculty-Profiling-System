<!-- Help Button -->
<div class="help-button" onclick="toggleHelpPopout()">
    <i class="fas fa-question"></i>
</div>

<!-- Main Help Popout -->
<div id="helpPopout" class="popout">
    <div class="popout-header">
        <h3>Need Help?</h3>
        <span class="popout-close" onclick="closeHelpPopout()">&times;</span>
    </div>
    <div class="help-option" onclick="openFaqPopout()">
        <i class="fas fa-question-circle"></i> FAQ's
    </div>
    <div class="help-option" onclick="openContactPopout()">
        <i class="fas fa-headset"></i> Still need help?
    </div>
</div>

<!-- FAQ Popout -->
<div id="faqPopout" class="content-popout">
    <div class="popout-header">
        <h3>Frequently Asked Questions</h3>
        <span class="popout-close" onclick="closeFaqPopout()">&times;</span>
    </div>
    <div class="faq-item">
        <div class="faq-question">Q: How do I update my profile information?</div>
        <p>A: Go to the Profile section and click on the "Edit Profile" button.</p>
    </div>
    <div class="faq-item">
        <div class="faq-question">Q: How do I upload my teaching schedule?</div>
        <p>A: Navigate to Teaching Load section and use the "Upload Schedule" button.</p>
    </div>
    <div class="faq-item">
        <div class="faq-question">Q: What file formats are accepted?</div>
        <p>A: We accept PDF, JPG, and PNG files for credential uploads.</p>
    </div>
    <div class="faq-item">
        <div class="faq-question">Q: How do I change my password?</div>
        <p>A: Go to Settings and use the "Change Password" option.</p>
    </div>
</div>

<!-- Contact Popout -->
<div id="contactPopout" class="content-popout">
    <div class="popout-header">
        <h3>Contact Support</h3>
        <span class="popout-close" onclick="closeContactPopout()">&times;</span>
    </div>
    <p>If you need further assistance:</p>
    <div class="contact-info">
        <p><i class="fas fa-envelope"></i> support@plpasig.edu.ph</p>
        <p><i class="fas fa-phone"></i> +63 2 123 4567</p>
        <p><i class="fas fa-clock"></i> Mon-Fri, 8:00 AM - 5:00 PM</p>
        <p><i class="fas fa-map-marker-alt"></i> Admin Building, Room 101</p>
    </div>
</div>