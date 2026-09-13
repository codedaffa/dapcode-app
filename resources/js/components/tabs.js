/**
 * DAPCODE UI - TABS CONTROLLER
 */
export const DapTabs = {
    init() {
        document.addEventListener('click', (e) => {
            const tabBtn = e.target.closest('[data-ui-tab-target]');
            if (!tabBtn) return;

            e.preventDefault();
            const targetId = tabBtn.getAttribute('data-ui-tab-target');
            const targetPane = document.getElementById(targetId);
            if (!targetPane) return;

            const tabsContainer = tabBtn.closest('.ui-tabs-container') || tabBtn.parentElement;
            const contentContainer = targetPane.parentElement;

            // Deactivate sibling tabs
            tabsContainer.querySelectorAll('[data-ui-tab-target]').forEach(btn => {
                btn.classList.remove('is-active');
            });
            tabBtn.classList.add('is-active');

            // Deactivate sibling panes
            contentContainer.querySelectorAll('.ui-tab-pane').forEach(pane => {
                pane.classList.remove('is-active');
            });
            targetPane.classList.add('is-active');
        });
    }
};

if (typeof window !== 'undefined') {
    window.DapTabs = DapTabs;
}
