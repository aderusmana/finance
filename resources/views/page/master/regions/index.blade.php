<x-app-layout>
    @section('title')
        Region List
    @endsection

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Region List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Region List</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Table Region -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#regionModal" id="btn-create-region">
                    <i class="ph-bold ph-plus pe-2"></i> Add Region
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="regions-table">
                            <thead>
                                <tr>
                                    <th>Region Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Region -->
    <div class="modal fade" id="regionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="regionModalLabel">Create Region</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="regionForm">
                    @csrf
                    <input type="hidden" id="region_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="region_name" class="form-label">Region Name</label>
                                <input type="text" class="form-control" id="region_name" name="region_name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm text-light fs-6" type="submit" id="saveRegionBtn">
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
                $('#regions-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('regions.index') }}"
                    },
                    columns: [
                        { data: 'region_name', name: 'region_name' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-region').on('click', function() {
                    $('#regionModalLabel').text('Create Region');
                    $('#regionForm')[0].reset();
                    $('#regionForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#region_id').val('');
                    $('#regionForm .is-invalid').removeClass('is-invalid');
                    $('#regionForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-region', function() {
                    const btn = $(this);
                    $('#regionModalLabel').text('Edit Region');
                    const id = btn.data('id');
                    const regionName = btn.data('region_name') ?? btn.data('name') ?? '';
                    $('#region_id').val(id);
                    $('#region_name').val(regionName);
                    $('#regionForm').attr('data-mode', 'edit').attr('data-id', id);
                    new bootstrap.Modal(document.getElementById('regionModal')).show();
                });

                // === Submit Form ===
                $('#regionForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let regionId = $(this).attr('data-id') || $('#region_id').val();
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('regions.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('master/regions') }}/" + regionId;
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
                            $('#regionModal').modal('hide');
                            $('#regions-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Region created successfully' : 'Region updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-region-btn', function(e) {
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
                                    $('#regions-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Region deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete region');
                                }
                            });
                        } else {
                            warningMessage('Region deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
