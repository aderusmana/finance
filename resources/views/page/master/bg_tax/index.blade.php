<x-app-layout>
    @section('title', 'BG Tax Config')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Tax Configuration</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Add Tax
                        </button>
                    </div>
                    <table class="table table-hover w-100" id="sampleTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Value (%)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Tax</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Increase Percentage" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value (%)</label>
                            <input type="number" name="value" id="value" class="form-control" step="0.01" placeholder="e.g. 11" required>
                            <small class="text-muted">Masukkan angka persen (contoh: ketik 11 untuk 11%). Sistem otomatis convert ke 0.11.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let table;
        $(document).ready(function() {
            table = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('tax.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },

                    { data: 'name', name: 'name' },
                    { data: 'value', name: 'value' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']]
            });

            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('tax.store') }}";
                let method = "POST";
                if(id) {
                    url = "{{ url('/tax') }}/" + id;
                    method = "PUT";
                }

                $.ajax({
                    url: url, method: method, data: $(this).serialize(),
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(err) { Swal.fire('Error', 'Gagal menyimpan', 'error'); }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/tax') }}/" + id, function(data) {
                    $('#dataId').val(data.id);
                    $('#name').val(data.name);
                    $('#value').val(data.value); // Sudah dikali 100 di controller
                    $('#modalTitle').text('Edit Tax');
                    $('#modalForm').modal('show');
                });
            });
        });

        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');
            $('#modalTitle').text('Add Tax');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
