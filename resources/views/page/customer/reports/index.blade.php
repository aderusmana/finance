<x-app-layout>
    @section('title', 'Batch Print Reports')

    @include('components.sample-table-styles')

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25); position: relative; overflow: hidden; margin-bottom: -1rem; z-index: 1;">
                    <div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Batch Print Reports</h3>
                        <p class="mb-0" style="color: #d1fae5; font-size: 0.95rem;">Select and print approved Customer master data documents in bulk.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. CONTROL BAR (FILTER & CETAK) --}}
        <div class="row m-2 mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between" style="background: #ffffff; border-radius: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; padding: 1.25rem 1.5rem; z-index: 2; position: relative;">
                    
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1 border">
                            <i class="ph-bold ph-calendar-blank text-primary"></i>
                            <span class="text-muted fw-bold" style="font-size: 0.85rem;">DATE CREATED</span>
                        </div>
                        <select id="dateFilter" class="form-select select2" style="width: 180px;">
                            <option value="all">All Time</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last_30_days">Last 30 Days</option>
                        </select>
                        <input type="hidden" id="startDate" name="startDate" />
                        <input type="hidden" id="endDate" name="endDate" />

                        <button id="resetDateFilter" class="btn btn-light border rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; color: #475569;" title="Reset Filters">
                            <i class="ph-bold ph-arrows-clockwise fs-5"></i>
                        </button>
                    </div>

                    <div class="d-flex mt-3 mt-md-0">
                        <button class="btn d-flex align-items-center gap-2 shadow-sm" type="button" onclick="printSelectedReports()" id="printSelectedBtn" disabled style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; border: none; border-radius: 50rem; padding: 0.6rem 1.5rem; font-weight: 700; transition: all 0.2s;">
                            <i class="ph-bold ph-printer fs-5"></i>
                            <span>Print Selected (<span id="selectedCount" class="text-warning">0</span>)</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. TABEL DATA CETAK --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-files me-2" style="color: #10b981;"></i>Customer List Ready for Printing</h5>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill"><i class="ph-bold ph-shield-check me-1"></i>Approved Data Only</span>
                    </div>
                    
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100 display" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; width: 5%; padding: 1.25rem 1rem;">
                                            <input type="checkbox" id="selectAll" class="form-check-input" style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                                        </th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 5%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">No PKD</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Customer Code</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 25%;">Company Name</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">PIC</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Created By</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateSelectedCount() {
            const selectedCheckboxes = $('.row-selector:checked');
            const count = selectedCheckboxes.length;
            $('#selectedCount').text(count);
            
            if (count > 0) {
                $('#printSelectedBtn').prop('disabled', false).css({'opacity': '1', 'cursor': 'pointer'});
            } else {
                $('#printSelectedBtn').prop('disabled', true).css({'opacity': '0.6', 'cursor': 'not-allowed'});
            }

            const totalCheckboxes = $('.row-selector').length;
            const selectAllCheckbox = $('#selectAll');

            if (count === 0) {
                selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
            } else if (count === totalCheckboxes && totalCheckboxes > 0) {
                selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true).prop('checked', false);
            }
        }

        function printSelectedReports() {
            const selectedIds = [];
            $('.row-selector:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire('Attention', 'Please check at least one customer data to print.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Print Confirmation',
                html: `You will be printing the Master Data document for <b>${selectedIds.length} Customer</b> selected. Continue?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ph-bold ph-printer me-1"></i> Yes, Print Now',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                    cancelButton: 'btn btn-light rounded-pill px-4 fw-bold shadow-sm border'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Preparing PDF Document...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

                    const form = $('<form>', { action: "{{ route('customers.reports.print') }}", method: 'POST', target: '_blank' });
                    form.append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }));
                    
                    selectedIds.forEach(id => {
                        form.append($('<input>', { type: 'hidden', name: 'selected_ids[]', value: id }));
                    });

                    $('body').append(form);
                    form.submit();
                    form.remove();

                    setTimeout(() => {
                        Swal.close();
                        $('.row-selector').prop('checked', false);
                        $('#selectAll').prop('checked', false).prop('indeterminate', false);
                        updateSelectedCount();
                    }, 1500);
                }
            });
        }

        $(document).ready(function () {
            $('.select2').select2({ theme: 'bootstrap-5', minimumResultsForSearch: 10 });

            let table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('customers.reports.data') }}",
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#startDate').val();
                        d.end_date = $('#endDate').val();
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle',
                        render: function (data, type, row) {
                            return `<input type="checkbox" class="form-check-input row-selector" value="${row.id}" style="width: 1.2rem; height: 1.2rem; cursor: pointer;">`;
                        }
                    },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
                    { data: 'no_pkd', name: 'customers.no_pkd', className: 'align-middle' },
                    { data: 'code', name: 'customers.code', className: 'align-middle' },
                    { data: 'name', name: 'customers.name', className: 'align-middle' }, 
                    { data: 'pic', name: 'customers.pic', defaultContent: '-', className: 'align-middle text-muted fw-bold' },
                    { data: 'requester_name', name: 'requester_name', defaultContent: '-', className: 'align-middle' }, 
                    { data: 'status_approval', name: 'customers.status_approval', className: 'text-center align-middle' }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 Search customer name...",
                    lengthMenu: "Show _MENU_ data",
                    info: "Showing _START_ to _END_ of _TOTAL_ print queue"
                },
                drawCallback: function() {
                    $('.row-selector').on('change', updateSelectedCount);
                    
                    $('#sampleTable tbody td').css({
                        'padding': '1rem',
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
                'background-color': '#ffffff',
                'box-shadow': 'inset 0 1px 2px rgba(0,0,0,0.02)'
            });

            $('#selectAll').on('change', function() {
                $('.row-selector').prop('checked', this.checked);
                updateSelectedCount();
            });

            $('#dateFilter').on('change', function() {
                let filterVal = $(this).val();
                let start = '';
                let end = '';

                let today = new Date();
                
                if(filterVal === 'yesterday') {
                    let yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    start = yesterday.toISOString().split('T')[0];
                    end = yesterday.toISOString().split('T')[0];
                } else if(filterVal === 'last_30_days') {
                    let last30 = new Date(today);
                    last30.setDate(last30.getDate() - 30);
                    start = last30.toISOString().split('T')[0];
                    end = today.toISOString().split('T')[0];
                }

                $('#startDate').val(start);
                $('#endDate').val(end);
                
                table.ajax.reload(function() {
                    $('#selectAll').prop('checked', false);
                    updateSelectedCount();
                });
            });

            $('#resetDateFilter').on('click', function() {
                $('#dateFilter').val('all').trigger('change');
            });
        });
    </script>
    @endpush
</x-app-layout>