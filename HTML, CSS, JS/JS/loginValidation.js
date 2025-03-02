document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    
    function validateLoginForm(event) {
        event.preventDefault();
        
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        
        clearErrorMessages();
        
        let isValid = true;
        
        if (!email) {
            displayError("email", "Email address or Username is required");
            isValid = false;
        }
        
        if (!password) {
            displayError("password", "Password is required");
            isValid = false;
        }
        
        if (isValid) {
            alert("Login successful!");
            // loginForm.submit();
        }
        
        return false; 
    }
    
    if (loginForm) {
        loginForm.addEventListener("submit", validateLoginForm);
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