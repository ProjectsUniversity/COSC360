document.addEventListener("DOMContentLoaded", function() {
    const recruiterLoginForm = document.getElementById("recruiterLoginForm");
    
    function validateLoginForm(event) {
        event.preventDefault();
        
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        
        clearErrorMessages();
        
        let isValid = true;
        
        if (!email) {
            displayError("email", "Email is required");
            isValid = false;
        }
        
        if (!password) {
            displayError("password", "Password is required");
            isValid = false;
        }
        
        if (isValid) {
            alert("Login successful!");
        }
        
        return false; 
    }
    
    
    if (recruiterLoginForm) {
        recruiterLoginForm.addEventListener("submit", validateLoginForm);
    }
    
    function displayError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement("div");
        
        errorDiv.className = "invalid-feedback";
        errorDiv.textContent = message;
        errorDiv.style.display = "block";
        
        field.classList.add("is-invalid");
        
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }
    
    function clearErrorMessages() {
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
    }
});