<x-app-layout>
    @section('title')
        Free Goods Approval
    @endsection

    @include('components.freegoods-table-styles')

    <div class="bg-white p-4 rounded shadow-sm">
        <div class="row m-1">
            <div class="col-12">
                <h4 class="main-title">Free Goods Approval</h4>
                <ul class="app-line-breadcrumbs mb-3">
                    <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-check-square f-s-16"></i> Approvals</a></li>
                    <li class="active"><a class="f-s-14 f-w-500" href="#">Free Goods List</a></li>
                </ul>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="main-table-container">
                    <div class="table-header-enhanced">
                        <h4 class="table-title"><i class="ph-duotone ph-list-checks"></i> Free Goods Action Center</h4>
                        <p class="table-subtitle">View and process all free goods requisition approval stages.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="w-100 display" id="fgApprovalTable">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>FG No.</th>
                                    <th>Requester</th>
                                    <th>Request Date</th>
                                    <th>Sub Category</th>
                                    <th>Status</th>
                                    <th>Approver NIK</th>
                                    <th>Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================== --}}
    {{-- MODAL DETAIL (Struktur HTML tidak berubah) --}}
    {{-- ========================================================== --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Free Goods Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">
                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6"><small class="view-label">Category</small><p class="view-data">FREE GOODS</p></div>
                                <div class="col-md-6"><small class="view-label">Sub Category</small><p class="view-data" id="view_sub_category">-</p></div>
                                <div class="col-md-3"><small class="view-label">FG No.</small><p class="view-data" id="view_no_srs">-</p></div>
                                <div class="col-md-3"><small class="view-label">Request Date</small><p class="view-data" id="view_request_date">-</p></div>
                                <div class="col-md-3"><small class="view-label">Customer Name</small><p class="view-data" id="view_customer_name">-</p></div>
                                <div class="col-md-3"><small class="view-label">Address</small><p class="view-data" id="view_customer_address">-</p></div>
                                <div class="col-md-3"><small class="view-label">Account</small><p class="view-data" id="view_account">-</p></div>
                                <div class="col-md-3"><small class="view-label">Cost Center</small><p class="view-data" id="view_cost_center">-</p></div>
                                <div class="col-md-6"><small class="view-label">Objectives</small><p class="view-data" id="view_objectives">-</p></div>
                                <div class="col-md-6"><small class="view-label">Estimated Potential</small><p class="view-data" id="view_estimated_potential">-</p></div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header"><h5 class="fw-bold text-warning mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5></div>
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100 mb-1">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Unit</th>
                                            <th class="text-center">Qty Required</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-tbody-fg"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 3: APPROVAL TRACKING --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container-fg">
                                {{-- Tracker akan di-generate oleh JavaScript --}}
                            </div>
                        </div>
                    </div>

                    {{-- CARD 4: REQUISITION HISTORY --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-warning mb-3"><i class="ph-bold ph-clock-counter-clockwise me-2"></i> Requisition History</h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush" id="history-log-container">
                                {{-- History akan diisi oleh JavaScript --}}
                            </ul>
                        </div>
                    </div>

                    {{-- Container untuk form aksi (approve/reject) yang dinamis --}}
                    <div id="viewModalActionFormContainer" class="mt-4"></div>
                </div>
                <div class="modal-footer" id="viewModalFooter">
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                const table = $('#fgApprovalTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('freegoods.approval.data') }}",
                    // [UPDATE] Urutan dan tambahan kolom disesuaikan seperti Sample
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '20px', className: 'text-center' },
                        { data: 'no_srs', name: 'requisition.no_srs' },
                        { data: 'requester', name: 'requisition.requester.name' }, // Kolom baru
                        { data: 'request_date', name: 'requisition.request_date' },
                        { data: 'sub_category', name: 'requisition.sub_category', className: 'text-center' },
                        { data: 'status', name: 'requisition.status', className: 'text-center' },
                        { data: 'approver_nik', name: 'approver_nik' },
                        { data: 'level', name: 'level', className: 'text-center' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', width: '120px' }
                    ],
                });

                //==================================================
                // FUNGSI UNTUK MENGISI MODAL DETAIL (Tidak diubah, hanya memastikan konsisten)
                //==================================================
                function populateViewForm(data) {
                    $('#view_sub_category').text(data.sub_category || '-');
                    $('#view_customer_name').text(data.customer ? data.customer.name : '-');
                    $('#view_customer_address').text(data.customer ? data.customer.address : '-');
                    $('#view_no_srs').text(data.no_srs || '-');
                    $('#view_account').text(data.account || '-');
                    $('#view_request_date').text(new Date(data.request_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) || '-');
                    $('#view_cost_center').text(data.cost_center || '-');
                    $('#view_objectives').text(data.objectives || '-');
                    $('#view_estimated_potential').text(data.estimated_potential || '-');

                    const viewItemTbody = $('#view-items-tbody-fg');
                    viewItemTbody.empty();
                    if (data.requisition_items && data.requisition_items.length > 0) {
                        data.requisition_items.forEach(item => {
                            let itemCode = item.item_master ? item.item_master.item_master_code : 'N/A';
                            let itemName = item.item_master ? item.item_master.item_master_name : 'N/A';
                            let unit = item.item_master ? item.item_master.unit : 'N/A';
                            const newRow = `<tr><td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td class="text-center">${item.quantity_required}</td><td class="text-center">${item.quantity_issued || '-'}</td></tr>`;
                            viewItemTbody.append(newRow);
                        });
                    } else {
                        viewItemTbody.html(`<tr><td colspan="5" class="text-center">No items have been added.</td></tr>`);
                    }

                    const status = data.status;
                    let badgeClass = 'bg-secondary';
                    if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-warning';
                    else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                    else if (['Rejected', 'Cancelled', 'Recalled'].includes(status)) badgeClass = 'bg-danger';
                    else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info';
                    $('#view_status_badge').html(`<span class="badge status-badge-lg fs-6 rounded-pill ${badgeClass}">${status}</span>`);

                    const trackerContainer = $('#approval-tracker-container-fg');
                    trackerContainer.empty();

                    let steps = [
                        { id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' },
                        { id: 'approver_1', label: 'Manager', icon: 'ph-user' },
                        { id: 'approver_2', label: 'Business Controller', icon: 'ph-briefcase' },
                        { id: 'outward', label: 'Outward WH', icon: 'ph-package' },
                        { id: 'completed', label: 'Completed', icon: 'ph-check-circle' }
                    ];

                    let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>';
                    steps.forEach(step => {
                        trackerHtml += `<div class="tracker-step" data-step-id="${step.id}"><div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div><div class="tracker-label">${step.label}</div><div class="tracker-details"></div></div>`;
                    });
                    trackerContainer.html(trackerHtml);

                    let lastCompletedIndex = -1;
                    const isRejected = ['Rejected', 'Cancelled', 'Recalled'].includes(data.status);

                    if (data.requester && data.created_at) {
                        const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                        const creationDate = new Date(data.created_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                        submittedStep.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${data.requester.name}</div><div class="tracker-date text-dark">${creationDate}</div>`);
                        lastCompletedIndex = 0;
                    }

                    if (data.approval_logs) {
                        data.approval_logs.forEach(log => {
                            const stepElement = $(`.tracker-step[data-step-id="approver_${log.level}"]`);
                            if (stepElement.length > 0) {
                                const logDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                if (log.status === 'Approved') {
                                    stepElement.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${log.approver.name}</div><div class="tracker-date text-dark">${logDate}</div>`);
                                    const stepIndex = steps.findIndex(s => s.id === `approver_${log.level}`);
                                    lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                                } else if (log.status === 'Rejected') {
                                     stepElement.addClass('rejected').find('.tracker-details').html(`<div class="tracker-user text-danger">${log.approver.name}</div><div class="tracker-date text-dark">${logDate}</div>`);
                                }
                            }
                        });
                    }

                    if (data.status === 'Processing') {
                        const whStep = $(`.tracker-step[data-step-id="outward"]`);
                        whStep.addClass('active');
                        lastCompletedIndex = 2;
                    } else if (data.status === 'Completed') {
                        $('.tracker-step').addClass('completed');
                        lastCompletedIndex = steps.length - 1;
                    }

                    if (lastCompletedIndex >= 0 && !isRejected) {
                        let progressPercentage = (lastCompletedIndex / (steps.length - 1)) * 100;
                        $('#tracker-progress').css('width', progressPercentage + '%');
                    }

                    const historyContainer = $('#history-log-container');
                    historyContainer.empty();
                    if (data.history && data.history.length > 0) {
                        data.history.forEach(log => {
                            let badgeClass = 'badge-created', avatarClass = 'avatar-created';
                            const action = log.action.toLowerCase();
                            if (action.includes('approved not review') || action.includes('approved')) { badgeClass = 'badge-approved'; avatarClass = 'avatar-approved'; }
                            else if (action.includes('approved with review')) { badgeClass = 'badge-review'; avatarClass = 'avatar-review'; }
                            else if (action.includes('rejected') || action.includes('cancelled') || action.includes('recalled')) { badgeClass = 'badge-rejected'; avatarClass = 'avatar-rejected'; }
                            else if (action.includes('completed step')) { badgeClass = 'badge-process'; avatarClass = 'avatar-process'; }
                            const logDate = new Date(log.timestamp).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                            const notesHtml = log.notes ? `<div class="history-notes">"${log.notes}"</div>` : '';
                            const historyItem = `<li class="list-group-item history-item"><div class="history-avatar ${avatarClass}">${log.actor.charAt(0).toUpperCase()}</div><div class="history-content"><div class="history-actor">${log.actor}</div>${notesHtml}</div><div class="history-meta"><div class="history-badge ${badgeClass}">${log.action}</div><div class="history-timestamp">${logDate}</div></div></li>`;
                            historyContainer.append(historyItem);
                        });
                    } else {
                        historyContainer.html('<li class="list-group-item">No history data available.</li>');
                    }
                }

                //==================================================
                // [UPDATE] SEMUA JAVASCRIPT AKSI DIGANTI DENGAN VERSI DARI SAMPLE
                //==================================================

                // --- Handler untuk Quick Approve (Approve Tanpa Review) ---
                $('#fgApprovalTable').on('click', '.action-btn', function(e) {
                    e.preventDefault();
                    const button = $(this);
                    const token = button.data('token');
                    const srs = button.data('srs');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Approve FG No. ${srs} without review?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3A6B35', // Warna hijau
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Approve!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const originalIcon = button.html();
                            $.ajax({
                                url: "{{ route('fg.approval.process') }}",
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    token: token,
                                    action: 'approve',
                                    notes: 'Approved without Review'
                                },
                                beforeSend: function() {
                                    button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                                },
                                success: function() {
                                    Swal.fire('Approved!', `Requisition ${srs} has been approved.`, 'success');
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                                },
                                complete: function() {
                                    button.html(originalIcon).prop('disabled', false);
                                }
                            });
                        }
                    });
                });

                // --- Handler untuk Review & Reject yang akan menampilkan Modal Detail ---
                $(document).on('click', '.action-btn-modal', function() {
                    const button = $(this);
                    const requisitionId = button.data('id');
                    const token = button.data('token');
                    const action = button.data('action');
                    const srs = button.data('srs');

                    const originalIcon = button.html();
                    button.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

                    $.ajax({
                        url: `/freegoods-form/${requisitionId}`, // URL disesuaikan
                        type: 'GET',
                        success: function(response) {
                            populateViewForm(response);

                            const isReject = action === 'reject';
                            const modalTitle = isReject ? 'Reject Requisition' : 'Approve with Review';
                            const btnClass = isReject ? 'btn-danger' : 'btn-primary-theme';
                            const btnText = isReject ? 'Submit Reject' : 'Submit Approve';
                            const notesPlaceholder = isReject ? 'Provide reason for rejection...' : 'Provide review notes...';
                            const notesLabel = isReject ? 'Rejection Reason' : 'Review Notes';

                            $('#viewModalLabel').text(`${modalTitle}: ${srs}`);

                            const actionFormHtml = `
                                <hr>
                                <form id="modalResponseForm" action="{{ route('fg.approval.process') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="${token}">
                                    <input type="hidden" name="action" value="${isReject ? 'reject' : 'review'}">
                                    <div class="card view-modal-card">
                                        <div class="card-header view-modal-card-header border-bottom">
                                            <h5 class="fw-bold text-warning mb-0"><i class="ph-bold ph-note-pencil me-2"></i>Notes</h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <label for="modal_notes" class="form-label fw-bold">${notesLabel} <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="modal_notes" name="notes" rows="4" placeholder="${notesPlaceholder}" required></textarea>
                                            <div class="form-text">Notes are required to proceed.</div>
                                        </div>
                                    </div>
                                </form>
                            `;
                            const submitButtonHtml = `<button type="submit" form="modalResponseForm" class="btn ${btnClass}">${btnText}</button>`;

                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalFooter').prepend(submitButtonHtml);

                            $('#viewModal').modal('show');
                        },
                        error: function() {
                            alert('Failed to fetch requisition details.');
                        },
                        complete: function() {
                            button.html(originalIcon).prop('disabled', false);
                        }
                    });
                });

                // --- Handler untuk submit form dari dalam modal ---
                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const notesField = $('#modal_notes');
                    const submitButton = $('#viewModalFooter button[type="submit"]');

                    // Validasi notes yang lebih baik
                    if (!/[a-zA-Z]/.test(notesField.val())) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid Notes',
                            text: 'Please provide a clear reason. Notes cannot only contain spaces, numbers, or symbols.'
                        });
                        return;
                    }

                    const action = form.find('input[name="action"]').val();
                    const isReject = action === 'reject';
                    const confirmTitle = isReject ? 'Confirm Rejection' : 'Confirm Approval';
                    const confirmText = isReject ? 'Are you sure you want to REJECT this requisition?' : 'Are you sure you want to APPROVE this requisition with your review?';
                    const confirmButtonText = isReject ? 'Yes, Reject It!' : 'Yes, Approve It!';

                    Swal.fire({
                        title: confirmTitle,
                        text: confirmText,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: isReject ? '#d33' : '#3A6B35',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmButtonText
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                method: 'POST',
                                data: form.serialize(),
                                beforeSend: function() {
                                    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...').prop('disabled', true);
                                },
                                success: function(response) {
                                    $('#viewModal').modal('hide');
                                    // Pesan sukses dari controller lebih diutamakan
                                    Swal.fire('Success!', response.message || 'Action was successful.', 'success');
                                    table.ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', xhr.responseJSON?.message || 'An unknown error occurred.', 'error');
                                },
                                complete: function() {
                                     // Kembalikan tombol ke kondisi semula jika terjadi error
                                     const btnText = isReject ? 'Submit Reject' : 'Submit Approve';
                                     submitButton.html(btnText).prop('disabled', false);
                                }
                            });
                        }
                    });
                });

                // --- Handler untuk membersihkan modal setelah ditutup ---
                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove();
                    $('#viewModalLabel').html('<i class="ph-bold ph-file-text me-2"></i>Free Goods Requisition Details');
                });
            });
        </script>
    @endpush
</x-app-layout>
