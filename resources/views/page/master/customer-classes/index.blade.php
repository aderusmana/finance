<x-app-layout>
    @section('title')
        Customer Class
    @endsection

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Customer Class</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Customer Class</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#customerClassModal" id="btn-create-class">
                    <i class="ph-bold ph-plus pe-2"></i> Add Customer Class
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="customer-classes-table">
                            <thead>
                                <tr>
                                    <th>Name Class</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="customerClassModalLabel">Create Customer Class</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="customerClassForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name_class" class="form-label">Name Class</label>
                                <input type="text" class="form-control" id="name_class" name="name_class" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm text-light fs-6" type="submit" id="saveClassBtn">
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
            // --- Helper Functions ---
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

            // --- Main Script ---
            $(document).ready(function() {
                // 1. Inisialisasi DataTable
                var table = $('#customer-classes-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('customer-classes.index') }}"
                    },
                    columns: [
                        { data: 'name_class', name: 'name_class' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // 2. Logic Tombol Create (Reset Modal)
                $('#btn-create-class').on('click', function() {
                    $('#customerClassModalLabel').text('Create Customer Class');
                    $('#customerClassForm')[0].reset();
                    $('#customerClassForm').attr('data-mode', 'create').removeAttr('data-id');
                });

                // 3. Logic Tombol Edit (Tarik Data ke Modal)
                // PERBAIKAN: Selector disesuaikan dengan Controller (.btn-edit-customer-class)
                $(document).on('click', '.btn-edit-customer-class', function() {
                    const btn = $(this);
                    const id = btn.data('id');
                    const name = btn.data('name_class');

                    $('#customerClassModalLabel').text('Edit Customer Class');
                    $('#name_class').val(name); // Isi input
                    
                    // Set mode edit dan ID
                    $('#customerClassForm').attr('data-mode', 'edit').attr('data-id', id);
                    
                    // Tampilkan modal
                    new bootstrap.Modal(document.getElementById('customerClassModal')).show();
                });

                // 4. Logic Submit Form (Create / Edit)
                $('#customerClassForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    let mode = $(this).attr('data-mode');
                    let classId = $(this).attr('data-id');
                    let url, method;

                    if (mode === 'create') {
                        url = "{{ route('customer-classes.store') }}";
                        method = "POST";
                    } else {
                        // Pastikan URL update sesuai route resource
                        url = "{{ url('master/customer-classes') }}/" + classId;
                        method = "POST"; // Laravel spoofing nanti via _method=PUT
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
                            $('#customerClassModal').modal('hide');
                            table.ajax.reload(null, false); // Reload DataTable
                            successMessage(res.message);
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Something went wrong';
                            if(xhr.status === 422) {
                                // Tampilkan error validasi pertama jika ada
                                let errors = xhr.responseJSON.errors;
                                let firstKey = Object.keys(errors)[0];
                                msg = errors[firstKey][0];
                            }
                            errorMessage(msg);
                        }
                    });
                });

                // 5. Logic Tombol Delete (Validasi & AJAX)
                // PERBAIKAN: Selector disesuaikan (.btn-delete-customer-class)
                $(document).on('click', '.btn-delete-customer-class', function(e) {
                    e.preventDefault();
                    const url = $(this).data('url');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Loading state (opsional)
                            Swal.fire({
                                title: 'Deleting...',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });

                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(res) {
                                    table.ajax.reload(null, false);
                                    successMessage(res.message || 'Deleted successfully');
                                },
                                error: function(xhr) {
                                    Swal.close();
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete data');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>