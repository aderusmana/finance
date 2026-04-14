<x-app-layout>
    @section('title', 'Master Logistic Fee')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Fee</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Logistic Fee</a>
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
                            <i class="ph-bold ph-plus"></i> Add Logistic Fee
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Distributor Code</th>
                                    <th>Distributor Name</th>
                                    <th>Customer Code</th>
                                    <th>Customer Name</th>
                                    <th class="bg-warning text-dark">Logistic Fee / ctn</th>
                                    <th>Route To</th> <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL LOGISTIC FEE --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Logistic Fee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">

                    <div class="modal-body">
                        {{-- Wrapper untuk Form Tambah (Dropdown) --}}
                        <div id="createModeWrapper">
                            <div class="mb-3">
                                <label class="form-label">Distributor <span class="text-danger">*</span></label>
                                <select name="distributor_id" id="distributor_id" class="form-select">
                                    <option value="">-- Pilih Distributor --</option>
                                    @foreach($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->code }} - {{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select">
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $customer)
                                        {{-- Sesuaikan properti code customer jika namanya berbeda --}}
                                        <option value="{{ $customer->id }}">{{ $customer->customer_code ?? $customer->code ?? '-' }} - {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Wrapper untuk Form Edit (Readonly) --}}
                        <div id="editModeWrapper" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label text-muted">Distributor</label>
                                <input type="text" id="distributor_info" class="form-control bg-light" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Customer</label>
                                <input type="text" id="customer_info" class="form-control bg-light" readonly>
                            </div>
                        </div>

                        {{-- Input Harga (Ditampilkan di kedua mode) --}}
                        <div class="mb-3">
                            <label class="form-label">Logistic Fee / ctn <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="logistic_fee" id="logistic_fee" class="form-control" required>
                            </div>
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

        // --- FUNGSI FORMAT RUPIAH ---
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return rupiah;
        }

        $(document).ready(function() {
            // Inisialisasi DataTables
            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('logistic-fees.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'distributor_code', name: 'distributor.code' },
                    { data: 'distributor_name', name: 'distributor.name' },
                    { data: 'customer_code', name: 'customer.customer_code' },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'logistic_fee', name: 'logistic_fee' },
                    { data: 'route_to', name: 'route_to' }, // Tambahkan ini
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'asc']]
            });

            // Event listener untuk memformat input menjadi Rupiah saat diketik
            $('#logistic_fee').on('keyup', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            // Load Customer by Distributor (AJAX Dropdown)
            $('#distributor_id').on('change', function() {
                let distributorId = $(this).val();
                let customerSelect = $('#customer_id');

                customerSelect.empty().append('<option value="">-- Loading... --</option>');

                if(distributorId) {
                    $.get("{{ url('/get-customers-by-distributor') }}/" + distributorId, function(data) {
                        customerSelect.empty().append('<option value="">-- Pilih Customer --</option>');
                        $.each(data, function(key, customer) {
                            let kode = customer.customer_code ? customer.customer_code : customer.code;
                            customerSelect.append('<option value="'+ customer.id +'">'+ kode +' - '+ customer.name +'</option>');
                        });
                    });
                } else {
                    customerSelect.empty().append('<option value="">-- Pilih Customer --</option>');
                }
            });

            // Proses Submit Modal
            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('logistic-fees.store') }}";
                let method = "POST";

                if(id) {
                    url = "{{ url('/logistic-fees') }}/" + id;
                    method = "PUT";
                }

                // CLEANING DATA: Hapus titik sebelum dikirim ke backend
                // Menggunakan serializeArray agar mudah dimanipulasi
                let rawData = $(this).serializeArray();
                $.each(rawData, function(i, field){
                    if (field.name === 'logistic_fee') {
                        field.value = field.value.replace(/\./g, ''); // Hapus semua titik
                    }
                });
                let formData = $.param(rawData); // Ubah kembali ke format URL encoded

                $.ajax({
                    url: url,
                    method: method,
                    data: formData, // Kirim data yang sudah dibersihkan
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Gagal menyimpan data.', 'error');
                    }
                });
            });

            // Menampilkan Modal Edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');

                $.get("{{ url('/logistic-fees') }}/" + id, function(data) {
                    $('#mainForm')[0].reset();

                    $('#dataId').val(data.id);
                    $('#distributor_info').val(data.distributor_info);
                    $('#customer_info').val(data.customer_info);

                    // Set nilai harga dan langsung format ke Rupiah
                    $('#logistic_fee').val(formatRupiah(data.logistic_fee.toString()));

                    $('#createModeWrapper').hide();
                    $('#distributor_id').prop('required', false);
                    $('#customer_id').prop('required', false);

                    $('#editModeWrapper').show();
                    $('#modalTitle').text('Edit Harga Logistic Fee');
                    $('#modalForm').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Data tidak ditemukan.', 'error');
                });
            });
        });

        // Fungsi untuk membuka Modal Create
        function openModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');

            $('#createModeWrapper').show();
            $('#distributor_id').prop('required', true);
            $('#customer_id').prop('required', true);

            $('#editModeWrapper').hide();
            $('#modalTitle').text('Tambah Logistic Fee');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
