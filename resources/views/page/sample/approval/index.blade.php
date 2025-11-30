<x-app-layout>
    @section('title')
        Requisition Approval
    @endsection

    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Requisition Approval</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li><a class="f-s-14 f-w-500" href="#"><i class="ph-duotone ph-check-square f-s-16"></i> Approvals</a></li>
                <li class="active"><a class="f-s-14 f-w-500" href="#">Requisition List</a></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-list-checks"></i> Requisition Action Center</h4>
                    <p class="table-subtitle">View and process all requisition approval stages.</p>
                </div>
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>SRS No.</th>
                                <th>Requester</th>
                                <th>Request Date</th>
                                <th>Sub Category</th>
                                <th>Status</th>
                                <th>Approver</th>
                                <th>Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Sample Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8faf8ff;">
                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6"><small class="view-label">Category</small><p class="view-data">SAMPLE</p></div>
                                <div class="col-md-6"><small class="view-label">Sub Category</small><p class="view-data" id="view_sub_category">-</p></div>
                                <div class="col-md-3"><small class="view-label">SRS No.</small><p class="view-data" id="view_no_srs">-</p></div>
                                <div class="col-md-3"><small class="view-label">Request Date</small><p class="view-data" id="view_request_date">-</p></div>
                                <div class="col-md-3"><small class="view-label">Customer Name</small><p class="view-data" id="view_customer_name">-</p></div>
                                <div class="col-md-3"><small class="view-label">Address</small><p class="view-data" id="view_customer_address">-</p></div>
                                <div class="col-md-3"><small class="view-label">Account</small><p class="view-data" id="view_account">-</p></div>
                                <div class="col-md-3"><small class="view-label">Cost Center</small><p class="view-data" id="view_cost_center">-</p></div>
                                <div class="col-md-3"><small class="view-label">Objectives</small><p class="view-data" id="view_objectives">-</p></div>
                                <div class="col-md-3"><small class="view-label">Estimated Potential</small><p class="view-data" id="view_estimated_potential">-</p></div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header"><h5 class="fw-bold text-primary mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5></div>
                        <div class="card-body p-1">
                            <div class="table-responsive">
                                <table class="table table-bordered w-100 mb-1">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th class="material-type-column">Material Type</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Unit</th>
                                            <th class="text-center">Qty Required</th>
                                            <th class="text-center">Qty Issued</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view-items-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Sisa card lainnya (Special Order, QA, Tracking, History) tetap sama --}}
                    <div class="card view-modal-card" id="view-special-order-section" style="display: none;">
                        <div class="card-header">
                           <h5 class="fw-bold text-primary mb-3">Special Order Details (Marketing)</h5>
                        </div>
                        <div class="card-body">
                             <div class="row g-4">
                                <div class="col-md-4"><label class="text-muted">Sample Completion Date</label><p class="fs-6 fw-semibold" id="view_requested_date">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Sample Weight</label><p class="fs-6 fw-semibold" id="view_weight_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Sample Packaging</label><p class="fs-6 fw-semibold" id="view_packaging_selection">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Number of Samples</label><p class="fs-6 fw-semibold" id="view_sample_count">-</p></div>
                                <div class="col-md-4"><label class="text-muted">COA Required?</label><p class="fs-6 fw-semibold" id="view_coa_required">-</p></div>
                                <div class="col-md-4"><label class="text-muted">Shipment Method</label><p class="fs-6 fw-semibold" id="view_shipment_method">-</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card" id="view-qa-section" style="display: none;">
                        <div class="card-header view-modal-card-header">
                           <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-test-tube me-2"></i> QA/QM Details</h5>
                        </div>
                        <div class="card-body p-4">
                             <div class="row g-4">
                                <div class="col-md-4">
                                    <small class="view-label">Asal Sample</small>
                                    <p class="view-data" id="view_source">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Tanggal Produksi</small>
                                    <p class="view-data" id="view_production_date">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Persiapan Sample</small>
                                    <p class="view-data" id="view_preparation_method">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Keterangan Sample</small>
                                    <p class="view-data" id="view_description">-</p>
                                </div>
                                <div class="col-md-4">
                                    <small class="view-label">Keterangan Tambahan</small>
                                    <p class="view-data fst-italic" id="view_sample_notes">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-path me-2"></i> Approval & Process Tracking</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold me-3">Current Status:</span>
                                <div id="view_status_badge"></div>
                            </div>
                            <div class="tracker-container" id="approval-tracker-container">
                                <div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>
                                <div class="tracker-step" data-step-name="Submitted"><div class="tracker-icon"><i class="ph-bold ph-file-arrow-up fs-6"></i></div><div class="tracker-label">Submitted</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Manager Approval"><div class="tracker-icon"><i class="ph-bold ph-user-plus fs-6"></i></div><div class="tracker-label">Manager</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Business Controller Approval"><div class="tracker-icon"><i class="ph-bold ph-briefcase fs-6"></i></div><div class="tracker-label">Business Controller</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Warehouse Processing"><div class="tracker-icon"><i class="ph-bold ph-package fs-6"></i></div><div class="tracker-label">Warehouse</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Ready for Dispatch"><div class="tracker-icon"><i class="ph-bold ph-truck fs-6"></i></div><div class="tracker-label">Dispatch</div><div class="tracker-details"></div></div>
                                <div class="tracker-step" data-step-name="Completed"><div class="tracker-icon"><i class="ph-bold ph-check-circle fs-6"></i></div><div class="tracker-label">Completed</div><div class="tracker-details"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-clock-counter-clockwise me-2"></i> Requisition History</h5>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-group list-group-flush" id="history-log-container">
                                {{-- History akan diisi oleh JavaScript di sini --}}
                            </ul>
                        </div>
                    </div>
                    <div id="viewModalActionFormContainer" class="mt-4"></div>
                </div>
                <div class="modal-footer" id="viewModalFooter">
                    {{-- [PENTING] Container untuk form aksi --}}
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('sample.approval.data') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '20px', },
                        { data: 'no_srs', name: 'requisition.no_srs' },
                        { data: 'requester', name: 'requisition.requester.name' },
                        { data: 'request_date', name: 'requisition.request_date' },
                        { data: 'sub_category', name: 'requisition.sub_category', },
                        { data: 'status', name: 'requisition.status', },
                        { data: 'approver', name: 'approval_logs.approver_nik' },
                        { data: 'level', name: 'level', },
                        { data: 'action', name: 'action', orderable: false, searchable: false, width: '120px' }
                    ],
                    drawCallback: function () {
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });
                    }
                });

                $('#sampleTable_filter input').attr({
                    'placeholder': '🔍 Search sample...',
                    'class': 'form-control'
                });

                //==================================================
                // FUNGSI UNTUK MENGISI MODAL DETAIL (DIAMBIL DARI sample/index.blade.php)
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
                    const viewItemTbody = $('#view-items-tbody');
                    const viewTable = viewItemTbody.closest('table');
                    viewItemTbody.empty();
                    const isPackaging = data.sub_category === 'Packaging';
                    viewTable.find('th.material-type-column').toggle(isPackaging);
                    if (data.requisition_items && data.requisition_items.length > 0) {
                        data.requisition_items.forEach(item => {
                            let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A';
                            if (item.item_detail) {
                                itemCode = item.item_detail.item_detail_code;
                                itemName = item.item_detail.item_detail_name;
                                unit = item.item_detail.unit;
                            } else if (item.item_master) {
                                itemCode = item.item_master.item_master_code;
                                itemName = item.item_master.item_master_name;
                                unit = item.item_master.unit;
                            }
                            const materialTypeCell = isPackaging ? `<td class="material-type-column">${item.material_type}</td>` : '';
                            const newRow = `<tr>${materialTypeCell}<td>${itemCode}</td><td>${itemName}</td><td>${unit}</td><td class="text-center">${item.quantity_required}</td><td class="text-center">${item.quantity_issued || '-'}</td></tr>`;
                            viewItemTbody.append(newRow);
                        });
                    } else {
                        const colspan = isPackaging ? 6 : 5;
                        viewItemTbody.html(`<tr><td colspan="${colspan}" class="text-center">No items have been added.</td></tr>`);
                    }
                    const specialOrderSection = $('#view-special-order-section');
                    const qaSection = $('#view-qa-section');
                    if (data.sub_category === 'Special Order' && data.requisition_special) {
                        const special = data.requisition_special;
                        $('#view_requested_date').text(special.requested_date ? new Date(special.requested_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
                        $('#view_weight_selection').text(special.weight_selection || '-');
                        $('#view_packaging_selection').text(special.packaging_selection || '-');
                        $('#view_sample_count').text(special.sample_count || '-');
                        $('#view_shipment_method').text(special.shipment_method || '-');
                        $('#view_coa_required').text(special.coa_required == 1 ? 'Yes' : 'No');
                        specialOrderSection.show();
                        if (special.source) {
                            $('#view_source').text(special.source || '-');
                            $('#view_description').text(special.description || '-');
                            $('#view_production_date').text(special.production_date ? new Date(special.production_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : '-');
                            $('#view_preparation_method').text(special.preparation_method || '-');
                            $('#view_sample_notes').text(special.sample_notes || '-');
                            qaSection.show();
                        } else {
                            qaSection.hide();
                        }
                    } else {
                        specialOrderSection.hide();
                        qaSection.hide();
                    }
                    const status = data.status;
                    let badgeClass = 'bg-secondary';
                    if (['Submitted', 'Pending'].includes(status)) badgeClass = 'bg-primary';
                    else if (status.includes('Approved') || status === 'Completed') badgeClass = 'bg-success';
                    else if (['Rejected', 'Recalled'].includes(status)) badgeClass = 'bg-danger';
                    else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-warning text-dark';
                    $('#view_status_badge').html(`<span class="badge fs-6 rounded-pill ${badgeClass}">${status}</span>`);
                    const trackerContainer = $('#approval-tracker-container');
                    trackerContainer.empty();
                    let steps = [{ id: 'submitted', label: 'Request Submit', icon: 'ph-file-arrow-up' }];
                    if (data.sequence_approvers) {
                        data.sequence_approvers.forEach((role, index) => {
                            const level = index + 1;
                            const stepTitle = (role === 'atasan') ? 'Atasan Dept' : 'Bisnis Controller';
                            steps.push({ id: `approver_${level}`, label: stepTitle, icon: 'ph-user' });
                        });
                    }
                    if (data.status !== 'Rejected' && data.status !== 'Recalled') {
                        if (data.sub_category === 'Packaging') {
                            if (data.print_batch == 1) {
                                steps.push({ id: 'inward_initial', label: 'Inward (Initial)', icon: 'ph-package' });
                                steps.push({ id: 'material', label: 'Material Support', icon: 'ph-printer' });
                                steps.push({ id: 'inward_final', label: 'Inward (Final)', icon: 'ph-package' });
                            } else {
                                steps.push({ id: 'inward_final', label: 'Inward Check', icon: 'ph-package' });
                            }
                        } else if (data.sub_category === 'Finished Goods') {
                            steps.push({ id: 'outward', label: 'Outward', icon: 'ph-truck' });
                        } else if (data.sub_category === 'Special Order') {
                            steps.push({ id: 'qa_form', label: 'QA/QM Form', icon: 'ph-clipboard-text' });
                        }
                        steps.push({ id: 'completed', label: 'Completed', icon: 'ph-check-circle' });
                    }
                    let trackerHtml = '<div class="tracker-line"><div class="tracker-line-progress" id="tracker-progress"></div></div>';
                    steps.forEach(step => {
                        trackerHtml += `<div class="tracker-step" data-step-id="${step.id}"><div class="tracker-icon"><i class="ph-bold ${step.icon} fs-6"></i></div><div class="tracker-label">${step.label}</div><div class="tracker-details"></div></div>`;
                    });
                    trackerContainer.html(trackerHtml);
                    let lastCompletedIndex = -1;
                    const isRejected = ['Rejected', 'Recalled'].includes(data.status);
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
                                if (log.status === 'Approved') {
                                    const approvalDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                    stepElement.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${log.approver.name}</div><div class="tracker-date text-dark">${approvalDate}</div>`);
                                    const stepIndex = steps.findIndex(s => s.id === `approver_${log.level}`);
                                    lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                                } else if (log.status === 'Rejected') {
                                    const rejectionDate = new Date(log.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                    stepElement.addClass('rejected').find('.tracker-details').html(`<div class="tracker-user text-danger">${log.approver.name}</div><div class="tracker-date text-dark">${rejectionDate}</div>`);
                                }
                            }
                        });
                    }
                    if (data.trackings && data.trackings.length > 0) {
                        const positionToStepId = { 'Inward WH Supervisor (Initial Check)': 'inward_initial', 'Material Support Supervisor': 'material', 'Inward WH Supervisor (Final Check)': 'inward_final', 'Outward WH Supervisor': 'outward', 'Waiting for QA/QM Form': 'qa_form' };
                        data.trackings.forEach(tracking => {
                            if (tracking.last_updated && new Date(tracking.last_updated).getFullYear() > 1970) {
                                const stepId = positionToStepId[tracking.current_position];
                                if (stepId) {
                                    const stepElement = $(`.tracker-step[data-step-id="${stepId}"]`);
                                    const userName = (stepId === 'qa_form') ? 'QA/QM HSE Team' : tracking.current_position;
                                    const completionDate = new Date(tracking.last_updated).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                                    stepElement.addClass('completed').find('.tracker-details').html(`<div class="tracker-user text-primary">${userName}</div><div class="tracker-date text-dark">${completionDate}</div>`);
                                    const stepIndex = steps.findIndex(s => s.id === stepId);
                                    lastCompletedIndex = Math.max(lastCompletedIndex, stepIndex);
                                }
                            }
                        });
                    }
                    if (data.status === 'Completed') {
                        const completedStep = $(`.tracker-step[data-step-id="completed"]`);
                        const completionDate = new Date(data.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                        completedStep.find('.tracker-details').html(`<div class="tracker-user text-primary">${data.requester.name}</div><div class="tracker-date text-dark">${completionDate}</div>`);
                        $('.tracker-step').addClass('completed');
                        lastCompletedIndex = steps.length - 1;
                    } else if (data.status === 'Recalled') {
                        const submittedStep = $(`.tracker-step[data-step-id="submitted"]`);
                        const recallDate = new Date(data.updated_at).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '');
                        submittedStep.addClass('rejected').find('.tracker-details').html(`<div class="tracker-user text-danger">${data.requester.name}</div><div class="tracker-date text-dark">${recallDate}</div>`);
                    } else if (!isRejected) {
                        const nextStepIndex = lastCompletedIndex + 1;
                        if (nextStepIndex < steps.length) {
                            const activeStepElement = trackerContainer.find('.tracker-step').eq(nextStepIndex);
                            activeStepElement.addClass('active');
                            if (data.route_to) {
                                activeStepElement.find('.tracker-details').html(`<div class="tracker-user" style="color: #ffc107; font-weight: 500;"><i class="ph-bold ph-arrow-circle-right me-1"></i>Processed by</div><div class="tracker-date text-dark">${data.route_to}</div>`);
                            }
                        }
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
                            if (action.includes('approved not review')) { badgeClass = 'badge-approved'; avatarClass = 'avatar-approved'; }
                            else if (action.includes('approved with review')) { badgeClass = 'badge-review'; avatarClass = 'avatar-review'; }
                            else if (action.includes('rejected') || action.includes('Recalled')) { badgeClass = 'badge-rejected'; avatarClass = 'avatar-rejected'; }
                            else if (action.includes('completed step')) { badgeClass = 'badge-process'; avatarClass = 'avatar-process'; }
                            let avatarHtml = '', actorInitial = log.actor ? log.actor.charAt(0).toUpperCase() : '?';
                            if (log.avatar) {
                                avatarClass += ' has-image';
                                avatarHtml = `<img src="${log.avatar}" alt="${actorInitial}">`;
                            } else {
                                avatarHtml = actorInitial;
                            }
                            const notesHtml = log.notes ? `<div class="history-notes">"${log.notes}"</div>` : '';
                            const logDate = new Date(log.timestamp).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                            const historyItem = `<li class="list-group-item history-item"><div class="history-avatar ${avatarClass}">${avatarHtml}</div><div class="history-content"><div class="history-actor">${log.actor}</div>${notesHtml}</div><div class="history-meta"><div class="history-badge ${badgeClass}">${log.action}</div><div class="history-timestamp">${logDate}</div></div></li>`;
                            historyContainer.append(historyItem);
                        });
                    } else {
                        historyContainer.html('<li class="list-group-item">No history data available.</li>');
                    }
                }

                //==================================================
                // JAVASCRIPT BARU UNTUK MENANGANI AKSI APPROVAL
                //==================================================

                // --- Handler untuk Quick Approve (Approve Tanpa Review) ---
                $('#sampleTable').on('click', '.action-btn', function(e) {
                    e.preventDefault();
                    const button = $(this); // Simpan referensi ke tombol
                    const token = button.data('token');
                    const srs = button.data('srs');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Approve SRS ${srs} without review?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Approve!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const originalIcon = button.html(); // Simpan ikon asli

                            $.ajax({
                                url: "{{ route('approval-sample.process-form') }}",
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    token: token,
                                    action: 'approve',
                                    notes: 'Approved without Review'
                                },
                                // [BARU] Tampilkan spinner sebelum AJAX dikirim
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
                                // [BARU] Kembalikan tombol ke kondisi semula setelah AJAX selesai (baik sukses maupun error)
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
                        url: `/sample-form/${requisitionId}`,
                        type: 'GET',
                        success: function(response) {
                            populateViewForm(response);

                            const isReject = action === 'reject';
                            const modalTitle = isReject ? 'Reject Requisition' : 'Approve with Review';
                            const btnClass = isReject ? 'btn-danger' : 'btn-primary';
                            const btnText = isReject ? 'Submit Reject' : 'Submit Approve with Review';
                            const notesPlaceholder = isReject ? 'Provide reason for rejection...' : 'Provide review notes...';
                            const notesLabel = isReject ? 'Rejection Reason' : 'Review Notes';

                            $('#viewModalLabel').text(`${modalTitle}: ${srs}`);

                            // [MODIFIKASI 1] Form sekarang TIDAK lagi berisi tombol submit
                            const actionFormHtml = `
                                <hr>
                                <form id="modalResponseForm" action="{{ route('approval-sample.process-form') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="${token}">
                                    <input type="hidden" name="action" value="${action}">

                                    <div class="card view-modal-card">
                                        <div class="card-header view-modal-card-header border-bottom">
                                            <h5 class="fw-bold text-primary mb-0"><i class="ph-bold ph-note-pencil me-2"></i>Notes</h5>
                                        </div>
                                        <div class="card-body p-4">
                                            <label for="modal_notes" class="form-label fw-bold">${notesLabel} <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="modal_notes" name="notes" rows="4" placeholder="${notesPlaceholder}" required></textarea>
                                            <div class="form-text">Notes are required to proceed.</div>
                                        </div>
                                    </div>
                                </form>
                            `;

                            // [MODIFIKASI 2] Buat HTML untuk tombol submit secara terpisah
                            // Atribut 'form="modalResponseForm"' akan men-trigger submit form walau tombol ada di luar tag <form>
                            const submitButtonHtml = `<button type="submit" form="modalResponseForm" class="btn ${btnClass}">${btnText}</button>`;

                            // [MODIFIKASI 3] Tempatkan form di body, dan tombol di footer
                            $('#viewModalActionFormContainer').html(actionFormHtml);
                            $('#viewModalFooter').prepend(submitButtonHtml); // .prepend() menaruhnya di awal (kiri)

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

                $('#sampleTable').on('click', '.btn-resend-email', function() {
                    const button = $(this);
                    const token = button.data('token');

                    Swal.fire({
                        title: 'Resend Email?',
                        text: "This will send the approval notification email again. Continue?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        confirmButtonText: 'Yes, Resend!',
                        cancelButtonText: 'Recall'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/approvals/resend/${token}`, // Sesuai dengan route yang dibuat
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                beforeSend: function() {
                                    button.prop('disabled', true).find('i').addClass('spinner-border spinner-border-sm').removeClass('ph-paper-plane-tilt');
                                },
                                success: function(response) {
                                    Swal.fire('Success!', response.message, 'success');
                                },
                                error: function(xhr) {
                                    const errorMsg = xhr.responseJSON?.message || 'An error occurred.';
                                    Swal.fire('Error!', errorMsg, 'error');
                                },
                                complete: function() {
                                    button.prop('disabled', false).find('i').removeClass('spinner-border spinner-border-sm').addClass('ph-paper-plane-tilt');
                                }
                            });
                        }
                    });
                });

                $(document).on('submit', '#modalResponseForm', function(e) {
                    e.preventDefault(); // Selalu hentikan submit standar

                    const form = $(this);
                    const notesField = $('#modal_notes');
                    const submitButton = $('#viewModalFooter button[type="submit"]');

                    const notesValue = notesField.val();
                    // Regex ini akan memeriksa apakah ada setidaknya SATU huruf (a-z, A-Z) di dalam string.
                    if (!/[a-zA-Z]/.test(notesValue)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Catatan Tidak Valid',
                            text: 'Mohon berikan alasan yang jelas. Catatan tidak boleh hanya berisi spasi, angka, atau simbol.'
                        });
                        return; // Hentikan proses jika tidak valid
                    }

                    const action = form.find('input[name="action"]').val();
                    const isReject = action === 'reject';
                    const confirmTitle = isReject ? 'Confirm Rejection' : 'Confirm Approval';
                    const confirmText = isReject
                        ? 'Are you sure you want to REJECT this requisition?'
                        : 'Are you sure you want to APPROVE this requisition?';
                    const confirmButtonText = isReject ? 'Yes, Reject It!' : 'Yes, Approve It!';

                    Swal.fire({
                        title: confirmTitle,
                        text: confirmText,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: isReject ? '#d33' : '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmButtonText
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, kirim data via AJAX
                            $.ajax({
                                url: form.attr('action'),
                                method: form.attr('method'),
                                data: form.serialize(), // Ambil semua data dari form
                                beforeSend: function() {
                                    // Tampilkan status loading
                                    submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...').prop('disabled', true);
                                },
                                success: function(response) {
                                    // Jika sukses
                                    $('#viewModal').modal('hide'); // Tutup modal
                                    Swal.fire('Success!', response.message, 'success'); // Tampilkan pesan sukses
                                    table.ajax.reload(null, false); // Refresh DataTable
                                },
                                error: function(xhr) {
                                    // Jika error
                                    const errorMsg = xhr.responseJSON?.message || 'An unknown error occurred.';
                                    Swal.fire('Error!', errorMsg, 'error');
                                },
                                complete: function() {
                                    // Kembalikan tombol ke kondisi semula (jika terjadi error)
                                    const btnText = isReject ? 'Submit Reject' : 'Submit Approve with Review';
                                    submitButton.html(btnText).prop('disabled', false);
                                }
                            });
                        }
                    });
                });

                // [MODIFIKASI 4] Saat modal ditutup, bersihkan form DAN tombol submit yang dinamis
                $('#viewModal').on('hidden.bs.modal', function () {
                    $('#viewModalActionFormContainer').empty();
                    $('#viewModalFooter button[type="submit"]').remove(); // Hapus tombol submit
                    $('#viewModalLabel').html('<i class="ph-bold ph-file-text me-2"></i>Sample Requisition Details');
                });
            });
        </script>
    @endpush
</x-app-layout>
