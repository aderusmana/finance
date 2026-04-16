<x-app-layout>
    @section('title', 'Logistic Orders')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Orders</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph ph-shopping-cart f-s-16"></i> Transaction</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Logistic Orders</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-primary" onclick="openModal()">
                            <i class="ph-bold ph-plus"></i> Create Logistic Order
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th>Distributor</th>
                                    <th>Ship To</th>
                                    <th>Status (Download)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREATE LOGISTIC ORDER --}}
    <div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">

            {{-- 1. Ganti div modal-content menjadi tag form --}}
            <form id="mainForm" class="modal-content border-0 rounded-4 shadow">

                <div class="modal-header bg-primary text-white pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle">Create Logistic Order</h5>
                        <small class="text-white-50">Silakan pilih Customer terlebih dahulu.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- 2. Letakkan input hidden tepat di bawah header --}}
                @csrf
                <input type="hidden" name="period" id="hidden_period">
                <input type="hidden" name="delivery_to" id="hidden_delivery_to">

                <div class="modal-body p-4">
                    {{-- BARIS ATAS: Informasi Order & Customer Ship To (Dibagi 2 Kolom) --}}
                    <div class="row g-4">
                        {{-- KIRI: Informasi Order --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ph-bold ph-info"></i> Informasi Order</h6>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                                        <select name="customer_id" id="customer_id" class="form-select select2-custom" style="width: 100%;" required>
                                            <option value="">-- Pilih Customer --</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->customer_code ?? $c->code ?? '-' }} - {{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Distributor <span class="text-danger">*</span></label>
                                        <select name="distributor_id" id="distributor_id" class="form-select select2-custom" style="width: 100%;" disabled required>
                                            <option value="">-- Pilih Distributor --</option>
                                        </select>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Delivery Date <span class="text-danger">*</span></label>
                                        <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KANAN: Customer Ship To --}}
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm rounded-3 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ph-bold ph-map-pin"></i> Customer Ship To</h6>

                                    <div class="mb-3">
                                        <select name="customer_ship_to_id" id="customer_ship_to_id" class="form-select select2-custom" style="width: 100%;" disabled required>
                                            <option value="">-- Pilih Alamat Pengiriman --</option>
                                        </select>
                                        <input type="hidden" name="ship_to_code_header" id="ship_to_code_header">
                                    </div>

                                    <div id="shipToDetailBox" class="bg-white border rounded-3 p-3 shadow-sm" style="display: none; border-left: 4px solid #3b82f6 !important;">
                                        <div class="row mb-2">
                                            <div class="col-5">
                                                <span class="text-muted small fw-bold d-block">Ship To Code:</span>
                                                <span class="text-dark fw-bold" id="txt_ship_code">-</span>
                                            </div>
                                            <div class="col-7">
                                                <span class="text-muted small fw-bold d-block">Ship To Name:</span>
                                                <span class="text-dark fw-bold" id="txt_ship_name">-</span>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-12">
                                                <span class="text-muted small fw-bold d-block">Alamat Lengkap:</span>
                                                <span class="text-dark small d-block" id="txt_address_1">-</span>
                                                <span class="text-dark small d-block" id="txt_address_2">-</span>
                                                <span class="text-dark small d-block" id="txt_address_3">-</span>
                                            </div>
                                        </div>

                                        <div class="row border-top pt-2">
                                            <div class="col-5">
                                                <span class="text-muted small fw-bold d-block">Kota:</span>
                                                <span class="text-dark small fw-semibold" id="txt_city">-</span>
                                            </div>
                                            <div class="col-7">
                                                <span class="text-muted small fw-bold d-block">Sales PIC:</span>
                                                <span class="text-dark small" id="txt_sales_name">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BARIS BAWAH: Order Items (Full Width) --}}
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <h6 class="fw-bold text-primary mb-0"><i class="ph-bold ph-package"></i> Order Items</h6>
                                            <span class="badge bg-warning text-dark py-2 px-3 border rounded-3">
                                                Logistic Fee: <b id="active_fee_display">Rp 0 / ctn</b>
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                            <i class="ph-bold ph-plus"></i> Tambah Manual
                                        </button>
                                    </div>

                                    {{-- Max-height disesuaikan agar tidak terlalu panjang ke bawah --}}
                                    <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                        <table class="table table-bordered table-hover align-middle mb-0" id="itemTable">
                                            <thead class="table-light position-sticky top-0 shadow-sm" style="z-index: 10;">
                                                <tr>
                                                    <th width="20%">Item Code</th>
                                                    <th width="35%">Item Name <span class="text-danger">*</span></th>
                                                    <th width="15%">Qty <span class="text-danger">*</span></th>
                                                    <th width="20%">Amount</th>
                                                    <th width="10%" class="text-center"><i class="ph-bold ph-gear"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer untuk memuat item otomatis...</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top-0 pt-0 pb-3 pe-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5" id="btnSubmit">Submit Order</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let table;
        let activeLogisticFee = 0;
        let cachedShipTos = []; // Simpan data alamat untuk ditampikan di card

        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah ? 'Rp ' + rupiah : 'Rp 0';
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // Setup DataTables
            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('logistic-orders.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'logistic_order_no', name: 'logistic_order_no' },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'distributor_name', name: 'distributor.name' },
                    { data: 'ship_to', name: 'customerShipTo.ship_to_name' },
                    { data: 'status_badge', name: 'note.status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('.select2-custom').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                allowClear: true
            });

            // 1. CUSTOMER DIPILIH: Tarik Distributor, ShipTo, dan Item
            $('#customer_id').on('change', function() {
                let custId = $(this).val();

                let distSelect = $('#distributor_id');
                let shipSelect = $('#customer_ship_to_id');

                distSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled', true).trigger('change');
                shipSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled', true).trigger('change');
                $('#itemTable tbody').empty();
                $('#shipToDetailBox').hide();
                activeLogisticFee = 0;
                $('#active_fee_display').text('Rp 0 / ctn');

                if(custId) {
                    $.get("{{ url('/logistic-orders/customer-dependencies') }}/" + custId, function(res) {

                        // Populate Distributor
                        distSelect.empty().append('<option value="">-- Pilih Distributor --</option>').prop('disabled', false);
                        $.each(res.distributors, function(key, d) {
                            distSelect.append('<option value="'+ d.id +'">'+ d.code +' - '+ d.name +'</option>');
                        });
                        distSelect.trigger('change');

                        // Populate Ship To
                        cachedShipTos = res.ship_to_list;
                        shipSelect.empty().append('<option value="">-- Pilih Alamat Pengiriman --</option>').prop('disabled', false);
                        $.each(res.ship_to_list, function(key, s) {
                            shipSelect.append('<option value="'+ s.id +'" data-code="'+ s.ship_to_code +'">'+ s.ship_to_code +' - '+ s.ship_to_name +'</option>');
                        });
                        shipSelect.trigger('change');

                        // Auto Generate Items ke Tabel (Termasuk Quantity)
                        if(res.items && res.items.length > 0) {
                            $.each(res.items, function(key, item) {
                                addRow(item.item_code || '', item.item_name, item.quantity || 0);
                            });
                        } else {
                            addRow();
                        }
                    });
                } else {
                    $('#itemTable tbody').html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer untuk memuat item otomatis...</td></tr>');
                }
            });

            // 2. DISTRIBUTOR DIPILIH: Tarik Harga Logistic Fee
            $('#distributor_id').on('change', function() {
                let distId = $(this).val();
                let custId = $('#customer_id').val();

                if(distId && custId) {
                    $.get("{{ url('/logistic-orders/fee') }}/" + distId + "/" + custId, function(res) {
                        activeLogisticFee = res.logistic_fee;
                        $('#active_fee_display').text(formatRupiah(activeLogisticFee) + ' / ctn');

                        // Recalculate semua baris
                        $('.qty-input').trigger('input');
                    });
                }
            });

            // 3. SHIP TO DIPILIH: Tampilkan Detail ke Card
            $('#customer_ship_to_id').on('change', function() {
                let id = $(this).val();
                let code = $(this).find(':selected').data('code');
                $('#ship_to_code_header').val(code);

                if(id) {
                    let st = cachedShipTos.find(x => x.id == id);
                    if(st) {
                        // Data Ship To Code & Name
                        $('#txt_ship_code').text(st.ship_to_code || '-');
                        $('#txt_ship_name').text(st.ship_to_name || '-');

                        // Data Alamat Dinamis (Sembunyikan jika kosong)
                        $('#txt_address_1').text(st.ship_to_address_1 || '-');

                        if(st.ship_to_address_2) { $('#txt_address_2').text(st.ship_to_address_2).show(); } else { $('#txt_address_2').hide(); }
                        if(st.ship_to_address_3) { $('#txt_address_3').text(st.ship_to_address_3).show(); } else { $('#txt_address_3').hide(); }

                        // Data Kota (Hilangkan string "Kota: ")
                        $('#txt_city').text(st.ship_to_city || '-');

                        // Data Sales
                        let salesName = st.user ? st.user.name : '-';
                        let salesNik = st.user ? st.user.nik : '-';
                        $('#txt_sales_name').text(salesNik + ' - ' + salesName);

                        $('#shipToDetailBox').slideDown();
                    }
                } else {
                    $('#shipToDetailBox').slideUp();
                }
            });

            // 4. SUBMIT FORM: Dialog Detail Summary
            $('#mainForm').on('submit', function(e){
                e.preventDefault();

                // Ambil text untuk ditampikan di swal
                let customerText = $('#customer_id option:selected').text();
                let distText = $('#distributor_id option:selected').text();
                let shipText = $('#customer_ship_to_id option:selected').text();
                let delDate = $('#delivery_date').val();

                // Hitung total item yang diisi qty-nya
                let totalItems = 0;
                $('.qty-input').each(function() {
                    if($(this).val() > 0) totalItems++;
                });

                if(totalItems === 0) {
                    Swal.fire('Peringatan', 'Minimal harus ada 1 item dengan Quantity lebih dari 0.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Pengajuan Order',
                    html: `
                        <div class="text-start" style="font-size: 0.95rem;">
                            <p>Mohon periksa kembali detail pesanan Anda:</p>
                            <table class="table table-sm table-borderless rounded">
                                <tr><td width="35%" class="text-muted">Customer</td><td>: <b>${customerText}</b></td></tr>
                                <tr><td class="text-muted">Distributor</td><td>: <b>${distText}</b></td></tr>
                                <tr><td class="text-muted">Ship To</td><td>: <b>${shipText}</b></td></tr>
                                <tr><td class="text-muted">Delivery Date</td><td>: <b>${delDate}</b></td></tr>
                                <tr><td class="text-muted">Total Item</td><td>: <b class="text-primary">${totalItems} Macam Barang</b></td></tr>
                            </table>
                            <p class="mb-0 mt-3 text-muted small italic">
                                <i class="ph-bold ph-info"></i> Data akan otomatis men-generate Logistic Order Number dan dikirim ke Atasan untuk Approval.
                            </p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonText: 'Cek Kembali',
                    confirmButtonText: 'Ya, Submit Sekarang',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Menyimpan Order...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});

                        $.ajax({
                            url: "{{ route('logistic-orders.store') }}",
                            method: "POST",
                            data: $(this).serialize(),
                            success: function(res) {
                                $('#modalForm').modal('hide');
                                table.ajax.reload();
                                Swal.fire('Berhasil!', res.message, 'success');
                            },
                            error: function(err) {
                                Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            });
        });

        // --- FUNGSI DINAMIS TABEL ITEM ---
        function addRow(code = '', name = '', qty = '') {
            $('#emptyRow').remove();
            let index = Date.now() + Math.floor(Math.random() * 1000);

            // Hitung awal jika ada qty default (tarikan db)
            let initialTotal = (qty > 0) ? formatRupiah(qty * activeLogisticFee) : 'Rp 0';

            let row = `
                <tr>
                    <td>
                        <input type="text" name="items[${index}][item_code]" class="form-control form-control-sm" value="${code}" placeholder="Kode Item">
                    </td>
                    <td>
                        <input type="text" name="items[${index}][item_name]" class="form-control form-control-sm" value="${name}" placeholder="Nama Item" required>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input" value="${qty}" placeholder="0" min="0" oninput="calculateRow(this)">
                    </td>
                    <td>
                        <input type="text" name="items[${index}][amount]" class="form-control form-control-sm bg-light amount-display fw-bold text-primary" readonly value="${initialTotal}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)">
                            <i class="ph-bold ph-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemTable tbody').append(row);
        }

        function removeRow(btn) {
            $(btn).closest('tr').remove();
            if($('#itemTable tbody tr').length === 0) {
                $('#itemTable tbody').append('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Tabel kosong. Silakan tambah item manual.</td></tr>');
            }
        }

        function calculateRow(input) {
            let qty = parseFloat($(input).val()) || 0;
            let total = qty * activeLogisticFee;
            $(input).closest('tr').find('.amount-display').val(formatRupiah(total));
        }

        // --- FUNGSI BUKA MODAL CREATE (SET DEFAULT DATES) ---
        function openModal() {
            $('#mainForm')[0].reset();

            // Set Default Dates (Current Date & Period)
            let today = new Date().toISOString().split('T')[0];
            let currentMonth = new Date().toLocaleString('id-ID', { month: 'long', year: 'numeric' });

            $('#delivery_date').val(today);
            $('#hidden_delivery_to').val(today);
            $('#hidden_period').val(currentMonth);

            // Reset Dropdowns
            $('#customer_id').val('').trigger('change');
            $('#distributor_id').empty().append('<option value="">-- Pilih Distributor --</option>').prop('disabled', true).trigger('change');
            $('#customer_ship_to_id').empty().append('<option value="">-- Pilih Alamat Pengiriman --</option>').prop('disabled', true).trigger('change');
            $('#shipToDetailBox').hide();

            activeLogisticFee = 0;
            $('#active_fee_display').text('Rp 0 / ctn');
            $('#itemTable tbody').html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer terlebih dahulu untuk memuat item otomatis...</td></tr>');

            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
