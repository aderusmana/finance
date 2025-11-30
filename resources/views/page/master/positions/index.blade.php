<x-app-layout>
    @section('title')
        Position
    @endsection

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Position</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Position</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Table Position -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#positionModal" id="btn-create-position">
                    <i class="ph-bold ph-plus pe-2"></i> Add Position
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="positions-table">
                            <thead>
                                <tr>
                                    <th>Position Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Position -->
    <div class="modal fade" id="positionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="positionModalLabel">Create Position</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="positionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="position_name" class="form-label">Position Name</label>
                                <input type="text" class="form-control" id="position_name" name="position_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm text-light fs-6" type="submit" id="savePositionBtn">
                            Save changes
                        </button>
                        <button class="btn btn-danger btn-sm text-light fs-6" data-bs-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function successMessage(message, title = 'Success', timer = 1500) {
                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    timer: timer,
                    showConfirmButton: false
                });
            }
            function errorMessage(message, title = 'Error') {
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: message
                });
            }
            function warningMessage(message, title = 'Warning') {
                Swal.fire({
                    icon: 'warning',
                    title: title,
                    text: message
                });
            }
            function confirmDialog({
                title = 'Are you sure?',
                text = 'This action cannot be undone!',
                confirmButtonText = 'Yes',
                cancelButtonText = 'Cancel',
                confirmButtonColor = '#3085d6',
                cancelButtonColor = '#d33',
                icon = 'warning',
                reverseButtons = true
            } = {}) {
                return Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor,
                    cancelButtonColor,
                    confirmButtonText,
                    cancelButtonText,
                    reverseButtons
                });
            }

            $(document).ready(function() {
                // === DataTable ===
                $('#positions-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('positions.index') }}"
                    },
                    columns: [
                        { data: 'position_name', name: 'position_name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-position').on('click', function() {
                    $('#positionModalLabel').text('Create Position');
                    $('#positionForm')[0].reset();
                    $('#positionForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#positionForm .is-invalid').removeClass('is-invalid');
                    $('#positionForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-position', function() {
                    const btn = $(this);
                    $('#positionModalLabel').text('Edit Position');
                    $('#position_name').val(btn.data('position_name'));
                    $('#positionForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('positionModal')).show();
                });

                // === Submit Form ===
                $('#positionForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let positionId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('positions.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('master/positions') }}/" + positionId;
                        method = "POST";
                    }
                    let formData = $(this).serialize();
                    if (mode === 'edit') {
                        formData += '&_method=PUT';
                    }
                    $.ajax({
                        url: url,
                        method: method,
                        data: formData,
                        success: function(res) {
                            $('#positionModal').modal('hide');
                            $('#positions-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Position created successfully' : 'Position updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-position-btn', function(e) {
                    e.preventDefault();
                    const btn = $(this);
                    confirmDialog({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone!',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        icon: 'warning',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: btn.closest('form').attr('action'),
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    $('#positions-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Position deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete Position');
                                }
                            });
                        } else {
                            warningMessage('Position deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
