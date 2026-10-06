<x-app-layout>
    @section('title', 'Detail Dokumen - ' . $distributor->name)

    <style>
        .detail-header-card {
            background: linear-gradient(135deg, #152238 0%, #1e3a5f 100%);
            border-radius: 0.75rem;
            color: #fff;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 4px 12px rgba(21, 34, 56, 0.15);
        }

        .distributor-code-badge-light {
            font-family: monospace;
            font-size: 0.85rem;
            font-weight: 600;
            color: #93c5fd;
            background-color: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(147, 197, 253, 0.3);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .nav-tabs-custom {
            border-bottom: 2px solid #e2e8f0;
            gap: 8px;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #64748b;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 0;
            background: transparent;
            transition: all 0.2s ease;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #1e3a8a;
            border-bottom-color: #cbd5e1;
        }

        .nav-tabs-custom .nav-link.active {
            color: #1e3a8a;
            border-bottom-color: #2563eb;
            background: transparent;
        }

        .month-card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            background: #ffffff;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .month-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }

        .month-card-header {
            padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .doc-section {
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .doc-item-row {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
        }
        .doc-item-row:last-child {
            margin-bottom: 0;
        }

        .btn-add-quick {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .badge-status-complete {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .badge-status-partial {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .badge-status-empty {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
    </style>

    <div class="row m-1 mb-2">
        <div class="col-12">
            <a href="{{ route('distributor.documents.index', ['year' => $year]) }}" class="text-decoration-none small text-muted">
                <i class="iconoir-arrow-left me-1"></i> Kembali ke Manajemen Dokumen Distributor
            </a>
        </div>
    </div>

    <div id="detailContentContainer" class="mb-5 pb-5">
        @include('finance.distributor_documents.partials.detail-content')
    </div>

    {{-- Modal PDF Viewer --}}
    <div class="modal fade" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-2 px-3 bg-light">
                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                        <i class="iconoir-page text-danger fs-5"></i>
                        <div>
                            <h6 class="modal-title text-truncate fw-bold mb-0" id="previewPdfModalLabel">Pratinjau Dokumen PDF</h6>
                            <small class="text-muted" id="previewPdfModalSubtitle"></small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <a href="#" id="previewPdfDownloadBtn" class="btn btn-sm btn-outline-primary" download>
                            <i class="iconoir-download me-1"></i> Unduh PDF
                        </a>
                        <a href="#" id="previewPdfNewTabBtn" target="_blank" class="btn btn-sm btn-outline-secondary" title="Buka di tab baru">
                            <i class="iconoir-open-new-window"></i>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-dark" style="min-height: 550px; height: 75vh;">
                    <iframe id="previewPdfIframe" src="" class="w-100 h-100" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Upload Document --}}
    <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="uploadDocumentForm" action="{{ route('distributor.documents.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="modal_distributor_id" name="distributor_id" value="{{ $distributor->id }}">
                    <input type="hidden" id="modal_year_input" name="year" value="{{ $year }}">

                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadDocumentModalLabel">
                            <i class="iconoir-upload me-2 text-primary"></i>Upload Dokumen Distributor
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_doc_type" class="form-label fw-semibold">Jenis Dokumen <span class="text-danger">*</span></label>
                            <select id="modal_doc_type" name="doc_type" class="form-select" required onchange="onDocTypeChanged()">
                                <option value="bupot">Bukti Potong (BuPot)</option>
                                <option value="top_insentif">TOP Insentif</option>
                                <option value="transfer">Penjelasan Transfer (Running)</option>
                            </select>
                        </div>

                        <div id="modal_monthly_wrapper" class="mb-3">
                            <label for="modal_month" class="form-label fw-semibold">Bulan Dokumen <span class="text-danger">*</span></label>
                            <select id="modal_month" name="month" class="form-select">
                                @foreach ([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $mNum => $mName)
                                    <option value="{{ $mNum }}">{{ $mName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="modal_transfer_wrapper" class="mb-3" style="display: none;">
                            <label for="modal_transaction_date" class="form-label fw-semibold">Tanggal Transaksi Transfer <span class="text-danger">*</span></label>
                            <input type="date" id="modal_transaction_date" name="transaction_date" class="form-control">
                            <small class="text-muted">Tanggal bukti transaksi transfer.</small>
                        </div>

                        <div class="mb-3">
                            <label for="modal_title" class="form-label fw-semibold">Judul / Keterangan Tampilan Dokumen</label>
                            <input type="text" id="modal_title" name="title" class="form-control" maxlength="255" placeholder="Contoh: BuPot PPh 23 Jan 2026, Memo Deviasi TOP">
                        </div>

                        <div class="mb-3">
                            <label for="modal_notes" class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea id="modal_notes" name="notes" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
                        </div>

                        <div class="mb-2">
                            <label for="modal_file" class="form-label fw-semibold">Pilih Berkas PDF <span class="text-danger">*</span></label>
                            <input type="file" id="modal_file" name="file" class="form-control" accept=".pdf" required>
                            <small class="text-muted d-block mt-1">Hanya format <strong>PDF</strong>. Maksimal 1 MB.</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitUpload">
                            <i class="iconoir-upload me-1"></i> Simpan & Unggah Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let previewPdfModalInstance = null;
            let uploadModalInstance = null;

            document.addEventListener('DOMContentLoaded', function () {
                const previewEl = document.getElementById('previewPdfModal');
                if (previewEl) {
                    previewPdfModalInstance = new bootstrap.Modal(previewEl);
                    previewEl.addEventListener('hidden.bs.modal', function () {
                        document.getElementById('previewPdfIframe').src = '';
                    });
                }

                const uploadEl = document.getElementById('uploadDocumentModal');
                if (uploadEl) {
                    uploadModalInstance = new bootstrap.Modal(uploadEl);
                }
            });

            function openPdfPreview(previewUrl, title, downloadUrl) {
                document.getElementById('previewPdfModalLabel').textContent = title || 'Pratinjau Dokumen PDF';
                document.getElementById('previewPdfModalSubtitle').textContent = 'Memuat berkas PDF...';
                document.getElementById('previewPdfDownloadBtn').href = downloadUrl;
                document.getElementById('previewPdfNewTabBtn').href = previewUrl;

                const iframe = document.getElementById('previewPdfIframe');
                iframe.src = previewUrl;
                iframe.onload = function () {
                    document.getElementById('previewPdfModalSubtitle').textContent = 'Selesai dimuat.';
                };

                if (previewPdfModalInstance) {
                    previewPdfModalInstance.show();
                }
            }

            function openUploadModal(docType, month, distributorId, year) {
                if (distributorId) document.getElementById('modal_distributor_id').value = distributorId;
                if (year) document.getElementById('modal_year_input').value = year;
                if (docType) document.getElementById('modal_doc_type').value = docType;
                if (month) document.getElementById('modal_month').value = month;

                onDocTypeChanged();

                if (uploadModalInstance) {
                    uploadModalInstance.show();
                }
            }

            function onDocTypeChanged() {
                const docType = document.getElementById('modal_doc_type').value;
                const monthlyWrapper = document.getElementById('modal_monthly_wrapper');
                const transferWrapper = document.getElementById('modal_transfer_wrapper');
                const monthInput = document.getElementById('modal_month');
                const dateInput = document.getElementById('modal_transaction_date');

                if (docType === 'transfer') {
                    monthlyWrapper.style.display = 'none';
                    transferWrapper.style.display = 'block';
                    monthInput.required = false;
                    monthInput.disabled = true;
                    dateInput.required = true;
                    dateInput.disabled = false;
                } else {
                    monthlyWrapper.style.display = 'block';
                    transferWrapper.style.display = 'none';
                    monthInput.required = true;
                    monthInput.disabled = false;
                    dateInput.required = false;
                    dateInput.disabled = true;
                }
            }

            function changeDetailYear(distributorId, newYear, tab) {
                window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${newYear}&tab=${tab}`;
            }

            function switchDetailTab(distributorId, year, tab) {
                window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${year}&tab=${tab}`;
            }

            function changeTransferYear(distributorId, year, transferYear) {
                window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${year}&tab=transfer&transfer_year=${transferYear}`;
            }

            function deleteDocument(deleteUrl, docTitle) {
                if (!confirm(`Hapus dokumen "${docTitle}"?`)) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        </script>
    @endpush
</x-app-layout>