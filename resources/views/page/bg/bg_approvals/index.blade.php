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
                    This page displays a list of submissions that are <strong>Waiting Approval</strong> from Finance.
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

    {{-- MODAL APPROVAL / REJECT (INLINE STYLED) --}}
    <div class="modal fade" id="approvalModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border-radius: 16px; overflow: hidden;">

                {{-- Header --}}
                <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 20px 25px;">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalTitle" style="color: #1e293b; font-size: 1.2rem;">Review Submission</h5>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">Verify Attachment D data before making a decision.</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" id="display_form_code" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 8px 12px; border-radius: 30px; font-weight: 600; font-size: 0.8rem;">LOADING...</span>
                            <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" style="background-size: 0.8rem;"></button>
                        </div>
                    </div>
                </div>

                <form id="approvalForm">
                    @csrf
                    <input type="hidden" id="submission_id" name="id">
                    <input type="hidden" id="action_type" name="action">

                    <div class="modal-body" style="background: #f8fafc; padding: 25px;">

                        {{-- SECTION 1: CUSTOMER INFO --}}
                        <div class="row mb-3">
                            <div class="col-12">
                                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.01); display: flex; align-items: center; gap: 20px;">
                                    <div style="background: #eff6ff; color: #3b82f6; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                        <i class="ph-duotone ph-buildings"></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; margin-bottom: 2px;">Distributor Name</div>
                                        <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;" id="d_nama">Loading...</div>
                                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                                            <i class="ph-bold ph-map-pin me-1"></i> <span id="d_kota">...</span> - <span id="d_wilayah">...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: PARAMETER ANALISA (Grid Compact) --}}
                        <div class="row g-3 mb-4">
                            {{-- Card Template Style --}}
                            @php $cardStyle = "background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.01);"; @endphp

                            <div class="col-md-3 col-6">
                                <div style="{{ $cardStyle }}">
                                    <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Avg. Sales</div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #3b82f6; margin-top: 4px;" id="d_sales">...</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div style="{{ $cardStyle }}">
                                    <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">TOP / Lead Time</div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-top: 4px;">
                                        <span id="d_top">..</span> / <span id="d_lead">..</span> Days
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div style="{{ $cardStyle }}">
                                    <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Inflation</div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #334155; margin-top: 4px;" id="d_inflasi">...</div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div style="{{ $cardStyle }}">
                                    <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Period</div>
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="d_periode">...</div>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 3: FINANCIAL SUMMARY --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                            <h6 style="font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Financial Summary</h6>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div style="background: #eff6ff; border: 1px solid #dbeafe; border-radius: 12px; padding: 15px; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #3b82f6; margin-bottom: 5px;">Credit Limit (Updated)</div>
                                    <div style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8;" id="d_limit">...</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center;">
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 5px;">Determined BG Amount</div>
                                    <div style="font-size: 1.1rem; font-weight: 800; color: #334155;" id="d_bg_tetap">...</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div style="background: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; padding: 15px; text-align: center; position: relative; overflow: hidden;">
                                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #16a34a; margin-bottom: 5px;">Total Submitted BG</div>
                                    <div style="font-size: 1.1rem; font-weight: 800; color: #15803d;" id="d_bg_serah">...</div>
                                    {{-- Icon Background --}}
                                    <i class="ph-duotone ph-check-circle" style="position: absolute; bottom: -10px; right: -10px; font-size: 4rem; color: #22c55e; opacity: 0.1;"></i>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 4: RINCIAN BANK --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
                            <div style="background: #f8fafc; padding: 10px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase;">Bank Details</span>
                                <span class="badge" style="background: #e2e8f0; color: #475569; border-radius: 20px; font-weight: 600; font-size: 0.7rem;" id="bank_count_badge">0 Bank</span>
                            </div>
                            <div id="rincian_bank_list" style="max-height: 200px; overflow-y: auto;">
                                {{-- Diisi via JS --}}
                            </div>
                        </div>

                        {{-- SECTION 4B: VERIFIKASI BANK GARANSI & DOKUMEN UPLOAD --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                            <h6 style="font-size: 0.75rem; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                                <i class="ph-bold ph-shield-check text-primary me-1"></i> Verifikasi Dokumen Bank Garansi
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted small" style="font-size: 0.75rem;">Nomor Bank Garansi</div>
                                        <div class="fw-bold font-monospace text-dark" id="d_bg_number">-</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted small" style="font-size: 0.75rem;">Tanggal Jatuh Tempo (Expired)</div>
                                        <div class="fw-bold text-danger" id="d_exp_date">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="#" target="_blank" id="btn_view_warkat" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-none">
                                    <i class="ph-bold ph-file-text me-1"></i> Buka Scan Bank Garansi Asli
                                </a>
                                <a href="#" target="_blank" id="btn_view_signed" class="btn btn-outline-success btn-sm rounded-pill px-3 d-none">
                                    <i class="ph-bold ph-file-pdf me-1"></i> Buka Dokumen Bertandatangan
                                </a>
                                <span id="no_files_badge" class="text-muted small fst-italic d-none">Belum ada file terlampir.</span>
                            </div>
                        </div>

                        {{-- SECTION 5: NOTES INPUT --}}
                        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px;">
                            <label style="font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 8px; display: block;">
                                <i class="ph-bold ph-note-pencil me-1"></i> Approval / Rejection Notes <span class="text-danger" id="notes_asterisk" style="display:none;">*</span>
                            </label>
                            <textarea name="notes" id="approval_notes" class="form-control" rows="2" placeholder="Write validation notes or rejection reason..."
                                    style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; resize: none;"></textarea>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer" style="border: none; background: #fff; padding: 15px 25px 25px; display: flex; justify-content: space-between;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-weight: 600; padding: 10px 24px; border-radius: 50px;">Cancel</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-danger btn-modal-reject" style="font-weight: 700; padding: 10px 24px; border-radius: 50px; display: flex; align-items: center; gap: 8px;">
                                <i class="ph-bold ph-x"></i> Reject
                            </button>
                            <button type="button" class="btn btn-success text-white btn-modal-approve" style="font-weight: 700; padding: 10px 24px; border-radius: 50px; display: flex; align-items: center; gap: 8px;">
                                <i class="ph-bold ph-check"></i> Approve
                            </button>
                        </div>
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

            $(document).on('click', '.btn-resend', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Resend Notification?',
                    text: "Resend approval notification email to Finance.",
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

            $(document).on('click', '.btn-review', function() {
                let id = $(this).data('id');
                prepareModal(id, 'Review Submission');
            });

            function prepareModal(id, title) {
                $('#submission_id').val(id);
                $('#action_type').val('');

                $('#modalTitle').text(title);

                $('textarea[name="notes"]').val('');
                $('#notes_asterisk').hide();

                $('#d_nama, #d_kota, #d_wilayah, #d_periode, #d_sales, #d_top, #d_lead, #d_inflasi, #d_limit, #d_bg_tetap, #d_bg_serah').html('<span class="spinner-border spinner-border-sm text-secondary"></span>');
                $('#display_form_code').text('LOADING...');
                $('#rincian_bank_list').html('<div style="padding:20px; text-align:center; color:#94a3b8; font-size:0.85rem;">Loading data...</div>');

                $('#approvalModal').modal('show');

                $.get("{{ url('bg/approvals/modal-data') }}/" + id, function(res) {
                    if(res.success) {
                        let d = res.data;

                        $('#display_form_code').text(d.form_code);
                        $('#d_nama').text(d.nama_distributor);
                        $('#d_kota').text(d.kota);
                        $('#d_wilayah').text(d.wilayah);
                        $('#d_periode').text(d.periode);
                        $('#d_sales').text('Rp ' + d.avg_sales);
                        $('#d_top').text(d.top);
                        $('#d_lead').text(d.lead_time);
                        $('#d_inflasi').text(d.inflasi + '%');

                        $('#d_limit').text('Rp ' + d.limit_kredit);
                        $('#d_bg_tetap').text('Rp ' + d.bg_ditetapkan);
                        $('#d_bg_serah').text('Rp ' + d.bg_diserahkan_total);

                        let listHtml = '';
                        let count = 0;

                        if(d.rincian_bank && d.rincian_bank.length > 0) {
                            d.rincian_bank.forEach(function(bank) {
                                count++;
                                listHtml += `
                                    <div style="padding: 12px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                                <i class="ph-bold ph-bank"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.9rem; color: #1e293b;">${bank.bank_name}</div>
                                                <div style="font-size: 0.75rem; color: #94a3b8;">Issuing Bank</div>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-weight: 700; color: #334155;">Rp ${bank.nominal}</div>
                                            <div style="font-size: 0.7rem; color: #16a34a; display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                                                <i class="ph-fill ph-check-circle"></i> Verified
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            listHtml = `<div style="padding: 20px; text-align: center; font-style: italic; color: #94a3b8; font-size: 0.85rem;">No bank details</div>`;
                        }

                        $('#rincian_bank_list').html(listHtml);
                        $('#bank_count_badge').text(count + ' Bank');

                        // Verifikasi Warkat & Dokumen
                        $('#d_bg_number').text(d.bg_number || '-');
                        $('#d_exp_date').text(d.exp_date || '-');

                        let hasFile = false;
                        if(d.warkat_url) {
                            $('#btn_view_warkat').attr('href', d.warkat_url).removeClass('d-none');
                            hasFile = true;
                        } else {
                            $('#btn_view_warkat').addClass('d-none');
                        }

                        if(d.signed_doc_url) {
                            $('#btn_view_signed').attr('href', d.signed_doc_url).removeClass('d-none');
                            hasFile = true;
                        } else {
                            $('#btn_view_signed').addClass('d-none');
                        }

                        if(!hasFile) {
                            $('#no_files_badge').removeClass('d-none');
                        } else {
                            $('#no_files_badge').addClass('d-none');
                        }
                    }
                });
            }

            $(document).on('click', '.btn-modal-reject', function() {
                let id = $('#submission_id').val();
                let notes = $('#approval_notes').val().trim();
                
                if (!notes) {
                    $('#notes_asterisk').show();
                    Swal.fire('Warning', 'Notes are required when rejecting a submission.', 'warning');
                    $('#approval_notes').focus();
                    return;
                }

                Swal.fire({
                    title: 'Confirm Rejection?',
                    html: 'You are about to <b>REJECT</b> this submission. The document will be returned to revision status.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Reject!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#approvalModal').modal('hide');
                        processApproval(id, 'reject', notes);
                    }
                });
            });

            $(document).on('click', '.btn-modal-approve', function() {
                let id = $('#submission_id').val();
                let notes = $('#approval_notes').val().trim();

                Swal.fire({
                    title: 'Confirm Approval?',
                    html: 'You are about to <b>APPROVE</b> this submission. Attachment D document will be issued.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Approve!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#approvalModal').modal('hide');
                        processApproval(id, 'approve', notes);
                    }
                });
            });

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
