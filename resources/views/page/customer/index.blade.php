<x-app-layout>
    @section('title')
        Customer List
    @endsection

    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Approvals Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Approvals
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Approvals List</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>

                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Account</option>
                        @foreach($accountStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <select id="approvalStatusFilter" class="form-select select2" style="width: 175px;">
                        <option value="all">All Approval</option>
                        @foreach($approvalStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <button id="resetFilters" class="btn btn-sm btn-secondary border" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                <div class="ms-auto d-flex">
                    <button class="btn btn-primary" type="button" id="btn-create-customer">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Customer</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="table-title mb-1">
                            <i class="ph-duotone ph-users-three me-2"></i> Customer List
                        </h4>
                        <small class="text-white opacity-75 f-s-12">
                            Manage customer data, credit limits, and monitor approval progress.
                        </small>
                    </div>

                    <div class="d-none d-md-flex gap-4 text-white align-items-center pe-2">
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Waiting for Approval">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-clock-countdown text-warning f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Pending</span>
                                <span class="f-s-14 fw-bold">{{ $pendingCount }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Processing">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-gear text-info f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Processing</span>
                                <span class="f-s-14 fw-bold">{{ $processingCount }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Approved (Administrative)">
                             <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="ph-fill ph-seal-check text-success f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Approved</span>
                                <span class="f-s-14 fw-bold">{{ $approvedCount }}</span>
                            </div>
                        </div>

                        <div class="vr opacity-100 bg-white" style="height: 50px;"></div>

                        {{-- Stat 3: BG Status --}}
                        <div class="d-flex align-items-center gap-4">

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Customers with Bank Garansi">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                    <i class="ph-fill ph-file-text text-success f-s-18"></i>
                                </div>
                                <div class="d-flex flex-column line-height-sm">
                                    <span class="f-s-11 opacity-75 text-uppercase fw-bold">BG Active</span>
                                    <span class="f-s-14 fw-bold">{{ $activeCount }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Customers without Bank Garansi">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                    <i class="ph-fill ph-prohibit text-danger f-s-18"></i>
                                </div>
                                <div class="d-flex flex-column line-height-sm">
                                    <span class="f-s-11 opacity-75 text-uppercase fw-bold">No BG</span>
                                    <span class="f-s-14 fw-bold">{{ $inactiveCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="customerTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Code</th>
                                <th>Customer</th>
                                <th>Credit</th>
                                <th>TOP</th>
                                <th class="text-center">Status</th>
                                <th>Route To</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl" style="width: 100%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="customerModalLabel">Create New Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="customerForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        {{-- STEP 1: Select User --}}
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-light-primary">
                                <h6 class="mb-2 fw-bold text-primary">Requester Info</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="user_id" class="form-label">Select User <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2-styled" id="user_id" name="user_id"
                                            style="width: 100%;" required>
                                            <option></option>
                                            @foreach ($sales as $user)
                                                <option value="{{ $user->id }}"
                                                    data-pos="{{ $user->user->position?->position_name ?? ($user->user->position_name ?? '') }}"
                                                    data-branch="{{ $user->branch?->branch_name ?? '' }}"
                                                    data-region="{{ $user->region?->region_name ?? '' }}">
                                                    {{ $user->user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: User Details (Read Only) --}}
                        <div id="user-info-section" style="display: none;">
                            <div class="card mb-3 bg-light">
                                <div class="card-body py-2">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="small text-muted">Position</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="user_position" readonly placeholder="Auto-filled">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small text-muted">Branch</label>
                                            <input type="text" class="form-control form-control-sm" id="user_branch"
                                                readonly placeholder="Auto-filled">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small text-muted">Region</label>
                                            <input type="text" class="form-control form-control-sm" id="user_region"
                                                readonly placeholder="Auto-filled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 4: Main Form Details --}}
                        <div id="main-form-section" style="display: none;">
                            <div class="card mb-3 border-primary">
                                <div class="card-header bg-light-success">
                                    <h6 class="mb-2 fw-bold text-white">Customer Detail</h6>
                                </div>

                                {{-- Account Group & Class --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="account_group" class="form-label">Account Group <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" id="account_group"
                                                name="account_group" style="width: 100%;" required>
                                                <option></option>
                                                @foreach ($accountgroup as $ag)
                                                    <option value="{{ $ag->id }}"
                                                        data-bank_garansi="{{ $ag->bank_garansi }}"
                                                        data-ccar="{{ $ag->ccar }}">
                                                        {{ $ag->name_account_group ?? $ag->name_account_group }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="customer_class" class="form-label">Customer Class <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" id="customer_class"
                                                name="customer_class" style="width: 100%;" required>
                                                <option></option>
                                                @foreach ($customerClass as $cc)
                                                    <option value="{{ $cc->id }}">
                                                        {{ $cc->name_class ?? $cc->name_class }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- D. Documents Upload (MOVED UP HERE) --}}
                                <div class="card-body bg-opacity-10">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary"><i class="ph-bold ph-upload-simple"></i>
                                            Document Uploads (Auto-fill Support)</h6>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload NPWP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_npwp" required>
                                            <small class="text-muted f-s-11">Upload NPWP untuk auto-fill nama &
                                                alamat.</small>
                                            <div id="preview_npwp" class="mt-2" style="display: none;">
                                                <a href="#" target="_blank" class="btn btn-sm btn-outline-primary file-link">
                                                    <i class="ph-bold ph-file-text me-1"></i> View Uploaded NPWP
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload NIB/SIUP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_nib" required>
                                            <div id="preview_nib" class="mt-2" style="display: none;">
                                                <a href="#" target="_blank" class="btn btn-sm btn-outline-primary file-link">
                                                    <i class="ph-bold ph-file-text me-1"></i> View Uploaded NIB
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload KTP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_ktp" required>
                                            <div id="preview_ktp" class="mt-2" style="display: none;">
                                                <a href="#" target="_blank" class="btn btn-sm btn-outline-primary file-link">
                                                    <i class="ph-bold ph-id-card me-1"></i> View Uploaded KTP
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- A. General Info --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary">General Information</h6>
                                        <div class="col-md-6">
                                            <label class="form-label">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="e.g. PT. Maju Mundur Cantik" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sort Name</label>
                                            <input type="text" class="form-control" name="sort_name" id="sort_name"
                                                placeholder="e.g. MMC">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Address <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control mb-2" name="address1"
                                                id="address1" placeholder="Address Line 1 (Required)" required>
                                            <input type="text" class="form-control mb-2" name="address2"
                                                id="address2" placeholder="Address Line 2 (Optional)">
                                            <input type="text" class="form-control" name="address3" id="address3"
                                                placeholder="Address Line 3 (Optional)">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor PKD <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-light" name="no_pkd" id="no_pkd" placeholder="No PKD akan otomatis tergenerate oleh sistem" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Postal Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="postal_code"
                                                id="postal_code" placeholder="e.g. 12345" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                placeholder="e.g. Jakarta Selatan" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Area <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="area" id="area"
                                                placeholder="e.g. Jabodetabek" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email (General) <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="e.g. info@company.com" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Country <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="Indonesia" placeholder="Country" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- B. Shipping & Management --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary">Shipping & Key Personnel</h6>
                                        <div class="col-md-6">
                                            <label class="form-label">Shipping To (Name) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="shipping_to_name"
                                                id="shipping_to_name" placeholder="Recipient Name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Shipping To (Address) <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="shipping_to_address" id="shipping_to_address" rows="1"
                                                placeholder="Full Shipping Address" required></textarea>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Purchasing Mgr Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="purchasing_manager_name"
                                                id="purchasing_manager_name" placeholder="Full Name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Purchasing Mgr Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="purchasing_manager_email"
                                                id="purchasing_manager_email" placeholder="email@example.com"
                                                required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Finance Mgr Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="finance_manager_name"
                                                id="finance_manager_name" placeholder="Full Name" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Finance Mgr Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="finance_manager_email"
                                                id="finance_manager_email" placeholder="email@example.com" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- C. Billing & Tax --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary">Billing (Penagihan) & Tax</h6>
                                        <div class="col-md-4">
                                            <label class="form-label">Kontak Penagihan <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="penagihan_nama_kontak"
                                                id="penagihan_nama_kontak" placeholder="Contact Person Name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Telepon Penagihan <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="penagihan_telepon"
                                                id="penagihan_telepon" placeholder="e.g. 021-5555xxx" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Alamat Penagihan <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="penagihan_address" id="penagihan_address" rows="1"
                                                placeholder="Billing Address" required></textarea>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">Alamat Surat Menyurat <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="surat_menyurat_address" id="surat_menyurat_address" rows="2"
                                                placeholder="Correspondence Address" required></textarea>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Tax Contact Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="tax_contact_name"
                                                id="tax_contact_name" placeholder="Tax Person Name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tax Contact Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="tax_contact_email"
                                                id="tax_contact_email" placeholder="tax@example.com" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Tax Contact Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="tax_contact_phone"
                                                id="tax_contact_phone" placeholder="Phone Number" required>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">No. NPWP <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="npwp" id="npwp"
                                                placeholder="00.000.000.0-000.000" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tanggal NPWP <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_npwp"
                                                id="tanggal_npwp" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">No. NPPKP <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nppkp" id="nppkp"
                                                placeholder="NPPKP Number" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tanggal NPPKP <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_nppkp"
                                                id="tanggal_nppkp" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">No Pengukuhan Kaber</label>
                                            <input type="text" class="form-control" name="no_pengukuhan_kaber"
                                                id="no_pengukuhan_kaber" placeholder="Optional">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- E. Financial Terms --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary">Financial Terms</h6>

                                        <div class="col-md-4">
                                            <label class="form-label">TOP (Term of Payment) <span class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="term_of_payment" id="term_of_payment" style="width:100%" required>
                                                <option></option>
                                                @foreach ($top as $t)
                                                    <option value="{{ $t->name_top }}">{{ $t->desc_top }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Output Tax <span class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="output_tax" id="output_tax" style="width:100%" required>
                                                <option></option>
                                                <option value="Terhutang PPN">Terhutang PPN</option>
                                                <option value="NON-PPN">Tidak Terhutang (NON-PPN)</option>
                                                <option value="PPN">PPN</option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="lead_time" id="lead_time" value="0">

                                        <div class="col-md-4">
                                            <label class="form-label">Credit Limit <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="credit_limit" id="credit_limit" placeholder="Click to calculate" required readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">CCAR <span class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="ccar" id="ccar" style="width:100%" required>
                                                <option></option>
                                                <option value="smd_idr">SMD (IDR)</option>
                                                <option value="smd_usd">SMD USD</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Bank Garansi <span class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="bank_garansi" id="bank_garansi" style="width:100%" required>
                                                <option></option>
                                                <option value="YA">Yes</option>
                                                <option value="TIDAK">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- System Info --}}
                            <input type="hidden" name="status" value="Active">
                            <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-customer" disabled>Save
                            Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@2.1.5/dist/tesseract.min.js"></script>
        <script>
            $(document).ready(function() {
                // 1. Initialize Select2
                $('.select2').select2({
                    theme: 'bootstrap-5'
                });

                $('#user_id').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Search & Select User'
                });

                // Inisialisasi Select2 untuk Account Group, Class, dan SEMUA Financial Terms
                $('#account_group, #customer_class, #term_of_payment, #output_tax, #ccar, #bank_garansi').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Select Option'
                });

                $('#customerForm').on('submit', function(e) {
                    e.preventDefault();

                    if (!this.checkValidity()) {
                        e.stopPropagation();
                        this.reportValidity();
                        return;
                    }

                    const formData = new FormData(this);
                    const url = "{{ route('customers.store') }}";
                    Swal.fire({
                        title: 'Konfirmasi Penyimpanan',
                        text: "Pastikan seluruh data yang diinput sudah benar. Lanjutkan penyimpanan?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menyimpan Data...',
                                html: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.close();

                                    if(response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            $('#customerModal').modal('hide');
                                            $('#customerTable').DataTable().ajax.reload();
                                        });
                                    } else {
                                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    let errorMessage = 'Terjadi kesalahan pada server.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire('Error!', errorMessage, 'error');
                                }
                            });
                        }
                    });
                });

                $(document).on('change', 'input[name="file_npwp"]', function(e) {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    const originalBtn = $('#btn-save-customer');
                    originalBtn.prop('disabled', true);
                    const notice = $(
                        '<div class="mt-2 text-info" id="ocr-status">Running OCR, please wait...</div>');
                    $(this).closest('.card-body').append(notice);

                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        try {
                            const dataUrl = evt.target.result;
                            Tesseract.recognize(dataUrl, 'eng', {
                                    logger: m => console.log(m)
                                })
                                .then(result => {
                                    const text = result.data && result.data.text ? result.data.text :
                                    '';
                                    console.log('OCR text:', text);
                                    let npwpMatch = text.match(/\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}/);
                                    if (!npwpMatch) {
                                        npwpMatch = text.match(/[0-9\.\-\s]{9,25}/);
                                    }
                                    const npwp = npwpMatch ? npwpMatch[0].trim() : '';

                                    const lines = text.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
                                    console.log('OCR lines:', lines);
                                    try {
                                        let nameFromOcr = '';
                                        if (lines.length > 3) {
                                            const parts = [];
                                            if (lines[3]) parts.push(lines[3].trim());
                                            if (lines[4]) parts.push(lines[4].trim());
                                            if (parts.length) nameFromOcr = parts.join(' ');
                                        }
                                        if (!nameFromOcr && lines.length >= 2 && lines[1]) {
                                            nameFromOcr = lines[1].trim();
                                        }
                                        if (nameFromOcr) {
                                            $('#name').val(nameFromOcr);
                                            generatePkdNumber(nameFromOcr);
                                        }
                                    } catch (e) {
                                        console.error('Failed to set name from OCR', e);
                                    }
                                    let address = '';

                                    const fixedStart = 5;
                                    if (lines.length > fixedStart) {
                                        const candidate = [];
                                        for (let i = fixedStart; i < Math.min(lines.length, fixedStart + 3); i++) {
                                            const ln = lines[i];
                                            if (!ln) continue;
                                            if (ln.length < 3) continue;
                                            const digitRatio = (ln.replace(/\D/g, '').length) / Math.max(1, ln.length);
                                            if (digitRatio > 0.8) continue;
                                            candidate.push(ln);
                                        }
                                        if (candidate.length) {
                                            address = candidate.join(' ');
                                            console.log('Collected address from fixed start index 5:', candidate);
                                        }
                                    }

                                    if (!address) {
                                        const npwpPattern = npwpMatch ? npwpMatch[0].trim() : null;
                                        let npwpLineIdx = -1;
                                        if (npwpPattern) {
                                            npwpLineIdx = lines.findIndex(l => l.includes(npwpPattern) || l.replace(/\s+/g, '').includes(npwpPattern.replace(/\s+/g, '')));
                                        }

                                        if (npwpLineIdx >= 0) {
                                            const skipLabelRegex = /\b(NPWP|NPPKP|No\.?|Nama|Name|Alamat|Address|Tgl|Tanggal|SIUP|NIB)\b/i;
                                            const collected = [];
                                            const skipAfterNpwp = 2;
                                            for (let i = npwpLineIdx + 1 + skipAfterNpwp; i < lines.length && collected.length < 3; i++) {
                                                const ln = lines[i];
                                                if (!ln) continue;
                                                if (skipLabelRegex.test(ln)) continue;
                                                if (ln.length < 4) continue;
                                                const digitRatio = (ln.replace(/\D/g, '').length) / Math.max(1, ln.length);
                                                if (digitRatio > 0.6) continue;
                                                collected.push(ln);
                                            }
                                            if (collected.length) {
                                                address = collected.join(' ');
                                            }
                                            console.log('npwpLineIdx, collected address lines:', npwpLineIdx, collected);
                                        }

                                        if (!address) {
                                            const addrIdx = lines.findIndex(l => /\b(Jl|Jalan|Address|Alamat)\b/i.test(l));
                                            if (addrIdx >= 0) {
                                                let foundLine = lines[addrIdx];
                                                let cleanLine = foundLine.replace(/^(Alamat|Address|Jalan|Jl)\s*[:.]?\s*/i, '').trim();
                                                if (cleanLine.length < 3) {
                                                    address = lines.slice(addrIdx + 1, addrIdx + 4).join(' ');
                                                } else {
                                                    lines[addrIdx] = cleanLine;
                                                    address = lines.slice(addrIdx, addrIdx + 3).join(' ');
                                                }
                                            } else {
                                                const filtered = lines.filter(l => !/\b(NPWP|NPPKP|No\.?|Nama|Name|Tgl|Tanggal|SIUP|NIB)\b/i.test(l));
                                                if (filtered.length) {
                                                    address = filtered.reduce((a, b) => a.length > b.length ? a : b, '');
                                                } else if (lines.length) {
                                                    address = lines.reduce((a, b) => a.length > b.length ? a : b, '');
                                                }
                                            }
                                        }
                                    }

                                    console.log('Extracted address (pre-chunk):', address);

                                    function splitChunksWordWrap(str, len) {
                                        if (!str) return [];
                                        str = str.replace(/\s+/g, ' ').trim();
                                        const words = str.split(' ');
                                        const out = [];
                                        let line = '';
                                        words.forEach(w => {
                                            if ((line + ' ' + w).trim().length <= len) {
                                                line = (line + ' ' + w).trim();
                                            } else {
                                                if (line) out.push(line);
                                                // if single word longer than len, break it
                                                if (w.length > len) {
                                                    for (let i = 0; i < w.length; i += len) {
                                                        out.push(w.substr(i, len));
                                                    }
                                                    line = '';
                                                } else {
                                                    line = w;
                                                }
                                            }
                                        });
                                        if (line) out.push(line);
                                        return out;
                                    }

                                    const chunks = splitChunksWordWrap(address || '', 28);
                                    try {
                                        if ($('#address1').length) $('#address1').val(chunks[0] || '');
                                        if ($('#address2').length) $('#address2').val(chunks[1] || '');
                                        if ($('#address3').length) $('#address3').val(chunks[2] || '');
                                    } catch (e) {
                                        console.error('Error setting address fields', e);
                                    }

                                    if (npwp) {
                                        $('#npwp').val(npwp);
                                    }

                                    $('#ocr-status').remove();
                                    originalBtn.prop('disabled', false);
                                })
                                .catch(err => {
                                    console.error('OCR error', err);
                                    $('#ocr-status').text('OCR failed, please input address manually');
                                    originalBtn.prop('disabled', false);
                                    setTimeout(() => $('#ocr-status').fadeOut(400, function() {
                                        $(this).remove();
                                    }), 3000);
                                });
                        } catch (outerErr) {
                            console.error('OCR outer error', outerErr);
                            $('#ocr-status').text('OCR failed, please input address manually');
                            originalBtn.prop('disabled', false);
                            setTimeout(() => $('#ocr-status').fadeOut(400, function() {
                                $(this).remove();
                            }), 3000);
                        }
                    };
                    reader.readAsDataURL(file);
                });

                $('#user_id').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Search & Select User'
                });

                $('#account_group, #customer_class, #term_of_payment').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Select Option'
                });

                function generatePkdNumber(customerName) {
                    if (!customerName || customerName.length < 3) return;

                    $('#no_pkd').val('Generating...').addClass('text-muted');

                    $.ajax({
                        url: "{{ route('customers.generate-pkd-preview') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: customerName
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#no_pkd').val(response.number).removeClass('text-muted').addClass('fw-bold');
                            } else {
                                console.warn('Generate Failed:', response);
                                $('#no_pkd').val('').attr('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);
                            
                            if(xhr.status === 403) {
                                $('#no_pkd').val('Error: Unauthorized (403)');
                            } else {
                                $('#no_pkd').val('').attr('placeholder', '(Failed to generate)');
                            }
                        }
                    });
                }

                $('#name').on('blur', function() {
                    let val = $(this).val();
                    if(val.length > 3) {
                        generatePkdNumber(val);
                    }
                });

                const table = $('#customerTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.index') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [
                        // NO
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center dt-no-wrap' // Mencegah nomor turun baris
                        },
                        // CODE
                        {
                            data: 'code',
                            name: 'code',
                            className: 'dt-no-wrap fw-bold',
                            render: function(data) {
                                return data && data.trim() !== '' ? `<span class="fw-bold text-dark">${data}</span>` : `<span class="text-muted fst-italic">Auto-generated</span>`;
                            }
                        },
                        // NAME
                        {
                            data: 'name',
                            name: 'name',
                            className: 'dt-no-wrap fw-bold',
                            render: function(data) { return `<span class="fw-bold text-dark">${data}</span>`; }
                        },

                        // CREDIT LIMIT
                        {
                            data: 'credit_limit_formatted',
                            name: 'credit_limit',
                            className: 'dt-wrap'
                        },

                        // TOP & BG STATUS
                        {
                            data: 'financial_info',
                            name: 'term_of_payment',
                            className: 'dt-no-wrap text-center' // Jaga tetap satu blok
                        },

                        // STATUS APPROVAL
                        {
                            data: 'status_approval',
                            name: 'status_approval',
                            className: 'text-center dt-no-wrap' // Badge status jangan wrap
                        },

                        // ROUTE TO (POSISI)
                        {
                            data: 'route_to',
                            name: 'route_to',
                            className: 'text-center dt-wrap', // Izinkan nama orang panjang wrap
                            width: '15%' // Beri lebar spesifik
                        },

                        // ACTION
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center dt-no-wrap'
                        }
                    ],
                    order: [[1, 'desc']],
                    // Opsional: Atur lebar kolom otomatis
                    autoWidth: false
                });

                $('#statusFilter, #approvalStatusFilter').on('change', function() {
                    table.ajax.reload();
                });

                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all');
                    $('#approvalStatusFilter').val('all');

                    $('#statusFilter').trigger('change');
                    $('#approvalStatusFilter').trigger('change');

                    // Reload tabel (opsional, karena trigger change di atas sudah memanggil reload)
                    // table.ajax.reload();
                });

                // A. Saat User dipilih -> Tampilkan Detail User & Section Klasifikasi
                $('#user_id').on('change', function() {
                    const selected = $(this).find(':selected');
                    const userId = selected.val();

                    if (userId) {
                        // Pull values from option data attributes
                        $('#user_position').val(selected.data('pos') || '-');
                        $('#user_branch').val(selected.data('branch') || '-');
                        $('#user_region').val(selected.data('region') || '-');

                        $('#user-info-section').slideDown();
                        // Show main form section (account group & class are now inside main form)
                        $('#main-form-section').slideDown();
                        // keep save disabled until account_group + customer_class chosen
                        $('#btn-save-customer').prop('disabled', true);
                    } else {
                        $('#user-info-section').slideUp();
                        $('#main-form-section').slideUp();
                        $('#btn-save-customer').prop('disabled', true);
                    }
                });

                $('#account_group, #customer_class').on('change', function() {
                    const ag = $('#account_group').val();
                    const cc = $('#customer_class').val();
                    const selectedAg = $('#account_group').find(':selected');

                    if (selectedAg.length) {
                        let rawBg = selectedAg.data('bank_garansi');
                        let rawCcar = selectedAg.data('ccar');

                        let bgValue = '';
                        if (rawBg == 1 || rawBg === true || rawBg === '1') {
                            bgValue = 'YA';
                        } else {
                            bgValue = 'TIDAK';
                        }

                        // 2. SET VALUE & TRIGGER CHANGE: Penting untuk Select2
                        // Kita set valuenya ke 'YA'/'TIDAK' lalu panggil trigger('change')
                        $('#bank_garansi').val(bgValue).trigger('change');

                        // Untuk CCAR biasanya teksnya sama (smd_idr/smd_usd), tapi tetap perlu trigger
                        $('#ccar').val(rawCcar).trigger('change');
                    }

                    // Cek validasi tombol save
                    if (ag && cc) {
                        $('#btn-save-customer').prop('disabled', false);
                    } else {
                        $('#btn-save-customer').prop('disabled', true);
                    }
                });

                // 4. Modal Handler - CREATE
                $('#btn-create-customer').on('click', function() {
                    $('#customerForm')[0].reset();
                    $('.select2-styled').val(null).trigger('change');
                    $('#customerForm').find('input, textarea, select').prop('disabled', false);
                    $('#user_position, #user_branch, #user_region').prop('readonly', true);
                    $('#credit_limit').prop('readonly', true);
                    $('#no_pkd').val('').prop('readonly', true);

                    $('#preview_npwp, #preview_nib, #preview_ktp').hide();
                    $('input[type="file"]').prop('disabled', false).prop('required', true);
                    $('#btn-save-customer').show().prop('disabled', true);
                    $('#user-info-section').hide();
                    $('#main-form-section').hide();

                    $('#customerModalLabel').text('Create New Customer');
                    $('#customerModal').modal('show');
                });

                $(document).on('click', '.btn-show-customer', function() {
                    const btn = $(this);

                    $('#customerForm')[0].reset();
                    $('#customerModalLabel').html('<i class="ph-bold ph-eye"></i> View Customer Details (Read Only)');
                    $('#preview_npwp, #preview_nib, #preview_ktp').hide();
                    $('#preview_npwp a, #preview_nib a, #preview_ktp a').attr('href', '#')
                    $('input[type="file"]').prop('required', false);
                    $('#user_id').val(btn.data('user_id')).trigger('change');

                    setTimeout(() => {
                        $('#account_group').val(btn.data('account_group')).trigger('change');
                        $('#customer_class').val(btn.data('customer_class')).trigger('change');
                        $('#term_of_payment').val(btn.data('term_of_payment')).trigger('change');

                        let outTax = btn.data('output_tax');
                        $('#output_tax').val(outTax).trigger('change');

                        let bgVal = btn.data('bank_garansi');
                        if(!bgVal) {
                            const agSel = $('#account_group').find(':selected');
                            bgVal = agSel.data('bank_garansi') == 1 ? 'YA' : 'TIDAK';
                        }
                        $('#bank_garansi').val(bgVal).trigger('change');

                        let ccarVal = btn.data('ccar');
                        if(!ccarVal) {
                            const agSel = $('#account_group').find(':selected');
                            ccarVal = agSel.data('ccar');
                        }
                        $('#ccar').val(ccarVal).trigger('change');

                        $('#customerForm').find('input, textarea, select').prop('disabled', true);
                    }, 100);

                    $('#name').val(btn.data('name'));
                    $('#code').val(btn.data('code'));
                    $('#no_pkd').val(btn.data('no_pkd') || '');
                    $('#address1').val(btn.data('address1'));
                    $('#address2').val(btn.data('address2'));
                    $('#address3').val(btn.data('address3'));
                    $('#city').val(btn.data('city'));
                    $('#postal_code').val(btn.data('postal_code'));
                    $('#country').val(btn.data('country'));
                    $('#email').val(btn.data('email'));
                    $('#area').val(btn.data('area'));
                    $('#join_date').val(btn.data('join_date'));

                    $('#shipping_to_name').val(btn.data('shipping_to_name'));
                    $('#shipping_to_address').val(btn.data('shipping_to_address'));

                    $('#purchasing_manager_name').val(btn.data('purchasing_manager_name'));
                    $('#purchasing_manager_email').val(btn.data('purchasing_manager_email'));
                    $('#finance_manager_name').val(btn.data('finance_manager_name'));
                    $('#finance_manager_email').val(btn.data('finance_manager_email'));

                    $('#penagihan_nama_kontak').val(btn.data('penagihan_nama_kontak'));
                    $('#penagihan_telepon').val(btn.data('penagihan_telepon'));
                    $('#penagihan_address').val(btn.data('penagihan_address'));
                    $('#surat_menyurat_address').val(btn.data('surat_menyurat_address'));
                    $('#sort_name').val(btn.data('sort_name'));

                    $('#tax_contact_name').val(btn.data('tax_contact_name'));
                    $('#tax_contact_email').val(btn.data('tax_contact_email'));
                    $('#tax_contact_phone').val(btn.data('tax_contact_phone'));
                    $('#npwp').val(btn.data('npwp'));
                    $('#tanggal_npwp').val(btn.data('tanggal_npwp'));
                    $('#nppkp').val(btn.data('nppkp'));
                    $('#tanggal_nppkp').val(btn.data('tanggal_nppkp'));
                    $('#no_pengukuhan_kaber').val(btn.data('no_pengukuhan_kaber') || '-');

                    $('#output_tax').val(btn.data('output_tax'));
                    $('#term_of_payment').val(btn.data('term_of_payment')).trigger('change');
                    $('#lead_time').val(btn.data('lead_time'));
                    $('#credit_limit').val(btn.data('credit_limit'));
                    $('#ccar').val(btn.data('ccar'));
                    $('#bank_garansi').val(btn.data('bank_garansi'));

                    const npwpPath = btn.data('file_npwp_path');
                    if (npwpPath && npwpPath.length > 10) { // Cek panjang string minimal
                        $('#preview_npwp a.file-link').attr('href', npwpPath);
                        $('#preview_npwp').show(); // Tampilkan tombol view
                    }

                    const nibPath = btn.data('file_nib_path');
                    if (nibPath && nibPath.length > 10) {
                        $('#preview_nib a.file-link').attr('href', nibPath);
                        $('#preview_nib').show();
                    }

                    const ktpPath = btn.data('file_ktp_path');
                    if (ktpPath && ktpPath.length > 10) {
                        $('#preview_ktp a.file-link').attr('href', ktpPath);
                        $('#preview_ktp').show();
                    }

                    $('input[type="file"]').prop('disabled', true); // Disable upload baru
                    $('#btn-save-customer').hide(); // Sembunyikan tombol save
                    $('#customerForm').find('input, textarea').prop('disabled', true); // Disable text input

                    // Tampilkan Modal
                    $('#customerModal').modal('show');
                });

                // 6. Delete Handler (SweetAlert)
                $(document).on('click', '.delete-customer-btn', function(e) {
                    e.preventDefault();
                    const form = $(this).closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });

                // 7. Credit Limit Calculator Modal
                // open modal when credit_limit input is focused or clicked
                $(document).on('click focus', '#credit_limit', function(e) {
                    e.preventDefault();

                    // 1. Ambil Data TOP dari Luar (Dropdown)
                    const termString = $('#term_of_payment').val();

                    // Validasi: Jika TOP belum dipilih, warning
                    if (!termString) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'TOP belum dipilih',
                            text: 'Harap pilih Term of Payment terlebih dahulu sebelum menghitung limit.'
                        });
                        // Opsional: Buka dropdown TOP agar user langsung memilih
                        $('#term_of_payment').select2('open');
                        return;
                    }

                    // 2. Reset Modal
                    $('#calc_products').empty();
                    addCalcRow();

                    // 3. AUTO GENERATE TOP (DAYS)
                    // Ekstrak angka dari string (misal: "Net 60 Days" -> ambil "60")
                    let topVal = 0;
                    const numberMatch = termString.match(/(\d+)/); // Regex cari angka

                    if (numberMatch && numberMatch[0]) {
                        topVal = parseInt(numberMatch[0]);
                    }

                    // Isi kolom TOP di dalam modal
                    $('#calc_top').val(topVal);

                    // Opsional: Buat readonly agar user tidak bingung (karena sudah auto dari depan)
                    // $('#calc_top').prop('readonly', true);

                    // 4. Set Lead Time (Ambil dari input hidden/form lead_time jika ada)
                    let ltVal = parseFloat($('#lead_time').val()) || 0;
                    $('#calc_lt').val(ltVal);

                    // 5. Reset Preview Format
                    $('#calc_preview_formatted').val('0');

                    // 6. Hitung Awal (Jika iseng user sudah isi LT di luar)
                    const initialR = computeCreditValues();
                    $('#calc_preview_formatted').val(fmt(Math.round(initialR.val30 || 0)));

                    // 7. Tampilkan Modal
                    new bootstrap.Modal(document.getElementById('creditCalcModal')).show();
                });

                function fmt(n) {
                    if (isNaN(n)) return '0';
                    return Number(n).toLocaleString(undefined, {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                }

                function computeCreditValues() {
                    // sum qty*price across all product rows
                    let totalValue = 0;
                    $('#calc_products .calc-row').each(function() {
                        const q = parseFloat($(this).find('.calc-qty').val()) || 0;
                        const p = parseFloat($(this).find('.calc-price').val()) || 0;
                        totalValue += (q * p);
                    });

                    const top = parseFloat($('#calc_top').val()) || 0;
                    const lt = parseFloat($('#calc_lt').val()) || 0;

                    const base = (top + lt) * totalValue;

                    const val45 = base / 45;
                    const val30 = base / 30;
                    const val7 = base / (30 / 4); // as per user formula
                    const val14 = base / (30 / 2);

                    return {
                        base,
                        totalValue,
                        val45,
                        val30,
                        val7,
                        val14
                    };
                }

                // helper: add product row
                function addCalcRow(name = '', qty = '', price = '') {
                    const row = $('<div class="calc-row d-flex gap-2 mb-2">' +
                        '<input type="text" class="form-control calc-product-name" placeholder="Product name" />' +
                        '<input type="number" step="1" min="0" class="form-control calc-qty" placeholder="Qty" />' +
                        '<input type="number" step="0.01" min="0" class="form-control calc-price" placeholder="Price" />' +
                        '<button type="button" class="btn btn-outline-danger btn-remove-row" title="Remove">&minus;</button>' +
                        '</div>');
                    row.find('.calc-product-name').val(name);
                    row.find('.calc-qty').val(qty);
                    row.find('.calc-price').val(price);
                    $('#calc_products').append(row);
                }

                // compute when inputs change (any qty/price/top/lt)
                $(document).on('input', '#calc_products .calc-qty, #calc_products .calc-price, #calc_top, #calc_lt', function() {
                    const r = computeCreditValues();
                    // update formatted preview (display only) using 30-day default
                    $('#calc_preview_formatted').val(fmt(Math.round(r.val30 || 0)));
                });

                // add/remove row handlers
                $(document).on('click', '#addCalcRow', function() {
                    addCalcRow();
                });

                $(document).on('click', '.btn-remove-row', function() {
                    $(this).closest('.calc-row').remove();
                    // trigger recompute
                    $('#calc_products').trigger('input');
                });

                // save calculated value into credit_limit input
                $('#creditCalcForm').on('submit', function(e) {
                    e.preventDefault();
                    const r = computeCreditValues();
                    const chosen = r.val30 || 0;

                    // 1. Set Tampilan Credit Limit
                    $('#credit_limit').val(Math.round(chosen));

                    const ltVal = $('#calc_lt').val() || 0;
                    const topVal = $('#calc_top').val() || 0;

                    $('#lead_time').val(ltVal);

                    // (Opsional) Update TOP Calc hidden jika Anda pakai
                    if($('#top_calc_hidden').length === 0) {
                        $('<input>').attr({type: 'hidden', id: 'top_calc_hidden', name: 'top_calc'}).appendTo('#customerForm');
                    }
                    $('#top_calc_hidden').val(topVal);

                    // 2. BERSIHKAN Input Hidden Item Lama (Penting agar tidak duplikat saat edit/hitung ulang)
                    $('#customerForm').find('.hidden-item-input').remove();

                    // 3. GENERATE INPUT HIDDEN untuk setiap Item di Kalkulator
                    $('#calc_products .calc-row').each(function(index) {
                        const name = $(this).find('.calc-product-name').val();
                        const qty = $(this).find('.calc-qty').val();
                        const price = $(this).find('.calc-price').val();

                        if(name && qty) {
                            // Append ke Main Form (#customerForm)
                            const container = $('#customerForm');

                            // Item Name
                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][item_name]`,
                                value: name
                            }).appendTo(container);

                            // Quantity
                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][quantity]`,
                                value: qty
                            }).appendTo(container);

                            // Price
                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][price]`,
                                value: price
                            }).appendTo(container);
                        }
                    });

                    // 4. Tutup Modal
                    const modalEl = document.getElementById('creditCalcModal');
                    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    try {
                        inst.hide();
                    } catch (err) {
                        $('#creditCalcModal').modal('hide');
                    }
                });

                // cleanup stray backdrop or body classes in case modal system left them
                document.getElementById('creditCalcModal').addEventListener('hidden.bs.modal', function() {
                    // remove any leftover .modal-backdrop elements
                    $('.modal-backdrop').remove();
                    // ensure body doesn't keep modal-open class
                    $('body').removeClass('modal-open');
                });
            });
        </script>
    @endpush

    <!-- Credit Limit Calculator Modal -->
    <div class="modal fade" id="creditCalcModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Credit Limit Calculator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="creditCalcForm">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Products</label>
                            <div id="calc_products">
                                <div class="calc-row d-flex gap-2 mb-2">
                                    <input type="text" class="form-control calc-product-name" placeholder="Product name" />
                                    <input type="number" step="1" min="0" class="form-control calc-qty" placeholder="Qty" />
                                    <input type="number" step="0.01" min="0" class="form-control calc-price" placeholder="Price" />
                                    <button type="button" class="btn btn-outline-danger btn-remove-row" title="Remove">&minus;</button>
                                </div>
                            </div>
                            <button type="button" id="addCalcRow" class="btn btn-sm btn-secondary mt-1">Add product</button>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">TOP (days)</label>
                                <input type="number" step="1" id="calc_top" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Time (LT)</label>
                                <input type="text" step="1" id="calc_lt" class="form-control" readonly/>
                            </div>
                        </div>

                        <hr />
                        <div class="mb-2">
                            <label class="form-label">Preview Credit Limit</label>
                            <input type="text" id="calc_preview_formatted" class="form-control" readonly />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply to Credit Limit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
