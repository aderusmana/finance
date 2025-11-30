<x-app-layout>
    @section('title')
        Add Permission to Role
    @endsection

    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12 ">
            <h4 class="main-title">Add Permission to Role</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a class="f-s-14 f-w-500" href="{{ route('roles.index') }}">
                        <i class="ph-duotone ph ph-address-book f-s-16"></i> Role List
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="#">Add Permission to Role</a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <h3 class="mb-0 px-4 py-2 bg-info text-white rounded">
                Add Permission to Role: <span class="fw-bold">{{ $role->name }}</span>
            </h3>
            <a href="{{ route('roles.index') }}" class="btn btn-info btn-sm text-white ms-3 rounded-md">
                <i class="ph ph-arrow-left"></i> Back to Role List
            </a>
        </div>
    </div>
    <!-- Permission Cards -->
    <form id="permissionForm" method="POST" action="{{ route('roles.give-permission', $role->id) }}">
        @csrf
        <div class="row">
            @php
                $permissionsByLastWord = [];
                foreach ($permissions as $permission) {
                    $words = explode(' ', $permission->name);
                    $lastWord = end($words);
                    if (!isset($permissionsByLastWord[$lastWord])) {
                        $permissionsByLastWord[$lastWord] = [];
                    }
                    $permissionsByLastWord[$lastWord][] = $permission;
                }
            @endphp
            @foreach ($permissionsByLastWord as $lastWord => $permissionsGroup)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light-primary d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">Permission: {{ ucfirst($lastWord) }}</h6>
                            <div class="form-check ms-2">
                                <input class="form-check-input check-all-group" type="checkbox"
                                    id="check-all-{{ Str::slug($lastWord) }}"
                                    data-group="group-{{ Str::slug($lastWord) }}">
                                <label class="form-check-label small"
                                    for="check-all-{{ Str::slug($lastWord) }}">All</label>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($permissionsGroup as $permission)
                                <div class="form-check mb-2">
                                    <input class="form-check-input group-{{ Str::slug($lastWord) }}" type="checkbox"
                                        name="permissions[]" id="perm-{{ $permission->id }}"
                                        value="{{ $permission->name }}"
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-center" style="margin-bottom: 50px">
                <button type="submit" class="btn btn-primary text-2xl fw-bold text-white">Update Permissions</button>
            </div>
        </div>
    </form>



    @push('scripts')
        <!-- SweetAlert -->
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
            // Check All per group
            $(document).ready(function() {
                $('.check-all-group').on('change', function() {
                    const group = $(this).data('group');
                    $('.' + group).prop('checked', this.checked);
                });
                // If all in group checked, check 'all', else uncheck
                $('input[type=checkbox][name="permissions[]"]').on('change', function() {
                    const classes = $(this).attr('class');
                    if (!classes) return;
                    const groupClass = classes.split(' ').find(c => c.startsWith('group-'));
                    if (!groupClass) return;
                    const groupCheckboxes = $('.' + groupClass);
                    const allChecked = groupCheckboxes.length === groupCheckboxes.filter(':checked').length;
                    $('#check-all-' + groupClass.replace('group-', '')).prop('checked', allChecked);
                });
                // On page load, set 'all' if all in group checked
                $('.check-all-group').each(function() {
                    const group = $(this).data('group');
                    const groupCheckboxes = $('.' + group);
                    const allChecked = groupCheckboxes.length === groupCheckboxes.filter(':checked').length;
                    $(this).prop('checked', allChecked);
                });
                // Show success/error after permission update (if needed)
                $('#permissionForm').on('submit', function(e) {
                    e.preventDefault();
                    $.post($(this).attr('action'), $(this).serialize())
                        .done(function(res) {
                            successMessage('Permissions updated!');
                        })
                        .fail(function(xhr) {
                            errorMessage('Failed to update permissions');
                        });
                });
            });
        </script>
    @endpush
</x-app-layout>
