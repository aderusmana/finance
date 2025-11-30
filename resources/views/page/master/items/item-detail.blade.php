<x-app-layout>
    @section('title')
        Item Details
    @endsection

    @include('components.complaint-table-styles')

    <div class="row m-1">
        <div class="col-12 ">
            @isset($itemMaster)
                <h4 class="main-title">Item Details for: {{ $itemMaster->item_master_name }}</h4>
            @else
                <h4 class="main-title">All Item Details</h4>
            @endisset
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('items.indexMaster') }}">
                        <i class="ph-duotone ph-arrow-left f-s-16"></i> Back to Items
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Item Details</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div></div>
                <div>
                    <button class="btn btn-primary" type="button" id="btn-create-item-detail">
                        <i class="ph-bold ph-plus"></i>
                        <span>New Detail</span>
                    </button>
                </div>
            </div>

            <div class="main-table-container">
                <div class="table-header-enhanced">
                    <h4 class="table-title"><i class="ph-duotone ph-list-checks"></i> Details</h4>
                    <p class="table-subtitle">Manage item detail records for this item</p>
                </div>

                <div class="table-responsive">
                    <table class="w-100 display" id="itemDetailTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                @unless(isset($itemMaster))
                                    <th>Item Master</th>
                                @endunless
                                <th>Code</th>
                                <th>Type</th>
                                <th>Unit</th>
                                <th>Net Weight</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- modal create/edit item detail -->
    <div class="modal fade" id="ItemDetailModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ItemDetailModalLabel">Create Item Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="ItemDetailForm" data-mode="create">
                        @csrf
                        <input type="hidden" id="detail_id">
                        <input type="hidden" id="item_master_id" name="item_master_id">
                        <div class="mb-3" id="item-master-select-wrapper">
                            <label for="item_master_select" class="form-label">Item Master</label>
                            <select id="item_master_select" class="form-control">
                                @php $masters = $itemMasters ?? collect(); @endphp
                                @if($masters->isEmpty())
                                    <option disabled selected>No Item Masters available</option>
                                @else
                                    <option disabled selected value="">-- Select Item Master --</option>
                                    @foreach($masters as $im)
                                        <option value="{{ $im->id }}">{{ $im->item_master_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="invalid-feedback" data-error-for="item_master_id"></div>
                        </div>

                        <div class="mb-3">
                            <label for="material_type" class="form-label">Material Type</label>
                            <input type="text" class="form-control" id="material_type" name="material_type">
                            <div class="invalid-feedback" data-error-for="material_type"></div>
                        </div>

                        <div class="mb-3">
                            <label for="item_detail_code" class="form-label">Detail Code</label>
                            <input type="text" class="form-control" id="item_detail_code" name="item_detail_code"
                                required>
                            <div class="invalid-feedback" data-error-for="item_detail_code"></div>
                        </div>

                        <div class="mb-3">
                            <label for="item_detail_name" class="form-label">Detail Name</label>
                            <input type="text" class="form-control" id="item_detail_name" name="item_detail_name"
                                required>
                            <div class="invalid-feedback" data-error-for="item_detail_name"></div>
                        </div>

                        <div class="mb-3">
                            <label for="unit_detail" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit_detail" name="unit">
                            <div class="invalid-feedback" data-error-for="unit"></div>
                        </div>

                        <div class="mb-3">
                            <label for="net_weight" class="form-label">Net Weight</label>
                            <input type="number" step="0.01" class="form-control" id="net_weight" name="net_weight">
                            <div class="invalid-feedback" data-error-for="net_weight"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveItemDetailBtn">Save</button>
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

                // Detect whether page is per-item (itemMaster passed) or all-details
                @isset($itemMaster)
                    const isAll = false;
                    const itemId = {{ $itemMaster->id }};
                @else
                    const isAll = true;
                    const itemId = null;
                @endisset

                // Compute URLs depending on mode
                const detailListUrl = isAll ? "{{ route('items.details.all') }}" : "{{ url('master/items') }}" + '/' +
                    itemId + '/details';
                const createUrl = isAll ? "{{ route('items.details.storeAll') }}" : "{{ url('master/items') }}" + '/' +
                    itemId + '/details';
                const editUrlAllPrefix = "{{ url('master/items/details') }}"; // /master/items/details/{id}/edit
                const editUrlPerItemPrefix = "{{ url('master/items') }}" + '/' + (itemId ?? '') +
                '/details'; // /master/items/{itemId}/details/{id}/edit
                const updateUrlAllPrefix = "{{ url('master/items/details') }}"; // /master/items/details/{id}
                const updateUrlPerItemPrefix = "{{ url('master/items') }}" + '/' + (itemId ?? '') + '/details';
                const deleteUrlAllPrefix = "{{ url('master/items/details') }}";
                const deleteUrlPerItemPrefix = "{{ url('master/items') }}" + '/' + (itemId ?? '') + '/details';

                // Configure table columns depending on mode
                let columns = [{
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }];
                if (isAll) {
                    columns.push({
                        data: 'item_master_name',
                        name: 'item_master_name'
                    });
                }
                columns = columns.concat([{
                        data: 'item_detail_code',
                        name: 'item_detail_code'
                    },
                    {
                        data: 'item_detail_name',
                        name: 'item_detail_name'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'net_weight',
                        name: 'net_weight'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]);

                let detailTable = $('#itemDetailTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: detailListUrl,
                    columns: columns
                });

                // Initialize Select2 for item master selection (only for all-details mode)
                if (isAll) {
                    $('#item_master_select').select2({
                        placeholder: 'Select item master',
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#ItemDetailModal')
                    });

                    // keep hidden input in sync
                    $('#item_master_select').on('change', function() {
                        $('#item_master_id').val($(this).val());
                    });
                }

                $('#btn-create-item-detail').on('click', function() {
                    $('#ItemDetailForm')[0].reset();
                    $('#ItemDetailForm').find('.is-invalid').removeClass('is-invalid');
                    $('#ItemDetailModalLabel').text('Create Item Detail');
                    $('#ItemDetailForm').attr('data-mode', 'create');
                    $('#detail_id').val('');
                    if (isAll) {
                        // clear select2 and hidden input for all-details mode
                        $('#item_master_select').val(null).trigger('change');
                        $('#item_master_id').val('');
                    }
                    if (!isAll) {
                        // set hidden item_master_id for per-item mode
                        $('#item_master_id').val(itemId);
                    }
                    $('#ItemDetailModal').modal('show');
                });

                // Edit detail
                $('#itemDetailTable').on('click', '.edit-item-detail', function() {
                    const id = $(this).data('id');
                    const editUrl = isAll ? (editUrlAllPrefix + '/' + id + '/edit') : (editUrlPerItemPrefix +
                        '/' + id + '/edit');
                    $.get(editUrl, function(res) {
                        if (res.data) {
                            const d = res.data;
                            $('#detail_id').val(d.id);
                            // component_item_master_id removed
                            $('#material_type').val(d.material_type);
                            $('#item_detail_code').val(d.item_detail_code);
                            $('#item_detail_name').val(d.item_detail_name);
                            $('#unit_detail').val(d.unit);
                            $('#item_master_id').val(d.item_master_id || '');
                            if (isAll) {
                                if (d.item_master_id) {
                                    $('#item_master_select').val(d.item_master_id).trigger('change');
                                    $('#item_master_id').val(d.item_master_id);
                                } else {
                                    $('#item_master_select').val(null).trigger('change');
                                    $('#item_master_id').val('');
                                }
                            }
                            $('#net_weight').val(d.net_weight);
                            $('#ItemDetailForm').attr('data-mode', 'edit');
                            $('#ItemDetailModalLabel').text('Edit Item Detail');
                            $('#ItemDetailModal').modal('show');
                        } else {
                            Swal.fire('Error', 'Failed to fetch detail', 'error');
                        }
                    }).fail(function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to fetch detail',
                            'error');
                    });
                });

                // Save detail (create/update)
                $('#saveItemDetailBtn').on('click', function() {
                    let mode = $('#ItemDetailForm').attr('data-mode');
                    let id = $('#detail_id').val();
                    let payload = {
                        material_type: $('#material_type').val(),
                        item_detail_code: $('#item_detail_code').val(),
                        item_detail_name: $('#item_detail_name').val(),
                        unit: $('#unit_detail').val(),
                        net_weight: $('#net_weight').val(),
                        item_master_id: $('#item_master_id').val()
                    };

                    let url, method;
                    if (isAll) {
                        url = mode === 'edit' ? (updateUrlAllPrefix + '/' + id) : createUrl;
                        method = mode === 'edit' ? 'PUT' : 'POST';
                    } else {
                        url = mode === 'edit' ? (updateUrlPerItemPrefix + '/' + id) : createUrl;
                        method = mode === 'edit' ? 'PUT' : 'POST';
                    }

                    $.ajax({
                            url: url,
                            method: method,
                            data: payload
                        })
                        .done(function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Saved',
                                text: res.message || 'Saved',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            $('#ItemDetailModal').modal('hide');
                            detailTable.ajax.reload(null, false);
                        })
                        .fail(function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors || {};
                                $('#ItemDetailForm').find('.is-invalid').removeClass('is-invalid');
                                $('#ItemDetailForm').find('[data-error-for]').text('');
                                $.each(errors, function(key, msgs) {
                                    let el = $('#' + key);
                                    el.addClass('is-invalid');
                                    $('[data-error-for="' + key + '"]').text(msgs[0]);
                                });
                            } else {
                                Swal.fire('Error', xhr.responseJSON?.message || 'An error occurred',
                                    'error');
                            }
                        });
                });

                // Delete detail
                $('#itemDetailTable').on('click', '.delete-item-detail', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                            title: 'Delete detail?',
                            text: 'This action cannot be undone',
                            icon: 'warning',
                            showCancelButton: true
                        })
                        .then(result => {
                            if (result.isConfirmed) {
                                const delUrl = isAll ? (deleteUrlAllPrefix + '/' + id) : (
                                    deleteUrlPerItemPrefix + '/' + id);
                                $.ajax({
                                        url: delUrl,
                                        method: 'DELETE'
                                    })
                                    .done(function(res) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted',
                                            text: res.message || 'Deleted',
                                            timer: 1200,
                                            showConfirmButton: false
                                        });
                                        detailTable.ajax.reload(null, false);
                                    })
                                    .fail(function(xhr) {
                                        Swal.fire('Error', xhr.responseJSON?.message || 'Delete failed',
                                            'error');
                                    });
                            }
                        });
                });
            });
        </script>
    @endpush
</x-app-layout>
