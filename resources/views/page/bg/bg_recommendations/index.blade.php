<x-app-layout>
    @section('title', 'BG Recommendations')
    @include('components.sample-table-styles')

    {{-- Ambil variabel persentase dari controller, default 11 jika tidak ada --}}
    @php
        $percent = isset($defaultPercent) ? $defaultPercent : 11;
    @endphp

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Credit Limit Recommendations</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Recommendations</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- Card Container Utama --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    {{-- Header & Filter Area --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                            <select id="statusFilter" class="form-select select2" style="width: 150px;">
                                <option value="all">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="sent_to_customer">Sent to Cust</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        {{-- Tombol Manual Create (Opsional jika ingin buat rekomendasi tanpa trigger expired) --}}
                        <div class="ms-auto">
                            <button class="btn btn-primary" type="button" id="btn-create-manual">
                                <i class="ph-bold ph-plus"></i>
                                <span>Manual Create</span>
                            </button>
                        </div>
                    </div>

                    {{-- NAV TABS --}}
                    <ul class="nav nav-tabs mb-4" id="recTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-danger" id="expiring-tab" data-bs-toggle="tab" data-bs-target="#expiring-pane" type="button" role="tab">
                                <i class="ph-bold ph-warning-circle"></i> Expiring (Action Needed)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                                <i class="ph-bold ph-clock-counter-clockwise"></i> History Data
                            </button>
                        </li>
                    </ul>

                    {{-- TAB CONTENT --}}
                    <div class="tab-content" id="recTabsContent">

                        {{-- TAB 1: EXPIRING BGs (Table Action) --}}
                        <div class="tab-pane fade show active" id="expiring-pane" role="tabpanel">
                            <div class="alert alert-soft-info border-0 d-flex align-items-center" role="alert">
                                <i class="ph-bold ph-info me-2 fs-5"></i>
                                <div>
                                    Daftar di bawah ini adalah BG yang akan expired dalam <strong>60 hari</strong>. Klik tombol <strong>Process</strong> untuk membuat rekomendasi perpanjangan.
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover w-100 display" id="sampleTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Customer</th>
                                            <th>No. BG</th>
                                            <th>Nominal Saat Ini</th>
                                            <th>Exp Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: HISTORY (Table Original) --}}
                        <div class="tab-pane fade" id="history-pane" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table w-100 display" id="tableHistory">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Customer</th>
                                            <th>Avg Sales</th>
                                            <th>Rec. Limit</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                    </div> {{-- End Tab Content --}}

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FORM RECOMMENDATION (SMART CALCULATION) --}}
    <div class="modal fade" id="recModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Form Recommendation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="recForm">
                    @csrf
                    {{-- Hidden Fields --}}
                    <input type="hidden" name="id" id="recId">
                    {{-- Default percent value from controller --}}
                    <input type="hidden" id="default_percent" value="{{ $percent }}">

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Customer Selection --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Customer <span class="text-danger">*</span></label>
                                {{-- Jika mode Manual: Select2 aktif. Jika mode Process: Readonly input --}}
                                <div id="customer_select_wrapper">
                                    <select name="customer_id" id="customer_id" class="form-select select2-modal" style="width:100%" required>
                                        <option></option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="customer_readonly_wrapper" class="d-none">
                                    <input type="text" id="customer_name_readonly" class="form-control bg-light" readonly>
                                    <input type="hidden" name="customer_id_hidden" id="customer_id_hidden">
                                </div>
                            </div>

                            {{-- Input Average & Info Percentage --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Average Sales / Basis (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="average" id="average" class="form-control" step="1" placeholder="Masukkan rata-rata..." required>
                                <div class="form-text text-primary f-s-12">
                                    <i class="ph-bold ph-magic-wand"></i> Sistem akan otomatis menghitung kenaikan <strong>{{ $percent }}%</strong>.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Inflation (%)</label>
                                <input type="number" name="inflation" id="inflation" class="form-control" step="0.01" value="0">
                            </div>

                            {{-- RESULT AREA --}}
                            <div class="col-12">
                                <div class="p-3 rounded border border-success bg-soft-success">
                                    <label class="form-label fw-bold text-success mb-0">Recommended Limit (Hasil Perhitungan)</label>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h3 class="m-0 text-success fw-bold" id="display_result">Rp 0</h3>
                                        <span class="badge bg-success">{{ $percent }}% Increase</span>
                                    </div>
                                    <input type="hidden" name="recommended_credit_limit" id="recommended_credit_limit">
                                    <small class="text-muted f-s-11">Rumus: Average + (Average × {{ $percent }}%)</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Catatan (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan untuk customer..."></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-bold ph-paper-plane-right me-1"></i> Save & Notify Customer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // 1. Setup Select2 di dalam Modal
            $('.select2-modal').select2({
                dropdownParent: $('#recModal'),
                theme: 'bootstrap-5',
                placeholder: 'Select Customer',
                allowClear: true
            });

            // 2. DataTable: EXPIRING BGs
            const tableExpiring = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bg-recommendations.index') }}",
                    data: { type: 'expiring' } // Kirim parameter type ke controller
                },
                columns: [
                    { data: 'customer.name', name: 'customer.name' },
                    {
                        data: 'bg_number', name: 'bg_number',
                        render: function(data) {
                            return `<span class="badge bg-soft-primary text-primary font-monospace">${data}</span>`;
                        }
                    },
                    { data: 'bg_nominal', name: 'bg_nominal' },
                    {
                        data: 'exp_date', name: 'exp_date',
                        render: function(data) {
                            return `<span class="text-danger fw-bold">${data}</span>`;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[3, 'asc']] // Urutkan berdasarkan tanggal expired terdekat
            });

            // 3. DataTable: HISTORY
            const tableHistory = $('#tableHistory').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('bg-recommendations.index') }}",
                    data: { type: 'history' }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'average', name: 'average', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') },
                    { data: 'recommended_credit_limit', name: 'recommended_credit_limit' },
                    { data: 'status_badge', name: 'status', className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            });

            // 4. Kalkulasi Otomatis (Realtime)
            function calculateLimit() {
                let avg = parseFloat($('#average').val()) || 0;
                let percent = parseFloat($('#default_percent').val()) || 11;

                // Rumus: Avg + (Avg * Percent / 100)
                let result = avg + (avg * (percent / 100));

                // Set Hidden Input
                $('#recommended_credit_limit').val(result);

                // Set Display Text (Format Rupiah)
                let formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(result);

                $('#display_result').text(formatted);
            }

            // Trigger kalkulasi saat user mengetik di kolom average
            $('#average').on('input', calculateLimit);

            // 5. Button "Process" dari Tab Expiring
            $(document).on('click', '.btn-process', function() {
                // Ambil data dari tombol
                let custId = $(this).data('customer-id');
                let custName = $(this).data('customer-name');
                let nominal = $(this).data('nominal');

                // Reset Form
                $('#recForm')[0].reset();
                $('#recId').val('');

                // Mode: Process (Readonly Customer)
                $('#customer_select_wrapper').addClass('d-none');
                $('#customer_readonly_wrapper').removeClass('d-none');
                $('#customer_name_readonly').val(custName);

                // Karena select2 disabled, kita pakai hidden input untuk kirim ID
                $('#customer_id_hidden').val(custId).attr('name', 'customer_id');
                $('#customer_id').removeAttr('name'); // disable select name agar tidak bentrok

                // Isi Average dengan nominal BG sebelumnya sebagai saran
                $('#average').val(nominal);

                // Trigger Hitung
                calculateLimit();

                // Tampilkan Modal
                $('#modalTitle').text('Process Recommendation');
                $('#recModal').modal('show');
            });

            // 6. Button "Manual Create"
            $('#btn-create-manual').click(function() {
                $('#recForm')[0].reset();
                $('#recId').val('');
                $('#display_result').text('Rp 0');

                // Mode: Manual (Select Customer Aktif)
                $('#customer_select_wrapper').removeClass('d-none');
                $('#customer_readonly_wrapper').addClass('d-none');

                $('#customer_id').attr('name', 'customer_id').val(null).trigger('change');
                $('#customer_id_hidden').removeAttr('name');

                $('#modalTitle').text('New Recommendation');
                $('#recModal').modal('show');
            });

            // 7. Submit Form
            $('#recForm').on('submit', function(e){
                e.preventDefault();
                let id = $('#recId').val();
                let url = "{{ route('bg-recommendations.store') }}"; // Kita fokus store dulu

                // Jika edit, sesuaikan url (logika edit bisa ditambahkan nanti jika perlu)
                // Saat ini fokus ke flow Create baru dari Expired List

                let btn = $(this).find('button[type="submit"]');
                let originalText = btn.html();
                btn.prop('disabled', true).html('<i class="ph-bold ph-spinner ph-spin"></i> Processing...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res){
                        $('#recModal').modal('hide');
                        tableExpiring.ajax.reload(); // Reload table expiring
                        tableHistory.ajax.reload();  // Reload table history

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        btn.prop('disabled', false).html(originalText);
                    },
                    error: function(err){
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data.', 'error');
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

        });
    </script>
    @endpush
</x-app-layout>
