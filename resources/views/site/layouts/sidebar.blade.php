<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo-small">
            <img src="{{ asset('assets/img/logo-small.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="dark-logo">
            <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Logo">
        </a>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>MAIN MENU</span></li>

                <!-- Dashboard - Always visible to authenticated users -->
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ti ti-smart-home" style="margin-right: 10px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Access Control Menu - Check if user has access control permissions -->
                @canany(['staff-create', 'staff-list', 'role-create', 'role-list'])
                    <li class="submenu">
                        <a href="#">
                            <i class="ti ti-lock" style="margin-right: 10px;"></i><span>Access Control</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Staff Submenu -->
                            @canany(['staff-create', 'staff-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-user"></i><span>Staff</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.staff-*') ? 'display:block;' : ''}}">
                                        @can('staff-create')
                                            <li><a href="{{ route('admin.staff-create') }}"
                                                    class="{{ request()->routeIs('admin.staff-create') || request()->routeIs('admin.staff-edit') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan
                                        @can('staff-list')
                                            <li><a href="{{ route('admin.staff-list') }}"
                                                    class="{{ request()->routeIs('admin.staff-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Role Master Submenu -->
                            @canany(['role-create', 'role-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-layout-board-split"></i><span>Role Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.role-*') ? 'display:block;' : ''}}">
                                        @can('role-create')
                                            <li><a href="{{ route('admin.role-create') }}"
                                                    class="{{ request()->routeIs('admin.role-create') || request()->routeIs('admin.role-edit') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('role-list')
                                            <li><a href="{{ route('admin.role-list') }}"
                                                    class="{{ request()->routeIs('admin.role-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Loan Assign Menu -->
                @canany(['loanassign-create', 'loanassign-list'])
                    <li class="submenu {{ request()->routeIs('admin.loan-assign.*') ? 'active ' : '' }}">
                        <a href="javascript:void(0);"
                            class="{{ request()->routeIs('admin.loan-assign.*') ? 'active' : '' }}">
                            <i class="ti ti-layout-grid-add" style="margin-right: 10px;"></i><span>Loan Assign</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="{{ request()->routeIs('admin.loan-assign.*') ? 'display: block;' : '' }}">
                            @can('loanassign-create')
                                <li>
                                    <a href="{{ route('admin.loan-assign-create') }}"
                                        class="{{ request()->routeIs('admin.loan-assign-create') || request()->routeIs('admin.loan-assign-edit') ? 'active' : '' }}">
                                        Create
                                    </a>
                                </li>
                            @endcan

                            @can('loanassign-list')
                                <li>
                                    <a href="{{ route('admin.loan-assign-list') }}"
                                        class="{{ request()->routeIs('admin.loan-assign-list') ? 'active' : '' }}">
                                        List
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Customer Menu -->
                @canany(['customer-create', 'customer-list'])
                    <li class="submenu {{ request()->routeIs('admin.customer.*') ? 'active ' : '' }}">
                        <a href="#" class="{{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                            <i class="ti ti-user-star" style="margin-right: 10px;"></i><span>Customer</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="{{ request()->routeIs('admin.customer.*') ? 'display: block;' : '' }}">
                            @can('customer-create')
                                <li><a href="{{ route('admin.customer-create') }}"
                                        class="{{ request()->routeIs('admin.customer-create') || request()->routeIs('admin.customer-edit') || request()->routeIs('admin.customer-view') ? 'active' : '' }}">Create</a>
                                </li>
                            @endcan

                            @can('customer-list')
                                <li><a href="{{ route('admin.customer-list') }}"
                                        class="{{ request()->routeIs('admin.customer-list') ? 'active' : '' }}">List</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Employee Menu -->
                @canany(['employee-create', 'employee-list'])
                    <li class="submenu {{ request()->routeIs('admin.employee.*') ? 'active ' : '' }}">
                        <a href="#" class="{{ request()->routeIs('admin.employee.*') ? 'active' : '' }}">
                            <i class="ti ti-users" style="margin-right: 10px;"></i><span>Employee</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="{{ request()->routeIs('admin.employee.*') ? 'display: block;' : '' }}">
                            @can('employee-create')
                                <li><a href="{{ route('admin.employee-create') }}"
                                        class="{{ request()->routeIs('admin.employee-create') || request()->routeIs('admin.employee-edit') ? 'active' : '' }}">Create</a>
                                </li>
                            @endcan

                            @can('employee-list')
                                <li><a href="{{ route('admin.employee-list') }}"
                                        class="{{ request()->routeIs('admin.employee-list') ? 'active' : '' }}">List</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Expense Menu -->
                @can('expense-list')
                    <li>
                        <a href="{{ route('admin.expense-list') }}"
                            class="{{ request()->routeIs('admin.expense-*') ? 'active' : '' }}">
                            <i class="ti ti-file-text" style="margin-right: 10px;"></i>
                            <span>Expense</span>
                        </a>
                    </li>
                @endcan

                <!-- Emi collection Menu -->
                @canany(['emicollection-create', 'emicollection-list'])
                    <li class="submenu {{ request()->routeIs('admin.emicollection.*') ? 'active ' : '' }}">
                        <a href="#" class="{{ request()->routeIs('admin.emicollection.*') ? 'active' : '' }}">
                            <i class="ti ti-briefcase" style="margin-right: 10px;"></i><span>Emi Collection</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul style="{{ request()->routeIs('admin.emicollection.*') ? 'display: block;' : '' }}">
                            @can('emicollection-create')
                                <li><a href="{{ route('admin.emicollection-create') }}"
                                        class="{{ request()->routeIs('admin.emicollection-create') || request()->routeIs('admin.emicollection-edit') || request()->routeIs('admin.emicollection-view') ? 'active' : '' }}">Create</a>
                                </li>
                            @endcan

                            @can('emicollection-list')
                                <li><a href="{{ route('admin.emicollection-list') }}"
                                        class="{{ request()->routeIs('admin.emicollection-list') ? 'active' : '' }}">List</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- Master Menu -->
                @canany(['loan-create', 'loan-list', 'interest-create', 'interest-list', 'branch-create', 'branch-list', 'route-create', 'route-list', 'document-create', 'document-list', 'group-create', 'group-list', 'group-view'])
                    <li class="submenu">
                        <a href="#">
                            <i class="ti ti-folders" style="margin-right: 10px;"></i><span>Master</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <!-- Loan Master -->
                            @canany(['loan-create', 'loan-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-layout-board-split"></i><span>Loan Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.loan-*') ? 'display:block;' : ''}}">
                                        @can('loan-create')
                                            <li><a href="{{ route('admin.loan-create') }}"
                                                    class="{{ request()->routeIs('admin.loan-create') || request()->routeIs('admin.loan-edit') || request()->routeIs('admin.loan-view') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('loan-list')
                                            <li><a href="{{ route('admin.loan-list') }}"
                                                    class="{{ request()->routeIs('admin.loan-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Interest Master -->
                            @canany(['interest-create', 'interest-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-timeline"></i><span>Interest Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.interest-*') ? 'display: block;' : '' }}">
                                        @can('interest-create')
                                            <li><a href="{{ route('admin.interest-create') }}"
                                                    class="{{ request()->routeIs('admin.interest-create') || request()->routeIs('admin.interest-edit') || request()->routeIs('admin.interest-view') ? 'active' : '' }}">Create</a>
                                            </li>
                                        @endcan

                                        @can('interest-list')
                                            <li><a href="{{ route('admin.interest-list') }}"
                                                    class="{{ request()->routeIs('admin.interest-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Branch Master -->
                            @canany(['branch-create', 'branch-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-box-multiple"></i><span>Branch Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.branch-*') ? 'display: block;' : '' }}">
                                        @can('branch-create')
                                            <li><a href="{{ route('admin.branch-create') }}"
                                                    class="{{ request()->routeIs('admin.branch-create') || request()->routeIs('admin.branch-edit') || request()->routeIs('admin.branch-view') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('branch-list')
                                            <li><a href="{{ route('admin.branch-list') }}"
                                                    class="{{ request()->routeIs('admin.branch-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Route Master -->
                            @canany(['route-create', 'route-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-file-check"></i><span>Route Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.route-*') ? 'display: block;' : '' }}">
                                        @can('route-create')
                                            <li><a href="{{ route('admin.route-create') }}"
                                                    class="{{ request()->routeIs('admin.route-create') || request()->routeIs('admin.route-edit') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('route-list')
                                            <li><a href="{{ route('admin.route-list') }}"
                                                    class="{{ request()->routeIs('admin.route-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Documents Master -->
                            @canany(['document-create', 'document-list'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-file-check"></i><span>Documents Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.document-*') ? 'display: block;' : '' }}">
                                        @can('document-create')
                                            <li><a href="{{ route('admin.document-create') }}"
                                                    class="{{ request()->routeIs('admin.document-create') || request()->routeIs('admin.document-edit') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('document-list')
                                            <li><a href="{{ route('admin.document-list') }}"
                                                    class="{{ request()->routeIs('admin.document-list') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany

                            <!-- Group Master -->
                            @canany(['group-create', 'group-list', 'group-view'])
                                <li class="submenu">
                                    <a href="#">
                                        <i class="ti ti-users-group"></i><span>Group Master</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul style="{{ request()->routeIs('admin.group-*') ? 'display: block;' : '' }}">
                                        @can('group-create')
                                            <li><a href="{{ route('admin.group-create') }}"
                                                    class="{{ request()->routeIs('admin.group-create') || request()->routeIs('admin.group-edit') ? 'active' : ''}}">Create</a>
                                            </li>
                                        @endcan

                                        @can('group-list')
                                            <li><a href="{{ route('admin.group-list') }}"
                                                    class="{{ request()->routeIs('admin.group-list') || request()->routeIs('admin.group-view') ? 'active' : '' }}">List</a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Report Menu -->
                @canany(['report-daily', 'report-loan','report-total'])
                    <li class="submenu {{ request()->routeIs('admin.report.*') ? 'active ' : '' }}">
                        <a href="#" class="{{ request()->routeIs('admin.report.*') ? 'active' : '' }}">
                            <i class="ti ti-layout-grid-add " style="margin-right: 10px;"></i><span>Report</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <ul style="{{ request()->routeIs('admin.report.*') ? 'display: block;' : '' }}">
                            @can('report-daily')
                                <li>
                                    <a href="{{ route('admin.report-daily')}}"
                                        class="{{ request()->routeIs('admin.report-daily') || request()->routeIs('admin.report-daily.filter') ? 'active' : '' }}">
                                        Daily Report
                                    </a>
                                </li>
                            @endcan
                            @can('report-loan')
                                <li>
                                    <a href="{{ route('admin.report-loan')}}"
                                        class="{{ request()->routeIs('admin.report-loan') ? 'active' : '' }}">
                                        New Loan Collection Report
                                    </a>
                                </li>
                            @endcan

                             @can('report-total')
                                <li>
                                    <a href="{{ route('admin.report-total')}}"
                                        class="{{ request()->routeIs('admin.report-total')  || request()->routeIs('admin.report-total.filter') ? 'active' : '' }}">
                                        Total Report
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

            </ul>
        </div>
    </div>

</div>
<!-- /Sidebar -->
