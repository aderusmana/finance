@can('view dashboard')
    @include('dashboard.dashboard')
@endcan
@can('view bg dashboard')
    @include('dashboard.bg')
@endcan
@can('view customer dashboard')
    @include('dashboard.customer')
@endcan
@can('view logistic dashboard')
    @include('dashboard.logistic')
@endcan