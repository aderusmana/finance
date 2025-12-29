<x-app-layout>
    @section('title')
        Bank Garansi List
    @endsection

    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Bank Garansi Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-bank f-s-16"></i> Financial</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Bank Garansi</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Filters --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent_to_customer">Sent to Cust.</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="expired">Expired</option>
                    </select>
                    <select id="typeFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Type</option>
                        <option value="new">New</option>
                        <option value="existing">Existing</option>
                        <option value="extension">Extension</option>
                    </select>
                    <button id="resetFilters" class="btn btn-sm btn-secondary border" title="Reset Filters"><i class="ph-bold ph-arrow-counter-clockwise"></i></button>
                </div>
                {{-- Create Button --}}
                <div class="ms-auto d-flex">
                    <button class="btn btn-primary" type="button" id="btn-create-bg">
                        <i class="ph-bold ph-plus"></i> <span>New Bank Garansi</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="table-title mb-1"><i class="ph-duotone ph-file-lock me-2"></i> Bank Garansi Data</h4>
                        <small class="text-white opacity-75 f-s-12">Manage BG records, renewals, and monitoring.</small>
                    </div>
                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;"><i class="ph-fill ph-files text-white f-s-18"></i></div>
                            <div class="d-flex flex-column line-height-sm"><span class="f-s-11 opacity-75 text-uppercase fw-bold">Total</span><span class="f-s-14 fw-bold">{{ $stats['total'] }}</span></div>
                        </div>
                    </div>
                </div>
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

                    <div class="modal-body">
                        {{-- 1. Global Customer --}}
                        <div class="card mb-3 border-primary shadow-sm">
                            <div class="card-body p-3 bg-light-primary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold">Select Customer <span class="text-danger">*</span></label>
                                        <select class="form-select select2-modal" id="customer_id" name="customer_id" required style="width: 100%;">
                                            <option></option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Items Container (Looping BG) --}}
                        <div id="items-container"></div>

                        {{-- Tombol Add Item (Hanya muncul saat Create) --}}
                        <button type="button" class="btn btn-outline-primary w-100 border-dashed py-2" id="btn-add-item">
                            <i class="ph-bold ph-plus-circle me-1"></i> Add Another BG for this Customer
                        </button>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-bg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL (Modern & Professional) --}}
    <div class="modal fade" id="bgDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 12px;">
                
                {{-- Modal Header with Gradient --}}
                <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="ph-bold ph-file-text fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Bank Garansi Detail</h5>
                            <small class="text-white-50" id="detail-bg-number">Loading...</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    
                    {{-- Status Banner --}}
                    <div class="px-4 py-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                        <span class="text-muted fw-bold text-uppercase small letter-spacing-1">Current Status</span>
                        <span class="badge rounded-pill px-3 py-2 fs-6" id="detail-status">Loading...</span>
                    </div>

                    <div class="row g-0">
                        {{-- LEFT COLUMN: Main Info --}}
                        <div class="col-md-7 p-4 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="ph-duotone ph-buildings me-2"></i>Customer Information</h6>
                            
                            <div class="mb-4">
                                <label class="text-muted small text-uppercase fw-bold">Customer Name</label>
                                <div class="fs-6 fw-bold text-dark" id="detail-customer">...</div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold">Type</label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <i class="ph-fill ph-tag text-info"></i>
                                        <span class="fw-bold text-dark" id="detail-type">...</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted small text-uppercase fw-bold">Nominal</label>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <i class="ph-fill ph-coins text-warning"></i>
                                        <span class="fw-bold text-dark" id="detail-nominal">...</span>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-primary mb-3 mt-4"><i class="ph-duotone ph-bank me-2"></i>Bank Details</h6>
                            <div class="p-3 rounded bg-light border border-dashed" id="detail-bank-container">
                                {{-- Bank details will be injected here --}}
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Dates & Meta --}}
                        <div class="col-md-5 p-4 bg-light-subtle">
                            <h6 class="fw-bold text-primary mb-3"><i class="ph-duotone ph-calendar me-2"></i>Timeline</h6>
                            
                            <div class="timeline-simple">
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold">Issued Date</label>
                                    <div class="d-flex align-items-center gap-2 text-dark">
                                        <i class="ph-bold ph-calendar-plus text-success"></i>
                                        <span id="detail-issued">...</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold">Expired Date</label>
                                    <div class="d-flex align-items-center gap-2 text-dark">
                                        <i class="ph-bold ph-calendar-x text-danger"></i>
                                        <span id="detail-expired">...</span>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-dashed my-4">

                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Created By</label>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-1">
                                        <i class="ph-fill ph-user text-secondary"></i>
                                    </div>
                                    <span class="small fw-bold" id="detail-creator">...</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="text-muted small fw-bold">Last Updated</label>
                                <div class="small text-dark" id="detail-updated">...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-2">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // --- GLOBAL HELPER: Format Rupiah ---
            function formatRupiah(element) {
                let value = element.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                element.value = rupiah;
            }

            $(document).ready(function() {
                $('.select2').select2({ theme: 'bootstrap-5' });
                $('.select2-modal').select2({ dropdownParent: $('#bgModal'), theme: 'bootstrap-5', placeholder: 'Select Option' });

                const table = $('#sampleTable').DataTable({
                    processing: true, serverSide: true,
                    ajax: {
                        url: "{{ route('bg-list.index') }}",
                        data: function(d) { d.status = $('#statusFilter').val(); d.bg_type = $('#typeFilter').val(); }
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'bg_number', className: 'fw-bold' },
                        { data: 'customer_name', className: 'text-wrap text-start', width: '25%' },
                        { data: 'bg_type', className: 'text-center', render: function(d) { return `<span class="badge bg-${d==='new'?'info':(d==='extension'?'warning':'secondary')} text-uppercase">${d}</span>`; } },
                        { data: 'bg_nominal', className: 'text-end fw-bold' },
                        { data: 'issued_date' }, { data: 'exp_date' },
                        { data: 'status', className: 'text-center', render: function(d) { return `<span class="badge bg-${d==='approved'?'success':(d==='submitted'?'primary':(d==='expired'?'danger':'secondary'))} text-uppercase">${d.replace('_', ' ')}</span>`; } },
                        { data: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    order: [[1, 'asc']]
                });

                $('#statusFilter, #typeFilter').on('change', function() { table.ajax.reload(); });
                $('#resetFilters').on('click', function() { $('#statusFilter, #typeFilter').val('all').trigger('change'); });

                let itemIndex = 0;

                function addBgItem(data = null) {
                    const index = itemIndex++;

                    const bgNumber = (data && data.bg_number) ? data.bg_number : '';
                    const bgType = (data && data.bg_type) ? data.bg_type : 'new';

                    let nominalVal = '';
                    if (data && data.bg_nominal && parseFloat(data.bg_nominal) > 0) {
                        nominalVal = new Intl.NumberFormat('id-ID').format(data.bg_nominal);
                    }

                    const today = new Date().toISOString().split('T')[0];
                    const issued = (data && data.issued_date) ? data.issued_date.substring(0,10) : today;
                    const exp = (data && data.exp_date) ? data.exp_date.substring(0,10) : today;

                    let detail = (data && data.details && data.details.length > 0) ? data.details[0] : {};
                    const bankName = detail.bank_name || '';
                    const branch = detail.branch_name || '';
                    const address = detail.bank_address || '';
                    const pic = detail.contact_person || '';

                    const html = `
                        <div class="card mb-3 border bg-item-row" data-index="${index}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0 fw-bold text-dark fs-6">Bank Garansi Information #${index + 1}</h6>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" ${index === 0 && !data ? '' : ''}>
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">BG Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="items[${index}][bg_number]" value="${bgNumber}" required placeholder="e.g. BG/2025/001">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Type <span class="text-danger">*</span></label>
                                        <select class="form-select" name="items[${index}][bg_type]">
                                            <option value="new" ${bgType === 'new' ? 'selected' : ''}>New</option>
                                            <option value="existing" ${bgType === 'existing' ? 'selected' : ''}>Existing</option>
                                            <option value="extension" ${bgType === 'extension' ? 'selected' : ''}>Extension</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Nominal (IDR) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text" class="form-control fw-bold rupiah-input"
                                                   name="items[${index}][nominal]"
                                                   value="${nominalVal}"
                                                   required placeholder="0"
                                                   onkeyup="formatRupiah(this)">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Issued Date</label>
                                        <input type="date" class="form-control bg-light" name="items[${index}][issued_date]" value="${issued}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Expiry Date</label>
                                        <input type="date" class="form-control bg-light" name="items[${index}][exp_date]" value="${exp}" readonly>
                                    </div>

                                    <div class="col-12"><hr class="my-1 border-dashed"></div>

                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="items[${index}][bank_name]" value="${bankName}" required placeholder="e.g. Bank Mandiri">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Branch</label>
                                        <input type="text" class="form-control" name="items[${index}][branch_name]" value="${branch}" placeholder="Branch Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">PIC Name</label>
                                        <input type="text" class="form-control" name="items[${index}][contact_person]" value="${pic}" placeholder="PIC Name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Bank Address</label>
                                        <input type="text" class="form-control" name="items[${index}][bank_address]" value="${address}" placeholder="Address">
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    $('#items-container').append(html);
                }

                $('#btn-add-item').on('click', function() { addBgItem(); });

                $(document).on('click', '.btn-remove-item', function() {
                    $(this).closest('.bg-item-row').remove();
                });

                $('#btn-create-bg').on('click', function() {
                    $('#bgForm')[0].reset();
                    $('#bgId').val(''); $('#formMethod').val('POST');

                    itemIndex = 0;
                    $('#items-container').empty();

                    $('.select2-modal').val(null).trigger('change');
                    $('#btn-add-item').show();
                    $('#bgModalLabel').text('Create New Bank Garansi');
                    addBgItem();
                    $('#bgModal').modal('show');
                });

                // --- EDIT MODE ---
                $(document).on('click', '.btn-edit-bg', function() {
                    const id = $(this).data('id');
                    const url = "{{ route('bg-list.show', ':id') }}".replace(':id', id);
                    Swal.fire({ title: 'Loading...', didOpen: () => Swal.showLoading() });

                    $.get(url, function(data) {
                        Swal.close();
                        $('#bgForm')[0].reset();

                        itemIndex = 0; // Reset index
                        $('#items-container').empty();

                        $('#bgId').val(data.id);
                        $('#customer_id').val(data.customer_id).trigger('change');
                        $('#formMethod').val('PUT');

                        $('#btn-add-item').hide();
                        $('#bgModalLabel').text('Edit Bank Garansi');

                        addBgItem(data);

                        $('#bgModal').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    });
                });

                // --- SUBMIT ---
                $('#bgForm').on('submit', function(e) {
                    e.preventDefault();
                    const id = $('#bgId').val();
                    let url = "{{ route('bg-list.store') }}";
                    if(id) {
                        url = "{{ route('bg-list.update', ':id') }}".replace(':id', id);
                        $('#formMethod').val('PUT');
                    }

                    const formData = new FormData(this);

                    // Bersihkan format Rupiah sebelum kirim
                    const keys = Array.from(formData.keys());
                    for (const key of keys) {
                        if (key.includes('[nominal]')) {
                            let rawVal = formData.get(key);
                            if(rawVal) {
                                let cleanVal = rawVal.replace(/\./g, '');
                                formData.set(key, cleanVal);
                            }
                        }
                    }

                    Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: url, method: 'POST', data: formData, processData: false, contentType: false,
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#bgModal').modal('hide');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Error occurred';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });

                // Delete Handler (Code sama seperti sebelumnya)
                $(document).on('click', '.btn-delete-bg', function() {
                    const id = $(this).data('id');
                    const url = "{{ route('bg-list.destroy', ':id') }}".replace(':id', id);
                    Swal.fire({
                        title: 'Are you sure?', text: "Deleted data cannot be recovered.", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url, method: 'DELETE', data: { _token: "{{ csrf_token() }}" },
                                success: function(res) { Swal.fire('Deleted!', res.message, 'success'); table.ajax.reload(); },
                                error: function() { Swal.fire('Error', 'Failed to delete', 'error'); }
                            });
                        }
                    });
                });

                // --- SHOW DETAIL MODAL LOGIC ---
                $(document).on('click', '.btn-show-bg', function() {
                    const id = $(this).data('id');
                    const url = "{{ route('bg-list.show', ':id') }}".replace(':id', id);
                    
                    // Tampilkan loading saat fetch data
                    Swal.fire({ 
                        title: 'Loading Data...', 
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading() 
                    });

                    $.get(url, function(data) {
                        Swal.close(); // Tutup loading

                        // 1. Populate Header & Status
                        $('#detail-bg-number').text(data.bg_number || '-');
                        
                        // Status Badge Logic
                        const statusMap = {
                            'approved': 'bg-success',
                            'submitted': 'bg-primary',
                            'draft': 'bg-secondary',
                            'expired': 'bg-danger',
                            'sent_to_customer': 'bg-info'
                        };
                        const statusClass = statusMap[data.status] || 'bg-secondary';
                        $('#detail-status')
                            .removeClass()
                            .addClass(`badge rounded-pill px-3 py-2 fs-6 ${statusClass}`)
                            .text((data.status || 'N/A').replace(/_/g, ' ').toUpperCase());

                        // 2. Populate Main Info
                        $('#detail-customer').text(data.customer ? data.customer.name : '-');
                        $('#detail-type').text((data.bg_type || '-').toUpperCase());
                        
                        // Format Rupiah
                        const nominal = data.bg_nominal ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(data.bg_nominal) : 'Rp 0';
                        $('#detail-nominal').text(nominal);

                        // 3. Populate Dates
                        const formatDate = (dateStr) => {
                            if(!dateStr) return '-';
                            const date = new Date(dateStr);
                            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                        };
                        $('#detail-issued').text(formatDate(data.issued_date));
                        $('#detail-expired').text(formatDate(data.exp_date));

                        // 4. Populate Meta
                        $('#detail-creator').text(data.creator ? data.creator.name : 'System');
                        $('#detail-updated').text(formatDate(data.updated_at));

                        // 5. Populate Bank Details (Looping)
                        let bankHtml = '';
                        if (data.details && data.details.length > 0) {
                            data.details.forEach(detail => {
                                bankHtml += `
                                    <div class="mb-2 last-mb-0">
                                        <div class="fw-bold text-dark">${detail.bank_name || '-'}</div>
                                        <div class="small text-muted">
                                            <i class="ph-bold ph-map-pin me-1"></i> ${detail.branch_name || 'Main Branch'}
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <i class="ph-bold ph-user me-1"></i> PIC: ${detail.contact_person || '-'}
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            bankHtml = '<span class="text-muted f-s-12">No bank details available.</span>';
                        }
                        $('#detail-bank-container').html(bankHtml);

                        // Show Modal
                        $('#bgDetailModal').modal('show');

                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch details.', 'error');
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
