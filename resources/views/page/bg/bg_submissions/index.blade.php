<x-app-layout>
    @section('title', 'BG Submissions')

    {{-- Include style yang sama dengan modul lain --}}
    @include('components.sample-table-styles')

    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">BG Submissions</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('bg-list.index') }}">Bank Garansi</a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Submissions</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Filter Sederhana --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted fw-bold me-1"><i class="ph-bold ph-funnel"></i> Filter:</span>
                    <select id="statusFilter" class="form-select select2" style="width: 150px;">
                        <option value="all">All Status</option>
                        <option value="pending_print">Pending Print</option>
                        <option value="awaiting_upload">Awaiting Upload</option>
                        <option value="uploaded">Uploaded</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                {{-- Create Button --}}
                <div class="ms-auto">
                    <button class="btn btn-primary" type="button" id="btn-create">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Submission</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title mb-1">
                        <i class="ph-duotone ph-files me-2"></i> Submission Data
                    </h4>
                    <small class="text-white opacity-75 f-s-12">
                        Upload and manage signed BG application forms.
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="submissionTable">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Customer (Recommendation)</th>
                                <th>Form Code</th>
                                <th>Total Nominal</th>
                                <th>Signed Doc</th>
                                <th class="text-center">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel">Create Submission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Form dengan enctype multipart --}}
                <form id="submissionForm" enctype="multipart/form-data">
                    @csrf
                    {{-- ID untuk Edit --}}
                    <input type="hidden" name="id" id="submissionId">
                    {{-- Method spoofing (akan diatur via JS untuk PUT) --}}
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Dropdown Recommendation --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Select Recommendation <span class="text-danger">*</span></label>
                                <select name="bg_recommendation_id" id="bg_recommendation_id" class="form-select select2-modal" required style="width: 100%;">
                                    <option></option>
                                    @foreach($recommendations as $r)
                                        <option value="{{ $r->id }}">
                                            {{ $r->customer->name ?? 'Unknown' }} - (Rec. Limit: Rp {{ number_format($r->recommended_credit_limit, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted f-s-12">Select the approved recommendation base.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Form Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="form_code" id="form_code" required placeholder="e.g. FORM/BG/001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Total Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="total_nominal" id="total_nominal" required placeholder="0">
                            </div>

                            {{-- File Upload --}}
                            <div class="col-12">
                                <label class="form-label fw-bold">Signed Document (PDF/Jpg)</label>
                                <input type="file" name="signed_document" id="signed_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <div id="current_file_info" class="mt-1 d-none">
                                    <small class="text-success"><i class="ph-bold ph-check-circle"></i> File currently exists.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select select2-modal">
                                    <option value="pending_print">Pending Print</option>
                                    <option value="awaiting_upload">Awaiting Upload</option>
                                    <option value="uploaded">Uploaded</option>
                                    <option value="reviewed">Reviewed</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Submitted At</label>
                                <input type="date" class="form-control" name="submitted_at" id="submitted_at">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Save Submission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/vendor/select/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // 1. Init Plugins
                $('.select2').select2({ theme: 'bootstrap-5' });
                $('.select2-modal').select2({ dropdownParent: $('#submissionModal'), theme: 'bootstrap-5', placeholder: 'Select Option' });

                // 2. DataTables
                const table = $('#submissionTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('bg-submissions.index') }}",
                        data: function(d) {
                            d.status = $('#statusFilter').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                        { data: 'customer_name', name: 'recommendation.customer.name', className: 'fw-bold' },
                        { data: 'form_code', name: 'form_code' },
                        { data: 'total_nominal', name: 'total_nominal', className: 'text-end' },
                        {
                            data: 'file', name: 'signed_document_path', className: 'text-center', orderable: false, searchable: false
                        },
                        {
                            data: 'status', name: 'status', className: 'text-center',
                            render: function(data) {
                                let color = 'secondary';
                                if(data === 'uploaded') color = 'info';
                                if(data === 'completed') color = 'success';
                                if(data === 'awaiting_upload') color = 'warning';
                                return `<span class="badge bg-${color} text-uppercase">${data.replace('_', ' ')}</span>`;
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ]
                });

                $('#statusFilter').change(function() { table.ajax.reload(); });

                // 3. Create Handler
                $('#btn-create').click(function() {
                    $('#submissionForm')[0].reset();
                    $('#submissionId').val('');
                    $('#formMethod').val('POST');
                    $('#bg_recommendation_id').val(null).trigger('change');
                    $('#status').val('pending_print').trigger('change');
                    $('#current_file_info').addClass('d-none');

                    $('#modalLabel').text('Create New Submission');
                    $('#submissionModal').modal('show');
                });

                // 4. Submit Handler (AJAX with FormData for File Upload)
                $('#submissionForm').on('submit', function(e) {
                    e.preventDefault();

                    // Gunakan FormData untuk menangani file
                    let formData = new FormData(this);

                    const id = $('#submissionId').val();
                    let url = "{{ route('bg-submissions.store') }}";

                    if(id) {
                        url = "{{ route('bg-submissions.update', ':id') }}".replace(':id', id);
                        // Laravel membutuhkan _method: PUT di dalam body jika menggunakan FormData
                        formData.append('_method', 'PUT');
                    }

                    Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

                    $.ajax({
                        url: url,
                        method: 'POST', // Selalu POST jika ada file upload (method spoofing via _method)
                        data: formData,
                        processData: false, // Wajib false
                        contentType: false, // Wajib false
                        success: function(res) {
                            Swal.fire('Success', res.message, 'success');
                            $('#submissionModal').modal('hide');
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            let msg = 'Error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });

                // 5. Edit Handler
                $(document).on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    let url = "{{ route('bg-submissions.show', ':id') }}".replace(':id', id);

                    Swal.fire({ title: 'Loading...', didOpen: () => Swal.showLoading() });

                    $.get(url, function(data) {
                        Swal.close();
                        $('#submissionForm')[0].reset();

                        $('#submissionId').val(data.id);
                        $('#formMethod').val('PUT'); // Set method UI logic (though AJAX uses POST+_method)

                        $('#bg_recommendation_id').val(data.bg_recommendation_id).trigger('change');
                        $('#form_code').val(data.form_code);
                        $('#total_nominal').val(data.total_nominal);
                        $('#status').val(data.status).trigger('change');

                        if(data.submitted_at) {
                            $('#submitted_at').val(data.submitted_at.substring(0, 10));
                        }

                        // Info File
                        if(data.signed_document_path) {
                            $('#current_file_info').removeClass('d-none');
                        } else {
                            $('#current_file_info').addClass('d-none');
                        }

                        $('#modalLabel').text('Edit Submission');
                        $('#submissionModal').modal('show');
                    }).fail(function() {
                        Swal.fire('Error', 'Failed to fetch data', 'error');
                    });
                });

                // 6. Delete Handler
                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    let url = "{{ route('bg-submissions.destroy', ':id') }}".replace(':id', id);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "File attached will also be deleted.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                method: 'DELETE',
                                data: { _token: "{{ csrf_token() }}" },
                                success: function(res) {
                                    Swal.fire('Deleted!', res.message, 'success');
                                    table.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error', 'Failed to delete', 'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
