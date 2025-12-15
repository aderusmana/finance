<x-app-layout>
    @section('title')
        Bank Garansi List
    @endsection

    {{-- Gunakan style tabel yang sama dengan Customer --}}
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Bank Garansi Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-bank f-s-16"></i> Financial
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Bank Garansi</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Filters --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>

                    {{-- Filter 1: Status --}}
                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent_to_customer">Sent to Cust.</option>
                        <option value="submitted">Submitted</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="approved">Approved</option>
                        <option value="expired">Expired</option>
                    </select>

                    {{-- Filter 2: Type --}}
                    <select id="typeFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Type</option>
                        <option value="new">New</option>
                        <option value="existing">Existing</option>
                        <option value="extension">Extension</option>
                    </select>

                    {{-- Reset Button --}}
                    <button id="resetFilters" class="btn btn-sm btn-secondary border" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                {{-- Create Button --}}
                <div class="ms-auto d-flex">
                    <button class="btn btn-primary" type="button" id="btn-create-bg">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Bank Garansi</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">

                    {{-- Title --}}
                    <div>
                        <h4 class="table-title mb-1">
                            <i class="ph-duotone ph-file-lock me-2"></i> Bank Garansi Data
                        </h4>
                        <small class="text-white opacity-75 f-s-12">
                            Manage BG records, renewals, and monitoring.
                        </small>
                    </div>

                    {{-- Stats --}}
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">

                        {{-- Total --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-files text-white f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Total</span>
                                <span class="f-s-14 fw-bold">{{ $stats['total'] }}</span>
                            </div>
                        </div>

                        {{-- Expiring Soon --}}
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Expiring in < 30 days">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-warning text-warning f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Expiring</span>
                                <span class="f-s-14 fw-bold">{{ $stats['expiring'] }}</span>
                            </div>
                        </div>

                        <div class="vr opacity-100 bg-white" style="height: 40px;"></div>

                        {{-- Active --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-check-circle text-success f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Active</span>
                                <span class="f-s-14 fw-bold">{{ $stats['active'] }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>BG Number</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Nominal (IDR)</th>
                                <th>Issued Date</th>
                                <th>Exp Date</th>
                                <th class="text-center">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="bgModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="bgModalLabel">Create Bank Garansi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bgForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="bgId">

                    {{-- Hidden input untuk menyimpan ID detail yang dihapus saat edit --}}
                    <input type="hidden" name="deleted_detail_ids" id="deleted_detail_ids">

                    <div class="modal-body">

                        {{-- 1. General Info --}}
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-light-primary">
                                <h6 class="mb-0 fw-bold text-primary"><i class="ph-bold ph-info"></i> General Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Customer <span class="text-danger">*</span></label>
                                        <select class="form-select select2-modal" id="customer_id" name="customer_id" required style="width: 100%;">
                                            <option></option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">BG Type <span class="text-danger">*</span></label>
                                        <select class="form-select select2-modal" id="bg_type" name="bg_type" required style="width: 100%;">
                                            <option value="new">New</option>
                                            <option value="existing">Existing</option>
                                            <option value="extension">Extension</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select select2-modal" id="status" name="status" required style="width: 100%;">
                                            <option value="draft">Draft</option>
                                            <option value="sent_to_customer">Sent to Customer</option>
                                            <option value="submitted">Submitted</option>
                                            <option value="reviewed">Reviewed</option>
                                            <option value="approved">Approved</option>
                                            <option value="expired">Expired</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">BG Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="bg_number" id="bg_number" required placeholder="e.g. BG/2025/XII/001">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Total Nominal (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="bg_nominal" id="bg_nominal" required placeholder="0">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Issued Date</label>
                                        <input type="date" class="form-control" name="issued_date" id="issued_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" name="exp_date" id="exp_date">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Bank Details (Dynamic Rows) --}}
                        <div class="card border-success">
                            <div class="card-header bg-light-success d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-success"><i class="ph-bold ph-bank"></i> Bank Details</h6>
                                <button type="button" class="btn btn-sm btn-success" id="btnAddDetail">
                                    <i class="ph-bold ph-plus"></i> Add Bank
                                </button>
                            </div>
                            <div class="card-body p-2 bg-light">
                                <div id="details-container">
                                    {{-- Dynamic rows will be appended here --}}
                                </div>
                                <div class="text-center text-muted f-s-12 mt-2" id="no-details-msg">
                                    No bank details added. Click "Add Bank" to insert issuing bank info.
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-bg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // 1. Initialize Plugins
                $('.select2').select2({ theme: 'bootstrap-5' });
                $('.select2-modal').select2({ dropdownParent: $('#bgModal'), theme: 'bootstrap-5', placeholder: 'Select Option' });

                // 2. DataTables
                const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('bg-list.index') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                            d.bg_type = $('#typeFilter').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'bg_number', name: 'bg_number', className: 'fw-bold' },
                        {
                            data: 'customer_name',
                            name: 'customer.name',
                            className: 'text-wrap text-start',
                            width: '25%',
                        },
                        {
                            data: 'bg_type', name: 'bg_type', className: 'text-center',
                            render: function(data) {
                                let badge = 'secondary';
                                if(data === 'new') badge = 'info';
                                if(data === 'extension') badge = 'warning';
                                return `<span class="badge bg-${badge} text-uppercase">${data}</span>`;
                            }
                        },
                        { data: 'bg_nominal', name: 'bg_nominal', className: 'text-end fw-bold' },
                        { data: 'issued_date', name: 'issued_date' },
                        { data: 'exp_date', name: 'exp_date' },
                        {
                            data: 'status', name: 'status', className: 'text-center',
                            render: function(data) {
                                let color = 'secondary';
                                if(data === 'approved') color = 'success';
                                if(data === 'submitted') color = 'primary';
                                if(data === 'expired') color = 'danger';
                                return `<span class="badge bg-${color} text-uppercase">${data.replace('_', ' ')}</span>`;
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    order: [[1, 'asc']]
                });

                // Filter Events
                $('#statusFilter, #typeFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() {
                    $('#statusFilter, #typeFilter').val('all').trigger('change');
                });

                // 3. Dynamic Rows Logic (Bg Details)
                let detailIndex = 0;

                function addDetailRow(data = null) {
                    $('#no-details-msg').hide();
                    const index = detailIndex++;

                    // Default values
                    const id = data ? data.id : '';
                    const bankName = data ? data.bank_name : '';
                    const branch = data ? data.branch_name : '';
                    const address = data ? data.bank_address : '';
                    const pic = data ? data.contact_person : '';
                    const nominal = data ? parseFloat(data.nominal) : 0;

                    const html = `
                        <div class="card mb-2 border shadow-sm detail-row" data-index="${index}">
                            <input type="hidden" name="details[${index}][id]" value="${id}">
                            <div class="card-body p-2">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" name="details[${index}][bank_name]" value="${bankName}" placeholder="Bank Name (e.g. BCA)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" name="details[${index}][branch_name]" value="${branch}" placeholder="Branch Name">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" step="0.01" class="form-control" name="details[${index}][nominal]" value="${nominal}" placeholder="Nominal Split" required>
                                            <button type="button" class="btn btn-outline-danger btn-remove-detail"><i class="ph-bold ph-trash"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" name="details[${index}][contact_person]" value="${pic}" placeholder="Contact Person (PIC)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" name="details[${index}][bank_address]" value="${address}" placeholder="Bank Address">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#details-container').append(html);
                }

                $('#btnAddDetail').on('click', function() { addDetailRow(); });

                $(document).on('click', '.btn-remove-detail', function() {
                    const row = $(this).closest('.detail-row');
                    const id = row.find('input[name*="[id]"]').val();

                    // Jika ID ada (data existing), masukkan ke array deleted_ids
                    if(id) {
                        let currentDeleted = $('#deleted_detail_ids').val();
                        let arr = currentDeleted ? currentDeleted.split(',') : [];
                        arr.push(id);
                        $('#deleted_detail_ids').val(arr.join(','));
                    }

                    row.remove();
                    if($('#details-container').children().length === 0) {
                        $('#no-details-msg').show();
                    }
                });

                // 4. Create Handler
                $('#btn-create-bg').on('click', function() {
                    $('#bgForm')[0].reset();
                    $('#bgId').val('');
                    $('#formMethod').val('POST');
                    $('#deleted_detail_ids').val('');
                    $('#details-container').empty();
                    $('#no-details-msg').show();
                    $('.select2-modal').val(null).trigger('change');

                    $('#bgModalLabel').text('Create New Bank Garansi');
                    $('#bgModal').modal('show');
                });

                // 5. Submit Handler (Create & Update)
                $('#bgForm').on('submit', function(e) {
                    e.preventDefault();

                    const id = $('#bgId').val();
                    let url = "{{ route('bg-list.store') }}";
                    if(id) {
                        url = "{{ route('bg-list.update', ':id') }}".replace(':id', id);
                        $('#formMethod').val('PUT'); // Laravel Method Spoofing
                    }

                    const formData = new FormData(this);

                    Swal.fire({
                        title: 'Saving Data...',
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: url,
                        method: 'POST', // Gunakan POST dengan _method untuk support file/put
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#bgModal').modal('hide');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = 'Error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });

                // 6. Edit Handler
                $(document).on('click', '.btn-edit-bg', function() {
                    const id = $(this).data('id');
                    const url = "{{ route('bg-list.show', ':id') }}".replace(':id', id);

                    Swal.fire({ title: 'Loading...', didOpen: () => Swal.showLoading() });

                    $.get(url, function(data) {
                        Swal.close();
                        $('#bgForm')[0].reset();
                        $('#deleted_detail_ids').val('');
                        $('#details-container').empty();

                        // Populate Basic Info
                        $('#bgId').val(data.id);
                        $('#customer_id').val(data.customer_id).trigger('change');
                        $('#bg_type').val(data.bg_type).trigger('change');
                        $('#status').val(data.status).trigger('change');
                        $('#bg_number').val(data.bg_number);
                        $('#bg_nominal').val(data.bg_nominal);

                        // Handle Date Format (YYYY-MM-DD)
                        if(data.issued_date) $('#issued_date').val(data.issued_date.substring(0,10));
                        if(data.exp_date) $('#exp_date').val(data.exp_date.substring(0,10));

                        // Populate Details
                        if(data.details && data.details.length > 0) {
                            data.details.forEach(detail => addDetailRow(detail));
                        } else {
                            $('#no-details-msg').show();
                        }

                        $('#bgModalLabel').text('Edit Bank Garansi');
                        $('#bgModal').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    });
                });

                // 7. Delete Handler
                $(document).on('click', '.btn-delete-bg', function() {
                    const id = $(this).data('id');
                    const url = "{{ route('bg-list.destroy', ':id') }}".replace(':id', id);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This BG and its details will be deleted permanently.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'DELETE',
                                data: { _token: "{{ csrf_token() }}" },
                                success: function(res) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    table.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Failed to delete', 'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
