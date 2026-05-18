<x-app-layout>
    @section('title')
        Customer List
    @endsection

    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Customers Management</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="/">
                        <i class="ph-bold f-s-16"></i> Home
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customers List</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-none d-md-flex align-items-center gap-2 mb-3">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>

                    <select id="statusFilter" class="form-select select2" style="width: 9.375rem;">
                        <option value="all">All Account</option>
                        <option value="Active">Active (BG)</option>
                        <option value="Inactive">Inactive (BG)</option>
                    </select>

                    <select id="approvalStatusFilter" class="form-select select2" style="width: 10.9375rem;">
                        <option value="all">All Approval</option>
                        @foreach ($approvalStatuses as $status)
                            <option value="{{ $status }}">{{ $status }}</option>
                        @endforeach
                    </select>

                    <button id="resetFilters" class="btn btn-secondary border" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                <div class="ms-auto d-flex gap-2">
                    @can('import customer')
                        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="ph-bold ph-file-csv"></i>
                            <span class="d-none d-sm-inline">Import CSV</span>
                        </button>
                    @endcan
                    @can('create customer')
                        <button class="btn btn-primary" type="button" id="btn-create-customer">
                            <i class="ph-bold ph-plus"></i>
                            <span>New Customer</span>
                        </button>
                    @endcan
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
                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip"
                            title="Waiting for Approval">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center"
                                style="width: 2rem; height: 2rem;">
                                <i class="ph-fill ph-clock-countdown text-warning f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Pending</span>
                                <span class="f-s-14 fw-bold">{{ $pendingCount }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip" title="Processing">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center"
                                style="width: 2rem; height: 2rem;">
                                <i class="ph-fill ph-gear text-info f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Processing</span>
                                <span class="f-s-14 fw-bold">{{ $processingCount }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip"
                            title="Approved (Administrative)">
                            <div class="bg-white bg-opacity-25 rounded-circle p-1 d-flex justify-content-center align-items-center"
                                style="width: 2rem; height: 2rem;">
                                <i class="ph-fill ph-seal-check text-success f-s-18"></i>
                            </div>
                            <div class="d-flex flex-column line-height-sm">
                                <span class="f-s-11 opacity-75 text-uppercase fw-bold">Approved</span>
                                <span class="f-s-14 fw-bold">{{ $approvedCount }}</span>
                            </div>
                        </div>

                        <div class="vr opacity-100 bg-white" style="height: 3.125rem;"></div>

                        {{-- Stat 3: BG Status --}}
                        <div class="d-flex align-items-center gap-4">

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip"
                                title="Customers with Bank Garansi">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center"
                                    style="width: 2rem; height: 2rem;">
                                    <i class="ph-fill ph-file-text text-success f-s-18"></i>
                                </div>
                                <div class="d-flex flex-column line-height-sm">
                                    <span class="f-s-11 opacity-75 text-uppercase fw-bold">BG Active</span>
                                    <span class="f-s-14 fw-bold">{{ $activeCount }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2" data-bs-toggle="tooltip"
                                title="Customers without Bank Garansi">
                                <div class="bg-white bg-opacity-10 rounded-circle p-1 d-flex justify-content-center align-items-center"
                                    style="width: 2rem; height: 2rem;">
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
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="5%">Code</th>
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
                                        <label for="user_id" class="form-label">Select User (Requester) <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2-styled" id="user_id" name="user_id"
                                            style="width: 100%;" required>
                                            <option></option>
                                            @foreach ($sales as $s)
                                                <option value="{{ $s->user_id }}"
                                                    data-pos="{{ $s->user->position?->position_name ?? '' }}"
                                                    data-branch="{{ $s->branch?->branch_name ?? '' }}"
                                                    data-region="{{ $s->region?->region_name ?? '' }}"
                                                    data-account-group-id="{{ $s->account_group_id }}">
                                                    {{ $s->user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="current_user_role"
                                            value="{{ Auth::user()->getRoleNames()->first() }}">
                                        <input type="hidden" id="current_user_id" value="{{ Auth::id() }}">
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
                                            <input type="text" class="form-control form-control-sm"
                                                id="user_branch" readonly placeholder="Auto-filled">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small text-muted">Region</label>
                                            <input type="text" class="form-control form-control-sm"
                                                id="user_region" readonly placeholder="Auto-filled">
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

                                {{-- D. Documents Upload --}}
                                <div class="card-body bg-opacity-10">
                                    <div class="row g-3 mb-4">
                                        {{-- Header takes full width --}}
                                        <div class="col-12">
                                            <h6 class="fw-bold text-secondary mb-0">
                                                <i class="ph-bold ph-upload-simple"></i> Document Uploads (Auto-fill
                                                Support)
                                            </h6>
                                        </div>

                                        {{-- 1. NPWP (REQUIRED) --}}
                                        <div class="col-md">
                                            <label class="form-label">Upload NPWP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_npwp"
                                                accept=".jpeg,.jpg,.png" required>
                                            <small class="text-muted f-s-11">Format: JPEG, JPG, PNG. Upload for
                                                auto-fill name & address.</small>
                                            <div id="preview_npwp" class="mt-2" style="display: none;"></div>
                                        </div>

                                        {{-- 2. NIB/SIUP (REQUIRED) --}}
                                        <div class="col-md">
                                            <label class="form-label">Upload NIB/SIUP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_nib"
                                                accept=".jpeg,.jpg,.png" required>
                                            <small class="text-muted f-s-11">Format: JPEG, JPG, PNG</small>
                                            <div id="preview_nib" class="mt-2" style="display: none;"></div>
                                        </div>

                                        {{-- 3. KTP (REQUIRED) --}}
                                        <div class="col-md">
                                            <label class="form-label">Upload KTP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_ktp"
                                                accept=".jpeg,.jpg,.png" required>
                                            <small class="text-muted f-s-11">Format: JPEG, JPG, PNG</small>
                                            <div id="preview_ktp" class="mt-2" style="display: none;"></div>
                                        </div>

                                        {{-- 4. AKTE (OPTIONAL - PDF ONLY) --}}
                                        <div class="col-md">
                                            <label class="form-label">Upload Akte Pendirian</label>
                                            <input type="file" class="form-control" name="file_akte"
                                                accept=".pdf">
                                            <small class="text-muted f-s-11">Format: PDF only</small>
                                            <div id="preview_akte" class="mt-4"
                                                style="display: none; position:relative; z-index:2;"></div>
                                        </div>

                                        {{-- 5. COMPANY PROFILE (OPTIONAL - PDF ONLY) --}}
                                        <div class="col-md">
                                            <label class="form-label">Upload Company Profile</label>
                                            <input type="file" class="form-control" name="file_company_profile"
                                                accept=".pdf">
                                            <small class="text-muted f-s-11">Format: PDF only</small>
                                            <div id="preview_company_profile" class="mt-2"
                                                style="display: none; position:relative; z-index:2;"></div>
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
                                            <input type="text" class="form-control" name="sort_name"
                                                id="sort_name" placeholder="e.g. MMC">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control mb-2" name="address1"
                                                id="address1" placeholder="Address Line 1 (Required)" required>
                                            <input type="text" class="form-control mb-2" name="address2"
                                                id="address2" placeholder="Address Line 2 (Optional)">
                                            <input type="text" class="form-control" name="address3"
                                                id="address3" placeholder="Address Line 3 (Optional)">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Postal Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="postal_code"
                                                id="postal_code" placeholder="e.g. 12345" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                placeholder="e.g. Jakarta Selatan" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Country <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="Indonesia" placeholder="Country" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">No PKD <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control bg-light" name="no_pkd"
                                                id="no_pkd" placeholder="PKD No. will be auto-generated by system"
                                                readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Person in Charge (PIC) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="pic" id="pic"
                                                placeholder="e.g. John Doe" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email (General) <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="e.g. info@company.com" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Area <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="area" id="area"
                                                placeholder="e.g. Jabodetabek" required>
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
                                            <input type="email" class="form-control"
                                                name="purchasing_manager_email" id="purchasing_manager_email"
                                                placeholder="email@example.com" required>
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
                                            <label class="form-label">Billing Contact Person <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="penagihan_nama_kontak"
                                                id="penagihan_nama_kontak" placeholder="Contact Person Name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Billing Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="penagihan_telepon"
                                                id="penagihan_telepon" placeholder="e.g. 021-5555xxx" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Billing Address <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" name="penagihan_address" id="penagihan_address" rows="1"
                                                placeholder="Billing Address" required></textarea>
                                        </div>

                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">Correspondence Address <span
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
                                            <label class="form-label">NPWP Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal_npwp"
                                                id="tanggal_npwp" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">No. NPPKP</label>
                                            <input type="text" class="form-control" name="nppkp" id="nppkp"
                                                placeholder="NPPKP Number">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">NPPKP Date</label>
                                            <input type="date" class="form-control" name="tanggal_nppkp"
                                                id="tanggal_nppkp">
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
                                            <label class="form-label">TOP (Term of Payment) <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="term_of_payment"
                                                id="term_of_payment" style="width:100%" required>
                                                <option></option>
                                                @foreach ($top as $t)
                                                    <option value="{{ $t->name_top }}">{{ $t->desc_top }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Output Tax <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="output_tax"
                                                id="output_tax" style="width:100%" required>
                                                <option></option>
                                                <option value="Terhutang PPN">Terhutang PPN</option>
                                                <option value="NON-PPN">Tidak Terhutang (NON-PPN)</option>
                                                <option value="PPN">PPN</option>
                                            </select>
                                        </div>

                                        <input type="hidden" name="lead_time" id="lead_time" value="0">

                                        <div class="col-md-4">
                                            <label class="form-label">Credit Limit <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="credit_limit"
                                                id="credit_limit" placeholder="Click to calculate" required readonly>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">CCAR <span class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="ccar" id="ccar"
                                                style="width:100%" required>
                                                <option></option>
                                                <option value="smd_idr">SMD (IDR)</option>
                                                <option value="smd_usd">SMD USD</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Bank Garansi <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2-styled" name="bank_garansi"
                                                id="bank_garansi" style="width:100%" required>
                                                <option></option>
                                                <option value="YA">Yes</option>
                                                <option value="TIDAK">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                {{-- F. Financial Schedule (Optional) --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary d-flex align-items-center">
                                            <i class="ph-bold ph-calendar-check me-2"></i> Financial Schedule
                                            (Optional)
                                        </h6>
                                        <div class="alert alert-light border border-dashed p-2 mb-3 f-s-12 text-muted">
                                            <i class="ph-bold ph-info me-1"></i> Set specific billing and payment
                                            schedules for this customer. This is optional and can be left blank if not
                                            needed. If schedules are set, they will override the default system settings
                                            for this customer.
                                        </div>

                                        {{-- CONTAINER INPUT SCHEDULE --}}
                                        <div id="create_schedule_section">
                                            <div class="row g-4">

                                                {{-- LEFT: FAKTUR --}}
                                                <div class="col-md-6 border-end">
                                                    <h6 class="text-success fw-bold small text-uppercase mb-2">Billing
                                                        Schedule</h6>

                                                    {{-- Faktur Days --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Billing Days</label>
                                                        <div class="schedule-selector"
                                                            id="create_faktur_days_container">
                                                            <div id="create_faktur_days_inputs"></div>

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-dark me-1 mb-1 btn-schedule"
                                                                data-type="faktur_days" data-val="All">All
                                                                Days</button>
                                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-success mb-1 btn-schedule"
                                                                    data-type="faktur_days"
                                                                    data-val="{{ $day }}">{{ $day }}</button>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Faktur Date --}}
                                                    <div>
                                                        <label class="form-label small fw-bold">Billing Date</label>
                                                        <div class="schedule-selector"
                                                            id="create_faktur_date_container">
                                                            <div id="create_faktur_date_inputs"></div>

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-dark me-1 mb-2 w-100 btn-schedule"
                                                                data-type="faktur_date" data-val="All">All Dates
                                                                (1-31)</button>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @for ($i = 1; $i <= 31; $i++)
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-secondary btn-schedule btn-date-box"
                                                                        style="width: 2.375rem !important; height: 2.375rem !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 13.6px !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                                        data-type="faktur_date"
                                                                        data-val="{{ $i }}">
                                                                        {{ $i }}
                                                                    </button>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- RIGHT: PAYMENT --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-primary fw-bold small text-uppercase mb-2">Payment
                                                        Schedule</h6>

                                                    {{-- Payment Days --}}
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Payment Days</label>
                                                        <div class="schedule-selector"
                                                            id="create_payment_days_container">
                                                            <div id="create_payment_days_inputs"></div>

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-dark me-1 mb-1 btn-schedule"
                                                                data-type="payment_days" data-val="All">All
                                                                Days</button>
                                                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary mb-1 btn-schedule"
                                                                    data-type="payment_days"
                                                                    data-val="{{ $day }}">{{ $day }}</button>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Payment Date --}}
                                                    <div>
                                                        <label class="form-label small fw-bold">Payment Date</label>
                                                        <div class="schedule-selector"
                                                            id="create_payment_date_container">
                                                            <div id="create_payment_date_inputs"></div>

                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-dark me-1 mb-2 w-100 btn-schedule"
                                                                data-type="payment_date" data-val="All">All Dates
                                                                (1-31)</button>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @for ($i = 1; $i <= 31; $i++)
                                                                    <button type="button"
                                                                        class="btn btn-xs btn-outline-secondary btn-schedule btn-date-box"
                                                                        style="width: 2.375rem !important; height: 2.375rem !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 13.6px !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                                        data-type="payment_date"
                                                                        data-val="{{ $i }}">
                                                                        {{ $i }}
                                                                    </button>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
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

    <div class="modal fade" id="customerDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="ph-bold ph-user-circle f-s-32"></i>
                        </div>
                        <div>
                            <h4 class="modal-title mb-0 fw-bold" id="view_header_name">Customer Name</h4>
                            <div class="opacity-75 f-s-14 mt-1" id="view_header_code">CODE-001</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light p-4" style="max-height: 85vh; overflow-y: auto;">

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-4">
                                    <div>
                                        <label class="fw-bold text-dark text-uppercase f-s-12 mb-1">Account
                                            Status</label>
                                        <div><span id="view_status_badge"
                                                class="badge bg-secondary f-s-12 px-3 py-2">STATUS</span></div>
                                    </div>
                                    <div class="vr" style="height: 2.5rem; opacity: 0.1;"></div>
                                    <div>
                                        <label class="fw-bold text-dark text-uppercase f-s-12 mb-1">Approval
                                            Progress</label>
                                        <div id="view_approval_badge" class="fw-bold text-dark f-s-16">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ph-fill ph-info me-2"></i> General Information
                    </h5>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Customer
                                                Name</label>
                                            <div class="fw-bold text-dark f-s-16" id="view_name">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Sort Name
                                                / Alias</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_sort_name">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Email
                                                Address</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_email">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">No.
                                                PKD</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_no_pkd">-</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">PIC
                                                (Penanggung Jawab)</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_pic">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Main
                                                Address</label>
                                            <div class="fw-bold text-dark f-s-14 lh-base" id="view_full_address">-
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="fw-bold text-secondary text-uppercase f-s-12 mb-1">City</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_city">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label
                                                class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Area</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_area">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="fw-bold text-secondary text-uppercase f-s-12 mb-1">Postal
                                                Code</label>
                                            <div class="fw-bold text-dark f-s-14" id="view_postal_code">-</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ph-fill ph-currency-dollar me-2"></i> Financial & Tax
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <label
                                                class="text-white text-opacity-75 text-uppercase f-s-12 fw-bold">Credit
                                                Limit</label>
                                            <h3 class="mb-0 fw-bold mt-1" id="view_credit_limit">IDR 0</h3>
                                        </div>
                                        <i class="ph-duotone ph-wallet f-s-40 text-white text-opacity-50"></i>
                                    </div>
                                    <div
                                        class="mt-4 pt-3 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center">
                                        <span class="f-s-13 opacity-75">Term of Payment</span>
                                        <span class="fw-bold f-s-16 bg-warning bg-opacity-20 px-2 py-1 rounded"><span
                                                id="view_top">-</span> Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">Tax Information</h6>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPWP No.</span>
                                        <span class="fw-bold text-dark" id="view_npwp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPWP Date</span>
                                        <span class="fw-bold text-dark" id="view_tanggal_npwp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="fw-bold text-secondary f-s-13">NPPKP</span>
                                        <span class="fw-bold text-dark" id="view_nppkp">-</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold text-secondary f-s-13">Output Tax</span>
                                        <span class="fw-bold text-dark" id="view_output_tax">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">Billing Contact</h6>
                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Contact
                                            Name</label>
                                        <div class="fw-bold text-dark f-s-15" id="view_penagihan_nama">-</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Phone
                                            Number</label>
                                        <div class="fw-bold text-dark f-s-15" id="view_penagihan_telp">-</div>
                                    </div>
                                    <div>
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Billing
                                            Address</label>
                                        <div class="fw-bold text-dark f-s-14 lh-sm" id="view_penagihan_addr">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ph-fill ph-users-three me-2"></i> Management & Logistics
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-0">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-4 py-3 fw-bold text-secondary text-uppercase f-s-12">
                                                    Position Role</th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Full Name
                                                </th>
                                                <th class="py-3 fw-bold text-secondary text-uppercase f-s-12">Email
                                                    Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Purchasing Mgr</td>
                                                <td class="fw-bold text-dark" id="view_purc_name">-</td>
                                                <td class="text-dark" id="view_purc_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Finance Mgr</td>
                                                <td class="fw-bold text-dark" id="view_fin_name">-</td>
                                                <td class="text-dark" id="view_fin_email">-</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4 text-secondary fw-bold">Tax Contact</td>
                                                <td class="fw-bold text-dark" id="view_tax_name">-</td>
                                                <td class="text-dark" id="view_tax_email">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div
                                class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10 border-warning border-opacity-25">
                                <div class="card-body p-4">
                                    <h6
                                        class="fw-bold text-dark mb-3 pb-2 border-bottom border-warning border-opacity-25">
                                        <i class="ph-fill ph-truck me-2 text-warning"></i>Shipping Destination
                                    </h6>

                                    <div class="mb-3">
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Recipient
                                            Name</label>
                                        <div class="fw-bold text-dark f-s-16" id="view_shipping_name">-</div>
                                    </div>

                                    <div>
                                        <label class="fw-bold text-secondary text-uppercase f-s-11 mb-1">Shipping
                                            Address</label>
                                        <div class="fw-bold text-dark f-s-14 lh-base" id="view_shipping_address">-
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ph-fill ph-calendar-check me-2"></i> Payment & Faktur Schedule
                    </h5>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <h6 class="text-primary fw-bold small text-uppercase mb-2">Payment Schedule</h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Payment Days</label>
                                        <div class="schedule-selector" id="view_payment_days_container">
                                            <div id="view_payment_days_inputs"></div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-dark me-1 mb-1 btn-schedule"
                                                data-type="payment_days" data-val="All" disabled>All Days</button>
                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary mb-1 btn-schedule"
                                                    data-type="payment_days" data-val="{{ $day }}"
                                                    disabled>{{ $day }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label small fw-bold">Payment Date</label>
                                        <div class="schedule-selector" id="view_payment_date_container">
                                            <div id="view_payment_date_inputs"></div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-dark me-1 mb-2 w-100 btn-schedule"
                                                data-type="payment_date" data-val="All" disabled>All Dates
                                                (1-31)</button>
                                            <div class="d-flex flex-wrap gap-1" id="view_payment_date_boxes">
                                                @for ($i = 1; $i <= 31; $i++)
                                                    <button type="button"
                                                        class="btn btn-xs btn-outline-secondary btn-schedule btn-date-box"
                                                        style="width: 2.375rem !important; height: 2.375rem !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 13.6px !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                        data-type="payment_date" data-val="{{ $i }}"
                                                        disabled>
                                                        {{ $i }}
                                                    </button>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 ps-md-4">
                                    <h6 class="text-success fw-bold small text-uppercase mb-2">Faktur Schedule</h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Faktur Days</label>
                                        <div class="schedule-selector" id="view_faktur_days_container">
                                            <div id="view_faktur_days_inputs"></div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-dark me-1 mb-1 btn-schedule"
                                                data-type="faktur_days" data-val="All" disabled>All Days</button>
                                            @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success mb-1 btn-schedule"
                                                    data-type="faktur_days" data-val="{{ $day }}"
                                                    disabled>{{ $day }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label small fw-bold">Faktur Date</label>
                                        <div class="schedule-selector" id="view_faktur_date_container">
                                            <div id="view_faktur_date_inputs"></div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-dark me-1 mb-2 w-100 btn-schedule"
                                                data-type="faktur_date" data-val="All" disabled>All Dates
                                                (1-31)</button>
                                            <div class="d-flex flex-wrap gap-1" id="view_faktur_date_boxes">
                                                @for ($i = 1; $i <= 31; $i++)
                                                    <button type="button"
                                                        class="btn btn-xs btn-outline-secondary btn-schedule btn-date-box"
                                                        style="width: 2.375rem !important; height: 2.375rem !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 13.6px !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                        data-type="faktur_date" data-val="{{ $i }}"
                                                        disabled>
                                                        {{ $i }}
                                                    </button>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-primary mb-3 d-flex align-items-center">
                        <i class="ph-fill ph-files me-2"></i> Documents
                    </h5>
                    <div class="row g-3" id="document_grid">
                    </div>
                    <div id="no_documents" class="text-center py-5 text-muted border border-dashed rounded bg-white"
                        style="display:none;">
                        <i class="ph-duotone ph-folder-notch-open f-s-48 mb-3 opacity-50"></i>
                        <p class="mb-0 f-s-16">No documents uploaded for this customer.</p>
                    </div>

                </div>

                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary px-5 rounded-pill" data-bs-dismiss="modal">Close
                        Detail</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fileViewerModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-xl" id="fileViewerDialog">
            <div class="modal-content border-0">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title text-white fw-bold f-s-14" id="fileViewerTitle">
                        FILE PREVIEW
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 d-flex align-items-center justify-content-center"
                    style="min-height: 31.25rem; background-color: #1a1a1a;">
                    <div id="fileContentArea" class="w-100 h-100 d-flex align-items-center justify-content-center"
                        style="margin-top:80px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recallCustomerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-gradient bg-warning text-dark border-0 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3">
                            <i class="ph-bold ph-arrow-u-up-left f-s-24"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Recall Submission</h5>
                            <small class="opacity-75">Perbaiki data yang ditolak dan ajukan ulang.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="recallCustomerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="recall_customer_id" name="id">
                    <input type="hidden" name="user_id" id="recall_user_id">
                    <input type="hidden" name="country" id="recall_country" value="Indonesia">

                    <div class="modal-body bg-light p-0">

                        <div class="alert alert-warning border-0 rounded-0 mb-0 d-flex align-items-center px-4 py-3"
                            role="alert">
                            <i class="ph-fill ph-info f-s-24 me-3"></i>
                            <div>
                                <strong>Note:</strong> Proses Recall akan mereset status menjadi
                                <strong>Pending</strong> (Level 1).
                            </div>
                        </div>

                        <div class="alert alert-danger border-0 rounded-0 mb-0 d-flex align-items-start px-4 py-3"
                            role="alert" id="recall_reject_alert" style="display: none;">
                            <i class="ph-fill ph-warning-circle f-s-24 me-3 mt-1"></i>
                            <div>
                                <strong class="d-block mb-1">Alasan Ditolak (Rejection Note):</strong>
                                <span id="recall_reject_reason" class="fst-italic"></span>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <div class="nav flex-column nav-pills me-3 bg-white h-100 p-3 border-end"
                                style="min-width: 12.5rem;" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <button class="nav-link active text-start fw-bold mb-2" id="v-pills-home-tab"
                                    data-bs-toggle="pill" data-bs-target="#tab-recall-general" type="button"
                                    role="tab">
                                    <i class="ph-bold ph-user-circle me-2"></i> General Info
                                </button>
                                <button class="nav-link text-start fw-bold mb-2" id="v-pills-items-tab"
                                    data-bs-toggle="pill" data-bs-target="#tab-recall-items" type="button"
                                    role="tab">
                                    <i class="ph-bold ph-shopping-cart me-2"></i> Items / Product
                                </button>
                                <button class="nav-link text-start fw-bold mb-2" id="v-pills-finance-tab"
                                    data-bs-toggle="pill" data-bs-target="#tab-recall-finance" type="button"
                                    role="tab">
                                    <i class="ph-bold ph-currency-circle-dollar me-2"></i> Financial
                                </button>
                                <button class="nav-link text-start fw-bold mb-2" id="v-pills-mgmt-tab"
                                    data-bs-toggle="pill" data-bs-target="#tab-recall-mgmt" type="button"
                                    role="tab">
                                    <i class="ph-bold ph-users-three me-2"></i> Management
                                </button>
                                <button class="nav-link text-start fw-bold mb-2" id="v-pills-docs-tab"
                                    data-bs-toggle="pill" data-bs-target="#tab-recall-docs" type="button"
                                    role="tab">
                                    <i class="ph-bold ph-files me-2"></i> Documents
                                </button>
                            </div>

                            <div class="tab-content p-4 w-100" id="v-pills-tabContent"
                                style="max-height: 70vh; overflow-y: auto;">

                                <div class="tab-pane fade show active" id="tab-recall-general" role="tabpanel">
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Informasi Utama Customer
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Customer Name</label>
                                            <input type="text" class="form-control" name="name"
                                                id="recall_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Sort Name / Alias</label>
                                            <input type="text" class="form-control" name="sort_name"
                                                id="recall_sort_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                id="recall_email" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">No PKD (Readonly)</label>
                                            <input type="text" class="form-control bg-light" name="no_pkd"
                                                id="recall_no_pkd" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">PIC (Penanggung
                                                Jawab)</label>
                                            <input type="text" class="form-control" name="pic"
                                                id="recall_pic" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-dark">Main Address</label>
                                            <input type="text" class="form-control mb-2" name="address1"
                                                id="recall_address1" placeholder="Line 1" required>
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control" name="address2"
                                                    id="recall_address2" placeholder="Line 2">
                                                <input type="text" class="form-control" name="address3"
                                                    id="recall_address3" placeholder="Line 3">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">City</label>
                                            <input type="text" class="form-control" name="city"
                                                id="recall_city" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Area</label>
                                            <input type="text" class="form-control" name="area"
                                                id="recall_area" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Postal Code</label>
                                            <input type="text" class="form-control" name="postal_code"
                                                id="recall_postal_code" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-recall-items" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold text-primary mb-0">Daftar Item / Produk</h6>
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3"
                                            id="btn-recall-add-item">
                                            <i class="ph-bold ph-plus me-1"></i> Tambah Item
                                        </button>
                                    </div>
                                    <div class="table-responsive border rounded">
                                        <table class="table table-hover align-middle mb-0" id="recall_items_table">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4 fw-bold text-dark" width="40%">Nama Item</th>
                                                    <th class="fw-bold text-dark" width="20%">Qty</th>
                                                    <th class="fw-bold text-dark" width="30%">Harga (Est)</th>
                                                    <th width="10%" class="text-center fw-bold text-dark">Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="recall_items_body"></tbody>
                                        </table>
                                    </div>
                                    <div id="recall_no_items"
                                        class="text-center py-5 text-muted bg-white border rounded mt-2">
                                        <i class="ph-duotone ph-basket f-s-32 mb-2"></i>
                                        <p class="mb-0">Belum ada item ditambahkan.</p>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-recall-finance" role="tabpanel">
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Financial Settings</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Account Group</label>
                                            <select class="form-select select2-recall" id="recall_account_group"
                                                name="account_group" style="width: 100%;" required>
                                                @foreach ($accountgroup as $ag)
                                                    <option value="{{ $ag->id }}"
                                                        data-bank_garansi="{{ $ag->bank_garansi }}">
                                                        {{ $ag->name_account_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Customer Class</label>
                                            <select class="form-select select2-recall" id="recall_customer_class"
                                                name="customer_class" style="width: 100%;" required>
                                                @foreach ($customerClass as $cc)
                                                    <option value="{{ $cc->id }}">{{ $cc->name_class }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Term of Payment</label>
                                            <select class="form-select select2-recall" name="term_of_payment"
                                                id="recall_term_of_payment" style="width:100%" required>
                                                @foreach ($top as $t)
                                                    <option value="{{ $t->name_top }}">{{ $t->desc_top }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Credit Limit</label>
                                            <input type="text" class="form-control" name="credit_limit"
                                                id="recall_credit_limit" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Bank Garansi</label>
                                            <select class="form-select select2-recall" name="bank_garansi"
                                                id="recall_bank_garansi" style="width:100%" required>
                                                <option value="YA">Yes</option>
                                                <option value="TIDAK">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">Output Tax</label>
                                            <select class="form-select select2-recall" name="output_tax"
                                                id="recall_output_tax" style="width:100%" required>
                                                <option value="Terhutang PPN">Terhutang PPN</option>
                                                <option value="NON-PPN">Tidak Terhutang (NON-PPN)</option>
                                                <option value="PPN">PPN</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-dark">CCAR</label>
                                            <select class="form-select select2-recall" name="ccar"
                                                id="recall_ccar" style="width:100%" required>
                                                <option value="smd_idr">SMD (IDR)</option>
                                                <option value="smd_usd">SMD USD</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                                                    class="ph-bold ph-receipt me-2"></i>Billing Information</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Billing
                                                Contact Name</label>
                                            <input type="text" class="form-control"
                                                name="penagihan_nama_kontak" id="recall_penagihan_nama_kontak">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Billing
                                                Phone</label>
                                            <input type="text" class="form-control" name="penagihan_telepon"
                                                id="recall_penagihan_telepon">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Billing
                                                Address</label>
                                            <textarea class="form-control" name="penagihan_address" id="recall_penagihan_address" rows="2"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label
                                                class="form-label fw-bold small text-dark text-uppercase">Correspondence
                                                Address</label>
                                            <textarea class="form-control" name="surat_menyurat_address" id="recall_surat_menyurat_address" rows="2"></textarea>
                                        </div>


                                        <div class="col-12 mt-4">
                                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                                                    class="ph-bold ph-calculator me-2"></i>Tax Information</h6>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">NPWP
                                                Number</label>
                                            <input type="text" class="form-control" name="npwp"
                                                id="recall_npwp" placeholder="00.000.000.0-000.000">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">NPWP
                                                Date</label>
                                            <input type="date" class="form-control" name="tanggal_npwp"
                                                id="recall_tanggal_npwp">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">NPPKP
                                                Number</label>
                                            <input type="text" class="form-control" name="nppkp"
                                                id="recall_nppkp">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">NPPKP
                                                Date</label>
                                            <input type="date" class="form-control" name="tanggal_nppkp"
                                                id="recall_tanggal_nppkp">
                                        </div>

                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Tax
                                                Contact Name</label>
                                            <input type="text" class="form-control" name="tax_contact_name"
                                                id="recall_tax_contact_name">
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Tax
                                                Email</label>
                                            <input type="email" class="form-control" name="tax_contact_email"
                                                id="recall_tax_contact_email">
                                        </div>
                                        <div class="col-md-4 mt-3">
                                            <label class="form-label fw-bold small text-dark text-uppercase">Tax
                                                Phone</label>
                                            <input type="text" class="form-control" name="tax_contact_phone"
                                                id="recall_tax_contact_phone">
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-recall-mgmt" role="tabpanel">
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Management Personnel</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Purchasing Manager
                                                Name</label>
                                            <input type="text" class="form-control"
                                                name="purchasing_manager_name" id="recall_purchasing_manager_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Purchasing Manager
                                                Email</label>
                                            <input type="email" class="form-control"
                                                name="purchasing_manager_email"
                                                id="recall_purchasing_manager_email">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Finance Manager
                                                Name</label>
                                            <input type="text" class="form-control" name="finance_manager_name"
                                                id="recall_finance_manager_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Finance Manager
                                                Email</label>
                                            <input type="email" class="form-control"
                                                name="finance_manager_email" id="recall_finance_manager_email">
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="fw-bold text-secondary border-bottom pb-1">Shipping Info</h6>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Shipping Recipient
                                                Name</label>
                                            <input type="text" class="form-control" name="shipping_to_name"
                                                id="recall_shipping_to_name">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-dark">Shipping Address</label>
                                            <textarea class="form-control" name="shipping_to_address" id="recall_shipping_to_address" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab-recall-docs" role="tabpanel">
                                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Dokumen Pendukung</h6>
                                    <div class="mb-3 border rounded p-2 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-bold small mb-0 text-dark">NPWP</label>
                                            <div id="recall_preview_npwp"></div>
                                        </div>
                                        <input type="file" class="form-control form-control-sm"
                                            name="file_npwp">
                                    </div>

                                    <div class="mb-3 border rounded p-2 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-bold small mb-0 text-dark">NIB / SIUP</label>
                                            <div id="recall_preview_nib"></div>
                                        </div>
                                        <input type="file" class="form-control form-control-sm"
                                            name="file_nib">
                                    </div>

                                    <div class="mb-0 border rounded p-2 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-bold small mb-0 text-dark">KTP Penanggung Jawab</label>
                                            <div id="recall_preview_ktp"></div>
                                        </div>
                                        <input type="file" class="form-control form-control-sm"
                                            name="file_ktp">
                                    </div>

                                    <div class="mb-3 border rounded p-2 bg-white">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-bold small mb-0 text-dark">Company Profile</label>
                                            <div id="recall_preview_company_profile"></div>
                                        </div>
                                        <input type="file" class="form-control form-control-sm"
                                            name="file_company_profile" accept=".pdf">
                                        <small class="text-muted f-s-11">Format: PDF saja</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top py-3">
                        <button type="button" class="btn btn-light border text-muted px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="ph-bold ph-paper-plane-tilt me-2"></i> Submit & Resend Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title fw-bold"><i class="ph-bold ph-upload-simple me-2"></i> Import Data
                        Customers</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="card modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm f-s-13">
                            <i class="ph-fill ph-info me-2"></i> Gunakan format CSV standar untuk memigrasi data. Data
                            yang di-import akan langsung masuk sebagai <strong>Active</strong> &
                            <strong>Approved</strong>.
                        </div>

                        <div class="mb-4 text-center">
                            <a href="{{ route('customers.template') }}"
                                class="btn btn-sm btn-outline-primary rounded-pill px-4">
                                <i class="ph-bold ph-download-simple me-1"></i> Download Template CSV
                            </a>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih File CSV <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="file" accept=".csv" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-white">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success fw-bold"><i
                                class="ph-bold ph-check me-1"></i> Mulai Import</button>
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
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    minimumResultsForSearch: Infinity
                });

                $('.select2-recall').select2({
                    dropdownParent: $('#recallCustomerModal'),
                    theme: 'bootstrap-5'
                });

                $('#user_id').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Search & Select User'
                });

                $('#account_group, #customer_class, #term_of_payment, #output_tax, #ccar, #bank_garansi').select2({
                    dropdownParent: $('#customerModal'),
                    theme: 'bootstrap-5',
                    placeholder: 'Select Option'
                });

                function addRecallItemRow(name = '', qty = '', price = '') {
                    const index = $('#recall_items_body tr').length;
                    const row = `
                        <tr class="recall-item-row">
                            <td class="ps-4">
                                <input type="text" class="form-control form-control-sm"
                                    name="items[${index}][item_name]"
                                    value="${name}" placeholder="Contoh: Jasa Web Dev" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm"
                                    name="items[${index}][quantity]"
                                    value="${qty}" placeholder="0" min="1" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm"
                                    name="items[${index}][price]"
                                    value="${price}" placeholder="0" min="0">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger btn-remove-recall-item hover-bg-light rounded-circle">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#recall_items_body').append(row);
                    checkRecallEmptyState();
                }

                function checkRecallEmptyState() {
                    if ($('#recall_items_body tr').length === 0) {
                        $('#recall_no_items').show();
                        $('#recall_items_table').hide();
                    } else {
                        $('#recall_no_items').hide();
                        $('#recall_items_table').show();
                    }
                }

                $('#btn-recall-add-item').on('click', function() {
                    addRecallItemRow();
                });

                $(document).on('click', '.btn-remove-recall-item', function() {
                    $(this).closest('tr').remove();
                    // Re-index names untuk array items[...]
                    $('#recall_items_body tr').each(function(idx) {
                        $(this).find('input').each(function() {
                            let name = $(this).attr('name');
                            if (name) {
                                const newName = name.replace(/items\[\d+\]/, `items[${idx}]`);
                                $(this).attr('name', newName);
                            }
                        });
                    });
                    checkRecallEmptyState();
                });

                $(document).on('click', '.btn-recall-customer', function() {
                    let btn = $(this);
                    let rawData = btn.attr('data-json');
                    let data = {};

                    try {
                        data = JSON.parse(rawData);
                    } catch (e) {
                        console.error("Failed to parse recall JSON data:", e);
                        Swal.fire('Error', 'Failed to load customer data.', 'error');
                        return;
                    }

                    // Reset Form & Table
                    $('#recallCustomerForm')[0].reset();
                    $('#recall_items_body').empty();

                    if (data.reject_note && data.reject_note !== '-' && data.reject_note !==
                        'Tidak ada catatan rejection.') {
                        $('#recall_reject_reason').text(data.reject_note);
                        $('#recall_reject_alert').show(); // Tampilkan alert merah
                    } else {
                        $('#recall_reject_alert').hide(); // Sembunyikan jika tidak ada note
                    }

                    // Set ID & Reset Tabs ke awal
                    $('#recall_customer_id').val(data.id);
                    $('.nav-pills button:first').tab('show');

                    // --- 2. POPULATE FIELDS (Mapping JSON ke ID Input) ---

                    // Select2 Fields (Set Value & Trigger Change)
                    $('#recall_user_id').val(data.user_id);
                    $('#recall_account_group').val(data.account_group).trigger('change');
                    $('#recall_customer_class').val(data.customer_class).trigger('change');
                    $('#recall_term_of_payment').val(data.term_of_payment).trigger('change');

                    // Perbaikan Logika BG (Boolean/String conversion)
                    let bgVal = (data.bank_garansi == '1' || data.bank_garansi == 'YA') ? 'YA' : 'TIDAK';
                    $('#recall_bank_garansi').val(bgVal).trigger('change');

                    $('#recall_output_tax').val(data.output_tax).trigger('change');
                    $('#recall_ccar').val(data.ccar).trigger('change');

                    // Text Inputs (General)
                    $('#recall_name').val(data.name);
                    $('#recall_sort_name').val(data.sort_name);
                    $('#recall_email').val(data.email);
                    $('#recall_no_pkd').val(data.no_pkd);
                    $('#recall_pic').val(data.pic);

                    // Address Inputs
                    $('#recall_address1').val(data.address1);
                    $('#recall_address2').val(data.address2);
                    $('#recall_address3').val(data.address3);
                    $('#recall_city').val(data.city);
                    $('#recall_area').val(data.area);
                    $('#recall_postal_code').val(data.postal_code);
                    $('#recall_credit_limit').val(data.credit_limit);

                    // Management & Shipping (Previously Hidden/Missing)
                    $('#recall_shipping_to_name').val(data.shipping_to_name);
                    $('#recall_shipping_to_address').val(data.shipping_to_address);

                    $('#recall_purchasing_manager_name').val(data.purchasing_manager_name);
                    $('#recall_purchasing_manager_email').val(data.purchasing_manager_email);
                    $('#recall_finance_manager_name').val(data.finance_manager_name);
                    $('#recall_finance_manager_email').val(data.finance_manager_email);

                    // Billing & Tax
                    $('#recall_penagihan_nama_kontak').val(data.penagihan_nama_kontak);
                    $('#recall_penagihan_telepon').val(data.penagihan_telepon);
                    $('#recall_penagihan_address').val(data.penagihan_address);
                    $('#recall_surat_menyurat_address').val(data.surat_menyurat_address);

                    $('#recall_npwp').val(data.npwp);
                    $('#recall_tanggal_npwp').val(data.tanggal_npwp);
                    $('#recall_nppkp').val(data.nppkp);
                    $('#recall_tanggal_nppkp').val(data.tanggal_nppkp);
                    $('#recall_tax_contact_name').val(data.tax_contact_name);
                    $('#recall_tax_contact_email').val(data.tax_contact_email);
                    $('#recall_tax_contact_phone').val(data.tax_contact_phone);

                    // --- 3. POPULATE ITEMS ---
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            addRecallItemRow(item.item_name, item.quantity, item.price);
                        });
                    } else {
                        checkRecallEmptyState(); // Show empty state
                    }

                    // --- 4. POPULATE FILE PREVIEWS ---
                    function createPreviewBtn(path, title) {
                        if (path && !path.includes('null')) {
                            return `
                                <button type="button" class="btn btn-xs btn-primary btn-view-file-trigger rounded-pill"
                                    data-path="${path}"
                                    data-title="${title} - EXISTING">
                                    <i class="ph-bold ph-eye me-1"></i> Preview
                                </button>
                            `;
                        }
                        return '<span class="badge bg-secondary opacity-50">Empty</span>';
                    }

                    $('#recall_preview_npwp').html(createPreviewBtn(data.file_npwp_path, 'NPWP'));
                    $('#recall_preview_nib').html(createPreviewBtn(data.file_nib_path, 'NIB'));
                    $('#recall_preview_ktp').html(createPreviewBtn(data.file_ktp_path, 'KTP'));
                    $('#recall_preview_akte').html(createPreviewBtn(data.file_akte_path, 'Akte Pendirian'));
                    $('#recall_preview_company_profile').html(createPreviewBtn(data.file_company_profile_path,
                        'Company Profile'));

                    $('#recallCustomerModal').modal('show');
                });

                $('#recallCustomerForm').on('submit', function(e) {
                    e.preventDefault();

                    const id = $('#recall_customer_id').val();
                    const formData = new FormData(this);
                    const url = `/customers/${id}/recall`;

                    // Normalize credit_limit (Recall form doesn't run HTML5 validity checks)
                    const topVal = $('#recall_term_of_payment').val();
                    const bgVal = $('#recall_bank_garansi').val();
                    let rawCreditLimit = formData.get('credit_limit');

                    if (bgVal === 'YA' || String(topVal).toUpperCase() === 'CBD') {
                        formData.set('credit_limit', '0');
                    } else if (rawCreditLimit) {
                        let cleanCreditLimit = rawCreditLimit.toString().replace(/[^0-9]/g, '');
                        formData.set('credit_limit', cleanCreditLimit);
                    }

                    const currentStatusFilter = $('#statusFilter').val();
                    const currentApprovalFilter = $('#approvalStatusFilter').val();

                    Swal.fire({
                        title: 'Konfirmasi Recall',
                        text: "Data akan diajukan ulang (Resubmit) dan status kembali Pending. Pastikan revisi sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Submit!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Processing...',
                                html: 'Sedang menyimpan perubahan...',
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

                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Recall Berhasil!',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            $('#recallCustomerModal').modal('hide');
                                            $('#statusFilter').val('all').trigger(
                                                'change.select2');
                                            $('#approvalStatusFilter').val('all')
                                                .trigger('change.select2');

                                            if (typeof table !== 'undefined') {
                                                table.search('').ajax.reload(null,
                                                    true);
                                            } else {
                                                $('#sampleTable').DataTable()
                                                    .search('').ajax.reload(null,
                                                        true);
                                            }
                                        });
                                    } else {
                                        $('#statusFilter').val(currentStatusFilter).trigger(
                                            'change.select2');
                                        $('#approvalStatusFilter').val(
                                            currentApprovalFilter).trigger(
                                            'change.select2');
                                        Swal.fire('Failed!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    let msg = 'An error occurred on the server.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) msg =
                                        xhr.responseJSON.message;
                                    Swal.fire('Error!', msg, 'error');
                                }
                            });
                        }
                    });
                });

                $('#customerForm').on('submit', function(e) {
                    e.preventDefault();

                    if (!this.checkValidity()) {
                        e.stopPropagation();
                        this.reportValidity();
                        return;
                    }

                    const formData = new FormData(this);
                    let rawCreditLimit = formData.get('credit_limit');
                    if (rawCreditLimit) {
                        let cleanCreditLimit = rawCreditLimit.toString().replace(/[^0-9]/g, '');
                        formData.set('credit_limit', cleanCreditLimit);
                    }

                    const url = "{{ route('customers.store') }}";
                    const currentStatusFilter = $('#statusFilter').val();
                    const currentApprovalFilter = $('#approvalStatusFilter').val();

                    Swal.fire({
                        title: 'Konfirmasi Penyimpanan',
                        text: "Pastikan seluruh data yang diinput sudah benar.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Simpan!'
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

                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            $('#customerModal').modal('hide');
                                            $('#statusFilter').val('all').trigger(
                                                'change.select2');
                                            $('#approvalStatusFilter').val('all')
                                                .trigger('change.select2');

                                            if (typeof table !== 'undefined') {
                                                table.search('').ajax.reload(null,
                                                    true);
                                            } else {
                                                $('#sampleTable').DataTable()
                                                    .search('').ajax.reload(null,
                                                        true);
                                            }
                                        });
                                    } else {
                                        $('#statusFilter').val(currentStatusFilter).trigger(
                                            'change.select2');
                                        $('#approvalStatusFilter').val(
                                            currentApprovalFilter).trigger(
                                            'change.select2');
                                        Swal.fire('Failed!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    let msg = 'An error occurred on the server.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) msg =
                                        xhr.responseJSON.message;
                                    Swal.fire('Error!', msg, 'error');
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
                        '<div class="mt-2 text-info" id="ocr-status"><i class="ph-bold ph-spinner ph-spin me-1"></i> Reading NPWP (Scanning all lines)...</div>'
                        );
                    $(this).closest('.card-body').append(notice);

                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        try {
                            Tesseract.recognize(evt.target.result, 'eng', {
                                    logger: m => console.log(m)
                                })
                                .then(result => {
                                    const text = result.data.text || '';
                                    const lines = text.split(/\r?\n/).map(s => s.trim()).filter(
                                    Boolean);

                                    // 1. CARI POSISI BARIS NPWP
                                    let npwpLineIdx = -1;
                                    let npwpValue = '';
                                    const npwpRegexStrict = /\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}/;
                                    const npwpRegexLoose = /[0-9\.\-\s]{15,25}/;

                                    for (let i = 0; i < lines.length; i++) {
                                        let match = lines[i].match(npwpRegexStrict);
                                        if (match) {
                                            npwpLineIdx = i;
                                            npwpValue = match[0];
                                            break;
                                        }
                                    }
                                    // Fallback search
                                    if (npwpLineIdx === -1) {
                                        for (let i = 0; i < lines.length; i++) {
                                            if (npwpRegexLoose.test(lines[i]) && lines[i].replace(/\D/g,
                                                    '').length >= 15) {
                                                npwpLineIdx = i;
                                                npwpValue = lines[i].match(npwpRegexLoose)[0];
                                                break;
                                            }
                                        }
                                    }

                                    if (npwpValue) {
                                        $('#npwp').val(npwpValue.trim());
                                    }

                                    if (npwpLineIdx !== -1 && lines.length > npwpLineIdx + 1) {
                                        let rawName = lines[npwpLineIdx + 1];
                                        rawName = rawName.replace(/^(Nama|Name)\s*[:.]?\s*/i, '')
                                    .trim();
                                        let safeName = rawName.replace(/[^a-zA-Z0-9\s\.\,\(\)\-\&]/g,
                                            '').trim();

                                        $('#name').val(safeName);
                                        let bgStatus = $('#bank_garansi').val();

                                        if (bgStatus === 'YA' || bgStatus === '1') {
                                            generatePkdNumber(safeName);
                                        } else {
                                            $('#no_pkd').val('');
                                            console.log('Skip generate PKD karena Bank Garansi = NO');
                                        }
                                    }

                                    // 3. AMBIL ALAMAT (LOOPING SAMPAI KETEMU "FOOTER")
                                    let rawAddress = '';
                                    if (npwpLineIdx !== -1) {
                                        let addressLines = [];
                                        let startIndex = npwpLineIdx + 2;

                                        // Loop dari baris alamat pertama sampai habis array
                                        for (let i = startIndex; i < lines.length; i++) {
                                            let currentLine = lines[i];

                                            // Jika ketemu kata "Penerbit", "Terdaftar", "KPP", stop pengambilan alamat
                                            if (currentLine.match(
                                                    /(Penerbit|Terdaftar|KPP|Pratama|Kanwil|Direktorat)/i
                                                    )) {
                                                break;
                                            }

                                            // Abaikan jika baris terlalu pendek (misal sampah OCR: ".")
                                            if (currentLine.length > 2) {
                                                addressLines.push(currentLine);
                                            }
                                        }

                                        // Gabungkan semua baris alamat yang ditemukan jadi satu string panjang
                                        rawAddress = addressLines.join(' ');

                                        // Bersihkan label "Alamat :" atau "Jalan" di awal (jika ada)
                                        rawAddress = rawAddress.replace(
                                            /^(Alamat|Address|Jalan|Jl)\s*[:.]?\s*/i, '').trim();
                                    }

                                    // Fungsi Chunking (Max 36 Karakter per Input)
                                    function splitChunksWordWrap(str, len) {
                                        if (!str) return [];
                                        str = str.replace(/\s+/g, ' ').trim(); // Normalisasi spasi
                                        const words = str.split(' ');
                                        const out = [];
                                        let line = '';
                                        words.forEach(w => {
                                            if ((line + ' ' + w).trim().length <= len) {
                                                line = (line + ' ' + w).trim();
                                            } else {
                                                if (line) out.push(line);
                                                // Handle kata yang lebih panjang dari limit (36)
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

                                    // Distribusi ke Input Address 1, 2, 3
                                    const chunks = splitChunksWordWrap(rawAddress || '', 36);

                                    try {
                                        // Reset field
                                        $('#address1, #address2, #address3').val('');

                                        if ($('#address1').length) $('#address1').val(chunks[0] || '');
                                        if ($('#address2').length) $('#address2').val(chunks[1] || '');
                                        if ($('#address3').length) $('#address3').val(chunks[2] || '');
                                    } catch (e) {
                                        console.error('Error setting address fields', e);
                                    }

                                    $('#ocr-status').remove();
                                    originalBtn.prop('disabled', false);
                                })
                                .catch(err => {
                                    console.error('OCR error', err);
                                    $('#ocr-status').text('OCR failed (Manual Input Required)')
                                        .addClass('text-danger');
                                    originalBtn.prop('disabled', false);
                                });
                        } catch (e) {
                            $('#ocr-status').text('Error processing file').addClass('text-danger');
                            originalBtn.prop('disabled', false);
                        }
                    };
                    reader.readAsDataURL(file);
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
                                $('#no_pkd').val(response.number).removeClass('text-muted').addClass(
                                    'fw-bold');
                            } else {
                                console.warn('Generate Failed:', response);
                                $('#no_pkd').val('').attr('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            console.error('Response:', xhr.responseText);

                            if (xhr.status === 403) {
                                $('#no_pkd').val('Error: Unauthorized (403)');
                            } else {
                                $('#no_pkd').val('').attr('placeholder', '(Failed to generate)');
                            }
                        }
                    });
                }

                const table = $('#sampleTable').DataTable({
                    processing: true,
                    serverSide: true,
                    // responsive: true,
                    ajax: {
                        url: "{{ route('customers.index') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                            d.approval_status = $('#approvalStatusFilter').val();
                        }
                    },
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "🔍 Search customer..."
                    },
                    order: [
                        [8, 'desc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center align-middle'
                        },
                        {
                            data: 'code',
                            name: 'customers.code',
                            className: 'dt-no-wrap align-middle'
                        },
                        {
                            data: 'name',
                            name: 'customers.name',
                            className: 'dt-no-wrap align-middle'
                        },
                        {
                            data: 'credit_limit',
                            name: 'customers.credit_limit',
                            className: 'dt-wrap align-middle'
                        },
                        {
                            data: 'financial_info',
                            name: 'customers.term_of_payment',
                            className: 'dt-no-wrap align-middle'
                        },
                        {
                            data: 'status_approval',
                            name: 'customers.status_approval',
                            className: 'text-center dt-no-wrap align-middle'
                        },
                        {
                            data: 'route_to',
                            name: 'customers.route_to',
                            className: 'text-center dt-wrap align-middle',
                            width: '15%'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center dt-no-wrap align-middle'
                        },
                        {
                            data: 'updated_at',
                            name: 'customers.updated_at',
                            visible: false,
                            searchable: false
                        }
                    ],
                    autoWidth: false
                });

                $(document).on('submit', 'form.delete-form.delete-customer-btn', function(e) {
                    e.preventDefault();

                    const form = this;
                    const $form = $(form);

                    Swal.fire({
                        title: 'Delete customer?',
                        text: 'Data will be permanently deleted and cannot be recovered.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (!result.isConfirmed) return;
                        Swal.fire({
                            title: 'Deleting... ',
                            text: 'Please wait a moment.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                                url: $form.attr('action'),
                                method: 'POST',
                                data: $form.serialize(),
                                dataType: 'json'
                            })
                            .done(function(response) {
                                Swal.close();

                                if (response && response.success) {
                                    Swal.fire('Success!', response.message ||
                                            'Customer deleted successfully.', 'success')
                                        .then(() => {
                                            try {
                                                if (typeof table !== 'undefined') {
                                                    table.ajax.reload(null, false);
                                                } else if ($('#sampleTable').length) {
                                                    $('#sampleTable').DataTable().ajax.reload(
                                                        null, false);
                                                }
                                            } catch (err) {
                                                console.warn('Reload DataTable failed', err);
                                            }
                                        });
                                    return;
                                }

                                Swal.fire('Failed!', (response && response.message) ? response
                                    .message : 'Failed to delete customer.', 'error');
                            })
                            .fail(function(xhr) {
                                Swal.close();

                                let msg = 'An error occurred while deleting data.';

                                if (xhr && xhr.status === 419) {
                                    msg = 'Session expired. Please refresh the page and try again.';
                                } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }

                                Swal.fire('Error!', msg, 'error');
                            });
                    });
                });

                (function() {
                    const modalSelector = '#customerModal, #recallCustomerModal';

                    $(document).on('show.bs.modal', modalSelector, function() {
                        const saved = {
                            status: $('#statusFilter').val(),
                            approval: $('#approvalStatusFilter').val(),
                            search: (typeof table !== 'undefined') ? table.search() : ($(
                                '#sampleTable_filter input').val() || '')
                        };
                        $(this).data('savedFilters', saved);
                    });

                    $(document).on('shown.bs.modal', modalSelector, function() {
                        const saved = $(this).data('savedFilters');
                        if (!saved) return;

                        $('#statusFilter').val(saved.status).trigger('change');
                        $('#approvalStatusFilter').val(saved.approval).trigger('change');

                        try {
                            if (typeof table !== 'undefined') {
                                table.search(saved.search).draw(false);
                            } else if ($('#sampleTable').length) {
                                $('#sampleTable').DataTable().search(saved.search).draw(false);
                            }
                        } catch (e) {
                            console.warn('Restore search failed', e);
                        }
                    });
                })();

                $('#statusFilter, #approvalStatusFilter').on('change', function() {
                    table.draw();
                });

                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                    $('#approvalStatusFilter').val('all').trigger('change');
                });

                $('#user_id').on('change', function() {
                    const selected = $(this).find(':selected');
                    const userId = selected.val();

                    if (userId) {
                        $('#user_position').val(selected.data('pos') || '-');
                        $('#user_branch').val(selected.data('branch') || '-');
                        $('#user_region').val(selected.data('region') || '-');

                        // ==========================================
                        // LOGIKA BARU: AUTO SELECT ACCOUNT GROUP
                        // ==========================================
                        const linkedAgId = selected.data('account-group-id');
                        if (linkedAgId) {
                            $('#account_group').val(linkedAgId).trigger('change');
                            // $('#account_group').select2({ disabled: true, theme: 'bootstrap-5' });
                        }

                        $('#user-info-section').slideDown();
                        $('#main-form-section').slideDown();
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

                        let bgValue = (rawBg == 1 || rawBg === true || rawBg === '1' || rawBg === 'YA') ? 'YA' :
                            'TIDAK';

                        $('#bank_garansi').val(bgValue).trigger('change');

                        $('#ccar').val(rawCcar).trigger('change');
                    }

                    if (ag && cc) {
                        $('#btn-save-customer').prop('disabled', false);
                    } else {
                        $('#btn-save-customer').prop('disabled', true);
                    }
                });

                function checkCreditLimitAccess() {
                    const bgVal = $('#bank_garansi').val();
                    const topVal = $('#term_of_payment').val();
                    const clInput = $('#credit_limit');
                    const pkdInput = $('#no_pkd');

                    clInput.parent().find('.cl-status-note').remove();

                    if (bgVal === 'YA' || topVal === 'CBD') {

                        clInput.val(0).prop('readonly', true)
                            .removeClass('bg-white cursor-pointer border-danger').addClass('bg-light')
                            .attr('placeholder', '0');

                        let msg = '';
                        if (bgVal === 'YA') msg = 'Credit Limit otomatis 0 (BG Aktif).';
                        else if (topVal === 'CBD') msg = 'Credit Limit otomatis 0 (Cash Before Delivery).';

                        clInput.after(
                            `<small class="cl-status-note text-info fw-bold mt-1 d-block"><i class="ph-bold ph-info me-1"></i> ${msg}</small>`
                            );

                    } else {
                        clInput.prop('readonly', true)
                            .removeClass('bg-light').addClass('bg-white cursor-pointer border-danger')
                            .attr('placeholder', 'Click to calculate');

                        clInput.after(
                            '<small class="cl-status-note text-danger fw-bold mt-1 d-block"><i class="ph-bold ph-calculator me-1"></i> Click to calculate</small>'
                            );
                    }
                }

                function checkRecallCreditLimitAccess() {
                    const bgVal = $('#recall_bank_garansi').val();
                    const topVal = $('#recall_term_of_payment').val();
                    const clInput = $('#recall_credit_limit');

                    if (!clInput.length) return;

                    clInput.parent().find('.recall-cl-status-note').remove();

                    if (bgVal === 'YA' || String(topVal).toUpperCase() === 'CBD') {
                        clInput.val(0).prop('readonly', true).addClass('bg-light');

                        let msg = '';
                        if (bgVal === 'YA') msg = 'Credit Limit otomatis 0 (BG Aktif).';
                        else msg = 'Credit Limit otomatis 0 (Cash Before Delivery).';

                        clInput.after(`<small class="recall-cl-status-note text-info fw-bold mt-1 d-block"><i class="ph-bold ph-info me-1"></i> ${msg}</small>`);
                    } else {
                        if (String(clInput.val()).trim() === '0') {
                            clInput.val('');
                        }
                        clInput.prop('readonly', false).removeClass('bg-light');
                    }
                }

                $('#term_of_payment').on('change', function() {
                    checkCreditLimitAccess();
                    const v = $(this).val();

                    if ($('#calc_top').length && $('#calc_top').val() !== v) {
                        $('#calc_top').val(v).trigger('change');
                    }

                    const modalVisible = $('#creditCalcModal').hasClass('show') || $('#creditCalcModal').is(
                        ':visible');
                    if ($('#calc_top').length && modalVisible) {
                        const r = computeCreditValues();
                        $('#calc_preview_formatted').val(formatRupiah(Math.round(r.valFinal || 0)));
                    }
                });

                $('#recall_term_of_payment, #recall_bank_garansi').on('change', function() {
                    checkRecallCreditLimitAccess();
                });

                $('#bank_garansi').on('change', function() {
                    const val = $(this).val();
                    const pkdInput = $('#no_pkd');
                    const customerName = $('#name').val();

                    pkdInput.parent().find('.pkd-status-note').remove();

                    if (val === 'TIDAK') {
                        pkdInput.val('').prop('readonly', true).removeClass('fw-bold');
                        pkdInput.after(
                            '<small class="pkd-status-note text-danger fw-bold mt-1 d-block"><i class="ph-bold ph-info me-1"></i> This Customer does not use Bank Guarantee.</small>'
                            );
                    } else {
                        pkdInput.prop('readonly', true).addClass('fw-bold');
                        if (customerName && customerName.length > 3) {
                            generatePkdNumber(customerName);
                        } else {
                            pkdInput.val('').attr('placeholder', 'Enter Customer Name to Generate PKD...');
                        }
                    }

                    checkCreditLimitAccess();
                });

                $('#name').on('blur', function() {
                    let val = $(this).val();
                    let bgStatus = $('#bank_garansi').val();

                    if (val.length > 3 && (bgStatus === 'YA' || bgStatus === '1')) {
                        generatePkdNumber(val);
                    }
                });

                $(document).on('focus', '#credit_limit', function(e) {

                    const bgStatus = $('#bank_garansi').val();
                    const topVal = $('#term_of_payment').val();
                    const termString = $('#term_of_payment').val();

                    if (!topVal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Term of Payment Required',
                            text: 'Please select Term of Payment first before calculating the limit.'
                        });
                        $('#term_of_payment').select2('open');
                        return;
                    }

                    if (bgStatus === 'YA' || topVal === 'CBD') {
                        return;
                    }


                    $('#calc_products').empty();

                    let existingItems = {};
                    let hasItems = false;

                    $('#customerForm').find('.hidden-item-input').each(function() {
                        let nameAttr = $(this).attr('name');
                        let matches = nameAttr.match(/items\[(\d+)\]\[(\w+)\]/);

                        if (matches) {
                            hasItems = true;
                            let index = matches[1];
                            let field = matches[2];

                            if (!existingItems[index]) existingItems[index] = {};
                            existingItems[index][field] = $(this).val();
                        }
                    });

                    if (hasItems) {
                        Object.keys(existingItems).forEach(function(key) {
                            let item = existingItems[key];
                            addCalcRow(item.item_name, item.quantity, item.price);
                        });
                    } else {
                        addCalcRow();
                    }

                    $('#calc_top').val(termString).trigger('change');

                    let ltVal = parseFloat($('#lead_time').val()) || 0;
                    $('#calc_lt').val(ltVal);

                    const initialR = computeCreditValues();
                    $('#calc_preview_formatted').val(formatRupiah(Math.round(initialR.valFinal || 0)));

                    new bootstrap.Modal(document.getElementById('creditCalcModal')).show();
                });

                $('#btn-create-customer').on('click', function() {
                    $('#customerForm')[0].reset();
                    $('.select2-styled').val(null).trigger('change');

                    $('#customerForm').find('input, textarea, select').prop('disabled', false);
                    $('#credit_limit').prop('readonly', true);
                    $('#no_pkd').val('').prop('readonly', true);

                    let currentUserId = $('#current_user_id').val();
                    let isUserInList = $('#user_id option[value="' + currentUserId + '"]').length > 0;

                    $('#hidden_user_id').remove();

                    if (isUserInList) {
                        $('#user_id').val(currentUserId).trigger('change');
                        $('#user_id').prop('disabled', true);

                        $('<input>').attr({
                            type: 'hidden',
                            id: 'hidden_user_id',
                            name: 'user_id',
                            value: currentUserId
                        }).appendTo('#customerForm');
                    } else {
                        $('#user_id').prop('disabled', false);
                    }

                    $('.btn-schedule').removeClass(
                        'active btn-dark btn-primary btn-success btn-info text-white');

                    $('.btn-schedule[data-val="All"]').addClass('btn-outline-dark');
                    $('.btn-date-box').addClass('btn-outline-secondary').removeClass('btn-info');

                    $('[data-type="payment_days"]').not('[data-val="All"]').addClass('btn-outline-primary');
                    $('[data-type="faktur_days"]').not('[data-val="All"]').addClass('btn-outline-success');

                    $('#create_payment_days_inputs, #create_payment_date_inputs, #create_faktur_days_inputs, #create_faktur_date_inputs')
                        .empty();

                    $('#customerModalLabel').text('Create New Customer');
                    $('#customerModal').modal('show');
                });

                function normalizeSelected(val) {
                    if (!val && val !== 0) return [];
                    if (Array.isArray(val)) return val.map(String);
                    if (typeof val === 'object') return Object.values(val).map(String);
                    if (typeof val === 'string') {
                        try {
                            const parsed = JSON.parse(val);
                            if (Array.isArray(parsed)) return parsed.map(String);
                            if (parsed === null) return [];
                        } catch (e) {
                            // not JSON
                        }
                        if (val.indexOf(',') !== -1) return val.split(',').map(s => s.trim());
                        return [val];
                    }
                    return [String(val)];
                }

                function renderDaySelector(containerSelector, rawVal, isPayment) {
                    const selected = normalizeSelected(rawVal);
                    const $container = $(containerSelector);
                    $container.find('[data-type]').each(function() {
                        const $b = $(this);
                        const val = String($b.data('val'));
                        if (val === 'All') {
                            if (selected.length === 1 && (selected[0] === 'All' || selected[0] === 'all')) {
                                $b.removeClass('btn-outline-dark').addClass('btn-dark');
                            } else {
                                $b.removeClass('btn-dark').addClass('btn-outline-dark');
                            }
                        } else {
                            const isSelected = selected.includes(val);
                            if (isPayment) {
                                if (isSelected) $b.removeClass('btn-outline-primary').addClass(
                                    'btn-primary text-white');
                                else $b.removeClass('btn-primary text-white').addClass('btn-outline-primary');
                            } else {
                                if (isSelected) $b.removeClass('btn-outline-success').addClass(
                                    'btn-success text-white');
                                else $b.removeClass('btn-success text-white').addClass('btn-outline-success');
                            }
                        }
                    });
                }

                function renderDateBoxes(boxesSelector, rawVal) {
                    const selected = normalizeSelected(rawVal).map(String);
                    const $boxes = $(boxesSelector).find('.btn-date-box');
                    if (typeof rawVal === 'string' && rawVal.toLowerCase() === 'all') {
                        $(boxesSelector).siblings('button[data-val="All"]').removeClass('btn-outline-dark').addClass(
                            'btn-dark');
                        $boxes.each(function() {
                            $(this).removeClass('btn-info text-white').addClass('btn-outline-secondary');
                        });
                        return;
                    } else {
                        $(boxesSelector).siblings('button[data-val="All"]').removeClass('btn-dark').addClass(
                            'btn-outline-dark');
                    }

                    $boxes.each(function() {
                        const $b = $(this);
                        const val = String($b.data('val'));
                        if (selected.includes(val)) {
                            $b.removeClass('btn-outline-secondary').addClass('btn-info text-white');
                        } else {
                            $b.removeClass('btn-info text-white').addClass('btn-outline-secondary');
                        }
                    });
                }

                $(document).on('click', '.btn-show-customer', function() {
                    const btn = $(this);

                    $('#view_header_name').text(btn.data('name'));
                    $('#view_header_code').text(btn.data('code') || 'New');

                    const status = btn.data('status');
                    $('#view_status_badge').text(status)
                        .removeClass('bg-success bg-secondary')
                        .addClass(status === 'Active' ? 'bg-success' : 'bg-secondary');

                    const approval = btn.data('status_approval');
                    let approvalColor = 'text-muted';
                    if (approval === 'Approved') approvalColor = 'text-success';
                    if (approval === 'Rejected') approvalColor = 'text-danger';
                    if (approval === 'Processing') approvalColor = 'text-primary';
                    $('#view_approval_badge').text(approval).attr('class', 'fw-bold ' + approvalColor);

                    // $('#view_updated_at').text(btn.data('updated_at') || '-');

                    $('#view_name').text(btn.data('name'));
                    $('#view_sort_name').text(btn.data('sort_name') || '-');
                    $('#view_email').text(btn.data('email'));
                    $('#view_no_pkd').text(btn.data('no_pkd') || '-');
                    $('#view_pic').text(btn.data('pic') || '-');

                    const addr1 = btn.data('address1') || '';
                    const addr2 = btn.data('address2') ? ', ' + btn.data('address2') : '';
                    const addr3 = btn.data('address3') ? ', ' + btn.data('address3') : '';
                    $('#view_full_address').text(addr1 + addr2 + addr3);

                    $('#view_city').text(btn.data('city'));
                    $('#view_area').text(btn.data('area'));
                    $('#view_postal_code').text(btn.data('postal_code'));

                    // 3. FINANCIAL
                    const cl = parseFloat(btn.data('credit_limit')) || 0;
                    $('#view_credit_limit').text('IDR ' + cl.toLocaleString('id-ID'));
                    $('#view_top').text(btn.data('term_of_payment'));

                    $('#view_npwp').text(btn.data('npwp'));
                    $('#view_tanggal_npwp').text(btn.data('tanggal_npwp') || '-');
                    $('#view_nppkp').text(btn.data('nppkp'));
                    $('#view_output_tax').text(btn.data('output_tax'));

                    $('#view_penagihan_nama').text(btn.data('penagihan_nama_kontak'));
                    $('#view_penagihan_telp').text(btn.data('penagihan_telepon'));
                    $('#view_penagihan_addr').text(btn.data('penagihan_address'));

                    $('#view_purc_name').text(btn.data('purchasing_manager_name'));
                    $('#view_purc_email').text(btn.data('purchasing_manager_email'));
                    $('#view_fin_name').text(btn.data('finance_manager_name'));
                    $('#view_fin_email').text(btn.data('finance_manager_email'));
                    $('#view_tax_name').text(btn.data('tax_contact_name'));
                    $('#view_tax_email').text(btn.data('tax_contact_email'));

                    $('#view_shipping_name').text(btn.data('shipping_to_name'));
                    $('#view_shipping_address').text(btn.data('shipping_to_address'));

                    const paymentDaysRaw = btn.attr('data-payment_days') || btn.data('payment_days');
                    const paymentDateRaw = btn.attr('data-payment_date') || btn.data('payment_date');
                    const fakturDaysRaw = btn.attr('data-faktur_days') || btn.data('faktur_days');
                    const fakturDateRaw = btn.attr('data-faktur_date') || btn.data('faktur_date');

                    renderDaySelector('#view_payment_days_container', paymentDaysRaw, true);
                    renderDateBoxes('#view_payment_date_boxes', paymentDateRaw);
                    renderDaySelector('#view_faktur_days_container', fakturDaysRaw, false);
                    renderDateBoxes('#view_faktur_date_boxes', fakturDateRaw);

                    const docs = [{
                            name: 'NPWP Document',
                            path: btn.data('file_npwp_path'),
                            icon: 'ph-file-text'
                        },
                        {
                            name: 'NIB / SIUP',
                            path: btn.data('file_nib_path'),
                            icon: 'ph-file-code'
                        },
                        {
                            name: 'KTP Penanggung Jawab',
                            path: btn.data('file_ktp_path'),
                            icon: 'ph-cardholder'
                        },
                        {
                            name: 'Akte Pendirian',
                            path: btn.data('file_akte_path'),
                            icon: 'ph-scroll'
                        },
                        {
                            name: 'Company Profile',
                            path: btn.data('file_company_profile_path'),
                            icon: 'ph-buildings'
                        }
                    ];

                    let docHtml = '';
                    let hasDoc = false;

                    docs.forEach(doc => {
                        if (doc.path && typeof doc.path === 'string' && doc.path.length > 20 && !doc
                            .path.includes('null')) {
                            hasDoc = true;
                            docHtml += `
                                <div class="col-md-4">
                                    <div class="card h-100 border border-secondary border-opacity-25 shadow-sm hover-shadow transition-all">
                                        <div class="card-body text-center p-3">
                                            <div class="bg-light rounded p-2 mb-2 d-inline-block">
                                                <i class="ph-duotone ${doc.icon} f-s-28 text-primary"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark f-s-14 mb-1">${doc.name}</h6>
                                            <button class="btn btn-sm btn-primary w-100 mt-2 btn-view-file-trigger"
                                                data-path="${doc.path}"
                                                data-title="${doc.name}">
                                                <i class="ph-bold ph-eye me-1"></i> Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    if (hasDoc) {
                        $('#document_grid').html(docHtml).show();
                        $('#no_documents').hide();
                    } else {
                        $('#document_grid').hide();
                        $('#no_documents').show();
                    }

                    $('#customerDetailModal').modal('show');
                });

                function fmt(n) {
                    if (isNaN(n)) return '0';
                    return Number(n).toLocaleString(undefined, {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                }

                $(document).on('keyup', '.calc-price', function() {
                    let val = $(this).val();
                    $(this).val(formatRupiah(val));
                    const r = computeCreditValues();
                    $('#calc_preview_formatted').val(formatRupiah(Math.round(r.valFinal || 0)));
                });

                // Compute credit limit preview using formula provided:
                // For payment X days:
                //   Credit = ((TOP + LT) * sum(QTY * HARGA)) / DIVIDER
                // Where DIVIDER =
                //   45 -> 45
                //   30 -> 30
                //   7  -> 30/4 = 7.5
                //   14 -> 30/2 = 15
                // This matches the formulas: (TOP+LT)*(QTY*HARGA)*100%/DIVIDER
                function computeCreditValues() {
                    let totalValue = 0;
                    $('#calc_products .calc-row').each(function() {
                        const q = parseFloat($(this).find('.calc-qty').val()) || 0;
                        const pStr = $(this).find('.calc-price').val();
                        const p = parseFloat(pStr.replace(/[^0-9]/g, '')) || 0; // Clean Rupiah manual
                        totalValue += (q * p);
                    });

                    // Extract numeric days from selected TOP (supports values like '30' or 'CBD')
                    const calcTopRaw = $('#calc_top').val();
                    let top = 0;
                    const m = String(calcTopRaw).match(/(\d+)/);
                    if (m && m[0]) top = parseFloat(m[0]) || 0;
                    const lt = parseFloat($('#calc_lt').val()) || 0;
                    const base = (top + lt) * totalValue;

                    let divider = top;
                    if (top === 7) divider = 7.5;
                    else if (top === 14) divider = 15;
                    if (divider === 0) divider = 1;

                    return {
                        base,
                        totalValue,
                        valFinal: base / divider
                    };
                }

                function formatRupiah(angka, prefix) {
                    if (!angka) return '';
                    var number_string = angka.toString().replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                    if (ribuan) {
                        separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }

                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
                }

                function cleanRupiah(angka) {
                    if (!angka) return 0;
                    return parseFloat(angka.toString().replace(/\./g, '').replace(/,/g, '.')) || 0;
                }

                function addCalcRow(name = '', qty = '', price = '') {
                    let displayPrice = price ? formatRupiah(price) : '';
                    const row = $('<div class="calc-row d-flex gap-2 mb-2">' +
                        '<input type="text" class="form-control calc-product-name" placeholder="Product name" />' +
                        '<input type="number" step="1" min="0" class="form-control calc-qty" placeholder="Qty" />' +
                        '<input type="text" class="form-control calc-price text-end" placeholder="Price (Rp)" />' +
                        '<button type="button" class="btn btn-outline-danger btn-remove-row" title="Remove">&minus;</button>' +
                        '</div>');

                    row.find('.calc-product-name').val(name);
                    row.find('.calc-qty').val(qty);
                    row.find('.calc-price').val(displayPrice);
                    $('#calc_products').append(row);
                }

                $(document).on('input change',
                    '#calc_products .calc-qty, #calc_products .calc-price, #calc_lt, #calc_top',
                    function() {
                        const r = computeCreditValues();
                        $('#calc_preview_formatted').val(formatRupiah(Math.round(r.valFinal || 0)));
                    });

                // When calc_top changes in modal, sync back to main TOP select and recalc
                $(document).on('change', '#calc_top', function() {
                    const v = $(this).val();
                    if ($('#term_of_payment').val() !== v) {
                        $('#term_of_payment').val(v).trigger('change');
                    }

                    const r = computeCreditValues();
                    const computed = Math.round(r.valFinal || 0);
                    $('#calc_preview_formatted').val(formatRupiah(computed));

                    // If Bank Garansi active or TOP is CBD, credit limit stays 0
                    const bgStatus = $('#bank_garansi').val();
                    if (bgStatus === 'YA' || String(v).toUpperCase() === 'CBD') {
                        $('#credit_limit').val(formatRupiah(0));
                    } else {
                        // Apply computed value directly to credit_limit field (dynamic behavior requested)
                        $('#credit_limit').val(formatRupiah(computed));
                    }

                    // Update lead_time and hidden top_calc so main form reflects change
                    const ltVal = $('#calc_lt').val() || 0;
                    $('#lead_time').val(ltVal);

                    if ($('#top_calc_hidden').length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'top_calc_hidden',
                            name: 'top_calc'
                        }).appendTo('#customerForm');
                    }
                    $('#top_calc_hidden').val(v);
                });

                $(document).on('click', '#addCalcRow', function() {
                    addCalcRow();
                });

                $(document).on('click', '.btn-remove-row', function() {
                    $(this).closest('.calc-row').remove();
                    const r = computeCreditValues();
                    $('#calc_preview_formatted').val(formatRupiah(Math.round(r.valFinal || 0)));
                });

                $(document).ready(function() {
                    $(document).on('click', '.btn-view-file-trigger', function(e) {
                        e.preventDefault();

                        let filePath = $(this).data('path');
                        const title = $(this).data('title');

                        console.log('Mencoba buka file:', filePath);

                        if (!filePath || filePath.trim() === '' || filePath.endsWith('/storage/')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No File Available',
                                text: 'File path is empty or invalid.',
                                confirmButtonColor: '#3085d6'
                            });
                            return;
                        }

                        $('#fileViewerTitle').html(
                            `<i class="ph-bold ph-eye me-2"></i> ${title.toUpperCase()}`);
                        let container = $('#fileContentArea');

                        container.html(`
                            <div class="text-center py-5">
                                <div class="spinner-border text-light mb-3" role="status"></div>
                                <div class="text-white-50">Loading file...</div>
                            </div>
                        `);

                        $('#fileViewerModal').modal('show');

                        let cleanPath = filePath.split('?')[0];
                        let extension = cleanPath.split('.').pop().toLowerCase();
                        let dialog = $('#fileViewerDialog');

                        dialog.removeClass('modal-xl modal-lg modal-sm').attr('style', '');

                        setTimeout(() => {
                            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                                dialog.css('max-width', 'fit-content');

                                container.html(`
                                    <img src="${filePath}"
                                        class="d-block shadow-lg rounded"
                                        style="max-height: 85vh; max-width: 90vw; border: .0625rem solid #555;"
                                        alt="Preview"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-center py-5\'><i class=\'ph-duotone ph-warning-circle text-danger f-s-48 mb-3\'></i><h5 class=\'text-white\'>Failed to Load Image</h5><p class=\'text-white-50 small\'>File not found on the server.<br>Please run: <code>php artisan storage:link</code></p></div>';"
                                    >
                                `);
                            } else if (extension === 'pdf') {
                                dialog.addClass('modal-xl');
                                container.html(`
                                    <iframe src="${filePath}"
                                            style="width: 100%; height: 85vh; border: none; background: white;"
                                            allowfullscreen>
                                    </iframe>
                                `);
                            } else {
                                dialog.addClass('modal-lg');
                                container.html(`
                                    <div class="text-center py-5 text-white">
                                        <i class="ph-duotone ph-file-arrow-down f-s-48 mb-3 opacity-50"></i>
                                        <p>Preview tidak tersedia untuk format <b>.${extension}</b></p>
                                        <a href="${filePath}" target="_blank" class="btn btn-primary rounded-pill px-4">
                                            <i class="ph-bold ph-download-simple me-2"></i> Download File
                                        </a>
                                    </div>
                                `);
                            }
                        }, 300);
                    });

                    $('#fileViewerModal').on('show.bs.modal', function() {
                        $(this).css('z-index', '1060');
                        setTimeout(function() {
                            $('.modal-backdrop').last().css('z-index', '1055');
                        }, 10);
                    });

                    $('#fileViewerModal').on('hidden.bs.modal', function() {
                        if ($('.modal.show').length > 0) {
                            $('body').addClass('modal-open');
                        }
                    });
                });

                $(document).on('click', '.btn-schedule', function() {
                    const btn = $(this);
                    const type = btn.data('type');
                    const value = btn.data('val');
                    const container = $(`#create_${type}_container`);
                    const isAll = value === 'All';

                    const colorClass = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                    const outlineClass = type.includes('faktur') ? 'btn-outline-success' :
                    'btn-outline-primary';
                    const dateSolid = 'btn-info';
                    const dateOutline = 'btn-outline-secondary';

                    if (isAll) {
                        const isActive = btn.hasClass('active');

                        if (!isActive) {
                            btn.addClass('active btn-dark').removeClass('btn-outline-dark');

                            container.find('.btn-schedule').not('[data-val="All"]').each(function() {
                                $(this).addClass('active text-white');
                                if ($(this).hasClass('btn-date-box')) {
                                    $(this).removeClass(dateOutline).addClass(dateSolid);
                                } else {
                                    $(this).removeClass(outlineClass).addClass(colorClass);
                                }
                            });
                        } else {
                            btn.removeClass('active btn-dark').addClass('btn-outline-dark');

                            container.find('.btn-schedule').not('[data-val="All"]').each(function() {
                                $(this).removeClass('active text-white btn-dark ' + colorClass + ' ' +
                                    dateSolid);
                                if ($(this).hasClass('btn-date-box')) {
                                    $(this).addClass(dateOutline);
                                } else {
                                    $(this).addClass(outlineClass);
                                }
                            });
                        }

                    } else {
                        const allBtn = container.find('[data-val="All"]');
                        if (allBtn.hasClass('active')) {
                            allBtn.removeClass('active btn-dark').addClass('btn-outline-dark');
                        }
                        if (btn.hasClass('active')) {
                            btn.removeClass('active text-white');

                            if (btn.hasClass('btn-date-box')) {
                                btn.removeClass(dateSolid).addClass(dateOutline);
                            } else {
                                btn.removeClass(colorClass).addClass(outlineClass);
                            }
                        } else {
                            btn.addClass('active text-white');

                            if (btn.hasClass('btn-date-box')) {
                                btn.removeClass(dateOutline).addClass(dateSolid);
                            } else {
                                btn.removeClass(outlineClass).addClass(colorClass);
                            }
                        }
                    }

                    btn.blur();
                    updateCreateHiddenInputs(type);
                });

                function updateCreateHiddenInputs(type) {
                    const container = $(`#create_${type}_container`);
                    const inputContainer = $(`#create_${type}_inputs`);
                    inputContainer.empty();

                    const allBtn = container.find('[data-val="All"]');

                    if (allBtn.hasClass('active')) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: `${type}[]`,
                            value: 'All'
                        }).appendTo(inputContainer);
                    } else {
                        container.find('.btn-schedule.active').not('[data-val="All"]').each(function() {
                            $('<input>').attr({
                                type: 'hidden',
                                name: `${type}[]`,
                                value: $(this).data('val')
                            }).appendTo(inputContainer);
                        });
                    }
                }

                $('#creditCalcForm').on('submit', function(e) {
                    e.preventDefault();
                    const r = computeCreditValues();
                    const chosen = Math.round(r.valFinal || 0);

                    $('#credit_limit').val(formatRupiah(chosen));

                    const ltVal = $('#calc_lt').val() || 0;
                    const topVal = $('#calc_top').val() || 0;

                    $('#lead_time').val(ltVal);

                    if ($('#top_calc_hidden').length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'top_calc_hidden',
                            name: 'top_calc'
                        }).appendTo('#customerForm');
                    }
                    $('#top_calc_hidden').val(topVal);

                    $('#customerForm').find('.hidden-item-input').remove();

                    $('#calc_products .calc-row').each(function(index) {
                        const name = $(this).find('.calc-product-name').val();
                        const qty = $(this).find('.calc-qty').val();
                        const price = $(this).find('.calc-price').val();
                        const cleanPrice = cleanRupiah(price);

                        if (name && qty) {
                            const container = $('#customerForm');

                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][item_name]`,
                                value: name
                            }).appendTo(container);

                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][quantity]`,
                                value: qty
                            }).appendTo(container);

                            $('<input>').attr({
                                type: 'hidden',
                                class: 'hidden-item-input',
                                name: `items[${index}][price]`,
                                value: cleanPrice
                            }).appendTo(container);
                        }
                    });

                    const modalEl = document.getElementById('creditCalcModal');
                    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    try {
                        inst.hide();
                    } catch (err) {
                        $('#creditCalcModal').modal('hide');
                    }
                });

                document.getElementById('creditCalcModal').addEventListener('hidden.bs.modal', function() {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                });

                $('#importForm').on('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    Swal.fire({
                        title: 'Importing...',
                        html: 'Please wait while the file is being imported.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('customers.import') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Import Selesai!',
                                    text: response.message,
                                    timer: 2500,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('#importModal').modal('hide');
                                    $('#importForm')[0].reset();
                                    $('#statusFilter').val('all').trigger('change.select2');
                                    $('#approvalStatusFilter').val('all').trigger(
                                        'change.select2');
                                    if (typeof table !== 'undefined') table.search('').ajax
                                        .reload(null, true);
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = 'An error occurred during import.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                                .message;
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                });
            });
        </script>
    @endpush

    <!-- Credit Limit Calculator Modal -->
    <div class="modal fade" id="creditCalcModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Credit Limit Calculator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="creditCalcForm">
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Products</label>
                            <div id="calc_products">
                                <div class="calc-row d-flex gap-2 mb-2">
                                    <input type="text" class="form-control calc-product-name"
                                        placeholder="Product name" />
                                    <input type="number" step="1" min="0"
                                        class="form-control calc-qty" placeholder="Qty" />
                                    <input type="number" step="0.01" min="0"
                                        class="form-control calc-price" placeholder="Price" />
                                    <button type="button" class="btn btn-outline-danger btn-remove-row"
                                        title="Remove">&minus;</button>
                                </div>
                            </div>
                            <button type="button" id="addCalcRow" class="btn btn-sm btn-secondary mt-1">Add
                                product</button>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">TOP (days)</label>
                                <select id="calc_top" class="form-select">
                                    <option></option>
                                    @foreach ($top as $t)
                                        <option value="{{ $t->name_top }}">{{ $t->desc_top }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lead Time (LT)</label>
                                <input type="text" step="1" id="calc_lt" class="form-control"
                                    readonly />
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
