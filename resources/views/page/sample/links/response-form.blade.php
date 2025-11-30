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

        /* [DIUBAH] Layout Grid 2 Kolom yang konsisten */
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

        /* Memberi jarak antar card di kolom kiri */

        /* [BARU] Style untuk radio button menyamping */
        .radio-group-horizontal .form-check {
            margin-right: 15px;
            /* Jarak antar radio button */
        }

        @media (max-width: 992px) {
            .main-container {
                /* Mengubah layout menjadi 1 kolom di layar kecil */
                grid-template-columns: 1fr;

                /* Mengurangi jarak/padding agar tidak terlalu mepet ke tepi */
                gap: 20px;
                padding: 0 15px;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .action-card {
                /* Menonaktifkan posisi 'sticky' di mobile agar tidak aneh */
                position: static;
                top: auto;
            }

            .card-body.p-md-5 {
                /* Mengurangi padding di dalam card agar tidak terlalu sesak */
                padding: 1.5rem !important;
            }

            .main-header h4 {
                font-size: 1.25rem; /* Sedikit mengecilkan judul utama */
            }
        }

    </style>

<body>
    <div class="main-container">
        <div class="left-column">
            <div class="card">
                <div class="card-header main-header">
                    <h4 class="mb-0">Sample Requisition Approval</h4>
                    <p class="mb-0 opacity-75">SRS No: {{ $requisition->no_srs }}</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    {{-- BAGIAN 1: DETAIL REQUISITION --}}
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

                    {{-- BAGIAN 2: DETAIL ITEM --}}
                    @if($requisition->requisitionItems->count() > 0)
                    <h5 class="section-title mt-5"><i class="fas fa-cubes"></i> Requested Item List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    @if($requisition->sub_category == 'Packaging')
                                    <th class="material-type-column">Material Type</th>
                                    @endif
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
                                    @if($requisition->sub_category == 'Packaging')
                                    <td>{{ $item->material_type ?? '-' }}</td>
                                    <td>{{ $item->itemDetail->item_detail_code ?? '-' }}</td>
                                    <td>{{ $item->itemDetail->item_detail_name ?? '-' }}</td>
                                    <td>{{ $item->itemDetail->unit ?? '-' }}</td>
                                    @else
                                    <td>{{ $item->itemMaster->item_master_code ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->item_master_name ?? '-' }}</td>
                                    <td>{{ $item->itemMaster->unit ?? '-' }}</td>
                                    @endif
                                    <td class="text-center">{{ $item->quantity_required }}</td>
                                    <td class="text-center">{{ $item->quantity_issued ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                    {{-- BAGIAN 3 (KONDISIONAL): DATA MARKETING --}}
                    @if($isQaForm)
                    <h5 class="section-title mt-5"><i class="fas fa-tags"></i> Marketing Data (Read-Only)</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 180px;">Tanggal Selesai Sample</th>
                                    <td>
                                        <input type="date" class="form-control"
                                            value="{{ $requisition->requisitionSpecial->end_date ?? '' }}" readonly>
                                    </td>
                                    <th style="width: 180px;">Kemasan Sample</th>
                                    <td>
                                        <input type="text" class="form-control"
                                            value="{{ $requisition->requisitionSpecial->packaging_selection ?? '' }}"
                                            readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jumlah Sample</th>
                                    <td>
                                        <input type="text" class="form-control"
                                            value="{{ $requisition->requisitionSpecial->sample_count ?? '' }}" readonly>
                                    </td>
                                    <th>Tujuan Sample</th>
                                    <td>
                                        <textarea class="form-control" rows="2"
                                            readonly>{{ $requisition->requisitionSpecial->purpose ?? '' }}</textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Certificate of Analysis</th>
                                    <td>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    @if($requisition->requisitionSpecial->coa_required == 1) checked
                                                @endif disabled>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    @if($requisition->requisitionSpecial->coa_required == 0) checked
                                                @endif disabled>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </td>
                                    <th>Shipment Method</th>
                                    <td>
                                        <input type="text" class="form-control"
                                            value="{{ $requisition->requisitionSpecial->shipment_method ?? '' }}"
                                            readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Aksi --}}
        <div class="right-column">
            <div class="card action-card">
                <div class="card-body p-4">
                    <h5 class="section-title"><i class="fas fa-edit"></i> {{ $pageTitle }}</h5>
                    <form id="responseForm" action="{{ route('approval-sample.process-form') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        @if($action === 'approve' || ($action === 'submit' && $isWarehouseProcess))
                            <input type="hidden" name="action" value="{{ $action }}">
                        @endif
                        @if($isQaForm)
                        <input type="hidden" name="action" value="qa_submit">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asal sample <span class="text-danger">*</span></label>
                            <div class="radio-group-horizontal d-flex flex-wrap">
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="source_option" value="WH"><label class="form-check-label">WH</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="source_option" value="Reference Sample"><label
                                        class="form-check-label">Reference Sample</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="source_option" value="Batch Refinery"><label
                                        class="form-check-label">Batch
                                        Refinery</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="source_option" value="Packing Room"><label
                                        class="form-check-label">Packing
                                        Room</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        id="source_other_radio" name="source_option" value="Lainnya"><label
                                        class="form-check-label">Lainnya...</label></div>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-2" id="source_other_input"
                                style="display: none;" placeholder="Sebutkan asal sample...">
                            <input type="hidden" name="source" id="source" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan sample <span
                                    class="text-danger">*</span></label>
                            <div class="radio-group-horizontal d-flex flex-wrap">
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="description_option" id="keterangan_batch_radio" value="batch"><label
                                        class="form-check-label">Batch / Pallet No</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="description_option" id="keterangan_wb_radio" value="wb"><label
                                        class="form-check-label">WB/DEO No</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="description_option" id="keterangan_tank_radio" value="tank"><label
                                        class="form-check-label">Tank No</label></div>
                            </div>
                            <div class="input-group mt-2" id="keterangan_sample_input_wrapper" style="display: none;">
                                <input type="text" class="form-control" id="keterangan_sample_input_1"
                                    placeholder="Masukkan nomor...">
                                <span class="input-group-text" id="batch_suffix_p" style="display: none;">P</span>
                                <input type="text" class="form-control" id="keterangan_sample_input_2"
                                    style="display: none;" placeholder="No Pallet...">
                            </div>
                            <input type="hidden" name="description" id="description" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tgl Produksi <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="production_date" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Persiapan sample <span
                                    class="text-danger">*</span></label>
                            <div class="radio-group-horizontal d-flex flex-wrap">
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="preparation_method_option" value="Tidak berubah"><label
                                        class="form-check-label">Tidak berubah</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="preparation_method_option" value="Rework Karton"><label
                                        class="form-check-label">Rework Karton</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="preparation_method_option" value="Rework Stencill"><label
                                        class="form-check-label">Rework Stencill</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="preparation_method_option" value="Rework Label"><label
                                        class="form-check-label">Rework Label</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        id="preparation_method_other_radio" name="preparation_method_option"
                                        value="Lainnya"><label class="form-check-label">Lainnya...</label></div>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-2"
                                id="preparation_method_other_input" style="display: none;"
                                placeholder="Sebutkan metode lain...">
                            <input type="hidden" name="preparation_method" id="preparation_method" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan <span class="text-danger">*</span></label>
                            <div class="radio-group-horizontal d-flex flex-wrap">
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        name="sample_notes_option" value="Tempel sticker"><label
                                        class="form-check-label">Tempel sticker</label></div>
                                <div class="form-check me-3 mb-1"><input class="form-check-input" type="radio"
                                        id="sample_notes_other_radio" name="sample_notes_option" value="Lainnya"><label
                                        class="form-check-label">Lainnya...</label></div>
                            </div>
                            <input type="text" class="form-control form-control-sm mt-2" id="sample_notes_other_input"
                                style="display: none;" placeholder="Sebutkan keterangan lain...">
                            <input type="hidden" name="sample_notes" id="sample_notes" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">Submit QA Form</button>
                        </div>

                        @elseif($isWarehouseProcess)
                            <input type="hidden" name="action" value="{{ $action }}">
                        @if ($action === 'review')
                            <p>Please provide notes for this warehouse step. Notes are required to proceed.</p>
                            <div class="mb-3">
                                <label for="notes" class="form-label"><strong>Notes/Reason: <span class="text-danger">*</span></strong></label>
                                <textarea class="form-control" id="notes" name="notes" rows="10" placeholder="Provide notes for your action..." required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">Submit with Notes</button>
                            </div>
                        @endif

                        @else
                        <div class="mb-3">
                            <label class="form-label"><strong>Decision:</strong></label>
                            <div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="action" id="action_review"
                                        value="review" @if($action === 'review' && $originalAction !== 'reject') checked @endif> {{-- Modifikasi di sini --}}
                                    <label class="form-check-label text-primary" for="action_review"><strong>
                                        Approve with Review</strong></label>
                                </div>
                                <div class="form-check me-3 mb-1">
                                    {{-- Tambahkan kondisi checked di sini berdasarkan $originalAction --}}
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

    <!-- Javascript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('responseForm');
            const overlay = document.getElementById('processingOverlay');
            const submitBtn = document.getElementById('submitBtn'); // Ambil tombol submit

            const isQaForm = {{ $isQaForm ? 'true' : 'false' }};
            const isWarehouseProcess = {{ $isWarehouseProcess ? 'true' : 'false' }};
            const isQuickAction = ('{{ $action }}' === 'approve') || ('{{ $action }}' === 'submit' && isWarehouseProcess);

            // --- FUNGSI VALIDASI (TETAP SAMA) ---
            const validateForm = () => {
                if (isQaForm) {
                    let allValid = true;
                    document.querySelectorAll('#responseForm [required]').forEach(input => {
                        if (!input.value.trim()) { allValid = false; }
                    });
                    if (!allValid) {
                        Swal.fire({ icon: 'warning', title: 'Form Tidak Lengkap', text: 'Mohon isi semua kolom yang wajib diisi (*).' });
                        return false;
                    }
                } else if (isWarehouseProcess) {
                    const notesTextarea = document.getElementById('notes');
                    if (!(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Mohon berikan catatan yang valid.' });
                        return false;
                    }
                } else { // Form Approval
                    const reviewRadio = document.getElementById('action_review');
                    const rejectRadio = document.getElementById('action_reject');
                    const notesTextarea = document.getElementById('notes');
                    if ((reviewRadio.checked || rejectRadio.checked) && !(/[a-zA-Z]/.test(notesTextarea.value.trim()))) {
                        Swal.fire({ icon: 'warning', title: 'Alasan Diperlukan', text: 'Mohon berikan alasan yang valid.' });
                        return false;
                    }
                }
                return true; // Jika semua validasi lolos
            };

            // --- LOGIKA AUTO-SUBMIT (TETAP SAMA) ---
            if (isQuickAction) {
                overlay.style.display = 'flex';
                form.submit();
            }
            // --- LOGIKA SUBMIT MANUAL (YANG DIPERBAIKI) ---
            else if (form && submitBtn) {
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

                    Swal.fire({
                        title: 'Konfirmasi Pengiriman',
                        text: "Apakah Anda yakin ingin melanjutkan?",
                        icon: 'question',
                        showRecallButton: true,
                        confirmButtonColor: '#3085d6',
                        recallButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan!',
                        recallButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika dikonfirmasi, tampilkan overlay dan submit form
                            overlay.style.display = 'flex';
                            form.submit();
                        } else {
                            // Jika dibatalkan, aktifkan kembali tombolnya
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    });
                });
            }

            // --- BLOK LOGIKA UNTUK UPDATE TAMPILAN TOMBOL (TETAP SAMA) ---
            if (!isQaForm && !isWarehouseProcess) {
                const reviewRadio = document.getElementById('action_review');
                const rejectRadio = document.getElementById('action_reject');
                const updateSubmitButton = () => {
                    if (reviewRadio.checked) {
                        submitBtn.textContent = 'Submit Approve with Review';
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

            // --- BLOK LOGIKA UNTUK FORM QA/QM (TETAP SAMA) ---
            if (isQaForm) {
                function setupQaRadioLainnya(baseName) {
                    const otherRadio = document.getElementById(`${baseName}_other_radio`);
                    const otherInput = document.getElementById(`${baseName}_other_input`);
                    const finalInput = document.getElementById(baseName);
                    document.querySelectorAll(`input[name="${baseName}_option"]`).forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === 'Lainnya') {
                                otherInput.style.display = 'block';
                                otherInput.focus();
                                finalInput.value = otherInput.value;
                            } else {
                                otherInput.style.display = 'none';
                                otherInput.value = '';
                                finalInput.value = this.value;
                            }
                        });
                    });
                    otherInput.addEventListener('input', function() {
                        otherRadio.checked = true;
                        finalInput.value = this.value;
                    });
                }
                setupQaRadioLainnya('source');
                setupQaRadioLainnya('preparation_method');
                setupQaRadioLainnya('sample_notes');
                const keteranganWrapper = document.getElementById('keterangan_sample_input_wrapper');
                const keteranganInput1 = document.getElementById('keterangan_sample_input_1');
                const keteranganInput2 = document.getElementById('keterangan_sample_input_2');
                const batchSuffix = document.getElementById('batch_suffix_p');
                const finalDescriptionInput = document.getElementById('description');
                document.querySelectorAll('input[name="description_option"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const selectedType = this.value;
                        keteranganInput1.value = '';
                        keteranganInput2.value = '';
                        finalDescriptionInput.value = '';
                        keteranganWrapper.style.display = 'flex';
                        if (selectedType === 'batch') {
                            keteranganInput1.placeholder = 'Batch No...';
                            keteranganInput2.style.display = 'block';
                            keteranganInput2.placeholder = 'Pallet No...';
                            batchSuffix.style.display = 'inline-block';
                        } else {
                            keteranganInput1.placeholder = selectedType === 'wb' ? 'WB/DEO No...' : 'Tank No...';
                            keteranganInput2.style.display = 'none';
                            batchSuffix.style.display = 'none';
                        }
                    });
                });
                function updateDescription() {
                    const selectedType = document.querySelector('input[name="description_option"]:checked')?.value;
                    if (!selectedType) return;

                    const val1 = document.getElementById('keterangan_sample_input_1').value;
                    const val2 = document.getElementById('keterangan_sample_input_2').value;
                    const finalDescriptionInput = document.getElementById('description');

                    let finalValue = '';
                    if (selectedType === 'batch') {
                        // Format untuk Batch/Pallet tetap sama: "Nilai1P Nilai2"
                        finalValue = `${val1}P${val2}`;
                    } else if (selectedType === 'wb') {
                        // Format BARU untuk WB/DEO: "WB:Nilai1"
                        finalValue = `WB:${val1}`;
                    } else if (selectedType === 'tank') {
                        // Format BARU untuk Tank: "TANK:Nilai1"
                        finalValue = `TANK:${val1}`;
                    }
                    finalDescriptionInput.value = finalValue;
                }
                keteranganInput1.addEventListener('input', updateDescription);
                keteranganInput2.addEventListener('input', updateDescription);
            }
        });
    </script>
</body>

</html>
