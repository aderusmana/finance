<x-app-layout>
    @section('title')
    Item Master
    @endsection

    {{-- Include table styles component (reuse existing styles) --}}
    @include('components.complaint-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Item Master</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <i class="ph-duotone ph-address-book f-s-16"></i> Master Management
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Item Master</a>
                </li>
            </ul>
        </div>
    </div>

    <!--  -->
    <div class="row">
        <div class="col-12">
            <!-- Action Bar with Enhanced Styling -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <!-- <h5 class="mb-1" style="color: rgb(76, 61, 61); font-weight: 700;">
                        <i class="ph-duotone ph-users-three me-2 text-warning"></i>
                        Approver Management
                    </h5>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-info me-1"></i>
                        Configure approval workflow and sequences
                    </p> -->
                </div>
                <div>
                    <button class="btn btn-primary" type="button" id="btn-create-item">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Item</span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Table Container -->
            <div class="main-table-container">
                <!-- Table Header -->
                <div class="table-header-enhanced">
                    <h4 class="table-title">
                        <i class="ph-duotone ph-list-checks"></i>
                        Item Master
                    </h4>
                    <p class="table-subtitle">
                        Manage item master data
                    </p>
                </div>

                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="w-100 display" id="itemMasterTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- modal create/edit item -->
    <div class="modal fade" id="ItemModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ItemModalLabel">Create Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ItemForm" data-mode="create">
                        @csrf
                        <input type="hidden" id="item_id">

                        <div class="mb-3">
                            <label for="item_master_code" class="form-label">Item Code</label>
                            <input type="text" class="form-control" id="item_master_code" name="item_master_code" required>
                            <div class="invalid-feedback" data-error-for="item_master_code"></div>
                        </div>

                        <div class="mb-3">
                            <label for="item_master_name" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="item_master_name" name="item_master_name" required>
                            <div class="invalid-feedback" data-error-for="item_master_name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                            <div class="invalid-feedback" data-error-for="unit"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveItemBtn">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <script>
        function successMessage(message, title = 'Success', timer = 1500) {
            Swal.fire({ icon: 'success', title: title, text: message, timer: timer, showConfirmButton: false });
        }
        function errorMessage(message, title = 'Error') { Swal.fire({ icon: 'error', title: title, text: message }); }

        $(document).ready(function () {
            // Setup CSRF token for AJAX
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // Base URL for item routes (matches routes in web.php prefix 'master')
            const baseUrl = "{{ url('master/items') }}";

            // Initialize DataTable
            let table = $('#itemMasterTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('items.indexMaster') }}",
                columns: [
                    { data: 'id', name: 'id', orderable: false, searchable: false, render: function (data, type, row, meta) { return meta.row + meta.settings._iDisplayStart + 1; } },
                    { data: 'item_master_code', name: 'item_master_code' },
                    { data: 'item_master_name', name: 'item_master_name' },
                    { data: 'unit', name: 'unit' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Debounced search input
            let searchInput = $('#itemMasterTable_filter input');
            searchInput.unbind();
            let debounceTimer;
            searchInput.bind('keyup', function () { clearTimeout(debounceTimer); debounceTimer = setTimeout(function () { table.search(searchInput.val()).draw(); }, 500); });

            // Open modal for create
            $('#btn-create-item').on('click', function () {
                $('#ItemForm')[0].reset();
                $('#ItemForm').find('.is-invalid').removeClass('is-invalid');
                $('#ItemModalLabel').text('Create Item');
                $('#ItemForm').attr('data-mode', 'create');
                $('#item_id').val('');
                $('#ItemModal').modal('show');
            });

            // Open modal for edit
            $('#itemMasterTable').on('click', '.edit-item', function () {
                const id = $(this).data('id');
                $.get(baseUrl + '/' + id + '/edit', function (res) {
                    if (res.data) {
                        const item = res.data;
                        $('#item_id').val(item.id);
                        $('#item_master_code').val(item.item_master_code);
                        $('#item_master_name').val(item.item_master_name);
                        $('#unit').val(item.unit);
                        $('#ItemForm').attr('data-mode', 'edit');
                        $('#ItemModalLabel').text('Edit Item');
                        $('#ItemModal').modal('show');
                    } else {
                        errorMessage('Failed to fetch item data.');
                    }
                }).fail(function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Failed to fetch item data.';
                    errorMessage(msg);
                });
            });

            // Save (create/update)
            $('#saveItemBtn').on('click', function () {
                let mode = $('#ItemForm').attr('data-mode');
                let id = $('#item_id').val();
                let payload = {
                    item_master_code: $('#item_master_code').val(),
                    item_master_name: $('#item_master_name').val(),
                    unit: $('#unit').val()
                };

                let url = mode === 'edit' ? baseUrl + '/' + id : baseUrl;
                let method = mode === 'edit' ? 'PUT' : 'POST';

                $.ajax({ url: url, method: method, data: payload })
                    .done(function (res) {
                        successMessage(res.message || 'Saved');
                        $('#ItemModal').modal('hide');
                        table.ajax.reload(null, false);
                    })
                    .fail(function (xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors || {};
                            // Clear previous errors
                            $('#ItemForm').find('.is-invalid').removeClass('is-invalid');
                            $('#ItemForm').find('[data-error-for]').text('');
                            $.each(errors, function (key, msgs) {
                                let el = $('#' + key);
                                el.addClass('is-invalid');
                                $('[data-error-for="' + key + '"]').text(msgs[0]);
                            });
                        } else {
                            errorMessage(xhr.responseJSON?.message || 'An error occurred');
                        }
                    });
            });

            // Delete
            $('#itemMasterTable').on('click', '.delete-item', function () {
                const id = $(this).data('id');
                Swal.fire({ title: 'Delete item?', text: 'This action cannot be undone', icon: 'warning', showCancelButton: true })
                    .then(result => {
                        if (result.isConfirmed) {
                            $.ajax({ url: baseUrl + '/' + id, method: 'DELETE' })
                                .done(function (res) { successMessage(res.message || 'Deleted'); table.ajax.reload(null, false); })
                                .fail(function (xhr) { errorMessage(xhr.responseJSON?.message || 'Delete failed'); });
                        }
                    });
            });
        });
    </script>
    @endpush
</x-app-layout>
