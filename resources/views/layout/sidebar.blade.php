<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>Main</span></li>
                <li class="active">
                    <a href="{{ route('dashboard') }}"><i data-feather="home"></i> <span>Dashboard</span></a>
                </li>

                <li class="submenu">
                    <a href="#"><i data-feather="user"></i> <span> Users</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('create.roles') }}">Create Roles</a></li>
                        <li><a href="{{ route('users.index') }}">User List</a></li>
                        <li><a href="{{ route('upcoming.users') }}">Upcoming users</a></li>
                        <li><a href="{{ route('vendor.application') }}">Vendor Application</a></li>
                        <li><a href="{{ route('vendor.application') }}">Vendor Management</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i data-feather="pie-chart"></i> <span> Reports</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('reports.sales') }}">Sales Report</a></li>
                        <li><a href="{{ route('budgets.index') }}">Budget Report</a></li>
                        <li><a href="{{ route('logistics.index') }}">Logistics Report</a></li>
                        <li><a href="{{ route('reports.invoices') }}">Invoices</a></li>

                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.documents') }}"><i data-feather="file-text"></i>
                        <span>Documents</span></a>
                </li>
                <li class="submenu">
                    <a href="#"><i data-feather="grid"></i> <span>Approvals</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li>
                            <a href="{{ route('approvals.procurement') }}">
                                Procurement
                            </a>
                        </li>

                    </ul>

                </li>



                <li>
                    <a href="{{ route('profile.edit') }}"><i data-feather="settings"></i> <span>Settings</span></a>
                </li>


            </ul>
        </div>
    </div>
</div>
