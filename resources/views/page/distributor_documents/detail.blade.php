<x-app-layout>
    @section('title', 'Detail Dokumen - ' . $distributor->name)

    @include('components.sample-table-styles')
    @include('page.distributor_documents.partials.styles')
    @include('page.distributor_documents.partials.scripts')

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
                transferTable = initDistributorTransferDataTable({
                    tableSelector: '#transferDocsTable',
                    ajaxUrl: `{{ url('distributor-documents/detail') }}/${distributorId}`,
                    dataParams: function (d) {
                        d.table = 'transfer';
                        d.transfer_year = $('#transfer_year_select').val() || 'all';
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

            function refreshDetailContent() {
                const container = document.getElementById('detailContentContainer');
                if (!container) {
                    window.location.reload();
                    return;
                }

                container.style.opacity = '0.5';
                fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memperbarui tampilan.');
                    return response.text();
                })
                .then(html => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                    if (document.getElementById('transferDocsTable')) {
                        initTransferDataTable({{ $distributor->id }});
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            }

            function deleteDocument(deleteUrl, docTitle) {
                deleteDistributorDocument(deleteUrl, docTitle, {
                    onSuccess: function () {
                        if (transferTable) {
                            transferTable.ajax.reload(null, false);
                        } else {
                            refreshDetailContent();
                        }
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>