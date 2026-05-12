<x-app-layout>
    @section('title', 'Master Logistic Fee')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

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
                                    <th>Route To</th>
                                    <th>Action</th>
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
                    <input type="hidden" id="old_logistic_fee" value="0">

                    <div class="modal-body">
                        {{-- Wrapper untuk Form Tambah (Dropdown) --}}
                        <div id="createModeWrapper">
                            <div class="mb-3">
                                <label class="form-label">Distributor <span class="text-danger">*</span></label>
                                <select name="distributor_id" id="distributor_id" class="form-select select2-custom"
                                    style="width: 100%;">
                                    <option value="">-- Pilih Distributor --</option>
                                    @foreach ($distributors as $distributor)
                                        <option value="{{ $distributor->id }}">{{ $distributor->code }} -
                                            {{ $distributor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select name="customer_id" id="customer_id" class="form-select select2-custom"
                                    style="width: 100%;">
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->customer_code ?? ($customer->code ?? '-') }} -
                                            {{ $customer->name }}</option>
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

                            <div class="mb-4 pb-3 border-bottom">
                                <label class="form-label text-muted fw-bold">Harga Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted">Rp</span>
                                    <input type="text" id="current_logistic_fee"
                                        class="form-control bg-light text-secondary fw-bold" readonly>
                                </div>
                                <div class="form-text"><i class="ph-fill ph-info"></i> Ini adalah harga yang sedang
                                    aktif di sistem.</div>
                            </div>
                        </div>

                        {{-- Input Harga Baru (Dinamis: Create / Edit) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold" id="label_logistic_fee">Logistic Fee / ctn <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">Rp</span>
                                <input type="text" name="logistic_fee" id="logistic_fee"
                                    class="form-control text-primary fw-bold" required placeholder="Ketik nominal...">
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
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            let table;
        let originalCustomerOptionsHtml = null;

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

            // Helper function to refresh Select2 after changing options
            function refreshCustomerSelect2() {
                let customerSelect = $('#customer_id');
                customerSelect.select2('destroy').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    placeholder: "-- Pilih Customer --",
                    allowClear: true
                });
            }

            $(document).ready(function() {
                // Setup CSRF
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // 3. INISIALISASI SELECT2
                $('#distributor_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'), // Wajib ada agar search bisa diklik di dalam modal
                    placeholder: "-- Pilih Distributor --",
                    allowClear: true
                });

                $('#customer_id').select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#modalForm'),
                    placeholder: "-- Pilih Customer --",
                    allowClear: true
                });

                // Simpan semua opsi customer dari server-render sebagai fallback
                originalCustomerOptionsHtml = $('#customer_id').html();

                // Inisialisasi DataTables
                table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('logistic-fees.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'distributor_code',
                            name: 'distributor.code'
                        },
                        {
                            data: 'distributor_name',
                            name: 'distributor.name'
                        },
                        {
                            data: 'customer_code',
                            name: 'customer.code'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer.name'
                        },
                        {
                            data: 'logistic_fee',
                            name: 'logistic_fee'
                        },
                        {
                            data: 'route_to',
                            name: 'route_to'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [2, 'asc']
                    ]
                });

                // Format input menjadi Rupiah
                $('#logistic_fee').on('keyup', function() {
                    $(this).val(formatRupiah($(this).val()));
                });

                // 4. Load Customer by Distributor (AJAX Dropdown) + SELECT2 REFRESH
                $('#distributor_id').on('change', function() {
                    let distributorId = $(this).val();
                    let customerSelect = $('#customer_id');

                    // Reset pilihan sebelumnya
                    customerSelect.val(null).trigger('change');

                    // Kalau distributor belum dipilih, tampilkan semua customer (fallback)
                    if (!distributorId) {
                        if (originalCustomerOptionsHtml !== null) {
                            customerSelect.html(originalCustomerOptionsHtml);
                            refreshCustomerSelect2();
                        }
                        return;
                    }

                    // Set ke loading dan paksa Select2 merender ulang
                    customerSelect
                        .empty()
                        .append('<option value="">-- Loading... --</option>');
                    refreshCustomerSelect2();

                    $.get("{{ url('/get-customers-by-distributor') }}/" + distributorId)
                        .done(function(data) {
                            // Jika endpoint mengembalikan kosong (belum ada relasi), pakai fallback semua customer
                            if (!Array.isArray(data) || data.length === 0) {
                                if (originalCustomerOptionsHtml !== null) {
                                    customerSelect.html(originalCustomerOptionsHtml);
                                    refreshCustomerSelect2();
                                } else {
                                    customerSelect
                                        .empty()
                                        .append('<option value="">-- Pilih Customer --</option>');
                                    refreshCustomerSelect2();
                                }
                                return;
                            }

                            customerSelect
                                .empty()
                                .append('<option value="">-- Pilih Customer --</option>');

                            $.each(data, function(key, customer) {
                                let kode = customer.customer_code ? customer.customer_code : customer.code;
                                customerSelect.append(
                                    '<option value="' + customer.id + '">' + kode + ' - ' + customer.name + '</option>'
                                );
                            });

                            refreshCustomerSelect2();
                        })
                        .fail(function() {
                            // Jika request gagal (auth/500/404), jangan biarkan dropdown kosong
                            if (originalCustomerOptionsHtml !== null) {
                                customerSelect.html(originalCustomerOptionsHtml);
                                refreshCustomerSelect2();
                            } else {
                                customerSelect
                                    .empty()
                                    .append('<option value="">-- Pilih Customer --</option>');
                                refreshCustomerSelect2();
                            }
                        });
                });

                // Proses Submit Modal
                $('#mainForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#dataId').val();
                    let url = id ? "{{ url('/logistic-fees') }}/" + id : "{{ route('logistic-fees.store') }}";
                    let method = id ? "PUT" : "POST";

                    // Ambil teks Distributor & Customer berdasarkan mode (Edit atau Create)
                    let distributorName, customerName;
                    if (id) {
                        // Mode Edit: ambil dari input text readonly
                        distributorName = $('#distributor_info').val();
                        customerName = $('#customer_info').val();
                    } else {
                        // Mode Create: ambil dari select option yang dipilih
                        distributorName = $('#distributor_id option:selected').text();
                        customerName = $('#customer_id option:selected').text();
                    }

                    // Ambil nominal Harga Baru dan Harga Lama
                    let newFeeVal = $('#logistic_fee').val();
                    let oldFeeVal = formatRupiah($('#old_logistic_fee').val().toString());

                    // Row Harga Lama: Hanya dimunculkan kalau ini adalah Mode Edit (bukan data baru)
                    let oldFeeRow = id ?
                        `<tr><td class="text-muted">Harga Saat Ini</td><td>: <span class="text-secondary text-decoration-line-through">Rp ${oldFeeVal}</span></td></tr>` :
                        '';

                    // Dialog Konfirmasi (Dinamis untuk Create/Update)
                    Swal.fire({
                        title: id ? 'Konfirmasi Perubahan Harga' : 'Konfirmasi Pengajuan Baru',
                        html: `
                        <div class="text-start" style="font-size: 0.95rem;">
                            <p>Anda akan memproses data berikut:</p>
                            <table class="table table-sm table-borderless mb-2">
                                <tr><td width="35%">Distributor</td><td>: <b>${distributorName}</b></td></tr>
                                <tr><td>Customer</td><td>: <b>${customerName}</b></td></tr>
                                ${oldFeeRow}
                                <tr><td>Harga Diajukan</td><td>: <b class="text-primary fs-6">Rp ${newFeeVal}</b></td></tr>
                            </table>
                            <hr class="my-2">
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; font-style: italic;">
                                <i class="ph-bold ph-info-circle text-primary"></i>
                                Sistem akan mengirimkan notifikasi <b>Approval kepada Atasan</b> untuk diverifikasi.
                            </p>
                        </div>
                    `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Kirim Pengajuan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Bersihkan format rupiah sebelum dikirim ke database
                            let rawData = $(this).serializeArray();
                            $.each(rawData, function(i, field) {
                                if (field.name === 'logistic_fee') {
                                    field.value = field.value.replace(/\./g, '');
                                }
                            });
                            let formData = $.param(rawData);

                            // Loading state
                            Swal.fire({
                                title: 'Sedang Mengirim...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: url,
                                method: method,
                                data: formData,
                                success: function(res) {
                                    $('#modalForm').modal('hide');
                                    table.ajax.reload();

                                    // Dialog Berhasil
                                    Swal.fire({
                                        title: 'Pengajuan Terkirim!',
                                        html: res.message,
                                        icon: 'success',
                                        confirmButtonColor: '#10b981'
                                    });
                                },
                                error: function(err) {
                                    Swal.fire('Gagal',
                                        'Terjadi kesalahan sistem, silakan hubungi admin.',
                                        'error');
                                }
                            });
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

                        $('#old_logistic_fee').val(data.logistic_fee);
                        $('#current_logistic_fee').val(formatRupiah(data.logistic_fee.toString()));

                        $('#logistic_fee').val('');
                        $('#label_logistic_fee').html(
                            'Harga Baru / Diajukan <span class="text-danger">*</span>');

                        $('#createModeWrapper').hide();
                        $('#distributor_id').prop('required', false);
                        $('#customer_id').prop('required', false);

                        $('#editModeWrapper').show();
                        $('#modalTitle').text('Pengajuan Perubahan Harga');
                        $('#modalForm').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Data tidak ditemukan.', 'error');
                    });
                });
            });

            // 5. Reset Select2 saat membuka Modal Create
            function openModal() {
                $('#mainForm')[0].reset();
                $('#dataId').val('');
                $('#old_logistic_fee').val(0);

                // Reset Select2 ke pilihan kosong
                $('#distributor_id').val('').trigger('change');

                // Kembalikan opsi customer ke kondisi awal agar tidak kosong
                if (originalCustomerOptionsHtml !== null) {
                    $('#customer_id').html(originalCustomerOptionsHtml);
                    refreshCustomerSelect2();
                } else {
                    $('#customer_id').empty().append('<option value="">-- Pilih Customer --</option>');
                    refreshCustomerSelect2();
                }

                $('#label_logistic_fee').html('Logistic Fee / ctn <span class="text-danger">*</span>');

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
