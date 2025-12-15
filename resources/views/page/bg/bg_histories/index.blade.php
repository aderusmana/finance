<x-app-layout>
    @section('title', 'BG Histories')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Histories</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">BG Histories</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <table class="w-100 display" id="sampleTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>BG Number</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('bg-histories.index') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'bg_number', name: 'bankGaransi.bg_number', className: 'fw-bold' },
                    { data: 'description', name: 'description', orderable: false },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[5, 'desc']]
            });
        });
    </script>
    @endpush
</x-app-layout>
