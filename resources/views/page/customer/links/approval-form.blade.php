<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Approval : {{ $customer->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    {{-- CSS INLINE (INTERNAL) --}}
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background-color: #f1f5f9; color: #334155; }
        .main-container { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; max-width: 1400px; margin: 40px auto; padding: 0 20px; }
        
        /* Card Styles */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); background: white; margin-bottom: 24px; }
        .card-header.main-header { background: linear-gradient(to right, #1e3a8a, #2563eb); color: white; padding: 20px 30px; border-radius: 12px 12px 0 0 !important; }
        
        /* Typography */
        .section-title { font-size: 0.95rem; font-weight: 700; color: #1e3a8a; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; letter-spacing: 0.5px; text-transform: uppercase; }
        .info-label { font-size: 0.7rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 3px; }
        .info-value { font-size: 0.9rem; color: #0f172a; font-weight: 500; word-break: break-word; }
        
        /* Utility Colors */
        .bg-light-info { background-color: #e0f2fe; color: #0284c7; }
        .bg-light-success { background-color: #dcfce7; color: #16a34a; }
        .bg-light-warning { background-color: #fff7ed; color: #9a3412; border: 1px solid #ffedd5; }
        .bg-light-danger { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* Action Card (Sticky) */
        .action-card { position: sticky; top: 30px; border-top: 5px solid #1e3a8a; }
        .form-check-input:checked { background-color: #1e3a8a; border-color: #1e3a8a; }
        .btn-submit { background-color: #1e3a8a; border: none; padding: 12px; font-weight: 600; letter-spacing: 0.5px; }
        .btn-submit:hover { background-color: #1e40af; }

        /* Editable Fields (Finance) */
        .editable-field:disabled { background-color: #e9ecef; border: 1px solid #ced4da; opacity: 1; color: #495057; cursor: not-allowed; }
        .editable-field:not(:disabled) { border: 2px solid #3b82f6; background-color: #fff; color: #1e3a8a; font-weight: bold; }

        /* Document Card */
        .doc-card { transition: all 0.2s; border: 1px solid #e2e8f0; }
        .doc-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-color: #cbd5e1; }

        /* Loading Overlay */
        #loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; }

        /* Preview Content */
        #preview-content-area {
            background-color: #333;
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #preview-content-area img {
            max-width: 100%;
            max-height: 80vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        /* Responsive */
        @media (max-width: 992px) { .main-container { grid-template-columns: 1fr; } .action-card { position: static; } }
    </style>
</head>
<body>
    @php
        $approverUser = \App\Models\User::where('nik', $log->approver_nik)->first();
        // Permission Check
        $canAdjust = $approverUser && ($approverUser->hasRole('manager-finance') || $approverUser->hasRole('head-finance'));
        $isIT = $approverUser && $approverUser->hasRole('it');
        
        // Ambil File untuk Preview
        $doc = $customer->files ? $customer->files->first() : null;
        $npwpPath = ($doc && $doc->npwp_file) ? asset('storage/' . $doc->npwp_file) : null;
        $nibPath  = ($doc && $doc->nib_siup_file) ? asset('storage/' . $doc->nib_siup_file) : null;
        $ktpPath  = ($doc && $doc->ktp_file) ? asset('storage/' . $doc->ktp_file) : null;
        $aktePath = ($doc && $doc->akte_file) ? asset('storage/' . $doc->akte_file) : null;
    @endphp

    <div class="main-container">
        
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">Customer Approval</h4>
                            <p class="mb-0 opacity-75">{{ $customer->code ?? 'New Customer' }}</p>
                        </div>
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm text-uppercase fw-bold" style="font-size: 0.8rem;">
                            {{ $customer->status_approval }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    {{-- IT SECTION --}}
                    @if($isIT)
                    <div class="card border-warning mb-5">
                        <div class="card-body bg-warning bg-opacity-10">
                            <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-keyboard me-2"></i> IT Input Section</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Customer Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold text-primary" name="update_code" id="it_code" value="{{ $customer->code }}" form="approvalForm" required placeholder="e.g. CUST-001">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Join Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="update_join_date" id="it_join_date" value="{{ $customer->join_date ? \Carbon\Carbon::parse($customer->join_date)->format('Y-m-d') : date('Y-m-d') }}" form="approvalForm" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 1. HEADER INFO & GENERAL --}}
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="info-group"><div class="info-label">Sales / Requester</div><div class="info-value fw-bold text-primary">{{ $customer->user->name ?? '-' }}</div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group"><div class="info-label">Customer Name</div><div class="info-value">{{ $customer->name }}</div></div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group"><div class="info-label">Account Group</div><div class="info-value">{{ $customer->accountGroup->name_account_group ?? '-' }}</div></div>
                        </div>
                    </div>

                    <h5 class="section-title mt-5"><i class="fas fa-info-circle me-2"></i> General Information</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="info-label">Main Address</div>
                                        <div class="info-value">{{ $customer->address1 }}</div>
                                        @if($customer->address2) <div class="info-value">{{ $customer->address2 }}</div> @endif
                                        @if($customer->address3) <div class="info-value">{{ $customer->address3 }}</div> @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">City</div>
                                        <div class="info-value">{{ $customer->city }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Postal Code</div>
                                        <div class="info-value">{{ $customer->postal_code }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Area</div>
                                        <div class="info-value">{{ $customer->area }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Country</div>
                                        <div class="info-value">{{ $customer->country }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">Sort Name / Alias</div>
                                        <div class="info-value">{{ $customer->sort_name ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group">
                                        <div class="info-label">No. PKD</div>
                                        <div class="info-value text-dark fw-bold">{{ $customer->no_pkd ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-group">
                                        <div class="info-label">General Email</div>
                                        <div class="info-value"><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. MANAGEMENT & SHIPPING --}}
                    <h5 class="section-title mt-5"><i class="fas fa-users me-2"></i> Key Personnel & Logistics</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2"><span class="fw-bold small text-uppercase">Management</span></div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted small fw-bold" width="35%">Purchasing Mgr</td>
                                            <td>
                                                <div class="fw-bold">{{ $customer->purchasing_manager_name }}</div>
                                                <div class="small text-muted">{{ $customer->purchasing_manager_email }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small fw-bold">Finance Mgr</td>
                                            <td>
                                                <div class="fw-bold">{{ $customer->finance_manager_name }}</div>
                                                <div class="small text-muted">{{ $customer->finance_manager_email }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border h-100">
                                <div class="card-header bg-light py-2"><span class="fw-bold small text-uppercase">Shipping Details</span></div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <div class="info-label">Recipient Name</div>
                                        <div class="info-value">{{ $customer->shipping_to_name }}</div>
                                    </div>
                                    <div>
                                        <div class="info-label">Shipping Address</div>
                                        <div class="info-value">{{ $customer->shipping_to_address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. BILLING & TAX CONTACT --}}
                    <h5 class="section-title mt-5"><i class="fas fa-receipt me-2"></i> Billing & Mail</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-secondary">Billing Information</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-label">Contact Person</div>
                                        <div class="info-value">{{ $customer->penagihan_nama_kontak }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Phone</div>
                                        <div class="info-value">{{ $customer->penagihan_telepon }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-label">Billing Address</div>
                                        <div class="info-value">{{ $customer->penagihan_address }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="info-label">Correspondence Address</div>
                                        <div class="info-value">{{ $customer->surat_menyurat_address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h6 class="fw-bold small border-bottom pb-2 mb-3 text-secondary">Tax Contact Person</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted small fw-bold" width="30%">Name</td>
                                        <td class="fw-bold">{{ $customer->tax_contact_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-bold">Email</td>
                                        <td>{{ $customer->tax_contact_email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted small fw-bold">Phone</td>
                                        <td>{{ $customer->tax_contact_phone }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 4. FINANCIAL TERMS (EDITABLE BY FINANCE) --}}
                    <h5 class="section-title mt-5"><i class="fas fa-coins me-2"></i> Financial Terms & Adjustments</h5>
                    <div class="row g-3 mb-4">
                        {{-- 1. TERM OF PAYMENT --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="info-label mb-2">Term of Payment</label>
                                @if($canAdjust)
                                    <select class="form-select form-select-sm fw-bold editable-field" name="update_top" id="left_top" form="approvalForm" disabled>
                                        <option value="7" {{ $customer->term_of_payment == '7' ? 'selected' : '' }}>Net 7 Days</option>
                                        <option value="14" {{ $customer->term_of_payment == '14' ? 'selected' : '' }}>Net 14 Days</option>
                                        <option value="30" {{ $customer->term_of_payment == '30' ? 'selected' : '' }}>Net 30 Days</option>
                                        <option value="45" {{ $customer->term_of_payment == '45' ? 'selected' : '' }}>Net 45 Days</option>
                                        <option value="CBD" {{ $customer->term_of_payment == 'CBD' ? 'selected' : '' }}>CBD</option>
                                    </select>
                                    <div id="top-changed-msg" class="text-danger small fst-italic mt-1 d-none">
                                        <i class="fas fa-exclamation-circle"></i> Notes required due to TOP change.
                                    </div>
                                @else
                                    <div class="info-value fw-bold text-dark">{{ $customer->term_of_payment }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. LEAD TIME --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="info-label mb-2">Lead Time (Days)</label>
                                @if($canAdjust)
                                    <input type="number" class="form-control form-control-sm fw-bold editable-field" 
                                        name="update_lead_time" id="left_lead_time" 
                                        value="{{ $customer->lead_time }}" form="approvalForm" disabled>
                                @else
                                    <div class="info-value fw-bold">{{ $customer->lead_time }} Days</div>
                                @endif
                            </div>
                        </div>

                        {{-- 3. CREDIT LIMIT (AUTO CALC) --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="info-label mb-2">Credit Limit (Calculated)</label>
                                <input type="hidden" name="update_credit_limit_value" id="final_credit_limit_input" value="{{ $customer->credit_limit }}" form="approvalForm">
                                <div class="fs-5 text-success fw-bold" id="display_credit_limit">
                                    IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}
                                </div>
                                <span id="calc-badge" class="badge bg-warning text-dark mt-1 d-none" style="font-size: 0.6rem;">
                                    <i class="fas fa-calculator me-1"></i> Auto-Updated
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 5. CREDIT LIMIT CALCULATION ITEMS (TABLE) --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2">
                                    <span class="fw-bold small text-uppercase">Credit Limit Calculation Items (Product Projection)</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="ps-3 bg-light text-secondary small">Item Name</th>
                                                    <th class="bg-light text-secondary small text-end">Quantity</th>
                                                    <th class="bg-light text-secondary small text-end">Price</th>
                                                    <th class="pe-3 bg-light text-secondary small text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalAmount = 0; @endphp
                                                @forelse($customer->items as $item)
                                                    @php 
                                                        $rowTotal = $item->quantity * $item->price; 
                                                        $totalAmount += $rowTotal;
                                                    @endphp
                                                    <tr>
                                                        <td class="ps-3">{{ $item->item_name }}</td>
                                                        <td class="text-end">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                                        <td class="text-end">{{ number_format($item->price, 0, ',', '.') }}</td>
                                                        <td class="pe-3 text-end fw-bold">{{ number_format($rowTotal, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted fst-italic">No items added for calculation.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="border-top-2">
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold ps-3 py-2">Total Monthly Projection :</td>
                                                    <td class="pe-3 text-end fw-bold py-2 text-primary">IDR {{ number_format($totalAmount, 0, ',', '.') }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        {{-- Hidden input for base calculation in JS --}}
                                        <input type="hidden" id="base-total-amount" value="{{ $totalAmount }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. TAX & LEGAL DATA (EDITABLE NPWP) --}}
                    <h5 class="section-title mt-5"><i class="fas fa-file-contract me-2"></i> Tax & Legal Data</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <div class="p-3 border rounded bg-light position-relative">
                                <div class="row align-items-center mb-3">
                                    {{-- NPWP Section --}}
                                    <div class="col-md-6">
                                        <label class="info-label mb-1">NPWP Number</label>
                                        @if($canAdjust)
                                            <div class="input-group">
                                                <input type="text" class="form-control fw-bold editable-field" 
                                                    name="update_npwp" id="input_npwp_main" 
                                                    value="{{ $customer->npwp }}" 
                                                    form="approvalForm" disabled>
                                                
                                                <button type="button" class="btn btn-outline-primary" id="btn-verify-npwp" disabled
                                                    data-bs-toggle="tooltip" title="Lihat File & Edit NPWP">
                                                    <i class="fas fa-search me-1"></i> Verify & Edit
                                                </button>
                                            </div>
                                        @else
                                            <div class="info-value fw-bold text-dark">{{ $customer->npwp }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">NPWP Date</label>
                                        <div class="info-value">{{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->format('d M Y') : '-' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">NPPKP No</label>
                                        <div class="info-value">{{ $customer->nppkp }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">Tanggal NPPKP</label>
                                        <div class="info-value">{{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->format('d M Y') : '-' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">No Pengukuhan Kaber</label>
                                        <div class="info-value">{{ $customer->no_pengukuhan_kaber ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">Output Tax</label>
                                        <div class="info-value">{{ $customer->output_tax }}</div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="info-label mb-1">Bank Garansi Status</label>
                                        <div class="info-value fw-bold {{ $customer->bank_garansi == 'YA' ? 'text-success' : 'text-danger' }}">
                                            {{ $customer->bank_garansi == 'YA' ? 'YES (Active)' : 'NO (Inactive)' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 7. DOCUMENTS (UPDATED TO USE MODAL) --}}
                    <h5 class="section-title mt-5"><i class="fas fa-paperclip me-2"></i> Uploaded Documents</h5>
                    <div class="row g-3">
                        {{-- NPWP --}}
                        <div class="col-md-3">
                            <div class="card doc-card h-100 bg-light">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-file-invoice text-primary fs-3 mb-2"></i>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">NPWP</h6>
                                    @if($npwpPath)
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-preview-doc"
                                            data-file-url="{{ $npwpPath }}"
                                            data-file-title="NPWP Document">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Not Uploaded</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- NIB --}}
                        <div class="col-md-3">
                            <div class="card doc-card h-100 bg-light">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-file-contract text-success fs-3 mb-2"></i>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">NIB / SIUP</h6>
                                    @if($nibPath)
                                        <button type="button" class="btn btn-sm btn-outline-success w-100 btn-preview-doc"
                                            data-file-url="{{ $nibPath }}"
                                            data-file-title="NIB / SIUP Document">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Not Uploaded</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- KTP --}}
                        <div class="col-md-3">
                            <div class="card doc-card h-100 bg-light">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-id-card text-info fs-3 mb-2"></i>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">KTP Penanggung Jawab</h6>
                                    @if($ktpPath)
                                        <button type="button" class="btn btn-sm btn-outline-info w-100 btn-preview-doc"
                                            data-file-url="{{ $ktpPath }}"
                                            data-file-title="KTP Document">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Not Uploaded</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- AKTE --}}
                        <div class="col-md-3">
                            <div class="card doc-card h-100 bg-light">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-scroll text-warning fs-3 mb-2"></i>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Akte Pendirian</h6>
                                    @if($aktePath)
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark w-100 btn-preview-doc"
                                            data-file-url="{{ $aktePath }}"
                                            data-file-title="Akte Pendirian">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Not Uploaded</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    <h5 class="section-title mb-4"><i class="fas fa-gavel"></i> Your Decision</h5>
                    <form id="approvalForm" action="{{ route('customers.approval_action', $customer->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            {{-- REVIEW / APPROVE --}}
                            <div class="form-check p-3 border rounded mb-2 {{ $isIT ? 'bg-light-warning border-warning' : 'bg-light-info' }}">
                                <input class="form-check-input" type="radio" name="action" id="action_review" value="review" {{ $preSelectedAction == 'review' || $isIT ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold d-block" for="action_review">
                                    @if($isIT) <i class="fas fa-keyboard me-2"></i> Input Code & Approve @else <i class="fas fa-edit me-2"></i> Review with Notes @endif
                                </label>
                                @if(!$isIT && $canAdjust) <small class="text-muted ms-4 d-block mt-1" style="font-size: 0.75rem;">Allows editing Terms, Limit & NPWP.</small> @endif
                            </div>

                            {{-- REJECT (Non-IT Only) --}}
                            @if(!$isIT)
                            <div class="form-check p-3 border rounded mb-4 bg-light-danger">
                                <input class="form-check-input" type="radio" name="action" id="action_reject" value="reject">
                                <label class="form-check-label fw-bold text-danger d-block" for="action_reject">
                                    <i class="fas fa-times-circle me-2"></i> Reject Request
                                </label>
                            </div>
                            @endif
                        </div>

                        {{-- NOTES AREA --}}
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold small text-uppercase text-muted">Notes / Reason <span id="note-asterisk" class="text-danger d-none">*</span></label>
                            <textarea class="form-control" name="notes" id="notes" rows="4" placeholder="Enter your notes here..."></textarea>
                            <small id="note-helper" class="text-muted" style="font-size: 0.75rem;"></small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-submit btn-lg text-white shadow-sm" id="btn-submit">Submit Decision</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header bg-dark text-white border-0 py-2">
                    <h6 class="modal-title text-white fw-bold" id="previewModalTitle">
                        <i class="fas fa-file me-2"></i> File Preview
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="preview-content-area">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="verifyNpwpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content h-100" style="min-height: 80vh;">
                <div class="modal-header bg-dark text-white py-2">
                    <h6 class="modal-title fw-bold"><i class="fas fa-search me-2"></i> Verify NPWP Data</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="container-fluid h-100">
                        <div class="row h-100 g-0">
                            {{-- LEFT: File Preview --}}
                            <div class="col-lg-7 d-flex align-items-center justify-content-center border-end bg-secondary" style="background-color: #525252; min-height: 500px;">
                                @if($npwpPath)
                                    @php $ext = pathinfo($npwpPath, PATHINFO_EXTENSION); @endphp
                                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                        <img src="{{ $npwpPath }}" class="img-fluid shadow-sm" style="max-height: 80vh; max-width: 100%;">
                                    @elseif(strtolower($ext) == 'pdf')
                                        <iframe src="{{ $npwpPath }}" width="100%" height="100%" style="min-height: 75vh; border:none;"></iframe>
                                    @else
                                        <div class="text-center text-white">
                                            <p>Preview not available.</p>
                                            <a href="{{ $npwpPath }}" target="_blank" class="btn btn-light btn-sm">Download File</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center text-white-50 p-5">
                                        <i class="fas fa-file-excel fs-1 mb-3"></i>
                                        <p>No NPWP File Uploaded</p>
                                    </div>
                                @endif
                            </div>

                            {{-- RIGHT: Input Form --}}
                            <div class="col-lg-5 p-4 bg-white d-flex flex-column justify-content-center">
                                <h5 class="fw-bold text-primary mb-3">Koreksi Data NPWP</h5>
                                <div class="alert alert-info small mb-3">
                                    <i class="fas fa-info-circle me-1"></i> Cocokkan data di kiri dengan input di bawah.
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small">NOMOR NPWP (SISTEM)</label>
                                    <input type="text" id="modal_npwp_input" class="form-control form-control-lg fw-bold text-dark border-primary" 
                                        value="{{ $customer->npwp }}" placeholder="Masukkan Nomor NPWP yang benar">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-lg" id="btn-save-verify">
                                        <i class="fas fa-check me-2"></i> Konfirmasi & Simpan
                                    </button>
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-primary">Processing...</h5>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- VARIABLES ---
            const form = document.getElementById('approvalForm');
            const btnSubmit = document.getElementById('btn-submit');
            const notesField = document.getElementById('notes');
            const noteAsterisk = document.getElementById('note-asterisk');
            const noteHelper = document.getElementById('note-helper');
            
            const canAdjust = @json($canAdjust);
            const isITMode = document.getElementById('it_code') !== null;

            // Finance Fields
            const inputTop = document.getElementById('left_top');
            const inputLead = document.getElementById('left_lead_time');
            const inputNpwpMain = document.getElementById('input_npwp_main');
            const btnVerifyNpwp = document.getElementById('btn-verify-npwp');
            const topMsg = document.getElementById('top-changed-msg');
            const editableFields = document.querySelectorAll('.editable-field');

            // Modal Elements
            const verifyModalEl = document.getElementById('verifyNpwpModal');
            const verifyModal = new bootstrap.Modal(verifyModalEl);
            const modalNpwpInput = document.getElementById('modal_npwp_input');
            const btnSaveVerify = document.getElementById('btn-save-verify');

            // Document Preview Modal
            const docPreviewModalEl = document.getElementById('documentPreviewModal');
            const docPreviewModal = new bootstrap.Modal(docPreviewModalEl);
            const previewContentArea = document.getElementById('preview-content-area');
            const previewModalTitle = document.getElementById('previewModalTitle');

            // Initial Values (Untuk validasi changes)
            const initialTop = "{{ $customer->term_of_payment }}";
            const baseAmount = parseFloat(document.getElementById('base-total-amount')?.value || 0);

            // Regex untuk kata yang jelas (Minimal 2 huruf)
            // Mencegah: "123", "-", ".", "?", "y", "ok" (ok diterima jika 2 huruf)
            const meaningfulRegex = /[a-zA-Z]{2,}/;

            // --- VALIDATION RULE UPDATE ---
            function updateValidationRules() {
                const actionReview = document.getElementById('action_review').checked;
                const actionReject = document.getElementById('action_reject') ? document.getElementById('action_reject').checked : false;
                
                // Reset states
                notesField.required = false;
                noteAsterisk.classList.add('d-none');
                noteHelper.innerText = "";
                noteHelper.className = "text-muted f-s-12"; // Reset class
                if(topMsg) topMsg.classList.add('d-none');

                // 1. LOGIC REJECT (Semua Role)
                if (actionReject) {
                    notesField.required = true;
                    noteAsterisk.classList.remove('d-none');
                    noteHelper.innerText = "Alasan penolakan wajib diisi dengan kalimat yang jelas.";
                    noteHelper.classList.add('text-danger');
                } 
                // 2. LOGIC REVIEW/APPROVE (Finance)
                else if (actionReview && canAdjust) {
                    const currentTop = inputTop.value;
                    
                    if (currentTop !== initialTop) {
                        // TOP Berubah => Notes Wajib
                        notesField.required = true;
                        noteAsterisk.classList.remove('d-none');
                        noteHelper.innerText = "Reason is required because Term of Payment is changed.";
                        noteHelper.classList.add('text-danger');
                        if(topMsg) topMsg.classList.remove('d-none');
                    } else {
                        // TOP Tidak Berubah (Meskipun Lead Time/NPWP berubah) => Notes Optional
                        noteHelper.innerText = "Notes opsional untuk perubahan Lead Time / NPWP.";
                        noteHelper.classList.add('text-success');
                    }
                }
                // 3. LOGIC REVIEW (Non-Finance / General User - KECUALI IT yang input kode)
                else if (actionReview && !canAdjust && !isITMode) {
                    // Review with Notes => Wajib isi dan validasi kata
                    notesField.required = true;
                    noteAsterisk.classList.remove('d-none');
                    noteHelper.innerText = "Notes wajib diisi dengan kalimat yang jelas.";
                    noteHelper.classList.add('text-danger');
                }
            }

            // --- UI STATE UPDATE ---
            function updateUI() {
                const selected = document.querySelector('input[name="action"]:checked')?.value;

                if (selected === 'review') {
                    if (canAdjust) {
                        editableFields.forEach(el => el.disabled = false);
                        if(btnVerifyNpwp) btnVerifyNpwp.disabled = false;
                        
                        btnSubmit.classList.remove('btn-primary', 'btn-danger');
                        btnSubmit.classList.add('btn-info', 'text-white');
                        btnSubmit.innerHTML = '<i class="fas fa-edit me-2"></i> Submit Review & Changes';
                    } else if (isITMode) {
                        btnSubmit.classList.add('btn-success');
                        btnSubmit.innerHTML = '<i class="fas fa-check-circle me-2"></i> Save Code & Activate';
                    }
                } else if (selected === 'reject') {
                    editableFields.forEach(el => el.disabled = true);
                    if(btnVerifyNpwp) btnVerifyNpwp.disabled = true;

                    btnSubmit.classList.remove('btn-primary', 'btn-info', 'btn-success');
                    btnSubmit.classList.add('btn-danger');
                    btnSubmit.innerHTML = '<i class="fas fa-times-circle me-2"></i> Reject Request';
                }
                updateValidationRules();
            }

            // --- EVENT LISTENERS ---
            document.querySelectorAll('input[name="action"]').forEach(el => el.addEventListener('change', updateUI));

            if (canAdjust) {
                if(inputTop) inputTop.addEventListener('change', () => { calculateLimit(); updateValidationRules(); });
                if(inputLead) inputLead.addEventListener('input', updateValidationRules); 
            }

            // --- DOCUMENT PREVIEW LOGIC ---
            document.querySelectorAll('.btn-preview-doc').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.getAttribute('data-file-url');
                    const title = this.getAttribute('data-file-title');
                    
                    if(!url) return;

                    previewModalTitle.innerHTML = `<i class="fas fa-file me-2"></i> ${title}`;
                    
                    const ext = url.split('.').pop().toLowerCase();
                    let content = '';

                    if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                        content = `<img src="${url}" class="img-fluid" alt="Preview">`;
                    } else if (ext === 'pdf') {
                        content = `<iframe src="${url}" width="100%" height="100%" style="min-height: 80vh; border:none;"></iframe>`;
                    } else {
                        content = `<div class="text-center text-white"><i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i><p>Preview not available.</p><a href="${url}" target="_blank" class="btn btn-primary mt-2">Download File</a></div>`;
                    }

                    previewContentArea.innerHTML = content;
                    docPreviewModal.show();
                });
            });

            // --- MODAL NPWP VERIFY ---
            if (btnVerifyNpwp) {
                btnVerifyNpwp.addEventListener('click', function() {
                    modalNpwpInput.value = inputNpwpMain.value; 
                    verifyModal.show();
                });
            }

            if (btnSaveVerify) {
                btnSaveVerify.addEventListener('click', function() {
                    inputNpwpMain.value = modalNpwpInput.value;
                    verifyModal.hide();
                    updateValidationRules(); 
                });
            }

            // --- CALCULATOR ---
            function calculateLimit() {
                if (!inputTop || !inputLead) return;
                const topStr = inputTop.value;
                const lt = parseFloat(inputLead.value) || 0;
                
                let topDays = 0;
                let divider = 30;

                if (topStr.includes('7')) { topDays = 7; divider = 7.5; }
                else if (topStr.includes('14')) { topDays = 14; divider = 15; }
                else if (topStr.includes('30')) { topDays = 30; divider = 30; }
                else if (topStr.includes('45')) { topDays = 45; divider = 45; }
                else { topDays = parseInt(topStr) || 0; divider = topDays > 0 ? topDays : 30; }

                const result = ((topDays + lt) * baseAmount) / divider;
                
                document.getElementById('display_credit_limit').innerText = 'IDR ' + new Intl.NumberFormat('id-ID').format(Math.round(result));
                document.getElementById('final_credit_limit_input').value = Math.round(result);
                document.getElementById('calc-badge').classList.remove('d-none');
            }

            // --- SUBMIT HANDLER (VALIDATION) ---
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const selected = document.querySelector('input[name="action"]:checked').value;
                const notesValue = notesField.value.trim();
                let isValid = true;
                let errorMsg = '';

                // A. REJECT (Anyone)
                if (selected === 'reject') {
                    if (!notesValue) {
                        isValid = false; errorMsg = 'Alasan penolakan wajib diisi.';
                    } else if (!meaningfulRegex.test(notesValue)) {
                        isValid = false; errorMsg = 'Alasan penolakan harus jelas (minimal 2 huruf, bukan simbol/angka saja).';
                    }
                }
                // B. REVIEW (Approve with Note)
                else if (selected === 'review') {
                    if (canAdjust) {
                        // Finance Logic
                        if (inputTop.value !== initialTop) {
                            // TOP Changed -> Mandatory & Meaningful
                            if (!notesValue) {
                                isValid = false; errorMsg = 'Notes wajib diisi karena Term of Payment berubah.';
                            } else if (!meaningfulRegex.test(notesValue)) {
                                isValid = false; errorMsg = 'Notes harus jelas (minimal 2 huruf).';
                            }
                        } else {
                            // Optional, tapi jika diisi harus jelas
                            if (notesValue.length > 0 && !meaningfulRegex.test(notesValue)) {
                                isValid = false; errorMsg = 'Jika mengisi notes, mohon gunakan kalimat yang jelas.';
                            }
                        }
                    } else if (!isITMode) {
                        // Non-Finance & Non-IT (General User) -> Mandatory & Meaningful
                        if (!notesValue) {
                            isValid = false; errorMsg = 'Notes wajib diisi untuk Review.';
                        } else if (!meaningfulRegex.test(notesValue)) {
                            isValid = false; errorMsg = 'Notes harus jelas (minimal 2 huruf).';
                        }
                    }
                }

                if (!isValid) {
                    Swal.fire({ icon: 'warning', title: 'Validasi Gagal', text: errorMsg });
                    return;
                }
                
                // Pastikan field terkirim saat disubmit
                if (selected === 'review' && canAdjust) {
                    editableFields.forEach(el => el.disabled = false);
                    inputNpwpMain.disabled = false; 
                }

                Swal.fire({
                    title: 'Confirm Submission?',
                    text: 'Pastikan data sudah benar sebelum mengirim.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('loading-overlay').style.display = 'flex';
                        form.submit();
                    }
                });
            });

            // Init
            updateUI();
        });
    </script>
</body>
</html>