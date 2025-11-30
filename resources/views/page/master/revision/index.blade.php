<x-app-layout>
    @section('title')
    Revision Management
    @endsection

    {{-- Include Complaint Table Styles Component --}}
    @include('components.complaint-table-styles')

    @push('css')
    <!-- Select2 CSS -->
    <link href="{{ asset('assets/vendor/select/select2.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        #revision-table .action-btn-group .action-btn-hover {
            padding: 8px 16px !important;
            border-radius: 8px !important;
            min-width: 80px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
    
        #revision-table .action-btn-group .action-btn-hover i {
            font-size: 1.1rem;
        }
    
        #revision-table .action-btn-group .action-btn-hover.btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            border: none;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.2);
        }
    
        #revision-table .action-btn-group .action-btn-hover.btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #545b62 100%);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
            transform: translateY(-2px);
        }
    
        .action-tooltip {
            background: rgba(0, 0, 0, 0.85);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            position: absolute;
            z-index: 9999;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            pointer-events: none;
        }
    
        .action-tooltip.show {
            opacity: 1;
            visibility: visible;
        }
    
        .action-tooltip.below {
            margin-top: 8px;
        }
    </style>
    @endpush

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Revision Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Revision</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tabel Revision -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar with Enhanced Styling -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- Optional: Add title or description here -->
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-notebook"></i>
                        Revision List
                    </h4>
                    <p class="table-subtitle">
                        Manage document revision numbers and tracking
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="revision-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Revision Number</th>
                                <th>Revision Count</th>
                                <th>Revision Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Revision -->
    <div class="modal fade" id="revisionModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header-enhanced d-flex align-items-center justify-content-between">
                    <h5 class="modal-title-enhanced mb-0" id="revisionModalLabel">
                        <i class="ph-duotone ph-notebook"></i>
                        Edit Revision
                    </h5>
                    <button type="button" class="btn-close btn-close-white fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="revisionForm">
                    @csrf
                    <div class="modal-body modal-body-enhanced">
                        <input type="hidden" id="revision_id" name="id">
                        
                        <div class="mb-3">
                            <label for="revision_number" class="form-label">
                                <i class="ph-duotone ph-hash me-1"></i>
                                Revision Number
                            </label>
                            <input type="text" class="form-control" id="revision_number" name="revision_number" 
                                placeholder="e.g., REV-001" required>
                            <div class="invalid-feedback" data-error-for="revision_number"></div>
                        </div>

                        <div class="mb-3">
                            <label for="revision_count" class="form-label">
                                <i class="ph-duotone ph-number-square-one me-1"></i>
                                Revision Count
                            </label>
                            <input type="number" class="form-control" id="revision_count" name="revision_count" 
                                placeholder="Enter revision count" min="0" required>
                            <div class="invalid-feedback" data-error-for="revision_count"></div>
                        </div>

                        <div class="mb-3">
                            <label for="revision_date" class="form-label">
                                <i class="ph-duotone ph-calendar me-1"></i>
                                Revision Date
                            </label>
                            <input type="date" class="form-control" id="revision_date" name="revision_date" required>
                            <div class="invalid-feedback" data-error-for="revision_date"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="submit" id="saveRevisionBtn">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>
                            Save Changes
                        </button>
                        <button class="btn btn-danger" data-bs-dismiss="modal" type="button">
                            <i class="ph-duotone ph-x me-1"></i>
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <!--js-->
    <script src="{{ asset('assets') }}/js/select.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        function warningMessage(message, title = 'Warning') {
            Swal.fire({
                icon: 'warning',
                title: title,
                text: message
            });
        }

        // confirmDialog: returns a Promise, so you can use .then()
        function confirmDialog({
            title = 'Are you sure?',
            text = 'This action cannot be undone!',
            confirmButtonText = 'Yes',
            cancelButtonText = 'Cancel',
            confirmButtonColor = '#3085d6',
            cancelButtonColor = '#d33',
            icon = 'warning',
            reverseButtons = true
        } = {}) {
            return Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonColor,
                cancelButtonColor,
                confirmButtonText,
                cancelButtonText,
                reverseButtons
            });
        }

        $(document).ready(function () {
            // === DataTable ===
            let table = $('#revision-table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('master.revision.getdata') }}",
                    dataSrc: 'data'
                },
                columns: [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return `<span class="badge bg-secondary rounded-pill">${meta.row + meta.settings._iDisplayStart + 1}</span>`;
                    }
                },
                {
                    data: 'revision_number',
                    name: 'revision_number',
                    render: function(data) {
                        return `<div class="d-flex align-items-center">
                                    <i class="ph-duotone ph-file-text me-2 text-primary" style="font-size:1.25rem;"></i>
                                    <span class="fw-medium" style="font-size:1.05rem;">${data}</span>
                               </div>`;
                    }
                },
                {
                    data: 'revision_count',
                    name: 'revision_count',
                    render: function(data) {
                        return `<span class="badge bg-info-subtle text-info rounded-2 px-3 py-2" style="font-size:0.95rem;">
                                    Count: ${data}
                                </span>`;
                    }
                },{
                    data: 'revision_date',
                    name: 'revision_date',
                    render: function(data) {
                        return `<div class="d-flex align-items-center">
                                    <i class="ph-duotone ph-calendar-blank me-2 text-success" style="font-size:1.1rem;"></i>
                                    <span>${data}</span>
                                </div>`;
                    }
                },{
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row){
                        return `
                            <div class="action-btn-group">
                                <button class="action-btn-hover btn-secondary btn-edit-revision" 
                                    data-id="${row.id}"
                                    data-revision-number="${row.revision_number}"
                                    data-revision-count="${row.revision_count}"
                                    data-revision-date="${row.revision_date}"
                                    data-tooltip="Edit Revision">
                                    <i class="ph-duotone ph-pencil-simple"></i>
                                </button>
                            </div>
                        `;
                    }
                }
                ]
            });

            // === DataTable Search Debounce ===
            let searchInput = $('#revision-table_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    table.search(searchInput.val()).draw();
                }, 500);
            });

            // === Modal Edit Revision ===
            $(document).on('click', '.btn-edit-revision', function () {
                const btn = $(this);
                $('#revisionModalLabel').text('Edit Revision');

                $('#revision_id').val(btn.data('id'));
                $('#revision_number').val(btn.data('revision-number'));
                $('#revision_count').val(btn.data('revision-count'));
                $('#revision_date').val(btn.data('revision-date'));

                new bootstrap.Modal(document.getElementById('revisionModal')).show();
            });

            // === Submit Form Update Revision ===
            $('#revisionForm').on('submit', function (e) {
                e.preventDefault();
                
                // Clear previous validation errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                let formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('master.revision.update') }}",
                    method: "POST",
                    data: formData,
                    success: function (res) {
                        $('#revisionModal').modal('hide');
                        table.ajax.reload(null, false);
                        successMessage('Revision updated successfully');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) { // Validation Error
                            let errors = xhr.responseJSON.errors;
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    let inputField = $(`[name="${key}"]`);
                                    let errorContainer = $(`[data-error-for="${key}"]`);
                                    
                                    inputField.addClass('is-invalid');
                                    errorContainer.text(errors[key][0]);
                                }
                            }
                            errorMessage('Please fix the errors in the form.');
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    }
                });
            });

            // Custom Tooltip Handler for Action Buttons
            function initActionTooltips() {
                // Remove any existing event handlers to prevent duplicates
                $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                $(document).on('mouseenter.customTooltip', '.action-btn-hover', function(e) {
                    const tooltipText = $(this).attr('data-tooltip');
                    if (tooltipText && !$(this).data('tooltip-element')) {
                        const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                        $('body').append(tooltip);

                        const button = $(this);
                        let isDestroyed = false;

                        // Function to update tooltip position
                        function updateTooltipPosition() {
                            if (isDestroyed || !button.is(':visible') || !tooltip.parent().length) {
                                return;
                            }

                            // Get button position
                            const buttonOffset = button.offset();
                            if (!buttonOffset) return;

                            const buttonWidth = button.outerWidth();
                            const buttonHeight = button.outerHeight();
                            const tooltipWidth = tooltip.outerWidth();
                            const tooltipHeight = tooltip.outerHeight();
                            const windowWidth = $(window).width();
                            const windowHeight = $(window).height();
                            const scrollTop = $(window).scrollTop();

                            // Calculate position
                            let left = buttonOffset.left + (buttonWidth / 2) - (tooltipWidth / 2);
                            let top = buttonOffset.top - tooltipHeight - 12;

                            // Horizontal bounds checking
                            if (left < 10) {
                                left = 10;
                            } else if (left + tooltipWidth > windowWidth - 10) {
                                left = windowWidth - tooltipWidth - 10;
                            }

                            // Vertical bounds checking
                            if (top < scrollTop + 10) {
                                top = buttonOffset.top + buttonHeight + 12;
                                tooltip.addClass('below');
                            } else {
                                tooltip.removeClass('below');
                            }

                            tooltip.css({
                                left: left + 'px',
                                top: top + 'px',
                                opacity: 1,
                                visibility: 'visible',
                                zIndex: 9999
                            });
                        }

                        // Initial positioning
                        setTimeout(() => {
                            updateTooltipPosition();
                        }, 10);

                        // Show tooltip with delay
                        setTimeout(() => {
                            if (!isDestroyed) {
                                tooltip.addClass('show');
                            }
                        }, 100);

                        // Store tooltip element and update function
                        button.data('tooltip-element', tooltip);
                        button.data('update-tooltip-position', updateTooltipPosition);
                        button.data('tooltip-destroyed', false);

                        // Create unique namespace for this tooltip
                        const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        button.data('tooltip-id', tooltipId);

                        // Listen for scroll events with throttling
                        let scrollTimeout;
                        function throttledUpdate() {
                            if (scrollTimeout) {
                                clearTimeout(scrollTimeout);
                            }
                            scrollTimeout = setTimeout(() => {
                                updateTooltipPosition();
                            }, 10);
                        }

                        $(window).on('scroll.' + tooltipId + ' resize.' + tooltipId, throttledUpdate);
                        $('.dataTables_scrollBody').on('scroll.' + tooltipId, throttledUpdate);
                        $('.table-responsive').on('scroll.' + tooltipId, throttledUpdate);
                        $('#revision-table_wrapper').on('scroll.' + tooltipId, throttledUpdate);

                        // Store cleanup function
                        button.data('tooltip-cleanup', function() {
                            isDestroyed = true;
                            $(window).off('.' + tooltipId);
                            $('.dataTables_scrollBody').off('.' + tooltipId);
                            $('.table-responsive').off('.' + tooltipId);
                            $('#revision-table_wrapper').off('.' + tooltipId);
                        });
                    }
                });

                $(document).on('mouseleave.customTooltip', '.action-btn-hover', function(e) {
                    const button = $(this);
                    const tooltip = button.data('tooltip-element');
                    const cleanup = button.data('tooltip-cleanup');

                    if (tooltip) {
                        button.data('tooltip-destroyed', true);

                        tooltip.removeClass('show');
                        setTimeout(() => {
                            tooltip.remove();
                        }, 200);

                        // Execute cleanup
                        if (cleanup) {
                            cleanup();
                        }

                        // Clear all data
                        button.removeData('tooltip-element');
                        button.removeData('update-tooltip-position');
                        button.removeData('tooltip-id');
                        button.removeData('tooltip-cleanup');
                        button.removeData('tooltip-destroyed');
                    }
                });
            }

            // Initialize tooltips after DataTable is ready
            table.on('draw', function() {
                initActionTooltips();
            });

            // Initialize tooltips for the first load
            initActionTooltips();
        });
    </script>
    @endpush
</x-app-layout>