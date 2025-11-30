<x-app-layout>
    @section('title')
        {{-- [DIUBAH] Judul halaman disesuaikan --}}
        Free Goods Activity Log
    @endsection

    @include('components.freegoods-table-styles')

    {{-- Pembungkus utama untuk memberikan padding dan background --}}
    <div class="bg-white p-4 rounded shadow-sm">
        <div class="row m-1">
            <div class="col-12">
                <h4 class="main-title">Activity Log Monitoring</h4>
                <ul class="app-line-breadcrumbs mb-3">
                    <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-monitor f-s-16"></i> Monitoring</a></li>
                    {{-- [DIUBAH] Breadcrumb disesuaikan --}}
                    <li class="active"><a class="f-s-14 f-w-500" href="#">Free Goods Activity Log</a></li>
                </ul>
            </div>
        </div>
    
        <div class="row">
            <div class="col-12">
                <div class="main-table-container">
                    <div class="table-header-enhanced">
                        <h4 class="table-title"><i class="ph-duotone ph-list-magnifying-glass"></i> System Activity Log</h4>
                        {{-- [DIUBAH] Subtitle disesuaikan --}}
                        <p class="table-subtitle">A read-only log of all activities related to Free Goods Requisitions.</p>
                    </div>
                    <div class="table-responsive">
                        {{-- [DIUBAH] ID Tabel disesuaikan --}}
                        <table class="w-100 display" id="freegoodsLogTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Log Name</th>
                                    <th>Description</th>
                                    <th>Event</th>
                                    {{-- [DIUBAH] Header kolom disesuaikan --}}
                                    <th>Subject (FG No.)</th>
                                    <th>Causer</th>
                                    <th>Timestamp</th>
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
                // [DIUBAH] Selector tabel dan route disesuaikan
                $('#freegoodsLogTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('freegoods.log.data') }}", // Asumsi nama route untuk log Free Goods
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '20px', className: 'text-center' },
                        { data: 'log_name', name: 'log_name' },
                        { data: 'description', name: 'description' },
                        { data: 'event', name: 'event', className: 'text-center' },
                        // Kolom 'subject.no_srs' biasanya sama karena modelnya sama (Requisition)
                        { data: 'subject_info', name: 'subject.no_srs', orderable: false },
                        { data: 'causer_info', name: 'causer.name', orderable: false },
                        { data: 'created_at', name: 'created_at' }
                    ],
                    order: [[ 6, 'desc' ]] // Default sort by timestamp descending
                });
            });
        </script>
    @endpush
</x-app-layout>