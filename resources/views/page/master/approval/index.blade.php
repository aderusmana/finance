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
                    <button type="button" class="btn-close btn-close-white fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <label class="form-label">Approver Sequence</label>

                            <div class="d-flex gap-2 mb-2">
                                <div class="flex-grow-1">
                                    <select class="form-select" id="source_approver_select">
                                        <option value="" selected disabled>Select Role to Add...</option>
                                        </select>
                                </div>
                                <button type="button" class="btn btn-primary" id="btn-add-to-sequence" disabled>
                                    <i class="ph-bold ph-plus"></i> Add
                                </button>
                            </div>

                            <div class="p-3 border rounded bg-light" style="min-height: 150px; max-height: 300px; overflow-y: auto;">
                                <ul id="approver_sequence_list" class="list-group list-group-flush bg-transparent">
                                    <li class="list-group-item bg-transparent text-center text-muted fst-italic empty-msg">
                                        No approvers added yet.
                                    </li>
                                </ul>
                                <div class="invalid-feedback d-block" id="sequence_error_msg" data-error-for="approvers"></div>
                            </div>
                            <div class="form-text">
                                The approval process will follow the sequence numbers (1, 2, 3...).
                            </div>
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
            // ==========================================
            // 1. HELPER FUNCTIONS (SweetAlert & Utilities)
            // ==========================================

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

            // ==========================================
            // 2. MAIN LOGIC (Document Ready)
            // ==========================================

            $(document).ready(function() {
                let allSubCategories = [];
                let existingPaths = [];
                let categoryMapping = {}; // Store mapping: Category -> [SubCategories]

                // --- A. INITIALIZE SELECT2 ---

                // Setup dropdown styling
                $('#category_id, #sub_category_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#ApproverModal')
                });

                $('#source_approver_select').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Select Role to Add...',
                    dropdownParent: $('#ApproverModal')
                });


                // --- B. APPROVER SEQUENCE LOGIC (List 1, 2, 3...) ---

                // Fungsi menambahkan item ke visual list
                function addApproverItem(id, name) {
                    // Hapus pesan "No approvers added" jika ada
                    $('#approver_sequence_list .empty-msg').remove();

                    // Hitung nomor urut selanjutnya
                    let count = $('#approver_sequence_list .sequence-item').length + 1;

                    let html = `
                        <li class="list-group-item d-flex align-items-center justify-content-between sequence-item p-2 mb-2 border rounded bg-white shadow-sm">
                            <div class="d-flex align-items-center w-100">
                                <span class="badge bg-primary rounded-circle me-3 sequence-number"
                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    ${count}
                                </span>

                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">${name}</span>
                                </div>

                                <input type="hidden" name="approvers[]" value="${id}">
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item ms-2 rounded-circle"
                                    style="width: 32px; height: 32px; padding: 0;" title="Remove">
                                <i class="ph-bold ph-trash"></i>
                            </button>
                        </li>
                    `;

                    $('#approver_sequence_list').append(html);
                }

                // Fungsi merapikan ulang nomor urut (1, 2, 3) setelah hapus
                function reorderSequence() {
                    let list = $('#approver_sequence_list');
                    let items = list.find('.sequence-item');

                    if (items.length === 0) {
                        list.html('<li class="list-group-item bg-transparent text-center text-muted fst-italic empty-msg">No approvers added yet.</li>');
                    } else {
                        items.each(function(index) {
                            $(this).find('.sequence-number').text(index + 1);
                        });
                    }
                }

                // Event Click: Tombol "Add" Approver
                $('#btn-add-to-sequence').on('click', function() {
                    let select = $('#source_approver_select');
                    let id = select.val();
                    let name = select.find(':selected').text();

                    if (!id) {
                        warningMessage('Please select a role first.');
                        return;
                    }

                    addApproverItem(id, name);
                    select.val('').trigger('change'); // Reset dropdown setelah add
                });

                // Event Click: Tombol "Trash" (Hapus Item)
                $(document).on('click', '.btn-remove-item', function() {
                    $(this).closest('li').remove();
                    reorderSequence();
                });


                // --- C. CATEGORY & SUB-CATEGORY LOGIC (CORE FIX) ---

                /**
                 * Fungsi Sentral untuk mengisi Sub-Category.
                 * Dipakai saat: 1. User ganti kategori, 2. Saat tombol Edit ditekan.
                 */
                function populateSubCategories(selectedCategory, selectedSubCategory = null) {
                    let subCategorySelect = $('#sub_category_id');
                    subCategorySelect.empty();
                    subCategorySelect.append(new Option('Select a sub-category', '', true, true)); // Placeholder

                    // Cek apakah kategori punya sub-category
                    if (selectedCategory && categoryMapping[selectedCategory] && categoryMapping[selectedCategory].length > 0) {

                        // ENABLE dropdown agar bisa dipilih/diganti
                        subCategorySelect.prop('disabled', false);

                        categoryMapping[selectedCategory].forEach(subVal => {
                            // Cek apakah kombinasi Category+SubCategory ini sudah ada di DB (biar gak duplikat)
                            const pathExists = existingPaths.some(path =>
                                path.category === selectedCategory && path.sub_category === subVal
                            );

                            let newOption = new Option(subVal, subVal, false, false);

                            // Disable opsi jika sudah terpakai di DB, KECUALI jika itu adalah data diri sendiri yg sedang diedit
                            if (pathExists && subVal !== selectedSubCategory) {
                                $(newOption).prop('disabled', true);
                            }
                            subCategorySelect.append(newOption);
                        });

                        // Jika ada value terpilih (Mode Edit), set value-nya
                        if (selectedSubCategory) {
                            subCategorySelect.val(selectedSubCategory).trigger('change');
                        } else {
                            subCategorySelect.val('').trigger('change');
                        }

                    } else {
                        // Jika tidak ada sub-category, disable dropdown
                        subCategorySelect.prop('disabled', true);
                    }
                }

                // Event Change: Saat user mengubah Category secara manual
                $('#category_id').on('change', function() {
                    let selectedCategory = $(this).val();
                    let sourceSelect = $('#source_approver_select');
                    let btnAdd = $('#btn-add-to-sequence');
                    const isEditMode = $('#ApproverForm').attr('data-mode') === 'edit';

                    // Logic Enable/Disable Input Approver
                    if (selectedCategory) {
                        sourceSelect.prop('disabled', false);
                        btnAdd.prop('disabled', false);
                    } else {
                        sourceSelect.prop('disabled', true);
                        btnAdd.prop('disabled', true);
                    }

                    // PENTING: Jika sedang mode edit, jangan auto-populate via event change ini
                    // karena akan menimpa logika pengisian data dari AJAX edit.
                    if (isEditMode) return;

                    // Jika mode Create, populate normal
                    populateSubCategories(selectedCategory);
                });


                // --- D. LOAD SERVER DATA ---

                function loadDropdownData() {
                    // Get Categories
                    $.ajax({
                        url: "{{ route('get.categories') }}",
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let categorySelect = $('#category_id');
                            categoryMapping = data.subCategories || {};
                            existingPaths = data.existingPaths || [];

                            categorySelect.empty().append('<option selected disabled value="">Choose a category...</option>');
                            if (data.categories) {
                                data.categories.forEach(cat => categorySelect.append(new Option(cat, cat)));
                            }
                        },
                        error: function() { errorMessage('Failed to load category data.'); }
                    });

                    // Get Approver Roles
                    $.ajax({
                        url: "{{ route('get.approver.name') }}",
                        method: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            let sourceSelect = $('#source_approver_select');
                            sourceSelect.empty().append(new Option('Select Role to Add...', '', true, true));
                            if (data.approverName) {
                                $.each(data.approverName, function(key, value) {
                                    sourceSelect.append(new Option(value, key, false, false));
                                });
                            }
                        }
                    });
                }
                loadDropdownData();


                // --- E. DATATABLE CONFIGURATION ---

                let table = $('#approvertable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('get.approverlist') }}",
                    columns: [
                        {
                            data: 'id', name: 'id', orderable: false, searchable: false,
                            render: function(data, type, row, meta) {
                                return `<span class="badge bg-secondary rounded-pill">${meta.row + meta.settings._iDisplayStart + 1}</span>`;
                            }
                        },
                        {
                            data: 'category', name: 'category',
                            render: function(data) {
                                let iconClass = data === 'BG' ? 'ph-building' : data === 'Customer' ? 'ph-user' : 'ph-question';
                                return `<div class="d-flex align-items-center text-black">
                                            <i class="ph-duotone ${iconClass} me-2 text-primary" style="font-size:1.25rem;"></i>
                                            <span class="fw-medium" style="font-size:1.05rem;">${data}</span>
                                    </div>`;
                            }
                        },
                        {
                            data: 'sub_category', name: 'sub_category',
                            render: function(data) {
                                return data ?
                                    `<span class="badge bg-info-subtle text-info rounded-2 px-2 py-1"><i class="ph-duotone ph-tag me-1"></i>${data}</span>` :
                                    `<span class="badge bg-light-subtle text-secondary rounded-2 px-2 py-1">Non Sub-category</span>`;
                            }
                        },
                        {
                            data: 'sequence_approvers', name: 'sequence_approvers',
                            render: function(data) {
                                if (!Array.isArray(data) || data.length === 0) return '<span class="text-muted">No approvers assigned</span>';
                                // Render list 1, 2, 3 di tabel
                                const listItems = data.map((approver, index) =>
                                    `<li class="d-flex align-items-center mb-1">
                                        <span class="badge bg-light text-dark border border-secondary rounded-circle me-2" style="width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:10px;">${index+1}</span>
                                        <span class="fw-medium text-black">${approver}</span>
                                    </li>`
                                ).join('');
                                return `<ul style="list-style-type: none; padding-left: 0; margin-bottom: 0;">${listItems}</ul>`;
                            }
                        },
                        {
                            data: 'id', name: 'action', orderable: false, searchable: false,
                            render: function(data) {
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
                        }
                    ]
                });

                // Search Debounce (Agar tidak reload tiap ketik huruf)
                let searchInput = $('#approvertable_filter input');
                searchInput.unbind();
                let debounceTimer;
                searchInput.bind('keyup', function(e) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() { table.search(searchInput.val()).draw(); }, 500);
                });


                // --- F. FORM HANDLING (CREATE, EDIT, SUBMIT) ---

                function resetFormState() {
                    const form = $('#ApproverForm');
                    form[0].reset();
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').text('');

                    // Reset States
                    $('#category_id').val(null).trigger('change').prop('disabled', false);
                    $('#sub_category_id').val(null).trigger('change').prop('disabled', true);

                    // Reset Approver Inputs
                    $('#source_approver_select').val(null).trigger('change').prop('disabled', true);
                    $('#btn-add-to-sequence').prop('disabled', true);

                    // Reset Visual List
                    $('#approver_sequence_list').html('<li class="list-group-item bg-transparent text-center text-muted fst-italic empty-msg">No approvers added yet.</li>');
                    $('#sequence_error_msg').hide();
                }

                // 1. OPEN CREATE MODAL
                $('#btn-create-approver').on('click', function() {
                    resetFormState();
                    $('#ApproverForm').attr('data-mode', 'create');
                    $('#ApproverForm').attr('action', "{{ route('approvers.store') }}");
                    $('#ApproverModalLabel').html('<i class="ph-duotone ph-user-plus"></i> Create New Approver');
                    $('#ApproverModal').modal('show');
                });

                // 2. OPEN EDIT MODAL / DELETE ACTION
                $('#approvertable').on('click', '.action-btn-hover', function(e) {
                    e.preventDefault();

                    // --- A. EDIT ---
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

                                // 1. Set Category (Disable agar user tidak mengganti kategori utama sembarangan)
                                $('#category_id').val(data.category_id).trigger('change').prop('disabled', true);

                                // 2. Populate & Set Sub Category
                                // Panggil helper function kita untuk mengisi opsi dan set value
                                populateSubCategories(data.category_id, data.sub_category_id);

                                // Pastikan dropdown ENABLED jika memang ada sub-kategorinya
                                if (categoryMapping[data.category_id] && categoryMapping[data.category_id].length > 0) {
                                    $('#sub_category_id').prop('disabled', false);
                                }

                                // 3. Rebuild Approver List (1, 2, 3...)
                                $('#approver_sequence_list').empty();
                                if (data.approver_user_ids && Array.isArray(data.approver_user_ids)) {
                                    data.approver_user_ids.forEach(function(roleName) {
                                        addApproverItem(roleName, roleName);
                                    });
                                }

                                // 4. Enable Input Approver di mode edit
                                $('#source_approver_select').prop('disabled', false);
                                $('#btn-add-to-sequence').prop('disabled', false);

                                $('#ApproverModalLabel').html('<i class="ph-duotone ph-user-gear"></i> Edit Approver Sequence');
                                $('#ApproverModal').modal('show');
                            },
                            error: function(xhr) {
                                errorMessage('Could not fetch approver data.');
                            }
                        });

                    // --- B. DELETE ---
                    } else if ($(this).hasClass('btn-danger')) {
                        let approverId = $(this).data('id');
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
                                        successMessage(res.message || 'Approver deleted successfully!');
                                    },
                                    error: function(xhr) {
                                        errorMessage('Failed to delete approver.');
                                    }
                                });
                            }
                        });
                    }
                });

                // 3. SUBMIT FORM
                $('#ApproverForm').on('submit', function(e) {
                    e.preventDefault();
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');
                    $('#sequence_error_msg').hide();

                    // Validasi: Pastikan ada minimal 1 approver
                    if ($('#approver_sequence_list .sequence-item').length === 0) {
                        $('#sequence_error_msg').text('Please add at least one approver.').show();
                        return;
                    }

                    let form = $(this);
                    let url = form.attr('action');
                    let formData = new FormData(this);
                    let mode = form.attr('data-mode');

                    if (mode === 'edit') {
                        formData.append('_method', 'PUT');

                        // FIX: Input yang 'disabled' tidak terkirim via FormData.
                        // Kita harus append manual category_id agar validasi backend tidak gagal.
                        if($('#category_id').prop('disabled')) {
                            formData.append('category_id', $('#category_id').val());
                        }
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

                            // Update local cache untuk mencegah duplikasi create tanpa refresh page
                            if (mode === 'create') {
                                existingPaths.push({
                                    category: formData.get('category_id'),
                                    sub_category: formData.get('sub_category_id')
                                });
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) { // Error Validasi Laravel
                                let errors = xhr.responseJSON.errors;
                                for (let key in errors) {
                                    // Handle error array approvers
                                    if (key === 'approvers') {
                                        $('#sequence_error_msg').text(errors[key][0]).show();
                                    } else {
                                        let input = $(`[name="${key}"], [name="${key}[]"]`);
                                        let errorContainer = $(`[data-error-for="${key}"]`);

                                        if (input.hasClass('select2-hidden-accessible')) {
                                            input.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                                        } else {
                                            input.addClass('is-invalid');
                                        }
                                        if (errorContainer.length) {
                                            errorContainer.text(errors[key][0]);
                                        }
                                    }
                                }
                                errorMessage('Please fix the errors in the form.');
                            } else {
                                errorMessage(xhr.responseJSON?.message || 'An unexpected error occurred.');
                            }
                        }
                    });
                });


                // --- G. TOOLTIPS ---
                // Menangani tooltip hover tombol action
                function initActionTooltips() {
                    $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                    $(document).on('mouseenter.customTooltip', '.action-btn-hover', function(e) {
                        const tooltipText = $(this).attr('data-tooltip');
                        if (tooltipText && !$(this).data('tooltip-element')) {
                            const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                            $('body').append(tooltip);

                            const button = $(this);

                            // Simple positioning
                            const offset = button.offset();
                            tooltip.css({
                                position: 'absolute',
                                left: (offset.left + button.outerWidth() / 2 - tooltip.outerWidth() / 2) + 'px',
                                top: (offset.top - tooltip.outerHeight() - 10) + 'px',
                                zIndex: 9999
                            }).addClass('show');

                            button.data('tooltip-element', tooltip);
                        }
                    });

                    $(document).on('mouseleave.customTooltip', '.action-btn-hover', function(e) {
                        const button = $(this);
                        const tooltip = button.data('tooltip-element');
                        if (tooltip) {
                            tooltip.remove();
                            button.removeData('tooltip-element');
                        }
                    });
                }

                // Init tooltip saat tabel di-load atau di-refresh
                table.on('draw', function() {
                    initActionTooltips();
                });
                initActionTooltips();
            });
        </script>
    @endpush
</x-app-layout>
