<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} : {{ $requisition->no_srs }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .05);
        }

        /* [DIKEMBALIKAN] Warna Coklat Emas untuk Header */
        .card-header.main-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
        }

        /* [DIKEMBALIKAN] Warna Coklat Emas untuk Judul Section */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #b8871a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eef2f9;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .info-label {
            color: #8a96a3;
            font-size: .85em;
            margin-bottom: 2px;
        }

        .info-value {
            color: #212529;
            font-weight: 500;
        }

        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, .9);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef;
            opacity: 1;
        }
        
        /* [DIKEMBALIKAN] Palet Warna Coklat Emas */
        .text-primary {
            color: #b8871a !important;
        }

        .btn-primary {
            background-color: #cc982f;
            border-color: #cc982f;
        }

        .btn-primary:hover {
            background-color: #b8871a;
            border-color: #b8871a;
        }

        .processing-overlay .spinner-border {
            color: #cc982f !important;
        }

        .main-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 30px;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .action-card {
            position: sticky;
            top: 40px;
        }

        .left-column>.card {
            margin-bottom: 30px;
        }

        .radio-group-horizontal .form-check {
            margin-right: 15px;
        }

        @media (max-width:1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

<body>
    <div class="main-container">
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <h4 class="mb-0">Free Goods Requisition Approval</h4>
                    <p class="mb-0 opacity-75">FG No: {{ $requisition->no_srs }}</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">Request Date</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Requester</div>
                            <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Department</div>
                            <div class="info-value">{{ $requisition->requester->department->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Customer Name</div>
                            <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Cost Center</div>
                            <div class="info-value">{{ $requisition->cost_center ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Objectives</div>
                            <div class="info-value">{{ $requisition->objectives }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Estimated Potential</div>
                            <div class="info-value">{{ $requisition->estimated_potential }}</div>
                        </div>
                    </div>

                    @if($requisition->requisitionItems->count() > 0)
                    <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requested Item List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Unit</th>
                                    <th class="text-center">Qty Required</th>
                                    <th class="text-center">Qty Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requisition->requisitionItems as $item)
                                <tr>
                                    <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity_required }}</td>
                                    <td class="text-center">{{ $item->quantity_issued ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    <h5 class="section-title"><i class="fas fa-edit"></i> {{ $pageTitle }}</h5>
                    <form id="responseForm" action="{{ route('fg.approval.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        @if($isWarehouseProcess)
                            <input type="hidden" name="action" value="submit"> 
                            <p>Please provide notes for this warehouse step. Notes are required to proceed.</p>
                            <div class="mb-3">
                                <label for="notes" class="form-label"><strong>Notes/Reason: <span class="text-danger">*</span></strong></label>
                                <textarea class="form-control" id="notes" name="notes" rows="10" placeholder="Provide notes for your action..." required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Warehouse Process</button>
                            </div>

                        @else
                        <div class="mb-3">
                            <label class="form-label"><strong>Decision:</strong></label>
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="action" id="action_review"
                                        value="review" @if($action === 'review' && $originalAction !== 'reject') checked @endif>
                                    <label class="form-check-label text-primary" for="action_review"><strong>
                                        Approve with Review</strong></label>
                                </div>
                                <div class="form-check me-3 mb-1">
                                    <input class="form-check-input" type="radio" name="action" id="action_reject"
                                        value="reject" @if($originalAction === 'reject') checked @endif>
                                    <label class="form-check-label text-danger"
                                        for="action_reject"><strong>Reject</strong></label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label"><strong>Notes/Reason:</strong></label>
                            <textarea class="form-control" id="notes" name="notes" rows="8"
                                placeholder="Provide notes for your decision..."></textarea>
                            <div class="form-text">Notes are required for rejection or review.</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit Decision</button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Logika validasi dan fungsionalitas lain tetap dipertahankan
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('responseForm');
            const overlay = document.getElementById('processingOverlay');
            
            const actionFromUrl = '{{ $action }}'; 
            const isWarehouseProcess = {{ $isWarehouseProcess ? 'true' : 'false' }};

            const handleFormSubmit = () => {
                let validationPassed = true;

                if (!isWarehouseProcess) {
                    const selectedAction = document.querySelector('input[name="action"]:checked');
                    const notesTextarea = document.getElementById('notes');
                    
                    if (selectedAction) {
                        const actionValue = selectedAction.value;
                        if (actionValue === 'review' || actionValue === 'reject') {
                            const notesValue = notesTextarea.value.trim();
                            const hasAlphanumeric = /[a-zA-Z0-9]/.test(notesValue);

                            if (notesValue.length < 10 || !hasAlphanumeric) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Alasan Diperlukan',
                                    text: 'Mohon berikan alasan yang valid. Alasan wajib diisi, minimal 10 karakter, dan harus berisi huruf atau angka (tidak boleh hanya spasi atau simbol).'
                                });
                                validationPassed = false;
                            }
                        }
                    }
                } else if (isWarehouseProcess && actionFromUrl !== 'approve') {
                    const notesTextarea = document.getElementById('notes');
                    if (!(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon berikan catatan yang valid.' });
                        validationPassed = false;
                    }
                }

                if (validationPassed) {
                    if (actionFromUrl === 'approve') {
                        document.getElementById('action_review').value = 'approve';
                    }
                    
                    overlay.style.display = 'flex';
                    form.submit();
                }
            };

            if (actionFromUrl === 'approve' && !isWarehouseProcess) {
                document.getElementById('action_review').value = 'approve'; 
                handleFormSubmit();
                document.body.style.display = 'none'; 
                return;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                
                Swal.fire({
                    title: 'Konfirmasi Pengiriman',
                    text: "Apakah Anda yakin ingin melanjutkan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        handleFormSubmit();
                    }
                });
            });

            if (!isWarehouseProcess) {
                const reviewRadio = document.getElementById('action_review');
                const rejectRadio = document.getElementById('action_reject');
                const submitBtn = document.getElementById('submitBtn');
                
                const updateSubmitButton = () => {
                    if (reviewRadio.checked) {
                        submitBtn.textContent = 'Submit Approve';
                        submitBtn.classList.remove('btn-danger'); submitBtn.classList.add('btn-primary');
                    } else if (rejectRadio.checked) {
                        submitBtn.textContent = 'Submit Reject';
                        submitBtn.classList.remove('btn-primary'); submitBtn.classList.add('btn-danger');
                    }
                };
                
                reviewRadio.addEventListener('change', updateSubmitButton);
                rejectRadio.addEventListener('change', updateSubmitButton);
                
                updateSubmitButton();
            }
        });
    </script>
</body>

</html>