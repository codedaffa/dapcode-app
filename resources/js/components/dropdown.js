/**
 * DAPCODE UI - ACCESSIBLE DROPDOWN CONTROLLER
 */
export const DapDropdown = {
    init() {
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-ui-dropdown-trigger]');

            // If clicked a trigger
            if (trigger) {
                e.preventDefault();
                e.stopPropagation();
                const container = trigger.closest('.ui-dropdown-container');
                if (!container) return;
                const menu = container.querySelector('.ui-dropdown-menu');
                if (!menu) return;

                const isOpen = menu.classList.contains('is-open');
                // Close any other open dropdowns first
                DapDropdown.closeAll();

                if (!isOpen) {
                    menu.classList.add('is-open');
                    container.classList.add('is-active');
                    trigger.setAttribute('aria-expanded', 'true');
                }
                return;
            }

            // If clicked inside menu item, close
            if (e.target.closest('.ui-dropdown-item')) {
                DapDropdown.closeAll();
                return;
            }

            // If clicked outside menu, close
            if (!e.target.closest('.ui-dropdown-menu')) {
                DapDropdown.closeAll();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                DapDropdown.closeAll();
            }
        });
    },

    closeAll() {
        document.querySelectorAll('.ui-dropdown-menu.is-open').forEach(menu => {
            menu.classList.remove('is-open');
            const container = menu.closest('.ui-dropdown-container');
            if (container) {
                container.classList.remove('is-active');
                const trigger = container.querySelector('[data-ui-dropdown-trigger]');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }
};

if (typeof window !== 'undefined') {
    window.DapDropdown = DapDropdown;
}
