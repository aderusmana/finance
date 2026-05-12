<x-app-layout>
    @section('title', 'Logistic Fee History Logs')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Fee History Logs</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph ph-clock-counter-clockwise f-s-16"></i> Master Data</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Fee History</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Distributor</th>
                                    <th>Harga Lama</th>
                                    <th>Harga Baru</th>
                                    <th>Status</th>
                                    <th>Diajukan Oleh</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('logistic-fees.log') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'date', name: 'created_at' },
                    { data: 'customer', name: 'customer' },
                    { data: 'distributor', name: 'distributor' },
                    { data: 'old_fee', name: 'old_fee' },
                    { data: 'new_fee', name: 'new_fee' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'action_by', name: 'action_by' },
                    { data: 'notes', name: 'notes', defaultContent: '-' },
                ]
            });
        });
    </script>
    @endpush
</x-app-layout>