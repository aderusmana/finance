<x-app-layout>
    @section('title', 'BG Histories (Completed Data)')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG History Logs</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">History Logs</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <div class="table-header-enhanced bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ph-bold ph-clock-counter-clockwise me-2 text-light"></i>
                            <span class="fw-bold text-light">Completed Transactions & Changes</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive p-3">
                    <table class="w-100 display align-middle" id="historyTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Customer</th>
                                <th width="15%">BG Number</th>
                                <th width="20%">Nominal Change (Old &rarr; New)</th>
                                <th width="15%">Exp. Date Change</th>
                                <th>Remarks</th>
                                <th>By</th>
                                <th>Time</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#historyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('bg-histories.index') }}",
                order: [[7, 'desc']],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'customer_name', name: 'bankGaransi.customer.name'},
                    {data: 'bg_number', name: 'bankGaransi.bg_number', className: 'fw-bold text-primary'},
                    {data: 'nominal_change', name: 'new_nominal'},
                    {data: 'date_change', name: 'new_exp_date'},
                    {data: 'remarks', name: 'remarks'},
                    {data: 'user', name: 'creator.name'},
                    {data: 'created_at', name: 'created_at', className: 'small text-muted'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });
        });
    </script>
    @endpush
</x-app-layout>
