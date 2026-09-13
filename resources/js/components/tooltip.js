/**
 * DAPCODE UI - LIGHTWEIGHT TOOLTIP CONTROLLER
 */
export const DapTooltip = {
    tooltipEl: null,

    init() {
        let el = document.getElementById('ui-tooltip-singleton');
        if (!el) {
            el = document.createElement('div');
            el.id = 'ui-tooltip-singleton';
            el.className = 'ui-tooltip';
            document.body.appendChild(el);
        }
        this.tooltipEl = el;

        document.addEventListener('mouseover', (e) => {
            const target = e.target.closest('[data-ui-tooltip]');
            if (!target) return;

            const text = target.getAttribute('data-ui-tooltip');
            if (!text) return;

            this.show(target, text);
        });

        document.addEventListener('mouseout', (e) => {
            const target = e.target.closest('[data-ui-tooltip]');
            if (!target) return;
            this.hide();
        });
    },

    show(target, text) {
        if (!this.tooltipEl) return;
        this.tooltipEl.textContent = text;
        this.tooltipEl.classList.add('is-visible');

        const rect = target.getBoundingClientRect();
        const tooltipRect = this.tooltipEl.getBoundingClientRect();

        const top = rect.top - tooltipRect.height - 8;
        const left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);

        this.tooltipEl.style.top = `${Math.max(8, top)}px`;
        this.tooltipEl.style.left = `${Math.max(8, Math.min(window.innerWidth - tooltipRect.width - 8, left))}px`;
    },

    hide() {
        if (!this.tooltipEl) return;
        this.tooltipEl.classList.remove('is-visible');
    }
};

if (typeof window !== 'undefined') {
    window.DapTooltip = DapTooltip;
}
