document.addEventListener('DOMContentLoaded', function() {
    // Add hamburger menu for mobile
    const mainContent = document.querySelector('.main-content');
    if (!document.querySelector('.navbar-toggler')) {
        const toggleButton = document.createElement('button');
        toggleButton.className = 'navbar-toggler d-lg-none';
        toggleButton.innerHTML = '<i class="fas fa-bars"></i>';
        toggleButton.style.cssText = `
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: #f8f9fa;
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            display: none;
        `;
        mainContent.insertBefore(toggleButton, mainContent.firstChild);

        // Show toggle button on mobile
        const mediaQuery = window.matchMedia('(max-width: 992px)');
        function handleMobileChange(e) {
            toggleButton.style.display = e.matches ? 'block' : 'none';
        }
        mediaQuery.addListener(handleMobileChange);
        handleMobileChange(mediaQuery);

        // Toggle sidebar
        const sidebar = document.querySelector('.sidebar');
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('show') && 
                !sidebar.contains(e.target) && 
                !toggleButton.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
});