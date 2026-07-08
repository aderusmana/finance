<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Logistic Fee</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background-color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; min-height: 100vh; margin: 0; display: flex; flex-direction: column; justify-content: center;">

<div class="container" style="padding-top: 1.5rem; padding-bottom: 1.5rem;">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11">

            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px; margin-bottom: 0.75rem; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);">
                    <i class="ph-bold ph-shield-check" style="font-size: 1.25rem; color: #ffffff;"></i>
                </div>
                <h3 style="font-weight: 800; color: #0f172a; margin-bottom: 0.2rem; letter-spacing: -0.5px;">Persetujuan Logistic Fee</h3>
                <p style="color: #64748b; font-size: 0.9rem; font-weight: 500; margin: 0;">PT Sinar Meadow International Indonesia</p>
            </div>

            <div style="background-color: #ffffff; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15); overflow: hidden; border: 1px solid #cbd5e1;">
                <div class="row g-0">

                    <div class="col-md-5" style="background: linear-gradient(145deg, #f8fafc 0%, #eff6ff 100%); border-right: 1px solid #e2e8f0; padding: 2rem 2.5rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                            <div style="background-color: #e0e7ff; padding: 8px; border-radius: 10px; margin-right: 0.75rem;">
                                <i class="ph-bold ph-receipt" style="font-size: 1.25rem; color: #4f46e5;"></i>
                            </div>
                            <h5 style="font-weight: 800; margin: 0; color: #1e293b; letter-spacing: -0.5px;">Detail Data</h5>
                        </div>

                        <div style="margin-bottom: 1rem; background-color: rgba(255,255,255,0.8); padding: 0.875rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem;">Distributor</div>
                            <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $logisticData->distributor->name ?? '-' }}</div>
                        </div>

                        <div style="margin-bottom: 1.5rem; background-color: rgba(255,255,255,0.8); padding: 0.875rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                            <div style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem;">Customer</div>
                            <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $logisticData->customer->name ?? '-' }}</div>
                        </div>

                        <div style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 8px 20px rgba(0,0,0,0.02); position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: linear-gradient(to bottom, #cbd5e1, #f97316);"></div>

                            <div style="margin-bottom: 1rem;">
                                <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem;">Harga Saat Ini</div>
                                <div style="font-size: 1.1rem; font-weight: 700; color: #64748b; text-decoration: line-through decoration-color: #cbd5e1 decoration-thickness: 2px;">Rp {{ number_format($logisticData->logistic_fee ?? 0, 0, ',', '.') }}</div>
                            </div>

                            <div>
                                <div style="font-size: 0.7rem; font-weight: 800; color: #ea580c; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; display: flex; align-items: center; gap: 4px;">
                                    Harga Diajukan <i class="ph-bold ph-trend-up"></i>
                                </div>
                                <div style="font-size: 1.75rem; font-weight: 800; background: linear-gradient(90deg, #ea580c, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                    Rp {{ number_format($logisticData->proposed_fee, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7" style="padding: 2rem 2.5rem; background-color: #ffffff;">
                        <form id="approvalForm" action="{{ route('logistic-fees.approval.process', ['token' => $log->token, 'action' => $action]) }}"
                            data-base-url="{{ url('/logistic-fees/approval/process/' . $log->token) }}"
                            method="POST">
                            @csrf

                            <h6 style="font-weight: 800; color: #0f172a; margin-bottom: 1rem; letter-spacing: -0.5px;">Tentukan Keputusan</h6>

                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="action_selection" id="btn-approve" value="approve_with_review" autocomplete="off" {{ $action !== 'reject' ? 'checked' : '' }}>
                                    <label id="label-approve" for="btn-approve" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0.875rem; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-weight: 700; color: #64748b; background: #ffffff;">
                                        <i class="ph-bold ph-check-circle" style="font-size: 1.2rem;"></i> Approve
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <input type="radio" class="btn-check" name="action_selection" id="btn-reject" value="reject" autocomplete="off" {{ $action === 'reject' ? 'checked' : '' }}>
                                    <label id="label-reject" for="btn-reject" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 0.875rem; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-weight: 700; color: #64748b; background: #ffffff;">
                                        <i class="ph-bold ph-x-circle" style="font-size: 1.2rem;"></i> Reject
                                    </label>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.25rem;">
                                <label for="notes" style="display: flex; align-items: center; justify-content: space-between; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem;">
                                    <span style="font-size: 0.9rem;">Catatan <span id="required-star" style="color: #ef4444; display: none;">*</span></span>
                                    <span id="form-help-text" style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; background-color: #f1f5f9; padding: 3px 8px; border-radius: 20px;">Opsional</span>
                                </label>
                                <textarea id="notes" name="notes" rows="3" style="width: 100%; border: 2px solid #e2e8f0; border-radius: 12px; padding: 0.875rem 1rem; font-size: 0.95rem; outline: none; transition: all 0.3s; background-color: #f8fafc; font-family: inherit; resize: none;" placeholder="Ketik catatan atau alasan Anda di sini..."></textarea>
                                <div id="notesError" style="color: #ef4444; font-weight: 600; margin-top: 0.4rem; display: none; font-size: 0.8rem; align-items: center; gap: 4px;"></div>
                            </div>

                            <div>
                                <button type="submit" id="submitBtn" style="width: 100%; border-radius: 12px; padding: 0.875rem; font-size: 1rem; font-weight: 700; color: #ffffff; border: none; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 8px 15px rgba(16, 185, 129, 0.2);">
                                    <i id="submitIcon" class="ph-bold ph-paper-plane-tilt" style="font-size: 1.2rem;"></i> <span id="submitText">Submit Keputusan</span>
                                </button>
                            </div>

                            <div style="margin-top: 1.25rem; padding: 0.875rem 1rem; background-color: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1; display: flex; align-items: flex-start; gap: 10px;">
                                <i class="ph-bold ph-shield-check" style="color: #3b82f6; font-size: 1.1rem; margin-top: 2px;"></i>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.5;">
                                    <strong style="color: #334155;">Audit Trail Aktif.</strong> Keputusan Anda akan direkam dengan aman ke dalam log sistem PT Sinar Meadow sebagai bukti persetujuan digital.
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

            <div style="text-align: center; margin-top: 1.5rem; color: #64748b; font-size: 0.75rem; font-weight: 500;">
                Sistem Persetujuan Digital &copy; {{ date('Y') }} PT Sinar Meadow
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('approvalForm');
        const baseUrl = form.getAttribute('data-base-url');
        const radios = document.querySelectorAll('input[name="action_selection"]');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');
        const notesInput = document.getElementById('notes');
        const notesError = document.getElementById('notesError');
        const requiredStar = document.getElementById('required-star');
        const helpText = document.getElementById('form-help-text');

        const labelApprove = document.getElementById('label-approve');
        const labelReject = document.getElementById('label-reject');

        // Hover effect JS
        function addHoverEffect(element, defaultBorder, defaultBg, hoverBorder, hoverBg) {
            element.addEventListener('mouseenter', () => {
                if(!document.getElementById(element.getAttribute('for')).checked) {
                    element.style.borderColor = hoverBorder;
                    element.style.backgroundColor = hoverBg;
                    element.style.transform = 'translateY(-2px)';
                }
            });
            element.addEventListener('mouseleave', () => {
                if(!document.getElementById(element.getAttribute('for')).checked) {
                    element.style.borderColor = defaultBorder;
                    element.style.backgroundColor = defaultBg;
                    element.style.transform = 'translateY(0)';
                }
            });
        }

        addHoverEffect(labelApprove, '#e2e8f0', '#ffffff', '#a7f3d0', '#f0fdf4');
        addHoverEffect(labelReject, '#e2e8f0', '#ffffff', '#fecaca', '#fef2f2');

        function updateFormUI() {
            const selectedAction = document.querySelector('input[name="action_selection"]:checked').value;
            form.action = `${baseUrl}/${selectedAction}`;
            notesError.style.display = 'none';

            // Reset Styles
            [labelApprove, labelReject].forEach(el => {
                el.style.borderColor = '#e2e8f0';
                el.style.backgroundColor = '#ffffff';
                el.style.color = '#64748b';
                el.style.transform = 'translateY(0)';
                el.style.boxShadow = 'none';
            });

            if(selectedAction === 'approve_with_review') {
                labelApprove.style.borderColor = '#10b981';
                labelApprove.style.backgroundColor = '#ecfdf5';
                labelApprove.style.color = '#059669';
                labelApprove.style.boxShadow = '0 8px 15px rgba(16, 185, 129, 0.1)';

                submitBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                submitBtn.style.boxShadow = '0 8px 15px rgba(16, 185, 129, 0.25)';
                submitText.innerText = 'Submit Approve';

                requiredStar.style.display = 'none';
                helpText.innerText = "Opsional";
                helpText.style.color = "#64748b";
                helpText.style.backgroundColor = "#f1f5f9";
            } else {
                labelReject.style.borderColor = '#ef4444';
                labelReject.style.backgroundColor = '#fef2f2';
                labelReject.style.color = '#dc2626';
                labelReject.style.boxShadow = '0 8px 15px rgba(239, 68, 68, 0.1)';

                submitBtn.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
                submitBtn.style.boxShadow = '0 8px 15px rgba(239, 68, 68, 0.25)';
                submitText.innerText = 'Submit Reject';

                requiredStar.style.display = 'inline';
                helpText.innerText = "Wajib Diisi";
                helpText.style.color = "#dc2626";
                helpText.style.backgroundColor = "#fef2f2";
            }
        }

        notesInput.addEventListener('focus', () => {
            notesInput.style.borderColor = '#3b82f6';
            notesInput.style.backgroundColor = '#ffffff';
            notesInput.style.boxShadow = '0 0 0 4px rgba(59, 130, 246, 0.1)';
        });
        notesInput.addEventListener('blur', () => {
            notesInput.style.borderColor = '#e2e8f0';
            notesInput.style.backgroundColor = '#f8fafc';
            notesInput.style.boxShadow = 'none';
        });

        submitBtn.addEventListener('mouseenter', () => { if(!submitBtn.disabled) submitBtn.style.transform = 'translateY(-2px)' });
        submitBtn.addEventListener('mouseleave', () => submitBtn.style.transform = 'translateY(0)');

        radios.forEach(radio => radio.addEventListener('change', updateFormUI));
        updateFormUI();

        // Validasi Form & SweetAlert Confirmation
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Hentikan pengiriman langsung

            const selectedAction = document.querySelector('input[name="action_selection"]:checked').value;
            const notesVal = notesInput.value.trim();
            notesError.style.display = 'none';
            let errorMessage = '';

            const hasAlphabet = /[a-zA-Z]/.test(notesVal);
            const isAllSameChar = /^(.)\1+$/.test(notesVal);
            const hasLongRepeatingChars = /(.)\1{4,}/.test(notesVal);

            if (selectedAction === 'reject' && notesVal === '') {
                errorMessage = 'Catatan wajib diisi sebagai alasan penolakan.';
            } else if (notesVal !== '') {
                if (!hasAlphabet) {
                    errorMessage = 'Catatan harus mengandung huruf (tidak boleh hanya angka atau simbol).';
                } else if (isAllSameChar || hasLongRepeatingChars) {
                    errorMessage = 'Catatan tidak valid. Hindari penggunaan huruf yang diulang-ulang.';
                }
            }

            if (errorMessage !== '') {
                notesError.innerHTML = `<i class="ph-bold ph-warning-circle" style="font-size:1.1rem;"></i> <span>${errorMessage}</span>`;
                notesError.style.display = 'flex';

                notesInput.style.transform = 'translateX(-5px)';
                setTimeout(() => notesInput.style.transform = 'translateX(5px)', 100);
                setTimeout(() => notesInput.style.transform = 'translateX(0)', 200);
                notesInput.style.borderColor = '#ef4444';
                notesInput.focus();
                return; // Stop jika error
            }

            // Jika lulus validasi, tampilkan SweetAlert
            // Jika lulus validasi, tampilkan SweetAlert
            const isApprove = selectedAction === 'approve_with_review';

            // Ambil data harga dari variabel PHP Blade
            const oldFee = "{{ number_format($logisticData->logistic_fee ?? 0, 0, ',', '.') }}";
            const newFee = "{{ number_format($logisticData->proposed_fee, 0, ',', '.') }}";

            const swalTitle = isApprove ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan';

            // Susun kalimat HTML yang elegan untuk SweetAlert
            let swalHtml = '';
            if (isApprove) {
                swalHtml = `Anda akan <b>Menyetujui</b> perubahan harga dari:<br><br>
                            <span style="color: #64748b; font-size: 1.1rem; text-decoration: line-through;">Rp ${oldFee}</span>
                            <i class="ph-bold ph-arrow-right mx-2" style="color: #94a3b8;"></i>
                            <b style="color: #10b981; font-size: 1.5rem;">Rp ${newFee}</b><br><br>
                            <span style="font-size: 0.95rem; color: #475569;">Keputusan ini akan direkam dan data akan diteruskan ke sistem.</span>`;
            } else {
                swalHtml = `Anda akan <b>Menolak</b> pengajuan harga sebesar <b style="color: #ef4444; font-size: 1.2rem;">Rp ${newFee}</b>.<br><br>
                            <span style="font-size: 0.95rem; color: #475569;">Apakah catatan alasan penolakan Anda sudah benar? Pengajuan akan dikembalikan ke pemohon.</span>`;
            }

            const confirmColor = isApprove ? '#10b981' : '#ef4444';
            const confirmText = isApprove ? 'Ya, Setujui Sekarang' : 'Ya, Tolak Pengajuan';

            Swal.fire({
                title: swalTitle,
                html: swalHtml, // Menggunakan properti HTML agar bisa dicustom warnanya
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#94a3b8',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true, // Tombol Batal di kiri
                customClass: {
                    title: 'fs-4 fw-bold font-monospace',
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Animasi Loading pada Tombol Submit Asli
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.8';
                    submitIcon.className = 'spinner-border spinner-border-sm';
                    submitText.innerText = 'Memproses Data...';

                    // Submit form secara programatis ke server
                    form.submit();
                }
            });
        });
    });
</script>
</body>
</html>
