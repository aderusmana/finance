@once
<style>
    /* ======================================================== */
    /* DISTRIBUTOR DOCUMENTS - SHARED DESIGN SYSTEM & STYLES     */
    /* ======================================================== */

    /* Detail Card Header (Navy gradient) */
    .detail-header-card {
        background: linear-gradient(135deg, #152238 0%, #1e3a5f 100%);
        border-radius: 0.75rem;
        color: #ffffff;
        padding: 1.5rem 1.75rem;
        box-shadow: 0 4px 12px rgba(21, 34, 56, 0.15);
    }

    /* Light badge for distributor code on dark backgrounds */
    .distributor-code-badge-light {
        font-family: monospace;
        font-size: 0.85rem;
        font-weight: 600;
        color: #93c5fd;
        background-color: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(147, 197, 253, 0.3);
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Standard distributor code badge */
    .distributor-code-badge {
        font-family: monospace;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e3a8a;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 2px 8px;
        border-radius: 4px;
    }

    .distributor-bupot-badge {
        font-size: 0.75rem;
        max-width: 250px;
    }

    .distributor-transfer-range {
        font-size: 0.72rem;
    }

    /* Custom Navigation Tabs */
    .nav-tabs-custom {
        border-bottom: 2px solid #e2e8f0;
        gap: 8px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #64748b;
        font-weight: 600;
        padding: 0.75rem 1.25rem;
        border-radius: 0;
        background: transparent;
        transition: all 0.2s ease;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #1e3a8a;
        border-bottom-color: #cbd5e1;
    }

    .nav-tabs-custom .nav-link.active {
        color: #1e3a8a;
        border-bottom-color: #2563eb;
        background: transparent;
    }

    /* Monthly Cards Matrix (Detail View & Modal) */
    .month-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #ffffff;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .month-card:hover {
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    .month-card-header {
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }

    .doc-section {
        border: 1px solid #f1f5f9;
        background: #f8fafc;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
    }

    .doc-item-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
    }

    .doc-item-row:last-child {
        margin-bottom: 0;
    }

    .btn-add-quick {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }

    /* Document Status Badges */
    .badge-status-complete {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    .badge-status-partial {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

    .badge-status-empty {
        background-color: #f3f4f6;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }

    /* Month Matrix Progress Indicator (Pills 1-12) */
    .month-matrix-container {
        display: inline-flex;
        gap: 4px;
        align-items: center;
        flex-wrap: nowrap;
    }

    .month-matrix-pill {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 38px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        border: 1px solid transparent;
        font-size: 0.72rem;
        cursor: pointer;
    }

    .month-matrix-pill .month-num {
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 1;
    }

    .month-matrix-pill .month-icon {
        font-size: 0.8rem;
        line-height: 1;
        margin-top: 2px;
    }

    /* Complete: BuPot & TOP exists */
    .month-matrix-pill.complete {
        background-color: #d1fae5;
        border-color: #6ee7b7;
        color: #065f46;
    }
    .month-matrix-pill.complete:hover {
        background-color: #a7f3d0;
        color: #064e3b;
    }

    /* Partial: BuPot or TOP exists */
    .month-matrix-pill.partial {
        background-color: #fef3c7;
        border-color: #fcd34d;
        color: #92400e;
    }
    .month-matrix-pill.partial:hover {
        background-color: #fde68a;
        color: #78350f;
    }

    /* Empty: No documents yet */
    .month-matrix-pill.empty {
        background-color: #f3f4f6;
        border-color: #e5e7eb;
        color: #64748b;
    }
    .month-matrix-pill.empty:hover {
        background-color: #e2e8f0;
        color: #334155;
    }

    /* Legend Indicators */
    .legend-indicator {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 3px;
        vertical-align: middle;
        margin-right: 5px;
    }
    .legend-complete { background-color: #10b981; border: 1px solid #059669; }
    .legend-partial  { background-color: #f59e0b; border: 1px solid #d97706; }
    .legend-empty    { background-color: #cbd5e1; border: 1px solid #94a3b8; }

    /* Distributor File Upload Zone */
    .distributor-file-zone {
        transition: all 0.2s ease-in-out;
    }
    .distributor-file-zone .file-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .distributor-file-zone .file-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }
    .distributor-file-zone .btn-remove-file {
        transition: all 0.15s ease;
    }
    .distributor-file-zone .btn-remove-file:hover {
        background: rgba(239, 68, 68, 0.2) !important;
        transform: scale(1.1);
    }

    #distributorTableContainer {
        transition: opacity 0.2s ease;
    }

    /* SweetAlert High Z-Index for Modals */
    .swal2-container {
        z-index: 99999 !important;
    }

    /* Modal Header Styling (High Contrast for Primary/Dark Gradient Headers) */
    #distributorDetailModal .modal-header,
    #previewPdfModal .modal-header,
    #uploadDocumentModal .modal-header {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
    }

    #distributorDetailModal .modal-header .modal-title,
    #distributorDetailModal .modal-header h6,
    #distributorDetailModal .modal-header i,
    #previewPdfModal .modal-header .modal-title,
    #previewPdfModal .modal-header h6,
    #previewPdfModal .modal-header i,
    #uploadDocumentModal .modal-header .modal-title,
    #uploadDocumentModal .modal-header h5,
    #uploadDocumentModal .modal-header i {
        color: #ffffff !important;
    }

    #distributorDetailModal .modal-header .btn-close,
    #previewPdfModal .modal-header .btn-close,
    #uploadDocumentModal .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%) !important;
        opacity: 0.85 !important;
    }

    #distributorDetailModal .modal-header .btn-close:hover,
    #previewPdfModal .modal-header .btn-close:hover,
    #uploadDocumentModal .modal-header .btn-close:hover {
        opacity: 1 !important;
    }
</style>
@endonce
