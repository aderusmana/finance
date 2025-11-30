<x-app-layout>
    @section('title')
        Account Group
    @endsection

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Account Group</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Master Data
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Account Group</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Table Account Group -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary btn-md" type="button" data-bs-toggle="modal"
                    data-bs-target="#accountGroupModal" id="btn-create-account-group">
                    <i class="ph-bold ph-plus pe-2"></i> Add Account Group
                </button>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="app-scroll table-responsive app-datatable-default">
                        <table class="w-100 display" id="account-groups-table">
                            <thead>
                                <tr>
                                    <th>Name Account Group</th>
                                    <th>Bank Garansi</th>
                                    <th>CCAR</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Account Group -->
    <div class="modal fade" id="accountGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="accountGroupModalLabel">Create Account Group</h5>
                    <button type="button" class="btn-close m-0 fs-5" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="accountGroupForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name_account_group" class="form-label">Name Account Group</label>
                                <input type="text" class="form-control" id="name_account_group" name="name_account_group" required>
                            </div>
                            <div class="col-12">
                                <label for="bank_garansi" class="form-label">Bank Garansi</label>
                                <select class="form-control" id="bank_garansi" name="bank_garansi">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="ccar" class="form-label">CCAR</label>
                                <select class="form-control" id="ccar" name="ccar">
                                    <option value="">-- Select CCAR --</option>
                                    <option value="smd_idr">SMD IDR</option>
                                    <option value="smd_usd">SMD USD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm text-light fs-6" type="submit" id="saveAccountGroupBtn">
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
                $('#account-groups-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('account-groups.index') }}"
                    },
                    columns: [
                        { data: 'name_account_group', name: 'name_account_group' },
                        {
                            data: 'bank_garansi',
                            name: 'bank_garansi',
                            render: function(data, type, row) {
                                // tampilkan "Yes" jika value 1, selain itu "No"
                                return String(data) === '1' ? 'Yes' : 'No';
                            }
                        },
                        {
                            data: 'ccar',
                            name: 'ccar',
                            render: function(data, type, row) {
                                const val = String(data || '');
                                if (val === 'smd_idr') return 'SMD (IDR)';
                                if (val === 'smd_usd') return 'SMD2 (USD)';
                                return val;
                            }
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                // === Modal Create ===
                $('#btn-create-account-group').on('click', function() {
                    $('#accountGroupModalLabel').text('Create Account Group');
                    $('#accountGroupForm')[0].reset();
                    // default: Bank Garansi = No (0), CCAR empty
                    $('#bank_garansi').val('0');
                    $('#ccar').val('');
                    $('#accountGroupForm').attr('data-mode', 'create').removeAttr('data-id');
                    $('#accountGroupForm .is-invalid').removeClass('is-invalid');
                    $('#accountGroupForm .invalid-feedback').remove();
                });

                // === Modal Edit ===
                $(document).on('click', '.btn-edit-account-group', function() {
                    const btn = $(this);
                    $('#accountGroupModalLabel').text('Edit Account Group');
                    $('#name_account_group').val(btn.data('name_account_group'));
                    // bank_garansi may come as number or string; ensure string '0' or '1'
                    let bankVal = (typeof btn.data('bank_garansi') !== 'undefined') ? String(btn.data('bank_garansi')) : '0';
                    $('#bank_garansi').val(bankVal);
                    $('#ccar').val(btn.data('ccar') ?? '');
                    $('#accountGroupForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    new bootstrap.Modal(document.getElementById('accountGroupModal')).show();
                });

                // === Submit Form ===
                $('#accountGroupForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode');
                    let agId = $(this).attr('data-id');
                    let url, method;
                    if (mode === 'create') {
                        url = "{{ route('account-groups.store') }}";
                        method = "POST";
                    } else {
                        url = "{{ url('master/account-group') }}/" + agId;
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
                            $('#accountGroupModal').modal('hide');
                            $('#account-groups-table').DataTable().ajax.reload(null, false);
                            successMessage((mode === 'create') ? 'Account Group created successfully' : 'Account Group updated successfully');
                        },
                        error: function(xhr) {
                            errorMessage(xhr.responseJSON?.message || 'Something went wrong');
                        }
                    });
                });

                // === SweetAlert Delete ===
                $(document).on('click', '.delete-account-group-btn', function(e) {
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
                                    $('#account-groups-table').DataTable().ajax.reload(null, false);
                                    successMessage(res.message || 'Account Group deleted successfully!');
                                },
                                error: function(xhr) {
                                    errorMessage(xhr.responseJSON?.message || 'Failed to delete Account Group');
                                }
                            });
                        } else {
                            warningMessage('Account Group deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
