<x-app-layout>
    @section('title', 'Master Customer Item')
    @include('components.sample-table-styles')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        .item-row td {
            vertical-align: middle;
        }
        .btn-remove-row {
            color: #dc3545;
            transition: all 0.2s;
        }
        .btn-remove-row:hover {
            color: #a71d2a;
            background-color: #fee2e2;
        }
    </style>

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Customer Item</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Master Management
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customer Item</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <span class="text-muted small"><i class="ph-bold ph-shield-check text-success"></i> Setiap customer hanya memiliki 1 baris data di list (tidak ada duplikasi customer).</span>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="ph-bold ph-plus"></i> Tambah Customer Item
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Customer Code</th>
                                    <th>Customer Name</th>
                                    <th>Customer Sort Name</th>
                                    <th>Daftar Item</th>
                                    <th>Total Item</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CUSTOMER ITEM (CREATE & EDIT) --}}
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Customer Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="mainForm">
                    @csrf
                    <input type="hidden" name="id" id="dataId">

                    <div class="modal-body p-4">
                        {{-- Field Customer --}}
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                                
                                {{-- Select2 untuk mode Create --}}
                                <div id="customerSelectWrapper">
                                    <select name="customer_id" id="customer_id" class="form-select select2-custom" required>
                                        <option value="">-- Pilih Customer --</option>
                                    </select>
                                    <small class="text-muted d-block mt-1">
                                        <i class="ph-bold ph-info text-primary"></i> Hanya menampilkan customer yang belum ada di list (1 customer hanya bisa 1 data di list, tidak duplicate).
                                    </small>
                                </div>

                                {{-- Text readonly untuk mode Edit --}}
                                <div id="customerDisplayWrapper" style="display: none;">
                                    <input type="text" id="customer_display_name" class="form-control bg-light fw-bold" readonly>
                                    <input type="hidden" id="customer_id_edit" name="customer_id_edit">
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted">

                        {{-- Section Items Repeater --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark"><i class="ph-bold ph-package me-1 text-primary"></i> Item untuk Customer Ini</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddItemRow">
                                <i class="ph-bold ph-plus"></i> Tambah Item Lain
                            </button>
                        </div>

                        <div class="table-responsive border rounded bg-white">
                            <table class="table table-bordered mb-0 align-middle" id="itemsTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 22%;">Kode Item</th>
                                        <th style="width: 38%;">Nama Item <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Quantity</th>
                                        <th style="width: 20%;">Harga Satuan (Rp)</th>
                                        <th style="width: 5%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    {{-- Baris item dinamis akan dimuat di sini --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL CUSTOMER ITEM --}}
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="ph-bold ph-info me-2"></i>Detail Customer Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Customer</span>
                            <span id="detail_customer" class="fw-bold fs-6 text-dark"></span>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="text-muted d-block text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Total Terdaftar</span>
                            <span id="detail_total_items" class="badge bg-primary fs-6 px-3 py-2 rounded-pill"></span>
                        </div>
                    </div>
                    
                    <hr class="text-muted my-3">

                    <h6 class="fw-bold text-dark mb-2"><i class="ph-bold ph-list-dashes me-1 text-info"></i> Rincian Item</h6>
                    <div class="table-responsive border rounded">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th style="width: 20%;">Kode Item</th>
                                    <th style="width: 35%;">Nama Item</th>
                                    <th class="text-end" style="width: 15%;">Quantity</th>
                                    <th class="text-end" style="width: 25%;">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items_body">
                                {{-- Item rows loaded via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let table;
        let itemRowIndex = 0;

        $(document).ready(function() {
            // Setup CSRF header
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() }
            });

            // Inisialisasi Select2
            $('#customer_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalForm'),
                placeholder: "-- Pilih Customer --",
                allowClear: true
            });

            // Initialize DataTable
            table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: "{{ route('customer-items.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'customer_code', name: 'code' },
                    { data: 'customer_name', name: 'name' },
                    { data: 'customer_sort_name', name: 'sort_name' },
                    { data: 'items_preview', name: 'items_preview', orderable: false },
                    { data: 'total_items', name: 'total_items', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center text-nowrap' }
                ],
                columnDefs: [
                    { targets: 0, width: "4%", className: "text-center" },
                    { targets: 1, width: "10%" },
                    { targets: 2, width: "20%" },
                    { targets: 3, width: "14%" },
                    { targets: 4, width: "32%" },
                    { targets: 5, width: "8%", className: "text-center" },
                    { targets: 6, width: "12%", className: "text-center text-nowrap" }
                ],
                order: [[2, 'asc']]
            });

            // Tambah baris item baru
            $('#btnAddItemRow').on('click', function() {
                addItemRow();
            });

            // Hapus baris item
            $(document).on('click', '.btn-remove-row', function() {
                if ($('#itemsTableBody tr').length <= 1) {
                    Swal.fire('Perhatian', 'Minimal harus ada 1 item untuk customer ini.', 'warning');
                    return;
                }
                $(this).closest('tr').remove();
            });

            // Form Submit (Create & Edit)
            $('#mainForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#dataId').val();
                let url = "{{ route('customer-items.store') }}";
                let method = "POST";

                if (id) {
                    url = "{{ url('/customer-items') }}/" + id;
                    method = "PUT";
                }

                let formData = $(this).serialize();

                $('#btnSubmit').html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop('disabled', true);

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(res) {
                        $('#modalForm').modal('hide');
                        $('#btnSubmit').html('Simpan').prop('disabled', false);
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(err) {
                        $('#btnSubmit').html('Simpan').prop('disabled', false);
                        let msg = 'Gagal menyimpan data.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        if (err.responseJSON && err.responseJSON.errors) {
                            let errors = Object.values(err.responseJSON.errors).flat().join('<br>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: errors
                            });
                        } else {
                            Swal.fire('Error', msg, 'error');
                        }
                    }
                });
            });

            // Tombol Edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');

                $.get("{{ url('/customer-items') }}/" + id, function(customer) {
                    $('#mainForm')[0].reset();
                    $('#dataId').val(customer.id);

                    // Mode edit: Customer di-lock agar tidak tertukar/duplicate
                    $('#customerSelectWrapper').hide();
                    $('#customer_id').prop('required', false);
                    $('#customerDisplayWrapper').show();

                    let custCode = customer.code ? customer.code + ' - ' : '';
                    let custSort = customer.sort_name ? ' [' + customer.sort_name + ']' : '';
                    $('#customer_display_name').val(custCode + customer.name + custSort);
                    $('#customer_id_edit').val(customer.id);

                    // Kosongkan dan render baris item yang sudah ada
                    $('#itemsTableBody').empty();
                    itemRowIndex = 0;

                    if (customer.items && customer.items.length > 0) {
                        customer.items.forEach(function(item) {
                            addItemRow(item.item_code, item.item_name, item.quantity, item.price);
                        });
                    } else {
                        addItemRow();
                    }

                    $('#modalTitle').text('Edit Customer Item: ' + customer.name);
                    $('#modalForm').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Data customer item tidak ditemukan.', 'error');
                });
            });

            // Tombol Delete
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin Hapus Data?',
                    text: "Seluruh data item untuk customer ini akan dihapus dari list!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('/customer-items') }}/" + id,
                            method: "DELETE",
                            success: function(res) {
                                table.ajax.reload(null, false);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Gagal menghapus data.', 'error');
                            }
                        });
                    }
                });
            });

            // Tombol Detail
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');

                $.get("{{ url('/customer-items') }}/" + id, function(customer) {
                    let custCode = customer.code ? customer.code + ' - ' : '';
                    let custSort = customer.sort_name ? ' [' + customer.sort_name + ']' : '';
                    $('#detail_customer').text(custCode + customer.name + custSort);

                    let totalCount = customer.items ? customer.items.length : 0;
                    $('#detail_total_items').text(totalCount + ' Item' + (totalCount > 1 ? 's' : ''));

                    let tbody = $('#detail_items_body');
                    tbody.empty();

                    if (customer.items && customer.items.length > 0) {
                        customer.items.forEach(function(item, idx) {
                            let priceText = item.price ? 'Rp ' + Number(item.price).toLocaleString('id-ID') : '-';
                            let qtyText = item.quantity !== null ? Number(item.quantity).toLocaleString('id-ID') + ' pcs' : '0 pcs';

                            let row = `
                                <tr>
                                    <td class="text-center">${idx + 1}</td>
                                    <td><span class="fw-bold">${item.item_code || '-'}</span></td>
                                    <td>${item.item_name}</td>
                                    <td class="text-end">${qtyText}</td>
                                    <td class="text-end fw-semibold text-primary">${priceText}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    } else {
                        tbody.append('<tr><td colspan="5" class="text-center text-muted py-3">Belum ada item untuk customer ini</td></tr>');
                    }

                    $('#modalDetail').modal('show');
                }).fail(function() {
                    Swal.fire('Error', 'Data detail tidak ditemukan.', 'error');
                });
            });
        });

        // Fungsi membuka modal Tambah
        function openCreateModal() {
            $('#mainForm')[0].reset();
            $('#dataId').val('');

            // Mode Create: Tampilkan select2 customer dan sembunyikan display readonly
            $('#customerSelectWrapper').show();
            $('#customer_id').prop('required', true);
            $('#customerDisplayWrapper').hide();
            $('#customer_id_edit').val('');

            // Load customer yang BELUM terdaftar di list (agar tidak bisa duplicate)
            $.get("{{ route('customer-items.available-customers') }}", function(customers) {
                let select = $('#customer_id');
                select.empty();
                select.append('<option value="">-- Pilih Customer --</option>');

                if (customers.length === 0) {
                    select.append('<option value="" disabled>Semua customer sudah terdaftar di list</option>');
                } else {
                    customers.forEach(function(c) {
                        let code = c.code ? c.code + ' - ' : '';
                        let sort = c.sort_name ? ' [' + c.sort_name + ']' : '';
                        select.append(`<option value="${c.id}">${code}${c.name}${sort}</option>`);
                    });
                }
                select.val('').trigger('change');
            });

            // Kosongkan dan buat 1 baris item awal
            $('#itemsTableBody').empty();
            itemRowIndex = 0;
            addItemRow();

            $('#modalTitle').text('Tambah Customer Item');
            $('#modalForm').modal('show');
        }

        // Helper fungsi tambah baris item
        function addItemRow(code = '', name = '', qty = '', price = '') {
            const idx = itemRowIndex++;
            const row = `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${idx}][item_code]" class="form-control form-control-sm"
                               value="${code || ''}" placeholder="Cth: FNG623">
                    </td>
                    <td>
                        <input type="text" name="items[${idx}][item_name]" class="form-control form-control-sm"
                               value="${name || ''}" placeholder="Cth: SHIHLIN FF15 KG" required>
                    </td>
                    <td>
                        <input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm text-end"
                               value="${qty !== '' ? qty : '0'}" min="0" placeholder="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${idx}][price]" class="form-control form-control-sm text-end"
                               value="${price || ''}" min="0" placeholder="0.00">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-remove-row p-1" title="Hapus baris item">
                            <i class="ph-bold ph-trash f-s-16"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#itemsTableBody').append(row);
        }
    </script>
    @endpush
</x-app-layout>
