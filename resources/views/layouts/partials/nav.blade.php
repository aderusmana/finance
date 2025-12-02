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
            <li class="menu-title"><span>Master Data</span></li>
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

            <li class="menu-title"><span>Master Management</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#master-management">
                    <i class="iconoir-settings"></i> Master Management
                </a>
                <ul class="collapse" id="master-management">
                    <li><a href="{{ route('approval.path') }}">Approval Path</a></li>
                    <li><a href="{{ route('revision.index') }}">Revision</a></li>
                    <li><a href="{{ route('account-groups.index') }}">Account Group</a></li>
                    <li><a href="{{ route('regions.index') }}">Region</a></li>
                    <li><a href="{{ route('branches.index') }}">Branch</a></li>
                    <li><a href="{{ route('sales.index') }}">Sales</a></li>
                    <li><a href="{{ route('tops.index') }}">TOP</a></li>
                    <li><a href="{{ route('customer-classes.index') }}">Customer Class</a></li>

                </ul>
            </li>

            <li class="menu-title"><span>Bank Garansi (BG)</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#bg-menu">
                    <i class="iconoir-bank"></i> Bank Garansi
                </a>
                <ul class="collapse" id="bg-menu">
                    <li><a href="#">BG List</a></li>
                    <li><a href="#">BG Histories</a></li>
                    <li><a href="#">Submissions</a></li>
                    <li><a href="#">Recommendations</a></li>
                    <li><a href="#">Lampiran D Overview</a></li>
                    <li><a href="#">Lampiran D Versions</a></li>
                    <li><a href="#">Credit Limits</a></li>
                    <li><a href="#">Approvals / Inbox</a></li>
                    <li><a href="#">Reports</a></li>
                </ul>
            </li>

            <li class="menu-title"><span>Customers</span></li>
            <li>
                <a aria-expanded="false" data-bs-toggle="collapse" href="#customers-menu">
                    <i class="iconoir-community"></i> Customers
                </a>
                <ul class="collapse" id="customers-menu">
                    <li><a href="{{ route('customers.index') }}">List</a></li>
                    <li><a href="#">New Customer</a></li>
                </ul>
            </li>
        </ul>
    </div>


    <div class="menu-navs">
        <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
        <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
    </div>

</nav>
