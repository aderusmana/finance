<x-app-layout>
    @section('title', 'Master Logistic Fee')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        {{-- 1. HEADER BANNER MEWAH --}}
        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2); position: relative; margin-bottom: -3rem; z-index: 1;">
                    <div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Logistics Fee Management</h3>
                        <p class="mb-0" style="color: #e0e7ff; font-size: 0.95rem;">Manage shipping fees, submit changes, and monitor approval status with ease.</p>
                    </div>
                    <!-- <div class="flex-shrink-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.15); padding: 0.5rem 1.2rem; border-radius: 2rem; display: inline-flex; flex-wrap: nowrap;">
                                <li class="breadcrumb-item"><a href="#" class="text-white text-decoration-none"><i class="ph-fill ph-folder me-1"></i> Master Data</a></li>
                                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Logistic Fee</li>
                            </ol>
                        </nav>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- 2. SUMMARY CARDS --}}
        <div class="row m-2 mb-2">
            <div class="col-md-3">
                <div style="background: #ffffff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255, 255, 255, 0.8); position: relative; z-index: 2; height: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-bold mb-1" style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Data</p>
                            <h3 class="fw-bolder mb-0" style="color: #1e293b;" id="total_data">-</h3>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #e0e7ff; color: #4f46e5;">
                            <i class="ph-fill ph-database"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: #ffffff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255, 255, 255, 0.8); position: relative; z-index: 2; height: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-bold mb-1" style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Active (Completed)</p>
                            <h3 class="fw-bolder mb-0" style="color: #059669;" id="total_active">-</h3>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #dcfce7; color: #16a34a;">
                            <i class="ph-fill ph-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: #ffffff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255, 255, 255, 0.8); position: relative; z-index: 2; height: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fw-bold mb-1" style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Waiting for Approval</p>
                            <h3 class="fw-bolder mb-0" style="color: #d97706;" id="total_pending">-</h3>
                        </div>
                        <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: #fef3c7; color: #d97706;">
                            <i class="ph-fill ph-hourglass-high"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background: #ffffff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid rgba(255, 255, 255, 0.8); position: relative; z-index: 2; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                    <button class="btn w-100 py-3 fw-bolder fs-6 d-flex align-items-center justify-content-center gap-2"
                            style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border: none; color: white; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2); border-radius: 50rem;"
                            onclick="openModal()">
                        <i class="ph-bold ph-plus-circle fs-4"></i> Add New Fee
                    </button>
                </div>
            </div>
        </div>

        {{-- 3. TABEL DATA UTAMA --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; margin-top: 1rem; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-list-dashes me-2" style="color: #4f46e5;"></i>Logistics Fee List</h5>
                        <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3" style="color: #475569;" onclick="table.ajax.reload()"><i class="ph-bold ph-arrows-clockwise me-1"></i> Refresh</button>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0; width: 5%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0;">Distributor Code</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0;">Distributor Name</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0;">Customer Code</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0;">Customer Name</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0; width: 15%;"><i class="ph-bold ph-currency-circle-dollar me-1" style="color: #4f46e5;"></i> Logistic Fee / ctn</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0; width: 10%;">Status</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0; width: 10%;">Route to</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1.5rem; border-bottom: 2px solid #e2e8f0; width: 10%;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL LOGISTIC FEE --}}
        <div class="modal fade" id="modalForm" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
                    <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; padding: 1.5rem 2rem;">
                        <div>
                            <h5 class="modal-title fw-bolder mb-1" style="color: #1e293b;" id="modalTitle"><i class="ph-fill ph-note-pencil me-2" style="color: #2563eb;"></i>Form Logistic Fee</h5>
                            <p class="mb-0" style="color: #64748b; font-size: 0.85rem;" id="modalSubtitle">Please complete the data for the price submission.</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="mainForm">
                        @csrf
                        <input type="hidden" name="id" id="dataId">
                        <input type="hidden" id="old_logistic_fee" value="0">

                        <div class="modal-body" style="padding: 2rem;">
                            <div id="createModeWrapper">
                                <div class="mb-4">
                                    <label class="form-label fw-bold" style="color: #334155;">Select Distributor <span class="text-danger">*</span></label>
                                    <select name="distributor_id" id="distributor_id" class="form-select custom-select-inline" style="width: 100%;">
                                        <option value="">-- Type to search --</option>
                                        @foreach ($distributors as $distributor)
                                            <option value="{{ $distributor->id }}">{{ $distributor->code }} - {{ $distributor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold" style="color: #334155;">Select Customer <span class="text-danger">*</span></label>
                                    <select name="customer_id" id="customer_id" class="form-select custom-select-inline" style="width: 100%;">
                                        <option value="">-- Type to search --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->customer_code ?? ($customer->code ?? '-') }} - {{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="editModeWrapper" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label fw-bold" style="color: #64748b; font-size: 0.85rem;">Distributor</label>
                                    <input type="text" id="distributor_info" class="form-control fw-bold" readonly style="background-color: #f1f5f9; border: none; color: #334155; border-radius: 0.75rem; padding: 0.6rem 1rem;">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-bold" style="color: #64748b; font-size: 0.85rem;">Customer</label>
                                    <input type="text" id="customer_info" class="form-control fw-bold" readonly style="background-color: #f1f5f9; border: none; color: #334155; border-radius: 0.75rem; padding: 0.6rem 1rem;">
                                </div>

                                <div class="mb-4 p-3" style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 1rem;">
                                    <label class="form-label fw-bold mb-1" style="color: #64748b; font-size: 0.85rem;">Current Active Price</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph-fill ph-tag fs-4" style="color: #94a3b8;"></i>
                                        <span class="fs-5 fw-bolder" style="color: #334155;" id="current_logistic_fee">Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bolder" style="color: #312e81;" id="label_logistic_fee">Submitted Price (Logistic Fee / ctn) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg shadow-sm" style="overflow: hidden; border-radius: 1rem;">
                                    <span class="input-group-text fw-bolder px-4" style="background-color: #eef2ff; border: 1px solid #c7d2fe; color: #4338ca;">Rp</span>
                                    <input type="text" name="logistic_fee" id="logistic_fee" class="form-control fw-bolder fs-4" required placeholder="0" style="border: 1px solid #c7d2fe; color: #4338ca;">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-end gap-2" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem;">
                            <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-bold border shadow-sm" style="color: #475569;" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border: none; color: white;"><i class="ph-bold ph-paper-plane-right me-2"></i>Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            let table;
            let originalCustomerOptionsHtml = null;

            function formatRupiah(angka) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return rupiah;
            }

            function refreshCustomerSelect2() {
                $('#customer_id').select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    placeholder: "-- Type to search --",
                    allowClear: true
                });
            }

            function updateSummaryCounters(json) {
                if(json) {
                    $('#total_data').text(json.recordsTotal || 0);
                    $('#total_active').text(json.total_active || 0);
                    $('#total_pending').text(json.total_pending || 0);
                }
            }

            $(document).ready(function() {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

                $('#distributor_id, #customer_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    placeholder: "-- Type to search --",
                    allowClear: true
                });

                originalCustomerOptionsHtml = $('#customer_id').html();

                table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('logistic-fees.index') }}",
                        dataSrc: function ( json ) {
                            updateSummaryCounters(json);
                            return json.data;
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'distributor_code', name: 'distributor.code' },
                        { data: 'distributor_name', name: 'distributor.name' },
                        { data: 'customer_code', name: 'customer.code' },
                        { data: 'customer_name', name: 'customer.name' },
                        { data: 'logistic_fee', name: 'logistic_fee' },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'route_to', name: 'route_to', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    order: [],
                    language: {
                        search: "",
                        searchPlaceholder: "🔍 Search data...",
                        lengthMenu: "Tampilkan _MENU_ baris",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data"
                    },
                    drawCallback: function(settings) {
                        $('#sampleTable tbody td').css({
                            'padding': '1.25rem 1.5rem',
                            'vertical-align': 'middle',
                            'border-bottom': '1px solid #f1f5f9'
                        });
                    }
                });

                $('.dataTables_filter input').css({
                    'width': '250px',
                    'margin-left': '10px',
                    'border-radius': '50rem',
                    'border': '1px solid #cbd5e1',
                    'padding': '0.4rem 1rem',
                    'background-color': '#f8fafc'
                });

                $('#logistic_fee').on('keyup', function() { $(this).val(formatRupiah($(this).val())); });

                $('#distributor_id').on('change', function() {
                    let distId = $(this).val();
                    let custSelect = $('#customer_id');
                    custSelect.val(null).trigger('change');
                    if (!distId) {
                        if (originalCustomerOptionsHtml) custSelect.html(originalCustomerOptionsHtml);
                        refreshCustomerSelect2(); return;
                    }
                    $.get("{{ url('/get-customers-by-distributor') }}/" + distId).done(function(data) {
                        custSelect.empty().append('<option value="">-- Type to search --</option>');
                        $.each(data, function(k, v) {
                            let code = v.customer_code ? v.customer_code : v.code;
                            custSelect.append('<option value="'+v.id+'">'+code+' - '+v.name+'</option>');
                        });
                        refreshCustomerSelect2();
                    });
                });

                $('#mainForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#dataId').val();
                    let url = id ? "{{ url('/logistic-fees') }}/" + id : "{{ route('logistic-fees.store') }}";
                    let method = id ? "PUT" : "POST";

                    let distributorName = id ? $('#distributor_info').val() : $('#distributor_id option:selected').text();
                    let customerName = id ? $('#customer_info').val() : $('#customer_id option:selected').text();
                    let newFeeVal = $('#logistic_fee').val();
                    let oldFeeVal = formatRupiah($('#old_logistic_fee').val().toString());

                    let oldFeeRow = id ?
                        `<tr><td style="color: #64748b; padding: 4px 0;">Current Price</td><td style="padding: 4px 0;">: <span style="color: #94a3b8; text-decoration: line-through;">Rp ${oldFeeVal}</span></td></tr>` : '';

                    Swal.fire({
                        title: id ? 'Confirm Change' : 'Confirm Request',
                        html: `
                        <div class="text-start" style="font-size: 0.95rem;">
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 1rem; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td width="35%" style="color: #64748b; padding: 4px 0;">Distributor</td><td style="padding: 4px 0;">: <b style="color: #1e293b;">${distributorName}</b></td></tr>
                                    <tr><td style="color: #64748b; padding: 4px 0;">Customer</td><td style="padding: 4px 0;">: <b style="color: #1e293b;">${customerName}</b></td></tr>
                                    ${oldFeeRow}
                                    <tr><td style="color: #64748b; padding: 8px 0;">New Price</td><td style="padding: 8px 0;">: <b style="color: #4f46e5; font-size: 1.25rem;">Rp ${newFeeVal}</b></td></tr>
                                </table>
                            </div>
                            <p class="mb-0" style="color: #64748b; font-size: 0.85rem;">
                                <i class="ph-fill ph-info me-1" style="color: #6366f1;"></i> Notification <b>Approval</b> will be sent to the Supervisor.
                            </p>
                        </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5',
                        cancelButtonColor: '#cbd5e1',
                        confirmButtonText: '<i class="ph-bold ph-paper-plane-right me-1"></i> Yes, Send',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm',
                            cancelButton: 'btn rounded-pill px-4 fw-bold border shadow-sm'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let rawData = $(this).serializeArray();
                            $.each(rawData, function(i, field) {
                                if (field.name === 'logistic_fee') field.value = field.value.replace(/\./g, '');
                            });

                            Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

                            $.ajax({
                                url: url, method: method, data: $.param(rawData),
                                success: function(res) {
                                    $('#modalForm').modal('hide');
                                    table.ajax.reload();
                                    Swal.fire({
                                        title: 'Success!', html: res.message, icon: 'success',
                                        customClass: { confirmButton: 'btn btn-success rounded-pill px-4 fw-bold shadow-sm' }, buttonsStyling: false
                                    });
                                },
                                error: function(err) { Swal.fire('Failed', 'An error occurred.', 'error'); }
                            });
                        }
                    });
                });

                $(document).on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    $.get("{{ url('/logistic-fees') }}/" + id, function(data) {
                        $('#mainForm')[0].reset();
                        $('#dataId').val(data.id);
                        $('#distributor_info').val(data.distributor_info);
                        $('#customer_info').val(data.customer_info);
                        $('#old_logistic_fee').val(data.logistic_fee);
                        $('#current_logistic_fee').text('Rp ' + formatRupiah(data.logistic_fee.toString()));
                        $('#logistic_fee').val('');

                        $('#modalTitle').html('<i class="ph-fill ph-pencil-simple-line me-2" style="color: #4f46e5;"></i>Edit Logistic Fee');
                        $('#modalSubtitle').text('Change the price and resend for approval.');
                        $('#createModeWrapper').hide();
                        $('#editModeWrapper').show();
                        $('#distributor_id, #customer_id').prop('required', false);
                        $('#modalForm').modal('show');
                    });
                });
            });

            function openModal() {
                $('#mainForm')[0].reset();
                $('#dataId').val('');
                $('#old_logistic_fee').val(0);
                $('#distributor_id').val('').trigger('change');

                if (originalCustomerOptionsHtml) $('#customer_id').html(originalCustomerOptionsHtml);
                else $('#customer_id').empty().append('<option value="">-- Type to search --</option>');
                refreshCustomerSelect2();

                $('#modalTitle').html('<i class="ph-fill ph-plus-circle me-2" style="color: #4f46e5;"></i>Add Logistic Fee');
                $('#modalSubtitle').text('Add a new price relationship between Distributor and Customer.');
                $('#createModeWrapper').show();
                $('#editModeWrapper').hide();
                $('#distributor_id, #customer_id').prop('required', true);
                $('#modalForm').modal('show');
            }
        </script>
    @endpush
</x-app-layout>
