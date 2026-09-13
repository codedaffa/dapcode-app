/**
 * DAPCODE UI - MODAL MICRO-DRIVER
 * Declarative modal via [data-ui-modal-target] or JavaScript API: DapModal.open(id) / DapModal.close(id)
 */
export const DapModal = {
    open(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        
        // Auto focus first input if present
        setTimeout(() => {
            const autoFocusEl = modal.querySelector('[autofocus], input, button:not(.ui-modal-close)');
            if (autoFocusEl) autoFocusEl.focus();
        }, 50);

        modal.dispatchEvent(new CustomEvent('modal:opened', { bubbles: true, detail: { id: modalId } }));
    },

    close(modalId) {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (!modal) return;
        modal.classList.remove('is-open');
        
        // Restore body overflow if no other open modals
        if (!document.querySelector('.ui-modal-backdrop.is-open')) {
            document.body.style.overflow = '';
        }

        modal.dispatchEvent(new CustomEvent('modal:closed', { bubbles: true, detail: { id: modal.id } }));
    },

    init() {
        // Declarative open triggers
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-ui-modal-target]');
            if (trigger) {
                e.preventDefault();
                const targetId = trigger.getAttribute('data-ui-modal-target');
                DapModal.open(targetId);
                return;
            }

            // Close triggers
            const closeBtn = e.target.closest('[data-ui-modal-close]');
            if (closeBtn) {
                e.preventDefault();
                const modal = closeBtn.closest('.ui-modal-backdrop');
                if (modal) DapModal.close(modal);
                return;
            }

            // Click backdrop to close
            if (e.target.classList.contains('ui-modal-backdrop') && !e.target.hasAttribute('data-ui-static')) {
                DapModal.close(e.target);
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.ui-modal-backdrop.is-open:not([data-ui-static])');
                openModals.forEach(m => DapModal.close(m));
            }
        });
    }
};

if (typeof window !== 'undefined') {
    window.DapModal = DapModal;
}
