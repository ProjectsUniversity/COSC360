document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        let isValid = true;
        
        if (!email || !validateEmail(email)) {
            showError('email', 'Please enter a valid email address');
            isValid = false;
        } else {
            removeError('email');
        }
        
        if (!password || password.length < 8) {
            showError('password', 'Password must be at least 8 characters long');
            isValid = false;
        } else {
            removeError('password');
        }
        
        if (isValid) {
            form.submit();
        }
    });
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error') || createErrorDiv(fieldId);
    errorDiv.textContent = message;
    field.classList.add('error');
}

function removeError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(fieldId + 'Error');
    if (errorDiv) errorDiv.remove();
    field.classList.remove('error');
}

function createErrorDiv(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.id = fieldId + 'Error';
    errorDiv.className = 'error-message';
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
    return errorDiv;
}