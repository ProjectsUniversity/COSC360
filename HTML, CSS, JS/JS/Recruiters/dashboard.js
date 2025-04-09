document.addEventListener('DOMContentLoaded', function() {
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

        const mediaQuery = window.matchMedia('(max-width: 992px)');
        function handleMobileChange(e) {
            toggleButton.style.display = e.matches ? 'block' : 'none';
        }
        mediaQuery.addListener(handleMobileChange);
        handleMobileChange(mediaQuery);

        const sidebar = document.querySelector('.sidebar');
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (sidebar.classList.contains('show') && 
                !sidebar.contains(e.target) && 
                !toggleButton.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }

    // Check for unread messages
    function checkUnreadMessages() {
        fetch('../api/get-message-count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('unread-badge');
                    if (badge) {
                        if (data.unread_count > 0) {
                            badge.textContent = data.unread_count;
                            badge.style.display = 'inline';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                }
            })
            .catch(error => console.error('Error checking unread messages:', error));
    }

    // Initialize unread message checking and poll every 10 seconds
    checkUnreadMessages();
    setInterval(checkUnreadMessages, 10000);
});