<x-app-layout>
    @section('title')
        Customer List
    @endsection

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Customer List</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customer List</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Table customer -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#customerModal" id="btn-create-customer">
                    <i class="ph-bold ph-plus pe-2"></i> Add customer
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="customers-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit customer -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="customerModalLabel">Create customer</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="customerForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="customer_name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="customer_name" name="name" required>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-12">
                                <label for="customer_address" class="form-label">Address</label>
                                <textarea class="form-control" id="customer_address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm text-light fs-6" type="submit" id="saveCustomerBtn">
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
                $('#customers-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customers.index') }}"
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'address', name: 'address' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-customer').on('click', function() {
                    $('#customerModalLabel').text('Create customer');
                    $('#customerForm')[0].reset();
                    $('#customerForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#customerForm .is-invalid').removeClass('is-invalid');
                    $('#customerForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-customer', function() {
                    const btn = $(this);
                    $('#customerModalLabel').text('Edit customer');
                    $('#customer_id').val(btn.data('id'));
                    $('#customer_name').val(btn.data('name'));
                    $('#customer_address').val(btn.data('address'));
                    $('#customerForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('customerModal')).show();
                });

                // === Submit Form ===
                $('#customerForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let customerId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('customers.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('customers') }}/" + customerId;
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
                            $('#customerModal').modal('hide');
                            $('#customers-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Customer created successfully' : 'Customer updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-customer-btn', function(e) {
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
                                    $('#customers-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Customer deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete Customer');
                                }
                            });
                        } else {
                            warningMessage('Customer deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
