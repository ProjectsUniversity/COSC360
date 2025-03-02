document.addEventListener("DOMContentLoaded", function() {
    const signupForm = document.getElementById("signupForm");

    if (signupForm) {
        signupForm.addEventListener("submit", validateSignupForm);
    }
    
    function validateSignupForm(event) {
        event.preventDefault();
        
        const username = document.getElementById("username").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirmPassword").value;
        const terms = document.getElementById("terms").checked;
        
        clearErrorMessages();
        
        let isValid = true;
        
        if (!username) {
            displayError("username", "Username is required");
            isValid = false;
        }
        
        if (!email) {
            displayError("email", "Email is required");
            isValid = false;
        }
        
        if (!password) {
            displayError("password", "Password is required");
            isValid = false;
        }
        
        if (!confirmPassword) {
            displayError("confirmPassword", "Please confirm your password");
            isValid = false;
        } else if (password !== confirmPassword) {
            displayError("confirmPassword", "Passwords do not match");
            isValid = false;
        }
        
        if (!terms) {
            displayError("terms", "You must agree to the Terms and Conditions");
            isValid = false;
        }

        if (isValid) {
            alert("Signup successful!");
            // signupForm.submit(); 
        }

        return false; 
    }
    
    function displayError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.createElement("div");
        
        errorDiv.className = "invalid-feedback";
        errorDiv.textContent = message;
        errorDiv.style.display = "block";
        
        field.classList.add("is-invalid");
        
        // Handling TOS Checkbox
        if (fieldId === "terms") {
            const termsLabel = field.nextElementSibling;
            if (termsLabel && termsLabel.classList.contains('form-check-label')) {
                termsLabel.parentNode.appendChild(errorDiv);
            } else {
                field.parentNode.insertBefore(errorDiv, field.nextSibling);
            }
        } else {
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
    }
    
    function clearErrorMessages() {
        document.querySelectorAll(".invalid-feedback").forEach(el => el.remove());
        document.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        
        const termsCheckbox = document.getElementById("terms");
        if (termsCheckbox) {
            const termsLabel = termsCheckbox.nextElementSibling;
            if (termsLabel && termsLabel.classList.contains('form-check-label')) {
                termsLabel.textContent = "I agree to the Terms and Conditions";
            }
        }
    }
}); 