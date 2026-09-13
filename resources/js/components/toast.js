/**
 * DAPCODE UI - TOAST NOTIFICATION ENGINE
 * Usage:
 * DapToast.success('Perubahan berhasil disimpan!', 'Sukses');
 * DapToast.error('Gagal memproses data.', 'Error');
 */
export const DapToast = {
    container: null,

    getContainer() {
        if (!this.container || !document.body.contains(this.container)) {
            let el = document.getElementById('ui-toast-container');
            if (!el) {
                el = document.createElement('div');
                el.id = 'ui-toast-container';
                el.className = 'ui-toast-container';
                document.body.appendChild(el);
            }
            this.container = el;
        }
        return this.container;
    },

    show({ title = '', message = '', type = 'info', duration = 4000 } = {}) {
        const container = this.getContainer();

        const toast = document.createElement('div');
        toast.className = `ui-toast ui-toast-${type}`;

        const icons = {
            success: 'fa-solid fa-circle-check',
            error: 'fa-solid fa-circle-exclamation',
            danger: 'fa-solid fa-circle-exclamation',
            warning: 'fa-solid fa-triangle-exclamation',
            info: 'fa-solid fa-circle-info'
        };

        const iconClass = icons[type] || icons.info;

        toast.innerHTML = `
            <div class="ui-toast-icon"><i class="${iconClass}"></i></div>
            <div class="ui-toast-body">
                ${title ? `<div class="ui-toast-title">${title}</div>` : ''}
                <div class="ui-toast-message">${message}</div>
            </div>
            <button type="button" class="ui-toast-close" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
        `;

        const closeBtn = toast.querySelector('.ui-toast-close');
        const dismiss = () => {
            toast.classList.add('is-closing');
            setTimeout(() => toast.remove(), 200);
        };

        closeBtn.addEventListener('click', dismiss);

        if (duration > 0) {
            setTimeout(dismiss, duration);
        }

        container.appendChild(toast);
        return toast;
    },

    success(message, title = 'Sukses', duration = 4000) {
        return this.show({ title, message, type: 'success', duration });
    },

    error(message, title = 'Terjadi Kesalahan', duration = 5000) {
        return this.show({ title, message, type: 'error', duration });
    },

    warning(message, title = 'Peringatan', duration = 4500) {
        return this.show({ title, message, type: 'warning', duration });
    },

    info(message, title = 'Informasi', duration = 4000) {
        return this.show({ title, message, type: 'info', duration });
    }
};

if (typeof window !== 'undefined') {
    window.DapToast = DapToast;
}
