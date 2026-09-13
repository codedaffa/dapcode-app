/**
 * DAPCODE UI - TABLE & DATATABLE ENGINE
 * 
 * Provides automated polling (auto-refresh) and programmatic table data reloads.
 * 
 * Usage Examples:
 * 
 * 1. Manual / Programmatic Refresh:
 *    await DapTable.refresh('users-table');
 * 
 * 2. Start Periodic Auto-Refresh:
 *    DapTable.startAutoRefresh('users-table', 5000); // polls every 5s
 * 
 * 3. Stop Auto-Refresh:
 *    DapTable.stopAutoRefresh('users-table');
 * 
 * 4. Toggle Auto-Refresh:
 *    const active = DapTable.toggleAutoRefresh('users-table', 10000);
 * 
 * 5. Register Custom Fetch Callback:
 *    DapTable.register('users-table', {
 *        endpoint: '/api/users/rows',
 *        onRefresh: async (tableEl) => { ... }
 *    });
 */

export const DapTable = {
    registry: new Map(),
    activeTimers: new Map(),

    /**
     * Resolve target table container element.
     * @param {string|HTMLElement} idOrEl
     * @returns {HTMLElement|null}
     */
    resolveElement(idOrEl) {
        if (!idOrEl) return null;
        if (typeof idOrEl === 'string') {
            return document.getElementById(idOrEl) || document.querySelector(`[data-ui-datatable="${idOrEl}"]`) || document.querySelector(idOrEl);
        }
        return idOrEl instanceof HTMLElement ? idOrEl : null;
    },

    /**
     * Get string ID of table element.
     * @param {HTMLElement} el 
     * @returns {string}
     */
    getId(el) {
        return el.id || el.getAttribute('data-ui-datatable') || 'ui-table-' + Math.random().toString(36).substr(2, 9);
    },

    /**
     * Register a table with configuration options.
     * @param {string|HTMLElement} idOrEl 
     * @param {Object} options 
     */
    register(idOrEl, options = {}) {
        const el = this.resolveElement(idOrEl);
        const id = el ? this.getId(el) : idOrEl;
        const current = this.registry.get(id) || {};
        this.registry.set(id, { ...current, ...options, element: el });
        return this;
    },

    /**
     * Refresh table content.
     * @param {string|HTMLElement} idOrEl 
     * @param {Object} [params] Optional query params or payload
     * @returns {Promise<Object>}
     */
    async refresh(idOrEl, params = {}) {
        const el = this.resolveElement(idOrEl);
        if (!el) {
            console.warn('[DapTable] Table element not found for:', idOrEl);
            return { success: false, message: 'Element not found' };
        }

        const tableId = this.getId(el);
        const config = this.registry.get(tableId) || {};
        const endpoint = config.endpoint || el.getAttribute('data-ui-refresh-url') || el.getAttribute('data-endpoint');

        // Target table elements
        const table = el.matches('table') ? el : el.querySelector('table');
        const tbody = table ? table.querySelector('tbody') : null;
        const refreshBtns = el.querySelectorAll('[data-ui-table-refresh], .ui-table-refresh-btn');

        // Set Loading Visual State
        el.classList.add('is-refreshing');
        refreshBtns.forEach(btn => {
            btn.classList.add('is-loading');
            const icon = btn.querySelector('i');
            if (icon) icon.classList.add('fa-spin');
        });

        if (tbody) {
            tbody.style.transition = 'opacity 0.2s ease';
            tbody.style.opacity = '0.5';
        }

        try {
            let resultData = null;

            // 1. Custom onRefresh handler if provided
            if (typeof config.onRefresh === 'function') {
                resultData = await config.onRefresh(el, params);
            } 
            // 2. Fetch from AJAX endpoint if available
            else if (endpoint) {
                const url = new URL(endpoint, window.location.origin);
                Object.keys(params).forEach(k => url.searchParams.append(k, params[k]));

                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/json'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status} ${response.statusText}`);

                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const json = await response.json();
                    resultData = json;
                    if (typeof config.renderRows === 'function' && tbody) {
                        tbody.innerHTML = config.renderRows(json.data || json);
                    }
                } else {
                    const html = await response.text();
                    resultData = html;
                    if (tbody) {
                        // If partial returns complete table or tbody rows
                        if (html.includes('<tr')) {
                            tbody.innerHTML = html;
                        } else {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTbody = doc.querySelector('tbody');
                            if (newTbody && tbody) {
                                tbody.innerHTML = newTbody.innerHTML;
                            }
                        }
                    }
                }
            } 
            // 3. Fallback / Showcase Demo Mode: subtle visual feedback & timestamp update
            else {
                await new Promise(resolve => setTimeout(resolve, 400));
                resultData = { demo: true, timestamp: new Date().toISOString() };
            }

            // Update timestamp indicators if present
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            el.querySelectorAll('.ui-table-last-updated').forEach(span => {
                span.textContent = timeStr;
            });

            // Dispatch Custom Event
            const event = new CustomEvent('dap:table:refreshed', {
                bubbles: true,
                detail: { id: tableId, data: resultData, timestamp: now }
            });
            el.dispatchEvent(event);

            if (typeof config.onSuccess === 'function') {
                config.onSuccess(resultData, el);
            }

            return { success: true, data: resultData, timestamp: now };
        } catch (error) {
            console.error('[DapTable] Error refreshing table:', error);
            if (typeof config.onError === 'function') {
                config.onError(error, el);
            }
            return { success: false, error };
        } finally {
            // Restore visual state
            setTimeout(() => {
                el.classList.remove('is-refreshing');
                refreshBtns.forEach(btn => {
                    btn.classList.remove('is-loading');
                    const icon = btn.querySelector('i');
                    if (icon) icon.classList.remove('fa-spin');
                });
                if (tbody) {
                    tbody.style.opacity = '1';
                }
            }, 150);
        }
    },

    /**
     * Start automatic periodic polling.
     * @param {string|HTMLElement} idOrEl 
     * @param {number} [intervalMs=10000] 
     * @returns {boolean}
     */
    startAutoRefresh(idOrEl, intervalMs = 10000) {
        const el = this.resolveElement(idOrEl);
        if (!el) return false;

        const tableId = this.getId(el);
        this.stopAutoRefresh(tableId);

        // Store active timer
        const timer = setInterval(() => {
            // Verify element is still in DOM before refreshing
            if (!document.body.contains(el)) {
                this.stopAutoRefresh(tableId);
                return;
            }
            this.refresh(el);
        }, Math.max(1000, intervalMs));

        this.activeTimers.set(tableId, { timer, intervalMs });

        // Update UI indicator badges/toggles
        el.setAttribute('data-ui-auto-refreshing', 'true');
        el.querySelectorAll('[data-ui-table-auto-refresh]').forEach(toggle => {
            toggle.classList.add('is-active');
            if (toggle.type === 'checkbox') toggle.checked = true;
        });

        // Dispatch status event
        el.dispatchEvent(new CustomEvent('dap:table:autorefresh:start', {
            bubbles: true,
            detail: { id: tableId, intervalMs }
        }));

        return true;
    },

    /**
     * Stop automatic periodic polling.
     * @param {string|HTMLElement} idOrEl 
     * @returns {boolean}
     */
    stopAutoRefresh(idOrEl) {
        const el = this.resolveElement(idOrEl);
        const tableId = el ? this.getId(el) : idOrEl;

        if (this.activeTimers.has(tableId)) {
            const { timer } = this.activeTimers.get(tableId);
            clearInterval(timer);
            this.activeTimers.delete(tableId);

            if (el) {
                el.removeAttribute('data-ui-auto-refreshing');
                el.querySelectorAll('[data-ui-table-auto-refresh]').forEach(toggle => {
                    toggle.classList.remove('is-active');
                    if (toggle.type === 'checkbox') toggle.checked = false;
                });
                el.dispatchEvent(new CustomEvent('dap:table:autorefresh:stop', {
                    bubbles: true,
                    detail: { id: tableId }
                }));
            }
            return true;
        }
        return false;
    },

    /**
     * Toggle auto-refresh on/off.
     * @param {string|HTMLElement} idOrEl 
     * @param {number} [intervalMs=10000] 
     * @returns {boolean} Returns true if now active, false if stopped.
     */
    toggleAutoRefresh(idOrEl, intervalMs = 10000) {
        if (this.isAutoRefreshing(idOrEl)) {
            this.stopAutoRefresh(idOrEl);
            return false;
        } else {
            this.startAutoRefresh(idOrEl, intervalMs);
            return true;
        }
    },

    /**
     * Check if auto-refresh is active for table.
     * @param {string|HTMLElement} idOrEl 
     * @returns {boolean}
     */
    isAutoRefreshing(idOrEl) {
        const el = this.resolveElement(idOrEl);
        const tableId = el ? this.getId(el) : idOrEl;
        return this.activeTimers.has(tableId);
    },

    /**
     * Initialize automatic binding for tables with data attributes in DOM.
     */
    init() {
        // 1. Bind manual refresh trigger buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-ui-table-refresh]');
            if (!btn) return;

            e.preventDefault();
            const targetId = btn.getAttribute('data-ui-table-refresh');
            const targetTable = targetId ? document.getElementById(targetId) : btn.closest('.ui-datatable-wrapper, .ui-table-container');

            if (targetTable) {
                this.refresh(targetTable).then(res => {
                    if (res.success && typeof window.DapToast !== 'undefined') {
                        window.DapToast.success('Data tabel berhasil diperbarui', 'Segarkan Tabel', 2500);
                    }
                });
            }
        });

        // 2. Bind auto-refresh toggle buttons/switches
        document.addEventListener('change', (e) => {
            const toggle = e.target.closest('[data-ui-table-auto-refresh]');
            if (!toggle || toggle.type !== 'checkbox') return;

            const targetId = toggle.getAttribute('data-ui-table-auto-refresh');
            const targetTable = targetId ? document.getElementById(targetId) : toggle.closest('.ui-datatable-wrapper, .ui-table-container');
            const interval = parseInt(toggle.getAttribute('data-interval') || '10000', 10);

            if (targetTable) {
                if (toggle.checked) {
                    this.startAutoRefresh(targetTable, interval);
                    if (typeof window.DapToast !== 'undefined') {
                        window.DapToast.info(`Auto-refresh tabel aktif (${interval / 1000}s)`, 'Auto Refresh', 2500);
                    }
                } else {
                    this.stopAutoRefresh(targetTable);
                    if (typeof window.DapToast !== 'undefined') {
                        window.DapToast.info('Auto-refresh tabel dinonaktifkan', 'Auto Refresh', 2500);
                    }
                }
            }
        });

        // 3. Scan tables declaring auto-refresh attribute: data-ui-auto-refresh="5000"
        document.querySelectorAll('[data-ui-auto-refresh]').forEach(el => {
            const val = el.getAttribute('data-ui-auto-refresh');
            const interval = parseInt(val, 10) || 10000;
            if (val !== 'false' && val !== '0') {
                this.startAutoRefresh(el, interval);
            }
        });
    }
};

if (typeof window !== 'undefined') {
    window.DapTable = DapTable;
}
