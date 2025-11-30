<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Approval Review - {{ config('app.name') }}</title>
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

        .card-header.main-header {
            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 16px 16px 0 0 !important;
        }

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

        /* Layout Grid 2 Kolom yang konsisten */
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

        .status-badge {
            font-size: 0.9em;
            padding: 0.5em 0.7em;
            font-weight: 700;
        }

        .level-badge {
            background: linear-gradient(45deg, #6f42c1, #e83e8c);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Responsive table wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .table-responsive table {
            min-width: 600px;
        }

        @media (max-width:1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }

            .action-card {
                position: relative;
                top: 0;
            }
        }

        @media (max-width:768px) {
            .main-container {
                margin: 20px auto;
                padding: 0 10px;
                gap: 20px;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            .card-header.main-header {
                padding: 15px 20px;
            }

            .card-header.main-header h4 {
                font-size: 1.1rem;
            }

            .card-header.main-header p {
                font-size: 0.9rem;
            }

            .section-title {
                font-size: 1rem;
            }

            .info-label {
                font-size: 0.8em;
            }

            .info-value {
                font-size: 0.9em;
            }

            .table-responsive {
                margin-left: -1.5rem;
                margin-right: -1.5rem;
                padding: 0 1.5rem;
            }

            .table-responsive table {
                font-size: 0.85rem;
            }

            .table-responsive th,
            .table-responsive td {
                padding: 0.5rem !important;
                white-space: nowrap;
            }

            .status-badge {
                font-size: 0.8em;
                padding: 0.4em 0.6em;
            }
        }

        @media (max-width:480px) {
            .main-container {
                padding: 0 5px;
            }

            .card {
                border-radius: 12px;
            }

            .card-header.main-header {
                border-radius: 12px 12px 0 0 !important;
                padding: 12px 15px;
            }

            .card-header.main-header h4 {
                font-size: 1rem;
            }

            .card-header.main-header p {
                font-size: 0.85rem;
            }

            .section-title {
                font-size: 0.95rem;
            }

            .table-responsive {
                margin-left: -1rem;
                margin-right: -1rem;
                padding: 0 1rem;
            }

            .table-responsive table {
                font-size: 0.8rem;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .btn-lg {
                padding: 0.75rem 1rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <h4 class="mb-0">
                        <i class="fas fa-warehouse me-2"></i>
                        Warehouse Approval Review
                    </h4>
                    <p class="mb-0 opacity-75">
                        {{ $tracking->current_position }} - Level
                        @if($tracking->current_position == 'WH Supervisor First')
                            1
                        @elseif($tracking->current_position == 'Material Supervisor') 
                            2
                        @elseif($tracking->current_position == 'WH Supervisor Final')
                            3
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="card-body p-4 p-md-5">
                    {{-- BAGIAN 1: DETAIL REQUISITION --}}
                    <h5 class="section-title"><i class="fas fa-file-invoice"></i> Requisition Details</h5>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="info-label">SRS Number</div>
                            <div class="info-value">{{ $requisition->no_srs }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Request Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($requisition->request_date)->format('d F Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Requester</div>
                            <div class="info-value">{{ $requisition->requester->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Customer Name</div>
                            <div class="info-value">{{ $requisition->customer->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="info-label">Customer Address</div>
                            <div class="info-value">{{ $requisition->customer->address ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Account</div>
                            <div class="info-value">{{ $requisition->account ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Cost Center</div>
                            <div class="info-value">{{ $requisition->cost_center ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Category</div>
                            <div class="info-value">
                                <span class="badge status-badge bg-info">{{ $requisition->category }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-label">Print Batch</div>
                            <div class="info-value">
                                @if($requisition->print_batch)
                                    <span class="badge status-badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Yes
                                    </span>
                                @else
                                    <span class="badge status-badge bg-secondary">
                                        <i class="fas fa-times-circle me-1"></i>No
                                    </span>
                                @endif
                            </div>
                        </div>
                        @if($requisition->objectives)
                        <div class="col-md-12">
                            <div class="info-label">Objectives</div>
                            <div class="info-value">{{ $requisition->objectives }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- BAGIAN 2: DETAIL ITEM --}}
                    @if($requisition->requisitionItems && $requisition->requisitionItems->count() > 0)
                    <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requisition Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>Batch - Remark</th>
                                    <th class="text-center">QTY Required</th>
                                    <th class="text-center">QTY Issued</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requisition->requisitionItems as $item)
                                <tr>
                                    <td>{{ $item->itemMaster->item_master_code ?? 'N/A' }}</td>
                                    <td>{{ $item->itemMaster->item_master_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($item->batch_number)
                                            {{ \Carbon\Carbon::parse($item->batch_number)->format('d/m/y') }}
                                        @else
                                            N/A
                                        @endif
                                        @if($item->remarks)
                                            - {{ $item->remarks }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge status-badge bg-primary">{{ $item->quantity_required ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge status-badge bg-success">{{ $item->quantity_issued ?? 0 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No items found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- BAGIAN 3: CURRENT STATUS --}}
                    <h5 class="section-title mt-5"><i class="fas fa-info-circle"></i> Current Status</h5>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($requisition->status == 'Pending')
                                <span class="badge status-badge bg-warning text-dark">Pending</span>
                                @elseif($requisition->status == 'In Progress')
                                <span class="badge status-badge bg-info text-white">In Progress</span>
                                @elseif($requisition->status == 'Approved')
                                <span class="badge status-badge bg-success">Approved</span>
                                @elseif($requisition->status == 'Rejected')
                                <span class="badge status-badge bg-danger">Rejected</span>
                                @else
                                <span class="badge status-badge bg-secondary">{{ $requisition->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Aksi Warehouse --}}
        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    <h5 class="section-title">
                        <i class="fas fa-clipboard-check"></i> 
                        Warehouse Approval - {{ $tracking->current_position }}
                    </h5>
                    <form action="{{ route('complain.warehouse.process') }}" method="POST" id="warehouseApprovalForm">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="id" value="{{ $requisition->id }}">

                        <!-- Decision -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Decision:</label>
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status" id="approve"
                                        value="approve" required>
                                    <label class="form-check-label text-success" for="approve">
                                        <strong>Approve</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Notes/Reason -->
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Notes/Comments: <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notes" name="notes" rows="8"
                                placeholder="Enter your notes or comments here..." required></textarea>
                            <div class="form-text text-danger" id="notes-help-text">Notes are required for your decision.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Submit Decision
                            </button>
                        </div>
                        
                        <div class="d-grid mt-2">
                            <a href="#" class="btn btn-secondary" onclick="window.close();">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner-border" role="status"></div>
        <p class="mt-3">Processing your response...</p>
    </div>

    <!-- Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('warehouseApprovalForm');
            const overlay = document.getElementById('processingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            const approveRadio = document.getElementById('approve');
            const notesTextarea = document.getElementById('notes');
            const notesHelpText = document.getElementById('notes-help-text');

            // --- FUNGSI VALIDASI ---
            const validateForm = () => {
                const notesTextarea = document.getElementById('notes');
                const notesValue = notesTextarea.value.trim();
                
                // Cek apakah notes kosong atau hanya berisi spasi
                if (!notesValue || notesValue.length === 0) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Notes Required', 
                        text: 'Please provide notes for your decision.' 
                    });
                    return false;
                }
                
                // Cek apakah notes mengandung setidaknya satu huruf (bukan hanya angka/simbol/spasi)
                if (!/[a-zA-Z]/.test(notesValue)) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Invalid Notes', 
                        text: 'Please provide valid notes with at least some text.' 
                    });
                    return false;
                }
                
                return true; // Jika semua validasi lolos
            };

            // Update form behavior based on selected decision
            function updateFormBehavior() {
                if (approveRadio.checked) {
                    notesTextarea.required = true;
                    notesHelpText.textContent = 'Please provide notes for your approval (required).';
                    notesHelpText.className = 'form-text text-warning';
                    submitBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Submit Approval';
                    submitBtn.className = 'btn btn-success btn-lg';
                } else {
                    notesTextarea.required = true;
                    notesHelpText.textContent = 'Please provide notes for your decision (required).';
                    notesHelpText.className = 'form-text text-danger';
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Decision';
                    submitBtn.className = 'btn btn-primary btn-lg';
                }
            }

            // Add event listeners
            approveRadio.addEventListener('change', updateFormBehavior);

            // --- LOGIKA SUBMIT MANUAL ---
            if (form && submitBtn) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault(); // Selalu hentikan submit default terlebih dahulu

                    // Jika validasi gagal, hentikan proses
                    if (!validateForm()) {
                        return;
                    }

                    // Simpan teks asli tombol
                    const originalBtnText = submitBtn.innerHTML;

                    // Langsung nonaktifkan tombol dan tampilkan status loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

                    const formData = new FormData(form);
                    const decision = formData.get('status');
                    const notes = formData.get('notes');

                    // Confirmation dialog
                    const title = decision === 'approve' ? 'Confirm Warehouse Approval' : 'Confirm Warehouse Rejection';
                    const text = decision === 'approve'
                        ? 'Are you sure you want to approve this warehouse requisition?'
                        : 'Are you sure you want to reject this warehouse requisition?';
                    const confirmButtonColor = decision === 'approve' ? '#28a745' : '#dc3545';

                    Swal.fire({
                        title: title,
                        text: text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: decision === 'approve' ? 'Yes, Approve' : 'Yes, Reject',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, tampilkan overlay dan submit form
                            overlay.style.display = 'flex';
                            
                            // Submit form via AJAX
                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                // Check if response is ok
                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }

                                // Check if response is JSON
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    // If response is not JSON (likely a redirect or HTML), treat as success
                                    return { success: true, message: 'Warehouse approval has been processed successfully.' };
                                }
                            })
                            .then(data => {
                                overlay.style.display = 'none';
                                
                                // Always treat as success if we reach this point
                                const successMessage = data.message || 'Warehouse approval has been processed successfully.';
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: successMessage,
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    // Close window after success
                                    if (window.opener) {
                                        window.close();
                                    } else {
                                        // Show closing message immediately
                                        document.body.innerHTML = `
                                        <div style="
                                            display: flex;
                                            justify-content: center;
                                            align-items: center;
                                            height: 100vh;
                                            background: linear-gradient(135deg, #cc982f 0%, #b8871a 100%);
                                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                            margin: 0;
                                            color: white;
                                        ">
                                            <div style="
                                                text-align: center;
                                                background: rgba(255,255,255,0.1);
                                                padding: 50px 40px;
                                                border-radius: 20px;
                                                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                                                backdrop-filter: blur(10px);
                                                border: 1px solid rgba(255,255,255,0.2);
                                                max-width: 500px;
                                                animation: fadeIn 0.5s ease-out;
                                            ">
                                                <div style="font-size: 4em; margin-bottom: 20px; animation: bounce 1s ease-out;">✅</div>
                                                <h1 style="margin: 0 0 20px 0; font-size: 2.5em; font-weight: 300;">Warehouse Approval Completed</h1>
                                                <p style="margin: 0 0 30px 0; font-size: 1.2em; opacity: 0.9; line-height: 1.6;">
                                                    ${successMessage}<br>
                                                    <span>You can safely close this browser tab</span>
                                                </p>
                                            </div>
                                        </div>
                                        <style>
                                            @keyframes fadeIn {
                                                from { opacity: 0; transform: scale(0.8); }
                                                to { opacity: 1; transform: scale(1); }
                                            }
                                            @keyframes bounce {
                                                0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                                                40% { transform: translateY(-10px); }
                                                60% { transform: translateY(-5px); }
                                            }
                                        </style>
                                    `;
                                        document.title = '✅ Warehouse Approval Completed - Page Closed';
                                    }
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                overlay.style.display = 'none';
                                
                                // Re-enable button first
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                                
                                let errorMessage = 'An error occurred while processing your request. Please try again.';

                                // Handle different types of errors
                                if (error.message && error.message.includes('HTTP error! status:')) {
                                    // Server returned an error status
                                    const statusCode = error.message.match(/\d+/);
                                    if (statusCode) {
                                        if (statusCode[0] === '404') {
                                            errorMessage = 'The approval link has expired or is invalid.';
                                        } else if (statusCode[0] === '500') {
                                            errorMessage = 'A server error occurred. Please contact administrator.';
                                        } else {
                                            errorMessage = `Server error (${statusCode[0]}). Please try again later.`;
                                        }
                                    }
                                } else if (error.message && error.message === 'Failed to fetch') {
                                    // Network error
                                    errorMessage = 'Network connection error. Please check your internet connection and try again.';
                                } else if (error.message && error.message !== 'Failed to fetch') {
                                    // Other specific errors
                                    errorMessage = error.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    showCancelButton: true,
                                    confirmButtonText: 'Try Submit Again',
                                    cancelButtonText: 'Use Standard Submit',
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#6c757d'
                                }).then((result) => {
                                    if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                                        // User chose standard submit
                                        overlay.style.display = 'flex';
                                        form.submit(); // Standard form submission
                                    }
                                    // If user clicks "Try Submit Again", button is already re-enabled
                                });
                            });
                        } else {
                            // Jika dibatalkan, aktifkan kembali tombolnya
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });
            }

            // Initialize form behavior
            updateFormBehavior();
        });
    </script>
</body>

</html>