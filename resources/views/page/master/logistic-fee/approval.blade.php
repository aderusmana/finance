<x-app-layout>
    @section('title', 'Logistic Fee Approvals')
    @include('components.sample-table-styles')

    <div style="background-color: #f8fafc; min-height: 100vh; padding-bottom: 2rem;">

        {{-- 1. HEADER BANNER MEWAH (TEMA EMERALD/PERSETUJUAN) --}}
        <div class="row m-2 mb-4">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 1.25rem; padding: 2rem 2.5rem; color: white; box-shadow: 0 10px 25px rgba(5, 150, 105, 0.2); position: relative; overflow: hidden; margin-bottom: -1rem; z-index: 1;">
                    <div>
                        <h3 class="fw-bolder mb-1" style="letter-spacing: -0.5px;">Logistic Fee Approvals</h3>
                        <p class="mb-0" style="color: #d1fae5; font-size: 0.95rem;">Review the Logistic Fee change requests carefully before approving or rejecting them.</p>
                    </div>
                    <!-- <div class="flex-shrink-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0" style="background: rgba(255,255,255,0.15); padding: 0.5rem 1.2rem; border-radius: 2rem; display: inline-flex; flex-wrap: nowrap;">
                                <li class="breadcrumb-item"><a href="#" class="text-white text-decoration-none"><i class="ph-fill ph-check-square-offset me-1"></i> Tasks</a></li>
                                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Fee Approvals</li>
                            </ol>
                        </nav>
                    </div> -->
                </div>
            </div>
        </div>

        {{-- 2. TABEL DATA --}}
        <div class="row m-2">
            <div class="col-12">
                <div class="card" style="background: #ffffff; border: none; border-radius: 1.25rem; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.03); overflow: hidden; z-index: 2; position: relative;">
                    <div class="card-header bg-white pt-4 pb-0 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 0;">
                        <h5 class="fw-bolder mb-0" style="color: #1e293b;"><i class="ph-fill ph-list-checks me-2" style="color: #059669;"></i>Waiting List for Approval</h5>
                        <button class="btn btn-sm btn-light border fw-bold rounded-pill px-3" style="color: #475569;" onclick="table.ajax.reload()"><i class="ph-bold ph-arrows-clockwise me-1"></i> Refresh</button>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table w-100" id="sampleTable" style="margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 5%;">No</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Date Submitted</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Customer</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Distributor</th>
                                        <th style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;">Old Price</th>
                                        <th style="background-color: #f8fafc; color: #b45309; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0;"><i class="ph-bold ph-bell-ringing me-1"></i> New Request</th>
                                        <th class="text-center" style="background-color: #f8fafc; color: #475569; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 1.25rem 1rem; border-bottom: 2px solid #e2e8f0; width: 18%;">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. MODAL TINJAU APPROVAL MEWAH --}}
        <div class="modal fade" id="modalApproval" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
                    <div class="modal-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; padding: 1.5rem 2rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #4f46e5; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(79,70,229,0.2);">
                                <i class="ph-bold ph-magnifying-glass fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bolder text-dark mb-0">Review Price Changes</h5>
                                <p class="mb-0 text-muted" style="font-size: 0.8rem;">Evaluate and provide your decision.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>

                    <form id="approvalForm">
                        <div class="modal-body" style="padding: 2rem;">
                            <input type="hidden" id="log_id">

                            {{-- Info Klien Box --}}
                            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td style="color: #64748b; padding: 6px 0; width: 35%;"><i class="ph-fill ph-buildings me-2" style="color: #4f46e5;"></i>Distributor</td>
                                        <td class="fw-bold" style="color: #1e293b; padding: 6px 0;" id="txt_distributor">: -</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; padding: 6px 0;"><i class="ph-fill ph-storefront me-2" style="color: #16a34a;"></i>Customer</td>
                                        <td class="fw-bold" style="color: #1e293b; padding: 6px 0;" id="txt_customer">: -</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Perbandingan Harga Box --}}
                            <div class="d-flex align-items-center justify-content-between" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                                <div class="text-center w-100">
                                    <small class="d-block fw-bold mb-1" style="color: #64748b; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;">Current Price</small>
                                    <span class="fs-5 text-decoration-line-through fw-bold" style="color: #94a3b8;" id="txt_old_fee">-</span>
                                </div>

                                <div style="background: #f1f5f9; padding: 8px; border-radius: 50%; color: #94a3b8; margin: 0 10px;">
                                    <i class="ph-bold ph-arrow-right fs-4"></i>
                                </div>

                                <div class="text-center w-100" style="background: #fffbeb; padding: 10px; border-radius: 0.75rem; border: 1.5px dashed #fcd34d;">
                                    <small class="d-block fw-bold mb-1" style="color: #b45309; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px;"><i class="ph-fill ph-bell-ringing me-1"></i>New Request</small>
                                    <span class="fs-5 fw-bolder" style="color: #d97706;" id="txt_new_fee">-</span>
                                </div>
                            </div>

                            {{-- Catatan --}}
                            <div class="mb-2">
                                <label class="fw-bold mb-2" style="color: #475569; font-size: 0.85rem;"><i class="ph-fill ph-note-pencil me-1"></i> Decision Notes (Optional)</label>
                                <textarea id="notes" class="form-control" rows="3" placeholder="Type the reason if rejected, or approval notes if approved..." style="border-radius: 0.75rem; border: 1px solid #cbd5e1; padding: 0.75rem; font-size: 0.9rem; background: #f8fafc; resize: none;"></textarea>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-between" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem;">
                            <button type="button" class="btn px-4 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; border: none;" onclick="submitApproval('reject')"><i class="ph-bold ph-x-circle me-1"></i> Reject</button>

                            <button type="button" class="btn px-4 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; border: none;" onclick="submitApproval('approve')"><i class="ph-bold ph-check-circle me-1"></i> Approve</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        let table;
        $(document).ready(function() {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            table = $('#sampleTable').DataTable({
                processing: true, serverSide: true,
                ajax: "{{ route('logistic-fees.approval.list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'date', name: 'created_at' },
                    { data: 'customer', name: 'customer' },
                    { data: 'distributor', name: 'distributor' },
                    { data: 'old_fee', name: 'old_fee' },
                    { data: 'new_fee', name: 'new_fee' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    search: "",
                    searchPlaceholder: "🔍 Cari pengajuan...",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data"
                },
                drawCallback: function(settings) {
                    $('#sampleTable tbody td').css({
                        'padding': '1rem 1rem',
                        'vertical-align': 'middle',
                        'border-bottom': '1px solid #f1f5f9'
                    });
                }
            });

            // Styling Search Box DataTable secara inline
            $('.dataTables_filter input').css({
                'width': '250px',
                'margin-left': '10px',
                'border-radius': '50rem',
                'border': '1px solid #cbd5e1',
                'padding': '0.4rem 1rem',
                'background-color': '#ffffff'
            });

            // Tinjau Button
            $(document).on('click', '.btn-detail', function() {
                let id = $(this).data('id');
                Swal.fire({ title: 'Memuat...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
                $.get("{{ url('/logistic-fees-approval') }}/" + id, function(data) {
                    Swal.close();
                    $('#log_id').val(data.log_id);
                    $('#txt_distributor').text(': ' + data.distributor);
                    $('#txt_customer').text(': ' + data.customer);
                    $('#txt_old_fee').text(data.old_fee);
                    $('#txt_new_fee').text(data.new_fee);
                    $('#notes').val('').css('border-color', '#cbd5e1'); // Reset border color
                    $('#modalApproval').modal('show');
                });
            });

            // Resend Email Button
            $(document).on('click', '.btn-resend', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Resend Email?',
                    html: '<p style="font-size:0.9rem; color:#64748b;">Approval notification email will be resent to your email for reminder.</p>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: '<i class="ph-bold ph-paper-plane-right me-1"></i> Yes, Resend',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                        cancelButton: 'btn rounded-pill px-4 fw-bold shadow-sm'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Send...', didOpen: () => Swal.showLoading(), allowOutsideClick: false });
                        $.post("{{ url('/logistic-fees-approval/resend') }}/" + id, function(res) {
                            Swal.fire({
                                title: 'Success!', html: res.message, icon: 'success',
                                customClass: { confirmButton: 'btn rounded-pill px-4 fw-bold text-white' }, buttonsStyling: false
                            });
                        }).fail(function() { Swal.fire('Error', 'Failed to send email.', 'error'); });
                    }
                });
            });
        });

        function submitApproval(action) {
            let id = $('#log_id').val();
            let notes = $('#notes').val();
            let actText = action === 'approve' ? 'Approving' : 'Rejecting';

            // VALIDASI REJECT WAJIB ISI CATATAN
            if (action === 'reject' && notes.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Note Required!',
                    text: 'You must fill in the Decision Note (reason) if you reject this request.',
                    confirmButtonColor: '#ef4444',
                    customClass: { confirmButton: 'btn rounded-pill px-4 fw-bold text-white shadow-sm' }, buttonsStyling: false
                });

                $('#notes').css({'border-color': '#ef4444', 'box-shadow': '0 0 0 3px rgba(239, 68, 68, 0.2)'}).focus();
                setTimeout(() => $('#notes').css({'border-color': '#cbd5e1', 'box-shadow': 'none'}), 3000);
                return;
            }

            Swal.fire({
                title: actText + ' Request?',
                html: '<p style="font-size:0.9rem; color:#64748b;">Are you sure you want to ' + actText.toLowerCase() + ' this Logistic Fee request?</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Process',
                cancelButtonText: 'Cancel',
                confirmButtonColor: action === 'approve' ? '#10b981' : '#ef4444',
                cancelButtonColor: '#94a3b8',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn rounded-pill px-4 fw-bold border-0 shadow-sm text-white',
                    cancelButton: 'btn rounded-pill px-4 fw-bold shadow-sm'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    $.post("{{ url('/logistic-fees-approval/process') }}/" + id, { action: action, notes: notes }, function(res) {
                        $('#modalApproval').modal('hide');
                        table.ajax.reload();
                        Swal.fire({
                            title: 'Finished!', html: res.message, icon: 'success',
                            customClass: { confirmButton: 'btn rounded-pill px-4 fw-bold text-white shadow-sm' }, buttonsStyling: false
                        });
                    }).fail(function(err) {
                        let errMsg = 'An error occurred.';
                        if(err.responseJSON && err.responseJSON.errors && err.responseJSON.errors.notes) {
                            errMsg = err.responseJSON.errors.notes[0];
                        }
                        Swal.fire('Failed', errMsg, 'error');
                    });
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
