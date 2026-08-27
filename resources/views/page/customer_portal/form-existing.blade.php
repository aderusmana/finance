<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Bank Garansi (Existing)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .form-container { max-width: 800px; margin: 50px auto; }
        .readonly-input { background-color: #e9ecef; cursor: not-allowed; }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">Update Bank Garansi (Existing)</h3>
            <p class="text-muted">BG Reference No.: <strong>{{ $existingBg->bg_number }}</strong></p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="ph-bold ph-info fs-4 me-2"></i>
                    <div>
                        Mode: <strong>Extension / Nominal Update</strong>. <br>
                        Please enter the new nominal. Bank data cannot be changed.
                    </div>
                </div>

                <form id="existingForm">
                    @csrf
                    
                    {{-- 1. HIDDEN INPUTS (PENTING UNTUK CONTROLLER) --}}
                    <input type="hidden" name="mode" value="{{ $mode }}">     {{-- value="existing" --}}
                    <input type="hidden" name="ref_id" value="{{ $refId }}"> {{-- ID BG Lama --}}

                    {{-- 2. LOOP DATA LAMA (READONLY) --}}
                    @foreach($existingBg->details as $index => $detail)
                    <div class="card mb-3 border border-secondary border-opacity-25 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-secondary">
                                <i class="ph-duotone ph-bank me-1"></i> Bank Data #{{ $index + 1 }}
                            </h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Bank Name</label>
                                    <input type="text" class="form-control readonly-input" 
                                           value="{{ $detail->bank_name }}" readonly>
                                    </div>

                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Branch</label>
                                    <input type="text" class="form-control readonly-input" 
                                           value="{{ $detail->branch_name }}" readonly>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">New Nominal (IDR) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">Rp</span>
                                        <input type="text" 
                                               name="bank_details[{{ $index }}][nominal]" 
                                               class="form-control fw-bold fs-5 currency-input" 
                                               placeholder="Enter New Nominal" 
                                               required>
                                    </div>
                                    <div class="form-text">Enter the new nominal to be submitted.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                            <i class="ph-bold ph-paper-plane-right me-2"></i> Submit Nominal Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted small">
            &copy; {{ date('Y') }} Customer Portal System
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        
        // 1. FORMAT CURRENCY (Auto dots separator)
        $(document).on('input', '.currency-input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            $(this).val(value);
        });

        // 2. HANDLE SUBMIT
        $('#existingForm').on('submit', function(e) {
            e.preventDefault();

            // Konfirmasi user dulu
            Swal.fire({
                title: 'Submit Data?',
                text: "Ensure the new nominal is correct.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0d6efd'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitData();
                }
            });
        });

        function submitData() {
            let formData = new FormData(document.getElementById('existingForm'));
            
            // Tampilkan Loading
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait a moment',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                // URL Store berdasarkan Token (sesuaikan route name Anda)
                url: "{{ route('bg.portal.store', $token) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Nominal update submitted successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Redirect atau reload (opsional)
                            window.location.reload(); 
                        });
                    } else {
                        Swal.fire('Failed', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON ? xhr.responseJSON.message : 'System error occurred';
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
    });
</script>

</body>
</html>