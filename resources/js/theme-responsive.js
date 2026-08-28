/**
 * DAPCODE HMVC - RESPONSIVE SCRIPT CONTROLLER
 * Handles multi-device interactions: Mobile Drawer, Desktop Collapse, Swipe gestures, and State Persistence.
 */

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const sidebarCloseBtn = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebar = document.querySelector('.app-sidebar');

    // 1. Restore Desktop Sidebar Preference from localStorage
    const isDesktop = () => window.innerWidth >= 992;

    if (isDesktop()) {
        const savedCollapsed = localStorage.getItem('dapcode_sidebar_collapsed');
        if (savedCollapsed === 'true') {
            body.classList.add('sidebar-collapsed');
        }
    }

    // 2. Main Sidebar Toggle Function
    const toggleSidebar = () => {
        if (isDesktop()) {
            body.classList.toggle('sidebar-collapsed');
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('dapcode_sidebar_collapsed', isCollapsed);
        } else {
            body.classList.toggle('sidebar-mobile-open');
        }
    };

    // 3. Close Mobile Sidebar Function
    const closeMobileSidebar = () => {
        body.classList.remove('sidebar-mobile-open');
    };

    // 4. Attach Event Listeners
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeMobileSidebar();
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            closeMobileSidebar();
        });
    }

    // Close on ESC key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && body.classList.contains('sidebar-mobile-open')) {
            closeMobileSidebar();
        }
    });

    // Close mobile sidebar when clicking any navigation link inside sidebar
    if (sidebar) {
        const navLinks = sidebar.querySelectorAll('.menu-item, .switcher-item, .sidebar-brand-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (!isDesktop()) {
                    closeMobileSidebar();
                }
            });
        });
    }

    // 5. Header Title Module Switcher Dropdown Toggle
    const titleDropdownBtn = document.getElementById('headerTitleDropdownBtn');
    const titleDropdownMenu = document.getElementById('headerTitleDropdownMenu');
    const titleDropdownIcon = document.querySelector('.title-dropdown-icon');
    const themeDropdownBtn = document.getElementById('themeDropdownBtn');
    const themeDropdownMenu = document.getElementById('themeDropdownMenu');

    if (titleDropdownBtn && titleDropdownMenu) {
        titleDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (themeDropdownMenu) themeDropdownMenu.style.display = 'none';
            const isOpen = titleDropdownMenu.style.display === 'block';
            titleDropdownMenu.style.display = isOpen ? 'none' : 'block';
            if (titleDropdownIcon) {
                titleDropdownIcon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });

        document.addEventListener('click', (e) => {
            if (!titleDropdownBtn.contains(e.target) && !titleDropdownMenu.contains(e.target)) {
                titleDropdownMenu.style.display = 'none';
                if (titleDropdownIcon) titleDropdownIcon.style.transform = 'rotate(0deg)';
            }
        });
    }

    // 6. Theme Dropdown Picker Toggle
    if (themeDropdownBtn && themeDropdownMenu) {
        themeDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (titleDropdownMenu) {
                titleDropdownMenu.style.display = 'none';
                if (titleDropdownIcon) titleDropdownIcon.style.transform = 'rotate(0deg)';
            }
            const isOpen = themeDropdownMenu.style.display === 'block';
            themeDropdownMenu.style.display = isOpen ? 'none' : 'block';
        });

        document.addEventListener('click', (e) => {
            if (!themeDropdownBtn.contains(e.target) && !themeDropdownMenu.contains(e.target)) {
                themeDropdownMenu.style.display = 'none';
            }
        });
    }

    // 6. Handle Window Resize
    window.addEventListener('resize', () => {
        if (isDesktop()) {
            closeMobileSidebar();
            const savedCollapsed = localStorage.getItem('dapcode_sidebar_collapsed');
            if (savedCollapsed === 'true') {
                body.classList.add('sidebar-collapsed');
            } else {
                body.classList.remove('sidebar-collapsed');
            }
        } else {
            body.classList.remove('sidebar-collapsed');
        }
    });
});
