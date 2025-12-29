<x-app-layout>
    @section('title', 'Approval Inbox')
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Approval Inbox</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Inbox</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="ph-bold ph-info me-2 fs-4"></i>
                <div>
                    Halaman ini menampilkan daftar pengajuan yang <strong>Menunggu Persetujuan (Waiting Approval)</strong> dari Finance.
                </div>
            </div>

            <div class="main-table-container">
                <table class="w-100 display align-middle" id="sampleTable">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Customer</th>
                            <th>Form Code</th>
                            <th>Total BG</th>
                            <th>Submitted At</th>
                            <th width="15%" class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL APPROVAL / REJECT --}}
    <div class="modal fade" id="approvalModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Pakai modal-lg biar muat tabel --}}
            <div class="modal-content">
                <div class="modal-header text-white" id="modalHeader">
                    <h5 class="modal-title fw-bold" id="modalTitle">Review Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="approvalForm">
                    @csrf
                    <input type="hidden" id="submission_id" name="id">
                    <input type="hidden" id="action_type" name="action">

                    <div class="modal-body">

                        {{-- DATA LAMPIRAN D FULL --}}
                        <h6 class="fw-bold text-primary mb-2 border-bottom pb-2">
                            <i class="ph-bold ph-file-text me-1"></i> Data Analisa (Lampiran D)
                        </h6>

                        <div class="table-responsive bg-light p-3 rounded border mb-3">
                            <table class="table table-sm table-borderless small mb-0">
                                <tr>
                                    <td width="5%">1.</td>
                                    <td width="40%" class="text-muted">Nama Distributor</td>
                                    <td class="fw-bold text-dark" id="d_nama">...</td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td class="text-muted">Kota</td>
                                    <td class="fw-bold text-dark" id="d_kota">...</td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td class="text-muted">Wilayah Kerja</td>
                                    <td class="fw-bold text-dark" id="d_wilayah">...</td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td class="text-muted">Periode</td>
                                    <td class="fw-bold text-dark" id="d_periode">...</td>
                                </tr>
                                <tr>
                                    <td>5.</td>
                                    <td class="text-muted">Rata-rata Penjualan</td>
                                    <td class="fw-bold text-dark" id="d_sales">...</td>
                                </tr>
                                <tr>
                                    <td>6.</td>
                                    <td class="text-muted">Syarat Pembayaran (TOP)</td>
                                    <td class="fw-bold text-dark" id="d_top">...</td>
                                </tr>
                                <tr>
                                    <td>7.</td>
                                    <td class="text-muted">Lead Time</td>
                                    <td class="fw-bold text-dark" id="d_lead">...</td>
                                </tr>
                                <tr>
                                    <td>8.</td>
                                    <td class="text-muted">Faktor Fluktuasi</td>
                                    <td class="fw-bold text-dark" id="d_inflasi">...</td>
                                </tr>
                                <tr>
                                    <td>9.</td>
                                    <td class="text-muted">Limit Kredit</td>
                                    <td class="fw-bold text-primary fs-6" id="d_limit">...</td>
                                </tr>
                                <tr>
                                    <td>10.</td>
                                    <td class="text-muted">Nilai BG Ditetapkan</td>
                                    <td class="fw-bold text-dark" id="d_bg_tetap">...</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="pt-2">11.</td>
                                    <td class="text-muted pt-2">Nilai BG Diserahkan</td>
                                    <td class="fw-bold text-success fs-5 pt-2" id="d_bg_serah">...</td>
                                </tr>
                            </table>
                        </div>

                        {{-- INPUT NOTES --}}
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Catatan / Alasan <span class="text-danger">*</span></label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Tuliskan catatan revisi atau alasan penolakan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btnSubmitModal">
                            <i class="ph-bold ph-paper-plane-right me-1"></i> Submit Decision
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('bg-approvals.index') }}",
                order: [[4, 'desc']],
                columns: [
                    {data: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center'},
                    {data: 'customer_name', name: 'recommendation.customer.name'},
                    {data: 'form_code', name: 'form_code'},
                    {data: 'bg_nominal', name: 'bg_nominal'},
                    {data: 'submitted_at', name: 'updated_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ]
            });

            // 1. QUICK APPROVE
            $(document).on('click', '.btn-quick-approve', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Quick Approve?',
                    text: "Dokumen akan langsung disetujui tanpa catatan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'Yes, Approve!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processApproval(id, 'approve', null);
                    }
                });
            });

            // 2. RESEND EMAIL
            $(document).on('click', '.btn-resend', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Resend Notification?',
                    text: "Kirim ulang email notifikasi approval ke Finance.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Send!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ url('bg/approvals/resend') }}/" + id, { _token: "{{ csrf_token() }}" })
                         .done(function(res) { Swal.fire('Sent!', res.message, 'success'); })
                         .fail(function() { Swal.fire('Error', 'Failed to send email', 'error'); });
                    }
                });
            });

            // 3. REVIEW (APPROVE WITH NOTES)
            $(document).on('click', '.btn-review', function() {
                let id = $(this).data('id');
                prepareModal(id, 'approve', 'Review with Notes', 'bg-warning', 'btn-warning');
            });

            // 4. REJECT
            $(document).on('click', '.btn-reject', function() {
                let id = $(this).data('id');
                prepareModal(id, 'reject', 'Reject Submission', 'bg-danger', 'btn-danger');
            });

            // --- FUNCTION: PREPARE MODAL ---
            function prepareModal(id, action, title, headerClass, btnClass) {
                $('#submission_id').val(id);
                $('#action_type').val(action);
                $('#modalTitle').text(title);

                // Reset Style
                $('#modalHeader').removeClass('bg-warning bg-danger bg-success').addClass(headerClass);
                $('#btnSubmitModal').removeClass('btn-warning btn-danger btn-success').addClass(btnClass);
                $('textarea[name="notes"]').val('');

                // Reset Text
                $('#d_nama, #d_kota, #d_wilayah, #d_periode, #d_sales, #d_top, #d_lead, #d_inflasi, #d_limit, #d_bg_tetap, #d_bg_serah').text('Loading...');

                $('#approvalModal').modal('show');

                $.get("{{ url('bg/approvals/modal-data') }}/" + id, function(res) {
                    if(res.success) {
                        let d = res.data;
                        $('#d_nama').text(d.nama_distributor);
                        $('#d_kota').text(d.kota);
                        $('#d_wilayah').text(d.wilayah);
                        $('#d_periode').text(d.periode);
                        $('#d_sales').text('Rp ' + d.avg_sales);
                        $('#d_top').text(d.top + ' Hari');
                        $('#d_lead').text(d.lead_time + ' Hari');
                        $('#d_inflasi').text(d.inflasi + '%');
                        $('#d_limit').text('Rp ' + d.limit_kredit);
                        $('#d_bg_tetap').text('Rp ' + d.bg_ditetapkan);
                        $('#d_bg_serah').text('Rp ' + d.bg_diserahkan);
                    }
                });
            }

            // --- SUBMIT MODAL FORM ---
            $('#approvalForm').submit(function(e) {
                e.preventDefault();
                let id = $('#submission_id').val();
                let action = $('#action_type').val();
                let notes = $('textarea[name="notes"]').val();

                $('#approvalModal').modal('hide');
                processApproval(id, action, notes);
            });

            // --- CORE PROCESS FUNCTION ---
            function processApproval(id, action, notes) {
                Swal.fire({ title: 'Processing...', didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: "{{ route('bg-approvals.process') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        action: action,
                        notes: notes
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Success', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Something went wrong', 'error');
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
