<x-app-layout>
    @section('title')
        {{-- [DIUBAH] Judul halaman disesuaikan --}}
        Free Goods Requisition Reports
    @endsection

    {{-- [DIUBAH] Memuat komponen gaya untuk Free Goods (Tema Hijau & Hitam) --}}
    @include('components.freegoods-table-styles')

    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @endpush

    {{-- [DITAMBAHKAN] Pembungkus utama untuk memberikan padding dan background --}}
    <div class="bg-white p-4 rounded shadow-sm">
        <div class="row m-1">
            <div class="col-12">
                <h4 class="main-title">Requisition Reports</h4>
                <ul class="app-line-breadcrumbs mb-3">
                    <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-chart-bar f-s-16"></i> Reports</a></li>
                    {{-- [DIUBAH] Breadcrumb disesuaikan --}}
                    <li class="active"><a class="f-s-14 f-w-500" href="#">Free Goods Report</a></li>
                </ul>
            </div>
        </div>
    
        <div class="row">
            <div class="col-12">
                <div class="filter-container">
                    <form id="filter-form" class="row g-3 justify-content-between align-items-end">
                        <div class="col-lg-6 col-md-8">
                            <label for="reportrange" class="form-label fw-bold">Filter by Request Date:</label>
                            <div id="reportrange" class="form-control">
                                <i class="ph-bold ph-calendar"></i>&nbsp;
                                <span></span> <i class="ph-bold ph-caret-down"></i>
                            </div>
                            <input type="hidden" id="start_date" name="start_date">
                            <input type="hidden" id="end_date" name="end_date">
                        </div>
                        <div class="col-lg-auto col-md-4 text-end">
                            <button type="button" id="btn-reset" class="btn btn-secondary">Reset Filter</button>
                        </div>
                    </form>
                </div>
    
                <div class="main-table-container">
                    <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                        <div>
                            {{-- [DIUBAH] Judul tabel disesuaikan --}}
                            <h4 class="table-title"><i class="ph-duotone ph-list-checks"></i> Free Goods List for Printing</h4>
                            <p class="table-subtitle">Select requisitions to print in a batch.</p>
                        </div>
                        <div>
                            {{-- [DIUBAH] Rute form disesuaikan --}}
                            <form id="print-form" action="{{ route('freegoods.report.print.batch') }}" method="POST" target="_blank">
                                @csrf
                                <div id="hidden-ids-container"></div>
                                <button type="submit" id="print-selected-btn" class="btn btn-success" disabled>
                                    <i class="ph-bold ph-printer me-1"></i> Print Selected (<span id="selected-count">0</span>)
                                </button>
                            </form>
                        </div>
                    </div>
    
                    <div class="table-responsive">
                        {{-- [DIUBAH] ID Tabel disesuaikan --}}
                        <table class="w-100 display" id="freegoodsReportTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 20px;">
                                        <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                    </th>
                                    {{-- [DIUBAH] Header kolom disesuaikan --}}
                                    <th>FG No.</th>
                                    <th>Requester</th>
                                    <th>Customer</th>
                                    <th>Request Date</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script>
            $(document).ready(function () {
                // [DIUBAH] ID Tabel dan route disesuaikan
                const table = $('#freegoodsReportTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('freegoods.reports.data') }}", // Asumsi nama route
                        data: function (d) {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                        }
                    },
                    columns: [
                        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
                        { data: 'no_srs', name: 'no_srs' }, // Kolom `no_srs` digunakan untuk FG No.
                        { data: 'requester_info', name: 'requester.name' },
                        { data: 'customer_name', name: 'customer.name' },
                        { data: 'request_date', name: 'request_date' },
                        { data: 'sub_category', name: 'sub_category' },
                        { data: 'status', name: 'status' }
                    ],
                    order: [[4, 'desc']]
                });

                // Fungsionalitas Date Range Picker (tidak perlu diubah)
                const initial_start = moment('2020-01-01');
                const initial_end = moment();

                function cb(start, end) {
                    if (start.isSame(initial_start) && end.isSame(initial_end, 'day')) {
                        $('#reportrange span').html('All Time');
                    } else {
                        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    }
                    $('#start_date').val(start.format('YYYY-MM-DD'));
                    $('#end_date').val(end.format('YYYY-MM-DD'));
                }

                $('#reportrange').daterangepicker({
                    startDate: initial_start,
                    endDate: initial_end,
                    ranges: {
                       'All Time': [moment('2020-01-01'), moment()],
                       'Today': [moment(), moment()],
                       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                       'This Month': [moment().startOf('month'), moment().endOf('month')],
                       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(initial_start, initial_end);

                $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                    table.ajax.reload();
                });

                $('#btn-reset').on('click', function() {
                    cb(initial_start, initial_end);
                    table.ajax.reload();
                });

                // Fungsionalitas Checkbox (hanya selector tabel yang diubah)
                function updateSelectedCount() {
                    const selectedCheckboxes = $('.requisition-checkbox:checked');
                    const selectedCount = selectedCheckboxes.length;
                    $('#selected-count').text(selectedCount);
                    $('#print-selected-btn').prop('disabled', selectedCount === 0);
                    $('#hidden-ids-container').empty();
                    selectedCheckboxes.each(function() {
                        $('#hidden-ids-container').append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
                    });
                }
                $('#select-all-checkbox').on('click', function() {
                    $('.requisition-checkbox').prop('checked', this.checked);
                    updateSelectedCount();
                });

                // [DIUBAH] Event listener disesuaikan dengan ID tabel
                $('#freegoodsReportTable tbody').on('change', '.requisition-checkbox', function() {
                    updateSelectedCount();
                    if (!this.checked) {
                        $('#select-all-checkbox').prop('checked', false);
                    }
                });

                table.on('draw', function() {
                    $('#select-all-checkbox').prop('checked', false);
                    updateSelectedCount();
                });
            });
        </script>
    @endpush
</x-app-layout>