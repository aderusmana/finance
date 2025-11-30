<x-app-layout>
    @section('title')
        Complain List
    @endsection

    {{-- Include Complaint Table Styles Template --}}
    @include('components.complaint-table-styles')

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Complain List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Requisition Slip form
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Complain form</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Enhanced Table Section -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar with Filter (mirip FreeGoods) -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Grup Filter di Kiri (status filter) --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold">Filter by:</span>
                    <select id="statusFilter" class="form-select" style="width: 200px;">
                        <option value="all">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Recalled">Recalled</option>
                        <option value="Payment Proof">Payment Proof</option>
                    </select>
                    <button id="resetFilters" class="btn btn-secondary border" data-bs-toggle="tooltip"
                        title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                {{-- Tombol Create di Kanan --}}
                <div>
                    <button class="btn new-complain-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#complineModal" id="btn-create-compline">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Complain</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list-dashes"></i>
                        Complaints List
                    </h4>
                    <p class="table-subtitle">
                        View, manage and track all complaint submissions
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="complainTable">
                        <thead>
                            <tr>
                                <th><i class="ph-duotone ph-hash me-1"></i>No</th>
                                <th><i class="ph-duotone ph-user me-1"></i>Requester</th>
                                <th><i class="ph-duotone ph-buildings me-1"></i>Customer</th>
                                <th><i class="ph-duotone ph-calendar me-1"></i>Request Date</th>
                                <th><i class="ph-duotone ph-currency-dollar me-1"></i>Cost Center</th>
                                <th><i class="ph-duotone ph-map-pin me-1"></i>Route To</th>
                                <th><i class="ph-duotone ph-flag me-1"></i>Status</th>
                                <th><i class="ph-duotone ph-gear me-1"></i>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="complineModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="complineModalLabel">Create complain</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('complain-form.store') }}" method="POST" data-mode="create" data-id=""
                        id="complineForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden input for print_batch -->
                        <input type="hidden" id="print_batch" name="print_batch" value="0">

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary"><strong>Common Information</strong></h5>
                                <hr>
                            </div>
                        </div>

                        <!-- Customer Information Row -->
                        <div class="row mb-3 g-3">
                            <div class="col-6">
                                <label for="customer_id" class="form-label"><strong>Customer Name :</strong></label>
                                <select name="customer_id" id="customer_id" class="form-select">
                                </select>
                                <div data-error-for="customer_id" class="text-danger mt-1 error-message"></div>
                            </div>
                            <div class="col-6">
                                <label for="customer_address" class="form-label"><strong>Customer Address
                                        :</strong></label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="2" readonly></textarea>
                                <div data-error-for="customer_address" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <!-- Basic Information Row -->
                        <div class="row mb-3 g-3">
                            <div class="col-3">
                                <label for="rs_number" class="form-label"><strong>Nomor RS :</strong></label>
                                <h5 id="rs_number_display" class="form-control-plaintext"
                                    style="font-weight: bold; text-align: center; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background-color: #f8f9fa;">
                                </h5>
                                <input type="hidden" class="form-control" id="rs_number" name="rs_number">
                            </div>
                            <div class="col-3">
                                <label for="account" class="form-label"><strong>Account :</strong></label>
                                <h5 id="account_display" class="form-control-plaintext"
                                    style="font-weight: bold; text-align: center; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.375rem 0.75rem; background-color: #f8f9fa;">
                                </h5>
                                <input type="hidden" class="form-control" id="account" name="account">
                            </div>
                            <div class="col-3">
                                <label for="date" class="form-label"><strong>Tanggal :</strong></label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ date('Y-m-d') }}">
                                <div data-error-for="date" class="text-danger mt-1 error-message"></div>
                            </div>
                            <div class="col-3">
                                <label for="cost_center" class="form-label"><strong>Cost Center :</strong></label>
                                <input type="text" class="form-control" id="cost_center" name="cost_center">
                                <div data-error-for="cost_center" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <!-- Objectives Row -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="objectives" class="form-label"><strong>Reason for Replacement
                                        :</strong></label>
                                <textarea name="objectives" id="objectives" class="form-control" rows="3"></textarea>
                                <div data-error-for="objectives" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <!-- Product Details Section -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary"><strong>Product Details</strong></h5>
                                <hr>
                            </div>
                        </div>

                        <!-- Product Selection Row -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="requisition_items" class="form-label"><strong>Select Products
                                        :</strong></label>
                                <select name="requisition_items[]" id="requisition_items" multiple="multiple"
                                    class="form-control" style="display: none;">
                                </select>
                                <div data-error-for="requisition_items" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <!-- Material Type Filter Row -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label"><strong>Material Type Filter :</strong></label>
                                <div id="material_type_wrapper">
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox"
                                            name="material_type[]" value="Raw" id="mt-raw">
                                        <label class="form-check-label" for="mt-raw">Raw Material</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox"
                                            name="material_type[]" value="Semi-Finished" id="mt-semi">
                                        <label class="form-check-label" for="mt-semi">Semi Finished Material</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input material-type-filter" type="checkbox"
                                            name="material_type[]" value="Finished" id="mt-finished">
                                        <label class="form-check-label" for="mt-finished">Finished Material</label>
                                    </div>
                                </div>
                                <div data-error-for="material_type" class="text-danger mt-1 error-message"></div>
                            </div>
                        </div>

                        <!-- Product Table Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div id="productDetailsContainer">
                                    <!-- Default placeholder when no products selected -->
                                    <div id="productDetailsPlaceholder"
                                        class="text-center py-2 text-muted border rounded-md">
                                        <i class="ph-duotone ph-package fs-1 mb-3 d-block text-secondary"></i>
                                        <h5 class="text-muted mb-2">Product Details</h5>
                                        <p class="mb-0">Product details will appear here after selecting the products
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="complain_images"><strong>Upload Images (Optional)</strong></label>
                                <div class="mb-2">
                                    <input type="file" class="form-control" id="complain_images"
                                        name="complain_images[]" multiple accept="image/*">
                                    <div class="form-text">
                                        <small class="text-muted">
                                            <i class="ph-duotone ph-info me-1"></i>
                                            Supported formats: JPG, PNG, GIF. Maximum size per image: 1MB. You can
                                            select up to 10 images.
                                        </small>
                                    </div>
                                    <div data-error-for="complain_images" class="text-danger mt-1 error-message">
                                    </div>
                                </div>

                                <!-- Image Preview Container -->
                                <div id="imagePreviewContainer" class="d-none">
                                    <h6 class="mb-2"><strong>Selected Images:</strong></h6>
                                    <div class="row" id="imagePreviewList">
                                        <!-- Image previews will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- tabel produk -->
                        <!-- <div class="row">
                            <div class="col-12">
                                <table class="table table-bordered slip-table">
                                    <thead>
                                        <tr>
                                            <th>PRODUCT CODE</th>
                                            <th>PRODUCT NAME</th>
                                            <th>UNIT</th>
                                            <th>QTY REQUIRED</th>
                                            <th>OBJECTIVES</th>
                                            <th style="width: 5%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-list">
                                        <tr>
                                            <td><input type="text" name="codes[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="names[0]" class="form-control form-control-sm"></td>
                                            <td><input type="text" name="units[0]" class="form-control form-control-sm"></td>
                                            <td><input type="number" name="quantities[0]"
                                                    class="form-control form-control-sm" min="1"></td>
                                            <td><input type="text" name="objectives[0]" class="form-control form-control-sm">
                                            </td>
                                            <td class="d-flex gap-1 align-items-center" style="white-space:nowrap;">
                                                <button type="button" class="btn btn-sm btn-danger remove-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning clear-row-btn" style="display:inline-flex; align-items:center;">
                                                    <i class="fa fa-eraser"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="add-row-btn" class="btn btn-sm btn-success mt-2">
                                    <i class="fa fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div> -->

                </div>

                <!-- footer modal -->
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="submit" id="saveUserBtn">
                        Save changes
                    </button>
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal detail -->
    <div class="modal fade" id="detailModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="detailModalLabel">
                        <i class="ph-duotone ph-file-text"></i>
                        Detail Requisition Complain
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced">
                    <!-- Header Section -->
                    <div class="slip-header-enhanced">
                        <div class="row align-items-center">
                            <div class="col-10">
                                <img src="{{ asset('storage/logo.png') }}" alt="Sinar Meadow Logo" class="logo"
                                    style="max-height: 60px; width: auto;">
                            </div>
                            <div class="col-2">
                                <div class="text-muted small">
                                    <strong>FORM NO:</strong> FA-INV-05<br>
                                    <strong>REVISION:</strong> 3<br>
                                    <strong>DATE:</strong> 18 FEBRUARY 2021
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Title Section -->
                    <div class="slip-title-enhanced">
                        <h4><strong>REQUISITION SLIP</strong></h4>
                        <p class="">SALES & MARKETING<br>SAMPLE PRODUCT</p>
                    </div>

                    <!-- Customer & Basic Info Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-user-circle"></i>
                            Customer & Basic Information
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-user text-primary"></i>
                                            Customer Name:
                                        </div>
                                        <div class="info-value readonly" id="detail_customer_name"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-map-pin text-info"></i>
                                            Address:
                                        </div>
                                        <div class="info-value readonly" id="detail_customer_address"
                                            style="min-height: 60px; white-space: pre-wrap;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-bank text-success"></i>
                                            Account:
                                        </div>
                                        <div class="info-value readonly" id="detail_account"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-currency-dollar text-warning"></i>
                                            Cost Center:
                                        </div>
                                        <div class="info-value readonly" id="detail_cost_center"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-hash text-secondary"></i>
                                            RS Number:
                                        </div>
                                        <div class="info-value readonly" id="detail_rs_number"></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-calendar text-danger"></i>
                                            Date:
                                        </div>
                                        <div class="info-value readonly" id="detail_date"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products & Objectives Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-package"></i>
                            Products & Objectives
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <h6 class="mb-3 fw-bold text-muted">
                                    <i class="ph-duotone ph-list-bullets me-2"></i>
                                    Selected Products
                                </h6>
                                <div class="product-list-container" id="requisition_product_list">
                                    <!-- Products will be populated here -->
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h6 class="mb-3 fw-bold text-muted">
                                    <i class="ph-duotone ph-target me-2"></i>
                                    Reason for Replacement :
                                </h6>
                                <div class="objectives-container">
                                    <div class="objectives-text" id="detail_objectives">
                                        <!-- Objectives will be populated here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-table"></i>
                            Detailed Product Information
                        </div>
                        <div class="detail-table-container">
                            <div id="detail_productDetailsContainer">
                                <!-- Product details table will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- Complain Images Section -->
                    <div class="detail-section" id="complainImagesSection" style="display: none;">
                        <div class="section-header">
                            <i class="ph-duotone ph-images"></i>
                            Complain Images
                        </div>
                        <div class="row" id="detail_complain_images">
                            <!-- Images will be populated here -->
                        </div>
                    </div>

                    <!-- Status & Approval History Section -->
                    <!-- <div class="detail-section"> -->
                    <!-- <div class="section-header"> -->
                    <!-- <i class="ph-duotone ph-clock-clockwise"></i> -->
                    <!-- Status & Approval History -->
                    <!-- </div> -->
                    <!-- <div class="row"> -->
                    <!-- <div class="col-md-4"> -->
                    <!-- <div class="status-display-container"> -->
                    <!-- <h6 class="mb-3 fw-bold text-muted"> -->
                    <!-- <i class="ph-duotone ph-flag me-2"></i> -->
                    <!-- Current Status -->
                    <!-- </h6> -->
                    <!-- <div class="current-status-badge" id="current_status_display"> -->
                    <!-- Current status will be populated here -->
                    <!-- </div> -->
                    <!-- </div> -->
                    <!-- </div> -->
                    <!-- <div class="col-md-8"> -->
                    <!-- <div class="approval-history-container"> -->
                    <!-- <h6 class="mb-3 fw-bold text-muted"> -->
                    <!-- <i class="ph-duotone ph-chat-teardrop-text me-2"></i> -->
                    <!-- Approval History & Notes -->
                    <!-- </h6> -->
                    <!-- <div class="approval-timeline" id="approval_history_list"> -->
                    <!-- Approval history will be populated here -->
                    <!-- </div> -->
                    <!-- </div> -->
                    <!-- </div> -->
                    <!-- </div> -->
                    <!-- </div> -->

                    <!-- Approval & Process Tracking Card -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-path"></i>
                            Approval & Process Tracking
                        </div>
                        <div class="card view-modal-card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="fw-bold me-3">Current Status:</span>
                                    <div id="detail_status_badge"></div>
                                </div>
                                <div class="tracker-container" id="complain-approval-tracker-container">
                                    <div class="tracker-line">
                                        <div class="tracker-line-progress" id="complain-tracker-progress"></div>
                                    </div>
                                    <div class="tracker-step" data-step-name="Submitted">
                                        <div class="tracker-icon"><i class="ph-bold ph-file-arrow-up fs-6"></i></div>
                                        <div class="tracker-label">Submitted</div>
                                        <div class="tracker-details"></div>
                                    </div>
                                    <div class="tracker-step" data-step-name="Manager Approval">
                                        <div class="tracker-icon"><i class="ph-bold ph-user-plus fs-6"></i></div>
                                        <div class="tracker-label">Manager</div>
                                        <div class="tracker-details"></div>
                                    </div>
                                    <div class="tracker-step" data-step-name="Business Controller Approval">
                                        <div class="tracker-icon"><i class="ph-bold ph-briefcase fs-6"></i></div>
                                        <div class="tracker-label">Business Controller</div>
                                        <div class="tracker-details"></div>
                                    </div>
                                    <div class="tracker-step" data-step-name="Warehouse Processing">
                                        <div class="tracker-icon"><i class="ph-bold ph-package fs-6"></i></div>
                                        <div class="tracker-label">Warehouse</div>
                                        <div class="tracker-details"></div>
                                    </div>
                                    <div class="tracker-step" data-step-name="Completed">
                                        <div class="tracker-icon"><i class="ph-bold ph-check-circle fs-6"></i></div>
                                        <div class="tracker-label">Completed</div>
                                        <div class="tracker-details"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Requisition History Card -->
                    <div class="detail-section">
                        <div class="section-header">
                            <i class="ph-duotone ph-clock-counter-clockwise"></i>
                            Requisition History
                        </div>
                        <div class="card view-modal-card">
                            <div class="card-body p-4">
                                <ul class="list-group list-group-flush" id="complain-history-log-container">
                                    <!-- History akan diisi oleh JavaScript di sini -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" data-bs-dismiss="modal" type="button">
                        <i class="ph-duotone ph-x me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Proof Upload Modal -->
    <div class="modal fade" id="paymentProofModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">
                        <i class="ph-duotone ph-file-upload me-2"></i>
                        Upload Payment Proof
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="paymentProofForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="payment_complain_id" name="complain_id">

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">
                                <i class="ph-duotone ph-calendar me-1"></i>
                                Payment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date"
                                required>
                            <div class="invalid-feedback" id="payment_date_error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="payment_document" class="form-label">
                                <i class="ph-duotone ph-file-image me-1"></i>
                                Payment Document <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="payment_document" name="payment_document"
                                accept="image/*,.pdf" required>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="ph-duotone ph-info me-1"></i>
                                    Supported formats: JPG, PNG, PDF. Maximum size: 1MB
                                </small>
                            </div>
                            <div class="invalid-feedback" id="payment_document_error"></div>
                        </div>

                        <!-- File preview -->
                        <div id="filePreview" class="d-none">
                            <div class="alert alert-info">
                                <i class="ph-duotone ph-file-check me-2"></i>
                                <span id="fileName"></span>
                                <br>
                                <small id="fileSize" class="text-muted"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="uploadPaymentBtn">
                            <i class="ph-duotone ph-upload me-2"></i>
                            Upload Payment Proof
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ph-duotone ph-x me-2"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image View Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-enhanced">
                    <h5 class="modal-title modal-title-enhanced" id="imageModalLabel">
                        <i class="ph-duotone ph-image"></i>
                        Image View
                    </h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" onclick="closeImageModal()"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-enhanced text-center">
                    <div class="image-container d-flex justify-content-center align-items-center"
                        style="min-height: 500px;">
                        <img id="modalImage" src="" class="img-fluid shadow-lg rounded"
                            alt="Full size image" style="max-width: 100%; height: auto; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeImageModal()">
                        <i class="ph-duotone ph-x me-2"></i>Close
                    </button>
                    <a id="downloadImage" href="" download class="btn btn-primary">
                        <i class="ph-duotone ph-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; backdrop-filter: blur(8px);">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; background: rgba(255, 255, 255, 0.1); padding: 40px 60px; border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
            <div class="spinner-border text-warning" role="status"
                style="width: 4rem; height: 4rem; border-width: 0.4rem; animation: spin 1s linear infinite;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div style="margin-top: 20px; color: white; font-size: 18px; font-weight: 600;">
                <i class="ph-duotone ph-spinner-gap ph-spin" style="font-size: 24px;"></i>
                <p class="mt-3 mb-1" style="letter-spacing: 0.5px;">Processing your request...</p>
                <p style="font-size: 14px; opacity: 0.8; margin-bottom: 0;">Please wait, do not close this page</p>
            </div>
            <div class="mt-3" style="font-size: 12px; color: rgba(255, 255, 255, 0.6);">
                <i class="ph-duotone ph-clock-clockwise"></i> This may take a few moments
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        #loadingOverlay {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    @push('scripts')
        <script>
            // === SweetAlert2 Reusable Functions ===
            function successMessage(message, title = 'Success', timer = 1500) {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    timer: timer,
                    showConfirmButton: false
                });
            }

            function errorMessage(message, title = 'Error') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message
                });
            }

            function warningMessage(message, title = 'Warning') {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message
                });
            }

            // confirmDialog: returns a Promise, so you can use .then()
            function confirmDialog({
                title = 'Are you sure?',
                text = 'This action cannot be undone!',
                confirmButtonText = 'Yes',
                cancelButtonText = 'Cancel',
                confirmButtonColor = '#3085d6',
                cancelButtonColor = '#d33',
                icon = 'warning',
                reverseButtons = true
            } = {}) {
                return Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor,
                    cancelButtonColor,
                    confirmButtonText,
                    cancelButtonText,
                    reverseButtons
                });
            }

            $(document).ready(function() {
                let customerSelect = $('#customer_id');
                let addressField = $('#customer_address');
                let productselect = $('#requisition_items');
                let materialtype = $('#material_type_wrapper');

                // define url detail
                let detailUrlTemplate = "{{ route('get.form.detail', ['id' => ':id']) }}";

                // Cache untuk menyimpan inputan jika ddilakukan render
                let qtyCache = {};

                // === DataTable ===
                // Inisialisasi Select2 untuk status filter (jika tersedia)
                if ($.fn.select2) {
                    $('#statusFilter').select2({
                        theme: 'bootstrap-5',
                        minimumResultsForSearch: Infinity
                    });
                }

                let table = $('#complainTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('get.complain.data') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'requester.name',
                            name: 'requester.name',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                return (row.requester && row.requester.name) ?
                                    `<span class="fw-bold text-primary">${data}</span>` : '-';
                            }
                        }, {
                            data: 'customer_id',
                            name: 'customer_id',
                            render: function(data, type, row) {
                                return (row.customer && row.customer.name) ? row.customer.name : '-';
                            }
                        }, {
                            data: 'request_date',
                            name: 'request_date',
                            render: function(data, type, row) {
                                if (!data) return '-';
                                const d = new Date(data);
                                if (isNaN(d.getTime())) return '-';
                                // Format DD/MM/YYYY
                                return String(d.getDate()).padStart(2, '0') + '/' +
                                    String(d.getMonth() + 1).padStart(2, '0') + '/' +
                                    d.getFullYear();
                            }
                        }, {
                            data: 'cost_center',
                            name: 'cost_center'
                        }, {
                            data: 'route_to',
                            name: 'route_to',
                            render: function(data, type, row) {
                                return `<span class="badge bg-info">${data}</span>` || 'N/A';
                            }
                        }, {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                // Normalize status untuk comparison
                                const status = (data || '').toLowerCase().trim();

                                switch (status) {
                                    case 'pending':
                                        return '<span class="badge status-badge-lg status-pending">Pending</span>';

                                    case 'approved':
                                        return '<span class="badge status-badge-lg status-approved">Approved</span>';

                                    case 'rejected':
                                    case 'failed':
                                        return '<span class="badge status-badge-lg status-rejected">Rejected</span>';

                                    case 'in progress':
                                        return '<span class="badge status-badge-lg status-in-progress">In Progress</span>';

                                    case 'completed':
                                    case 'success':
                                        return '<span class="badge status-badge-lg status-completed">Completed</span>';

                                    case 'cancelled':
                                        return '<span class="badge status-badge-lg status-cancelled">Cancelled</span>';

                                    case 'payment proof':
                                        return '<span class="badge status-badge-lg status-payment-proof">Payment Proof</span>';

                                    default:
                                        return '<span class="badge status-badge-lg bg-secondary">' +
                                            data + '</span>';
                                }
                            }
                        },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                let status = (row.status || '').toLowerCase();

                                let deleteButton = (status === 'pending') ?
                                    `<button type="button" class="btn btn-danger btn-sm delete-button action-btn-hover" data-id="${data}"
                                    data-tooltip="Delete Complaint">
                                    <i class="ph-duotone ph-trash"></i>
                                   </button>` :
                                    '';

                                let paymentProofButton = (status === 'payment proof') ?
                                    `<button type="button" class="btn btn-danger payment-button btn-sm action-btn-hover" data-id="${data}"
                                data-tooltip="Upload Payment Proof">
                                    <i class="ph-duotone ph-file"></i>
                                   </button>` :
                                    '';
                                return `
                                <div class="action-btn-group">
                                    <button type="button" class="btn btn-info btn-sm detail-button action-btn-hover" data-id="${data}"
                                        data-tooltip="View Details">
                                        <i class="ph-duotone ph-eye"></i>
                                    </button>
                                    ${deleteButton}
                                    ${paymentProofButton}
                                </div>
                            `;
                            }
                        }
                    ]
                });
                // Event listener untuk status filter
                $('#statusFilter').on('change', function() {
                    $('#complainTable').DataTable().ajax.reload();
                });

                // Reset tombol
                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                });

                let searchInput = $('#complainTable_filter input');
                searchInput.unbind();
                let debounceTimer;
                searchInput.bind('keyup', function(e) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        let searchTerm = searchInput.val();
                        table.search(searchTerm).draw();
                    }, 500);
                });

                // Custom Tooltip Handler for Action Buttons
                function initActionTooltips() {
                    // Remove any existing event handlers to prevent duplicates
                    $(document).off('mouseenter.customTooltip mouseleave.customTooltip', '.action-btn-hover');

                    $(document).on('mouseenter.customTooltip', '.action-btn-hover', function(e) {
                        const tooltipText = $(this).attr('data-tooltip');
                        if (tooltipText && !$(this).data('tooltip-element')) {
                            const tooltip = $('<div class="action-tooltip">' + tooltipText + '</div>');
                            $('body').append(tooltip);

                            const button = $(this);
                            let isDestroyed = false;

                            // Function to update tooltip position
                            function updateTooltipPosition() {
                                if (isDestroyed || !button.is(':visible') || !tooltip.parent().length) {
                                    return;
                                }

                                // Get button position
                                const buttonOffset = button.offset();
                                if (!buttonOffset) return;

                                const buttonWidth = button.outerWidth();
                                const buttonHeight = button.outerHeight();
                                const tooltipWidth = tooltip.outerWidth();
                                const tooltipHeight = tooltip.outerHeight();
                                const windowWidth = $(window).width();
                                const windowHeight = $(window).height();
                                const scrollTop = $(window).scrollTop();

                                // Calculate position
                                let left = buttonOffset.left + (buttonWidth / 2) - (tooltipWidth / 2);
                                let top = buttonOffset.top - tooltipHeight - 12;

                                // Horizontal bounds checking
                                if (left < 10) {
                                    left = 10;
                                } else if (left + tooltipWidth > windowWidth - 10) {
                                    left = windowWidth - tooltipWidth - 10;
                                }

                                // Vertical bounds checking
                                if (top < scrollTop + 10) {
                                    top = buttonOffset.top + buttonHeight + 12;
                                    tooltip.addClass('below');
                                } else {
                                    tooltip.removeClass('below');
                                }

                                tooltip.css({
                                    position: 'absolute',
                                    left: left + 'px',
                                    top: top + 'px',
                                    zIndex: 9999
                                });
                            }

                            // Initial positioning
                            setTimeout(() => {
                                updateTooltipPosition();
                            }, 10);

                            // Show tooltip with delay
                            setTimeout(() => {
                                if (!isDestroyed) {
                                    tooltip.addClass('show');
                                }
                            }, 100);

                            // Store tooltip element and update function
                            button.data('tooltip-element', tooltip);
                            button.data('update-tooltip-position', updateTooltipPosition);
                            button.data('tooltip-destroyed', false);

                            // Create unique namespace for this tooltip
                            const tooltipId = 'tooltip_' + Date.now() + '_' + Math.random().toString(36).substr(
                                2, 9);
                            button.data('tooltip-id', tooltipId);

                            // Listen for scroll events with throttling
                            let scrollTimeout;

                            function throttledUpdate() {
                                if (scrollTimeout) {
                                    clearTimeout(scrollTimeout);
                                }
                                scrollTimeout = setTimeout(() => {
                                    if (!isDestroyed) {
                                        updateTooltipPosition();
                                    }
                                }, 10);
                            }

                            $(window).on('scroll.' + tooltipId + ' resize.' + tooltipId, throttledUpdate);
                            $('.dataTables_scrollBody').on('scroll.' + tooltipId, throttledUpdate);
                            $('.table-responsive').on('scroll.' + tooltipId, throttledUpdate);
                            $('#complainTable_wrapper').on('scroll.' + tooltipId, throttledUpdate);

                            // Store cleanup function
                            button.data('tooltip-cleanup', function() {
                                isDestroyed = true;
                                $(window).off('.' + tooltipId);
                                $('.dataTables_scrollBody').off('.' + tooltipId);
                                $('.table-responsive').off('.' + tooltipId);
                                $('#complainTable_wrapper').off('.' + tooltipId);
                                if (scrollTimeout) {
                                    clearTimeout(scrollTimeout);
                                }
                            });
                        }
                    });

                    $(document).on('mouseleave.customTooltip', '.action-btn-hover', function(e) {
                        const button = $(this);
                        const tooltip = button.data('tooltip-element');
                        const cleanup = button.data('tooltip-cleanup');

                        if (tooltip) {
                            button.data('tooltip-destroyed', true);

                            tooltip.removeClass('show');
                            setTimeout(() => {
                                tooltip.remove();
                            }, 200);

                            // Execute cleanup
                            if (cleanup) {
                                cleanup();
                            }

                            // Clear all data
                            button.removeData('tooltip-element');
                            button.removeData('update-tooltip-position');
                            button.removeData('tooltip-id');
                            button.removeData('tooltip-cleanup');
                            button.removeData('tooltip-destroyed');
                        }
                    });
                }

                // Initialize tooltips after DataTable is ready
                table.on('draw', function() {
                    initActionTooltips();
                });

                // Initialize tooltips for the first load
                initActionTooltips();

                // Disable Bootstrap tooltips on action buttons to prevent conflicts
                $(document).ready(function() {
                    // Remove any existing Bootstrap tooltip instances
                    $('.action-btn-hover').tooltip('dispose');

                    // Prevent Bootstrap tooltip initialization
                    $(document).off('mouseenter.bs.tooltip', '.action-btn-hover');
                });


                // detail trigger
                $('#complainTable tbody').on('click', '.detail-button', function() {
                    let complainId = $(this).data('id');
                    let finalUrl = detailUrlTemplate.replace(':id', complainId);

                    $('#detailModal').modal('show');

                    // Clear previous payment proof section before loading new data
                    $('.payment-proof-section').remove();

                    $.ajax({
                        url: finalUrl,
                        method: 'GET',
                        success: function(data) {
                            // Populate basic information with new structure
                            $('#detail_customer_name').text(data.customer ? data.customer.name :
                                '-');
                            $('#detail_customer_address').text(data.customer ? data.customer
                                .address : '-');
                            $('#detail_account').text(data.account || '-');
                            $('#detail_cost_center').text(data.cost_center || '-');
                            $('#detail_rs_number').text(data.no_srs || '-');

                            // Format date nicely
                            if (data.request_date) {
                                const date = new Date(data.request_date);
                                const formattedDate = date.toLocaleDateString('en-GB', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                                $('#detail_date').text(formattedDate);
                            } else {
                                $('#detail_date').text('-');
                            }

                            $('#detail_objectives').text(data.reason_for_replacement ||
                                'No reason specified');

                            // Enhanced product list with better styling
                            const selectedProductsDiv = $('#requisition_product_list');
                            const items = data.requisition_items;

                            selectedProductsDiv.empty();

                            if (items && items.length > 0) {
                                const uniqueMastersMap = new Map();

                                items.forEach(item => {
                                    if (item.item_master) {
                                        uniqueMastersMap.set(item.item_master.id, item
                                            .item_master);
                                    }
                                });

                                const uniqueMasters = Array.from(uniqueMastersMap.values());
                                let productListHtml = '';

                                // Create enhanced product items
                                uniqueMasters.forEach(function(master) {
                                    const masterName =
                                        `${master.item_master_code} - ${master.item_master_name}`;
                                    productListHtml += `
                                    <div class="product-item">
                                        <i class="ph-duotone ph-package"></i>
                                        <span class="fw-medium">${masterName}</span>
                                    </div>
                                `;
                                });

                                selectedProductsDiv.html(productListHtml);

                            } else {
                                selectedProductsDiv.html(`
                                <div class="text-center py-4 text-muted">
                                    <i class="ph-duotone ph-package fs-2 mb-2 d-block"></i>
                                    <span>No products selected</span>
                                </div>
                            `);
                            }

                            // Populate Status & Approval History
                            populateStatusAndHistory(data);

                            // Populate Approval Tracker
                            populateApprovalTracker(data);

                            // Populate History Log
                            populateHistoryLog(data);

                            renderDetailProductTable(data.requisition_items);
                            renderComplainImages(data.complain_images);

                            // Check if payment proof exists and add to detail - filter by complain ID
                            if (data.payments && data.payments.length > 0) {
                                // Find payment proof that matches the current complain ID
                                const relevantPayment = data.payments.find(payment =>
                                    payment.requisition_id == complainId
                                );

                                if (relevantPayment) {
                                    addPaymentProofSection(relevantPayment, complainId);
                                }
                            }
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message ||
                                'Failed to load complain details.');
                            $('#detailModal').modal('hide');
                        }
                    });
                });

                // Handle payment proof upload button click
                $('#complainTable tbody').on('click', '.payment-button', function() {
                    let complainId = $(this).data('id');
                    $('#payment_complain_id').val(complainId);
                    $('#paymentProofModal').modal('show');

                    // Reset form
                    $('#paymentProofForm')[0].reset();
                    $('#filePreview').addClass('d-none');
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');

                    // Set today as default payment date
                    $('#payment_date').val(new Date().toISOString().split('T')[0]);
                });

                // File validation and preview
                $('#payment_document').on('change', function() {
                    const file = this.files[0];
                    const maxSize = 1 * 1024 * 1024; // 1MB in bytes
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];

                    // Clear previous errors
                    $('#payment_document_error').text('');
                    $(this).removeClass('is-invalid');
                    $('#filePreview').addClass('d-none');

                    if (file) {
                        // Validate file size
                        if (file.size > maxSize) {
                            $('#payment_document_error').text('File size must not exceed 1MB');
                            $(this).addClass('is-invalid');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        if (!allowedTypes.includes(file.type)) {
                            $('#payment_document_error').text('Only JPG, PNG, and PDF files are allowed');
                            $(this).addClass('is-invalid');
                            this.value = '';
                            return;
                        }

                        // Show file preview
                        $('#fileName').text(file.name);
                        $('#fileSize').text(`Size: ${(file.size / 1024 / 1024).toFixed(2)} MB`);
                        $('#filePreview').removeClass('d-none');
                    }
                });

                // Handle payment proof form submission
                $('#paymentProofForm').on('submit', function(e) {
                    e.preventDefault();

                    // Clear previous errors
                    $('.invalid-feedback').text('');
                    $('.form-control').removeClass('is-invalid');

                    let formData = new FormData(this);

                    // Show loading state
                    $('#uploadPaymentBtn').prop('disabled', true);
                    $('#uploadPaymentBtn').html(
                        '<i class="ph-duotone ph-spinner ph-spin me-2"></i>Uploading...');

                    $.ajax({
                        url: "{{ route('upload.payment.proof') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#paymentProofModal').modal('hide');
                            $('#complainTable').DataTable().ajax.reload(null, false);
                            successMessage(response.message ||
                                'Payment proof uploaded successfully!');
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                // Validation errors
                                let errors = xhr.responseJSON.errors;
                                for (let field in errors) {
                                    $(`#${field}_error`).text(errors[field][0]);
                                    $(`#${field}`).addClass('is-invalid');
                                }
                                errorMessage('Please check the form for errors');
                            } else {
                                errorMessage(xhr.responseJSON?.message ||
                                    'Failed to upload payment proof');
                            }
                        },
                        complete: function() {
                            // Reset button state
                            $('#uploadPaymentBtn').prop('disabled', false);
                            $('#uploadPaymentBtn').html(
                                '<i class="ph-duotone ph-upload me-2"></i>Upload Payment Proof');
                        }
                    });
                });

                // Function to add payment proof section to detail modal
                function addPaymentProofSection(payment, complainId) {
                    const paymentDate = new Date(payment.payment_date).toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const paymentSection = `
                    <div class="detail-section payment-proof-section" data-complain-id="${complainId}">
                        <div class="section-header">
                            <i class="ph-duotone ph-file-check"></i>
                            Payment Proof Information
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-calendar text-primary"></i>
                                            Payment Date:
                                        </div>
                                        <div class="info-value readonly">${paymentDate}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-row">
                                        <div class="info-label">
                                            <i class="ph-duotone ph-file-pdf text-danger"></i>
                                            Payment Document:
                                        </div>
                                        <div class="info-value">
                                            <a href="${window.location.origin}/storage/${payment.document_url}"
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ph-duotone ph-download me-1"></i>
                                                View Document
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    // Insert payment section after complain images section
                    $('#complainImagesSection').after(paymentSection);
                }

                // Enhanced modal detail table
                function renderDetailProductTable(items) {
                    const container = $('#detail_productDetailsContainer');
                    container.empty();

                    if (!items || items.length === 0) {
                        container.html(`
                        <div class="text-center py-5">
                            <i class="ph-duotone ph-table fs-1 text-muted mb-3 d-block"></i>
                            <h6 class="text-muted mb-2">No Product Details</h6>
                            <p class="text-muted small mb-0">No product details found for this requisition.</p>
                        </div>
                    `);
                        return;
                    }

                    let tableRowsHTML = items.map((item, index) => {
                        const allDetails = item.item_master ? item.item_master.item_details : [];
                        const specificDetail = allDetails.find(detail => detail.id === item.item_detail_id);

                        if (!specificDetail) {
                            return `
                            <tr class="table-danger">
                                <td colspan="8" class="text-center py-3">
                                    <i class="ph-duotone ph-warning-circle text-danger me-2"></i>
                                    Product Detail with ID ${item.item_detail_id} not found.
                                </td>
                            </tr>
                        `;
                        }

                        // Add material type badge styling
                        let materialTypeBadge = '';
                        const materialType = specificDetail.material_type || '-';
                        if (materialType === 'Raw') {
                            materialTypeBadge = `<span class="badge bg-primary">${materialType}</span>`;
                        } else if (materialType === 'Semi-Finished') {
                            materialTypeBadge =
                                `<span class="badge bg-warning text-dark">${materialType}</span>`;
                        } else if (materialType === 'Finished') {
                            materialTypeBadge = `<span class="badge bg-success">${materialType}</span>`;
                        } else {
                            materialTypeBadge = `<span class="badge bg-secondary">${materialType}</span>`;
                        }

                        return `
                        <tr class="animate-row" style="animation-delay: ${index * 0.1}s">
                            <td class="text-center">${materialTypeBadge}</td>
                            <td class="fw-medium">${specificDetail.item_detail_code || '-'}</td>
                            <td>${specificDetail.item_detail_name || '-'}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">${specificDetail.unit || '-'}</span>
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control text-center fw-bold"
                                       value="${item.quantity_required}" readonly
                                       style="background: rgba(192, 127, 0, 0.1); border-color: rgba(192, 127, 0, 0.3);">
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control text-center fw-bold"
                                       value="${item.quantity_issued}" readonly
                                       style="background: rgba(25, 135, 84, 0.1); border-color: rgba(25, 135, 84, 0.3);">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control text-center"
                                    value="${item.batch_number ?? ''}" readonly
                                    style="background: rgba(13, 110, 253, 0.1); border-color: rgba(13, 110, 253, 0.3);">
                            </td>
                            <td class="text-center">
                                <input type="text" class="form-control text-center"
                                       value="${item.remarks || ''}" readonly
                                       style="background: rgba(108, 117, 125, 0.1); border-color: rgba(108, 117, 125, 0.3);">
                            </td>
                        </tr>
                    `;
                    }).join('');

                    const tableHTML = `
                    <table class="table detail-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 15%;">
                                    <i class="ph-duotone ph-tag me-2"></i>Material Type
                                </th>
                                <th style="width: 15%;">
                                    <i class="ph-duotone ph-barcode me-2"></i>Detail Code
                                </th>
                                <th style="width: 20%;">
                                    <i class="ph-duotone ph-package me-2"></i>Detail Name
                                </th>
                                <th style="width: 8%;">
                                    <i class="ph-duotone ph-ruler me-2"></i>Unit
                                </th>
                                <th style="width: 12%;">
                                    <i class="ph-duotone ph-shopping-cart me-2"></i>QTY Required
                                </th>
                                <th style="width: 12%;">
                                    <i class="ph-duotone ph-check-circle me-2"></i>QTY Issued
                                </th>
                                <th style="width: 10%;">
                                    <i class="ph-duotone ph-calendar me-2"></i>Batch Number
                                </th>
                                <th style="width: 18%;">
                                    <i class="ph-duotone ph-note me-2"></i>Remarks
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRowsHTML}
                        </tbody>
                    </table>
                `;
                    container.html(tableHTML);

                    // Add animation styles to head if not already present
                    if (!$('head').find('#animate-styles').length) {
                        $('head').append(`
                        <style id="animate-styles">
                            @keyframes slideInUp {
                                from {
                                    opacity: 0;
                                    transform: translateY(20px);
                                }
                                to {
                                    opacity: 1;
                                    transform: translateY(0);
                                }
                            }
                            .animate-row {
                                animation: slideInUp 0.6s ease-out forwards;
                                opacity: 0;
                            }
                        </style>
                    `);
                    }
                }

                // Populate Status & Approval History Section
                function populateStatusAndHistory(data) {
                    // Populate current status
                    const statusContainer = $('#current_status_display');
                    const status = data.status || 'Unknown';

                    // Map status to CSS classes and display text dengan teks bilingual
                    let statusClass = 'status-badge-progress';
                    let statusText = status;

                    switch (status.toLowerCase().trim()) {
                        case 'pending':
                            statusClass = 'status-pending';
                            statusText = 'Pending';
                            break;
                        case 'approved':
                            statusClass = 'status-approved';
                            statusText = 'Approved';
                            break;
                        case 'rejected':
                        case 'failed':
                            statusClass = 'status-rejected';
                            statusText = 'Rejected';
                            break;
                        case 'in progress':
                            statusClass = 'status-in-progress';
                            statusText = 'In Progress';
                            break;
                        case 'completed':
                        case 'success':
                            statusClass = 'status-completed';
                            statusText = 'Completed - Selesai';
                            break;
                        case 'cancelled':
                            statusClass = 'status-cancelled';
                            statusText = 'Cancelled';
                            break;
                        case 'payment proof':
                            statusClass = 'status-payment-proof';
                            statusText = 'Payment Proof';
                            break;
                        default:
                            statusClass = 'bg-secondary';
                            statusText = status;
                    }

                    statusContainer.html(
                        `<div class="current-status-badge status-badge-lg ${statusClass}">${statusText}</div>`);

                    // Populate approval history
                    const historyContainer = $('#approval_history_list');
                    const approvalLogs = data.approval_logs || [];

                    // Filter out pending status entries
                    const filteredLogs = approvalLogs.filter(log =>
                        log.status && log.status.toLowerCase() !== 'pending'
                    );

                    if (filteredLogs.length === 0) {
                        historyContainer.html(`
                        <div class="empty-history">
                            <i class="ph-duotone ph-clock-clockwise"></i>
                            <div class="fw-medium">No approval history yet</div>
                            <small class="text-muted">Approval history will appear here when available</small>
                        </div>
                    `);
                    } else {
                        let historyHtml = '';

                        filteredLogs.forEach((log, index) => {
                            const approverName = log.approver.name || log.approver_nik || 'Unknown Approver';
                            const notes = log.notes || 'No notes provided';
                            const logStatus = log.status || 'N/A';
                            const createdDate = log.updated_at ? new Date(log.updated_at).toLocaleDateString(
                                'en-GB', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) : 'Unknown date';

                            // Determine status class and accent color for ::before
                            let statusBadgeClass = 'approval-level-default';
                            let approvalItemClass = 'approval-item';

                            switch (logStatus.toLowerCase()) {
                                case 'approved':
                                    statusBadgeClass = 'approval-level-approved';
                                    approvalItemClass = 'approval-item approval-item-approved';
                                    break;
                                case 'rejected':
                                    statusBadgeClass = 'approval-level-rejected';
                                    approvalItemClass = 'approval-item approval-item-rejected';
                                    break;
                                default:
                                    statusBadgeClass = 'approval-level-default';
                                    approvalItemClass = 'approval-item';
                            }

                            historyHtml += `
                            <div class="${approvalItemClass}">
                                <div class="approval-meta">
                                    <span class="approver-name">${approverName}</span>
                                    <span class="approval-level ${statusBadgeClass}">${logStatus}</span>
                                    <span class="text-muted ms-2">${createdDate}</span>
                                </div>
                                <div class="approval-notes ${notes === 'No notes provided' ? 'no-notes' : ''}">
                                    ${notes}
                                </div>
                            </div>
                        `;
                        });

                        historyContainer.html(historyHtml);
                    }
                }

                // Render Complain Images Function
                function renderComplainImages(images) {
                    const section = $('#complainImagesSection');
                    const container = $('#detail_complain_images');

                    container.empty();

                    if (!images || images.length === 0) {
                        section.hide();
                        return;
                    }

                    section.show();

                    images.forEach((image, index) => {
                        const imageUrl = `{{ asset('storage/') }}/${image.image_path}`;
                        const imageHtml = `
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="${imageUrl}"
                                    class="card-img-top image-clickable"
                                    style="height: 200px; object-fit: cover; cursor: pointer;"
                                    alt="Complain Image ${index + 1}"
                                    data-image-src="${imageUrl}"
                                    data-image-title="Complain Image ${index + 1}">
                                <div class="card-body p-2 text-center">
                                    <small class="text-muted">Image ${index + 1}</small>
                                </div>
                            </div>
                        </div>
                    `;
                        container.append(imageHtml);
                    });
                }

                // Populate Approval Tracker Function
                function populateApprovalTracker(data) {
                    const trackerContainer = $('#complain-approval-tracker-container');
                    const progressBar = $('#complain-tracker-progress');
                    const steps = trackerContainer.find('.tracker-step');

                    // Reset all steps
                    steps.removeClass('active completed rejected');

                    // Set status badge using the same logic as current status display
                    const status = data.status || 'Unknown';
                    let statusClass = 'status-badge-progress';
                    let statusText = status;

                    switch (status.toLowerCase().trim()) {
                        case 'pending':
                            statusClass = 'status-pending';
                            statusText = 'Pending';
                            break;
                        case 'approved':
                            statusClass = 'status-approved';
                            statusText = 'Approved';
                            break;
                        case 'rejected':
                        case 'failed':
                            statusClass = 'status-rejected';
                            statusText = 'Rejected';
                            break;
                        case 'payment proof':
                            statusClass = 'status-payment-proof';
                            statusText = 'Payment Proof';
                            break;
                        case 'in progress':
                            statusClass = 'status-in-progress';
                            statusText = 'In Progress';
                            break;
                        case 'completed':
                        case 'success':
                            statusClass = 'status-completed';
                            statusText = 'Completed - Selesai';
                            break;
                        case 'cancelled':
                            statusClass = 'status-cancelled';
                            statusText = 'Cancelled';
                            break;
                        default:
                            statusClass = 'bg-secondary';
                            statusText = status;
                    }

                    $('#detail_status_badge').html(
                        `<div class="current-status-badge status-badge-lg ${statusClass}">${statusText}</div>`);

                    let progressPercent = 0;

                    // Always mark submitted as completed
                    steps.filter('[data-step-name="Submitted"]').addClass('completed');
                    progressPercent = 20; // 1/5 of progress

                    // Check approval logs - using token null as indicator for completion
                    const approvalLogs = data.approval_logs || [];
                    let managerApproved = false;
                    let allApprovalsComplete = false;
                    let hasPaymentProof = data.payments && data.payments.length > 0;

                    // Check for manager approval (level 1)
                    const managerLog = approvalLogs.find(log => log.level === 1);
                    if (managerLog && managerLog.token === null) {
                        if (managerLog.status === 'Approved') {
                            steps.filter('[data-step-name="Manager Approval"]').addClass('completed');
                            managerApproved = true;
                            progressPercent = 40; // 2/5 of progress
                        } else if (managerLog.status === 'Rejected') {
                            steps.filter('[data-step-name="Manager Approval"]').addClass('rejected');
                            progressBar.css('width', '40%');
                            return; // Stop here if rejected
                        }
                    } else if (managerLog && managerLog.status === 'Pending') {
                        steps.filter('[data-step-name="Manager Approval"]').addClass('active');
                    }

                    // Check for business controller approval (level 2 and above)
                    const bcLogs = approvalLogs.filter(log => log.level >= 2);
                    let lastBcLevel = 0;
                    let bcComplete = false;

                    if (managerApproved && bcLogs.length > 0) {
                        // Find the highest level approval
                        const maxLevel = Math.max(...bcLogs.map(log => log.level));
                        let allBcApproved = true;
                        let hasRejected = false;
                        let hasPending = false;

                        // Check each level from 2 to maxLevel
                        for (let level = 2; level <= maxLevel; level++) {
                            const levelLog = bcLogs.find(log => log.level === level);
                            if (levelLog && levelLog.token === null) {
                                if (levelLog.status === 'Rejected') {
                                    hasRejected = true;
                                    lastBcLevel = level;
                                    break;
                                } else if (levelLog.status !== 'Approved') {
                                    allBcApproved = false;
                                    break;
                                }
                            } else if (levelLog && levelLog.status === 'Pending') {
                                hasPending = true;
                                lastBcLevel = level;
                                allBcApproved = false;
                                break;
                            } else {
                                allBcApproved = false;
                                break;
                            }
                        }

                        if (hasRejected) {
                            // If rejected at any level, check if payment proof exists
                            if (hasPaymentProof) {
                                // With payment proof, show as completed (not warning)
                                steps.filter('[data-step-name="Business Controller Approval"]').addClass('completed');
                                bcComplete = true;
                                progressPercent = 60; // 3/5 of progress
                                allApprovalsComplete = true;
                            } else {
                                // Without payment proof, show as rejected
                                steps.filter('[data-step-name="Business Controller Approval"]').addClass('rejected');
                                progressBar.css('width', '60%');
                                return; // Stop here if rejected without payment proof
                            }
                        } else if (allBcApproved) {
                            steps.filter('[data-step-name="Business Controller Approval"]').addClass('completed');
                            bcComplete = true;
                            progressPercent = 60; // 3/5 of progress
                            allApprovalsComplete = true;
                        } else if (hasPending) {
                            steps.filter('[data-step-name="Business Controller Approval"]').addClass('active');
                        }
                    } else if (managerApproved) {
                        // If manager approved but no BC logs yet, mark as active
                        steps.filter('[data-step-name="Business Controller Approval"]').addClass('active');
                    }

                    // Check warehouse tracking - consolidated warehouse process
                    const trackings = data.trackings || [];
                    let warehouseComplete = false;

                    if (allApprovalsComplete && trackings.length > 0) {
                        // Check if all warehouse tracking steps are completed
                        const completedTrackings = trackings.filter(tracking => tracking.token === null);
                        const totalTrackings = trackings.length;

                        if (completedTrackings.length === totalTrackings && totalTrackings > 0) {
                            // All warehouse steps completed
                            warehouseComplete = true;
                            steps.filter('[data-step-name="Warehouse Processing"]').addClass('completed');
                            progressPercent = 80; // 4/5 of progress
                        } else if (completedTrackings.length > 0) {
                            // Some warehouse steps completed, but not all
                            steps.filter('[data-step-name="Warehouse Processing"]').addClass('active');
                        } else {
                            // No warehouse steps completed yet, but warehouse process is active
                            steps.filter('[data-step-name="Warehouse Processing"]').addClass('active');
                        }

                        // Check if ready for dispatch
                        if (warehouseComplete) {
                            steps.filter('[data-step-name="Ready for Dispatch"]').addClass('completed');
                            progressPercent = 80; // 4/5 of progress
                        } else if (completedTrackings.length > 0) {
                            steps.filter('[data-step-name="Ready for Dispatch"]').addClass('active');
                        }
                    }

                    // Final completion check
                    if (data.status === 'Completed' || data.status === 'completed') {
                        steps.filter('[data-step-name="Completed"]').addClass('completed');
                        progressPercent = 100;
                    } else if (warehouseComplete) {
                        steps.filter('[data-step-name="Completed"]').addClass('active');
                    }

                    // Set progress bar
                    progressBar.css('width', progressPercent + '%');
                }

                // Populate History Log Function
                function populateHistoryLog(data) {
                    const historyContainer = $('#complain-history-log-container');
                    const history = data.history || [];

                    if (history.length === 0) {
                        historyContainer.html(`
                        <li class="list-group-item text-center py-4">
                            <i class="ph-duotone ph-clock-clockwise fs-2 text-muted mb-2 d-block"></i>
                            <div class="fw-medium text-muted">No history available</div>
                            <small class="text-muted">History will appear here as the requisition progresses</small>
                        </li>
                    `);
                        return;
                    }

                    let historyHtml = '';

                    history.forEach((item, index) => {
                        const timestamp = new Date(item.timestamp).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        historyHtml += `
                        <li class="list-group-item d-flex align-items-start">
                            <div class="history-item-icon ${item.color}">
                                <i class="ph-duotone ${item.icon}"></i>
                            </div>
                            <div class="history-item-content">
                                <div class="history-item-title">${item.title}</div>
                                <div class="history-item-description">${item.description}</div>
                                <div class="history-item-timestamp">${timestamp}</div>
                            </div>
                        </li>
                    `;
                    });

                    historyContainer.html(historyHtml);
                }

                // === Modal Create ===
                $('#btn-create-compline').on('click', function() {
                    $('#complineForm')[0].reset();
                    $('#requisition_items').empty();
                    $('#product-detail').remove();
                    $('[data-error-for]').text('');
                    $('#complineForm .is-invalid').removeClass('is-invalid');

                    // Reset image preview
                    $('#imagePreviewContainer').addClass('d-none');
                    $('#imagePreviewList').empty();

                    // Reset product details container to show placeholder
                    $('#productDetailsContainer').html(`
                    <div id="productDetailsPlaceholder" class="text-center py-2 text-muted border rounded-md">
                        <i class="ph-duotone ph-package fs-1 mb-3 d-block text-secondary"></i>
                        <h5 class="text-muted mb-2">Product Details</h5>
                        <p class="mb-0">Product details will appear here after selecting the products</p>
                    </div>
                `);

                    // Reset print_batch hidden input
                    $('#print_batch').val('0');

                    var today = new Date().toISOString().split('T')[0];
                    $('#date').val(today);


                    // menarik data costumer dari server
                    $.ajax({
                        url: "{{ route('customers.list') }}",
                        method: "GET",
                        success: function(data) {

                            customerSelect.empty();
                            customerSelect.append('<option value=""></option>');
                            data.forEach(function(customer) {
                                customerSelect.append('<option value="' + customer.id +
                                    '">' + customer.name + '</option>');
                            });

                            customerSelect.select2({
                                theme: "bootstrap-5",
                                placeholder: "Pilih atau cari customer",
                                allowClear: true,
                                dropdownParent: $('#complineModal')
                            });

                            customerSelect.on('change', function() {
                                let selectedCustomer = $(this).val();
                                if (!selectedCustomer) {
                                    addressField.val('');
                                    return;
                                }
                                let selectedAddress = data.find(c => c.id ==
                                    selectedCustomer)?.address || '';
                                addressField.val(selectedAddress);

                            });
                        },
                        error: function() {
                            errorMessage('Failed to load customers');
                        }
                    });

                    // menarik data account dan serial number dari server
                    $.ajax({
                        url: "{{ route('get.serial') }}",
                        method: "GET",
                        success: function(data) {
                            // menampilkan nomor account dan serial number di modal
                            $('#account_display').text(data.account_number);
                            $('#account').val(data.account_number);
                            $('#rs_number_display').text(data.series_number);
                            $('#rs_number').val(data.series_number);
                        },
                        error: function() {
                            $('#rs_number_display').text('serial number gagal dibuat');
                            errorMessage('Failed to generate RS number');
                        }
                    });

                    // menarik data product dari server
                    $.ajax({
                        url: "{{ route('get.product.list') }}",
                        method: "GET",
                        success: function(data) {
                            allProductData = data;

                            productselect.empty();
                            productselect.append('<option value=""></option>');
                            data.items.forEach(function(item) {
                                productselect.append('<option value="' + item.id + '">' +
                                    item.item_master_code + ' - ' + item
                                    .item_master_name + '</option>');
                            });

                            productselect.select2({
                                theme: "bootstrap-5",
                                placeholder: "Pilih atau cari produk",
                                allowClear: true,
                                dropdownParent: $('#complineModal'),
                                width: '100%'
                            });

                            function renderProductDetails() {
                                const selectedProductIds = productselect.val();
                                const selectedTypes = $('input.material-type-filter:checked')
                                    .map(function() {
                                        return this.value;
                                    }).get();
                                const detailsContainer = $('#productDetailsContainer');

                                // Simpan nilai input sebelumnya kalo ada
                                detailsContainer.find(
                                    'input[name$="[qty_required]"], input[name$="[qty_issued]"], input[name$="[batch_number]"], input[name$="[remarks]"]'
                                    ).each(function() {
                                    qtyCache[$(this).attr('name')] = $(this).val();
                                });

                                detailsContainer.empty();

                                if (!selectedProductIds || selectedProductIds.length === 0) {
                                    // Show placeholder when no products selected
                                    detailsContainer.html(`
                                    <div id="productDetailsPlaceholder" class="text-center py-2 text-muted border rounded-md">
                                        <i class="ph-duotone ph-package fs-1 mb-3 d-block text-secondary"></i>
                                        <h5 class="text-muted mb-2">Product Details</h5>
                                        <p class="mb-0">Product details will appear here after selecting the products</p>
                                    </div>
                                `);
                                    return;
                                }

                                let tableRowsHTML = '';

                                selectedProductIds.forEach(function(productId) {
                                    const selectedProduct = allProductData.items.find(
                                        item => item.id == productId);
                                    if (!selectedProduct || !selectedProduct.item_details
                                        .length) return;

                                    // Filter data
                                    const filteredDetails = selectedProduct.item_details
                                        .filter(d =>
                                            selectedTypes.length === 0 || selectedTypes
                                            .includes(d.material_type)
                                        );

                                    if (filteredDetails.length > 0) {
                                        tableRowsHTML += filteredDetails.map(detail => {
                                            const rqName =
                                                `items[${productId}][details][${detail.id}][qty_required]`;
                                            const isName =
                                                `items[${productId}][details][${detail.id}][qty_issued]`;
                                            const batchName =
                                                `items[${productId}][details][${detail.id}][batch_number]`;
                                            const remarksName =
                                                `items[${productId}][details][${detail.id}][remarks]`;
                                            return `
                                            <tr>
                                                <td>${detail.material_type}</td>
                                                <td>${detail.item_detail_code}</td>
                                                <td>${detail.item_detail_name}</td>
                                                <td>${detail.unit}</td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        class="form-control"
                                                        name="${rqName}"
                                                        placeholder="0"
                                                        value="${qtyCache[rqName] ?? ''}">
                                                    <div data-error-for="${rqName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        class="form-control"
                                                        name="${isName}"
                                                        placeholder="0"
                                                        value="${qtyCache[isName] ?? ''}">
                                                    <div data-error-for="${isName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="${batchName}"
                                                        value="${qtyCache[batchName] ?? ''}">
                                                    <div data-error-for="${batchName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="${remarksName}"
                                                        placeholder="Enter remarks"
                                                        value="${qtyCache[remarksName] ?? ''}">
                                                    <div data-error-for="${remarksName}" class="text-danger mt-1 error-message"></div>
                                                </td>
                                            </tr>
                                        `;
                                        }).join(
                                        ''); // Gabungkan semua baris menjadi satu string HTML
                                    } else {
                                        // data dengan filter tidak ditemukan
                                        tableRowsHTML += `
                                        <tr>
                                            <td colspan="8" class="bg-light text-danger text-center">
                                                Tidak ada material tipe <strong>${selectedTypes.join(", ")}</strong> pada produk <strong>${selectedProduct.item_master_code}</strong>
                                            </td>
                                        </tr>
                                    `;
                                    }
                                });

                                const tableHTML = `
                                <table class="table table-bordered table-striped" id="product-detail">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Tipe Material</th>
                                            <th>Kode Detail</th>
                                            <th>Nama Detail</th>
                                            <th>Unit</th>
                                            <th>QTY Required</th>
                                            <th>QTY Issued</th>
                                            <th>Batch Number</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tableRowsHTML}
                                    </tbody>
                                </table>
                            `;

                                detailsContainer.html(tableHTML);
                            }
                            productselect.on('change', renderProductDetails);

                            materialtype.on('change', renderProductDetails);

                        },
                        error: function() {
                            productselect.append('<option>produk gagal dimuat</option>');
                            errorMessage('Failed to fetch product list');
                        }
                    })

                    // Handle image preview and validation
                    $('#complain_images').on('change', function(e) {
                        const files = e.target.files;
                        const previewContainer = $('#imagePreviewContainer');
                        const previewList = $('#imagePreviewList');
                        const maxSize = 1 * 1024 * 1024; // 1MB in bytes
                        const maxFiles = 10; // Maximum 10 files
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

                        // Clear previous errors
                        $('[data-error-for="complain_images"]').text('');
                        $(this).removeClass('is-invalid');
                        previewList.empty();

                        if (files.length === 0) {
                            previewContainer.addClass('d-none');
                            return;
                        }

                        // Validate number of files
                        if (files.length > maxFiles) {
                            $('[data-error-for="complain_images"]').text(
                                `Maksimal ${maxFiles} gambar yang dapat diupload.`);
                            $(this).addClass('is-invalid');
                            previewContainer.addClass('d-none');
                            // Clear the input
                            this.value = '';
                            return;
                        }

                        let validFiles = [];
                        let hasError = false;

                        for (let i = 0; i < files.length; i++) {
                            const file = files[i];

                            // Validate file size
                            if (file.size > maxSize) {
                                $('[data-error-for="complain_images"]').text(
                                    `File "${file.name}" terlalu besar. Ukuran maksimal adalah 1MB.`
                                    );
                                $(this).addClass('is-invalid');
                                hasError = true;
                                break;
                            }

                            // Validate file type
                            if (!allowedTypes.includes(file.type)) {
                                $('[data-error-for="complain_images"]').text(
                                    `File "${file.name}" tidak didukung. Hanya file JPG, PNG, dan GIF yang diperbolehkan.`
                                    );
                                $(this).addClass('is-invalid');
                                hasError = true;
                                break;
                            }

                            validFiles.push(file);
                        }

                        if (hasError) {
                            previewContainer.addClass('d-none');
                            // Clear the input
                            this.value = '';
                            return;
                        }

                        // Show previews for valid files
                        if (validFiles.length > 0) {
                            previewContainer.removeClass('d-none');

                            validFiles.forEach((file, index) => {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const previewHtml = `
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2">
                                                <small class="text-muted">${file.name}</small><br>
                                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                    previewList.append(previewHtml);
                                };
                                reader.readAsDataURL(file);
                            });
                        }
                    });
                });

                // === Submit Form ===
                $('#complineForm').on('submit', function(e) {
                    e.preventDefault();

                    $('#complineForm .is-invalid').removeClass('is-invalid');
                    $('.select2-selection.is-invalid').removeClass('is-invalid'); // Khusus untuk select2
                    $('[data-error-for]').text('');

                    let mode = $(this).attr('data-mode');
                    let userId = $(this).attr('data-id');
                    let url, method;

                    if (mode === 'create') {
                        url = "{{ route('complain-form.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('users') }}/" + userId;
                        method = "POST"; // tetap POST, override pakai _method
                    }

                    let formData = new FormData(this);

                    // Show print batch confirmation dialog first
                    confirmDialog({
                        title: 'Print Batch Confirmation',
                        text: 'Do you want to print the batch for this requisition?',
                        confirmButtonText: 'Yes, Print Batch',
                        cancelButtonText: 'No, Skip Printing',
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        icon: 'question',
                        reverseButtons: true
                    }).then((result) => {
                        // Set print_batch value based on user choice
                        const printBatchValue = result.isConfirmed ? 1 : 0;

                        // Update the hidden input value
                        $('#print_batch').val(printBatchValue);

                        // Recreate FormData to include updated print_batch value
                        let updatedFormData = new FormData(document.getElementById('complineForm'));

                        if (mode === 'edit') {
                            updatedFormData.append('_method', 'PUT'); // override
                        }

                        // Show loading overlay and disable submit button
                        $('#loadingOverlay').fadeIn(300);
                        $('#saveUserBtn').prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...'
                            );

                        // Submit the form
                        $.ajax({
                            url: url,
                            method: method,
                            data: updatedFormData,
                            processData: false,
                            contentType: false,
                            success: function(res) {
                                // Hide loading overlay
                                $('#loadingOverlay').fadeOut(300);

                                // Reset button state
                                $('#saveUserBtn').prop('disabled', false).html(
                                    'Save changes');

                                $('#complineModal').modal('hide');
                                $('#complainTable').DataTable().ajax.reload(null, false);
                                qtyCache = {};

                                // Show success message with print batch status
                                const printStatus = printBatchValue ?
                                    'with batch printing' : 'without batch printing';
                                const successMsg = (mode === 'create') ?
                                    `${res.message} (${printStatus})` :
                                    `Complain updated successfully (${printStatus})`;

                                successMessage(successMsg);
                            },
                            error: function(xhr) {
                                // Hide loading overlay
                                $('#loadingOverlay').fadeOut(300);

                                // Reset button state
                                $('#saveUserBtn').prop('disabled', false).html(
                                    'Save changes');
                                if (xhr.status ===
                                    422) { // Unprocessable Entity -> Error Validasi
                                    let errors = xhr.responseJSON.errors;

                                    for (let key in errors) {

                                        if (key === 'items') {

                                            productselect.next('.select2-container').find(
                                                '.select2-selection').addClass(
                                                'is-invalid');

                                            let errorContainer = $(
                                                '[data-error-for="requisition_items"]');
                                            if (errorContainer.length) {
                                                errorContainer.text(errors[key][0]);
                                            }
                                        } else {
                                            let parts = key.split('.');
                                            let fieldName = parts[0] + parts.slice(1).map(
                                                part => `[${part}]`).join('');

                                            let input = $(`[name="${fieldName}"]`);
                                            let errorContainer = $(
                                                `[data-error-for="${fieldName}"]`);

                                            if (input.length) {
                                                if (input.hasClass(
                                                        'select2-hidden-accessible')) {
                                                    input.next('.select2-container').find(
                                                        '.select2-selection').addClass(
                                                        'is-invalid');
                                                } else {
                                                    input.addClass('is-invalid');
                                                }
                                            }
                                            if (errorContainer.length) {
                                                errorContainer.text(errors[key][0]);
                                            }
                                        }
                                    }

                                    errorMessage('Lengkapi data yang diperlukan');
                                } else {
                                    errorMessage(xhr.responseJSON?.message ||
                                        'Terjadi kesalahan pada server.');
                                }
                            }
                        });

                    }); // End of confirmDialog then()
                });

                // Handle image modal
                $(document).on('click', '.image-clickable', function() {
                    const imageSrc = $(this).data('image-src');
                    const imageTitle = $(this).data('image-title');

                    $('#modalImage').attr('src', imageSrc);
                    $('#imageModalLabel').text(imageTitle);
                    $('#downloadImage').attr('href', imageSrc);

                    // Reset scroll position and show image modal without hiding detail modal
                    $('#imageModal .modal-body').scrollTop(0);
                    $('#imageModal').modal('show');

                    // Optional: Add loading state while image loads
                    $('#modalImage').on('load', function() {
                        $(this).addClass('loaded');
                    }).on('error', function() {
                        $(this).attr('alt', 'Failed to load image');
                    });
                });

                // Function to close image modal properly
                window.closeImageModal = function() {
                    $('#imageModal').modal('hide');
                    // Ensure detail modal remains open and focused
                    setTimeout(() => {
                        if ($('#detailModal').hasClass('show')) {
                            $('body').addClass('modal-open');
                            $('#detailModal').focus();
                        }
                    }, 300);
                };

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-button', function(e) {
                    e.preventDefault();
                    const btn = $(this);
                    confirmDialog({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone!',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        icon: 'warning',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const complainId = btn.data('id');
                            let deleteUrl = "{{ route('complain-form.destroy', ':id') }}".replace(
                                ':id', complainId);

                            $.ajax({
                                url: deleteUrl,
                                method: 'DELETE',
                                data: {
                                    _method: 'DELETE',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    $('#complainTable').DataTable().ajax.reload(null,
                                    false);
                                    successMessage(res.message ||
                                        'Complain deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message ||
                                        'Failed to delete complain');
                                }
                            });
                        } else {
                            warningMessage('Complain deletion canceled');
                        }
                    });
                });

                // Add modal cleanup when detail modal is hidden
                $('#detailModal').on('hidden.bs.modal', function() {
                    // Clear payment proof sections to prevent data mixing
                    $('.payment-proof-section').remove();

                    // Clear other dynamic content
                    $('#detail_productDetailsContainer').empty();
                    $('#requisition_product_list').empty();
                    $('#approval_history_list').empty();
                    $('#current_status_display').empty();

                    // Clear image section
                    $('#complainImagesSection').hide();
                    $('#detail_complain_images').empty();
                });

                // Initialize Bootstrap tooltips for action buttons
                $(document).on('draw.dt', function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });

                // Initialize tooltips on page load
                $('[data-bs-toggle="tooltip"]').tooltip();

                // Enhanced DataTable draw callback for animations
                table.on('draw', function() {
                    // Add staggered animation to table rows
                    $('#complainTable tbody tr').each(function(index) {
                        $(this).css({
                            'animation': 'fadeInUp 0.6s ease-out forwards',
                            'animation-delay': (index * 0.05) + 's',
                            'opacity': '0'
                        });
                    });

                    // Initialize tooltips for new content
                    setTimeout(function() {
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }, 100);
                });

                // Add enhanced search placeholder
                $('#complainTable_filter input').attr({
                    'placeholder': 'Search complaints...',
                    'class': 'form-control'
                });

                // Add icons to DataTable controls
                //$('.dataTables_filter label').prepend('<i class="ph-duotone ph-magnifying-glass me-2"></i>');
                //$('.dataTables_length label').prepend('<i class="ph-duotone ph-list-numbers me-2"></i>');

                // Add fade-in animation to DataTable wrapper
                $('.dataTables_wrapper').css({
                    'animation': 'fadeInUp 0.8s ease-out forwards',
                    'opacity': '0'
                });

                setTimeout(function() {
                    $('.dataTables_wrapper').css('opacity', '1');
                }, 200);
            });
        </script>
    @endpush
</x-app-layout>
