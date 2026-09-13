/**
 * DAPCODE UI - MASTER JAVASCRIPT BUNDLE
 */
import { DapModal } from './modal';
import { DapToast } from './toast';
import { DapConfirm } from './confirm';
import { DapDropdown } from './dropdown';
import { DapTooltip } from './tooltip';
import { DapTabs } from './tabs';
import { DapTable } from './table';

export const DapUI = {
    modal: DapModal,
    toast: DapToast,
    confirm: DapConfirm,
    dropdown: DapDropdown,
    tooltip: DapTooltip,
    tabs: DapTabs,
    table: DapTable,

    init() {
        DapModal.init();
        DapDropdown.init();
        DapTooltip.init();
        DapTabs.init();
        DapTable.init();
    }
};

if (typeof window !== 'undefined') {
    window.DapUI = DapUI;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => DapUI.init());
    } else {
        DapUI.init();
    }
}
