<x-app-layout>
    @section('title', 'BG Recommendations')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Credit Limit Recommendations</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    {{-- TABS --}}
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <button class="nav-link active text-danger fw-bold" data-bs-toggle="tab" data-bs-target="#expiring-pane">Expiring (Action Needed)</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#history-pane">History</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- TAB 1 --}}
                        <div class="tab-pane fade show active" id="expiring-pane">
                            <table class="table table-hover w-100" id="sampleTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Customer</th>
                                        <th>Current BG</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        {{-- TAB 2 --}}
                        <div class="tab-pane fade" id="history-pane">
                            <table class="table table-hover w-100" id="historyTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Customer</th>
                                        <th>Avg Sales</th>
                                        <th>Rec. Limit</th>
                                        <th>Set BG (Final)</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PROCESS RECOMMENDATION (UWOW DESIGN) --}}
    <div class="modal fade" id="recModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                
                {{-- Header Modern (Clean White) --}}
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div class="d-flex align-items-center">
                        
                        {{-- Icon Wrapper: Biru Muda Transparan dengan Icon Biru --}}
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="ph-bold ph-calculator f-s-24"></i>
                        </div>
                        
                        <div>
                            {{-- Title: Hitam Pekat agar Jelas --}}
                            <h5 class="modal-title fw-bold text-light mb-0">Credit Analysis & Recommendation</h5>
                            
                            {{-- Subtitle: Abu-abu --}}
                            <small class="text-light">System-assisted credit limit calculation</small>
                        </div>
                    </div>
                    
                    {{-- Tombol Close Standar --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="recForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="recId">

                    <div class="modal-body p-0 bg-light">
                        
                        {{-- SECTION 1: CUSTOMER INFO BAR --}}
                        <div class="bg-white px-4 py-3 border-bottom">
                            <div class="row align-items-center">
        
                                {{-- Nama Customer --}}
                                
                                <div class="col-md-6 mt-3 mt-md-0">
                                    <div class="d-inline-block text-start bg-soft-primary px-4 py-2 rounded-3 border border-primary border-opacity-10 shadow-sm">
                                        <small class="d-block text-primary fw-bold f-s-11 mb-1 opacity-75">CUSTOMER NAME</small>
                                        <h3 class="fw-bold text-primary mb-0 f-s-24" id="disp_customer">-</h3>
                                    </div>
                                </div>

                                {{-- Current BG (DIPERBESAR) --}}
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <div class="d-inline-block text-start bg-soft-primary px-4 py-2 rounded-3 border border-primary border-opacity-10 shadow-sm">
                                        <small class="d-block text-primary fw-bold f-s-11 mb-1 opacity-75">CURRENT EXISTING BG</small>
                                        {{-- Ubah jadi h3 dan f-s-24 agar besar --}}
                                        <h3 class="fw-bold text-primary mb-0 f-s-24" id="disp_current_bg">-</h3>
                                    </div>
                                </div>
                            </div>

                            {{-- Parameters Cards --}}
                            <div class="row g-2 mt-3">
                                <div class="col-6 col-md">
                                    <div class="p-2 border rounded bg-light text-center h-100">
                                        <small class="text-muted d-block f-s-10 mb-1">TOP (Days)</small>
                                        <span class="fw-bold text-dark" id="disp_top">-</span>
                                        <input type="hidden" id="top">
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-2 border rounded bg-light text-center h-100">
                                        <small class="text-muted d-block f-s-10 mb-1">Lead Time</small>
                                        <span class="fw-bold text-dark" id="disp_lead">-</span>
                                        <input type="hidden" id="lead_time">
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-2 border rounded bg-light text-center h-100">
                                        <small class="text-muted d-block f-s-10 mb-1">Inflation</small>
                                        <span class="fw-bold text-dark" id="disp_inflation">-</span>
                                        <input type="hidden" id="inflation">
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-2 border rounded bg-light text-center h-100">
                                        <small class="text-muted d-block f-s-10 mb-1">Tax (PPN)</small>
                                        <span class="fw-bold text-dark" id="disp_tax">-</span>
                                        {{-- Simpan value asli (0.11) di hidden input --}}
                                        <input type="hidden" id="tax_val">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="p-2 border border-primary rounded bg-primary bg-opacity-10 text-center h-100 d-flex flex-column justify-content-center">
                                        <small class="text-primary fw-bold d-block f-s-10 mb-1 text-uppercase">Limit Rule (Auto)</small>
                                        <strong class="text-primary f-s-16"><span id="disp_rule">-</span></strong>
                                        <input type="hidden" id="rule_percent">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-0">
                            {{-- SECTION 2: CALCULATION (LEFT) --}}
                            <div class="col-lg-7 border-end bg-white">
                                <div class="p-4">
                                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="ph-bold ph-chart-bar me-2 text-primary"></i>Input & Calculation
                                    </h6>

                                    {{-- Input Average --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold text-dark f-s-12">INPUT AVERAGE SALES <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg shadow-sm">
                                            <span class="input-group-text bg-white fw-bold border-end-0 text-muted">Rp</span>
                                            <input type="number" name="average" id="average" class="form-control border-start-0 fw-bold text-dark" placeholder="0" required>
                                        </div>
                                    </div>

                                    {{-- Calculation Table --}}
                                    <div class="bg-light border rounded-3 overflow-hidden">
                                        <table class="table table-borderless table-sm mb-0 align-middle">
                                            {{-- 1. PPN --}}
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted w-50">
                                                    Est. PPN <small class="fw-bold text-dark" id="lbl_tax_calc">(Waiting...)</small>
                                                </td>
                                                <td class="pe-3 py-2 text-end fw-bold text-dark f-s-14" id="calc_avg_ppn">-</td>
                                            </tr>
                                            
                                            {{-- 2. Faktor --}}
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted">
                                                    Faktor Pengali <br>
                                                    <small class="f-s-10 text-primary">Rumus: ((TOP+Lead)/TOP) x 130%</small>
                                                </td>
                                                <td class="pe-3 py-2 text-end">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary f-s-12 px-3" id="calc_factor_val">-</span>
                                                </td>
                                            </tr>

                                            {{-- 3. Rec Limit --}}
                                            <tr class="border-bottom border-white bg-white">
                                                <td class="ps-3 py-2 fw-bold text-dark">
                                                    Recommendation Limit <br>
                                                    <small class="fw-normal text-muted f-s-10">(Est. PPN x Faktor)</small>
                                                </td>
                                                <td class="pe-3 py-2 text-end fw-bold text-dark f-s-15" id="calc_rec_limit">-</td>
                                            </tr>

                                            {{-- 4. FK Limit --}}
                                            <tr class="border-bottom border-white">
                                                <td class="ps-3 py-2 text-muted">
                                                    <i class="ph-arrow-elbow-down-right me-2"></i>FK Limit Rule <small class="text-dark fw-bold" id="lbl_rule_calc">(x Rule %)</small>
                                                </td>
                                                <td class="pe-3 py-2 text-end fw-bold text-secondary" id="calc_fk_limit">-</td>
                                            </tr>

                                            {{-- 5. Rounded --}}
                                            <tr class="bg-success bg-opacity-10">
                                                <td class="ps-3 py-3 fw-bold text-success">
                                                    ROUNDED (Jutaan)
                                                </td>
                                                <td class="pe-3 py-3 text-end fw-bold text-success f-s-18" id="calc_rounded">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: DECISION (RIGHT) --}}
                            <div class="col-lg-5 border-end bg-white">
                                <div class="p-4 h-100 d-flex flex-column">
                                    <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">
                                        <i class="ph-bold ph-check-circle me-2 text-success"></i>Final Decision
                                    </h6>

                                    {{-- Input Set BG --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-dark f-s-12">SET BG (APPROVED NOMINAL) <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-lg shadow-sm">
                                            <span class="input-group-text bg-success text-white border-success fw-bold">Rp</span>
                                            <input type="number" name="set_bg" id="set_bg" class="form-control border-success text-success fw-bold" placeholder="0">
                                        </div>
                                    </div>

                                    {{-- Credit Limit Updated (Blue Box Style) --}}
                                    <div class="p-4 border border-primary border-opacity-25 rounded-3 bg-primary bg-opacity-10 text-center mb-4">
                                        <small class="text-uppercase text-primary fw-bold f-s-11 mb-2 d-block letter-spacing-1">
                                            CREDIT LIMIT UPDATED (TO CUSTOMER)
                                        </small>
                                        
                                        <h2 class="fw-bold text-primary mb-2 f-s-28" id="calc_limit_updated">
                                            Rp 0
                                        </h2>
                                        
                                        <div class="d-inline-block bg-white text-primary px-3 py-1 rounded-pill border border-primary border-opacity-10 shadow-sm">
                                            <small class="f-s-10 fw-bold">Rumus: Set BG / (Limit Rule %)</small>
                                        </div>
                                    </div>

                                    {{-- Notes --}}
                                    <div class="mb-4 flex-grow-1">
                                        <label class="form-label fw-bold text-muted f-s-12">Notes / Remarks</label>
                                        <textarea name="notes" id="notes" class="form-control bg-white" rows="3" placeholder="Tulis catatan untuk customer..."></textarea>
                                    </div>

                                    {{-- Tombol Action (Cancel & Save) --}}
                                    <div class="row g-2 mt-auto">
                                        <div class="col-8">
                                            <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold shadow-sm">
                                                <i class="ph-paper-plane-right me-2"></i> Save Recommendation
                                            </button>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-white border w-100 btn-lg fw-bold text-muted" data-bs-dismiss="modal">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            const fmt = (num) => new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(num);

            // Table Expiring
            let tableExpiring = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-recommendations.index') }}", data: { type: 'expiring' } },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex', 
                        orderable: false,   // <--- WAJIB ADA
                        searchable: false,
                        className: 'text-center'
                    },
                    { data: 'customer_name', name: 'customer.name', className: 'fw-bold' },
                    { data: 'current_bg', name: 'current_bg' },
                    { data: 'action', orderable: false, searchable: false, className: 'text-center' }
                ]
            }); 

            // Table History
            let tableHistory = $('#historyTable').DataTable({
                processing: true, serverSide: true,
                ajax: { url: "{{ route('bg-recommendations.index') }}", data: { type: 'history' } },
                columns: [
                    { 
                        data: 'DT_RowIndex', 
                        name: 'DT_RowIndex', 
                        orderable: false, 
                        searchable: false,
                        className: 'text-center'
                    },
                    { data: 'customer_name', name: 'customer.name' },
                    { data: 'average', name: 'average' },
                    { data: 'recommended_credit_limit', name: 'recommended_credit_limit' },
                    { data: 'set_bg', name: 'set_bg' },
                    { data: 'status', name: 'bg_recommendations.status' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'asc']]
            });

            // --- Cari bagian Fetch Data (onClick button process) ---
            $(document).on('click', '.btn-process, .btn-edit-rec', function() {
                let id = $(this).data('id');
                $('#recForm')[0].reset();
                $('#recId').val(id);

                $.get("{{ url('bg/bg-recommendations') }}/" + id, function(data) {
                    // --- 1. Header Info ---
                    $('#disp_customer').text(data.customer ? data.customer.name : '-');
                    $('#disp_current_bg').text(fmt(data.current_bg));

                    // --- 2. Parameter Display (Format Cantik) ---
                    
                    // Inflation: 130.00 -> "130%"
                    let inflRaw = parseFloat(data.inflation);
                    $('#disp_inflation').text(inflRaw + '%');
                    $('#inflation').val(inflRaw);

                    // Tax: 0.11 -> "11%"
                    let taxRaw = parseFloat(data.tax_value); // misal 0.11
                    let taxPercent = Math.round(taxRaw * 100); // 11
                    $('#disp_tax').text(taxPercent + '%');
                    $('#tax_val').val(taxRaw);
                    // Update label di tabel kalkulasi
                    $('#lbl_tax_calc').text('(Avg x ' + taxPercent + '%)');

                    // TOP & Lead
                    $('#disp_top').text(data.top);
                    $('#top').val(data.top);
                    $('#disp_lead').text(data.lead_time);
                    $('#lead_time').val(data.lead_time);

                    // Limit Rule
                    let ruleRaw = parseFloat(data.calculated_rule_percent);
                    $('#disp_rule').text(ruleRaw + '%');
                    $('#rule_percent').val(ruleRaw);
                    $('#lbl_rule_calc').text('(x ' + ruleRaw + '%)');

                    // --- 3. Isi Inputan jika edit ---
                    if(data.average > 0) $('#average').val(data.average);
                    if(data.set_bg > 0) $('#set_bg').val(data.set_bg);
                    $('#notes').val(data.notes);

                    calculateAll();
                    $('#recModal').modal('show');
                });
            });

            // --- 3. Logic Kalkulasi Updated ---
            function calculateAll() {
                let avg       = parseFloat($('#average').val()) || 0;
                let top       = parseFloat($('#top').val()) || 0;
                let lead      = parseFloat($('#lead_time').val()) || 0;
                let inflation = parseFloat($('#inflation').val()) || 130;
                let rule      = parseFloat($('#rule_percent').val()) || 0;
                let taxRaw    = parseFloat($('#tax_val').val()) || 0.11;

                let valAvgPpn = avg * taxRaw;
                $('#calc_avg_ppn').text(fmt(valAvgPpn));

                let timeFactor = top > 0 ? (top + lead) / top : 1;
                let inflFactor = inflation / 100; // 1.3
                let totalFactor = timeFactor * inflFactor;
                
                $('#calc_factor_val').text(totalFactor.toFixed(3)); 

                let recLimit = valAvgPpn * totalFactor;
                $('#calc_rec_limit').text(fmt(recLimit));

                let fkLimit = recLimit * (rule / 100);
                $('#calc_fk_limit').text(fmt(fkLimit));

                let rounded = Math.round(fkLimit / 1000000) * 1000000;
                $('#calc_rounded').text(fmt(rounded));

                if(!$('#set_bg').is(':focus')) {
                    $('#set_bg').val(rounded);
                }
                
                let setBgUser = parseFloat($('#set_bg').val()) || 0;
                let limitUpd = 0;
                if (rule > 0) {
                    limitUpd = setBgUser / (rule / 100);
                } else {
                    limitUpd = setBgUser;
                }
                $('#calc_limit_updated').text(fmt(limitUpd));
            }

            $('#average, #set_bg').on('input', calculateAll);

            // SUBMIT DENGAN VALIDASI SWEETALERT
            $('#recForm').on('submit', function(e) {
                e.preventDefault();
                
                let id = $('#recId').val();
                let form = this;
                let submitBtn = $(this).find('button[type="submit"]');
                let originalBtnContent = submitBtn.html(); // Simpan teks asli tombol

                // 1. Tampilkan Konfirmasi Dulu
                Swal.fire({
                    title: 'Apakah data sudah benar?',
                    text: "Pastikan nominal dan perhitungan sudah sesuai sebelum disimpan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd', // Warna Biru (Primary)
                    cancelButtonColor: '#d33',     // Warna Merah
                    confirmButtonText: 'Ya, Simpan & Kirim!',
                    cancelButtonText: 'Cek Lagi',
                    reverseButtons: true // Tombol Confirm di kanan
                }).then((result) => {
                    
                    // 2. Jika User Klik "Ya"
                    if (result.isConfirmed) {
                        
                        // Ubah tombol jadi Loading biar ga diklik 2x
                        submitBtn.prop('disabled', true).html('<i class="ph-bold ph-spinner ph-spin me-2"></i> Processing...');

                        // 3. Proses AJAX
                        $.ajax({
                            url: "{{ url('bg/bg-recommendations') }}/" + id,
                            method: "PUT",
                            data: $(form).serialize(),
                            success: function(res) {
                                $('#recModal').modal('hide');
                                tableExpiring.ajax.reload(); // Reload Tabel Expiring
                                tableHistory.ajax.reload();  // Reload Tabel History
                                
                                // Notifikasi Sukses
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(err) {
                                // Notifikasi Error
                                Swal.fire(
                                    'Gagal!',
                                    'Terjadi kesalahan saat menyimpan data.',
                                    'error'
                                );
                            },
                            complete: function() {
                                // Balikin tombol ke semula
                                submitBtn.prop('disabled', false).html(originalBtnContent);
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>