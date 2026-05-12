<x-app-layout>
    @section('title', 'Master Distributor')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Master Distributor</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Tambah Distributor
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Distributor</th>
                                    <th>Nama Distributor</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Distributor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Distributor <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: ID3455" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Distributor <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: PT. CITRA BHOGA JAYA" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Contoh: email@yahoo.com" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
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
                processing: true,
                serverSide: true,
                ajax: "{{ route('distributors.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('distributors.store') }}";
                let method = "POST";

                if(id) {
                    url = "{{ url('/distributors') }}/" + id;
                    method = "PUT";
                }

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(err) {
                        let msg = err.responseJSON.message || 'Gagal menyimpan data';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get("{{ url('/distributors') }}/" + id, function(data) {
                    $('#dataId').val(data.id);
                    $('#code').val(data.code);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#modalTitle').text('Edit Distributor');
                    $('#modalForm').modal('show');
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/distributors') }}/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(res) {
                                table.ajax.reload();
                                Swal.fire('Terhapus!', res.message, 'success');
                            }
                        });
                    }
                });
            });
        });

        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');
            $('#modalTitle').text('Tambah Distributor');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
