<x-app-layout>
    @section('title', 'Master Customer Ship To')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Customer Ship To</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customer Ship To</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Add Ship To
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer Code</th>
                                    <th>Kode Ship To</th>
                                    <th>Nama Ship To</th>
                                    <th>Kota</th>
                                    <th>Sales Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CUSTOMER SHIP TO --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Customer Ship To</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select select2-custom" required>
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->customer_code ?? $customer->code ?? '-' }} - {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">User PIC / Sales <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select select2-custom" required>
                                    <option value="">-- Pilih Sales --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nik }} - {{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Ship To <span class="text-danger">*</span></label>
                                <input type="text" name="ship_to_code" id="ship_to_code" class="form-control" placeholder="Masukkan Kode Ship To (Cth: SHP-001)" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Ship To <span class="text-danger">*</span></label>
                                <input type="text" name="ship_to_name" id="ship_to_name" class="form-control" placeholder="Masukkan Nama Ship To/Penerima" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat 1 <span class="text-danger">*</span></label>
                            <input type="text" name="ship_to_address_1" id="ship_to_address_1" class="form-control" placeholder="Contoh: Jl. Jend. Sudirman Kav. 21, Gedung X" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat 2</label>
                                <input type="text" name="ship_to_address_2" id="ship_to_address_2" class="form-control" placeholder="Gedung / Lantai / Blok (Opsional)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat 3</label>
                                <input type="text" name="ship_to_address_3" id="ship_to_address_3" class="form-control" placeholder="Patokan / Keterangan Lain (Opsional)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kota <span class="text-danger">*</span></label>
                            <input type="text" name="ship_to_city" id="ship_to_city" class="form-control" placeholder="Contoh: Jakarta Selatan" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Setup CSRF for all AJAX requests
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
            });

            // 3. Inisialisasi Select2
            $('#customer_id').select2({
                theme: 'bootstrap-5', // Menggunakan tema BS5 agar rapi
                dropdownParent: $('#modalForm'), // WAJIB ada agar input search bisa diklik di dalam modal
                placeholder: "-- Pilih Customer --",
                allowClear: true
            });

            $('#user_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                placeholder: "-- Pilih Sales --",
                allowClear: true
            });

            // Initialize DataTable
            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('customer-ship-tos.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'customer_code', name: 'customer.code' },
                    { data: 'ship_to_code', name: 'ship_to_code' },
                    { data: 'ship_to_name', name: 'ship_to_name' },
                    { data: 'ship_to_city', name: 'ship_to_city' },
                    { data: 'user_name', name: 'user.name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']] // Urutkan berdasarkan Customer secara default
            });

            // Proses Submit Modal (Create & Edit)
            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('customer-ship-tos.store') }}";
                let method = "POST";

                if(id) {
                    url = "{{ url('/customer-ship-tos') }}/" + id;
                    method = "PUT";
                }

                let formData = $(this).serialize();

                $('#btnSubmit').html('<span class="spinner-border spinner-border-sm"></span> Processing...').prop('disabled', true);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        $('#btnSubmit').html('Simpan').prop('disabled', false);
                        table.ajax.reload();
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(err) {
                        $('#btnSubmit').html('Simpan').prop('disabled', false);
                        Swal.fire('Error', 'Gagal menyimpan data. Pastikan semua field wajib terisi.', 'error');
                    }
                });
            });

            // Tampilkan Modal Edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');

                $.get("{{ url('/customer-ship-tos') }}/" + id, function(data) {
                    $('#mainForm')[0].reset();
                    $('#dataId').val(data.id);

                    // Isi form dengan data dari server dan trigger 'change' untuk update Select2
                    $('#customer_id').val(data.customer_id).trigger('change');
                    $('#user_id').val(data.user_id).trigger('change');

                    $('#ship_to_code').val(data.ship_to_code);
                    $('#ship_to_name').val(data.ship_to_name);
                    $('#ship_to_address_1').val(data.ship_to_address_1);
                    $('#ship_to_address_2').val(data.ship_to_address_2);
                    $('#ship_to_address_3').val(data.ship_to_address_3);
                    $('#ship_to_city').val(data.ship_to_city);

                    $('#modalTitle').text('Edit Customer Ship To');
                    $('#modalForm').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Data tidak ditemukan.', 'error');
                });
            });

            // Hapus Data
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin Hapus Data?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/customer-ship-tos') }}/" + id,
                            method: "DELETE",
                            success: function(res) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', res.message, 'success');
                            },
                            error: function(err) {
                                Swal.fire('Error', 'Gagal menghapus data.', 'error');
                            }
                        });
                    }
                });
            });
        });

        // Fungsi untuk membuka Modal Create
        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');

            // 4. Reset nilai Select2 saat Tambah Data
            $('#customer_id').val('').trigger('change');
            $('#user_id').val('').trigger('change');

            $('#modalTitle').text('Tambah Customer Ship To');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
