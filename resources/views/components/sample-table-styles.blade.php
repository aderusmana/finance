{{--
==========================================================
sample TABLE STYLES COMPONENT
==========================================================
Include this component to apply consistent sample management table styling
Usage: @include('components.sample-table-styles')

==========================================================
CSS CLASSES REFERENCE & USAGE GUIDE
==========================================================

🎨 MODAL STYLING:
• .modal-header                  → Standard modal header dengan gold gradient
• .modal-header-enhanced         → Enhanced modal header dengan pattern texture
• .modal-title-enhanced          → Title dengan icon dan gold styling
• .modal-body-enhanced           → Body dengan gradient background
• .modal-footer                  → Footer dengan light gray background

📋 TABLE CONTAINER:
• .main-table-container          → Container utama tabel dengan shadow dan rounded corners
• .table-header-enhanced         → Header tabel dengan gold gradient dan pattern
• .table-title                   → Title tabel dengan icon
• .table-subtitle                → Subtitle tabel dengan opacity

🔘 BUTTON STYLING:
• .new-complain-btn              → Button "New Complain" dengan gold gradient
• .btn-light-danger              → Button dengan gold gradient theme
• .btn-secondary                 → Button dengan gold gradient (override Bootstrap)
• .btn-danger                    → Button merah dengan dark theme
• .action-btn-group              → Container untuk action buttons di tabel
• .action-btn-hover              → Button dengan custom hover tooltip

🏷️ STATUS BADGES:
• .status-badge-lg               → Badge status dengan enhanced styling
• .bg-warning.status-badge-lg    → Badge kuning dengan gold gradient
• .bg-info.status-badge-lg       → Badge biru dengan gradient
• .bg-success.status-badge-lg    → Badge hijau dengan gradient
• .bg-danger.status-badge-lg     → Badge merah dengan gradient

📄 DETAIL MODAL SECTIONS:
• .detail-section                → Container section untuk detail modal
• .section-header                → Header section dengan gold gradient
• .info-card                     → Card untuk informasi dengan left border
• .info-row                      → Row informasi dengan label dan value
• .info-label                    → Label informasi dengan icon
• .info-value                    → Value field dengan background
• .slip-header-enhanced          → Header slip dengan border
• .slip-title-enhanced           → Title slip dengan center alignment

📊 PRODUCT & TABLE DETAILS:
• .product-list-container        → Container untuk list produk
• .product-item                  → Item produk dengan hover effect
• .objectives-container          → Container untuk objectives
• .objectives-text               → Text area untuk objectives
• .detail-table-container        → Container tabel detail dengan shadow
• .detail-table                  → Tabel detail dengan enhanced styling

🎯 DATATABLES ENHANCEMENTS:
• #sampleable                 → Main table dengan gold theme
• .dataTables_wrapper            → Wrapper dengan padding dan animations
• .dataTables_filter input       → Search input dengan gold focus
• .dataTables_length select      → Length selector dengan gold theme
• .dataTables_paginate           → Pagination dengan gold buttons
• .dataTables_info               → Info text dengan gold color

💡 TOOLTIP SYSTEM:
• .action-tooltip                → Custom tooltip untuk action buttons
• .action-tooltip.show           → Tooltip dalam state visible
• .action-btn-hover              → Element yang akan menampilkan tooltip

🎨 COLOR THEME:
• Primary Gold: rgb(192, 127, 0)
• Secondary Gold: rgb(160, 100, 0)
• Dark Gold: rgb(128, 80, 0)
• Text Dark: rgb(76, 61, 61)
• Light Blue (Edit): rgb(52, 144, 220)

📝 FORM ELEMENTS:
• .form-control:focus            → Input focus dengan gold border
• .form-select:focus             → Select focus dengan gold border
• .select2-container focus       → Select2 focus dengan gold theme
• .form-check-input:checked      → Checkbox/radio dengan gold color

⚡ ANIMATIONS:
• fadeInUp                       → Animation untuk section details
• Hover transforms               → Scale dan translateY effects
• Staggered delays               → Animation delays untuk multiple elements

==========================================================
USAGE EXAMPLES:
==========================================================

1. MODAL HEADER:
<div class="modal-header-enhanced">
    <h5 class="modal-title-enhanced">
        <i class="ph-duotone ph-file-text"></i>
        Your Title Here
    </h5>
</div>

2. TABLE CONTAINER:
<div class="main-table-container">
    <div class="table-header-enhanced">
        <h4 class="table-title">
            <i class="ph-duotone ph-list"></i>
            Table Title
        </h4>
        <p class="table-subtitle">Table description</p>
    </div>
    <table id="sampleTable">...</table>
</div>

3. ACTION BUTTONS:
<div class="action-btn-group">
    <button class="btn btn-info action-btn-hover" data-tooltip="View Details">
        <i class="ph-bold ph-eye"></i>
    </button>
    <button class="btn btn-secondary action-btn-hover" data-tooltip="Edit">
        <i class="ph-bold ph-pencil"></i>
    </button>
    <button class="btn btn-danger action-btn-hover" data-tooltip="Delete">
        <i class="ph-bold ph-trash"></i>
    </button>
</div>

4. STATUS BADGE:
<span class="badge bg-warning status-badge-lg">Pending</span>

5. DETAIL SECTION:
<div class="detail-section">
    <div class="section-header">
        <i class="ph-duotone ph-info"></i>
        Section Title
    </div>
    <div class="info-row">
        <div class="info-label">
            <i class="ph-duotone ph-user"></i>
            Label:
        </div>
        <div class="info-value">Value here</div>
    </div>
</div>

==========================================================
JAVASCRIPT REQUIREMENTS:
==========================================================
• Custom tooltip system sudah include di component
• DataTables dengan server-side processing
• SweetAlert2 untuk confirmations
• Select2 untuk enhanced selects
• Bootstrap 5 untuk base styling

==========================================================
QUICK REFERENCE UNTUK DEVELOPER:
==========================================================

MODAL:          modal-header-enhanced, modal-title-enhanced, modal-body-enhanced
TABLE:          main-table-container, table-header-enhanced, table-title
BUTTONS:        new-complain-btn, action-btn-group, action-btn-hover
BADGES:         status-badge-lg + bg-warning/info/success/danger
DETAILS:        detail-section, section-header, info-row, info-label, info-value
COLORS:         Gold rgb(192,127,0), Dark rgb(76,61,61), Edit Blue rgb(52,144,220)
--}}

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/sample-table-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive-table-cards.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script src="{{ asset('assets/js/responsive-table-cards.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltip = document.createElement('div');
    tooltip.className = 'action-tooltip';
    document.body.appendChild(tooltip);

    document.querySelectorAll('.action-btn-hover').forEach(button => {
        const tooltipText = button.getAttribute('data-tooltip');
        if (tooltipText) {
            button.addEventListener('mouseenter', () => {
                tooltip.textContent = tooltipText;
                tooltip.classList.add('show');
                const rect = button.getBoundingClientRect();
                tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
            });

            button.addEventListener('mouseleave', () => {
                tooltip.classList.remove('show');
            });
        }
    });
});
</script>
@endpush
