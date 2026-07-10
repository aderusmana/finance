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

        /* Class btn-date tetap ada untuk selector JS, style utamanya sudah di-inline */
        .btn-date {
            /* Styles handled inline for specific box shape requirements */
        }

        /* Responsive */
        @media (max-width: 992px) { .main-container { grid-template-columns: 1fr; } .action-card { position: static; } }
    </style>
</head>
<body>
    @php
        $approverUser = \App\Models\User::where('nik', $log->approver_nik)->first();
        $canAdjust = $approverUser && ($approverUser->hasRole('manager-finance') || $approverUser->hasRole('head-finance') || $approverUser->hasRole('super-admin'));
        $isIT = $approverUser && $approverUser->hasRole('it');

        $doc = $customer->files ? $customer->files->first() : null;
        $npwpPath = ($doc && $doc->npwp_file) ? asset('storage/' . $doc->npwp_file) : null;
        $nibPath  = ($doc && $doc->nib_siup_file) ? asset('storage/' . $doc->nib_siup_file) : null;
        $ktpPath  = ($doc && $doc->ktp_file) ? asset('storage/' . $doc->ktp_file) : null;
        $aktePath = ($doc && $doc->akte_file) ? asset('storage/' . $doc->akte_file) : null;
        $companyProfilePath = ($doc && $doc->company_profile_file) ? asset('storage/' . $doc->company_profile_file) : null;
        $latestRevision = \App\Models\Master\Revision::orderBy('created_at', 'desc')->first();
    @endphp

    <div class="main-container">

        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 fw-bold">Customer Approval</h4>
                            <div class="d-flex align-items-start justify-content-between flex-wrap w-100">
                                <div class="d-flex align-items-center gap-2 text-white opacity-75 mb-2" style="font-size: 0.9rem;">
                                    <span>{{ $customer->code ?? 'New Customer' }}</span>
                                    <span class="mx-1">|</span>
                                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm text-uppercase fw-bold" style="font-size: 0.8rem;">
                                        {{ $customer->status_approval }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($latestRevision)
                            <div class="text-start ms-auto" style="min-width: 180px;">
                                <div style="font-size: 12px; color: rgba(255, 255, 255, 0.75); margin-bottom: 4px;">
                                    <span style="font-weight: 600; margin-right: 4px;">No Rev:</span>
                                    <span style="font-weight: 700; color: #ffffff; font-size: 13px;">{{ $latestRevision->revision_number }}</span>
                                </div>
                                <div style="font-size: 12px; color: rgba(255, 255, 255, 0.75); margin-bottom: 4px;">
                                    <span style="font-weight: 600; margin-right: 4px;">Revision:</span>
                                    <span style="font-weight: 700; color: #ffffff; font-size: 13px;">{{ $latestRevision->revision_count }}</span>
                                </div>
                                <div style="font-size: 12px; color: rgba(255, 255, 255, 0.75);">
                                    <span style="font-weight: 600; margin-right: 4px;">Date:</span>
                                    <span style="font-weight: 700; color: #ffffff; font-size: 13px;">{{ $latestRevision->revision_date ? \Carbon\Carbon::parse($latestRevision->revision_date)->format('d-M-y') : '-' }}</span>
                                </div>
                            </div>
                        @endif
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
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="row g-3">
                                    <div class="col-6">
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
                                    <div class="col-md-6">
                                        <div class="info-label">Sort Name / Alias</div>
                                        <div class="info-value">{{ $customer->sort_name ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">No. PKD</div>
                                        <div class="info-value text-dark fw-bold">{{ $customer->no_pkd ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">PIC (Penanggung Jawab)</div>
                                        <div class="info-value text-dark fw-bold">{{ $customer->pic ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6">
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
                                                <div class="small text-muted">{{ $customer->purchasing_manager_telepon }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small fw-bold">Finance Mgr</td>
                                            <td>
                                                <div class="fw-bold">{{ $customer->finance_manager_name }}</div>
                                                <div class="small text-muted">{{ $customer->finance_manager_email }}</div>
                                                <div class="small text-muted">{{ $customer->finance_manager_telepon }}</div>
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
                                        value="{{ $customer->lead_time == 0 ? '' : $customer->lead_time }}"
                                        placeholder="0"
                                        form="approvalForm" disabled>
                                @else
                                    <div class="info-value fw-bold">{{ $customer->lead_time }} Days</div>
                                @endif
                            </div>
                        </div>

                        {{-- 3. CREDIT LIMIT (AUTO CALC) --}}
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <label class="info-label mb-2">Credit Limit</label>
                                <div class="fs-5 text-success fw-bold">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        {{-- NEW: APPROVED CREDIT LIMIT --}}
                        @if($customer->bank_garansi === 'YA' || strtoupper($customer->term_of_payment) === 'CBD')
                        <div class="col-md-4">
                            <div class="p-3 border rounded border-success bg-light-success h-100">
                                <label class="info-label mb-2 text-success">Approved Credit Limit</label>
                                @if($canAdjust)
                                    <input type="text" class="form-control fw-bold text-success editable-field" 
                                           name="update_approved_credit_limit" 
                                           value="{{ number_format((float)$customer->approved_credit_limit, 0, ',', '.') }}" 
                                           form="approvalForm" disabled placeholder="Input Limit Opsional">
                                @else
                                    <div class="fs-5 text-success fw-bold">IDR {{ number_format((float)$customer->approved_credit_limit, 0, ',', '.') }}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- 4.5. FINANCE PAYMENT & FAKTUR DETAILS (New Section) --}}
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light py-2 d-flex align-items-center">
                                    <i class="fas fa-wallet me-2 text-secondary"></i>
                                    <span class="fw-bold small text-uppercase">Payment & Faktur Schedule (Finance Only)</span>
                                </div>
                                <div class="card-body">
                                    {{-- Virtual Account --}}
                                    <div class="mb-4">
                                        <label class="info-label mb-2">Virtual Account</label>
                                        @if($canAdjust)
                                            <input type="text" class="form-control editable-field"
                                                name="update_va" value="{{ $customer->virtual_account }}"
                                                form="approvalForm" placeholder="Masukkan Nomor VA" disabled>
                                        @else
                                            <div class="info-value fw-bold">{{ $customer->virtual_account ?? '-' }}</div>
                                        @endif
                                    </div>

                                    <div class="row g-4">
                                        {{-- LEFT: BILLING (FAKTUR) --}}
                                        <div class="col-md-6 border-end">
                                            <h6 class="text-success fw-bold mb-3 border-bottom pb-2">Billing Schedule</h6>

                                            {{-- Billing Days --}}
                                            <div class="mb-3">
                                                <label class="info-label mb-2 d-block">Billing Days</label>
                                                @if($canAdjust)
                                                    <div class="schedule-selector" id="faktur_days_container">
                                                        <div id="faktur_days_inputs"></div>
                                                        <button type="button" class="btn btn-sm btn-outline-dark me-2 mb-1 btn-day" data-val="All" onclick="toggleSchedule(this, 'faktur_days')">All Days</button>
                                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                                            <button type="button" class="btn btn-sm btn-outline-success mb-1 btn-day" data-val="{{ $day }}" onclick="toggleSchedule(this, 'faktur_days')">{{ $day }}</button>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div>
                                                        @if(is_array($customer->faktur_days) && in_array('All', $customer->faktur_days))
                                                            <span class="badge bg-dark">All Days</span>
                                                        @elseif(!empty($customer->faktur_days))
                                                            @foreach($customer->faktur_days as $d) <span class="badge bg-success">{{ $d }}</span> @endforeach
                                                        @else <span class="text-muted">-</span> @endif
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Billing Date --}}
                                            <div>
                                                <label class="info-label mb-2 d-block">Billing Date</label>
                                                @if($canAdjust)
                                                    <div class="schedule-selector" id="faktur_date_container">
                                                        <div id="faktur_date_inputs"></div>
                                                        <button type="button" class="btn btn-sm btn-outline-dark me-2 mb-1 w-100" data-val="All" onclick="toggleSchedule(this, 'faktur_date')">All Dates (1-31)</button>
                                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                                            @for($i=1; $i<=31; $i++)
                                                                <button type="button"
                                                                    class="btn btn-xs btn-outline-secondary btn-date"
                                                                    style="width: 38px !important; height: 38px !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 0.85rem !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                                    data-val="{{ $i }}"
                                                                    onclick="toggleSchedule(this, 'faktur_date')">
                                                                    {{ $i }}
                                                                </button>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        @if(is_array($customer->faktur_date) && in_array('All', $customer->faktur_date))
                                                            <span class="badge bg-dark">All Dates</span>
                                                        @elseif(!empty($customer->faktur_date))
                                                            @foreach($customer->faktur_date as $d) <span class="badge bg-info text-dark">{{ $d }}</span> @endforeach
                                                        @else <span class="text-muted">-</span> @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- RIGHT: PAYMENT --}}
                                        <div class="col-md-6">
                                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Payment Schedule</h6>

                                            {{-- Payment Days --}}
                                            <div class="mb-3">
                                                <label class="info-label mb-2 d-block">Payment Days</label>
                                                @if($canAdjust)
                                                    <div class="schedule-selector" id="payment_days_container">
                                                        {{-- Hidden Input untuk kirim array ke backend --}}
                                                        <div id="payment_days_inputs"></div>

                                                        <button type="button" class="btn btn-sm btn-outline-dark me-2 mb-1 btn-day" data-val="All" onclick="toggleSchedule(this, 'payment_days')">All Days</button>
                                                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $day)
                                                            <button type="button" class="btn btn-sm btn-outline-primary mb-1 btn-day" data-val="{{ $day }}" onclick="toggleSchedule(this, 'payment_days')">{{ $day }}</button>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{-- Read Only View --}}
                                                    <div>
                                                        @if(is_array($customer->payment_days) && in_array('All', $customer->payment_days))
                                                            <span class="badge bg-dark">All Days</span>
                                                        @elseif(!empty($customer->payment_days))
                                                            @foreach($customer->payment_days as $d) <span class="badge bg-primary">{{ $d }}</span> @endforeach
                                                        @else <span class="text-muted">-</span> @endif
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Payment Date --}}
                                            <div>
                                                <label class="info-label mb-2 d-block">Payment Date</label>
                                                @if($canAdjust)
                                                    <div class="schedule-selector" id="payment_date_container">
                                                        <div id="payment_date_inputs"></div>
                                                        <button type="button" class="btn btn-sm btn-outline-dark me-2 mb-1 w-100" data-val="All" onclick="toggleSchedule(this, 'payment_date')">All Dates (1-31)</button>
                                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                                            @for($i=1; $i<=31; $i++)
                                                                <button type="button"
                                                                    class="btn btn-xs btn-outline-secondary btn-date"
                                                                    style="width: 38px !important; height: 38px !important; padding: 0 !important; display: inline-flex !important; align-items: center; justify-content: center; font-size: 0.85rem !important; font-weight: 600; line-height: 1 !important; white-space: nowrap !important;"
                                                                    data-val="{{ $i }}"
                                                                    onclick="toggleSchedule(this, 'payment_date')">
                                                                    {{ $i }}
                                                                </button>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        @if(is_array($customer->payment_date) && in_array('All', $customer->payment_date))
                                                            <span class="badge bg-dark">All Dates</span>
                                                        @elseif(!empty($customer->payment_date))
                                                            @foreach($customer->payment_date as $d) <span class="badge bg-info text-dark">{{ $d }}</span> @endforeach
                                                        @else <span class="text-muted">-</span> @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Responsible Person's ID Card</h6>
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
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Articles of Incorporation</h6>
                                    @if($aktePath)
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark w-100 btn-preview-doc"
                                            data-file-url="{{ $aktePath }}"
                                            data-file-title="Articles of Incorporation">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">Not Uploaded</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- 5. COMPANY PROFILE --}}
                        <div class="col-md">
                            <div class="card doc-card h-100 bg-light">
                                <div class="card-body text-center p-3">
                                    <i class="fas fa-building text-secondary fs-3 mb-2"></i>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Company Profile</h6>
                                    @if($companyProfilePath)
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-dark w-100 btn-preview-doc"
                                            data-file-url="{{ $companyProfilePath }}"
                                            data-file-title="Company Profile">
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
                                    @if($isIT) <i class="fas fa-keyboard me-2"></i> Input Code & Approve @else <i class="fas fa-edit me-2"></i> Approved with Notes (Optional) @endif
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
                                <h5 class="fw-bold text-primary mb-3">Verify NPWP Data</h5>
                                <div class="alert alert-info small mb-3">
                                    <i class="fas fa-info-circle me-1"></i> Please verify the NPWP number from the uploaded document. If the number is correct, simply click "Confirm & Save". If it needs correction, please edit the number in the input field and then confirm.
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted small">NPWP Number</label>
                                    <input type="text" id="modal_npwp_input" class="form-control form-control-lg fw-bold text-dark border-primary"
                                        value="{{ $customer->npwp }}" placeholder="Enter the correct NPWP number">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-lg" id="btn-save-verify">
                                        <i class="fas fa-check me-2"></i> Confirm & Save
                                    </button>
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
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

    <!-- AJAX Success Modal -->
    <div class="modal fade" id="ajaxSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 560px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div id="ajaxSuccessModalBody">
                    <!-- injected HTML from approval-success-modal.blade.php -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ==========================================
            // 1. VARIABLES & SETUP
            // ==========================================
            const form = document.getElementById('approvalForm');
            const btnSubmit = document.getElementById('btn-submit');
            const notesField = document.getElementById('notes');
            const noteAsterisk = document.getElementById('note-asterisk');
            const noteHelper = document.getElementById('note-helper');

            const canAdjust = @json($canAdjust);
            const isITMode = document.getElementById('it_code') !== null;

            // --- NEW: Saved Data for Finance Schedule ---
            const savedData = {
                payment_days: @json($customer->payment_days ?? []),
                payment_date: @json($customer->payment_date ?? []),
                faktur_days: @json($customer->faktur_days ?? []),
                faktur_date: @json($customer->faktur_date ?? [])
            };

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

            const meaningfulRegex = /[a-zA-Z]{2,}/;

            // ==========================================
            // 2. NEW LOGIC: PAYMENT & FAKTUR SCHEDULE
            // ==========================================

            window.toggleSchedule = function(btn, type) {
                const container = document.getElementById(type + '_container');
                const value = btn.getAttribute('data-val');
                const isAll = value === 'All';

                const colorClass = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                const dateColor = 'btn-info'; // Tanggal

                if (isAll) {
                    const isActive = btn.classList.contains('active');

                    if (!isActive) {
                        btn.classList.add('active', 'btn-dark');

                        container.querySelectorAll('button:not([data-val="All"])').forEach(b => {
                            b.classList.add('active', 'text-white');
                            b.classList.remove('btn-outline-secondary', 'btn-outline-primary', 'btn-outline-success');

                            if (b.classList.contains('btn-date')) {
                                b.classList.add(dateColor);
                            } else {
                                b.classList.add(colorClass);
                            }
                        });
                    } else {
                        container.querySelectorAll('button').forEach(b => {
                            b.classList.remove('active', 'btn-dark', colorClass, dateColor, 'text-white');
                            if (b.classList.contains('btn-date')) {
                                b.classList.add('btn-outline-secondary');
                            } else if (b.getAttribute('data-val') !== 'All') {
                                b.classList.add(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                            }
                        });
                        btn.classList.add('btn-outline-dark');
                    }

                } else {
                    const allBtn = container.querySelector('button[data-val="All"]');
                    if(allBtn) {
                        allBtn.classList.remove('active', 'btn-dark');
                        allBtn.classList.add('btn-outline-dark');
                    }

                    btn.classList.toggle('active');

                    if (btn.classList.contains('active')) {
                        btn.classList.add('text-white');
                        if (btn.classList.contains('btn-date')) {
                            btn.classList.add(dateColor);
                            btn.classList.remove('btn-outline-secondary');
                        } else {
                            btn.classList.add(colorClass);
                            btn.classList.remove(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                        }
                    } else {
                        btn.classList.remove('text-white', colorClass, dateColor);
                        if (btn.classList.contains('btn-date')) {
                            btn.classList.add('btn-outline-secondary');
                        } else {
                            btn.classList.add(type.includes('faktur') ? 'btn-outline-success' : 'btn-outline-primary');
                        }
                    }
                }

                updateHiddenInputs(type);
            };

            function updateHiddenInputs(type) {
                const container = document.getElementById(type + '_container');
                const inputContainer = document.getElementById(type + '_inputs');
                inputContainer.innerHTML = '';

                const allBtn = container.querySelector('button[data-val="All"]');

                if (allBtn && allBtn.classList.contains('active')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `update_${type}[]`;
                    input.value = 'All';
                    input.setAttribute('form', 'approvalForm');
                    inputContainer.appendChild(input);
                } else {
                    const activeBtns = container.querySelectorAll('button.active:not([data-val="All"])');
                    activeBtns.forEach(btn => {
                        const val = btn.getAttribute('data-val');
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `update_${type}[]`;
                        input.value = val;
                        input.setAttribute('form', 'approvalForm');
                        inputContainer.appendChild(input);
                    });
                }
            }

            function loadSavedSchedule() {
                if (!canAdjust) return;

                ['payment_days', 'payment_date', 'faktur_days', 'faktur_date'].forEach(type => {
                    const data = savedData[type];
                    if (Array.isArray(data)) {
                        data.forEach(val => {
                            const btn = document.querySelector(`#${type}_container button[data-val="${val}"]`);
                            if (btn) {
                                if (val === 'All') {
                                    toggleSchedule(btn, type);
                                } else {
                                    const colorClass = type.includes('faktur') ? 'btn-success' : 'btn-primary';
                                    const dateColor = 'btn-info';

                                    btn.classList.add('active');
                                    if (btn.classList.contains('btn-date')) {
                                        btn.classList.add(dateColor, 'text-white');
                                        btn.classList.remove('btn-outline-secondary');
                                    } else {
                                        btn.classList.add(colorClass, 'text-white');
                                    }
                                }
                            }
                        });
                        updateHiddenInputs(type);
                    }
                });
            }

            // ==========================================
            // 3. EXISTING LOGIC (VALIDATION & UI)
            // ==========================================

            function updateValidationRules() {
                const actionReview = document.getElementById('action_review').checked;
                const actionReject = document.getElementById('action_reject') ? document.getElementById('action_reject').checked : false;

                notesField.required = false;
                noteAsterisk.classList.add('d-none');
                noteHelper.innerText = "";
                noteHelper.className = "text-muted f-s-12";
                if(topMsg) topMsg.classList.add('d-none');

                if (actionReject) {
                    notesField.required = true;
                    noteAsterisk.classList.remove('d-none');
                    noteHelper.innerText = "Reason is required for rejection and should clearly explain the issues.";
                    noteHelper.classList.add('text-danger');
                }
                else if (actionReview && canAdjust) {
                    const currentTop = inputTop.value;
                    if (currentTop !== initialTop) {
                        notesField.required = true;
                        noteAsterisk.classList.remove('d-none');
                        noteHelper.innerText = "Reason is required because Term of Payment is changed.";
                        noteHelper.classList.add('text-danger');
                        if(topMsg) topMsg.classList.remove('d-none');
                    } else {
                        noteHelper.innerText = "Notes are optional for changes to Lead Time / NPWP / Schedule.";
                        noteHelper.classList.add('text-success');
                    }
                }
                // 3. REVIEW (General/Non-IT) -> Notes optional (no required rule)
                else if (actionReview && !canAdjust && !isITMode) {
                    noteHelper.innerText = "Notes are optional.";
                    noteHelper.classList.add('text-success');
                }
            }

            function updateUI() {
                const selected = document.querySelector('input[name="action"]:checked')?.value;
                const isReject = selected === 'reject';

                // Reset submit button to a safe default before applying mode-specific styling
                btnSubmit.classList.remove('btn-danger', 'btn-info', 'btn-success');
                btnSubmit.classList.add('btn-primary');
                btnSubmit.innerHTML = 'Submit Decision';

                // --- Logic Existing: Fields ---
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
                    } else {
                        // General approval/review (notes optional)
                        btnSubmit.classList.remove('btn-danger', 'btn-info', 'btn-success');
                        btnSubmit.classList.add('btn-primary');
                        btnSubmit.innerHTML = '<i class="fas fa-check me-2"></i> Submit Approval';
                    }
                } else if (isReject) {
                    editableFields.forEach(el => el.disabled = true);
                    if(btnVerifyNpwp) btnVerifyNpwp.disabled = true;

                    btnSubmit.classList.remove('btn-primary', 'btn-info', 'btn-success');
                    btnSubmit.classList.add('btn-danger');
                    btnSubmit.innerHTML = '<i class="fas fa-times-circle me-2"></i> Reject Request';
                }

                // --- NEW: Disable Schedule Buttons if Reject ---
                const scheduleButtons = document.querySelectorAll('.schedule-selector button');
                scheduleButtons.forEach(btn => {
                    btn.disabled = isReject;
                    btn.style.pointerEvents = isReject ? 'none' : 'auto';
                    btn.style.opacity = isReject ? '0.6' : '1';
                });

                updateValidationRules();
            }

            // ==========================================
            // 4. EVENT LISTENERS
            // ==========================================

            document.querySelectorAll('input[name="action"]').forEach(el => el.addEventListener('change', updateUI));

            if (canAdjust) {
                if(inputTop) inputTop.addEventListener('change', () => { calculateLimit(); updateValidationRules(); });

                if(inputLead) inputLead.addEventListener('input', () => {
                    calculateLimit();
                    updateValidationRules();
                });
            }

            // Document Preview
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

            // NPWP Verify
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

            // Calculator (UPDATED LOGIC)
            function calculateLimit() {
                if (!inputTop || !inputLead) return;
                const topStr = inputTop.value; // Bisa string "7", "14", "CBD"
                const lt = parseFloat(inputLead.value) || 0;

                // Jika CBD, set 0
                if (topStr === 'CBD') {
                    document.getElementById('display_credit_limit').innerText = 'IDR 0';
                    document.getElementById('final_credit_limit_input').value = 0;
                    document.getElementById('calc-badge').classList.remove('d-none');
                    return;
                }

                let topDays = parseInt(topStr) || 0;
                let divider = topDays; // Default pembagi = TOP

                // Logic Pembagi Khusus
                if (topDays === 7) {
                    divider = 7.5;
                } else if (topDays === 14) {
                    divider = 15;
                }

                // Safety check
                if (divider === 0) divider = 30; // Default fallback

                // Hitung Limit: (TOP + LT) * BaseAmount / Divider
                const result = ((topDays + lt) * baseAmount) / divider;

                document.getElementById('display_credit_limit').innerText = 'IDR ' + new Intl.NumberFormat('id-ID').format(Math.round(result));
                document.getElementById('final_credit_limit_input').value = Math.round(result);
                document.getElementById('calc-badge').classList.remove('d-none');
            }

            // Submit Handler
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const selected = document.querySelector('input[name="action"]:checked').value;
                const notesValue = notesField.value.trim();
                let isValid = true;
                let errorMsg = '';

                if (selected === 'reject') {
                    if (!notesValue) {
                        isValid = false; errorMsg = 'Reason for rejection is required.';
                    } else if (!meaningfulRegex.test(notesValue)) {
                        isValid = false; errorMsg = 'Reason for rejection must be clear (minimum 2 characters).';
                    }
                }
                else if (selected === 'review') {
                    if (canAdjust) {
                        if (inputTop.value !== initialTop) {
                            if (!notesValue) {
                                isValid = false; errorMsg = 'Notes are required because the Term of Payment has changed.';
                            } else if (!meaningfulRegex.test(notesValue)) {
                                isValid = false; errorMsg = 'Notes must be clear (minimum 2 characters).';
                            }
                        } else {
                            if (notesValue.length > 0 && !meaningfulRegex.test(notesValue)) {
                                isValid = false; errorMsg = 'If filling out notes, please use clear sentences.';
                            }
                        }
                    } else if (!isITMode) {
                        // Notes optional for review; validate only if user fills it.
                        if (notesValue.length > 0 && !meaningfulRegex.test(notesValue)) {
                            isValid = false; errorMsg = 'If filling out notes, please use clear sentences.';
                        }
                    }
                }

                if (!isValid) {
                    Swal.fire({ icon: 'warning', title: 'Validation Failed', text: errorMsg });
                    return;
                }

                if (selected === 'review' && canAdjust) {
                    editableFields.forEach(el => el.disabled = false);
                    inputNpwpMain.disabled = false;
                }

                Swal.fire({
                    title: 'Confirm Submission?',
                    text: 'Please ensure the data is correct before submitting.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Submit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('loading-overlay').style.display = 'flex';

                        const formData = new FormData(form);
                        const actionUrl = form.getAttribute('action');

                        fetch(actionUrl, {
                            method: 'POST',
                            headers: { 
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        })
                        .then(async (res) => {
                            const text = await res.text();
                            
                            if (!res.ok) {
                                let errorMsg = `HTTP Error ${res.status}`;
                                try {
                                    const errData = JSON.parse(text);
                                    if (errData.errors) {
                                        errorMsg = Object.values(errData.errors).flat().join('\n');
                                    } else if (errData.message) {
                                        errorMsg = errData.message;
                                    }
                                } catch(e) {}
                                throw new Error(errorMsg);
                            }

                            // Try parse JSON first; if response is JSON use it, otherwise treat as HTML
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                return { success: true, html: text };
                            }
                        })
                        .then((data) => {
                            document.getElementById('loading-overlay').style.display = 'none';
                            if (data && data.success) {
                                if (data.html) {
                                    const modalBody = document.getElementById('ajaxSuccessModalBody');
                                    modalBody.innerHTML = data.html;

                                    const ajaxModalEl = document.getElementById('ajaxSuccessModal');
                                    const ajaxModal = new bootstrap.Modal(ajaxModalEl);
                                    ajaxModal.show();

                                    // Countdown: update #countdown el and close/clear page after 5s
                                    const countdownEl = modalBody.querySelector('#countdown');
                                    let seconds = parseInt(countdownEl?.innerText) || 5;
                                    if (countdownEl) {
                                        countdownEl.innerText = seconds;
                                    }
                                    const iv = setInterval(() => {
                                        seconds--;
                                        if (countdownEl) countdownEl.innerText = seconds;
                                        if (seconds <= 0) {
                                            clearInterval(iv);
                                            try { ajaxModal.hide(); } catch(e){}
                                            try { window.open('', '_self'); window.close(); } catch(e) {}
                                            setTimeout(() => {
                                                try {
                                                    document.body.innerHTML = "<div style='display:flex;height:100vh;justify-content:center;align-items:center;font-family:sans-serif;color:#64748b;flex-direction:column;gap:12px;'><svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' fill='#22c55e' viewBox='0 0 256 256'><path d='M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z'/></svg><div style='font-size:1.1rem;font-weight:600;'>Halaman sudah tidak aktif.</div><div style='font-size:0.9rem;'>Silakan tutup tab ini.</div></div>";
                                                } catch(e) {}
                                            }, 500);
                                        }
                                    }, 1000);
                                } else {
                                    Swal.fire('Success', data.message || 'Action processed.', 'success').then(() => location.reload());
                                }
                            } else {
                                Swal.fire('Error', data.message || 'Failed to process the action.', 'error');
                            }
                        })
                        .catch((err) => {
                            document.getElementById('loading-overlay').style.display = 'none';
                            const msg = (err && err.message) ? err.message : 'Server error';
                            Swal.fire('Error', msg, 'error');
                        });
                    }
                });
            });

            // ==========================================
            // 5. INITIALIZATION
            // ==========================================
            loadSavedSchedule(); // Load data schedule dari DB
            updateUI(); // Set UI state awal
        });
    </script>
</body>
</html>
