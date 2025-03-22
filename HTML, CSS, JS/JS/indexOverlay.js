// Function to open the overlay message - making it take over the whole screen
function showOverlay() {
    const overlay = document.getElementById('overlayMessage');
    if (overlay) {
        // Ensure it takes over the whole screen
        overlay.style.display = 'flex';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.zIndex = '1000';
        
        // Prevent scrolling of the background content
        document.body.style.overflow = 'hidden';
    }
}

// Function to close the overlay message
function closeOverlay() {
    const overlay = document.getElementById('overlayMessage');
    if (overlay) {
        overlay.style.display = 'none';
        
        // Re-enable scrolling
        document.body.style.overflow = 'auto';
    }
}

// Set up event listeners when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all the control buttons
    const rejectButton = document.querySelector('.control-btn.reject');
    const applyButton = document.querySelector('.control-btn.apply');
    const likeButton = document.querySelector('.control-btn.like');
    
    // Add click event listeners to each button to show the overlay
    if (rejectButton) {
        rejectButton.addEventListener('click', showOverlay);
    }
    
    if (applyButton) {
        applyButton.addEventListener('click', showOverlay);
    }
    
    if (likeButton) {
        likeButton.addEventListener('click', showOverlay);
    }
    
    // Set up the cancel button in the overlay
    const cancelButton = document.querySelector('.btn-secondary');
    if (cancelButton) {
        cancelButton.addEventListener('click', closeOverlay);
    }
    
    // Also close the overlay when clicking outside of its content
    const overlay = document.getElementById('overlayMessage');
    if (overlay) {
        overlay.addEventListener('click', function(event) {
            // Only close if clicking on the overlay background, not its content
            if (event.target === overlay) {
                closeOverlay();
            }
        });
    }

    // Ensure the overlay is properly styled initially
    if (overlay) {
        overlay.style.position = 'fixed';
        // overlay.style.top = '0';
        // overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        // overlay.style.zIndex = '1000';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';
    }
});

// Make closeOverlay available globally for the HTML buttons
window.closeOverlay = closeOverlay;