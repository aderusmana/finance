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
            // Init Plugins
            $('.select2').select2({ theme: 'bootstrap-5' });
            $('.select2-modal').select2({ dropdownParent: $('#bgModal'), theme: 'bootstrap-5', placeholder: 'Select Option' });

            // Init Datatable
            const table = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: {
                    url: "{{ route('bg-list.index') }}",
                    data: function(d) {
                        d.status = $('#statusFilter').val();
                        d.bg_type = $('#typeFilter').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'bg_number', className: 'fw-bold' },
                    { data: 'customer_name', className: 'text-wrap text-start', width: '25%' },
                    { data: 'bg_type', className: 'text-center', render: function(d) {
                        let color = d === 'new' ? 'info' : (d === 'extension' ? 'warning' : 'secondary');
                        return `<span class="badge bg-${color} text-uppercase">${d}</span>`;
                    }},
                    { data: 'bg_nominal', className: 'text-end fw-bold' },
                    { data: 'issued_date' },
                    { data: 'exp_date' },
                    { data: 'status', className: 'text-center', render: function(d) {
                        let color = d === 'approved' ? 'success' : (d === 'submitted' ? 'primary' : (d === 'expired' ? 'danger' : 'secondary'));
                        return `<span class="badge bg-${color} status-badge-lg              ">${d.replace('_', ' ')}</span>`;
                    }},
                    { data: 'action', orderable: false, searchable: false, className: 'text-center position-static' }
                ],
                order: [[1, 'asc']]
            });

            $('#statusFilter, #typeFilter').on('change', function() { table.ajax.reload(); });
            $('#resetFilters').on('click', function() { $('#statusFilter, #typeFilter').val('all').trigger('change'); });

            let isPopulating = false;
            let currentSequence = 0;
            let currentPrefix = '';

            $('#customer_id').on('change', function() {
                if (isPopulating) return;

                let custId = $(this).val();
                if(!custId) return;

                $.ajax({
                    url: "{{ route('bg.generate-number') }}",
                    type: "GET",
                    data: { customer_id: custId },
                    success: function(res) {
                        if(res.status === 'success') {
                            currentSequence = res.sequence;
                            currentPrefix = res.prefix;

                            let firstInput = $('input[name="items[0][bg_number]"]');

                            if(firstInput.val() === '' || firstInput.val().startsWith('BG-')) {
                                firstInput.val(res.number);
                            }
                        }
                    }
                });
            });

            function updateRowNumbers() {
                $('.bg-item-row').each(function(index) {
                    let rowNumber = index + 1
                    let rowIndex = index;

                    $(this).find('.card-header h6').text(`Bank Garansi Information #${rowNumber}`);

                    $(this).find('input, select').each(function() {
                        let name = $(this).attr('name');
                        if (name) {
                            let newName = name.replace(/items\[\d+\]/, `items[${rowIndex}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            function addBgItem(data = null, forceType = null) {
                const tempIndex = new Date().getTime() + Math.floor(Math.random() * 1000);
                let bgNumber = '';
                if (data && data.bg_number) {
                    bgNumber = data.bg_number;
                } else {
                    if (currentPrefix !== '' && currentSequence > 0) {
                        let existingRows = $('.bg-item-row').length;
                        let nextSeq = currentSequence + existingRows;
                        let seqStr = nextSeq.toString().padStart(4, '0');
                        bgNumber = currentPrefix + seqStr;
                    }
                }

                let bgType = 'new';
                if(forceType) {
                    bgType = forceType;
                } else if (data && data.bg_type) {
                    bgType = data.bg_type;
                }

                let nominalVal = '';
                if (data && data.bg_nominal && parseFloat(data.bg_nominal) > 0) {
                    nominalVal = new Intl.NumberFormat('id-ID').format(data.bg_nominal);
                }

                const today = new Date().toISOString().split('T')[0];
                const issued = (data && data.issued_date) ? data.issued_date.substring(0,10) : new Date().toISOString().split('T')[0];
                const exp = (data && data.exp_date) ? data.exp_date.substring(0,10) : new Date().toISOString().split('T')[0];

                let detail = (data && data.details && data.details.length > 0) ? data.details[0] : (data && data.details ? data.details : {}); // Handle structure variations
                const bankName = detail.bank_name || '';
                const branch = detail.branch_name || '';
                const address = detail.bank_address || '';
                const pic = detail.contact_person || '';

                const html = `
                    <div class="card mb-3 border bg-item-row">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="mb-0 fw-bold text-dark fs-6">Bank Garansi Information #...</h6>
                            ${!data ? `<button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="ph-bold ph-trash"></i></button>` : ''}
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">BG Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="items[${tempIndex}][bg_number]" value="${bgNumber}" required placeholder="e.g. BG/2025/001">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Type <span class="text-danger">*</span></label>
                                    <select class="form-select bg-type-select" name="items[${tempIndex}][bg_type]">
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
                                                name="items[${tempIndex}][nominal]"
                                                value="${nominalVal}"
                                                required placeholder="0"
                                                onkeyup="formatRupiah(this)">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Issued Date</label>
                                    <input type="date" class="form-control bg-light" name="items[${tempIndex}][issued_date]" value="${issued}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Expiry Date</label>
                                    <input type="date" class="form-control bg-light" name="items[${tempIndex}][exp_date]" value="${exp}">
                                </div>

                                <div class="col-12"><hr class="my-1 border-dashed"></div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="items[${tempIndex}][bank_name]" value="${bankName}" required placeholder="e.g. Bank Mandiri">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Branch</label>
                                    <input type="text" class="form-control" name="items[${tempIndex}][branch_name]" value="${branch}" placeholder="Branch Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">PIC Name</label>
                                    <input type="text" class="form-control" name="items[${tempIndex}][contact_person]" value="${pic}" placeholder="PIC Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Bank Address</label>
                                    <input type="text" class="form-control" name="items[${tempIndex}][bank_address]" value="${address}" placeholder="Address">
                                </div>
                            </div>
                        </div>
                    </div>`;

                $('#items-container').append(html);
                updateRowNumbers();
            }

            $('#btn-add-item').on('click', function() { addBgItem(); });
            $(document).on('click', '.btn-remove-item', function() {
                $(this).closest('.bg-item-row').remove();
                updateRowNumbers();
            });

            function resetModal(title) {
                $('#bgForm')[0].reset();
                $('#bgId').val('');
                $('#formMethod').val('POST');
                $('#items-container').empty();
                $('.select2-modal').val(null).trigger('change');
                $('#bgModalLabel').text(title);
                $('#btn-add-item').hide();
                currentSequence = 0;
                currentPrefix = '';
            }

            $('#btn-create-bg').on('click', function() {
                resetModal('Create New Bank Garansi');
                $('#btn-add-item').show();
                addBgItem();
                $('#bgModal').modal('show');
            });

            const baseUrl = "{{ route('bg-list.index') }}";

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get(baseUrl + "/" + id, function(data) {
                    resetModal('Edit Bank Garansi');
                    $('#bgId').val(data.id);
                    $('#formMethod').val('PUT');

                    isPopulating = true;

                    $('#customer_id').val(data.customer_id).trigger('change');

                    addBgItem(data);
                    isPopulating = false;

                    $('#btn-add-item').show();
                    $('#bgModal').modal('show');
                });
            });

            $(document).on('click', '.btn-extension', function() {
                let id = $(this).data('id');
                $.get(baseUrl + "/" + id, function(data) {
                    resetModal('Extension Bank Garansi');

                    $('#formMethod').val('POST');

                    isPopulating = true;
                    $('#customer_id').val(data.customer_id).trigger('change');
                    isPopulating = false;

                    $.ajax({
                        url: "{{ route('bg.generate-number') }}",
                        type: "GET",
                        data: { customer_id: data.customer_id },
                        success: function(res) {
                            if(res.status === 'success') {
                                currentSequence = res.sequence;
                                currentPrefix = res.prefix;
                                addBgItem(null, 'extension');

                                $('input[name="items[0][bg_number]"]').val(res.number);
                            }
                        }
                    });

                    $('#bgModal').modal('show');
                });
            });

            $(document).on('click', '.btn-existing', function() {
                let id = $(this).data('id');
                $.get(baseUrl + "/" + id, function(data) {
                    resetModal('Existing Bank Garansi (Renewal)');

                    $('#bgId').val(data.id);
                    $('#formMethod').val('PUT');

                    isPopulating = true;
                    $('#customer_id').val(data.customer_id).trigger('change');

                    addBgItem(data, 'existing');

                    isPopulating = false;

                    $('#bgModal').modal('show');
                });
            });

            $(document).on('click', '.btn-show', function() {
                let id = $(this).data('id');
                $('#detail-bg-number').text('Loading...');
                $('#bgDetailModal').modal('show');

                const formatDate = (dateStr) => {
                    if (!dateStr) return '-';
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    return new Date(dateStr).toLocaleDateString('id-ID', options);
                };

                $.get(baseUrl + "/" + id, function(data) {
                    $('#detail-bg-number').text(data.bg_number);

                    let statusClass = 'secondary';
                    if(data.status === 'approved') statusClass = 'success';
                    else if(data.status === 'submitted') statusClass = 'primary';
                    else if(data.status === 'expired') statusClass = 'danger';

                    $('#detail-status').text(data.status.toUpperCase())
                        .attr('class', `badge rounded-pill px-3 py-2 fs-6 bg-${statusClass}`);

                    $('#detail-customer').text(data.customer ? data.customer.name : '-');
                    $('#detail-type').text(data.bg_type.toUpperCase());
                    $('#detail-nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.bg_nominal));

                    $('#detail-issued').text(formatDate(data.issued_date));
                    $('#detail-expired').text(formatDate(data.exp_date));

                    $('#detail-creator').text(data.creator ? data.creator.name : 'System');
                    $('#detail-updated').text(formatDate(data.updated_at));

                    let bankHtml = '<p class="text-muted text-center mb-0">No bank details found.</p>';
                    if(data.details && data.details.length > 0) {
                        let d = data.details[0];

                        bankHtml = `
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                <span class="text-muted small">Bank Name:</span>
                                <span class="fw-bold text-dark text-end">${d.bank_name}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                <span class="text-muted small">Branch:</span>
                                <span class="fw-bold text-dark text-end">${d.branch_name || '-'}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-1">
                                <span class="text-muted small">Address:</span>
                                <span class="fw-bold text-dark text-end text-wrap w-50" style="line-height: 1.2;">${d.bank_address || '-'}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">PIC:</span>
                                <span class="fw-bold text-dark text-end">${d.contact_person || '-'}</span>
                            </div>
                        `;
                    }
                    $('#detail-bank-container').html(bankHtml);
                }).fail(function() {
                    Swal.fire('Error', 'Failed to load data details.', 'error');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: baseUrl + "/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                Swal.fire('Deleted!', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Failed to delete data.', 'error');
                            }
                        });
                    }
                });
            });

            $('#bgForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#bgId').val();
                const method = $('#formMethod').val();

                let url = "{{ route('bg-list.store') }}";

                if(method === 'PUT' && id) {
                    url = "{{ route('bg-list.update', ':id') }}".replace(':id', id);
                }

                const formData = new FormData(this);

                const keys = Array.from(formData.keys());
                for (const key of keys) {
                    if (key.includes('[nominal]')) {
                        let rawVal = formData.get(key);
                        if(rawVal) {
                            let cleanVal = rawVal.replace(/\./g, '').replace(/,/g, '.');
                            cleanVal = rawVal.replace(/\./g, '');
                            formData.set(key, cleanVal);
                        }
                    }
                }

                Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
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
        });
    </script>
    @endpush
</x-app-layout>
