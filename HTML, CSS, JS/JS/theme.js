// Check for saved theme preference or default to light
const currentTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', currentTheme);

function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', newTheme);

    // Update toggle button text
    const toggleButton = document.querySelector('.theme-toggle');
    const icon = toggleButton.querySelector('i');
    if (newTheme === 'dark') {
        icon.classList.remove('fa-moon');
        icon.classList.add('fa-sun');
        toggleButton.innerHTML = `${icon.outerHTML} Light Mode`;
    } else {
        icon.classList.remove('fa-sun');
        icon.classList.add('fa-moon');
        toggleButton.innerHTML = `${icon.outerHTML} Dark Mode`;
    }

    // Save theme preference
    localStorage.setItem('theme', newTheme);
}

// Set theme on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    
    // Update toggle button initial state
    const toggleButton = document.querySelector('.theme-toggle');
    if (toggleButton) {
        const icon = toggleButton.querySelector('i');
        if (savedTheme === 'dark') {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            toggleButton.innerHTML = `${icon.outerHTML} Light Mode`;
        }
    }
});