const validationRules = {
    profile: {
        name: {
            minLength: 2,
            maxLength: 50,
            pattern: /^[a-zA-Z\s]*$/
        },
        title: {
            minLength: 2,
            maxLength: 100
        }
    },
    skills: {
        minSkills: 1,
        maxSkills: 10,
        pattern: /^[a-zA-Z0-9+#\s]*$/
    },
    qualifications: {
        degree: {
            minLength: 2,
            maxLength: 100
        },
        institution: {
            minLength: 2,
            maxLength: 100
        },
        year: {
            pattern: /^\d{4}(-\d{4})?$/
        }
    },
    resume: {
        maxSize: 5242880, // 5MB
        allowedTypes: ['.pdf', '.doc', '.docx']
    }
};

function openEditModal(section) {
    const modal = document.getElementById('editModal');
    const modalTitle = document.getElementById('modal-title');
    const modalContent = document.getElementById('modal-content');
    
    modal.style.display = 'block';
    modalTitle.innerText = 'Edit ' + section.charAt(0).toUpperCase() + section.slice(1);
    
    const modalHTML = generateModalContent(section);
    modalContent.innerHTML = modalHTML;
    
    if (section === 'resume') {
        handleResumeUpload();
    }
}

function generateModalContent(section) {
    switch(section) {
        case 'profile':
            return `
                <div class="form-group">
                    <input type="text" id="name" placeholder="Name" value="John Doe" required>
                    <span class="error-message" id="name-error"></span>
                </div>
                <div class="form-group">
                    <input type="text" id="title" placeholder="Title" value="Software Developer" required>
                    <span class="error-message" id="title-error"></span>
                </div>
                <div class="form-group">
                    <input type="file" id="profile-image" accept="image/*">
                    <span class="error-message" id="image-error"></span>
                </div>
            `;
        case 'skills':
            return `
                <div class="form-group">
                    <input type="text" id="skill-input" placeholder="Add new skill">
                    <span class="error-message" id="skill-error"></span>
                </div>
                <div id="current-skills">
                    ${document.getElementById('skills-list').innerHTML}
                </div>
            `;
    }
}

function validateField(value, rules) {
    if (rules.minLength && value.length < rules.minLength) {
        return `Minimum ${rules.minLength} characters required`;
    }
    if (rules.maxLength && value.length > rules.maxLength) {
        return `Maximum ${rules.maxLength} characters allowed`;
    }
    if (rules.pattern && !rules.pattern.test(value)) {
        return 'Invalid format';
    }
    return '';
}

function validateForm(section) {
    let isValid = true;
    const errors = {};

    switch(section) {
        case 'profile':
            const name = document.getElementById('name').value;
            const title = document.getElementById('title').value;
            const imageFile = document.getElementById('profile-image').files[0];

            const nameError = validateField(name, validationRules.profile.name);
            if (nameError) {
                errors.name = nameError;
                isValid = false;
            }

            const titleError = validateField(title, validationRules.profile.title);
            if (titleError) {
                errors.title = titleError;
                isValid = false;
            }

            if (imageFile && !imageFile.type.startsWith('image/')) {
                errors.image = 'Please upload a valid image file';
                isValid = false;
            }
            break;
    }

    return { isValid, errors };
}

function saveChanges() {
    const modalTitle = document.getElementById('modal-title').innerText;
    const section = modalTitle.split(' ')[1].toLowerCase();

    const { isValid, errors } = validateForm(section);
    
    if (!isValid) {
        displayErrors(errors);
        return;
    }

    updateContent(section);
    closeModal();
}

function displayErrors(errors) {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    Object.entries(errors).forEach(([field, message]) => {
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = message;
        }
    });
}

function updateContent(section) {
    switch(section) {
        case 'profile':
            const name = document.getElementById('name').value;
            const title = document.getElementById('title').value;
            document.querySelector('.profile-info h1').textContent = name;
            document.querySelector('.profile-info p').textContent = title;
            
            const imageFile = document.getElementById('profile-image').files[0];
            if (imageFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-picture').src = e.target.result;
                };
                reader.readAsDataURL(imageFile);
            }
            break;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeModal();
        }
    };
    document.querySelector('.close').addEventListener('click', closeModal);
});