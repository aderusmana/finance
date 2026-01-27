<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Approval : {{ $customer->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background-color: #f1f5f9; color: #334155; }
        .main-container { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; max-width: 1400px; margin: 40px auto; padding: 0 20px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); background: white; margin-bottom: 24px; }
        .card-header.main-header { background: linear-gradient(to right, #1e3a8a, #2563eb); color: white; padding: 20px 30px; border-radius: 12px 12px 0 0 !important; }
        .section-title { font-size: 0.95rem; font-weight: 700; color: #1e3a8a; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; letter-spacing: 0.5px; text-transform: uppercase; }
        .section-title i { margin-right: 10px; color: #3b82f6; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 0.95rem; color: #0f172a; font-weight: 500; word-break: break-word; }

        /* Warna Background Khusus */
        .bg-light-info { background-color: #e0f2fe; color: #0284c7; }
        .bg-light-success { background-color: #dcfce7; color: #16a34a; }
        .bg-light-warning { background-color: #fff7ed; color: #9a3412; border: 1px solid #ffedd5; }

        .action-card { position: sticky; top: 30px; border-top: 5px solid #1e3a8a; }
        .form-check-input:checked { background-color: #1e3a8a; border-color: #1e3a8a; }
        .btn-submit { background-color: #1e3a8a; border: none; padding: 12px; font-weight: 600; letter-spacing: 0.5px; }
        .btn-submit:hover { background-color: #1e40af; }

        /* Loader Overlay */
        #loading-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 9999; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
        }

        @media (max-width: 992px) {
            .main-container { grid-template-columns: 1fr; }
            .action-card { position: static; }
        }
    </style>
</head>
<body>
    @php
        $approverUser = \App\Models\User::where('nik', $log->approver_nik)->first();
        $canAdjust = $approverUser && ($approverUser->hasRole('manager-finance') || $approverUser->hasRole('head-finance'));
        $isIT = $approverUser && $approverUser->hasRole('it');
    @endphp
    <div class="main-container">
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">Customer Approval</h4>
                            <p class="mb-0 opacity-75"><i class="fas fa-id-card me-2"></i>{{ $customer->code ?? 'New Customer' }}</p>
                        </div>
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
                            {{ $customer->status_approval ?? 'Pending' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if($isIT)
                    <div class="card border-warning mb-5">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-keyboard me-2"></i> IT Input Section</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info small mb-3">
                                <i class="fas fa-info-circle me-1"></i> Sebagai IT, Anda wajib melengkapi data berikut.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Customer Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control fw-bold text-primary"
                                        name="update_code" id="it_code"
                                        value="{{ $customer->code }}"
                                        placeholder="e.g. ID-0001"
                                        form="approvalForm" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">Join Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control"
                                        name="update_join_date" id="it_join_date"
                                        value="{{ $customer->join_date ? \Carbon\Carbon::parse($customer->join_date)->format('Y-m-d') : date('Y-m-d') }}"
                                        form="approvalForm" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Sales / Requester</div>
                                {{-- PERBAIKAN: Menggunakan relasi 'user' bukan 'users' --}}
                                <div class="info-value fw-bold text-primary">
                                    {{ $customer->user->name ?? $customer->created_by }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Account Group</div>
                                {{-- PERBAIKAN: Menggunakan relasi 'accountGroup' --}}
                                <div class="info-value">
                                    {{ $customer->accountGroup->name_account_group ?? $customer->account_group }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Customer Class</div>
                                {{-- PERBAIKAN: Menggunakan relasi 'customerClass' --}}
                                <div class="info-value">
                                    {{ $customer->customerClass->name_class ?? $customer->customer_class }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4 opacity-25">

                    <h5 class="section-title"><i class="fas fa-building"></i> General Information</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Customer Name</div>
                                <div class="info-value fs-7 fw-bold">{{ $customer->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Sort Name</div>
                                <div class="info-value">{{ $customer->sort_name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded">
                                <div class="info-label">Address</div>
                                <div class="info-value mb-2">{{ $customer->address1 }}</div>
                                <div class="info-value mb-2">{{ $customer->address2 }}</div>
                                <div class="info-value">{{ $customer->address3 }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">City</div>
                                <div class="info-value">{{ $customer->city }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Postal Code</div>
                                <div class="info-value">{{ $customer->postal_code }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Country</div>
                                <div class="info-value">{{ $customer->country }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="info-label">Email (General)</div>
                                <div class="info-value">{{ $customer->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-group">
                                <div class="info-label">Area</div>
                                <div class="info-value">{{ $customer->area }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-group">
                                <div class="info-label">Join Date</div>
                                <div class="info-value">{{ $customer->join_date ? \Carbon\Carbon::parse($customer->join_date)->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border bg-light-info bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-truck me-2"></i>Shipping Details</h6>
                                    <div class="info-group">
                                        <div class="info-label">Shipping To</div>
                                        <div class="info-value">{{ $customer->shipping_to_name }}</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Shipping Address</div>
                                        <div class="info-value">{{ $customer->shipping_to_address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border bg-light-success bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-file-invoice-dollar me-2"></i>Billing & Mail</h6>
                                    <div class="info-group">
                                        <div class="info-label">Billing Contact</div>
                                        <div class="info-value">{{ $customer->penagihan_nama_kontak }} ({{ $customer->penagihan_telepon }})</div>
                                    </div>
                                    <div class="info-group">
                                        <div class="info-label">Billing Address</div>
                                        <div class="info-value small">{{ $customer->penagihan_address }}</div>
                                    </div>
                                    <hr>
                                    <div class="info-group">
                                        <div class="info-label">Correspondence Address</div>
                                        <div class="info-value small">{{ $customer->surat_menyurat_address }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="section-title"><i class="fas fa-users-cog"></i> Key Personnel</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase small text-muted" style="width: 30%">Role</th>
                                    <th class="text-uppercase small text-muted">Name</th>
                                    <th class="text-uppercase small text-muted">Email / Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-primary">Purchasing Mgr</td>
                                    <td>{{ $customer->purchasing_manager_name }}</td>
                                    <td>{{ $customer->purchasing_manager_email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-primary">Finance Mgr</td>
                                    <td>{{ $customer->finance_manager_name }}</td>
                                    <td>{{ $customer->finance_manager_email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-primary">Tax Contact</td>
                                    <td>{{ $customer->tax_contact_name }}</td>
                                    <td>
                                        <div>{{ $customer->tax_contact_email }}</div>
                                        <small class="text-muted">{{ $customer->tax_contact_phone }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="section-title"><i class="fas fa-file-contract"></i> Tax & Legal Data</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted small fw-bold" width="30%">NPWP No.</td>
                                    <td class="fw-bold">{{ $customer->npwp }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold">NPWP Date</td>
                                    <td>{{ $customer->tanggal_npwp ? \Carbon\Carbon::parse($customer->tanggal_npwp)->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold">No. Pengukuhan</td>
                                    <td>{{ $customer->no_pengukuhan_kaber ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted small fw-bold" width="30%">NPPKP No.</td>
                                    <td class="fw-bold">{{ $customer->nppkp }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small fw-bold">NPPKP Date</td>
                                    <td>{{ $customer->tanggal_nppkp ? \Carbon\Carbon::parse($customer->tanggal_nppkp)->format('d M Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5 class="section-title"><i class="fas fa-coins"></i> Financial Terms</h5>

                    {{-- Gunakan g-3 untuk jarak antar kolom yang lebih rapat (compact) --}}
                    <div class="row g-3 mb-4">

                        {{-- 1. TERM OF PAYMENT (TOP) --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">Term of Payment</div>
                                @if($canAdjust)
                                    <select class="form-select form-select-sm fw-bold text-primary border-primary editable-field"
                                            name="update_top" id="left_top" form="approvalForm" disabled>
                                        <option value="7" {{ $customer->term_of_payment == '7' ? 'selected' : '' }}>Net 7 Days</option>
                                        <option value="14" {{ $customer->term_of_payment == '14' ? 'selected' : '' }}>Net 14 Days</option>
                                        <option value="30" {{ $customer->term_of_payment == '30' ? 'selected' : '' }}>Net 30 Days</option>
                                        <option value="45" {{ $customer->term_of_payment == '45' ? 'selected' : '' }}>Net 45 Days</option>
                                        <option value="60" {{ $customer->term_of_payment == '60' ? 'selected' : '' }}>Net 60 Days</option>
                                        <option value="CBD" {{ $customer->term_of_payment == 'CBD' ? 'selected' : '' }}>Cash Before Delivery (CBD)</option>
                                    </select>
                                @else
                                    <div class="info-value fw-bold text-dark">{{ $customer->term_of_payment }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- 2. LEAD TIME (Disamping TOP) --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">Lead Time (Days)</div>
                                @if($canAdjust)
                                    <input type="number" class="form-control form-control-sm fw-bold text-primary border-primary editable-field"
                                        name="update_lead_time" id="left_lead_time"
                                        value="{{ $customer->lead_time == 0 ? '' : $customer->lead_time }}"
                                        placeholder="0" form="approvalForm" disabled>
                                @else
                                    <div class="info-value fw-bold">{{ $customer->lead_time }} Days</div>
                                @endif
                            </div>
                        </div>

                        {{-- 3. CCAR (Ditaruh diatas sesuai request) --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">CCAR</div>
                                <div class="info-value">{{ $customer->ccar }}</div>
                            </div>
                        </div>

                        {{-- 4. CREDIT LIMIT --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">Credit Limit</div>

                                <input type="hidden" name="update_credit_limit_value" id="final_credit_limit_input"
                                    value="{{ $customer->credit_limit }}" form="approvalForm">

                                <div class="info-value fs-6 text-success fw-bold" id="display_credit_limit">
                                    IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}
                                </div>

                                <small id="calc-badge" class="badge bg-warning text-dark mt-1" style="display:none; font-size: 0.65rem;">
                                    <i class="fas fa-calculator me-1"></i> Updated
                                </small>
                            </div>
                        </div>

                        {{-- 5. OUTPUT TAX --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">Output Tax</div>
                                <div class="info-value">{{ $customer->output_tax }}</div>
                            </div>
                        </div>

                        {{-- 6. BANK GARANSI --}}
                        <div class="col-md-4">
                            <div class="p-2 border rounded bg-light bg-opacity-50 h-100">
                                <div class="info-label text-muted small fw-bold mb-1">Bank Garansi</div>
                                <div class="info-value">{{ $customer->bank_garansi }}</div>
                            </div>
                        </div>
                    </div>

                    <h5 class="section-title"><i class="fas fa-calculator"></i> Credit Limit Calculation Items</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-sm table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody id="calculation-items">
                                @php $totalAmount = 0; @endphp
                                @foreach(\App\Models\Customer\CustomerItem::where('customer_id', $customer->id)->get() as $item)
                                    @php
                                        $subtotal = $item->quantity * $item->price;
                                        $totalAmount += $subtotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-end item-qty">{{ number_format($item->quantity, 0) }}</td>
                                        <td class="text-end item-price">{{ number_format($item->price, 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($subtotal, 0) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Base Amount</td>
                                    <td class="text-end fw-bold text-primary" id="base-total-amount" data-value="{{ $totalAmount }}">
                                        IDR {{ number_format($totalAmount, 0) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="section-title"><i class="fas fa-paperclip"></i> Supporting Documents</h5>
                    <div class="row g-3 mb-4">
                        @php
                            // Ambil data file (asumsi relasi 'files' adalah HasMany, ambil yang pertama)
                            $doc = $customer->files ? $customer->files->first() : null;
                        @endphp

                        {{-- 1. NPWP FILE --}}
                        <div class="col-md-4">
                            <div class="card h-100 border bg-light">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-file-invoice text-primary fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-3">NPWP Document</h6>
                                    
                                    @if($doc && $doc->npwp_file)
                                        <a href="{{ asset('storage/' . $doc->npwp_file) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="fas fa-times me-1"></i> Not Uploaded
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 2. NIB / SIUP FILE --}}
                        <div class="col-md-4">
                            <div class="card h-100 border bg-light">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-file-contract text-success fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-3">NIB / SIUP</h6>
                                    
                                    @if($doc && $doc->nib_siup_file)
                                        <a href="{{ asset('storage/' . $doc->nib_siup_file) }}" target="_blank" class="btn btn-sm btn-outline-success w-100">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="fas fa-times me-1"></i> Not Uploaded
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 3. KTP FILE --}}
                        <div class="col-md-4">
                            <div class="card h-100 border bg-light">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2">
                                        <i class="fas fa-id-card text-info fs-2"></i>
                                    </div>
                                    <h6 class="fw-bold small text-muted text-uppercase mb-3">KTP Penanggung Jawab</h6>
                                    
                                    @if($doc && $doc->ktp_file)
                                        <a href="{{ asset('storage/' . $doc->ktp_file) }}" target="_blank" class="btn btn-sm btn-outline-info w-100">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </a>
                                    @else
                                        <button disabled class="btn btn-sm btn-outline-secondary w-100">
                                            <i class="fas fa-times me-1"></i> Not Uploaded
                                        </button>
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

                            {{-- OPSI 1: REVIEW / INPUT CUSTOMER CODE --}}
                            {{-- Logic: Jika IT, label berubah & otomatis checked --}}
                            <div class="form-check p-3 border rounded mb-2 {{ $isIT ? 'bg-warning bg-opacity-10 border-warning' : 'bg-light-info bg-opacity-10' }}">
                                <input class="form-check-input" type="radio" name="action" id="action_review" value="review"
                                    {{ $preSelectedAction == 'review' || $isIT ? 'checked' : '' }}>
                                
                                <label class="form-check-label fw-bold {{ $isIT ? 'text-dark' : 'text-primary' }} d-block" for="action_review">
                                    @if($isIT)
                                        <i class="fas fa-keyboard me-2"></i> Input Customer Code & Approve
                                    @else
                                        <i class="fas fa-edit me-2"></i> Review with Notes
                                    @endif
                                </label>
                                
                                <small class="text-muted ms-4">
                                    @if($isIT)
                                        Masukkan Kode Customer dan Tanggal Join untuk mengaktifkan customer.
                                    @else
                                        Setujui dengan catatan.
                                        @if($canAdjust)
                                            <br><span class="badge bg-primary mt-1"><i class="fas fa-lock-open me-1"></i> Unlocks Data Editing</span>
                                        @endif
                                    @endif
                                </small>
                            </div>

                            {{-- OPSI 2: REJECT --}}
                            {{-- Logic: Disembunyikan jika role IT --}}
                            @if(!$isIT)
                            <div class="form-check p-3 border rounded mb-4 bg-light-danger bg-opacity-10" style="border-color: #fecaca !important; background-color: #fef2f2;">
                                <input class="form-check-input" type="radio" name="action" id="action_reject" value="reject"
                                    {{ $preSelectedAction == 'reject' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-danger d-block" for="action_reject">
                                    <i class="fas fa-times-circle me-2"></i> Reject Request
                                </label>
                                <small class="text-muted ms-4">
                                    Tolak permintaan customer.
                                </small>
                            </div>
                            @endif
                        </div>

                        {{-- NOTES CONTAINER --}}
                        <div class="mb-4" id="notes-container">
                            <label for="notes" class="form-label fw-bold small text-uppercase text-muted">Notes / Reason</label>
                            <textarea class="form-control" name="notes" id="notes" rows="4" 
                                placeholder="{{ $isIT ? 'Catatan tambahan (Opsional)...' : 'Enter your notes here...' }}"></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-submit btn-lg text-white shadow-sm" id="btn-submit">
                                Submit Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <div id="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
        <h5 class="mt-3 fw-bold text-primary">Processing Approval...</h5>
        <p class="text-muted">Please wait, do not close this window.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('approvalForm');
            const btnSubmit = document.getElementById('btn-submit');
            const notesField = document.getElementById('notes');
            const radios = document.querySelectorAll('input[name="action"]');
            const loadingOverlay = document.getElementById('loading-overlay');

            // --- PERMISSION VARIABLES DARI PHP ---
            const canAdjust = @json($canAdjust);
            const isITMode = document.getElementById('it_code') !== null; // Deteksi mode IT

            // Elemen Input Finance (Kiri)
            const inputTop = document.getElementById('left_top');
            const inputLead = document.getElementById('left_lead_time');
            const editableFields = document.querySelectorAll('.editable-field');

            // Elemen IT
            const itCodeInput = document.getElementById('it_code');
            const itJoinInput = document.getElementById('it_join_date');

            // Elemen Display Kalkulasi
            const displayLimitDiv = document.getElementById('display_credit_limit');
            const finalLimitInput = document.getElementById('final_credit_limit_input');
            const calcBadge = document.getElementById('calc-badge');
            const baseAmount = parseFloat(document.getElementById('base-total-amount')?.getAttribute('data-value') || 0);

            const preSelectedAction = "{{ $preSelectedAction }}";

            // 1. AUTO SUBMIT LOGIC
            if (preSelectedAction === 'approve') {
                // Jangan auto submit jika IT (karena harus isi data)
                if (!isITMode) {
                    loadingOverlay.style.display = 'flex';
                    setTimeout(() => { form.submit(); }, 500);
                    return;
                }
            }

            // 2. UPDATE UI FUNCTION
            function updateUI() {
                const selected = document.querySelector('input[name="action"]:checked')?.value;

                // Reset Button Defaults
                btnSubmit.className = 'btn btn-lg w-100 btn-submit text-white shadow-sm';
                btnSubmit.disabled = false;
                
                if (notesField) {
                    notesField.required = false;
                    notesField.placeholder = "Notes (Optional)...";
                }

                // --- LOGIC REVIEW / IT INPUT ---
                if (selected === 'review') {
                    
                    // A. JIKA IT ROLE
                    if (isITMode) {
                        btnSubmit.classList.add('btn-success'); // Warna hijau
                        btnSubmit.innerHTML = '<i class="fas fa-check-circle me-2"></i> Save Code & Activate';
                        
                        if(notesField) {
                            notesField.placeholder = "Catatan tambahan untuk requester (Opsional)...";
                        }
                    }
                    // B. JIKA FINANCE/HEAD: Enable Input & Calculator
                    else if (canAdjust) {
                        editableFields.forEach(el => {
                            el.disabled = false;
                            el.classList.add('bg-white');
                        });
                        calculateLimit();
                        
                        if (notesField) {
                            notesField.required = true;
                            notesField.placeholder = "Tuliskan catatan perbaikan (Wajib)...";
                        }
                        btnSubmit.classList.add('btn-info');
                        btnSubmit.innerHTML = '<i class="fas fa-edit me-2"></i> Submit Review';
                    }
                    // C. JIKA BUKAN FINANCE & BUKAN IT
                    else {
                        editableFields.forEach(el => {
                            el.disabled = true;
                            el.classList.remove('bg-white');
                        });
                        
                        if (notesField) {
                            notesField.required = true;
                            notesField.placeholder = "Tuliskan catatan (Wajib)...";
                        }
                        btnSubmit.classList.add('btn-primary');
                        btnSubmit.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Review';
                    }

                } else {
                    // --- LOGIC REJECT (Hanya muncul untuk Non-IT) ---
                    editableFields.forEach(el => {
                        el.disabled = true;
                        el.classList.remove('bg-white');
                    });

                    if (selected === 'reject') {
                        if (notesField) {
                            notesField.required = true;
                            notesField.placeholder = "Jelaskan alasan penolakan (Wajib)...";
                        }
                        btnSubmit.classList.remove('btn-submit');
                        btnSubmit.classList.add('btn-danger');
                        btnSubmit.innerHTML = '<i class="fas fa-times-circle me-2"></i> Reject Request';
                    }
                }
            }

            // 3. CALCULATOR LOGIC (Hanya jalan jika canAdjust = true)
            function calculateLimit() {
                if (!canAdjust || !inputTop || !inputLead) return;

                const topStr = inputTop.value;
                const lt = parseFloat(inputLead.value) || 0;
                const qtyXHarga = baseAmount;

                let topDays = 0;
                let divider = 30;

                if (topStr.includes('7')) { topDays = 7; divider = 7.5; }
                else if (topStr.includes('14')) { topDays = 14; divider = 15; }
                else if (topStr.includes('30')) { topDays = 30; divider = 30; }
                else if (topStr.includes('45')) { topDays = 45; divider = 45; }
                else { topDays = parseInt(topStr) || 0; divider = topDays > 0 ? topDays : 30; }

                const result = ((topDays + lt) * qtyXHarga) / divider;

                if(displayLimitDiv) {
                    displayLimitDiv.innerText = 'IDR ' + new Intl.NumberFormat('id-ID').format(Math.round(result));
                    displayLimitDiv.classList.add('text-primary');
                }
                if(finalLimitInput) finalLimitInput.value = Math.round(result);
                if(calcBadge) calcBadge.style.display = 'inline-block';
            }

            // Listeners
            radios.forEach(radio => radio.addEventListener('change', updateUI));

            if (canAdjust && inputTop && inputLead) {
                inputTop.addEventListener('change', calculateLimit);
                inputLead.addEventListener('input', calculateLimit);
            }

            if (isITMode) {
                itCodeInput.addEventListener('input', function() { this.classList.remove('is-invalid'); });
                itJoinInput.addEventListener('input', function() { this.classList.remove('is-invalid'); });
            }

            updateUI(); // Init

            // 4. SUBMIT HANDLER
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Stop default submit

                const selected = document.querySelector('input[name="action"]:checked').value;
                const notesVal = notesField ? notesField.value.trim() : '';

                // --- VALIDASI 1: NOTES (Review & Reject Wajib) ---
                if ((selected === 'review' || selected === 'reject') && (!notesVal || !/[a-zA-Z]/.test(notesVal))) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Notes Required',
                        text: 'Mohon isi catatan dengan jelas (harus mengandung huruf).',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // --- VALIDASI 2: IT FIELDS ---
                if (isITMode && selected !== 'reject') {
                    if (!itCodeInput.value.trim() || !itJoinInput.value) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Incomplete',
                            text: 'Sebagai IT, Anda wajib mengisi Customer Code dan Join Date.'
                        });
                        if(!itCodeInput.value) itCodeInput.classList.add('is-invalid');
                        if(!itJoinInput.value) itJoinInput.classList.add('is-invalid');
                        return;
                    }
                }

                // --- KONFIGURASI ALERT ---
                let title = '';
                let text = '';
                let icon = '';
                let confirmBtnColor = '';
                let confirmBtnText = '';

                if (selected === 'reject') {
                    title = 'Confirm Rejection?';
                    text = 'Tindakan ini akan menolak pengajuan Customer. Apakah Anda yakin?';
                    icon = 'warning';
                    confirmBtnColor = '#d33';
                    confirmBtnText = 'Yes, Reject it!';
                } else {
                    // Review / Approve
                    title = 'Confirm Submission?';
                    confirmBtnText = 'Yes, Submit!';
                    confirmBtnColor = '#3085d6';
                    icon = 'question';

                    if (canAdjust) {
                        const newLimit = displayLimitDiv.innerText;
                        text = `Data akan disetujui dengan Credit Limit: ${newLimit}`;
                    } else if (isITMode) {
                        text = `Customer akan diaktifkan dengan Kode: ${itCodeInput.value}`;
                    } else {
                        text = 'Apakah Anda yakin ingin mengirim keputusan ini?';
                    }
                }

                // --- EKSEKUSI SWEETALERT ---
                Swal.fire({
                    title: title,
                    text: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmBtnColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmBtnText,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan Loading
                        loadingOverlay.style.display = 'flex';
                        btnSubmit.disabled = true;

                        // PENTING: Enable field finance agar value terkirim ke controller
                        if (selected === 'review' && canAdjust) {
                            editableFields.forEach(el => el.disabled = false);
                        }

                        // Submit Form secara manual setelah delay sedikit (opsional)
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
