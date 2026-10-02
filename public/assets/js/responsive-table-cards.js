/**
 * Responsive Table Cards & Mobile Controls Engine
 * Universal & Dynamic table-to-card transformation for mobile devices.
 * Automatically inspects column headers and maps data-labels and column semantics.
 */

(function () {
    'use strict';

    /**
     * Process a single table element and map headers to body cells
     */
    function processTable(table) {
        if (!table) return;

        // Skip modal tables or explicitly opt-out tables
        if (table.closest('.modal') || table.classList.contains('no-card')) {
            return;
        }

        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;

        // Find the active header row (last tr in thead)
        const headerRows = thead.querySelectorAll('tr');
        if (!headerRows.length) return;
        const lastHeaderRow = headerRows[headerRows.length - 1];
        const ths = Array.from(lastHeaderRow.querySelectorAll('th, td'));
        if (!ths.length) return;

        // Build column metadata
        const cols = ths.map((th, index) => {
            const rawText = (th.innerText || th.textContent || '').trim();
            const lower = rawText.toLowerCase();

            const isNumber = (index === 0 && /^(no|#|num|nomor)$/i.test(lower));
            const isStatus = (/status|state|kondisi/i.test(lower));
            const isAction = (/^(action|aksi|act|menu|operasional|operation)$/i.test(lower) || (index === ths.length - 1 && /action|aksi/i.test(lower)));
            const isCode = (!isNumber && /(code|kode|nik|ref|doc|order)/i.test(lower));
            const isTitleCandidate = (!isNumber && !isAction && !isStatus && /(customer|nama|name|title|distributor|user|role|position|department|deskripsi|description|item|produk)/i.test(lower));

            return {
                index,
                label: rawText || `Col ${index + 1}`,
                lower,
                isNumber,
                isStatus,
                isAction,
                isCode,
                isTitleCandidate,
                type: 'data'
            };
        });

        // Determine the primary title column (only 1 per table)
        let titleIndex = cols.findIndex(c => c.isTitleCandidate);
        if (titleIndex === -1) {
            // Fallback: First column that is not number, code, status, or action
            titleIndex = cols.findIndex(c => !c.isNumber && !c.isAction && !c.isStatus && !c.isCode);
        }
        if (titleIndex === -1) {
            // Further fallback: First non-number column
            titleIndex = cols.findIndex(c => !c.isNumber && !c.isAction);
        }

        // Finalize semantic types
        cols.forEach((c, idx) => {
            if (idx === titleIndex) {
                c.type = 'title';
            } else if (c.isNumber) {
                c.type = 'number';
            } else if (c.isAction) {
                c.type = 'action';
            } else if (c.isStatus) {
                c.type = 'status';
            } else if (c.isCode) {
                c.type = 'code';
            } else {
                c.type = 'data';
            }
        });

        // Apply metadata to tbody rows
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(tr => {
            // Handle DataTables empty state row
            if (tr.querySelector('.dataTables_empty') || (tr.cells.length <= 1 && tr.cells[0]?.colSpan > 1)) {
                tr.classList.add('table-card-empty');
                tr.classList.remove('table-card-row');
                return;
            }

            tr.classList.add('table-card-row');
            tr.classList.remove('table-card-empty');

            Array.from(tr.cells).forEach((td, cellIdx) => {
                const col = cols[cellIdx];
                if (col) {
                    td.setAttribute('data-label', col.label);
                    let finalType = col.type;

                    // If cell contains action buttons or action btn group, ensure it is treated as action
                    if (td.querySelector('.action-btn-group, .btn-group, .btn-action-modern') ||
                        (td.querySelector('.btn') && (cellIdx === tr.cells.length - 1 || col.isAction))) {
                        finalType = 'action';
                    }

                    td.setAttribute('data-col-type', finalType);
                }
            });
        });
    }

    /**
     * Process all valid tables in document
     */
    function processAllTables() {
        const selector = '#sampleTable, table.display, table.dataTable, table.table-card-enabled, .main-table-container table';
        const tables = document.querySelectorAll(selector);
        tables.forEach(table => {
            processTable(table);
        });
    }

    // Debounce helper
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const debouncedProcessAll = debounce(processAllTables, 80);

    // Initial DOM Ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processAllTables);
    } else {
        processAllTables();
    }

    window.addEventListener('load', processAllTables);
    window.addEventListener('resize', debouncedProcessAll);

    // Hook into Bootstrap Tab switches
    document.addEventListener('shown.bs.tab', function () {
        setTimeout(processAllTables, 50);
    });

    // Hook into jQuery / DataTables events if jQuery is present
    if (typeof window.jQuery !== 'undefined') {
        const $ = window.jQuery;

        // DataTables draw event (fires on init, page, search, sort, ajax reload)
        $(document).on('draw.dt', function (e, settings) {
            if (settings && settings.nTable) {
                processTable(settings.nTable);
            } else {
                debouncedProcessAll();
            }
        });

        // DataTables init event
        $(document).on('init.dt', function (e, settings) {
            if (settings && settings.nTable) {
                processTable(settings.nTable);
            }
        });

        // Ajax completion in general
        $(document).ajaxComplete(function () {
            debouncedProcessAll();
        });
    }

    // MutationObserver to catch dynamic DOM changes
    const observer = new MutationObserver(function (mutations) {
        let shouldUpdate = false;
        for (let i = 0; i < mutations.length; i++) {
            const m = mutations[i];
            if (m.type === 'childList' && m.target && (m.target.tagName === 'TBODY' || m.target.tagName === 'TABLE')) {
                // Ensure not inside modal
                if (!m.target.closest || !m.target.closest('.modal')) {
                    shouldUpdate = true;
                    break;
                }
            }
        }
        if (shouldUpdate) {
            debouncedProcessAll();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Expose globally if needed
    window.initResponsiveTableCards = processAllTables;
    window.processResponsiveTable = processTable;

})();
