<x-app-layout>
    @section('title')
    Free Goods Requisition
    @endsection

    {{-- Memuat file CSS Hijau Anda dari komponen --}}
    @include('components.freegoods-table-styles')

    @push('css')
    {{-- [DITAMBAHKAN] CSS untuk Select2 agar filter terlihat rapi --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        .modal-body hr {
            margin-top: 1.75rem;
            margin-bottom: 1.75rem;
        }
        .modal-body h5.fw-bold {
            margin-bottom: 1.25rem !important;
        }
        .table-responsive h6.fw-bold {
             margin-bottom: 1rem;
        }
        .view-modal-card .row > [class^="col-"] {
            margin-bottom: 1rem;
        }

        /* Style untuk badge Requester */
        .requester-badge {
            background-color: #4A5568;
            color: #ffffff;
            padding: 0.35em 0.75em;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 50rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }
        .requester-badge i {
            font-size: 1.2em;
        }
    </style>
    @endpush


    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Free Goods Requisition List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-note-pencil f-s-16"></i> Forms
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Free Goods Requisition</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
             {{-- [UPDATE] Layout Filter & Tombol disesuaikan seperti Sample --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Grup Filter di Kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold">Filter by:</span>
                    <select id="statusFilter" class="form-select select2" style="width: 200px;">
                        <option value="all">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Recalled">Recalled</option>
                    </select>
                    <button id="resetFilters" class="btn btn-secondary border" data-bs-toggle="tooltip" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                {{-- Tombol Create di Kanan --}}
                <div>
                    <button class="btn new-freegoods-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#fgModal" id="btn-create-fg">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Free Goods</span>
                    </button>
                </div>
            </div>

            {{-- [UPDATE] Container tabel disesuaikan seperti Sample --}}
            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list"></i>
                        Free Goods Requisition List
                    </h4>
                    <p class="table-subtitle">View, manage and track all free goods requisition submissions</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="fgTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No. FG</th>
                                <th>Requester</th>
                                <th>Customer</th>
                                <th>Request Date</th>
                                <th>Category</th>
                                <th>Route To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================== --}}
    {{-- MODAL & SCRIPT (Isi modal tidak diubah) --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="fgModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="loading-overlay" style="display: none;">
                    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-3 fw-bold">Processing...</h5>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="fgModalLabel">Create New Free Goods Requisition</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="fgForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        {{-- Bagian Atas Form --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold">1. Category</label>
                                <input type="text" class="form-control" value="FREE GOODS" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Requester Department</label>
                                <input type="text" class="form-control" value="{{ $userDepartmentName }}" readonly>
                            </div>
                        </div>

                        <div id="requisition-form-details">
                            <div id="main-requisition-data">
                                <hr>
                                <h5 class="fw-bold text-warning">Requisition Details</h5>

                                {{-- Baris 1: Informasi Customer --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer Name<i class="text-danger">*</i></label>
                                        <select class="form-select select2-styled" id="customer_id" name="customer_id" style="width: 100%;">
                                            <option></option>
                                            @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">
                                                {{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="customer_id_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="customer_address" rows="2" readonly></textarea>
                                    </div>
                                </div>

                                {{-- Baris 2: Detail Nomor, Akun, dan Tanggal --}}
                                <div class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <label for="no_srs" class="form-label">FG No.<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="no_fg" name="no_fg" value="{{ $generatedFg }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="account" name="account" value="5300" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="request_date" class="form-label">Request Date<i class="text-danger">*</i></label>
                                        <input type="date" class="form-control" id="request_date" name="request_date" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cost_center" class="form-label">Cost Center</label>
                                        <input type="text" class="form-control" id="cost_center" name="cost_center" placeholder="e.g: CC1001, CC2002">
                                    </div>
                                </div>

                                {{-- Baris 3: Tujuan dan Potensi --}}
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="objectives" class="form-label">Objectives<i class="text-danger">*</i></label>
                                        <textarea class="form-control" id="objectives" name="objectives" placeholder="e.g: Promotional Items, Internal Use, etc." rows="2"></textarea>
                                        <div class="invalid-feedback" id="objectives_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="estimated_potential" class="form-label">Estimated Potential<i class="text-danger">*</i></label>
                                        <textarea class="form-control" id="estimated_potential" name="estimated_potential" placeholder="e.g.: High, Medium, Low, Others: Specify Here" rows="2"></textarea>
                                        <div class="invalid-feedback" id="estimated_potential_error"></div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="fw-bold text-warning">Product Details</h5>

                                <div class="mb-3" id="product-selection-container-fg">
                                    <label for="product_select_fg" class="form-label fw-bold">2. Select Product Name<i class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="product_select_fg" multiple="multiple" style="width: 100%;"></select>
                                    <button type="button" class="btn btn-success btn-sm mt-2" id="btn-add-items-master">
                                        <i class="ph-bold ph-plus"></i> Add Item to List
                                    </button>
                                </div>

                                <div class="alert alert-danger" id="items_error" style="display: none;"></div>

                                <div class="table-responsive mt-4">
                                    <h6 class="fw-bold">3. Requested Item List</h6>
                                    <table class="table table-bordered w-100">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Unit</th>
                                                <th style="width: 15%;">Qty Required</th>
                                                <th style="width: 15%;">Qty Issued</th>
                                            </tr>
                                        </thead>
                                        <tbody id="requisition-items-tbody-fg">
                                            <tr id="no-items-row">
                                                <td colspan="5" class="text-center">No items have been added yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="submit" id="saveFgBtn">Save</button>
                        <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Free Goods Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">

                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold mb-0"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <small class="view-label">Category</small>
                                    <p class="view-data">FREE GOODS</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Sub Category</small>
                                    <p class="view-data" id="view_sub_category">General Request</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">FG No.</small>
                                    <p class="view-data" id="view_no_srs">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Request Date</small>
                                    <p class="view-data" id="view_request_date">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Customer Name</small>
                                    <p class="view-data" id="view_customer_name">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Address</small>
                                    <p class="view-data" id="view_customer_address">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Account</small>
                                    <p class="view-data" id="view_account">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Cost Center</small>
                                    <p class="view-data" id="view_cost_center">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Objectives</small>
                                    <p class="view-data fst-italic fw-normal" id="view_objectives">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Estimated Potential</small>
                                    <p class="view-data fst-italic fw-normal" id="view_estimated_potential">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card">
                         <div class="card-header">
                            <h5 class="fw-bold mb-0"><i class="ph-bold ph-list me-2"></i>Requested Item List</h5>
                        </div>
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100 mb-1">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Unit</th>
                                            <th class="text-center">Qty Required</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-tbody-fg"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold mb-0"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container-fg">
                                {{-- Tracker steps akan diisi oleh JS --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let nextFgNumber = "{{ $generatedFg }}";

        function successMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 1500,
                showConfirmButton: true
            });
        }

        function errorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'An Error Occurred',
                text: message
            });
        }

        function warningMessage(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: message
            });
        }

        $(document).ready(function () {
            const userDepartmentName = "{{ $userDepartmentName ?? '' }}";

            function initSelect2() {

                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Select Customer</span>';
                    return `<i class='ph ph-user-circle me-2 text-warning'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#fgModal'),
                    placeholder: 'Select Customer',
                    allowClear: true,
                    templateResult: formatCustomer,
                    templateSelection: formatCustomer,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                $('#product_select_fg').select2({
                    dropdownParent: $('#fgModal'),
                    placeholder: 'Select products',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('freegoods.getAllItemMasters') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            const results = data.map(item => ({
                                id: item.id,
                                text: `[${item.item_master_code}] ${item.item_master_name}`
                            }));
                            return {
                                results: results
                            };
                        },
                        cache: true
                    }
                });
            }
            initSelect2();

            // [UPDATE] Inisialisasi Select2 untuk filter
            $('#statusFilter').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity // Sembunyikan kotak pencarian
            });

            const table = $('#fgTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('freegoods.data') }}",
                    data: function (d) {
                        // [UPDATE] Menggunakan ID filter yang baru
                        d.status = $('#statusFilter').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '20px',
                        className: 'text-center'
                    },
                    { data: 'no_srs', name: 'requisitions.no_srs' },
                    { data: 'requester_info', name: 'users.name' },
                    { data: 'customer_name', name: 'customers.name' },
                    { data: 'request_date', name: 'requisitions.created_at' },
                    { data: 'sub_category', name: 'requisitions.sub_category' },
                    { data: 'route_to', name: 'requisitions.route_to' },
                    { data: 'status', name: 'requisitions.status' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                    }
                ]
            });

            // [UPDATE] Event listener untuk filter status yang baru
            $('#statusFilter').on('change', function() {
                table.ajax.reload();
            });

            // [UPDATE] Tombol reset untuk filter yang baru
            $('#resetFilters').on('click', function() {
                $('#statusFilter').val('all').trigger('change');
            });

            let searchInput = $('#fgTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            $('#fgTable_filter input').attr({
                'placeholder': 'Search Free Goods...',
                'class': 'form-control'
            });

            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#items_error').hide().text('');
                $('.select2-selection').css('border-color', '');
            }

            function resetForm() {
                $('#fgForm')[0].reset();
                $('#fgForm').removeAttr('data-mode data-id');
                $('#customer_id, #product_select_fg').val(null).trigger('change');
                $('#requisition-items-tbody-fg').html(
                    '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                );

                clearValidationErrors();
                $('#fgModalLabel').text('Create New Free Goods Requisition');
                $('#no_fg').val(nextFgNumber);
                $('#saveFgBtn').text('Save');
            }

            $('#btn-create-fg').on('click', function () {
                resetForm();
                $('#fgForm').attr('data-mode', 'create').removeAttr('data-id');
                $('#fgModal').modal('show');
            });

            $('#customer_id').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#customer_address').val(address);
            });

            $('#btn-add-items-master').on('click', function () {
                    const selectedMasterIds = $('#product_select_fg').val();
                    if (!selectedMasterIds || selectedMasterIds.length === 0) {
                        warningMessage('Please select a product first.');
                        return;
                    }
                    $.ajax({
                        url: "{{ route('freegoods.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (allMasters) {
                            const tbody = $('#requisition-items-tbody-fg');
                            $('#no-items-row').remove();

                            const selectedMasters = allMasters.filter(m => selectedMasterIds.includes(String(m.id)));

                            selectedMasters.forEach(master => {
                                if ($(`#item-row-master-${master.id}`).length === 0) {
                                    const newRow = `
                                        <tr id="item-row-master-${master.id}" data-master-id="${master.id}">
                                            <td>${master.item_master_code}</td>
                                            <td>${master.item_master_name}</td>
                                            <td>${master.unit}</td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_required]" min="1"></td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_issued]" min="0"></td>
                                        </tr>`;
                                    tbody.append(newRow);
                                }
                            });
                        }
                    });
                });

            $('#product_select_fg').on('select2:unselect', function (e) {
                const unselectedMasterId = e.params.data.id;
                $(`tr[data-master-id="${unselectedMasterId}"][id^="item-row-master-"]`).remove();

                if ($('#requisition-items-tbody-fg tr').length === 0) {
                    $('#requisition-items-tbody-fg').html(
                        '<tr id="no-items-row"><td colspan="5" class="text-center">No items have been added yet.</td></tr>'
                        );
                }
            });

            $('#fgForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;

                const customerName = $('#customer_id option:selected').text().trim();
                const requestDate = $('#request_date').val();
                const itemCount = $('#requisition-items-tbody-fg tr[id^="item-row-"]').length;

                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    html: `Anda akan mengajukan Requisition dengan ringkasan data berikut:
                        <ul class="text-start mt-3" style="list-style: none; padding-left: 0;">
                            <li style="padding: 5px 0;"><strong>Kategori:</strong> FREE GOODS</li>
                            <li style="padding: 5px 0;"><strong>Customer:</strong> ${customerName || '<i>Belum dipilih</i>'}</li>
                            <li style="padding: 5px 0;"><strong>Tgl. Request:</strong> ${requestDate}</li>
                            <li style="padding: 5px 0;"><strong>Jumlah Item:</strong> ${itemCount} item</li>
                        </ul>
                        <hr>
                        <b class="text-danger">Pastikan semua data yang Anda masukkan sudah benar.</b>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Data Sudah Benar!',
                    cancelButtonText: 'Batal, Cek Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const mode = $(form).attr('data-mode');
                        const id = $(form).attr('data-id');

                        function submitForm(formData) {
                        const submitBtn = $('#saveFgBtn');
                        const overlay = $('#fgModal .loading-overlay');
                        overlay.show();
                        submitBtn.prop('disabled', true);

                        let url = (mode === 'edit') ? `/freegoods-form/${id}` : "{{ route('freegoods-form.store') }}";
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
                                if (res.success) {
                                    $('#fgModal').modal('hide');
                                    successMessage(res.message);
                                    table.ajax.reload(null, false);
                                    nextFgNumber = res.next_fg_number;
                                }
                            },

                            error: function (xhr) {
                                if (xhr.status === 422) {
                                    const errors = xhr.responseJSON.errors;
                                    let itemErrorMessages = new Set();
                                    clearValidationErrors();
                                    for (const key in errors) {
                                        const errorMsg = errors[key][0];
                                        if (key.startsWith('items.')) {
                                            itemErrorMessages.add(errorMsg);
                                        } else {
                                            const field = $(`#${key}`);
                                            const errorDiv = $(`#${key}_error`);
                                            field.addClass('is-invalid');
                                            errorDiv.text(errorMsg).show();
                                            if (field.hasClass('select2-styled')) {
                                                field.next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
                                            }
                                        }
                                    }
                                    if (itemErrorMessages.size > 0) {
                                        $('#items_error').show().html(Array.from(itemErrorMessages).join('<br>'));
                                    }
                                } else {
                                    errorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.');
                                }
                        },
                        complete: function() {
                            overlay.hide();
                            submitBtn.prop('disabled', false);
                        }
                    });
                }

                let formData = new FormData(form);
                formData.append('category', 'FREE GOODS');
                formData.append('sub_category', 'General Request');
                formData.append('no_srs', $('#no_fg').val());

                submitForm(formData);
                    }
                });
            });

            function populateForm(data) {
                $('#fgForm').attr('data-mode', 'edit').attr('data-id', data.id);

                $('#customer_id').val(data.customer_id).trigger('change.select2');
                $('#no_fg').val(data.no_srs);
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                $('#request_date').val(data.request_date);
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);

                const productSelectFg = $('#product_select_fg');
                productSelectFg.empty();
                if (data.product_options && data.product_options.length > 0) {
                    data.product_options.forEach(option => {
                        productSelectFg.append(new Option(option.text, option.id, false, false));
                    });
                }
                if (data.selected_master_ids && data.selected_master_ids.length > 0) {
                    productSelectFg.val(data.selected_master_ids);
                }
                productSelectFg.trigger('change.select2');

                const itemTbody = $('#requisition-items-tbody-fg');
                itemTbody.empty();
                const colspan = 5;

                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';

                        if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }

                        const newRow = `
                            <tr id="item-row-master-${item.item_master_id}" data-master-id="${item.item_master_id}">
                                <td>${itemCode}</td>
                                <td>${itemName}</td>
                                <td>${unit}</td>
                                <td><input type="number" class="form-control" name="items[${item.item_master_id}][quantity_required]" value="${item.quantity_required || ''}" min="1"></td>
                                <td><input type="number" class="form-control" name="items[${item.item_master_id}][quantity_issued]" value="${item.quantity_issued || ''}" min="0"></td>
                            </tr>`;
                        itemTbody.append(newRow);
                    });
                } else {
                    itemTbody.html(`<tr id="no-items-row"><td colspan="${colspan}" class="text-center">No items found.</td></tr>`);
                }

                $('#saveFgBtn').text('Save Changes').prop('disabled', false);
            }

            function populateViewForm(data) {
                $('#view_sub_category').text(data.sub_category || 'General Request');
                $('#view_customer_name').text(data.customer ? data.customer.name : '-');
                $('#view_customer_address').text(data.customer ? data.customer.address : '-');
                $('#view_no_srs').text(data.no_srs || '-');
                $('#view_account').text(data.account || '-');
                $('#view_request_date').text(new Date(data.request_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) || '-');
                $('#view_cost_center').text(data.cost_center || '-');
                $('#view_objectives').text(data.objectives || '-');
                $('#view_estimated_potential').text(data.estimated_potential || '-');

                const viewItemTbody = $('#view-items-tbody-fg');
                viewItemTbody.empty();
                const colspan = 5;

                if (data.requisition_items && data.requisition_items.length > 0) {
                    data.requisition_items.forEach(item => {
                        let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';

                        if (item.item_master) {
                            itemCode = item.item_master.item_master_code;
                            itemName = item.item_master.item_master_name;
                            unit = item.item_master.unit;
                        }

                        const newRow = `
                        <tr>
                            <td>${itemCode}</td>
                            <td>${itemName}</td>
                            <td>${unit}</td>
                            <td class="text-center">${item.quantity_required}</td>
                            <td class="text-center">${item.quantity_issued || '-'}</td>
                        </tr>`;
                        viewItemTbody.append(newRow);
                    });
                } else {
                    viewItemTbody.html(`<tr><td colspan="${colspan}" class="text-center">No items have been added.</td></tr>`);
                }

                const status = data.status;
                let badgeClass = 'bg-secondary';
                if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-warning';
                else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                else if (['Rejected', 'Recalled'].includes(status)) badgeClass = 'bg-danger';
                else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info';

                $('#view_status_badge').html(`<span class="badge status-badge-lg fs-6 rounded-pill ${badgeClass}">${status}</span>`);

                const trackerContainer = $('#approval-tracker-container-fg');
                trackerContainer.empty();

                let steps = [
                    { id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' }
                ];

                const approvalLogs = data.approval_logs ? data.approval_logs.filter(log => log.level <= 100) : [];

                let isSnM = data.requester.department?.name === 'SnM' || data.requester.department?.code === '5300';
                let approvalSteps = [
                    { label: isSnM ? 'SnM Manager' : 'HCD Dept. Head', icon: 'ph-user-plus' },
                    { label: 'Business Controller', icon: 'ph-briefcase' }
                ];

                approvalSteps.forEach((step, index) => {
                    const log = approvalLogs.find(l => l.level === (index + 1));
                    const approverName = log && log.approver ? log.approver.name : step.label;

                    steps.push({
                        id: 'approver_' + (index + 1),
                        label: `${step.label}<br><small class="text-muted fw-normal">${log ? approverName : '...'}</small>`,
                        icon: 'ph-user-check'
                    });
                });

                if (status !== 'Rejected' && status !== 'Recalled') {
                    steps.push({ id: 'outward', label: 'Outward WH Supervisor', icon: 'ph-package' });
                    steps.push({ id: 'completed', label: 'Completed', icon: 'ph-check-circle' });
                }

                let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress-fg"></div></div>';
                steps.forEach(step => {
                    trackerHtml += `
                        <div class="tracker-step" data-step-id="${step.id}">
                            <div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div>
                            <div class="tracker-label">${step.label}</div>
                            <div class="tracker-details"></div>
                        </div>`;
                });
                trackerContainer.html(trackerHtml);

                let lastCompletedIndex = -1;
                let isRejected = ['Rejected', 'Recalled'].includes(status);

                if (data.requester && data.created_at) {
                    const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                    submittedStep.addClass('completed');
                    const requesterName = data.requester.name;
                    submittedStep.find('.tracker-details').html(
                        `<div class="tracker-user text-warning">${requesterName}</div>`
                    );
                    lastCompletedIndex = 0;
                }

                approvalLogs.forEach(log => {
                    const stepId = 'approver_' + log.level;
                    const stepIndex = steps.findIndex(s => s.id === stepId);
                    if (stepIndex > -1 && log.status === 'Approved') {
                        const stepElement = $(`.tracker-step[data-step-id="${stepId}"]`);
                        stepElement.addClass('completed');
                        stepElement.find('.tracker-details').html(
                            `<div class="tracker-user text-warning">${log.approver.name}</div>`
                        );
                        lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                    }
                });

                let currentTrackingPosition = data.trackings ? data.trackings.find(t => !t.last_updated)?.current_position : null;

                if (currentTrackingPosition && currentTrackingPosition.includes('Outward WH Supervisor')) {
                    const stepIndex = steps.findIndex(s => s.id === 'outward');
                    if(stepIndex > -1) {
                        $(`.tracker-step[data-step-id="outward"]`).addClass('active');
                        lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex - 1);
                    }
                } else if (status === 'Completed' && data.trackings && data.trackings.length > 0) {
                     const stepIndex = steps.findIndex(s => s.id === 'completed');
                     if(stepIndex > -1) {
                         $(`.tracker-step`).addClass('completed');
                         lastCompletedIndex = stepIndex;
                     }
                }

                if (lastCompletedIndex >= 0 && !isRejected) {
                    let totalSteps = steps.length - 1;
                    let progressPercentage = (lastCompletedIndex / totalSteps) * 100;
                    if (totalSteps === 0) progressPercentage = 100;

                    $('#tracker-progress-fg').css('width', progressPercentage + '%');
                }

                if (status === 'Completed') {
                    $('.tracker-step').addClass('completed');
                } else if (isRejected) {
                    const nextStepIndex = lastCompletedIndex + 1;
                    if (nextStepIndex < steps.length) {
                        $(`.tracker-step`).eq(nextStepIndex).addClass('rejected');
                    }
                } else if (status === 'Pending' || status === 'In Progress') {
                    const nextStepIndex = lastCompletedIndex + 1;
                    if (nextStepIndex < steps.length) {
                        $(`.tracker-step`).eq(nextStepIndex).addClass('active');
                    }
                }
            }

            $(document).on('click', '.btn-view-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/freegoods-form/${id}`,
                    type: 'GET',
                    success: function(response) {
                        populateViewForm(response);
                        $('#viewModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch requisition details.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            // [UPDATE] Event listener untuk tombol recall disesuaikan seperti Sample
            $(document).on('click', '.btn-recall-requisition', function() {
                const requisitionId = $(this).data('id');
                const row = $(this).closest('tr');
                const rowData = table.row(row).data();
                const fgNumber = rowData.no_srs; // Mengambil nomor FG dari data baris
                const button = $(this);
                const originalHtml = button.html();

                // Langkah 1: Meminta input alasan recall
                Swal.fire({
                    title: `Recall Requisition ${fgNumber}`,
                    width: '600px',
                    html: `
                        <p class="text-danger fw-bold">Tindakan ini akan membatalkan requisition dan tidak dapat di-undo.</p>
                        <textarea id="recallNotes" class="swal2-textarea" placeholder="Mohon berikan alasan untuk recall (wajib)..." style="width: 400px; height: 150px;"></textarea>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    focusConfirm: false,
                    preConfirm: () => {
                        const notes = Swal.getPopup().querySelector('#recallNotes').value;
                        if (!notes.trim()) {
                            Swal.showValidationMessage('Alasan recall wajib diisi.');
                            return false;
                        }
                        return notes;
                    }
                }).then((result) => {
                    // Lanjutkan jika langkah 1 di-konfirmasi
                    if (result.isConfirmed && result.value) {
                        const notes = result.value;

                        // Langkah 2: Konfirmasi alasan
                        Swal.fire({
                            title: 'Konfirmasi Alasan Recall',
                            html: `
                                <p>Pastikan alasan yang Anda masukkan sudah benar:</p>
                                <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; text-align: left; margin-top: 10px;">
                                    <i>"${notes}"</i>
                                </div>
                            `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Data Benar & Recall!',
                            cancelButtonText: 'Batal'
                        }).then((confirmResult) => {
                            // Lanjutkan jika langkah 2 di-konfirmasi
                            if (confirmResult.isConfirmed) {
                                $.ajax({
                                    url: `/freegoods-form/${requisitionId}/recall`,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        notes: notes // Kirim notes ke controller
                                    },
                                    beforeSend: function() {
                                        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire('Recalled!', response.message, 'success');
                                            table.ajax.reload(null, false);
                                        }
                                    },
                                    error: function (xhr) {
                                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                                    },
                                    complete: function() {
                                        button.html(originalHtml).prop('disabled', false);
                                    }
                                });
                            }
                        });
                    }
                });
            });


            // Event listener untuk tombol duplicate (tidak diubah)
            $(document).on('click', '.btn-duplicate-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();
                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/freegoods-form/${id}/edit`,
                    type: 'GET',
                    success: function(data) {
                        resetForm();
                        populateForm(data);

                        $.ajax({
                            url: "{{ route('freegoods.get-next-number') }}",
                            type: 'GET',
                            success: function(res) {
                                $('#no_fg').val(res.next_fg_number);
                                nextFgNumber = res.next_fg_number;
                            },
                            complete: function() {
                                $('#fgForm').attr('data-mode', 'create').removeAttr('data-id');
                                $('#fgModalLabel').text('Duplicate Free Goods Requisition');
                                $('#saveFgBtn').text('Save as New');

                                button.html(originalIcon).prop('disabled', false);
                                $('#fgModal').modal('show');
                            }
                        });
                    },
                    error: function() {
                        errorMessage('Failed to fetch data for duplication.');
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $('#fgModal').on('hidden.bs.modal', function () {
                resetForm();
                $('#fgForm').removeAttr('data-mode data-id');
            });

        });

    </script>
    @endpush
</x-app-layout>
