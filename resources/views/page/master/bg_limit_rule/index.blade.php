<x-app-layout>
    @section('title', 'BG Limit Rules')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Limit Rules Configuration</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Add Rule
                        </button>
                    </div>
                    <table class="table table-hover w-100" id="sampleTable">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Range Tahun</th>
                                <th>Percentage (%)</th>
                                <th>Description</th>
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
                    <h5 class="modal-title" id="modalTitle">Form Limit Rule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Min Year</label>
                                <input type="number" name="min_year" id="min_year" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Max Year</label>
                                <input type="number" name="max_year" id="max_year" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Percentage / FK Rule (%)</label>
                            <div class="input-group">
                                <input type="number" name="percentage" id="percentage" class="form-control" step="0.01" placeholder="75" required>
                                <span class="input-group-text">%</span>
                            </div>  
                            <div class="form-text text-info bg-soft-info p-2 rounded mt-1">
                                <small>
                                    <strong>Catatan:</strong><br>
                                    - Input <strong>75</strong> akan dihitung sebagai <strong>75%</strong> (Faktor Kali 0.75).<br>
                                    - Gunakan <strong>titik (.)</strong> untuk desimal (Contoh: <strong>75.5</strong>).
                                </small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="2"></textarea>
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
                ajax: "{{ route('limit-rules.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'range', name: 'range' },
                    { data: 'percentage', name: 'percentage' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']]
            });

            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('limit-rules.store') }}";
                let method = "POST";
                if(id) {
                    url = "{{ url('/limit-rules') }}/" + id;
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
                $.get("{{ url('/limit-rules') }}/" + id, function(data) {
                    $('#dataId').val(data.id);
                    $('#min_year').val(data.min_year);
                    $('#max_year').val(data.max_year);
                    $('#percentage').val(data.percentage);
                    $('#description').val(data.description);
                    $('#modalTitle').text('Edit Rule');
                    $('#modalForm').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/limit-rules') }}/" + id,
                            type: 'DELETE',
                            data: { "_token": "{{ csrf_token() }}" },
                            success: function(res) { table.ajax.reload(); Swal.fire('Deleted', '', 'success'); }
                        });
                    }
                });
            });
        });

        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');
            $('#modalTitle').text('Add Rule');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
