<nav class="semi-nav dark-sidebar selected">
    <div class="app-logo">
        <a class="logo d-inline-block" href="{{ route('dashboard') }}">
            <img alt="#" src="{{ asset('assets') }}/images/logo/logohitam.png"></a>
        <span class="bg-light-primary toggle-semi-nav">
            <i class="ti ti-chevrons-right f-s-20"></i>
        </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
            @canany(['view dashboard', 'view customer dashboard', 'view bg dashboard'])
            <li class="menu-title text-white"><span>Dashboards</span></li>

            <li class="no-sub">
                <a href="{{ route('dashboard') }}">
                    <i class="iconoir-home"></i> Main Dashboard
                </a>
            </li>
            @endcanany

            @can('view dashboard area')
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#dashboards-menu">
                    <i class="iconoir-graph-up"></i> Dashboards Area
                </a>
                <ul class="collapse" id="dashboards-menu">
                    <li><a href="{{ route('dashboard.customer') }}">Customer Dashboard</a></li>
                    <li><a href="{{ route('dashboard.bg') }}">Bank Garansi Dashboard</a></li>
                    <li><a href="{{ route('dashboard.logistic') }}">Logistic Dashboard</a></li>
                </ul>
            </li>
            @endcan
            @can('view master data menu')
            <li class="menu-title text-white"><span>Master Data</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#master-data">
                    <i class="iconoir-database"></i> Master Data
                </a>
                <ul class="collapse" id="master-data">
                    <li><a href="{{ route('users.index') }}">Users</a></li>
                    <li><a href="{{ route('departments.index') }}">Department</a></li>
                    <li><a href="{{ route('positions.index') }}">Position</a></li>
                    <li><a href="{{ route('permissions.index') }}">Permission</a></li>
                    <li><a href="{{ route('roles.index') }}">Role</a></li>
                </ul>
            </li>

            @endcan

            @can('view master management menu')

            <li class="menu-title text-white"><span>Master Management</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#master-management">
                    <i class="iconoir-settings"></i> Master Management
                </a>
                <ul class="collapse" id="master-management">
                    <li><a href="{{ route('approval.path') }}">Approval Path</a></li>
                    <li><a href="{{ route('system-logs.index') }}">System Logs</a></li>
                    <li><a href="{{ route('revision.index') }}">Revision</a></li>
                    <li><a href="{{ route('account-groups.index') }}">Account Group</a></li>
                    <li><a href="{{ route('regions.index') }}">Region</a></li>
                    <li><a href="{{ route('branches.index') }}">Branch</a></li>
                    <li><a href="{{ route('sales.index') }}">Sales</a></li>
                    <li><a href="{{ route('tops.index') }}">TOP</a></li>
                    <li><a href="{{ route('customer-classes.index') }}">Customer Class</a></li>
                    <li><a href="{{ route('tax.index') }}">BG Tax</a></li>
                    <li><a href="{{ route('limit-rules.index') }}">BG Limit Rules</a></li>
                    <li><a href="{{ route('distributors.index') }}">Distributor</a></li>
                    <li><a href="{{ route('customer-ship-tos.index') }}">Customer Ship To</a></li>
                </ul>
            </li>
            @endcan

            @can('view logistic fees menu')
            <li class="menu-title text-white"><span>Pengajuan dan Perubahan Harga</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#logistic-fees-menu">
                    <i class="iconoir-community"></i> Logistic Fee
                </a>
                <ul class="collapse" id="logistic-fees-menu">
                    <li><a href="{{ route('logistic-fees.index') }}">Logistic Fee List</a></li>
                    <li><a href="{{ route('logistic-fees.approval.list') }}">Approvals List</a></li>
                    <li><a href="{{ route('logistic-fees.log') }}">Logistic Fee Logs</a></li>
                </ul>
            </li>
            @endcan

            @can('view logistic-orders menu')
            <li class="menu-title text-white"><span>Logistic Orders</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#logistic-orders-menu">
                    <i class="iconoir-community"></i> Logistic Orders
                </a>
                <ul class="collapse" id="logistic-orders-menu">
                    <li><a href="{{ route('logistic-orders.index') }}">Logistic Order List</a></li>
                </ul>
            </li>
            @endcan

            @can('view customers menu')
            <li class="menu-title text-white"><span>Customers</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#customers-menu">
                    <i class="iconoir-community"></i> Customers
                </a>
                <ul class="collapse" id="customers-menu">
                    <li><a href="{{ route('customers.index') }}">Customer List</a></li>
                    <li><a href="{{ route('customers.approval') }}">Approvals List</a></li>
                    <li><a href="{{ route('customers.log') }}">Customer Logs</a></li>
                </ul>
            </li>
            @endcan

            @can('view bank garansi menu')
            <li class="menu-title text-white"><span>Bank Garansi (BG)</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#bg-menu">
                    <i class="iconoir-bank"></i> Bank Garansi
                </a>
                <ul class="collapse" id="bg-menu">
                    <li><a href="{{ route('bg-list.index') }}">BG List</a></li>
                    <li><a href="{{ route('bg-histories.index') }}">BG Histories</a></li>
                        <li><a href="{{ route('bg-submissions.index') }}">Submissions</a></li>
                    <li><a href="{{ route('bg-recommendations.index') }}">Recommendations</a></li>
                    <li><a href="{{ route('lampiran-d.index') }}">Lampiran D</a></li>
                    <!-- <li><a href="#">Lampiran D Versions</a></li> -->
                    <!-- <li><a href="#">Credit Limits</a></li> -->
                    <li><a href="{{ route('bg-approvals.index') }}">Approvals / Inbox</a></li>
                    <li><a href="{{ route('bg-reports.index') }}">Reports</a></li>
                </ul>
            </li>
            @endcan
        </ul>
    </div>


    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>
