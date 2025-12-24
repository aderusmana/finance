<x-app-layout>
    @section('title', 'Version History')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Version History</h4>
            <div class="text-muted small mb-3">
                Customer: <strong>{{ $lampiranD->submission->recommendation->customer->name }}</strong> | 
                Ref: {{ $lampiranD->submission->form_code }}
            </div>
            <a href="{{ route('lampiran-d.index') }}" class="btn btn-sm btn-secondary mb-3">
                <i class="ph-bold ph-arrow-left"></i> Back to Overview
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <table class="w-100 display" id="versionsTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Version</th>
                            <th>Modified By</th>
                            <th>Date</th>
                            <th>Snapshot Data</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL SNAPSHOT DETAIL --}}
    <div class="modal fade" id="snapshotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Version Snapshot Data</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <pre id="jsonViewer" class="bg-light p-3 small" style="max-height: 400px; overflow: auto;"></pre>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            var id = "{{ $lampiranD->id }}";
            $('#versionsTable').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('lampiran-d.versions', $lampiranD->id) }}",
                order: [[1, 'desc']], // Sort by Version descending
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'version', name: 'version_no'},
                    {data: 'modified_by', name: 'generator.name'},
                    {data: 'date', name: 'generated_at'},
                    {data: 'changes', name: 'changes', orderable: false, searchable: false}
                ]
            });

            $(document).on('click', '.btn-view-snapshot', function() {
                var vId = $(this).data('id');
                $.get("{{ url('bg/lampiran-d/version') }}/" + vId, function(data) {
                    // Pretty print JSON
                    var json = JSON.stringify(data.data_snapshot, null, 2);
                    $('#jsonViewer').text(json);
                    $('#snapshotModal').modal('show');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>