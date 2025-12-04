<x-app-layout>
    @section('title')
        Customer List
    @endsection

    @include('components.sample-table-styles')


    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Customer Management</h4>
            {{-- <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-users f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customer List</a>
                </li>
            </ul> --}}
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Filter Section --}}
                {{-- <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold">Filter:</span>
                    <select id="statusFilter" class="form-select select2" style="width: 100px;">
                        <option value="all">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <button id="resetFilters" class="btn btn-secondary border" title="Reset">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </button>
                </div> --}}

                {{-- Create Button --}}
                <div class="ms-auto d-flex">
                    <button class="btn btn-primary" type="button" id="btn-create-customer">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Customer</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-users-three"></i> Customer List
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="w-100 display" id="customerTable">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>City</th>
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
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload NIB/SIUP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_nib" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Upload KTP <span
                                                    class="text-danger">*</span></label>
                                            <input type="file" class="form-control" name="file_ktp" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- A. General Info --}}
                                <div class="card-body">
                                    <div class="row g-3 mb-4">
                                        <h6 class="fw-bold text-secondary">General Information</h6>
                                        <div class="col-md-12">
                                            <label class="form-label">Customer Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="e.g. PT. Maju Mundur Cantik" required>
                                        </div>
                                        <div class="col-md-12">
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
                                        <div class="col-md-4">
                                            <label class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="city" id="city"
                                                placeholder="e.g. Jakarta Selatan" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Postal Code <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="postal_code"
                                                id="postal_code" placeholder="e.g. 12345" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Country <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="country" id="country"
                                                value="Indonesia" placeholder="Country" required>
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
                    e.preventDefault(); // Mencegah submit default browser

                    // Cek validasi HTML native (required, type, dll)
                    if (!this.checkValidity()) {
                        // Jika tidak valid, biarkan browser menampilkan pesan error default
                        e.stopPropagation();
                        this.reportValidity();
                        return;
                    }

                    const formData = new FormData(this);
                    // Untuk default saya set ke store (create), jika edit biasanya ada hidden input ID
                    const url = "{{ route('customers.store') }}";

                    // Tampilkan SweetAlert Konfirmasi
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
                            // Tampilkan Loading
                            Swal.fire({
                                title: 'Menyimpan Data...',
                                html: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Proses AJAX Request
                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    Swal.close(); // Tutup loading

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

                // OCR handling for NPWP upload: extract NPWP number and address
                $(document).on('change', 'input[name="file_npwp"]', function(e) {
                    const file = this.files && this.files[0];
                    if (!file) return;

                    // show temporary notice
                    const originalBtn = $('#btn-save-customer');
                    originalBtn.prop('disabled', true);
                    const notice = $(
                        '<div class="mt-2 text-info" id="ocr-status">Running OCR, please wait...</div>');
                    $(this).closest('.card-body').append(notice);

                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        try {
                            const dataUrl = evt.target.result;
                            // run OCR
                            Tesseract.recognize(dataUrl, 'eng', {
                                    logger: m => console.log(m)
                                })
                                .then(result => {
                                    const text = result.data && result.data.text ? result.data.text :
                                    '';
                                    console.log('OCR text:', text);
                                    // try to find NPWP number pattern like 99.999.999.9-999.999 or groups of digits
                                    let npwpMatch = text.match(/\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}/);
                                    if (!npwpMatch) {
                                        // fallback: any sequence of 9-20 digits and punctuation
                                        npwpMatch = text.match(/[0-9\.\-\s]{9,25}/);
                                    }
                                    const npwp = npwpMatch ? npwpMatch[0].trim() : '';

                                    // --- PERBAIKAN OCR: bila tersedia, ambil alamat mulai dari baris ke-5 ---
                                    const lines = text.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
                                    console.log('OCR lines:', lines);
                                    // Prefer OCR lines 4 and 5 (indices 4 and 5) for Customer Name per request.
                                    // Join line 4 and 5 if both exist. If not available, fall back to line 2.
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
                                        }
                                    } catch (e) {
                                        console.error('Failed to set name from OCR', e);
                                    }
                                    let address = '';

                                    // Prefer fixed start index 5 when OCR has enough lines (user request)
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

                                    // fallback: NPWP-relative heuristics if fixed-start didn't produce an address
                                    if (!address) {
                                        // Normalisasi NPWP yang ditemukan (jika ada)
                                        const npwpPattern = npwpMatch ? npwpMatch[0].trim() : null;
                                        let npwpLineIdx = -1;
                                        if (npwpPattern) {
                                            npwpLineIdx = lines.findIndex(l => l.includes(npwpPattern) || l.replace(/\s+/g, '').includes(npwpPattern.replace(/\s+/g, '')));
                                        }

                                        if (npwpLineIdx >= 0) {
                                            const skipLabelRegex = /\b(NPWP|NPPKP|No\.?|Nama|Name|Alamat|Address|Tgl|Tanggal|SIUP|NIB)\b/i;
                                            const collected = [];
                                            // Skip 2 lines after the NPWP line then start collecting address lines
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

                                    // split address into 24-character chunks without breaking words
                                    function splitChunksWordWrap(str, len) {
                                        if (!str) return [];
                                        // normalize spaces
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

                                    // if NPWP extracted, fill npwp field
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

                // 2. DataTables Configuration
                const table = $('#customerTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.index') }}", // Sesuaikan dengan route Anda
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'customer_class',
                            name: 'customer_class',
                            className: 'text-center'
                        },
                        {
                            data: 'account_group',
                            name: 'account_group'
                        },
                        {
                            data: 'city',
                            name: 'city'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data) {
                                let badge = data === 'Active' ? 'bg-success' : 'bg-secondary';
                                return `<span class="badge ${badge}">${data}</span>`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                $('#statusFilter').on('change', function() {
                    table.ajax.reload();
                });
                $('#resetFilters').on('click', function() {
                    $('#statusFilter').val('all').trigger('change');
                });

                // 3. Logic: Progressive Form Display

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

                // When account group or customer class changes, enable save when both are present
                $('#account_group, #customer_class').on('change', function() {
                    const ag = $('#account_group').val();
                    const cc = $('#customer_class').val();

                    // Ambil option yang sedang dipilih
                    const selectedAg = $('#account_group').find(':selected');

                    if (selectedAg.length) {
                        // Ambil data raw dari attribut (kemungkinan 1 atau 0)
                        let rawBg = selectedAg.data('bank_garansi');
                        let rawCcar = selectedAg.data('ccar');

                        // 1. LOGIC KONVERSI: Ubah 1/0 menjadi 'YA'/'TIDAK'
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

                // 4. Modal Handler
                $('#btn-create-customer').on('click', function() {
                    $('#customerForm')[0].reset();
                    $('.select2-styled').val(null).trigger('change');

                    // Reset Visibility
                    $('#user-info-section').hide();
                    // classification section moved into main form, no separate section to hide
                    $('#main-form-section').hide();
                    $('#btn-save-customer').prop('disabled', true);

                    $('#customerModalLabel').text('Create New Customer');
                    $('#customerModal').modal('show');
                });

                // 5. Populate Data for View/Edit (From Action Button)
                $(document).on('click', '.btn-show-customer', function() {
                    const btn = $(this);
                    // Mengambil semua data-attribute yang dirender dari controller

                    // Reset & Set Values
                    $('#customerForm')[0].reset();
                    $('#customerModalLabel').text('View Customer Details');

                    // Isi select2 User (Trigger change untuk memicu logika display)
                    $('#user_id').val(btn.data('user_id')).trigger('change');

                    // Isi select2 Klasifikasi (account_group and class moved into main form)
                    setTimeout(() => {
                        $('#account_group').val(btn.data('account_group')).trigger('change');
                        $('#customer_class').val(btn.data('customer_class')).trigger('change');
                        // ensure bank_garansi and ccar filled
                        const agSel = $('#account_group').find(':selected');
                        if (agSel.length) {
                            $('#bank_garansi').val(agSel.data('bank_garansi') || btn.data(
                                'bank_garansi') || '');
                            $('#ccar').val(agSel.data('ccar') || btn.data('ccar') || '');
                        } else {
                            $('#bank_garansi').val(btn.data('bank_garansi') || '');
                            $('#ccar').val(btn.data('ccar') || '');
                        }
                    }, 100);

                    // Isi Field Text standar
                    $('#name').val(btn.data('name'));
                    $('#code').val(btn.data('code'));
                    $('#address1').val(btn.data('address1'));
                    $('#address2').val(btn.data('address2'));
                    $('#address3').val(btn.data('address3'));
                    $('#city').val(btn.data('city'));
                    $('#postal_code').val(btn.data('postal_code'));
                    $('#country').val(btn.data('country'));
                    $('#email').val(btn.data('email'));
                    $('#area').val(btn.data('area'));
                    $('#join_date').val(btn.data('join_date'));

                    // Isi Shipping
                    $('#shipping_to_name').val(btn.data('shipping_to_name'));
                    $('#shipping_to_address').val(btn.data('shipping_to_address'));

                    // Isi Managers
                    $('#purchasing_manager_name').val(btn.data('purchasing_manager_name'));
                    $('#purchasing_manager_email').val(btn.data('purchasing_manager_email'));
                    $('#finance_manager_name').val(btn.data('finance_manager_name'));
                    $('#finance_manager_email').val(btn.data('finance_manager_email'));

                    // Isi Billing
                    $('#penagihan_nama_kontak').val(btn.data('penagihan_nama_kontak'));
                    $('#penagihan_telepon').val(btn.data('penagihan_telepon'));
                    $('#penagihan_address').val(btn.data('penagihan_address'));
                    // Surat menyurat address (new)
                    $('#surat_menyurat_address').val(btn.data('surat_menyurat_address'));

                    // Sort name (new)
                    $('#sort_name').val(btn.data('sort_name'));

                    // Isi Tax
                    $('#tax_contact_name').val(btn.data('tax_contact_name'));
                    $('#tax_contact_email').val(btn.data('tax_contact_email'));
                    $('#tax_contact_phone').val(btn.data('tax_contact_phone'));
                    $('#npwp').val(btn.data('npwp'));
                    $('#tanggal_npwp').val(btn.data('tanggal_npwp'));
                    $('#nppkp').val(btn.data('nppkp'));
                    $('#tanggal_nppkp').val(btn.data('tanggal_nppkp'));

                    // Isi Financial
                    $('#output_tax').val(btn.data('output_tax'));
                    $('#term_of_payment').val(btn.data('term_of_payment')).trigger('change');
                    $('#lead_time').val(btn.data('lead_time'));
                    $('#credit_limit').val(btn.data('credit_limit'));
                    $('#ccar').val(btn.data('ccar'));
                    $('#bank_garansi').val(btn.data('bank_garansi'));

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
                    // clear existing product rows and add one empty row
                    $('#calc_products').empty();
                    addCalcRow();

                    // require TOP selected; if not, prompt user to choose TOP first
                    const termVal = $('#term_of_payment').val();
                    if (!termVal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'TOP belum dipilih',
                            text: 'Pilih TOP terlebih dahulu sebelum membuka kalkulator kredit.'
                        });
                        return;
                    }

                    // default TOP for calculation: read from term_of_payment select (value expected numeric days)
                    let topVal = parseFloat(termVal) || 0;
                    $('#calc_top').val(topVal);
                    // lead time
                    let ltVal = parseFloat($('#lead_time').val()) || 0;
                    $('#calc_lt').val(ltVal);
                    // initialize formatted preview (display only)
                    $('#calc_preview_formatted').val('0');
                    // compute initial preview
                    const initialR = computeCreditValues();
                    $('#calc_preview_formatted').val(fmt(Math.round(initialR.val30 || 0)));
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

                // calc_choice removed; preview uses 30-day default

                // save calculated value into credit_limit input
                $('#creditCalcForm').on('submit', function(e) {
                    e.preventDefault();
                    const r = computeCreditValues();
                    // default to 30-day calculation when there's no UI choice
                    const chosen = r.val30 || 0;

                    // set credit_limit input (rounded raw value for submission)
                    $('#credit_limit').val(Math.round(chosen));
                    // close modal robustly
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
    <div class="modal fade" id="creditCalcModal" tabindex="-1" aria-hidden="true">
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
                                <input type="number" step="1" id="calc_lt" class="form-control" />
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
