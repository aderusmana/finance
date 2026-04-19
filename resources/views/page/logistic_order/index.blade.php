<x-app-layout>
    @section('title', 'Logistic Center')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Logistic Center</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph ph-shopping-cart f-s-16"></i> Transaction</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Logistic Center</a></li>
            </ul>
        </div>
    </div>

    {{-- 1. INFO BANNER --}}
    <div class="row m-1">
        <div class="col-12">
            <div class="info-banner">
                <div class="info-banner-icon">
                    <i class="ph-bold ph-printer"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Pusat Kelola Dokumen Logistik</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Kelola pesanan logistik baru dan pantau arsip Surat Jalan (Delivery Order) yang telah diunduh oleh Distributor melalui panel ini.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. TOGGLE MENU CARDS --}}
    <div class="row m-1 g-3 mb-4">
        {{-- Card: Logistic Orders (Pending) --}}
        <div class="col-md-6">
            <div class="toggle-card active" id="btn-tab-pending" onclick="switchTab('pending')">
                <div class="icon-box"><i class="ph-bold ph-file-text"></i></div>
                <div>
                    <h6 class="title">Logistic Orders</h6>
                    <p class="subtitle">Pesanan baru yang menunggu proses cetak DN.</p>
                </div>
            </div>
        </div>

        {{-- Card: Delivery Notes (Selesai) --}}
        <div class="col-md-6">
            <div class="toggle-card" id="btn-tab-downloaded" onclick="switchTab('downloaded')">
                <div class="icon-box"><i class="ph-bold ph-check-circle"></i></div>
                <div>
                    <h6 class="title">Delivery Notes</h6>
                    <p class="subtitle">Arsip Surat Jalan yang telah diunduh (Selesai).</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. AREA TABEL --}}
    <div class="row m-1">
        <div class="col-12">
            <div class="table-panel">

                {{-- Bagian Header Tabel (Dinamis) --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div id="table-title-area">
                        <h5 class="fw-bold text-dark mb-1" id="dynamic-table-title"><i class="ph-fill ph-stack text-primary me-2"></i> Daftar Logistic Order</h5>
                        <p class="text-muted mb-0 small" id="dynamic-table-subtitle">Menampilkan data pesanan dengan status Pending.</p>
                    </div>

                    {{-- Tombol Buat Order (Hanya tampil di tab Pending) --}}
                    <button class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm" id="btn-create-order" onclick="openModal()">
                        <i class="ph-bold ph-plus me-1"></i> Buat Order Baru
                    </button>
                </div>

                {{-- Tabel Pending --}}
                <div id="wrapper-pending" class="table-responsive">
                    <table class="table table-hover w-100 align-middle" id="sampleTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>Distributor</th>
                                <th>Ship To</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                {{-- Tabel Downloaded (Sembunyikan via d-none default) --}}
                <div id="wrapper-downloaded" class="table-responsive d-none">
                    <table class="table table-hover w-100 align-middle" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>DN Number</th>
                                <th>Customer</th>
                                <th>Distributor</th>
                                <th>Tujuan Pengiriman</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL CREATE LOGISTIC ORDER --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="mainForm" class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header bg-primary text-white pb-3">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="modalTitle">Create Logistic Order</h5>
                        <small class="text-white-50">Silakan pilih Customer terlebih dahulu.</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                @csrf
                <input type="hidden" name="period" id="hidden_period">
                <input type="hidden" name="delivery_to" id="hidden_delivery_to">
                <div class="modal-body p-4">
                    <div class="row g-4">
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
                                            <div class="col-5"><span class="text-muted small fw-bold d-block">Ship To Code:</span><span class="text-dark fw-bold" id="txt_ship_code">-</span></div>
                                            <div class="col-7"><span class="text-muted small fw-bold d-block">Ship To Name:</span><span class="text-dark fw-bold" id="txt_ship_name">-</span></div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-12"><span class="text-muted small fw-bold d-block">Alamat Lengkap:</span><span class="text-dark small d-block" id="txt_address_1">-</span><span class="text-dark small d-block" id="txt_address_2">-</span><span class="text-dark small d-block" id="txt_address_3">-</span></div>
                                        </div>
                                        <div class="row border-top pt-2">
                                            <div class="col-5"><span class="text-muted small fw-bold d-block">Kota:</span><span class="text-dark small fw-semibold" id="txt_city">-</span></div>
                                            <div class="col-7"><span class="text-muted small fw-bold d-block">Sales PIC:</span><span class="text-dark small" id="txt_sales_name">-</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center gap-3">
                                            <h6 class="fw-bold text-primary mb-0"><i class="ph-bold ph-package"></i> Order Items</h6>
                                            <span class="badge bg-warning text-dark py-2 px-3 border rounded-3">Logistic Fee: <b id="active_fee_display">Rp 0 / ctn</b></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()"><i class="ph-bold ph-plus"></i> Tambah Manual</button>
                                    </div>
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

    {{-- ================================================================= --}}
    {{-- MODAL DETAIL PESANAN  --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        {{-- Mengubah modal-xl menjadi modal-lg agar lebarnya lebih pas & proporsional --}}
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                
                {{-- Header --}}
                <div class="modal-header border-bottom-0 p-4">
                    <div class="d-flex align-items-center gap-3">
                        {{-- Kotak Ikon Transparan (Glassmorphism) --}}
                        <div class="bg-white bg-opacity-25 text-white border border-white border-opacity-25 d-flex align-items-center justify-content-center rounded-3 shadow-sm" style="width: 50px; height: 50px; backdrop-filter: blur(4px);">
                            <i class="ph-bold ph-receipt fs-3"></i>
                        </div>
                        <div>
                            {{-- Judul diubah ke Putih --}}
                            <h5 class="fw-bolder text-white mb-1">Detail Pesanan Logistik</h5>
                            <div class="d-flex align-items-center gap-2">
                                {{-- Badge Nomor Order dibalik warnanya (Bg Putih, Teks Biru) --}}
                                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold shadow-sm" id="detail_lo_no" style="letter-spacing: 0.5px;">LO-0000</span>
                                <span id="detail_status"></span>
                            </div>
                        </div>
                    </div>
                    {{-- Tombol Close diubah jadi putih (btn-close-white) --}}
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    
                    {{-- Row 1: Info Dokumen & Tujuan --}}
                    <div class="row g-4 mb-4">
                        {{-- Kiri: Info Dokumen --}}
                        <div class="col-md-6">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm">
                                <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: 0.85rem; letter-spacing: 1px;">
                                    <i class="ph-bold ph-info me-2"></i>Informasi Dokumen
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td width="40%" class="text-muted pb-2">Delivery No</td>
                                        <td class="fw-bold text-dark pb-2">: <span id="detail_do_no">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Tgl. Kirim</td>
                                        <td class="fw-semibold text-dark pb-2">: <span id="detail_delivery_date">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-2">Customer</td>
                                        <td class="fw-bold text-primary pb-2">: <span id="detail_customer">-</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted pb-0">Distributor</td>
                                        <td class="fw-semibold text-dark pb-0">: <span id="detail_distributor">-</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        {{-- Kanan: Tujuan Pengiriman --}}
                        <div class="col-md-6">
                            <div class="bg-white border rounded-4 p-4 h-100 shadow-sm" style="border-top: 4px solid #3b82f6 !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-primary text-uppercase mb-0" style="font-size: 0.85rem; letter-spacing: 1px;">
                                        <i class="ph-bold ph-map-pin me-2"></i>Tujuan Pengiriman
                                    </h6>
                                    <span class="badge bg-light text-primary border border-primary border-opacity-25 px-2 py-1 rounded" id="detail_ship_to_code">-</span>
                                </div>
                                <div class="mt-3">
                                    <h6 class="fw-bold text-dark mb-1 fs-6" id="detail_ship_to_name">-</h6>
                                    <p class="text-secondary mb-3" style="font-size: 0.9rem; line-height: 1.5;" id="detail_ship_to_address">-</p>
                                </div>
                                <div class="d-flex align-items-center gap-4 mt-auto border-top pt-3">
                                    <div>
                                        <span class="text-muted d-block fw-semibold" style="font-size: 0.75rem;">KOTA</span>
                                        <span class="fw-bold text-dark fs-6" id="detail_ship_to_city">-</span>
                                    </div>
                                    <div class="border-start ps-4">
                                        <span class="text-muted d-block fw-semibold" style="font-size: 0.75rem;">SALES PIC</span>
                                        <span class="fw-bold text-dark fs-6" id="detail_sales_pic">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Tabel Barang --}}
                    <div class="bg-white border rounded-4 p-4 shadow-sm">
                        <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: 0.85rem; letter-spacing: 1px;">
                            <i class="ph-bold ph-package me-2"></i>Rincian Barang
                        </h6>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center text-muted py-3">NO</th>
                                        <th width="20%" class="text-muted py-3">KODE ITEM</th>
                                        <th width="60%" class="text-muted py-3">NAMA / DESKRIPSI BARANG</th>
                                        <th width="15%" class="text-center text-muted py-3">QTY</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_items_table">
                                    {{-- Data Items di-inject via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                {{-- Footer --}}
                <div class="modal-footer border-top p-3 bg-white rounded-bottom-4">
                    <button type="button" class="btn btn-secondary px-5 py-2 rounded-pill fw-semibold shadow-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let sampleTable, historyTable;
        let activeLogisticFee = 0;
        let cachedShipTos = [];

        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
            return rupiah ? 'Rp ' + rupiah : 'Rp 0';
        }

        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // Inisialisasi DataTable 1: PENDING
            sampleTable = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('logistic-orders.index') }}", data: { tab: 'pending' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'logistic_order_no', name: 'logistic_order_no', className: 'fw-bold text-primary' },
                    { data: 'customer_name', name: 'customer.name', className: 'fw-semibold' },
                    { data: 'distributor_name', name: 'distributor.name' },
                    { data: 'ship_to', name: 'customerShipTo.ship_to_name' },
                    { data: 'status_badge', name: 'note.status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Inisialisasi DataTable 2: DOWNLOADED
            historyTable = $('#historyTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('logistic-orders.index') }}", data: { tab: 'downloaded' } },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'do_no', name: 'note.delivery_order_no', className: 'fw-bold text-success' },
                    { data: 'customer_name', name: 'customer.name', className: 'fw-semibold' },
                    { data: 'distributor_name', name: 'distributor.name' },
                    { data: 'ship_to', name: 'customerShipTo.ship_to_name' },
                    { data: 'status_badge', name: 'note.status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // Init Select2
            $('.select2-custom').select2({ theme: 'bootstrap-5', dropdownParent: $('#modalForm'), allowClear: true });

            // LOGIC FORM CREATE (Sama persis seperti sebelumnya)
            $('#customer_id').on('change', function() {
                let custId = $(this).val();
                let distSelect = $('#distributor_id'), shipSelect = $('#customer_ship_to_id');

                distSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled', true).trigger('change');
                shipSelect.empty().append('<option value="">-- Loading... --</option>').prop('disabled', true).trigger('change');
                $('#itemTable tbody').empty(); $('#shipToDetailBox').hide();
                activeLogisticFee = 0; $('#active_fee_display').text('Rp 0 / ctn');

                if(custId) {
                    $.get("{{ url('/logistic-orders/customer-dependencies') }}/" + custId, function(res) {
                        distSelect.empty().append('<option value="">-- Pilih Distributor --</option>').prop('disabled', false);
                        $.each(res.distributors, function(key, d) { distSelect.append('<option value="'+ d.id +'">'+ d.code +' - '+ d.name +'</option>'); });
                        distSelect.trigger('change');

                        cachedShipTos = res.ship_to_list;
                        shipSelect.empty().append('<option value="">-- Pilih Alamat Pengiriman --</option>').prop('disabled', false);
                        $.each(res.ship_to_list, function(key, s) { shipSelect.append('<option value="'+ s.id +'" data-code="'+ s.ship_to_code +'">'+ s.ship_to_code +' - '+ s.ship_to_name +'</option>'); });
                        shipSelect.trigger('change');

                        if(res.items && res.items.length > 0) {
                            $.each(res.items, function(key, item) { addRow(item.item_code || '', item.item_name, item.quantity || 0); });
                        } else { addRow(); }
                    });
                } else {
                    $('#itemTable tbody').html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer untuk memuat item otomatis...</td></tr>');
                }
            });

            $('#distributor_id').on('change', function() {
                let distId = $(this).val(), custId = $('#customer_id').val();
                if(distId && custId) {
                    $.get("{{ url('/logistic-orders/fee') }}/" + distId + "/" + custId, function(res) {
                        activeLogisticFee = res.logistic_fee;
                        $('#active_fee_display').text(formatRupiah(activeLogisticFee) + ' / ctn');
                        $('.qty-input').trigger('input');
                    });
                }
            });

            $('#customer_ship_to_id').on('change', function() {
                let id = $(this).val(), code = $(this).find(':selected').data('code');
                $('#ship_to_code_header').val(code);
                if(id) {
                    let st = cachedShipTos.find(x => x.id == id);
                    if(st) {
                        $('#txt_ship_code').text(st.ship_to_code || '-'); $('#txt_ship_name').text(st.ship_to_name || '-');
                        $('#txt_address_1').text(st.ship_to_address_1 || '-');
                        if(st.ship_to_address_2) { $('#txt_address_2').text(st.ship_to_address_2).show(); } else { $('#txt_address_2').hide(); }
                        if(st.ship_to_address_3) { $('#txt_address_3').text(st.ship_to_address_3).show(); } else { $('#txt_address_3').hide(); }
                        $('#txt_city').text(st.ship_to_city || '-');
                        $('#txt_sales_name').text((st.user ? st.user.nik : '-') + ' - ' + (st.user ? st.user.name : '-'));
                        $('#shipToDetailBox').slideDown();
                    }
                } else { $('#shipToDetailBox').slideUp(); }
            });

            $('#mainForm').on('submit', function(e){
                e.preventDefault();
                let totalItems = 0; $('.qty-input').each(function() { if($(this).val() > 0) totalItems++; });
                if(totalItems === 0) { Swal.fire('Peringatan', 'Minimal 1 item diisi.', 'warning'); return; }

                Swal.fire({
                    title: 'Konfirmasi Pengajuan Order',
                    html: `<div class="text-start">Pesanan ini akan men-generate nomor dokumen dan mengirim email notifikasi.</div>`,
                    icon: 'question', showCancelButton: true, confirmButtonText: 'Submit Sekarang', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Menyimpan Order...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                        $.ajax({
                            url: "{{ route('logistic-orders.store') }}", method: "POST", data: $(this).serialize(),
                            success: function(res) {
                                $('#modalForm').modal('hide');
                                sampleTable.ajax.reload(); // Refresh tabel pending
                                Swal.fire('Berhasil!', res.message, 'success');
                            },
                            error: function() { Swal.fire('Gagal', 'Terjadi kesalahan sistem.', 'error'); }
                        });
                    }
                });
            });

            // TOMBOL DETAIL DI-KLIK
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                Swal.fire({ title: 'Memuat...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
                $.get("{{ url('/logistic-orders') }}/" + id, function(data) {
                    Swal.close();
                    $('#detail_lo_no').text('LO-' + String(data.id).padStart(4, '0'));

                    if(data.note) {
                        $('#detail_do_no').text(data.note.delivery_order_no || '-');
                        let statusHtml = data.note.status === 'Downloaded'
                            ? `<span class="badge bg-success text-white border border-success px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-check-circle me-1"></i> Downloaded</span>`
                            : `<span class="badge bg-warning text-dark border border-warning px-3 py-1 rounded-pill fw-bold shadow-sm"><i class="ph-bold ph-clock me-1"></i> Pending</span>`;
                        $('#detail_status').html(statusHtml);
                    }

                    $('#detail_delivery_date').text(data.delivery_date || '-');
                    $('#detail_customer').text(data.customer ? data.customer.name : '-');
                    $('#detail_distributor').text(data.distributor ? data.distributor.name : '-');

                    if(data.customer_ship_to) {
                        let st = data.customer_ship_to;
                        $('#detail_ship_to_code').text(st.ship_to_code || '-');
                        $('#detail_ship_to_name').text(st.ship_to_name || '-');
                        $('#detail_ship_to_address').text(st.ship_to_address_1 + ' ' + (st.ship_to_address_2 || ''));
                        $('#detail_ship_to_city').text(st.ship_to_city || '-');
                        $('#detail_sales_pic').text(st.user ? st.user.name : '-');
                    }

                    // UPDATE: Tabel Data diperbesar padding dan font size-nya (fs-6, fs-5)
                    let tbody = $('#detail_items_table'); tbody.empty();
                    if(data.items && data.items.length > 0) {
                        $.each(data.items, function(index, item) {
                            tbody.append(`
                                <tr>
                                    <td class="text-center text-muted py-3">${index + 1}</td>
                                    <td class="py-3"><span class="badge bg-light text-secondary border px-2 py-1 fs-6 fw-normal">${item.order_item_code || '-'}</span></td>
                                    <td class="fw-semibold text-dark py-3 fs-6">${item.order_item_name}</td>
                                    <td class="text-center fw-bold text-primary py-3 fs-5">${item.order_quantity}</td>
                                </tr>
                            `);
                        });
                    } else {
                        tbody.append('<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada rincian barang.</td></tr>');
                    }
                    
                    $('#modalDetail').modal('show');
                });
            });
        });

        // --- FUNGSI GANTI TAB CARD ---
        function switchTab(tabName) {
            // Ubah Warna Card
            $('.toggle-card').removeClass('active');
            $('#btn-tab-' + tabName).addClass('active');

            if (tabName === 'pending') {
                // Tampilkan Tabel Pending
                $('#wrapper-downloaded').addClass('d-none');
                $('#wrapper-pending').removeClass('d-none');

                // Ubah Judul & Tombol
                $('#dynamic-table-title').html('<i class="ph-fill ph-stack text-primary me-2"></i> Daftar Logistic Order');
                $('#dynamic-table-subtitle').text('Menampilkan data pesanan dengan status Pending.');
                $('#btn-create-order').removeClass('d-none');

                // Refresh Data
                sampleTable.ajax.reload(null, false);
            } else {
                // Tampilkan Tabel Downloaded
                $('#wrapper-pending').addClass('d-none');
                $('#wrapper-downloaded').removeClass('d-none');

                // Ubah Judul & Sembunyikan Tombol Create
                $('#dynamic-table-title').html('<i class="ph-fill ph-check-circle text-success me-2"></i> Arsip Delivery Note');
                $('#dynamic-table-subtitle').text('Menampilkan Surat Jalan yang telah diunduh (Selesai).');
                $('#btn-create-order').addClass('d-none');

                // Refresh Data
                historyTable.ajax.reload(null, false);
            }
        }

        function addRow(code = '', name = '', qty = '') {
            $('#emptyRow').remove();
            let index = Date.now() + Math.floor(Math.random() * 1000);
            let initialTotal = (qty > 0) ? formatRupiah(qty * activeLogisticFee) : 'Rp 0';
            let row = `
                <tr>
                    <td><input type="text" name="items[${index}][item_code]" class="form-control form-control-sm" value="${code}" placeholder="Kode Item"></td>
                    <td><input type="text" name="items[${index}][item_name]" class="form-control form-control-sm" value="${name}" placeholder="Nama Item" required></td>
                    <td><input type="number" name="items[${index}][qty]" class="form-control form-control-sm qty-input" value="${qty}" placeholder="0" min="0" oninput="calculateRow(this)"></td>
                    <td><input type="text" name="items[${index}][amount]" class="form-control form-control-sm bg-light amount-display fw-bold text-primary" readonly value="${initialTotal}"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="ph-bold ph-trash"></i></button></td>
                </tr>
            `;
            $('#itemTable tbody').append(row);
        }
        function removeRow(btn) {
            $(btn).closest('tr').remove();
            if($('#itemTable tbody tr').length === 0) $('#itemTable tbody').append('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Tabel kosong.</td></tr>');
        }
        function calculateRow(input) {
            let qty = parseFloat($(input).val()) || 0;
            $(input).closest('tr').find('.amount-display').val(formatRupiah(qty * activeLogisticFee));
        }
        function openModal() {
            $('#mainForm')[0].reset();
            let today = new Date().toISOString().split('T')[0];
            let currentMonth = new Date().toLocaleString('id-ID', { month: 'long', year: 'numeric' });
            $('#delivery_date').val(today); $('#hidden_delivery_to').val(today); $('#hidden_period').val(currentMonth);
            $('#customer_id').val('').trigger('change');
            $('#distributor_id').empty().append('<option value="">-- Pilih Distributor --</option>').prop('disabled', true).trigger('change');
            $('#customer_ship_to_id').empty().append('<option value="">-- Pilih Alamat --</option>').prop('disabled', true).trigger('change');
            $('#shipToDetailBox').hide();
            activeLogisticFee = 0; $('#active_fee_display').text('Rp 0 / ctn');
            $('#itemTable tbody').html('<tr id="emptyRow"><td colspan="5" class="text-center text-muted py-5">Pilih Customer terlebih dahulu.</td></tr>');
            $('#modalForm').modal('show');
        }
    </script>
    @endpush
</x-app-layout>
