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

        /* Layout Grid */
        .main-container { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; max-width: 1400px; margin: 40px auto; padding: 0 20px; }

        /* Card Styles */
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); background: white; margin-bottom: 24px; }
        .card-header.main-header { background: linear-gradient(to right, #1e3a8a, #2563eb); color: white; padding: 20px 30px; border-radius: 12px 12px 0 0 !important; }
        .sub-header { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 15px 25px; border-radius: 12px 12px 0 0; }

        /* Section Styles */
        .section-title { font-size: 0.95rem; font-weight: 700; color: #1e3a8a; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; letter-spacing: 0.5px; text-transform: uppercase; }
        .section-title i { margin-right: 10px; color: #3b82f6; }

        /* Data Display */
        .info-group { margin-bottom: 15px; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 0.95rem; color: #0f172a; font-weight: 500; word-break: break-word; }

        /* Specific Badges */
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .bg-light-info { background-color: #e0f2fe; color: #0284c7; }
        .bg-light-success { background-color: #dcfce7; color: #16a34a; }

        /* Sticky Action Card */
        .action-card { position: sticky; top: 30px; border-top: 5px solid #1e3a8a; }

        /* Radio Buttons Custom */
        .form-check-input:checked { background-color: #1e3a8a; border-color: #1e3a8a; }
        .btn-submit { background-color: #1e3a8a; border: none; padding: 12px; font-weight: 600; letter-spacing: 0.5px; }
        .btn-submit:hover { background-color: #1e40af; }

        @media (max-width: 992px) {
            .main-container { grid-template-columns: 1fr; }
            .action-card { position: static; }
        }
    </style>
</head>
<body>
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
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Term of Payment</div>
                                <div class="info-value badge bg-info text-dark">{{ $customer->term_of_payment }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Credit Limit</div>
                                <div class="info-value fs-5 text-success fw-bold">IDR {{ number_format($customer->credit_limit, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Output Tax</div>
                                <div class="info-value">{{ $customer->output_tax }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Bank Garansi</div>
                                <div class="info-value">{{ $customer->bank_garansi }}</div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">CCAR</div>
                                <div class="info-value">{{ $customer->ccar }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-group">
                                <div class="info-label">Lead Time</div>
                                <div class="info-value">{{ $customer->lead_time }} Days</div>
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
                            <label class="form-label fw-bold mb-3 text-dark">Choose Action:</label>

                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input" type="radio" name="action" id="action_review" value="review">
                                <label class="form-check-label fw-bold text-primary d-block" for="action_review">
                                    <i class="fas fa-edit me-2"></i> Review with Notes
                                </label>
                                <small class="text-muted ms-4">Setujui tapi dengan catatan perbaikan.</small>
                            </div>

                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input" type="radio" name="action" id="action_reject" value="reject">
                                <label class="form-check-label fw-bold text-danger d-block" for="action_reject">
                                    <i class="fas fa-times-circle me-2"></i> Reject Request
                                </label>
                                <small class="text-muted ms-4">Tolak permintaan ini.</small>
                            </div>
                        </div>

                        <div class="mb-4" id="notes-container">
                            <label for="notes" class="form-label fw-bold">Notes / Reason <span class="text-danger" id="req-star" style="display:none">*</span></label>
                            <textarea class="form-control bg-white" id="notes" name="notes" rows="5" placeholder="Tuliskan catatan atau alasan penolakan di sini..."></textarea>
                            <div class="form-text">Notes wajib diisi jika memilih Review atau Reject.</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-submit btn-lg text-white shadow-sm" id="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i> Submit Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('approvalForm');
            const notesContainer = document.getElementById('notes-container');
            const notesField = document.getElementById('notes');
            const reqStar = document.getElementById('req-star');
            const btnSubmit = document.getElementById('btn-submit');

            const radios = document.querySelectorAll('input[name="action"]');

            function updateUI() {
                const selected = document.querySelector('input[name="action"]:checked').value;
                btnSubmit.className = 'btn btn-lg w-100 btn-submit text-white shadow-sm';

                if (selected === 'approve') {
                    reqStar.style.display = 'none';
                    notesField.required = false;
                    notesField.placeholder = "Catatan opsional...";
                    btnSubmit.classList.add('btn-primary');
                    btnSubmit.innerHTML = '<i class="fas fa-check-circle me-2"></i> Approve Now';
                } else if (selected === 'review') {
                    reqStar.style.display = 'inline';
                    notesField.required = true;
                    notesField.placeholder = "Tuliskan catatan perbaikan untuk user...";
                    btnSubmit.classList.add('btn-info');
                    btnSubmit.innerHTML = '<i class="fas fa-edit me-2"></i> Submit Review';
                } else if (selected === 'reject') {
                    reqStar.style.display = 'inline';
                    notesField.required = true;
                    notesField.placeholder = "Jelaskan alasan penolakan...";
                    btnSubmit.classList.remove('btn-submit');
                    btnSubmit.classList.add('btn-danger');
                    btnSubmit.innerHTML = '<i class="fas fa-times-circle me-2"></i> Reject Request';
                }
            }

            radios.forEach(radio => radio.addEventListener('change', updateUI));
            updateUI();

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const selected = document.querySelector('input[name="action"]:checked').value;
                if ((selected === 'review' || selected === 'reject') && !notesField.value.trim()) {
                    Swal.fire('Perhatian', 'Notes wajib diisi untuk opsi Review atau Reject!', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah Anda yakin ingin mengirim keputusan ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#1e3a8a',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>
</html>
