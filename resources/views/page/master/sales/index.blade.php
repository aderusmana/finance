<x-app-layout>
    @section('title')
        Sales
    @endsection

    @include('components.complaint-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Sales Records</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('items.indexMaster') }}">
                        <i class="ph-duotone ph-arrow-left f-s-16"></i> Back
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Sales</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary" type="button" id="btn-create-sales">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Sales</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-list-checks"></i> Sales</h4>
                    <p class="table-subtitle">Manage sales records</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="salesTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Sales Name</th>
                                <th>Position</th>
                                <th>Branch</th>
                                <th>Region</th>
                                <th>Account Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- modal create/edit sales -->
    <div class="modal fade" id="SalesModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="SalesModalLabel">Create Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="SalesForm" data-mode="create">
                        @csrf
                        <input type="hidden" id="detail_id">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User</label>
                            @php $users = $users ?? collect(); @endphp
                            <select id="user_id" name="user_id" class="form-control">
                                @if($users->isEmpty())
                                    <option disabled selected>No users available</option>
                                @else
                                    <option disabled selected value="">-- Select User --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" data-position="{{ $u->position?->position_name ?? '' }}">{{ $u->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" data-error-for="user_id"></div>
                            <div class="form-text" id="user_position"></div>
                        </div>

                        <div class="mb-3">
                            <label for="account_group_id" class="form-label">Account Group</label>
                            @php $accountGroups = $accountGroups ?? collect(); @endphp
                            <select id="account_group_id" name="account_group_id" class="form-control">
                                @if($accountGroups->isEmpty())
                                    <option disabled selected>No account groups</option>
                                @else
                                    <option disabled selected value="">-- Select Account Group --</option>
                                    @foreach($accountGroups as $ag)
                                        <option value="{{ $ag->id }}">{{ $ag->name_account_group ?? $ag->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" data-error-for="account_group_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch</label>
                            @php $branches = $branches ?? collect(); @endphp
                            <select id="branch_id" name="branch_id" class="form-control">
                                @if($branches->isEmpty())
                                    <option disabled selected>No branches</option>
                                @else
                                    <option disabled selected value="">-- Select Branch --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->branch_name ?? $b->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" data-error-for="branch_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="region_id" class="form-label">Region</label>
                            @php $regions = $regions ?? collect(); @endphp
                            <select id="region_id" name="region_id" class="form-control">
                                @if($regions->isEmpty())
                                    <option disabled selected>No regions</option>
                                @else
                                    <option disabled selected value="">-- Select Region --</option>
                                    @foreach($regions as $r)
                                        <option value="{{ $r->id }}">{{ $r->region_name ?? $r->name ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" data-error-for="region_id"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveSalesBtn">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/@phosphor-icons/web"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const detailListUrl = "{{ route('sales.index') }}"; // adjust route as needed
                const createUrl = "{{ route('sales.store') }}";
                const editUrlPrefix = "{{ url('master/sales') }}"; // /sales/{id}/edit
                const updateUrlPrefix = "{{ url('master/sales') }}"; // /sales/{id}
                const deleteUrlPrefix = "{{ url('master/sales') }}"; // /sales/{id}

                let columns = [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'position_name',
                        name: 'position_name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'region_name',
                        name: 'region_name'
                    },
                    {
                        data: 'account_group_name',
                        name: 'account_group_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ];

                let salesTable = $('#salesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: detailListUrl,
                    columns: columns
                });

                // helper messages and confirm dialog (reused)
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

                // Initialize Select2 for all selects
                $('#user_id, #account_group_id, #branch_id, #region_id').select2({
                    placeholder: function(){
                        return $(this).find('option[disabled][selected]').text() || 'Select';
                    },
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#SalesModal')
                });

                $('#btn-create-sales').on('click', function() {
                    $('#SalesForm')[0].reset();
                    $('#SalesForm').find('.is-invalid').removeClass('is-invalid');
                    $('#SalesModalLabel').text('Create Sales');
                    $('#SalesForm').attr('data-mode', 'create');
                    $('#detail_id').val('');
                    // clear select2
                    $('#user_id, #account_group_id, #branch_id, #region_id').val(null).trigger('change');
                    // clear displayed user position
                    $('#user_position').text('');
                    $('#SalesModal').modal('show');
                });

                // Edit sales - populate modal from data-* attributes on the button (no extra AJAX)
                $(document).on('click', '.btn-edit-sale', function() {
                    const btn = $(this);
                    $('#SalesModalLabel').text('Edit Sales');
                    // set form mode and id
                    $('#SalesForm').attr('data-mode', 'edit').attr('data-id', btn.data('id'));
                    // hidden id field
                    $('#detail_id').val(btn.data('id'));
                    // populate selects by id
                    $('#user_id').val(btn.data('user_id') || null).trigger('change');
                    $('#account_group_id').val(btn.data('account_group_id') || null).trigger('change');
                    $('#branch_id').val(btn.data('branch_id') || null).trigger('change');
                    $('#region_id').val(btn.data('region_id') || null).trigger('change');
                    // display user position if available
                    const userPos = btn.data('user_position') || '';
                    $('#user_position').text(userPos ? 'Position: ' + userPos : '');
                    // show modal using bootstrap Modal class
                    new bootstrap.Modal(document.getElementById('SalesModal')).show();
                });

                // Submit Sales form (create/update) using same pattern as TOP
                $('#saveSalesBtn').on('click', function() {
                    // trigger form submit so validation logic is centralized
                    $('#SalesForm').submit();
                });

                $('#SalesForm').on('submit', function(e) {
                    e.preventDefault();
                    let mode = $(this).attr('data-mode') || 'create';
                    let id = $(this).attr('data-id') || $('#detail_id').val();
                    let url, method = 'POST';
                    let formData = $(this).serialize();

                    if (mode === 'create') {
                        url = createUrl;
                    } else {
                        url = updateUrlPrefix + '/' + id;
                        // use POST with method override for PUT
                        formData += '&_method=PUT';
                    }

                    $.ajax({
                        url: url,
                        method: method,
                        data: formData
                    }).done(function(res) {
                        $('#SalesModal').modal('hide');
                        salesTable.ajax.reload(null, false);
                        successMessage(mode === 'create' ? (res.message || 'Sales created') : (res.message || 'Sales updated'));
                    }).fail(function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors || {};
                            $('#SalesForm').find('.is-invalid').removeClass('is-invalid');
                            $('#SalesForm').find('[data-error-for]').text('');
                            $.each(errors, function(key, msgs) {
                                let el = $('#' + key);
                                el.addClass('is-invalid');
                                $('[data-error-for="' + key + '"]').text(msgs[0]);
                            });
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'An error occurred');
                        }
                    });

                    // update displayed position when user select changes
                    $('#user_id').on('change', function() {
                        const pos = $('#user_id option:selected').data('position') || '';
                        $('#user_position').text(pos ? 'Position: ' + pos : '');
                    });
                });

                // Delete sales - use click on delete button to show confirmation and send AJAX delete
                $(document).on('click', '.delete-sale-btn', function(e) {
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
                                }
                            }).done(function(res) {
                                salesTable.ajax.reload(null, false);
                                successMessage(res.message || 'Sales deleted successfully!');
                            }).fail(function(xhr) {
                                errorMessage(xhr.responseJSON?.message || 'Failed to delete');
                            });
                        } else {
                            warningMessage('Deletion canceled');
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
