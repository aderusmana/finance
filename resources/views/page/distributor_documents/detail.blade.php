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
        .swal2-container {
            z-index: 99999 !important;
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
        @include('page.distributor_documents.partials.detail-content')
    </div>

    {{-- Modal PDF Viewer --}}
    @include('page.distributor_documents.partials.preview-modal')

    {{-- Modal Upload Document --}}
    @include('page.distributor_documents.partials.upload-modal', [
        'distributor' => $distributor,
        'year' => $year,
    ])

    @push('scripts')
        <script>
            let previewPdfModalInstance = null;
            let uploadModalInstance = null;
            let transferTable = null;

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

                if (document.getElementById('transferDocsTable')) {
                    initTransferDataTable({{ $distributor->id }});
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

            function initTransferDataTable(distributorId) {
                if ($.fn.DataTable.isDataTable('#transferDocsTable')) {
                    $('#transferDocsTable').DataTable().destroy();
                }
                const tableEl = $('#transferDocsTable');
                if (!tableEl.length) return;

                transferTable = tableEl.DataTable({
                    processing: true,
                    serverSide: true,
                    dom: "<'row p-2 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end'f>>" +
                         "<'row'<'col-12'tr>>" +
                         "<'row p-3 border-top align-items-center'<'col-12 col-md-6 text-muted small'i><'col-12 col-md-6 d-flex justify-content-md-end'p>>",
                    ajax: {
                        url: `{{ url('distributor-documents/detail') }}/${distributorId}`,
                        data: function (d) {
                            d.table = 'transfer';
                            d.transfer_year = $('#transfer_year_select').val() || 'all';
                        }
                    },
                    columns: [
                        { data: 'formatted_date', name: 'transaction_date', className: 'ps-3 fw-semibold text-nowrap', orderable: true },
                        { data: 'file_display', name: 'title', orderable: false },
                        { data: 'notes', name: 'notes', orderable: false },
                        { data: 'file_size_badge', name: 'file_size', className: 'text-center text-nowrap', orderable: true },
                        { data: 'actions', name: 'actions', className: 'text-end pe-3 text-nowrap', orderable: false, searchable: false }
                    ],
                    order: [[0, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    language: {
                        processing: '<div class="d-flex justify-content-center align-items-center py-3"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat berkas transfer...</div>',
                        emptyTable: '<div class="py-4 text-center text-muted"><i class="iconoir-empty-page fs-2 d-block mb-2"></i>Belum ada dokumen penjelasan transfer</div>',
                        zeroRecords: '<div class="py-4 text-center text-muted"><i class="iconoir-search fs-2 d-block mb-2"></i>Tidak ada dokumen yang sesuai dengan filter / pencarian</div>',
                        lengthMenu: '_MENU_ per halaman',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ berkas',
                        infoEmpty: 'Menampilkan 0 berkas',
                        infoFiltered: '(disaring dari _MAX_ total berkas)',
                        search: '_INPUT_',
                        searchPlaceholder: 'Cari berkas transfer...',
                        paginate: {
                            previous: '<i class="iconoir-nav-arrow-left"></i>',
                            next: '<i class="iconoir-nav-arrow-right"></i>'
                        }
                    }
                });
            }

            function changeDetailYear(distributorId, newYear, tab) {
                window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${newYear}&tab=${tab}`;
            }

            function switchDetailTab(distributorId, year, tab) {
                window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${year}&tab=${tab}`;
            }

            function changeTransferYear(distributorId, year, transferYear) {
                if (transferTable) {
                    transferTable.ajax.reload();
                } else {
                    window.location.href = `{{ url('distributor-documents/detail') }}/${distributorId}?year=${year}&tab=transfer&transfer_year=${transferYear}`;
                }
            }

            function deleteDocument(deleteUrl, docTitle) {
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text || '';
                    return div.innerHTML;
                };

                const safeTitle = escapeHtml(docTitle);

                const executeDelete = () => {
                    Swal.fire({
                        title: 'Menghapus Dokumen...',
                        html: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

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
                };

                if (window.Swal) {
                    Swal.fire({
                        title: 'Hapus Dokumen?',
                        html: `Apakah Anda yakin ingin menghapus dokumen <strong>"${safeTitle}"</strong>?<br><small class="text-muted">Berkas akan dihapus secara permanen dari server.</small>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="iconoir-trash me-1"></i> Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            executeDelete();
                        }
                    });
                } else {
                    if (confirm(`Hapus dokumen "${docTitle}"?`)) {
                        executeDelete();
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>