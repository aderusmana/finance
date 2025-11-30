<x-app-layout>
    @section('title')
        Complain Approval
    @endsection

    {{-- Include Complaint Table Styles Template --}}
    @include('components.complaint-table-styles')

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Complain Approval</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="{{ route('dashboard') }}" class="f-s-14 f-w-500">
                        <span class="ph-duotone ph-cardholder f-s-16" data-bs-toggle="tooltip" title="Dashboard"></span>
                        Dashboard
                    </a>
                </li>
                <li class="active">
                    <span class="ph-duotone ph-clipboard-text f-s-16 me-2" data-bs-toggle="tooltip"
                        title="Complain Approval"></span>
                    Complain Approval
                </li>
            </ul>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar -->
            <div class="d-flex justify-content-end align-items-center mb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.location.reload()">
                    <i class="ph-duotone ph-arrow-clockwise me-1"></i>
                    Refresh
                </button>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="table-title-group">
                            <h6 class="table-title mb-0">
                                <i class="ph-duotone ph-list-checks me-2"></i>
                                Complain Action Center
                            </h6>
                            <small class="text-white">View and process all complain requisition approval stages.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table id="approvalTable" class="table table-striped table-hover custom-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Requisition Number</th>
                                <th>Requester</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Route to</th>
                                <th>Requested at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="detailModalLabel">
                        <i class="ph-duotone ph-info me-2"></i>
                        Complain Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced">
                    <!-- Header Section -->
                    <div class="slip-header-enhanced">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo"
                                    style="max-height: 60px; width: auto;">
                            </div>
                        </div>
                    </div>

                    <!-- Title Section -->
                    <div class="slip-title-enhanced">
                        <h4><strong>REQUISITION SLIP</strong></h4>
                        <p class="">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                    </div>

                    <!-- Customer & Basic Info Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-user-circle"></i>
                            Customer & Basic Information
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-user text-primary"></i>
                                            Customer Name:
                                        </div>
                                        <div class="info-value readonly" id="detail_customer_name"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-map-pin text-info"></i>
                                            Address:
                                        </div>
                                        <div class="info-value readonly" id="detail_customer_address"
                                            style="min-height: 60px; white-space: pre-wrap;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-bank text-success"></i>
                                            Account:
                                        </div>
                                        <div class="info-value readonly" id="detail_account"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-currency-dollar text-warning"></i>
                                            Cost Center:
                                        </div>
                                        <div class="info-value readonly" id="detail_cost_center"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-hash text-secondary"></i>
                                            RS Number:
                                        </div>
                                        <div class="info-value readonly" id="detail_rs_number"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-calendar text-danger"></i>
                                            Date:
                                        </div>
                                        <div class="info-value readonly" id="detail_date"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products & Objectives Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-package"></i>
                            Products & Objectives
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <h6 class="mb-3 fw-bold text-muted">
                                    <i class="ph-duotone ph-list-bullets me-2"></i>
                                    Selected Products
                                </h6>
                                <div class="product-list-container" id="requisition_product_list">
                                    <!-- Products will be populated here -->
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="mb-3 fw-bold text-muted">
                                    <i class="ph-duotone ph-target me-2"></i>
                                    Reason for Complain
                                </h6>
                                <div class="objectives-container">
                                    <div class="objectives-text" id="detail_objectives">
                                        <!-- Objectives will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-table"></i>
                            Detailed Product Information
                        </div>
                        <div class="detail-table-container">
                            <div id="detail_productDetailsContainer">
                                <!-- Product details table will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Complain Images Section -->
                    <div class="detail-section" id="complainImagesSection" style="display: none;">
                        <div class="section-header">
                            <i class="ph-duotone ph-images"></i>
                            Complain Images
                        </div>
                        <div class="row" id="detail_complain_images">
                            <!-- Images will be populated here -->
                        </div>
                    </div>

                    <!-- Status & Approval History Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-clock-clockwise"></i>
                            Status & Approval History
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="status-display-container">
                                    <h6 class="mb-3 fw-bold text-muted">
                                        <i class="ph-duotone ph-flag me-2"></i>
                                        Current Status
                                    </h6>
                                    <div class="current-status-badge" id="current_status_display">
                                        <!-- Current status will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="approval-history-container">
                                    <h6 class="mb-3 fw-bold text-muted">
                                        <i class="ph-duotone ph-chat-teardrop-text me-2"></i>
                                        Approval History & Notes
                                    </h6>
                                    <div class="approval-timeline" id="approval_history_list">
                                        <!-- Approval history will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        <i class="ph-duotone ph-x me-1"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Review -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="reviewModalLabel">
                        <i class="ph-duotone ph-note me-2"></i>
                        Review Complain Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm">
                        <input type="hidden" id="review_token" name="token">
                        <input type="hidden" id="review_id" name="id">

                        <!-- Request Info Display -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="ph-duotone ph-info me-2"></i>
                                    Request Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Requisition ID:</strong> <span
                                                id="review_requisition_id"></span></p>
                                        <p class="mb-2"><strong>Customer:</strong> <span
                                                id="review_customer"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Status:</strong> <span id="review_status"></span>
                                        </p>
                                        <p class="mb-2"><strong>Date:</strong> <span id="review_date"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Decision Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="ph-duotone ph-check-circle me-2"></i>
                                Your Decision <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="approve_radio"
                                        value="approve" required>
                                    <label class="form-check-label text-success fw-medium" for="approve_radio">
                                        <i class="ph-duotone ph-check-circle me-1"></i>
                                        Approve
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status"
                                        id="approve_with_review_radio" value="approve_with_review" required>
                                    <label class="form-check-label text-info fw-medium"
                                        for="approve_with_review_radio">
                                        <i class="ph-duotone ph-check-circle me-1"></i>
                                        Approve with Review
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="reject_radio"
                                        value="reject" required>
                                    <label class="form-check-label text-danger fw-medium" for="reject_radio">
                                        <i class="ph-duotone ph-x-circle me-1"></i>
                                        Reject
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-4" id="notes_section" style="display: none;">
                            <label for="review_notes" class="form-label fw-bold">
                                <i class="ph-duotone ph-note-pencil me-2"></i>
                                Notes/Comments
                                <span class="text-danger" id="notes_required_indicator">*</span>
                            </label>
                            <textarea class="form-control" id="review_notes" name="notes" rows="4"
                                placeholder="Enter your notes or comments here..."></textarea>
                            <div class="form-text">
                                <span id="notes_help_text">Notes are required for this action.</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ph-duotone ph-x me-1"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="submitReview">
                        <i class="ph-duotone ph-paper-plane-tilt me-1"></i>
                        Submit Review
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            #notes_section {
                transition: all 0.3s ease-in-out;
                overflow: hidden;
            }

            #notes_section.fade-in {
                animation: fadeInSlide 0.3s ease-in-out;
            }

            @keyframes fadeInSlide {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // === Core Helper Functions ===
            const showSuccessMessage = (message, title = 'Success') => {
                Swal.fire({
                    icon: 'success',
                    title,
                    text: message,
                    timer: 1500,
                    showConfirmButton: false
                });
            };

            const showErrorMessage = (message, title = 'Error') => {
                Swal.fire({
                    icon: 'error',
                    title,
                    text: message
                });
            };

            const showConfirmDialog = (options = {}) => {
                const defaults = {
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    icon: 'warning'
                };
                return Swal.fire({
                    ...defaults,
                    ...options,
                    showCancelButton: true,
                    reverseButtons: true
                });
            };

            // === Status and History Functions ===
            const populateStatusAndHistory = (data) => {
                const statusContainer = $('#current_status_display');
                const status = data.status || 'Unknown';

                const statusMap = {
                    'pending': {
                        class: 'status-badge-pending',
                        text: 'Pending Review'
                    },
                    'approved': {
                        class: 'status-badge-approved',
                        text: 'Approved'
                    },
                    'rejected': {
                        class: 'status-badge-rejected',
                        text: 'Rejected'
                    },
                    'in progress': {
                        class: 'status-badge-progress',
                        text: 'In Progress'
                    }
                };

                const statusInfo = statusMap[status.toLowerCase()] || {
                    class: 'status-badge-progress',
                    text: status
                };
                statusContainer.html(`<div class="current-status-badge ${statusInfo.class}">${statusInfo.text}</div>`);

                const historyContainer = $('#approval_history_list');
                const filteredLogs = (data.approval_logs || []).filter(log =>
                    log.status && log.status.toLowerCase() !== 'pending'
                );

                if (filteredLogs.length === 0) {
                    historyContainer.html(`
                    <div class="empty-history">
                        <i class="ph-duotone ph-clock-clockwise"></i>
                        <div class="fw-medium">No approval history yet</div>
                        <small class="text-muted">Approval history will appear here when available</small>
                    </div>
                `);
                    return;
                }

                const historyHtml = filteredLogs.map(log => {
                    const approverName = log.approver?.name || log.approver_nik || 'Unknown Approver';
                    const notes = log.notes || 'No notes provided';
                    const logStatus = log.status || 'N/A';
                    const createdDate = log.updated_at ? new Date(log.updated_at).toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'Unknown date';

                    const statusClass = logStatus.toLowerCase() === 'approved' ? 'approval-level-approved' :
                        logStatus.toLowerCase() === 'rejected' ? 'approval-level-rejected' :
                        'approval-level-default';
                    const itemClass = logStatus.toLowerCase() === 'approved' ?
                        'approval-item approval-item-approved' :
                        logStatus.toLowerCase() === 'rejected' ? 'approval-item approval-item-rejected' :
                        'approval-item';

                    return `
                    <div class="${itemClass}">
                        <div class="approval-meta">
                            <span class="approver-name">${approverName}</span>
                            <span class="approval-level ${statusClass}">${logStatus}</span>
                            <span class="text-muted ms-2">${createdDate}</span>
                        </div>
                        <div class="approval-notes ${notes === 'No notes provided' ? 'no-notes' : ''}">
                            ${notes}
                        </div>
                    </div>
                `;
                }).join('');

                historyContainer.html(historyHtml);
            };

            const renderDetailProductTable = (items) => {
                const container = $('#detail_productDetailsContainer');

                if (!items?.length) {
                    container.html(`
                    <div class="text-center py-5">
                        <i class="ph-duotone ph-table fs-1 text-muted mb-3 d-block"></i>
                        <h6 class="text-muted mb-2">No Product Details</h6>
                        <p class="text-muted small mb-0">No product details found for this requisition.</p>
                    </div>
                `);
                    return;
                }

                const materialTypeMap = {
                    'Raw': 'primary',
                    'Semi-Finished': 'warning text-dark',
                    'Finished': 'success',
                    'default': 'secondary'
                };

                const tableRowsHTML = items.map((item, index) => {
                    const allDetails = item.item_master?.item_details || [];
                    const specificDetail = allDetails.find(detail => detail.id === item.item_detail_id);

                    if (!specificDetail) {
                        return `
                        <tr class="table-danger">
                            <td colspan="8" class="text-center py-3">
                                <i class="ph-duotone ph-warning-circle text-danger me-2"></i>
                                Product Detail with ID ${item.item_detail_id} not found.
                            </td>
                        </tr>
                    `;
                    }

                    const materialType = specificDetail.material_type || '-';
                    const badgeClass = materialTypeMap[materialType] || materialTypeMap.default;

                    return `
                    <tr class="animate-row" style="animation-delay: ${index * 0.1}s">
                        <td class="text-center"><span class="badge bg-${badgeClass}">${materialType}</span></td>
                        <td class="fw-medium">${specificDetail.item_detail_code || '-'}</td>
                        <td>${specificDetail.item_detail_name || '-'}</td>
                        <td class="text-center"><span class="badge bg-light text-dark">${specificDetail.unit || '-'}</span></td>
                        <td class="text-center">
                            <input type="number" class="form-control text-center fw-bold" value="${item.quantity_required}" readonly
                                style="background: rgba(192, 127, 0, 0.1); border-color: rgba(192, 127, 0, 0.3);">
                        </td>
                        <td class="text-center">
                            <input type="number" class="form-control text-center fw-bold" value="${item.quantity_issued}" readonly
                                style="background: rgba(25, 135, 84, 0.1); border-color: rgba(25, 135, 84, 0.3);">
                        </td>
                        <td class="text-center">
                            <input type="date" class="form-control text-center" readonly
                                value="${item.batch_number ? new Date(item.batch_number).toISOString().split('T')[0] : ''}"
                                style="background: rgba(13, 110, 253, 0.1); border-color: rgba(13, 110, 253, 0.3);">
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control text-center" value="${item.remarks || ''}" readonly
                                style="background: rgba(108, 117, 125, 0.1); border-color: rgba(108, 117, 125, 0.3);">
                        </td>
                    </tr>
                `;
                }).join('');

                container.html(`
                <table class="table detail-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%;"><i class="ph-duotone ph-tag me-2"></i>Material Type</th>
                            <th style="width: 15%;"><i class="ph-duotone ph-barcode me-2"></i>Detail Code</th>
                            <th style="width: 20%;"><i class="ph-duotone ph-package me-2"></i>Detail Name</th>
                            <th style="width: 8%;"><i class="ph-duotone ph-ruler me-2"></i>Unit</th>
                            <th style="width: 12%;"><i class="ph-duotone ph-shopping-cart me-2"></i>QTY Required</th>
                            <th style="width: 12%;"><i class="ph-duotone ph-check-circle me-2"></i>QTY Issued</th>
                            <th style="width: 10%;"><i class="ph-duotone ph-calendar me-2"></i>Batch Number</th>
                            <th style="width: 18%;"><i class="ph-duotone ph-note me-2"></i>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>${tableRowsHTML}</tbody>
                </table>
            `);

                // Add animation styles once
                if (!$('#animate-styles').length) {
                    $('head').append(`
                    <style id="animate-styles">
                        @keyframes slideInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
                        .animate-row { animation: slideInUp 0.6s ease-out forwards; opacity: 0; }
                    </style>
                `);
                }
            };

            const renderComplainImages = (images) => {
                const section = $('#complainImagesSection');
                const container = $('#detail_complain_images');

                if (!images?.length) {
                    section.hide();
                    return;
                }

                section.show();
                container.empty();

                images.forEach((image, index) => {
                    const imageUrl = `{{ asset('storage/') }}/${image.image_path}`;
                    container.append(`
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="${imageUrl}" class="card-img-top image-clickable"
                                style="height: 200px; object-fit: cover; cursor: pointer;"
                                alt="Complain Image ${index + 1}"
                                data-image-src="${imageUrl}"
                                data-image-title="Complain Image ${index + 1}">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted">Image ${index + 1}</small>
                            </div>
                        </div>
                    </div>
                `);
                });
            };

            // === Review Modal Functions ===
            const showReviewModal = (requisitionId, token) => {
                const detailUrl = "{{ route('get.form.detail', ['id' => ':id']) }}".replace(':id', requisitionId);

                $.ajax({
                    url: detailUrl,
                    method: 'GET',
                    success: (data) => {
                        $('#review_token').val(token);
                        $('#review_id').val(requisitionId);
                        $('#review_requisition_id').text(data.no_srs || data.requisition_number || 'N/A');
                        $('#review_customer').text(data.customer?.name || 'N/A');
                        $('#review_status').text(data.status || 'Pending');

                        const date = data.request_date || data.created_at;
                        if (date) {
                            const formattedDate = new Date(date).toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });
                            $('#review_date').text(formattedDate);
                        } else {
                            $('#review_date').text('N/A');
                        }

                        $('#reviewForm')[0].reset();
                        updateNotesRequirement();

                        try {
                            new bootstrap.Modal(document.getElementById('reviewModal')).show();
                        } catch (e) {
                            $('#reviewModal').modal('show');
                        }
                    },
                    error: (xhr) => showErrorMessage(xhr.responseJSON?.message || 'Failed to load request details.')
                });
            };

            const updateNotesRequirement = () => {
                const selectedDecision = $('input[name="status"]:checked').val();
                const notesSection = $('#notes_section');
                const notesField = $('#review_notes');
                const helpText = $('#notes_help_text');

                if (selectedDecision === 'reject') {
                    // Tampilkan notes section untuk reject
                    notesSection.addClass('fade-in').slideDown(300);
                    notesField.prop('required', true);
                    helpText.text('Notes are required for rejection.');
                    notesField.attr('placeholder', 'Please provide reason for rejection...');
                } else if (selectedDecision === 'approve_with_review') {
                    // Tampilkan notes section untuk approve with review
                    notesSection.addClass('fade-in').slideDown(300);
                    notesField.prop('required', true);
                    helpText.text('Notes are required for approve with review.');
                    notesField.attr('placeholder', 'Please provide your review notes...');
                } else {
                    // Sembunyikan notes section untuk approve (default)
                    notesSection.slideUp(300, function() {
                        notesSection.removeClass('fade-in');
                    });
                    notesField.prop('required', false);
                    notesField.val(''); // Clear nilai notes
                }
            };

            // === AJAX Request Helper ===
            const makeApprovalRequest = (url, data, successCallback) => {
                $.ajax({
                    url,
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        ...data,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: () => {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your request',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading()
                        });
                    },
                    success: (response) => {
                        Swal.close();
                        if (response?.success) {
                            successCallback?.(response);
                            table.ajax.reload(null, false);
                        } else {
                            showErrorMessage(response?.message || 'Request failed');
                        }
                    },
                    error: (xhr) => {
                        Swal.close();
                        const errorMessages = {
                            500: 'Server error occurred. Please try again.',
                            422: 'Validation error. Please check your input.',
                            419: 'Session expired. Please refresh the page.'
                        };
                        const errorMsg = xhr.responseJSON?.message || errorMessages[xhr.status] ||
                            'Request failed';
                        showErrorMessage(errorMsg);
                    }
                });
            };

            $(document).ready(function() {
                const approvalDataUrl = "{{ route('get.approver.data') }}";
                const detailUrl = "{{ route('get.form.detail', ['id' => ':id']) }}";
                const approvalProcessUrl = "{{ route('complain.approval.process') }}";

                // Setup AJAX defaults
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                let isAdmin = false; // Variable to store admin status

                const table = $('#approvalTable').DataTable({
                    processing: false,
                    serverSide: false,
                    ajax: {
                        url: approvalDataUrl,
                        type: 'GET',
                        dataSrc: (json) => {
                            isAdmin = json.is_admin || false; // Store admin status
                            return json.data && Array.isArray(json.data) ? json.data : [];
                        },
                        error: () => showErrorMessage('Failed to load approval data')
                    },
                    columns: [{
                            data: 'requisition_number',
                            render: (data) => `<span class="fw-bold text-primary">${data || 'N/A'}</span>`
                        },
                        {
                            data: 'requisition_details.requester_name',
                            render: (data) => `<span class="badge bg-info">${data}</span>`,
                            width: '8%'
                        },
                        {
                            data: 'status',
                            render: (data) => {
                                const statusMap = {
                                    'approved': 'bg-success',
                                    'rejected': 'bg-danger',
                                    'pending': 'bg-warning'
                                };
                                const badgeClass = statusMap[data?.toLowerCase()] || 'bg-warning';
                                return `<span class="badge ${badgeClass}">${data || 'Pending'}</span>`;
                            },
                            width: '10%'
                        },
                        {
                            data: 'requisition_details.customer_name',
                            render: (data, type, row) => data || row.requisition_details?.customer?.name ||
                                'N/A'
                        },
                        {
                            data: 'requisition_details.route_to',
                            render: (data, type, row) => `<span class="badge bg-info">${data}</span>` ||
                                'N/A',
                        },
                        {
                            data: 'requisition_details.updated_at',
                            render: (data) => {
                                if (!data) return 'N/A';

                                const diffInSeconds = Math.floor((new Date() - new Date(data)) / 1000);
                                const timeUnits = [{
                                        unit: 'year',
                                        seconds: 31536000
                                    },
                                    {
                                        unit: 'month',
                                        seconds: 2592000
                                    },
                                    {
                                        unit: 'day',
                                        seconds: 86400
                                    },
                                    {
                                        unit: 'hour',
                                        seconds: 3600
                                    },
                                    {
                                        unit: 'minute',
                                        seconds: 60
                                    }
                                ];

                                if (diffInSeconds < 60) return 'Just now';

                                for (const {
                                        unit,
                                        seconds
                                    }
                                    of timeUnits) {
                                    const value = Math.floor(diffInSeconds / seconds);
                                    if (value >= 1) {
                                        return `<span class="fw-medium">${value} ${unit}${value > 1 ? 's' : ''} ago</span>`;
                                    }
                                }
                                return 'N/A';
                            },
                            width: '15%'
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: (data, type, row) => {
                                const token = row.requisition_details?.token || row.token || '';

                                // Jika token null, tampilkan icon checklist
                                if (data.token == null) {
                                    return `
                                    <div class="text-center">
                                        <i class="ph-duotone ph-check-circle text-success fs-4 action-btn-hover" data-tooltip="Already Processed"></i>
                                    </div>
                                `;
                                }

                                // Jika ada token, tampilkan button actions
                                let buttons = `
                                <button type="button" class="btn btn-info btn-sm detail-button action-btn-hover"
                                        data-id="${row.requisition_id}" data-tooltip="View Details">
                                    <i class="ph-duotone ph-eye"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm review-button action-btn-hover"
                                        data-token="${token}" data-tooltip="Review with Notes">
                                    <i class="ph-duotone ph-note"></i>
                                </button>
                            `;

                                // Jika user adalah admin, tambahkan tombol resend
                                if (isAdmin) {
                                    buttons += `
                                    <button type="button" class="btn btn-warning btn-sm resend-button action-btn-hover"
                                            data-token="${token}" data-tooltip="Resend Email">
                                        <i class="ph-duotone ph-paper-plane-tilt"></i>
                                    </button>
                                `;
                                }

                                return `<div class="action-btn-group">${buttons}</div>`;
                            },
                            width: '15%'
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    responsive: true,
                    language: {
                        processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                        emptyTable: '<div class="text-center py-4"><i class="ph-duotone ph-inbox ph-3x text-muted mb-3"></i><br><span class="text-muted">No approval requests found</span></div>',
                        zeroRecords: '<div class="text-center py-4"><i class="ph-duotone ph-magnifying-glass ph-3x text-muted mb-3"></i><br><span class="text-muted">No matching records found</span></div>'
                    }
                });

                const searchInput = $('#approvalTable_filter input').unbind();
                let debounceTimer;
                searchInput.on('keyup', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => table.search($(this).val()).draw(), 500);
                });

                // === Custom Tooltip System ===
                const initTooltips = () => {
                    $(document).off('mouseenter.tooltip mouseleave.tooltip', '.action-btn-hover')
                        .on('mouseenter.tooltip', '.action-btn-hover', function() {
                            const $btn = $(this);
                            const text = $btn.attr('data-tooltip');
                            if (!text || $btn.data('tooltip-element')) return;

                            const rect = this.getBoundingClientRect();
                            const $tooltip = $(`
                            <div class="custom-tooltip" style="
                                position: fixed; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white; padding: 8px 12px; border-radius: 8px; font-size: 12px;
                                white-space: nowrap; z-index: 9999; opacity: 0; transform: translateY(5px);
                                transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                                left: ${rect.left + rect.width/2}px; top: ${rect.top - 35}px;
                                transform: translateX(-50%) translateY(5px);
                            ">${text}</div>
                        `).appendTo('body');

                            $btn.data('tooltip-element', $tooltip);
                            setTimeout(() => $tooltip.css({
                                opacity: 1,
                                transform: 'translateX(-50%) translateY(0)'
                            }), 10);

                            const hideTimer = setTimeout(() => {
                                $tooltip.css({
                                    opacity: 0,
                                    transform: 'translateX(-50%) translateY(5px)'
                                });
                                setTimeout(() => {
                                    $tooltip.remove();
                                    $btn.removeData('tooltip-element');
                                }, 200);
                            }, 3000);
                            $btn.data('tooltip-timer', hideTimer);
                        })
                        .on('mouseleave.tooltip', '.action-btn-hover', function() {
                            const $btn = $(this);
                            const $tooltip = $btn.data('tooltip-element');
                            const timer = $btn.data('tooltip-timer');

                            if (timer) clearTimeout(timer);
                            if ($tooltip) {
                                $tooltip.css({
                                    opacity: 0,
                                    transform: 'translateX(-50%) translateY(5px)'
                                });
                                setTimeout(() => {
                                    $tooltip.remove();
                                    $btn.removeData(['tooltip-element', 'tooltip-timer']);
                                }, 200);
                            }
                        });
                };

                table.on('draw', initTooltips);
                initTooltips();

                // === Event Handlers ===
                // === Resend Email Handler ===
                $('#approvalTable tbody').on('click', '.resend-button', function() {
                    const token = $(this).data('token');
                    const resendUrl = "{{ route('complain.approval.resend', ['token' => ':token']) }}".replace(
                        ':token', token);

                    showConfirmDialog({
                        title: 'Resend Approval Email?',
                        text: 'This will generate a new approval link and send it to the approver.',
                        confirmButtonText: 'Yes, Resend',
                        cancelButtonText: 'Cancel',
                        icon: 'question'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            makeApprovalRequest(resendUrl, {}, (response) => {
                                showSuccessMessage(response.message ||
                                    'Approval email has been resent successfully!',
                                    'Email Sent');
                                table.ajax.reload(null, false);
                            });
                        }
                    });
                });

                $('#approvalTable tbody').on('click', '.detail-button', function() {
                        const complainId = $(this).data('id');
                        const finalUrl = detailUrl.replace(':id', complainId);
                        $('#detailModal').modal('show');

                        $.ajax({
                            url: finalUrl,
                            method: 'GET',
                            success: (data) => {
                                // Populate modal fields
                                $('#detail_customer_name').text(data.customer?.name || '-');
                                $('#detail_customer_address').text(data.customer?.address || '-');
                                $('#detail_account').text(data.account || '-');
                                $('#detail_cost_center').text(data.cost_center || '-');
                                $('#detail_rs_number').text(data.no_srs || data.requisition_number ||
                                    '-');
                                $('#detail_objectives').text(data.reason_for_replacement ||
                                    'No reason specified');

                                // Format and set date
                                const date = data.request_date || data.created_at;
                                $('#detail_date').text(date ? new Date(date).toLocaleDateString(
                                    'en-GB', {
                                        day: '2-digit',
                                        month: 'short',
                                        year: 'numeric'
                                    }) : '-');

                                // Populate product list
                                const items = data.requisition_items || [];
                                const selectedProductsDiv = $('#requisition_product_list');

                                if (items.length > 0) {
                                    const uniqueMasters = [...new Map(items.filter(item => item
                                                .item_master)
                                            .map(item => [item.item_master.id, item.item_master]))
                                        .values()
                                    ];

                                    const productListHtml = uniqueMasters.map(master =>
                                        `<div class="product-item">
                                        <i class="ph-duotone ph-package"></i>
                                        <span class="fw-medium">${master.item_master_code} - ${master.item_master_name}</span>
                                    </div>`
                                    ).join('');
                                    selectedProductsDiv.html(productListHtml);
                                } else {
                                    selectedProductsDiv.html(`
                                    <div class="text-center py-4 text-muted">
                                        <i class="ph-duotone ph-package fs-2 mb-2 d-block"></i>
                                        <span>No products selected</span>
                                    </div>
                                `);
                                }

                                populateStatusAndHistory(data);
                                renderDetailProductTable(items);
                                renderComplainImages(data.complain_images || []);
                            },
                            error: (xhr) => {
                                showErrorMessage(xhr.responseJSON?.message ||
                                    'Failed to load complain details.');
                                $('#detailModal').modal('hide');
                            }
                        });
                    })
                    .on('click', '.review-button', function() {
                        const token = $(this).data('token');
                        const requisitionId = $(this).closest('tr').find('.detail-button').data('id');
                        showReviewModal(requisitionId, token);
                    });

                // === Modal Event Handlers ===
                $('#detailModal').on('hidden.bs.modal', function() {
                    $('#detail_customer_name, #detail_customer_address, #detail_account, #detail_cost_center, #detail_rs_number, #detail_date, #detail_objectives')
                        .text('');
                    $('#requisition_product_list, #detail_productDetailsContainer, #detail_complain_images, #current_status_display, #approval_history_list')
                        .html('');
                    $('#complainImagesSection').hide();
                    $('.payment-proof-section').remove();
                });

                $('#reviewModal').on('hidden.bs.modal', function() {
                    $('#reviewForm')[0].reset();
                    $('#review_token, #review_id').val('');
                    $('#submitReview').prop('disabled', false).html(
                        '<i class="ph-duotone ph-paper-plane-tilt me-1"></i>Submit Review');
                    updateNotesRequirement();
                });

                $('input[name="status"]').on('change', updateNotesRequirement);

                $('#submitReview').on('click', function() {
                    const form = $('#reviewForm')[0];
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    const formData = {
                        token: $('#review_token').val(),
                        id: $('#review_id').val(),
                        status: $('input[name="status"]:checked').val(),
                        notes: $('#review_notes').val()
                    };

                    // Validasi notes berdasarkan status
                    if ((formData.status === 'reject' || formData.status === 'approve_with_review') && !formData
                        .notes.trim()) {
                        showErrorMessage('Notes are required for this action.');
                        return;
                    }

                    $('#submitReview').prop('disabled', true).html(
                        '<i class="spinner-border spinner-border-sm me-1"></i>Processing...');

                    makeApprovalRequest(approvalProcessUrl, formData, () => {
                        $('#reviewModal').modal('hide');
                        let message, title;

                        switch (formData.status) {
                            case 'approve':
                                message = 'Request approved successfully!';
                                title = 'Approved';
                                break;
                            case 'approve_with_review':
                                message = 'Request approved with review successfully!';
                                title = 'Approved with Review';
                                break;
                            case 'reject':
                                message = 'Request rejected successfully!';
                                title = 'Rejected';
                                break;
                            default:
                                message = 'Request processed successfully!';
                                title = 'Processed';
                        }
                        table.ajax.reload(null, false);
                        showSuccessMessage(message, title);
                    });
                });

                // === Image Preview ===
                $(document).on('click', '.image-clickable', function() {
                    Swal.fire({
                        imageUrl: $(this).data('image-src'),
                        imageAlt: $(this).data('image-title'),
                        showConfirmButton: false,
                        showCloseButton: true,
                        customClass: {
                            image: 'img-fluid'
                        }
                    });
                });

                // === Initialize UI ===
                $('#approvalTable_filter input').attr({
                    placeholder: 'Search approvals...',
                    class: 'form-control'
                });
                $('.dataTables_wrapper').css({
                    animation: 'fadeIn 0.5s ease-in',
                    opacity: '0'
                });
                setTimeout(() => $('.dataTables_wrapper').css('opacity', '1'), 200);
            });
        </script>
    @endpush
</x-app-layout>
