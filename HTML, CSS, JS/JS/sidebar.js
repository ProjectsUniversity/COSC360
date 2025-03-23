// Manages sidebar functionality and state
class Sidebar {
    constructor(options = {}) {
        this.options = {
            role: options.role || 'guest',
            currentPage: options.currentPage || 'home',
            user: options.user || null,
            ...options
        };
        this.init();
    }

    init() {
        this.setupMobileResponsiveness();
        this.setupNavigation();
        this.setupAuthSection();
    }

    setupMobileResponsiveness() {
        const sidebar = document.querySelector('.sidebar');
        if (!sidebar) return;

        // Add mobile toggle button if not present
        if (!document.querySelector('.sidebar-toggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'sidebar-toggle btn btn-primary d-md-none';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.appendChild(toggleBtn);

            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const isClickInside = sidebar.contains(e.target) || 
                                    e.target.classList.contains('sidebar-toggle');
                if (!isClickInside) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    setupNavigation() {
        const navContainer = document.getElementById('sidebar-nav');
        if (!navContainer) return;

        const navItems = this.getNavigationItems();
        navContainer.innerHTML = navItems.map(item => this.createNavLink(item)).join('');
        
        // Highlight active nav item
        const activeLink = navContainer.querySelector(`[href="${this.options.currentPage}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
            activeLink.setAttribute('aria-current', 'page');
        }
    }

    getNavigationItems() {
        const commonItems = [
            { href: 'homepage.html', icon: 'fas fa-home', text: 'Home' },
            { href: 'company-dashboard.html', icon: 'fas fa-building', text: 'Companies' }
        ];

        if (this.options.role === 'guest') {
            return commonItems;
        }

        if (this.options.role === 'user') {
            return [
                ...commonItems,
                { href: 'userprofile.html', icon: 'fas fa-user', text: 'My Profile' },
                { href: '#', icon: 'fas fa-bookmark', text: 'Saved Jobs' },
                { href: '#', icon: 'fas fa-file-alt', text: 'Applications' }
            ];
        }

        if (this.options.role === 'recruiter') {
            return [
                { href: 'dashboard.html', icon: 'fas fa-chart-simple', text: 'Dashboard' },
                { href: '#', icon: 'fas fa-message', text: 'Messages' },
                { href: '#', icon: 'fas fa-building', text: 'Company Profile' }
            ];
        }

        return commonItems;
    }

    createNavLink(item) {
        return `
            <a href="${item.href}" class="nav-link link-dark">
                <i class="${item.icon} me-2"></i>
                ${item.text}
            </a>
        `;
    }

    setupAuthSection() {
        const authContainer = document.getElementById('sidebar-auth');
        if (!authContainer) return;

        if (this.options.user) {
            // Logged in user view
            authContainer.innerHTML = `
                <hr class="dropdown-divider">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <img src="${this.options.user.avatar || 'default-avatar.jpg'}" alt="" width="32" height="32" class="rounded-circle me-2">
                        <strong>${this.options.user.name}</strong>
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-gear me-2"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="userprofile.html"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="login.html"><i class="fas fa-door-open me-2"></i>Sign out</a></li>
                    </ul>
                </div>
            `;
        } else {
            // Guest view
            authContainer.innerHTML = `
                <hr class="dropdown-divider">
                <button class="btn btn-primary w-100 mb-2" onclick="window.location.href='signup.html'">
                    <i class="fa-solid fa-rocket me-2"></i>Sign up
                </button>
                <button class="btn btn-outline-primary w-100" onclick="window.location.href='login.html'">
                    <i class="fa fa-sign-in me-2"></i>Log In
                </button>
            `;
        }
    }
}

// Initialize sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const user = document.body.classList.contains('logged-in') ? {
        name: 'John Doe',
        avatar: 'https://avatars.githubusercontent.com/u/106793433?v=4'
    } : null;

    const role = document.body.classList.contains('recruiter-view') ? 'recruiter' : 
                 user ? 'user' : 'guest';

    new Sidebar({
        role,
        user,
        currentPage: window.location.pathname.split('/').pop()
    });
});
