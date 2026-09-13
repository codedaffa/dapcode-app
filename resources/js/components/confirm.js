/**
 * DAPCODE UI - CONFIRMATION DIALOG (PROMISE-BASED)
 * Usage:
 * const ok = await DapConfirm({
 *     title: 'Hapus Data?',
 *     message: 'Tindakan ini permanen dan tidak dapat dibatalkan.',
 *     type: 'danger',
 *     confirmText: 'Ya, Hapus',
 *     cancelText: 'Batal'
 * });
 * if (ok) { ... }
 */
export function DapConfirm({
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    type = 'warning',
    confirmText = 'Lanjutkan',
    cancelText = 'Batal'
} = {}) {
    return new Promise((resolve) => {
        let dialog = document.getElementById('ui-confirm-dialog-singleton');
        if (dialog) dialog.remove();

        const icons = {
            danger: 'fa-solid fa-triangle-exclamation',
            warning: 'fa-solid fa-circle-exclamation',
            info: 'fa-solid fa-circle-question',
            success: 'fa-solid fa-circle-check'
        };

        const btnClass = type === 'danger' ? 'ui-btn-danger' : (type === 'success' ? 'ui-btn-success' : 'ui-btn-primary');

        dialog = document.createElement('div');
        dialog.id = 'ui-confirm-dialog-singleton';
        dialog.className = 'ui-modal-backdrop is-open';
        dialog.style.zIndex = '10005';

        dialog.innerHTML = `
            <div class="ui-modal ui-modal-sm">
                <div class="ui-modal-body" style="text-align: center; padding: 28px 24px 20px;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; background: rgba(239, 68, 68, 0.12); color: ${type === 'danger' ? 'var(--danger)' : 'var(--warning)'};">
                        <i class="${icons[type] || icons.warning}"></i>
                    </div>
                    <h3 style="font-size: 17px; font-weight: 700; color: #fff; margin-bottom: 8px;">${title}</h3>
                    <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.5; margin: 0;">${message}</p>
                </div>
                <div class="ui-modal-footer" style="justify-content: center; gap: 12px; border-top: none; padding: 0 24px 24px; background: transparent;">
                    <button type="button" class="ui-btn ui-btn-secondary ui-btn-md" id="ui-confirm-cancel-btn" style="min-width: 110px; padding: 10px 22px;">
                        <i class="fa-solid fa-xmark"></i> <span>${cancelText}</span>
                    </button>
                    <button type="button" class="ui-btn ${btnClass} ui-btn-md" id="ui-confirm-ok-btn" style="min-width: 110px; padding: 10px 22px;">
                        <i class="${type === 'danger' ? 'fa-solid fa-trash' : (type === 'success' ? 'fa-solid fa-check' : 'fa-solid fa-circle-check')}"></i> <span>${confirmText}</span>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(dialog);
        document.body.style.overflow = 'hidden';

        const cleanup = (result) => {
            dialog.classList.remove('is-open');
            document.body.style.overflow = '';
            setTimeout(() => dialog.remove(), 250);
            resolve(result);
        };

        const okBtn = dialog.querySelector('#ui-confirm-ok-btn');
        const cancelBtn = dialog.querySelector('#ui-confirm-cancel-btn');

        okBtn.focus();

        okBtn.addEventListener('click', () => cleanup(true));
        cancelBtn.addEventListener('click', () => cleanup(false));

        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) cleanup(false);
        });

        const keyHandler = (e) => {
            if (e.key === 'Escape') {
                document.removeEventListener('keydown', keyHandler);
                cleanup(false);
            }
        };
        document.addEventListener('keydown', keyHandler);
    });
}

if (typeof window !== 'undefined') {
    window.DapConfirm = DapConfirm;
}
