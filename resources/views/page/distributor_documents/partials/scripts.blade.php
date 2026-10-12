@once
<script>
    // ========================================================
    // DISTRIBUTOR DOCUMENTS - SHARED JAVASCRIPT HELPERS
    // ========================================================

    let globalPreviewPdfModalInstance = null;

    /**
     * Unified DataTables Initializer for Running Transfer Documents
     * Used across Detail View, Index Modal Detail, and Public Portal.
     */
    function initDistributorTransferDataTable(config) {
        const tableSelector = config.tableSelector || '#transferDocsTable';
        const tableEl = $(tableSelector);
        if (!tableEl.length) return null;

        if ($.fn.DataTable.isDataTable(tableSelector)) {
            tableEl.DataTable().destroy();
        }

        // Suppress native DataTables browser alert popups
        if ($.fn.dataTable && $.fn.dataTable.ext) {
            $.fn.dataTable.ext.errMode = 'none';
        }

        return tableEl.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: config.ajaxUrl,
                data: function (d) {
                    if (typeof config.dataParams === 'function') {
                        config.dataParams(d);
                    } else if (config.dataParams && typeof config.dataParams === 'object') {
                        Object.assign(d, config.dataParams);
                    }
                },
                error: function (xhr, error, thrown) {
                    if (xhr.status === 0 || error === 'abort') {
                        return;
                    }
                    if (typeof config.onError === 'function') {
                        config.onError(xhr, error, thrown);
                    } else {
                        console.error('Transfer documents table error:', xhr.status, error);
                    }
                }
            },
            columns: [
                { data: 'formatted_date', name: 'transaction_date', className: 'ps-3 fw-semibold text-nowrap', orderable: true },
                { data: 'file_display', name: 'title', orderable: true },
                { data: 'notes', name: 'notes', orderable: false },
                { data: 'file_size_badge', name: 'file_size', className: 'text-center text-nowrap', orderable: true },
                { data: 'actions', name: 'actions', className: 'text-end pe-3 text-nowrap', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: config.searchPlaceholder || "🔍 Cari berkas transfer..."
            }
        });
    }

    /**
     * Unified PDF Preview Modal Handler
     */
    function openPdfPreview(previewUrl, title, downloadUrl, modalId = 'previewPdfModal') {
        const titleEl = document.getElementById('previewPdfModalLabel');
        const subtitleEl = document.getElementById('previewPdfModalSubtitle');
        const downloadBtn = document.getElementById('previewPdfDownloadBtn');
        const newTabBtn = document.getElementById('previewPdfNewTabBtn');
        const iframe = document.getElementById('previewPdfIframe');

        if (titleEl) titleEl.textContent = title || 'Pratinjau Dokumen PDF';
        if (subtitleEl) subtitleEl.textContent = 'Memuat berkas PDF...';
        if (downloadBtn) {
            downloadBtn.href = downloadUrl;
            downloadBtn.style.display = 'inline-flex';
        }
        if (newTabBtn) {
            newTabBtn.href = previewUrl;
            newTabBtn.style.display = 'inline-flex';
        }

        if (iframe) {
            iframe.src = previewUrl;
            iframe.onload = function () {
                if (subtitleEl) subtitleEl.textContent = 'Selesai dimuat.';
            };
        }

        const modalEl = document.getElementById(modalId);
        if (modalEl) {
            if (!globalPreviewPdfModalInstance) {
                globalPreviewPdfModalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            }
            globalPreviewPdfModalInstance.show();
        }
    }

    // Alias for public portal backward compatibility
    function openPublicPdfPreview(previewUrl, title, downloadUrl) {
        openPdfPreview(previewUrl, title, downloadUrl, 'previewPdfModal');
    }

    /**
     * Unified Asynchronous Document Deletion Handler
     */
    function deleteDistributorDocument(deleteUrl, docTitle, options = {}) {
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        };

        const safeTitle = escapeHtml(docTitle);

        const executeDelete = () => {
            if (window.Swal) {
                Swal.fire({
                    title: 'Menghapus Dokumen...',
                    html: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menghapus dokumen.');
                }
                return data;
            })
            .then(data => {
                if (window.Swal) {
                    Swal.close();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'Dokumen berhasil dihapus.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }

                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(data);
                }
            })
            .catch(err => {
                if (typeof options.onError === 'function') {
                    options.onError(err);
                } else if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus',
                        text: err.message || 'Terjadi kesalahan saat menghapus dokumen.'
                    });
                } else {
                    alert('Gagal menghapus dokumen: ' + err.message);
                }
            });
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

    /**
     * Helper to Format File Size
     */
    function formatDocBytes(bytes, decimals = 2) {
        if (!bytes || bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
@endonce
