<x-app-layout>
    @section('title')
    Sample Requisition
    @endsection

    @include('components.sample-table-styles')

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/vendor/select/select2.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    @endpush

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Sample Requisition List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-note-pencil f-s-16"></i> Forms
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Sample Requisition</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Grup Filter di Kiri --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- [PERBAIKAN] Tambahkan judul/label yang jelas --}}
                    <span class="text-muted fw-bold">Filter by:</span>

                    <select id="subCategoryFilter" class="form-select select2" style="width: 220px;">
                        <option value="all">All Sub Categories</option>
                        <option value="Packaging">Packaging</option>
                        <option value="Finished Goods">Finished Goods</option>
                        <option value="Special Order">Special Order</option>
                    </select>

                    <select id="statusFilter" class="form-select select2" style="width: 200px;">
                        <option value="all">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Approved">Approved</option>
                        <option value="Completed">Completed</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Recalled">Recalled</option>
                    </select>

                    <button id="resetFilters" class="btn btn-secondary border" data-bs-toggle="tooltip" title="Reset Filters">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div>

                {{-- Tombol Create di Kanan --}}
                <div>
                    <button class="btn new-sample-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#sampleModal" id="btn-create-sample">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Sample</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list"></i>
                        Sample Requisition List
                    </h4>
                    <p class="table-subtitle">View, manage and track all sample requisition submissions</p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="sampleTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No Srs</th>
                                <th>Requester</th>
                                <th>Customer</th>
                                <th>Request Date</th>
                                <th>Sub Category</th>
                                <th>Route To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    <div class="modal fade" id="sampleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="loading-overlay" style="display: none;">
                    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-3 fw-bold">Processing...</h5>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="sampleModalLabel">Create New Sample Requisition</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="sampleForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="sub_category" class="form-label fw-bold">1. Select Sub Category<i
                                        class="text-danger">*</i></label>
                                <select class="form-select select2-styled" id="sub_category" name="sub_category"
                                    style="width: 100%;">
                                    <option></option>
                                    @foreach ($allowedSubCategories as $subCat)
                                    <option value="{{ $subCat }}">{{ $subCat }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="sub_category_hidden">
                                <div class="invalid-feedback" id="sub_category_error"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" value="SAMPLE" readonly>
                            </div>
                        </div>

                        <div id="requisition-form-details" style="display: none;">
                            <div id="main-requisition-data">
                                <hr>
                                <h5 class="fw-bold text-primary mb-3">Requisition Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="customer_id" class="form-label">Customer Name<i
                                                class="text-danger">*</i></label>
                                        <select class="form-select select2-styled" id="customer_id" name="customer_id"
                                            style="width: 100%;">
                                            <option></option>
                                            @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" data-address="{{ $customer->address }}">
                                                {{ $customer->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="customer_id_hidden">
                                        <div class="invalid-feedback" id="customer_id_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="customer_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="customer_address" rows="2" readonly></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="no_srs" class="form-label">SRS No.<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="no_srs" name="no_srs"
                                            value="{{ $generatedSrs }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="account" class="form-label">Account<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control" id="account" name="account"
                                            value="{{ $userAccount }}" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="request_date" class="form-label">Request Date<i
                                                class="text-danger">*</i></label>
                                        <input type="date" class="form-control" id="request_date" name="request_date"
                                            value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="cost_center" class="form-label">Cost Center</label>
                                        <input type="text" class="form-control" id="cost_center" name="cost_center"
                                        placeholder="e.g: CC1001, CC2002e">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="objectives" class="form-label">Objectives<i
                                                class="text-danger">*</i></label>
                                        <textarea class="form-control" id="objectives" name="objectives"
                                            placeholder="e.g: New Product Development, Quality Improvement, Others: Market Testing"
                                            rows="2"></textarea>
                                        <div class="invalid-feedback" id="objectives_error"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="estimated_potential" class="form-label">Estimated Potential<i
                                                class="text-danger">*</i></label>
                                        <textarea class="form-control" id="estimated_potential" name="estimated_potential"
                                            placeholder="e.g.: High, Medium, Low, Others: Specify Here"
                                            rows="2"></textarea>
                                        <div class="invalid-feedback" id="estimated_potential_error"></div>
                                    </div>
                                </div>

                                <hr class="mt-4">

                                <h5 class="fw-bold text-primary mb-2">Product Details</h5>
                                <div class="mb-3" id="product-selection-container" style="display:none;">
                                    <label for="product_select" class="form-label fw-bold">2. Select Product Name<i
                                            class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="product_select" multiple="multiple"
                                        style="width: 100%;"></select>
                                    <div class="form-text">Selecting a product will automatically add all its related items to the list below.</div>
                                </div>

                                <div class="mb-3" id="sample-weight-selection-container" style="display:none;">
                                    <label class="form-label fw-bold">3. Berat Sample<i class="text-danger">*</i></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="30kg">
                                            <label class="form-check-label">30kg</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="15kg">
                                            <label class="form-check-label">15kg</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="1kg">
                                            <label class="form-check-label">1kg</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="500g">
                                            <label class="form-check-label">500gr</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="250g">
                                            <label class="form-check-label">250gr</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="5lt">
                                            <label class="form-check-label">5lt</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="1lt">
                                            <label class="form-check-label">1lt</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" name="weight_selection_option" value="500ml">
                                            <label class="form-check-label">500ml</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input sample-weight-radio" type="radio" id="weight_other_radio" name="weight_selection_option" value="Lainnya">
                                            <label class="form-check-label">Lainnya</label>
                                        </div>
                                        <input type="text" class="form-control form-control-sm mt-2" id="weight_other_input" style="display: none; max-width: 200px;" placeholder="Sebutkan berat lain">
                                        <input type="hidden" name="weight_selection" id="weight_selection">
                                    </div>
                                </div>

                                <div class="mb-3" id="material-type-selection-container" style="display:none;">
                                    <label class="form-label fw-bold">3. Filter by Material Type</label>
                                    <div>
                                        @foreach ($materialTypes as $type)
                                        <div class="form-check">
                                            <input class="form-check-input material-type-checkbox" type="checkbox"
                                                id="type_{{ Str::slug($type) }}" value="{{ $type }}">
                                            <label class="form-check-label"
                                                for="type_{{ Str::slug($type) }}">{{ $type }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3" id="product-selection-container-fg" style="display:none;">
                                    <label for="product_select_fg" class="form-label fw-bold">2. Select Product Name<i
                                            class="text-danger">*</i></label>
                                    <select class="form-select select2-styled" id="product_select_fg" multiple="multiple"
                                        style="width: 100%;"></select>
                                    <button type="button" class="btn btn-success btn-sm mt-2" id="btn-add-items-master">
                                        <i class="ph-bold ph-plus"></i> Add Item to List
                                    </button>
                                </div>

                                <div class="alert alert-danger" id="items_error" style="display: none;"></div>

                                <div class="table-responsive mt-4">
                                    <h6 class="fw-bold">3. Requested Item List</h6>
                                    <table class="table table-bordered w-100">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th class="material-type-column">Material Type</th>
                                                <th>Item Code</th>
                                                <th>Item Name</th>
                                                <th>Unit</th>
                                                <th style="width: 15%;">Qty Required</th>
                                                <th style="width: 15%;">Qty Issued</th>
                                            </tr>
                                        </thead>
                                        <tbody id="requisition-items-tbody">
                                            <tr id="no-items-row">
                                                <td colspan="5" class="text-center">No items have been added yet.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="special-order-fields" style="display: none;">
                                <h5 class="text-primary fw-bold">Special Order Details</h5>
                                <h6 class="text-danger fw-bold"><center><i>To be filled by Marketing</i></center></h6>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label">Tanggal Selesai Sample<i class="text-danger">*</i></label>
                                        <input type="date" class="form-control sm-field" name="end_date">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kemasan Sample<i class="text-danger">*</i></label>

                                        {{-- [MODIFIKASI] Menyesuaikan atribut agar cocok dengan fungsi setupQaRadioLainnya --}}
                                        <div id="packaging-options-wrapper">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="packaging_selection_option" id="pack_tub" value="Tub">
                                                <label class="form-check-label" for="pack_tub">a. Tub</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="packaging_selection_option" id="pack_karton" value="Karton">
                                                <label class="form-check-label" for="pack_karton">b. Karton</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="packaging_selection_option" id="pack_botol" value="Botol">
                                                <label class="form-check-label" for="pack_botol">c. Botol</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="packaging_selection_option" id="pack_jerrycan" value="Jerrycan">
                                                <label class="form-check-label" for="pack_jerrycan">d. Jerrycan</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                {{-- ID radio 'Lainnya' diubah --}}
                                                <input class="form-check-input sm-field" type="radio" name="packaging_selection_option" id="packaging_selection_other_radio" value="Lainnya">
                                                <label class="form-check-label" for="packaging_selection_other_radio">e. Lainnya...</label>
                                            </div>
                                        </div>

                                        {{-- ID input teks 'Lainnya' diubah --}}
                                        <input type="text" class="form-control sm-field mt-2" id="packaging_selection_other_input" style="display: none;" placeholder="Sebutkan kemasan lain...">

                                        {{-- Hidden input tetap sama, ID disesuaikan --}}
                                        <input type="hidden" name="packaging_selection" id="packaging_selection" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sample_count" class="form-label">Jumlah Sample<i class="text-danger">*</i></label>
                                        <input type="text" class="form-control sm-field" name="sample_count" placeholder="e.g.: 2x1kg (setiap product)">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purpose" class="form-label">Tujuan Sample<i class="text-danger">*</i></label>
                                        <textarea class="form-control sm-field" name="purpose" rows="2" placeholder="e.g.: Mengenalkan product SMII ke customer baru"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Certificate of Analysis<i class="text-danger">*</i></label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="coa_required"
                                                    id="coa_yes" value="1">
                                                <label class="form-check-label" for="coa_yes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="coa_required"
                                                    id="coa_no" value="0">
                                                <label class="form-check-label" for="coa_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Dikirim Melalui <i class="text-danger">*</i></label>

                                        {{-- [MODIFIKASI] Menggunakan radio button untuk pilihan --}}
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="shipment_method_option" id="ship_sales" value="Sales">
                                                <label class="form-check-label" for="ship_sales">a. Sales</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="shipment_method_option" id="ship_dhl" value="Delivery (DHL)">
                                                <label class="form-check-label" for="ship_dhl">b. Delivery (DHL)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="shipment_method_option" id="ship_container" value="Container">
                                                <label class="form-check-label" for="ship_container">c. Container</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="shipment_method_option" id="ship_kurir" value="Kurir">
                                                <label class="form-check-label" for="ship_kurir">d. Kurir</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input sm-field" type="radio" name="shipment_method_option" id="shipment_method_other_radio" value="Lainnya">
                                                <label class="form-check-label" for="shipment_method_other_radio">e. Lainnya...</label>
                                            </div>
                                        </div>

                                        {{-- Input teks yang muncul jika "Lainnya" dipilih --}}
                                        <input type="text" class="form-control sm-field mt-2" id="shipment_method_other_input" style="display: none;" placeholder="Sebutkan metode pengiriman lain...">

                                        {{-- Hidden input untuk menyimpan nilai akhir yang akan disubmit --}}
                                        <input type="hidden" name="shipment_method" id="shipment_method" required>
                                    </div>
                                </div>

                                <hr class="mt-4">

                                @if ((isset($userAccount) && $userAccount == '5302') ||
                                auth()->user()->roles()->where('name', 'super-admin')->exists())
                                {{-- GANTI SELURUH ISI DIV "qa-fields-section" DENGAN BLOK KODE DI BAWAH INI --}}
                                <div class="qa-fields-section mt-4">
                                    <h6 class="text-danger fw-bold"><center><i>Diisi oleh QA</i></center></h6>
                                    <div class="mt-3">

                                        {{-- Baris Asal sample --}}
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label fw-semibold">Asal sample</label>
                                            <div class="col-sm-9">
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        {{-- Nama diubah menjadi source_option --}}
                                                        <input class="form-check-input qa-field" type="radio" name="source_option" value="WH">
                                                        <label class="form-check-label">a. WH</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="source_option" value="Reference Sample">
                                                        <label class="form-check-label">b. Reference Sample</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="source_option" value="Batch Refinery">
                                                        <label class="form-check-label">c. Batch Refinery</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="source_option" value="Packing Room">
                                                        <label class="form-check-label">d. Packing Room</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" id="source_other_radio" name="source_option" value="Lainnya">
                                                        <label class="form-check-label">e. Lainnya...</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control form-control-sm mt-2 qa-field" id="source_other_input" style="display: none; max-width: 300px;">
                                                {{-- Input tersembunyi inilah yang akan dikirim dengan nama 'source' --}}
                                                <input type="hidden" name="source" id="source">
                                            </div>
                                        </div>

                                        {{-- Baris Keterangan sample --}}
                                        <div class="row mb-3">
                                            <label class="col-sm-3 col-form-label fw-semibold">Keterangan sample</label>
                                            <div class="col-sm-9">
                                                <div class="mb-2">
                                                    <div class="form-check form-check-inline">
                                                        {{-- Nama diubah menjadi description_option --}}
                                                        <input class="form-check-input qa-field" type="radio" name="description_option" id="keterangan_batch_radio" value="batch">
                                                        <label class="form-check-label" for="keterangan_batch_radio">a. Batch / Pallet No</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="description_option" id="keterangan_wb_radio" value="wb">
                                                        <label class="form-check-label" for="keterangan_wb_radio">b. WB/DEO No</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="description_option" id="keterangan_tank_radio" value="tank">
                                                        <label class="form-check-label" for="keterangan_tank_radio">c. Tank No</label>
                                                    </div>
                                                </div>
                                                <div class="input-group" id="keterangan_sample_input_wrapper" style="display: none; max-width: 400px;">
                                                    <input type="text" class="form-control qa-field" id="keterangan_sample_input_1" placeholder="Masukkan nomor...">
                                                    <span class="input-group-text" id="batch_suffix_p" style="display: none;">P</span>
                                                    <input type="text" class="form-control qa-field" id="keterangan_sample_input_2" style="display: none;" placeholder="No Pallet...">
                                                </div>
                                                {{-- Input tersembunyi inilah yang akan dikirim dengan nama 'description' --}}
                                                <input type="hidden" name="description" id="description">
                                            </div>
                                        </div>

                                        {{-- Baris Tgl Produksi --}}
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label fw-semibold">Tgl Produksi</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control qa-field" name="production_date" style="max-width: 200px;">
                                            </div>
                                        </div>

                                        {{-- Baris Persiapan sample --}}
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label fw-semibold">Persiapan sample</label>
                                            <div class="col-sm-9">
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        {{-- Nama diubah menjadi preparation_method_option --}}
                                                        <input class="form-check-input qa-field" type="radio" name="preparation_method_option" value="Tidak berubah">
                                                        <label class="form-check-label">a. Tidak berubah</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="preparation_method_option" value="Rework Karton">
                                                        <label class="form-check-label">b. Rework Karton</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="preparation_method_option" value="Rework Stencill">
                                                        <label class="form-check-label">c. Rework Stencill</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" name="preparation_method_option" value="Rework Label">
                                                        <label class="form-check-label">d. Rework Label</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" id="preparation_method_other_radio" name="preparation_method_option" value="Lainnya">
                                                        <label class="form-check-label">e. Lainnya...</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control form-control-sm mt-2 qa-field" id="preparation_method_other_input" style="display: none; max-width: 300px;">
                                                <input type="hidden" name="preparation_method" id="preparation_method">
                                            </div>
                                        </div>

                                        {{-- Baris Keterangan --}}
                                        <div class="row mb-3 align-items-center">
                                            <label class="col-sm-3 col-form-label fw-semibold">Keterangan</label>
                                            <div class="col-sm-9">
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        {{-- Nama diubah menjadi sample_notes_option --}}
                                                        <input class="form-check-input qa-field" type="radio" name="sample_notes_option" value="Tempel sticker">
                                                        <label class="form-check-label">a. Tempel sticker</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input qa-field" type="radio" id="sample_notes_other_radio" name="sample_notes_option" value="Lainnya">
                                                        <label class="form-check-label">b. Lainnya...</label>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control form-control-sm mt-2 qa-field" id="sample_notes_other_input" style="display: none; max-width: 300px;">
                                                <input type="hidden" name="sample_notes" id="sample_notes">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="submit" id="saveSampleBtn">Save</button>
                        <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- View Modal (MODIFIED with new layout) --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title text-white" id="viewModalLabel"><i class="ph-bold ph-file-text me-2"></i>Sample Requisition Details</h5>
                    <button type="button" class="btn-close btn-close-white m-0 fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">

                    {{-- CARD 1: MAIN REQUISITION DETAILS --}}
                    <div class="card view-modal-card">
                        <div class="card-header view-modal-card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold ph-identification-card me-2"></i> Requisition Details</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <small class="view-label">Category</small>
                                    <p class="view-data">SAMPLE</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="view-label">Sub Category</small>
                                    <p class="view-data" id="view_sub_category">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">SRS No.</small>
                                    <p class="view-data" id="view_no_srs">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Request Date</small>
                                    <p class="view-data" id="view_request_date">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Customer Name</small>
                                    <p class="view-data" id="view_customer_name">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Address</small>
                                    <p class="view-data" id="view_customer_address">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Account</small>
                                    <p class="view-data" id="view_account">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Cost Center</small>
                                    <p class="view-data" id="view_cost_center">-</p>
                                </div>

                                <div class="col-md-3">
                                    <small class="view-label">Objectives</small>
                                    <p class="view-data" id="view_objectives">-</p>
                                </div>
                                <div class="col-md-3">
                                    <small class="view-label">Estimated Potential</small>
                                    <p class="view-data" id="view_estimated_potential">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 2: REQUESTED ITEM LIST --}}
                    <div class="card view-modal-card">
                         <div class="card-header">
                            <h5 class="fw-bold text-primary mb-3"><i class="ph-bold  ph-list me-2"></i>Requested Item List</h5>
                        </div>
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

                    {{-- CARD 3: SPECIAL ORDER DETAILS (Conditional) --}}
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

                    {{-- CARD 4: APPROVAL TRACKING (MOVED TO BOTTOM) --}}
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
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light-secondary" data-bs-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Unchanged JS from original file ---
        let nextSrsNumber = "{{ $generatedSrs }}";

        function successMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 1500,
                showConfirmButton: true
            });
        }

        function errorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'An Error Occurred',
                text: message
            });
        }

        function warningMessage(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: message
            });
        }

        $(document).ready(function () {
            let isPopulatingForm = false;
            const userDepartmentName = @json($userDepartmentName ?? '');
            const userDepartmentCode = "{{ $userAccount ?? '' }}";

            function initSelect2() {
                function formatSubCategory(option) {
                    if (!option.id) return '<span class="text-muted">Select Sub Category</span>';
                    let icon = '';
                    if (option.text.includes('Packaging')) icon =
                        '<i class="ph ph-package me-2 text-primary"></i>';
                    if (option.text.includes('Finished Goods')) icon =
                        '<i class="ph ph-cube me-2 text-success"></i>';
                    if (option.text.includes('Special')) icon =
                        '<i class="ph ph-star-four me-2 text-warning"></i>';
                    return `<span style='font-weight:500;'>${icon}${option.text}</span>`;
                }

                $('#sub_category').select2({
                    dropdownParent: $('#sampleModal'),
                    width: '100%',
                    placeholder: 'Select Sub Category',
                    allowClear: true,
                    templateResult: formatSubCategory,
                    templateSelection: formatSubCategory,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                function formatCustomer(option) {
                    if (!option.id) return '<span class="text-muted">Select Customer</span>';
                    return `<i class='ph ph-user-circle me-2 text-info'></i> <span style='font-weight:500;'>${option.text}</span>`;
                }
                $('#customer_id').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select Customer',
                    allowClear: true,
                    templateResult: formatCustomer,
                    templateSelection: formatCustomer,
                    escapeMarkup: function (markup) {
                        return markup;
                    }
                });

                $('#product_select').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select products',
                    allowClear: true
                });

                $('#product_select_fg').select2({
                    dropdownParent: $('#sampleModal'),
                    placeholder: 'Select Finished Goods/Spesial Order products',
                    allowClear: true
                });
            }
            initSelect2();

            $('#subCategoryFilter, #statusFilter').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity // Sembunyikan kotak pencarian
            });

            const table = $('#sampleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sample.data') }}",
                    data: function (d) {
                        d.sub_category = $('#subCategoryFilter').val();
                        d.status = $('#statusFilter').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '20px',
                        className: 'text-center dt-no-wrap'
                    },
                    {
                        data: 'no_srs',
                        name: 'requisitions.no_srs',
                        className: 'dt-no-wrap'
                    },
                    {
                        data: 'requester_info',
                        name: 'users.name',
                        className: 'dt-no-wrap'
                    },
                    {
                        data: 'customer_name',
                        name: 'customers.name',
                        className: 'dt-wrap'
                    },
                    {
                        data: 'request_date',
                        name: 'requisitions.request_date',
                        className: 'dt-no-wrap'
                    },
                    {
                        data: 'sub_category',
                        name: 'requisitions.sub_category',
                        className: 'dt-no-wrap'
                    },
                    {
                        data: 'route_to',
                        name: 'requisitions.route_to',
                        className: 'dt-wrap'
                    },
                    {
                        data: 'status',
                        name: 'requisitions.status',
                        className: 'dt-no-wrap'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'dt-no-wrap'
                    }
                ]
            });

            $('#subCategoryFilter, #statusFilter').on('change', function () {
                table.ajax.reload(); // Muat ulang data tabel
            });

            // [BARU] Event listener untuk tombol reset
            $('#resetFilters').on('click', function() {
                $('#subCategoryFilter').val('all').trigger('change');
                $('#statusFilter').val('all').trigger('change');
                // Cukup trigger satu kali karena keduanya akan memuat ulang tabel
            });

            let searchInput = $('#sampleTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function (e) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    let searchTerm = searchInput.val();
                    table.search(searchTerm).draw();
                }, 500);
            });

            $('#sampleTable_filter input').attr({
                'placeholder': '🔍 Search sample...',
                'class': 'form-control'
            });

            function clearValidationErrors() {
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#items_error').hide().text('');
                $('.select2-selection').css('border-color', '');
            }

            function resetForm() {
                $('#sampleForm')[0].reset();
                $('#sampleForm').removeAttr('data-mode data-id');
                $('#sub_category, #customer_id, #product_select, #product_select_fg').val(null).trigger('change');
                $('#requisition-items-tbody').html(
                    '<tr id="no-items-row"><td colspan="6" class="text-center">No items have been added yet.</td></tr>'
                );

                clearValidationErrors();
                $('#requisition-form-details').hide();
                $('#special-order-fields').hide();
                $('.row.g-3.mb-3').show();
                $('#main-requisition-data').show();
                $('.qa-fields-section').hide();
                $('#sub_category, #customer_id, .sm-field, .qa-field').prop('disabled', false);
                $('#saveSampleBtn').text('Save');
            }

            $('#btn-create-sample').on('click', function () {
                resetForm();
                $('#sampleModalLabel').text('Create Sample Requisition');
                $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id');
                $('#sampleForm').data('is-fresh-creation', true);
                $('#no_srs').val(nextSrsNumber);
                $('#sampleModal').modal('show');
            });

            $('#sub_category').on('change', function () {
                const selectedSubCategory = $(this).val();
                $('#items_error').hide().text('');

                // Reset Tampilan
                $('#product-selection-container, #material-type-selection-container, #product-selection-container-fg, #sample-weight-selection-container, #requested-item-list-container, #special-order-fields, #btn-add-items-master, #print-batch-container').hide();

                const isPackaging = selectedSubCategory === 'Packaging';
                $('.material-type-column').toggle(isPackaging);

                // Sesuaikan colspan untuk baris kosong
                const colspan = isPackaging ? 6 : 5;
                if ($('#sampleForm').attr('data-mode') === 'create') {
                    $('#requisition-items-tbody').html(`<tr id="no-items-row"><td colspan="${colspan}" class="text-center">No items have been added yet.</td></tr>`);
                }

                if (!selectedSubCategory) {
                    $('#requisition-form-details').slideUp();
                    return;
                }
                $('#requisition-form-details').slideDown();

                // 3. Logika untuk menampilkan UI dan memanggil AJAX
                if (selectedSubCategory === 'Packaging') {
                    $('#product-selection-container, #material-type-selection-container, #requested-item-list-container, #print-batch-container').slideDown();

                    // AJAX untuk mengisi dropdown produk Packaging
                    $.ajax({
                        url: "{{ route('sample.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (masters) {
                            const productSelect = $('#product_select');
                            productSelect.empty();
                            masters.forEach(m => productSelect.append(new Option(`[${m.item_master_code}] ${m.item_master_name}`, m.id)));
                            productSelect.trigger('change');
                        }
                    });

                } else if (selectedSubCategory === 'Finished Goods' || selectedSubCategory === 'Special Order') {
                    $('#product-selection-container-fg, #requested-item-list-container, #btn-add-items-master').slideDown();
                    $('#print-batch-container').hide();

                    if (selectedSubCategory === 'Special Order') {
                        $('#sample-weight-selection-container, #special-order-fields').slideDown();
                    }

                    // AJAX untuk mengisi dropdown produk (berlaku untuk FG & SO)
                    $.ajax({
                        url: "{{ route('sample.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (masters) {
                            const productSelectFg = $('#product_select_fg');
                            productSelectFg.empty();
                            masters.forEach(m => productSelectFg.append(new Option(`[${m.item_master_code}] ${m.item_master_name}`, m.id)));
                            productSelectFg.trigger('change');
                        }
                    });
                }
            });

            $('#request_date').on('change', function() {
                $('#special_request_date').val($(this).val());
            }).trigger('change'); // Trigger saat halaman dimuat

            $('input[name="weight_selection_option"]').on('change', function() {
                const otherInput = $('#weight_other_input');
                const finalInput = $('#weight_selection');

                if ($(this).val() === 'Lainnya') {
                    otherInput.show().focus();
                    // Saat 'Lainnya' dipilih, nilai final adalah dari teks input
                    finalInput.val(otherInput.val());
                } else {
                    otherInput.hide().val(''); // Sembunyikan dan kosongkan input teks
                    // Saat opsi standar dipilih, nilai final adalah value dari radio button itu
                    finalInput.val($(this).val());
                }
            });

            $('#weight_other_input').on('input', function() {
                $('#weight_other_radio').prop('checked', true);
                $('#weight_selection').val($(this).val());
            });

            $('#customer_id').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const address = selectedOption.data('address') || '';
                $('#customer_address').val(address);
            });

            $('#product_select').on('change', function() {
                if (isPopulatingForm) return;

                const selectedProductIds = $(this).val();
                const isEditMode = $('#sampleForm').attr('data-mode') === 'edit';
                const tbody = $('#requisition-items-tbody');

                if (!selectedProductIds || selectedProductIds.length === 0) {
                    if (!isEditMode) {
                        tbody.html('<tr id="no-items-row"><td colspan="6" class="text-center">No items have been added yet.</td></tr>');
                    } else {
                        tbody.find('tr[id^="item-row-detail-"]').each(function() {
                            const masterId = $(this).data('master-id');
                            if (!selectedProductIds.includes(String(masterId))) {
                                $(this).remove();
                            }
                        });
                        if (tbody.find('tr').length === 0) {
                            tbody.html('<tr id="no-items-row"><td colspan="6" class="text-center">No items have been added yet.</td></tr>');
                        }
                    }
                    applyMaterialTypeFilter();
                    return;
                }

                $.ajax({
                    url: "{{ route('sample.getItemDetailsByProducts') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_ids: selectedProductIds
                    },
                    success: function (itemDetails) {
                        $('#no-items-row').remove();

                        const existingItemDetailIds = new Set();
                        tbody.find('tr[id^="item-row-detail-"]').each(function() {
                            const id = $(this).attr('id').split('-')[3];
                            existingItemDetailIds.add(id);
                        });

                        itemDetails.forEach(detail => {
                            if (!existingItemDetailIds.has(String(detail.id))) {
                                const newRow = `
                                    <tr id="item-row-detail-${detail.id}" data-master-id="${detail.item_master_id}" data-material-type="${detail.material_type}">
                                        <td><span class="badge bg-info">${detail.material_type}</span></td>
                                        <td>${detail.item_detail_code}</td>
                                        <td>${detail.item_detail_name}</td>
                                        <td>${detail.unit}</td>
                                        <td><input type="number" class="form-control" name="items[${detail.id}][quantity_required]" min="1"></td>
                                        <td><input type="number" class="form-control" name="items[${detail.id}][quantity_issued]" min="0"></td>
                                    </tr>`;
                                tbody.append(newRow);
                            }
                        });

                        // Langsung terapkan filter. Item baru akan tampil jika cocok dengan filter yang ada.
                        applyMaterialTypeFilter();
                    },
                    error: function() {
                        errorMessage('Failed to load item details.');
                    }
                });
            });

            // --- [NEW] Handler untuk menghapus produk dari $('#product_select') (Packaging) ---
            $('#product_select').on('select2:unselect', function(e) {
                const unselectedMasterId = e.params.data.id;
                // Hapus semua baris item detail yang berelasi dengan item master yang di-unselect
                $(`tr[data-master-id="${unselectedMasterId}"][id^="item-row-detail-"]`).remove();

                if ($('#requisition-items-tbody tr').length === 0) {
                    $('#requisition-items-tbody').html(
                        '<tr id="no-items-row"><td colspan="6" class="text-center">No items have been added yet.</td></tr>'
                    );
                }
                applyMaterialTypeFilter(); // Terapkan filter setelah menghapus item
            });

            function applyMaterialTypeFilter() {
                const selectedTypes = [];
                $('.material-type-checkbox:checked').each(function () {
                    selectedTypes.push($(this).val());
                });

                const tableRows = $('#requisition-items-tbody tr');

                if (selectedTypes.length === 0) {
                    tableRows.show();
                    tableRows.find('input').prop('disabled', false);
                    return;
                }

                tableRows.each(function() {
                    const row = $(this);
                    const rowMaterialType = row.data('material-type');

                    if (selectedTypes.includes(rowMaterialType)) {
                        row.show();
                        row.find('input').prop('disabled', false);
                    } else {
                        row.hide();
                        row.find('input').prop('disabled', true);
                    }
                });
            }

            $('.material-type-checkbox').on('change', function () {
                applyMaterialTypeFilter();
            });

            $('#btn-add-items-master').on('click', function () {
                    const selectedMasterIds = $('#product_select_fg').val();
                    if (!selectedMasterIds || selectedMasterIds.length === 0) {
                        warningMessage('Please select a product first.');
                        return;
                    }
                    $.ajax({
                        url: "{{ route('sample.getAllItemMasters') }}",
                        method: 'GET',
                        success: function (allMasters) {
                            const tbody = $('#requisition-items-tbody');
                            $('#no-items-row').remove();
                            const selectedMasters = allMasters.filter(m => selectedMasterIds.includes(String(m.id)));

                            selectedMasters.forEach(master => {
                                if ($(`#item-row-master-${master.id}`).length === 0) {
                                    // [FIX] Baris ini tidak lagi membuat sel <td> untuk Material Type
                                    const newRow = `
                                        <tr id="item-row-master-${master.id}" data-master-id="${master.id}">
                                            <td>${master.item_master_code}</td>
                                            <td>${master.item_master_name}</td>
                                            <td>${master.unit}</td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_required]" min="1"></td>
                                            <td><input type="number" class="form-control" name="items[${master.id}][quantity_issued]" min="0"></td>
                                        </tr>`;
                                    tbody.append(newRow);
                                }
                            });
                        }
                    });
                });

            $('#product_select_fg').on('select2:unselect', function (e) {
                const unselectedMasterId = e.params.data.id;
                $(`tr[data-master-id="${unselectedMasterId}"][id^="item-row-master-"]`).remove();

                if ($('#requisition-items-tbody tr').length === 0) {
                    $('#requisition-items-tbody').html(
                        '<tr id="no-items-row"><td colspan="6" class="text-center">No items have been added yet.</td></tr>'
                        );
                }
                applyMaterialTypeFilter();
            });

            $('#sampleForm').on('submit', function (e) {
                e.preventDefault(); // Menghentikan submit form default
                    const form = this; // Menyimpan konteks form untuk digunakan nanti

                    // --- 1. Ambil beberapa data kunci untuk ditampilkan di pop-up konfirmasi ---
                    const subCategory = $('#sub_category option:selected').text().trim();
                    const customerName = $('#customer_id option:selected').text().trim();
                    const noSrs = $('#no_srs').val();
                    const requestDate = $('#request_date').val();

                        // --- 2. Tampilkan SweetAlert untuk konfirmasi ---
                        Swal.fire({
                            title: 'Konfirmasi Pengajuan',
                            html: `Anda akan mengajukan Requisition dengan ringkasan data berikut:
                                <ul class="text-start mt-3" style="list-style: none; padding-left: 0;">
                                    <li style="padding: 5px 0;"><strong>Sub Kategori:</strong> ${subCategory || '<i>Belum dipilih</i>'}</li>
                                    <li style="padding: 5px 0;"><strong>SRS No.:</strong> ${noSrs}</li>
                                    <li style="padding: 5px 0;"><strong>Customer:</strong> ${customerName || '<i>Belum dipilih</i>'}</li>
                                    <li style="padding: 5px 0;"><strong>Tgl. Request:</strong> ${requestDate}</li>
                                </ul>
                                <hr>
                                <b class="text-danger">Pastikan semua data yang Anda masukkan sudah benar.</b>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: 'rgba(248, 0, 0, 1)',
                            confirmButtonText: 'Ya, Data Sudah Benar!',
                            cancelButtonText: 'Batal, Cek Lagi'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const mode = $(form).attr('data-mode');
                                const id = $(form).attr('data-id');
                                const currentSubCategory = $('#sub_category').val();

                                function submitForm(formData) {
                                const submitBtn = $('#saveSampleBtn');
                                const overlay = $('#sampleModal .loading-overlay');
                                overlay.show();
                                submitBtn.prop('disabled', true);

                                let url = (mode === 'edit') ? `/sample-form/${id}` : "{{ route('sample-form.store') }}";
                                if (mode === 'edit') {
                                    formData.append('_method', 'PUT');
                                }

                                $.ajax({
                                    url: url,
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        if (res.success) {
                                            $('#sampleModal').modal('hide');
                                            successMessage(res.message);
                                            table.ajax.reload(null, false);

                                            const urlParams = new URLSearchParams(window.location.search);
                                            const openFormId = urlParams.get('open_form');
                                            if (openFormId) {
                                                setTimeout(function() {
                                                    const qaButton = $(`.btn-qa-form[data-id="${openFormId}"]`);
                                                    if (qaButton.length) {
                                                        qaButton.click();
                                                    } else {
                                                        console.warn(`Tombol QA untuk requisition ID ${openFormId} tidak ditemukan.`);
                                                    }
                                                }, 500);
                                            }
                                        }
                                    },

                                    error: function (xhr) {
                                        if (xhr.status === 422) {
                                            const errors = xhr.responseJSON.errors;
                                            let itemErrorMessages = new Set();
                                            clearValidationErrors();
                                            for (const key in errors) {
                                                const errorMsg = errors[key][0];
                                                if (key.startsWith('items.')) {
                                                    itemErrorMessages.add(errorMsg);
                                                } else {
                                                    const field = $(`#${key}`);
                                                    const errorDiv = $(`#${key}_error`);
                                                    field.addClass('is-invalid');
                                                    errorDiv.text(errorMsg).show();
                                                    if (field.hasClass('select2-styled')) {
                                                        field.next('.select2-container').find('.select2-selection').css('border-color', '#dc3545');
                                                    }
                                                }
                                            }
                                            if (itemErrorMessages.size > 0) {
                                                $('#items_error').show().html(Array.from(itemErrorMessages).join('<br>'));
                                            }
                                        } else {
                                            errorMessage(xhr.responseJSON?.message || 'Terjadi kesalahan pada sistem.');
                                        }
                                },
                                complete: function() {
                                    overlay.hide();
                                    submitBtn.prop('disabled', false);
                                }
                            });
                        }

                        // Logika untuk print_batch (jika ada) dipindahkan ke sini juga
                        if (currentSubCategory === 'Packaging' && mode === 'create' && $(form).data('is-fresh-creation')) {
                            Swal.fire({
                                title: 'Print Batch Number',
                                text: "Apakah Anda ingin mencetak Batch Number untuk requisition ini?",
                                icon: 'question',
                                showDenyButton: true,
                                confirmButtonText: 'Yes, Print',
                                denyButtonText: `No, Don't Print`,
                                confirmButtonColor: '#3085d6',
                                denyButtonColor: '#6c757d',
                            }).then((batchResult) => {
                                if (batchResult.isConfirmed) {

                                    if (userDepartmentName === 'R&D') {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Aksi Tidak Diizinkan',
                                            html: 'Departemen R&D tidak dapat melakukan print batch.<br><br><b>Lanjutkan proses tanpa print batch?</b>',
                                            showRecallButton: true,
                                            confirmButtonText: 'Ya, Lanjutkan',
                                            recallButtonText: 'Batal',
                                            confirmButtonColor: '#28a745',
                                        }).then((warningResult) => {
                                            if (warningResult.isConfirmed) {
                                                let formData = new FormData(form);
                                                formData.append('print_batch', '0');
                                                submitForm(formData);
                                            }
                                        });

                                    } else {
                                        let formData = new FormData(form);
                                        formData.append('print_batch', '1');
                                        submitForm(formData);
                                    }

                                } else if (batchResult.isDenied) {
                                    let formData = new FormData(form);
                                    formData.append('print_batch', '0');
                                    submitForm(formData);
                                }
                            });
                        } else {
                            let formData = new FormData(form);

                            if (currentSubCategory === 'Packaging' && mode === 'create' && !$(form).data('is-fresh-creation')) {
                                formData.append('print_batch', '0');
                            }

                            submitForm(formData);
                        }
                    }
                });
            });

            function setupQaRadioLainnya(baseName) {
                const radioSelector = `input[name="${baseName}_option"]`;
                const otherRadio = $(`#${baseName}_other_radio`);
                const otherInput = $(`#${baseName}_other_input`);
                const finalInput = $(`#${baseName}`);

                $(document).on('change', radioSelector, function() {
                    if ($(this).val() === 'Lainnya') {
                        otherInput.show().focus();
                        finalInput.val(otherInput.val());
                    } else {
                        otherInput.hide().val('');
                        finalInput.val($(this).val());
                    }
                });

                otherInput.on('input', function() {
                    otherRadio.prop('checked', true);
                    finalInput.val($(this).val());
                });
            }

            setupQaRadioLainnya('shipment_method');
            setupQaRadioLainnya('packaging_selection');
            setupQaRadioLainnya('source');
            setupQaRadioLainnya('preparation_method');
            setupQaRadioLainnya('sample_notes');


            const keteranganWrapper = $('#keterangan_sample_input_wrapper');
            const keteranganInput1 = $('#keterangan_sample_input_1');
            const keteranganInput2 = $('#keterangan_sample_input_2');
            const batchSuffix = $('#batch_suffix_p');
            const finalDescriptionInput = $('#description');

            $('input[name="description_option"]').on('change', function() {
                const selectedType = $(this).val();

                keteranganInput1.val('');
                keteranganInput2.val('');
                finalDescriptionInput.val('');
                keteranganWrapper.show();

                if (selectedType === 'batch') {
                    keteranganInput1.attr('placeholder', 'Batch No...');
                    keteranganInput2.show().attr('placeholder', 'Pallet No...');
                    batchSuffix.show();
                } else {
                    keteranganInput1.attr('placeholder', selectedType === 'wb' ? 'WB/DEO No...' : 'Tank No...');
                    keteranganInput2.hide();
                    batchSuffix.hide();
                }
            });

            $('#keterangan_sample_input_1, #keterangan_sample_input_2').on('input', function() {
                const selectedType = $('input[name="description_option"]:checked').val();
                const val1 = keteranganInput1.val();
                const val2 = keteranganInput2.val();
                let finalValue = '';

                if (selectedType === 'batch') {
                    finalValue = `${val1}P${val2}`;
                } else if (selectedType === 'wb') {
                    // Format BARU untuk WB/DEO: "WB:Nilai1"
                    finalValue = `WB:${val1}`;
                } else if (selectedType === 'tank') {
                    // Format BARU untuk Tank: "TANK:Nilai1"
                    finalValue = `TANK:${val1}`;
                }

                finalDescriptionInput.val(finalValue);
            });

            function populateForm(data, mode = null) {
                // $('#sampleForm').attr('data-mode', 'edit').attr('data-id', data.id);

                if (mode === 'qa_mode') {
                    // Sembunyikan form utama & pilihan sub-kategori yang interaktif
                    $('.row.g-3.mb-3').hide(); // Menyembunyikan baris "Select Sub Category"
                    $('#main-requisition-data').hide();
                    $('#special-order-fields, .qa-fields-section').show();

                    // Gunakan input tersembunyi untuk mengirim data yang tidak bisa diubah
                    $('#sub_category_hidden').val(data.sub_category).attr('name', 'sub_category');
                    $('#sub_category').removeAttr('name').prop('disabled', true);
                    $('#customer_id_hidden').val(data.customer_id).attr('name', 'customer_id');
                    $('#customer_id').prop('disabled', true);

                    // Isi form marketing agar read-only
                    $('.sm-field').prop('disabled', true);
                    $('.qa-field').prop('disabled', false);
                    $('#saveSampleBtn').text('Save QM Data & Complete').prop('disabled', false);

                } else { // Mode Edit Normal
                    $('.row.g-3.mb-3').show();
                    $('#sub_category').attr('name', 'sub_category').prop('disabled', false);
                    $('#customer_id').prop('disabled', false);
                    $('#sub_category_hidden').removeAttr('name');
                    $('#customer_id_hidden').removeAttr('name');
                    // if ($('#sampleForm').attr('data-mode') === 'edit') {
                    //     $('#saveSampleBtn').text('Save Changes');
                    // }
                    $('.sm-field').prop('disabled', false);
                    $('.qa-field').prop('disabled', true);
                    $('#saveSampleBtn').prop('disabled', false);
                }

                $('#sub_category').val(data.sub_category).trigger('change.select2');
                $('#customer_id').val(data.customer_id).trigger('change.select2');
                if ($('#sampleForm').attr('data-mode') === 'edit') {
                    $('#no_srs').val(data.no_srs);
                }
                $('#account').val(data.account);
                $('#cost_center').val(data.cost_center);
                if (data.request_date) {
                    const formattedDate = new Date(data.request_date).toISOString().split('T')[0];
                    $('#request_date').val(formattedDate);
                }
                $('#objectives').val(data.objectives);
                $('#estimated_potential').val(data.estimated_potential);
                if (data.print_batch !== undefined) {
                    $(`input[name="print_batch"][value="${data.print_batch}"]`).prop('checked', true);
                }

                $('#requisition-form-details').show();

                const isPackaging = data.sub_category === 'Packaging';
                const isFg = data.sub_category === 'Finished Goods';
                const isSpecialOrder = data.sub_category === 'Special Order';

                $('#product-selection-container').toggle(isPackaging);
                $('#material-type-selection-container').toggle(isPackaging);
                $('#print-batch-container').toggle(isPackaging);
                $('#product-selection-container-fg').toggle(isFg || isSpecialOrder);
                $('#requested-item-list-container').toggle(true);
                $('#special-order-fields').toggle(isSpecialOrder);
                $('#sample-weight-selection-container').toggle(isSpecialOrder);
                $('#btn-add-items-master').toggle(isFg || isSpecialOrder);
                $('#sampleModal .material-type-column').toggle(isPackaging);

                // === Mengisi Dropdown Produk ===
                const productSelect = isPackaging ? $('#product_select') : $('#product_select_fg');
                productSelect.empty();
                if (data.product_options && data.product_options.length > 0) {
                    data.product_options.forEach(option => {
                        productSelect.append(new Option(option.text, option.id, false, false));
                    });
                }
                // Set nilai yang sudah tersimpan dari database
                if (data.selected_master_ids && data.selected_master_ids.length > 0) {
                    productSelect.val(data.selected_master_ids);
                }
                productSelect.trigger('change.select2');

                // === Mengisi Checkbox Material Type (hanya untuk Packaging) ===
                if (isPackaging) {
                    $('.material-type-checkbox').prop('checked', false);
                    if (data.attached_material_types && Array.isArray(data.attached_material_types)) {
                        data.attached_material_types.forEach(type => {
                            $(`.material-type-checkbox[value="${type}"]`).prop('checked', true);
                        });
                    }
                }

                if (isPackaging || isFg || isSpecialOrder) {
                    const itemTbody = $('#requisition-items-tbody');
                    itemTbody.empty();

                    if (data.requisition_items && data.requisition_items.length > 0) {
                        data.requisition_items.forEach(item => {
                            let itemCode = 'N/A', itemName = 'N/A', unit = 'N/A', id, type, masterId;
                            let materialType = item.material_type || data.sub_category;

                            if (isPackaging && item.item_detail) {
                                type = 'detail';
                                id = item.item_detail.id;
                                masterId = item.item_master_id;
                                itemCode = item.item_detail.item_detail_code;
                                itemName = item.item_detail.item_detail_name;
                                unit = item.item_detail.unit;
                            } else if ((isFg || isSpecialOrder) && item.item_master) {
                                type = 'master';
                                id = item.item_master.id;
                                masterId = item.item_master.id;
                                itemCode = item.item_master.item_master_code;
                                itemName = item.item_master.item_master_name;
                                unit = item.item_master.unit;
                            }

                             if (itemCode !== 'N/A') {
                                    const materialTypeCell = isPackaging
                                    ? `<td class="material-type-column"><span class="badge bg-info">${item.material_type}</span></td>`
                                    : '';

                                const inputId = isPackaging ? item.item_detail_id : item.item_master_id;
                                const inputName = `items[${id}]`;

                                const newRow = `
                                    <tr id="item-row-${type}-${id}" data-master-id="${masterId}" data-material-type="${item.material_type || ''}">
                                        ${materialTypeCell}
                                        <td>${itemCode}</td>
                                        <td>${itemName}</td>
                                        <td>${unit}</td>
                                        <td><input type="number" class="form-control" name="${inputName}[quantity_required]" value="${item.quantity_required || ''}" min="1"></td>
                                        <td><input type="number" class="form-control" name="${inputName}[quantity_issued]" value="${item.quantity_issued || ''}" min="0"></td>
                                    </tr>`;
                                itemTbody.append(newRow);
                            }
                        });
                    } else {
                        const colspan = isPackaging ? 6 : 5;
                        itemTbody.html(`<tr id="no-items-row"><td colspan="${colspan}" class="text-center">No items found.</td></tr>`);
                    }
                    applyMaterialTypeFilter();
                }

                // === Mengisi Form Special Order ===
                if (isSpecialOrder && data.requisition_special) {
                    const specialData = data.requisition_special;

                    const populateRadioWithOptions = (baseName, value) => {
                        if (!value) return;

                        $(`#${baseName}`).val(value);

                        const standardRadio = $(`input[name="${baseName}_option"][value="${value}"]`);

                        if (standardRadio.length > 0) {
                            standardRadio.prop('checked', true);
                            $(`#${baseName}_other_input`).hide().val('');
                        } else {
                            $(`#${baseName}_other_radio`).prop('checked', true);
                            $(`#${baseName}_other_input`).val(value).show();
                        }
                    };

                    // --- Mengisi data Marketing ---
                    $('input[name="end_date"]').val(specialData.end_date);
                    $('textarea[name="purpose"]').val(specialData.purpose);
                    $('input[name="sample_count"]').val(specialData.sample_count);
                    $('input[name=coa_required][value="' + specialData.coa_required + '"]').prop('checked', true);

                    populateRadioWithOptions('weight_selection', specialData.weight_selection);
                    populateRadioWithOptions('packaging_selection', specialData.packaging_selection);
                    populateRadioWithOptions('shipment_method', specialData.shipment_method);
                }

                if (data.requisition_special) {
                    const populateQaRadio = (baseName, value) => {
                        if (!value) return;
                        const finalInput = $(`#${baseName}`);
                        const standardRadio = $(`input[name="${baseName}_option"][value="${value}"]`);

                        finalInput.val(value);

                        if (standardRadio.length > 0) {
                            standardRadio.prop('checked', true);
                            $(`#${baseName}_other_input`).hide().val('');
                        } else {
                            $(`#${baseName}_other_radio`).prop('checked', true);
                            $(`#${baseName}_other_input`).val(value).show();
                        }
                    };

                    const special = data.requisition_special;
                    populateQaRadio('source', special.source);
                    populateQaRadio('preparation_method', special.preparation_method);
                    populateQaRadio('sample_notes', special.sample_notes);

                    // Mengisi Keterangan Sample
                    const description = special.description || '';
                    finalDescriptionInput.val(description);

                    // Coba parsing nilai description
                    if (description.includes('P')) {
                        $('#keterangan_batch_radio').prop('checked', true).trigger('change');
                        const parts = description.split('P');
                        keteranganInput1.val(parts[0] || '');
                        keteranganInput2.val(parts[1] || '');
                    } else if (description) {
                        $('#keterangan_wb_radio').prop('checked', true).trigger('change');
                        keteranganInput1.val(description);
                    }

                    $('input[name="production_date"]').val(special.production_date);
                }

                // === Menangani Tampilan Field QA/QM ===
                if (mode === 'qa_mode') {
                    $('.qa-fields-section').show();
                    $('.sm-field').prop('disabled', true);
                    $('#customer_id').prop('disabled', true).trigger('change');
                    $('.qa-field').prop('disabled', false);
                    $('#saveSampleBtn').text('Save QM Data & Complete').prop('disabled', false);
                } else {
                    const isSuperAdmin = "{{ auth()->user()->hasRole('super-admin') }}";
                    const userDept = "{{ Auth::user()->department?->name }}";

                    if (userDept !== 'QM & HSE' && !isSuperAdmin) {
                        $('.qa-fields-section').hide();
                    } else {
                        $('.qa-fields-section').show();
                    }
                    $('.sm-field').prop('disabled', false);
                    $('#customer_id').prop('disabled', false).trigger('change');
                    $('.qa-field').prop('disabled', true);
                    $('#saveSampleBtn').text('Save Changes').prop('disabled', false);
                }
            }

            function populateViewForm(data) {
                // --- (Bagian atas fungsi yang mengisi detail tidak berubah) ---
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
                else if (['Rejected'].includes(status)) badgeClass = 'bg-danger';
                else if (['Recalled'].includes(status)) badgeClass = 'bg-secondary';
                else if (status === 'Processing' || status === 'In Progress') badgeClass = 'bg-info';
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
                            steps.push({ id: 'inward_initial', label: 'Inward WH Supervisor (Initial)', icon: 'ph-package' });
                            steps.push({ id: 'material', label: 'Material Support', icon: 'ph-printer' });
                            steps.push({ id: 'inward_final', label: 'Inward WH Supervisor (Final)', icon: 'ph-package' });
                        } else {
                            steps.push({ id: 'inward_final', label: 'Inward WH Supervisor Check', icon: 'ph-package' });
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
                    const positionToStepId = {
                        'Inward WH Supervisor (Initial Check)': 'inward_initial',
                        'Material Support Supervisor': 'material',
                        'Inward WH Supervisor (Final Check)': 'inward_final',
                        'Outward WH Supervisor': 'outward',
                        'Waiting for QA/QM Form': 'qa_form'
                    };
                    data.trackings.forEach(tracking => {
                        // [FIX 1] Hanya proses tracking jika tanggalnya valid (bukan 1970)
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

                        // [FIX 2] Mengubah teks pada langkah aktif agar lebih informatif
                        if (data.route_to) {
                            activeStepElement.find('.tracker-details').html(
                                `<div class="tracker-user" style="color: #ffc107; font-weight: 500;">
                                    <i class="ph-bold ph-arrow-circle-right me-1"></i>Processed by
                                </div>
                                <div class="tracker-date text-dark">${data.route_to}</div>`
                            );
                        }
                    }
                }

                if (lastCompletedIndex >= 0 && !isRejected) {
                    let progressPercentage = (lastCompletedIndex / (steps.length - 1)) * 100;
                    $('#tracker-progress').css('width', progressPercentage + '%');
                }

                // --- (Bagian history log tidak berubah) ---
                const historyContainer = $('#history-log-container');
                historyContainer.empty();
                if (data.history && data.history.length > 0) {
                    data.history.forEach(log => {
                        let badgeClass = 'badge-created', avatarClass = 'avatar-created';
                        const action = log.action.toLowerCase();
                        if (action.includes('approved not review')) { badgeClass = 'badge-approved'; avatarClass = 'avatar-approved'; }
                        else if (action.includes('approved with review')) { badgeClass = 'badge-review'; avatarClass = 'avatar-review'; }
                        else if (action.includes('rejected') || action.includes('recalled')) { badgeClass = 'badge-rejected'; avatarClass = 'avatar-rejected'; }
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

            $(document).on('click', '.btn-view-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/sample-form/${id}`,
                    type: 'GET',
                    success: function(response) {
                        populateViewForm(response);
                        $('#viewModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch requisition details.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-duplicate-requisition', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    // Tambahkan query parameter 'mode=duplicate' agar Controller menghasilkan SRS baru
                    url: `/sample-form/${id}/edit?mode=duplicate`,
                    type: 'GET',
                    success: function(response) {
                        resetForm();
                        $('#sampleModalLabel').text('Duplicate Sample Requisition (New SRS)');
                        $('#sampleForm').attr('data-mode', 'create').removeAttr('data-id'); // Penting: set mode ke 'create'
                        $('#sampleForm').data('is-fresh-creation', false);
                        // Set SRS Number baru yang di-generate dari Controller
                        $('#no_srs').val(response.new_srs || nextSrsNumber);

                        // Hapus SRS baru dari response agar tidak merusak populateForm
                        delete response.new_srs;

                        // Isi form dengan data lama, kecuali SRS
                        isPopulatingForm = true;
                        populateForm(response);
                        isPopulatingForm = false;

                        // Disable field yang tidak boleh diubah pada mode duplicate/edit awal
                        $('.qa-fields-section').hide();
                        $('.qa-field').prop('disabled', true);
                        $('.sm-field').prop('disabled', false); // Marketing fields
                        $('#sub_category').prop('disabled', false); // Memungkinkan ubah sub-category

                        // Tombol harus kembali ke "Save" (store)
                        $('#saveSampleBtn').text('Save as New Requisition').prop('disabled', false);

                        // Show modal
                        $('#sampleModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch data for duplication.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            // [MODIFIKASI] Handler untuk menampilkan modal SweetAlert untuk Recall Notes
            $(document).on('click', '.btn-recall-modal', function () {
                const requisitionId = $(this).data('id');
                const srsNumber = $(this).data('srs');
                const button = $(this);
                const originalHtml = button.html();

                // --- LANGKAH 1: Meminta input alasan recall ---
                Swal.fire({
                    title: `Recall Requisition ${srsNumber}`,
                    width: '600px',
                    html: `
                        <p class="text-danger fw-bold">Tindakan ini akan membatalkan requisition dan tidak dapat di-undo.</p>
                        <textarea id="recallNotes" class="swal2-textarea" placeholder="Mohon berikan alasan untuk recall (wajib)..." style="width: 400px; height: 150px;"></textarea>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Lanjutkan',
                    cancelButtonText: 'Batal',
                    focusConfirm: false,
                    preConfirm: () => {
                        const notes = Swal.getPopup().querySelector('#recallNotes').value;
                        if (!notes.trim()) {
                            Swal.showValidationMessage('Alasan recall wajib diisi.');
                            return false;
                        }
                        return notes;
                    }
                }).then((result) => {
                    // Lanjutkan hanya jika langkah 1 di-konfirmasi dan ada isinya
                    if (result.isConfirmed && result.value) {
                        const notes = result.value;

                        // --- LANGKAH 2: Konfirmasi alasan yang sudah diinput ---
                        Swal.fire({
                            title: 'Konfirmasi Alasan Recall',
                            html: `
                                <p>Pastikan alasan yang Anda masukkan sudah benar:</p>
                                <div style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; text-align: left; margin-top: 10px;">
                                    <i>"${notes}"</i>
                                </div>
                            `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Data Benar & Recall!',
                            cancelButtonText: 'Batal'
                        }).then((confirmResult) => {
                            // Lanjutkan hanya jika langkah 2 di-konfirmasi
                            if (confirmResult.isConfirmed) {
                                // Panggil AJAX untuk proses recall
                                $.ajax({
                                    url: `/sample-form/${requisitionId}/recall`,
                                    type: 'POST',
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        notes: notes // Kirim notes yang sudah dikonfirmasi
                                    },
                                    beforeSend: function() {
                                        button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire('Recalled!', response.message, 'success');
                                            table.ajax.reload(null, false);
                                        }
                                    },
                                    error: function (xhr) {
                                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                                    },
                                    complete: function() {
                                        button.html(originalHtml).prop('disabled', false);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-qa-form', function() {
                const id = $(this).data('id');
                const button = $(this);
                const originalIcon = button.html();

                button.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);

                $.ajax({
                    url: `/sample-form/${id}/edit`,
                    type: 'GET',
                    success: function(response) {
                        // resetForm(); // <<< INI JUGA BARIS YANG SALAH DAN SUDAH DIHAPUS
                        $('#sampleModalLabel').text('Complete QM & HSE Form');
                        $('#sampleForm').attr('data-id', id).attr('data-mode', 'edit');

                        populateForm(response, 'qa_mode'); // Langsung isi form dengan data

                        $('#sampleModal').modal('show');
                    },
                    error: function() {
                        errorMessage('Failed to fetch data for QM form.');
                    },
                    complete: function() {
                        button.html(originalIcon).prop('disabled', false);
                    }
                });
            });

            $('#sampleModal').on('hidden.bs.modal', function () {
                resetForm();
                $('#sampleForm').removeAttr('data-mode data-id');
            });

            const urlParams = new URLSearchParams(window.location.search);
            const openFormId = urlParams.get('open_form');
            if (openFormId) {
                $(`.btn-qa-form[data-id="${openForm-id}"]`).click();
            }
        });

    </script>
    @endpush
</x-app-layout>
