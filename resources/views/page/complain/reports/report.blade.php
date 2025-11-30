<x-app-layout>
    @section('title')
        Approval Report
    @endsection

    {{-- Include Complaint Table Styles Template --}}
    @include('components.complaint-table-styles')

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Approval Report</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approval Report</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- Optional: Add filter buttons or other controls here -->
                </div>
                <div>
                    <button class="btn btn-success" type="button" onclick="printSelectedReports()" id="printSelectedBtn" disabled>
                        <i class="ph-bold ph-printer"></i>
                        <span>Print Selected (<span id="selectedCount">0</span>)</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-chart-bar"></i>
                        Approval Reports
                    </h4>
                    <p class="table-subtitle">
                        Track all approval activities and requisition status
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="approvalReportTable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Requisition No</th>
                                <th>Requester</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="approvalDetailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="approvalDetailModalLabel">
                        <i class="ph-duotone ph-info"></i>
                        Approval Detail
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced">
                    <!-- Approval Details will be populated here -->
                    <div class="row">
                        <div class="col-12">
                            <div class="detail-section">
                                <div class="section-header">
                                    <i class="ph-duotone ph-info-circle"></i>
                                    Requisition Information
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-file-text text-primary"></i>
                                                    Requisition No:
                                                </div>
                                                <div class="info-value readonly" id="detail_requisition_no"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-user text-info"></i>
                                                    Requester:
                                                </div>
                                                <div class="info-value readonly" id="detail_requester"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-calendar text-success"></i>
                                                    Request Date:
                                                </div>
                                                <div class="info-value readonly" id="detail_request_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-check-circle text-warning"></i>
                                                    Status:
                                                </div>
                                                <div class="info-value readonly" id="detail_status"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-user-check text-danger"></i>
                                                    Approver:
                                                </div>
                                                <div class="info-value readonly" id="detail_approver"></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">
                                                    <i class="ph-duotone ph-calendar-check text-secondary"></i>
                                                    Approval Date:
                                                </div>
                                                <div class="info-value readonly" id="detail_approval_date"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <div class="section-header">
                                    <i class="ph-duotone ph-chat-text"></i>
                                    Description
                                </div>
                                <div class="objectives-container">
                                    <div class="objectives-text" id="detail_description">
                                        <!-- Description will be populated here -->
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section" id="itemsSection" style="display: none;">
                                <div class="section-header">
                                    <i class="ph-duotone ph-list"></i>
                                    Requested Items
                                </div>
                                <div class="properties-container">
                                    <div id="detail_items" class="bg-light p-3 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">
                        <i class="ph-duotone ph-x me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Custom styles for checkbox selection */
        .row-selector {
            cursor: pointer;
        }
        
        #selectAll {
            cursor: pointer;
        }
        
        /* Disabled button styling */
        #printSelectedBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Selected row highlight */
        .selected-row {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }
        
        /* Action button group spacing */
        .action-btn-group {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>

    <script>
        // === SweetAlert2 Reusable Functions ===
        function successMessage(message, title = 'Success', timer = 1500) {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                timer: timer,
                showConfirmButton: false
            });
        }

        function errorMessage(message, title = 'Error') {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message
            });
        }

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function truncateText(text, maxLength = 100) {
            if (!text) return '-';
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }

        function getStatusBadge(data){
        const status = (data || '').toLowerCase().trim();
            switch (status) {
                case 'pending':
                    return '<span class="badge status-badge-lg status-pending">Pending</span>';
                case 'approved':
                    return '<span class="badge status-badge-lg status-approved">Approved</span>';
                case 'rejected':
                case 'failed':
                    return '<span class="badge status-badge-lg status-rejected">Rejected</span>';
                case 'in progress':
                    return '<span class="badge status-badge-lg status-in-progress">In Progress</span>';
                case 'completed':
                case 'success':
                    return '<span class="badge status-badge-lg status-completed">Completed</span>';
                case 'cancelled':
                    return '<span class="badge status-badge-lg status-cancelled">Cancelled</span>';
                case 'payment proof':
                    return '<span class="badge status-badge-lg status-payment-proof">Payment Proof</span>';
                default:
                    return '<span class="badge status-badge-lg bg-secondary">' + data + '</span>';
            }
        }

        // Function to update selected count and button state
        function updateSelectedCount() {
            const selectedCheckboxes = $('.row-selector:checked');
            const count = selectedCheckboxes.length;
            
            $('#selectedCount').text(count);
            $('#printSelectedBtn').prop('disabled', count === 0);
            
            // Update select all checkbox state
            const totalCheckboxes = $('.row-selector').length;
            const selectAllCheckbox = $('#selectAll');
            
            if (count === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (count === totalCheckboxes) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
                selectAllCheckbox.prop('checked', false);
            }
        }

        // Function to print selected reports
        function printSelectedReports() {
            const selectedIds = [];
            $('.row-selector:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                errorMessage('Please select at least one report to print.');
                return;
            }

            // Confirm before proceeding
            Swal.fire({
                title: 'Print Selected Reports',
                text: `Are you sure you want to print ${selectedIds.length} selected report(s)?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Print!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Preparing reports for printing...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create form and submit
                    const form = $('<form>', {
                        method: 'POST',
                        action: '{{ route("report.print.bulk") }}',
                        target: '_blank', // Open in new tab
                        style: 'display: none;'
                    });

                    // Add CSRF token
                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: '{{ csrf_token() }}'
                    }));

                    // Add selected IDs as array
                    selectedIds.forEach(function(id) {
                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'selected_ids[]',
                            value: id
                        }));
                    });

                    // Debug log
                    console.log('Sending selected IDs:', selectedIds);
                    console.log('Form action:', '{{ route("report.print.bulk") }}');

                    // Add additional data if needed
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'print_type',
                        value: 'bulk'
                    }));

                    // Append form to body and submit
                    $('body').append(form);
                    
                    try {
                        form.submit();
                        
                        // Close loading and show success
                        setTimeout(() => {
                            Swal.close();
                            successMessage(`${selectedIds.length} report(s) sent for printing!`);
                            
                            // Clear selections and row highlighting
                            $('.row-selector').prop('checked', false);
                            $('.selected-row').removeClass('selected-row');
                            $('#selectAll').prop('checked', false).prop('indeterminate', false);
                            updateSelectedCount();
                        }, 1500);
                        
                    } catch (error) {
                        console.error('Form submission error:', error);
                        Swal.close();
                        errorMessage('Error submitting form. Please try again.');
                    } finally {
                        // Clean up form
                        form.remove();
                    }
                }
            });
        }

        $(document).ready(function () {
            // Check if table element exists
            if (!$('#approvalReportTable').length) {
                console.error('Table element not found!');
                errorMessage('Table initialization failed: Element not found');
                return;
            }

            // === DataTable ===
            let table = $('#approvalReportTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('get.complain.data') }}", // Sesuaikan dengan route Anda
                    type: 'GET',
                    dataSrc: 'data',
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error);
                        errorMessage('Error loading data: ' + error);
                    }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        width: '4%',
                        render: function (data, type, row, meta) {
                            return `<input type="checkbox" class="row-selector form-check-input" value="${data}">`;
                        }
                    },
                    {
                        data: 'no_srs',
                        name: 'no_srs',
                        width: '15%',
                        render: function (data, type, row) {
                            return data ? `<span class="fw-bold text-primary">${data}</span>` : '-';
                        }
                    },
                    {
                        data: 'requester',
                        name: 'requester',
                        width: '15%',
                        render: function (data, type, row) {
                            if (data && data.name) {
                                return `<span class="fw-medium">${data.name}</span>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '10%',
                        render: function (data, type, row) {
                            // Normalize status untuk comparison
                            const status = (data || '').toLowerCase().trim();
                            switch (status) {
                                case 'pending':
                                    return '<span class="badge status-badge-lg status-pending">Pending</span>';
                                case 'approved':
                                    return '<span class="badge status-badge-lg status-approved">Approved</span>';
                                case 'rejected':
                                case 'failed':
                                    return '<span class="badge status-badge-lg status-rejected">Rejected</span>';
                                case 'in progress':
                                    return '<span class="badge status-badge-lg status-in-progress">In Progress</span>';
                                case 'completed':
                                case 'success':
                                    return '<span class="badge status-badge-lg status-completed">Completed</span>';
                                case 'cancelled':
                                    return '<span class="badge status-badge-lg status-cancelled">Cancelled</span>';
                                case 'payment proof':
                                    return '<span class="badge status-badge-lg status-payment-proof">Payment Proof</span>';
                                default:
                                    return '<span class="badge status-badge-lg bg-secondary">' + data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        width: '12%',
                        render: function (data, type, row) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: null,
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '8%',
                        render: function (data, type, row) {
                            const safeRow = JSON.stringify(row).replace(/"/g, '&quot;');
                            return `
                                <div class="action-btn-group">
                                    <button type="button" class="btn btn-info btn-sm detail-button action-btn-hover" 
                                            data-approval="${safeRow}"
                                            data-tooltip="View Details">
                                        <i class="ph-duotone ph-eye"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[5, 'desc']], // Order by created_at descending
                pageLength: 25,
                responsive: true,
                language: {
                    processing: "Loading data...",
                    emptyTable: "No data available",
                    zeroRecords: "No matching records found"
                },
                drawCallback: function() {
                    // Initialize tooltips after each draw
                    initActionTooltips();
                    // Update selection count and state
                    updateSelectedCount();
                }
            });

            let searchInput = $('#approvalReportTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            // Select All checkbox handler
            $('#selectAll').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.row-selector').prop('checked', isChecked);
                
                // Update row highlighting
                $('#approvalReportTable tbody tr').each(function() {
                    if (isChecked) {
                        $(this).addClass('selected-row');
                    } else {
                        $(this).removeClass('selected-row');
                    }
                });
                
                updateSelectedCount();
            });

            // Update count after table draw
            table.on('draw', function() {
                updateSelectedCount();
            });

            // Row checkbox handler  
            $('#approvalReportTable tbody').on('change', '.row-selector', function() {
                const row = $(this).closest('tr');
                if ($(this).is(':checked')) {
                    row.addClass('selected-row');
                } else {
                    row.removeClass('selected-row');
                }
                updateSelectedCount();
            });

            // Detail button click handler
            $('#approvalReportTable tbody').on('click', '.detail-button', function () {
                try {
                    const approvalData = $(this).data('approval');
                    console.log('Approval Data:', approvalData); // Debug log

                    // Populate modal with approval data
                    $('#detail_requisition_no').text(approvalData.no_srs || '-');
                    $('#detail_requester').text(approvalData.requester ? approvalData.requester.name : '-');
                    $('#detail_request_date').text(formatDate(approvalData.created_at));
                    $('#detail_status').html(getStatusBadge(approvalData.status));
                    
                    // Handle approver from approval_logs
                    $('#detail_approver').text(approvalData.route_to);
                    
                    $('#detail_approval_date').text(formatDate(approvalData.updated_at));
                    $('#detail_description').text(approvalData.reason_for_replacement || '-');

                    // Handle Items if available
                    if (approvalData.items && approvalData.items.length > 0) {
                        let itemsHtml = '<ul class="list-group">';
                        approvalData.items.forEach(function (item, index) {
                            itemsHtml += `
                                <li class="list-group-item">
                                    <strong>${index + 1}. ${item.name || 'Item'}</strong>
                                    <br>
                                    <small class="text-muted">Quantity: ${item.quantity || 'N/A'}</small>
                                    ${item.description ? `<br><small>${item.description}</small>` : ''}
                                </li>
                            `;
                        });
                        itemsHtml += '</ul>';
                        $('#detail_items').html(itemsHtml);
                        $('#itemsSection').show();
                    } else {
                        $('#itemsSection').hide();
                    }

                    $('#approvalDetailModal').modal('show');

                } catch (error) {
                    console.error('Error parsing approval data:', error);
                    errorMessage('Error loading approval details');
                }
            });

            // Custom Tooltip Handler for Action Buttons
            function initActionTooltips() {
                $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                $(document).on('mouseenter.customTooltip', '.action-btn-hover', function (e) {
                    const tooltipText = $(this).attr('data-tooltip');
                    if (tooltipText && !$(this).data('tooltip-element')) {
                        const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                        $('body').append(tooltip);

                        const button = $(this);
                        let isDestroyed = false;

                        function updateTooltipPosition() {
                            if (isDestroyed || !button.is(':visible') || !tooltip.parent().length) {
                                return;
                            }

                            const buttonOffset = button.offset();
                            if (!buttonOffset) return;

                            const buttonWidth = button.outerWidth();
                            const buttonHeight = button.outerHeight();
                            const tooltipWidth = tooltip.outerWidth();
                            const tooltipHeight = tooltip.outerHeight();
                            const windowWidth = $(window).width();
                            const scrollTop = $(window).scrollTop();

                            let left = buttonOffset.left + (buttonWidth / 2) - (tooltipWidth / 2);
                            let top = buttonOffset.top - tooltipHeight - 12;

                            if (left < 10) {
                                left = 10;
                            } else if (left + tooltipWidth > windowWidth - 10) {
                                left = windowWidth - tooltipWidth - 10;
                            }

                            if (top < scrollTop + 10) {
                                top = buttonOffset.top + buttonHeight + 12;
                                tooltip.addClass('below');
                            } else {
                                tooltip.removeClass('below');
                            }

                            tooltip.css({
                                position: 'absolute',
                                left: left + 'px',
                                top: top + 'px',
                                zIndex: 9999
                            });
                        }

                        setTimeout(() => {
                            updateTooltipPosition();
                        }, 10);

                        setTimeout(() => {
                            if (!isDestroyed) {
                                tooltip.addClass('show');
                            }
                        }, 100);

                        button.data('tooltip-element', tooltip);

                        const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        button.data('tooltip-id', tooltipId);

                        let scrollTimeout;
                        function throttledUpdate() {
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                            scrollTimeout = setTimeout(() => {
                                if (!isDestroyed) {
                                    updateTooltipPosition();
                                }
                            }, 10);
                        }

                        $(window).on('scroll.' + tooltipId + ' resize.' + tooltipId, throttledUpdate);

                        button.data('tooltip-cleanup', function () {
                            isDestroyed = true;
                            $(window).off('.' + tooltipId);
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                        });
                    }
                });

                $(document).on('mouseleave.customTooltip', '.action-btn-hover', function (e) {
                    const button = $(this);
                    const tooltip = button.data('tooltip-element');
                    const cleanup = button.data('tooltip-cleanup');

                    if (tooltip) {
                        tooltip.removeClass('show');
                        setTimeout(() => {
                            tooltip.remove();
                        }, 200);

                        if (cleanup) {
                            cleanup();
                        }

                        button.removeData('tooltip-element');
                        button.removeData('tooltip-id');
                        button.removeData('tooltip-cleanup');
                    }
                });
            }

            // Initialize tooltips after DataTable is ready
            table.on('draw', function () {
                initActionTooltips();
            });

            // Initialize tooltips after table is fully loaded
            setTimeout(function() {
                initActionTooltips();
            }, 1000);

            // Enhanced search placeholder
            $('#approvalReportTable_filter input').attr({
                'placeholder': 'Search approval reports...',
                'class': 'form-control'
            });

            // Add fade-in animation to DataTable wrapper
            $('.dataTables_wrapper').css({
                'animation': 'fadeInUp 0.8s ease-out forwards',
                'opacity': '0'
            });

            setTimeout(function () {
                $('.dataTables_wrapper').css('opacity', '1');
            }, 200);
        });
    </script>
    @endpush
</x-app-layout>