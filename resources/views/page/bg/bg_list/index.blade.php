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

                // --- DYNAMIC ITEMS LOGIC ---
                let itemIndex = 0;

                function addBgItem(data = null) {
                    const index = itemIndex++;

                    // PERBAIKAN: Gunakan || '' untuk mencegah "undefined" pada input value
                    const bgNumber = (data && data.bg_number) ? data.bg_number : '';
                    const bgType = (data && data.bg_type) ? data.bg_type : 'new';

                    // Nominal Logic (Format ke Ribuan)
                    let nominalVal = '';
                    if (data && data.bg_nominal && parseFloat(data.bg_nominal) > 0) {
                        nominalVal = new Intl.NumberFormat('id-ID').format(data.bg_nominal);
                    }

                    // Dates
                    const today = new Date().toISOString().split('T')[0];
                    const issued = (data && data.issued_date) ? data.issued_date.substring(0,10) : today;
                    const exp = (data && data.exp_date) ? data.exp_date.substring(0,10) : today;

                    // Detail Logic (Safe Navigation)
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
                                    {{-- Baris 1: BG Info --}}
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

                                    {{-- Baris 2: Dates (Readonly) --}}
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Issued Date</label>
                                        <input type="date" class="form-control bg-light" name="items[${index}][issued_date]" value="${issued}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted">Expiry Date</label>
                                        <input type="date" class="form-control bg-light" name="items[${index}][exp_date]" value="${exp}" readonly>
                                    </div>

                                    <div class="col-12"><hr class="my-1 border-dashed"></div>

                                    {{-- Baris 3: Bank Details --}}
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

                // --- CREATE MODE ---
                $('#btn-create-bg').on('click', function() {
                    $('#bgForm')[0].reset();
                    $('#bgId').val(''); $('#formMethod').val('POST');

                    itemIndex = 0; // Reset index agar mulai dari #1
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

                        // Load data yang diterima dari controller (sekarang sudah pasti JSON)
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
            });
        </script>
    @endpush
</x-app-layout>
