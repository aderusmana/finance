<x-app-layout>
    @section('title')
        Approver List
    @endsection

    {{-- Include Complaint Table Styles Component --}}
    @include('components.complaint-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Approver Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Settings
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approver List</a>
                </li>
            </ul>
        </div>
    </div>

    <!--  -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar with Enhanced Styling -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- <h5 class="mb-1" style="color: rgb(76, 61, 61); font-weight: 700;">
                        <i class="ph-duotone ph-users-three me-2 text-warning"></i>
                        Approver Management
                    </h5>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-info me-1"></i>
                        Configure approval workflow and sequences
                    </p> -->
                </div>
                <div>
                    <button class="btn new-complain-btn" type="button" id="btn-create-approver">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Approver</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list-checks"></i>
                        Approvers List
                    </h4>
                    <p class="table-subtitle">
                        Manage approval workflow configurations and sequences
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="approvertable">
                        <thead>
                            <tr class="text-black">
                                <th>No</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Approver</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- modal create approver -->
    <div class="modal fade" id="ApproverModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header-enhanced d-flex align-items-center justify-content-between">
                    <h5 class="modal-title-enhanced mb-0" id="ApproverModalLabel">
                        <i class="ph-duotone ph-user-plus"></i>
                        Create Approver
                    </h5>
                    <button type="button" class="btn-close btn-close-white fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced">
                    <form action="#" method="POST" data-mode="create" id="ApproverForm">
                        @csrf

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                            </select>
                            <div class="invalid-feedback" data-error-for="category_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="sub_category_id" class="form-label">Sub Category</label>
                            <select class="form-select" id="sub_category_id" name="sub_category_id">
                            </select>
                            <input type="hidden" name="sub_category_id" id="hidden_sub_category_id" disabled>
                            <div class="invalid-feedback" data-error-for="sub_category_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="approvers" class="form-label">Approver Sequence</label>
                            <select class="form-select" id="approvers" name="approvers[]" multiple="multiple" required>
                            </select>
                            <div class="invalid-feedback" data-error-for="approvers"></div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="submit" id="saveApproverBtn" form="ApproverForm">
                        Save Changes
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/@phosphor-icons/web"></script>

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

            $(document).ready(function() {
                let allSubCategories = [];
                let existingPaths = [];

                // === Initialize Select2 ===
                $('#approvers, #category_id, #sub_category_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#ApproverModal')
                });
                // Atur placeholder secara spesifik
                $('#category_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select a category',
                    dropdownParent: $('#ApproverModal')
                });
                $('#sub_category_id').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select a sub-category',
                    dropdownParent: $('#ApproverModal')
                });
                $('#approvers').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select approvers in order',
                    dropdownParent: $('#ApproverModal')
                });

                // atur urutan selected approvers
                $('#approvers').on('select2:select', function(e) {
                    let id = e.params.data.id;
                    let option = $(this).find('option[value="' + id + '"]');
                    option.appendTo(this);
                });

                // === Dynamic Sub-Category Handling ===
                // Only two categories allowed in UI: 'BG' and 'Customer'
                $('#category_id').on('change', function() {
                    let selectedCategory = $(this).val();
                    let subCategorySelect = $('#sub_category_id');
                    let approversSelect = $('#approvers');
                    const isEditMode = $('#ApproverForm').attr('data-mode') === 'edit';

                    if (isEditMode) return;

                    // Reset and disable downstream fields
                    subCategorySelect.val(null).trigger('change');
                    approversSelect.val(null).trigger('change').prop('disabled', true);

                    if (selectedCategory === 'BG') {
                        // BG has no sub-categories
                        subCategorySelect.prop('disabled', true);
                        approversSelect.prop('disabled', false);
                    } else if (selectedCategory === 'Customer') {
                        // Customer supports only 'CBD' sub-category
                        subCategorySelect.prop('disabled', false);

                        const subValue = 'CBD';
                        const pathExists = existingPaths.some(path =>
                            path.category === selectedCategory && path.sub_category === subValue
                        );

                        const subCategoryData = [{
                            id: subValue,
                            text: subValue,
                            disabled: pathExists
                        }];

                        subCategorySelect.empty().select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#ApproverModal'),
                            placeholder: 'Select a sub-category',
                            data: subCategoryData
                        });
                        subCategorySelect.val(null).trigger('change');
                        // Allow creating Customer without sub-category — enable approver selection immediately
                        approversSelect.prop('disabled', false);
                    } else {
                        // Any other category - keep sub-category disabled and prevent approver selection
                        subCategorySelect.prop('disabled', true);
                        approversSelect.prop('disabled', true);
                    }
                });

                $('#sub_category_id').on('change', function() {
                    let selectedSubCategory = $(this).val();
                    let approversSelect = $('#approvers');

                    // If current category is Customer, approvers are allowed even without sub-category
                    let currentCategory = $('#category_id').val();

                    if (currentCategory === 'Customer') {
                        approversSelect.prop('disabled', false);
                        return;
                    }

                    // For non-Customer categories, only enable approver when sub-category is chosen
                    if (selectedSubCategory) {
                        approversSelect.prop('disabled', false);
                    } else {
                        approversSelect.val(null).trigger('change').prop('disabled', true);
                    }
                });

                // === Load ALL Dropdown Data via AJAX ===
                function loadDropdownData() {
                    // Fetch Categories & Sub-Categories
                    $.ajax({
                        url: "{{ route('get.categories') }}",
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let categorySelect = $('#category_id');
                            let subCategorySelect = $('#sub_category_id');

                            categorySelect.empty().append(
                                '<option selected disabled value="">Choose a category...</option>');
                            subCategorySelect.empty();

                            // Restrict categories in the UI to these two values
                            const allowedCategories = ['BG', 'Customer'];
                            categorySelect.empty().append('<option selected disabled value="">Choose a category...</option>');
                            allowedCategories.forEach(cat => {
                                categorySelect.append(new Option(cat, cat));
                            });
                            allSubCategories = [];
                            if (data.subCategories) {
                                data.subCategories.forEach(subCat => {
                                    allSubCategories.push({
                                        value: subCat,
                                        text: subCat
                                    });
                                });
                            }
                            if (data.existingPaths) {
                                existingPaths = data.existingPaths;
                            }

                            categorySelect.trigger('change');
                        },
                        error: function() {
                            errorMessage('Failed to load category data.');
                        }
                    });

                    // 2. Fetch Approver Names
                    $.ajax({
                        url: "{{ route('get.approver.name') }}",
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let approverSelect = $('#approvers');
                            approverSelect.empty();

                            console.log(data.approverName);

                            if (data.approverName) {
                                $.each(data.approverName, function(key, value) {
                                    approverSelect.append(new Option(value, key, false, false));
                                });
                            }

                            approverSelect.trigger('change');
                        },
                        error: function(xhr) {
                            errorMessage('Failed to load approver data.');
                        }
                    });
                }

                loadDropdownData();

                // === DataTable Initialization ===
                let table = $('#approvertable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('get.approverlist') }}",
                    columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return `<span class="badge bg-secondary rounded-pill">${meta.row + meta.settings._iDisplayStart + 1}</span>`;
                        }
                    }, {
                        data: 'category',
                        name: 'category',
                        render: function(data) {
                            let iconClass = data === 'BG' ? 'ph-building' :
                                data === 'Customer' ? 'ph-user' : 'ph-question';
                            return `<div class="d-flex align-items-center text-black">
                                    <i class="ph-duotone ${iconClass} me-2 text-primary" style="font-size:1.25rem;"></i>
                                    <span class="fw-medium" style="font-size:1.05rem;">${data}</span>
                               </div>`;
                        }
                    }, {
                        data: 'sub_category',
                        name: 'sub_category',
                        render: function(data, type, row) {
                            return data ?
                                `<span class="badge bg-info-subtle text-info rounded-2 px-2 py-1" style="font-size:1rem;">
                                <i class="ph-duotone ph-tag me-1" style="font-size:1.05rem;"></i>${data}
                             </span>` :
                                `<span class="badge bg-light-subtle text-secondary rounded-2 px-2 py-1" style="font-size:1rem;">
                                <i class="ph-duotone ph-minus-circle me-1" style="font-size:1.05rem;"></i>Non Sub-category
                             </span>`;
                        }
                    }, {
                        data: 'sequence_approvers',
                        name: 'sequence_approvers',
                        render: function(data, type, row) {
                            if (!Array.isArray(data) || data.length === 0) {
                                return '<span class="text-muted">No approvers assigned</span>';
                            }
                            const listItems = data.map(approver =>
                                `<li class="d-flex align-items-center mb-1 ">
                                <i class="ph-duotone ph-user-check fs-5 me-2 text-success"></i>
                                <span class="fw-medium text-black">${approver}</span>
                            </li>`
                            ).join('');
                            return `<ul style="list-style-type: none; padding-left: 0; margin-bottom: 0;">${listItems}</ul>`;
                        }
                    }, {
                        data: 'id',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-btn-group">
                                    <button type="button" class="btn btn-secondary action-btn-hover" data-id="${data}" data-tooltip="Edit Approver">
                                        <i class="ph-duotone ph-eraser"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger action-btn-hover" data-id="${data}" data-tooltip="Delete Approver">
                                        <i class="ph-duotone ph-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }]
                });

                // === DataTable Search Debounce ===
                let searchInput = $('#approvertable_filter input');
                searchInput.unbind();
                let debounceTimer;
                searchInput.bind('keyup', function(e) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        table.search(searchInput.val()).draw();
                    }, 500);
                });

                // === Modal: Show for Create ===
                $('#btn-create-approver').on('click', function() {
                    resetFormState(); // Gunakan helper function
                    $('#ApproverForm').attr('data-mode', 'create');
                    $('#ApproverForm').attr('action', '{{ route('approvers.store') }}');

                    // [MODIFIKASI] Atur state awal saat modal create dibuka
                    $('#sub_category_id').prop('disabled', true);
                    $('#approvers').prop('disabled', true);

                    $('#ApproverModalLabel').html(
                    '<i class="ph-duotone ph-user-plus"></i> Create New Approver');
                    $('#ApproverModal').modal('show');
                });

                // === Modal: Show for Edit ===
                $('#approvertable').on('click', '.action-btn-hover', function(e) {
                    e.preventDefault();

                    if ($(this).hasClass('btn-secondary')) {
                        let approverId = $(this).data('id');
                        let editUrl = `/approvers/${approverId}/edit`;
                        let updateUrl = `/approvers/${approverId}`;

                        $.ajax({
                            url: editUrl,
                            method: 'GET',
                            success: function(data) {
                                resetFormState();

                                $('#ApproverForm').attr('data-mode', 'edit');
                                $('#ApproverForm').attr('action', updateUrl);

                                $('#category_id').val(data.category_id).trigger('change').prop(
                                    'disabled', true);
                                $('#sub_category_id').val(data.sub_category_id).trigger('change')
                                    .prop('disabled', true);

                                // [MODIFIKASI] Pastikan field approver SELALU aktif saat mode edit
                                $('#approvers').prop('disabled', false).val(data.approver_user_ids)
                                    .trigger('change');

                                $('#ApproverModalLabel').html(
                                    '<i class="ph-duotone ph-user-gear"></i> Edit Approver Sequence'
                                    );
                                $('#ApproverModal').modal('show');
                            },
                            error: function(xhr) {
                                errorMessage(xhr.responseJSON?.message ||
                                    'Could not fetch approver data.');
                            }
                        });
                    } else if ($(this).hasClass('btn-danger')) {
                        // Delete functionality
                        let approverId = $(this).data('id');
                        // IMPORTANT: Replace with your actual destroy route
                        let deleteUrl = `/approvers/${approverId}`;

                        confirmDialog({
                            text: 'You won\'t be able to revert this!',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: deleteUrl,
                                    method: 'POST',
                                    data: {
                                        _method: 'DELETE',
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(res) {
                                        table.ajax.reload(null, false);
                                        successMessage(res.message ||
                                            'Approver deleted successfully!');
                                    },
                                    error: function(xhr) {
                                        errorMessage(xhr.responseJSON?.message ||
                                            'Failed to delete approver.');
                                    }
                                });
                            }
                        });
                    }
                });

                function resetFormState() {
                    const form = $('#ApproverForm');
                    form[0].reset();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').text('');

                    // Aktifkan kembali field yang mungkin di-disable saat edit
                    $('#category_id, #sub_category_id').prop('disabled', false);

                    // Reset dan disable field secara berurutan
                    $('#category_id').val(null).trigger('change');
                    $('#sub_category_id').val(null).trigger('change').prop('disabled', true);
                    $('#approvers').val(null).trigger('change').prop('disabled', true);
                }

                // === Form Submit Handler (Create & Edit) ===
                $('#ApproverForm').on('submit', function(e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    let form = $(this);
                    let url = form.attr('action');
                    let formData = new FormData(this);
                    let mode = form.attr('data-mode'); // Ambil mode form

                    if (mode === 'edit') {
                        formData.append('_method', 'PUT');
                    }

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#ApproverModal').modal('hide');
                            table.ajax.reload(null, false);
                            successMessage(res.message || 'Operation successful!');

                            if (mode === 'create') {
                                const newCategory = formData.get('category_id');
                                const newSubCategory = formData.get('sub_category_id');

                                // Tambahkan path baru ke array di sisi klien
                                existingPaths.push({
                                    category: newCategory,
                                    sub_category: newSubCategory
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) { // Validation Error
                                let errors = xhr.responseJSON.errors;
                                for (let key in errors) {
                                    let input = $(`[name="${key}"], [name="${key}[]"]`);
                                    let errorContainer = $(`[data-error-for="${key}"]`);

                                    if (input.hasClass('select2-hidden-accessible')) {
                                        input.next('.select2-container').find('.select2-selection')
                                            .addClass('is-invalid');
                                    } else {
                                        input.addClass('is-invalid');
                                    }

                                    if (errorContainer.length) {
                                        errorContainer.text(errors[key][0]);
                                    }
                                }
                                errorMessage('Please fix the errors in the form.');
                            } else {
                                errorMessage(xhr.responseJSON?.message ||
                                    'An unexpected error occurred.');
                            }
                        }
                    });
                });

                // === Delete Handler ===
                $('#approvertable').on('click', '.delete-approver-btn', function(e) {
                    e.preventDefault();
                    let approverId = $(this).data('id');
                    // IMPORTANT: Replace with your actual destroy route
                    let deleteUrl = `/approvers/${approverId}`;

                    confirmDialog({
                        text: 'You won\'t be able to revert this!',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: deleteUrl,
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    table.ajax.reload(null, false);
                                    successMessage(res.message ||
                                        'Approver deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message ||
                                        'Failed to delete approver.');
                                }
                            });
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
                                    position: 'absolute',
                                    left: left + 'px',
                                    top: top + 'px',
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
                            const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(
                                2, 9);
                            button.data('tooltip-id', tooltipId);

                            // Listen for scroll events with throttling
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
                            $('.dataTables_scrollBody').on('scroll.' + tooltipId, throttledUpdate);
                            $('.table-responsive').on('scroll.' + tooltipId, throttledUpdate);
                            $('#approvertable_wrapper').on('scroll.' + tooltipId, throttledUpdate);

                            // Store cleanup function
                            button.data('tooltip-cleanup', function() {
                                isDestroyed = true;
                                $(window).off('.' + tooltipId);
                                $('.dataTables_scrollBody').off('.' + tooltipId);
                                $('.table-responsive').off('.' + tooltipId);
                                $('#approvertable_wrapper').off('.' + tooltipId);
                                if (scrollTimeout) {
                                    clearTimeout(scrollTimeout);
                                }
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
