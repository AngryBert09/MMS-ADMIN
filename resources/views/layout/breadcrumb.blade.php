<div class="page-header">
    <div class="row">
        <div class="col">
            <h3 class="page-title">
                @if (Route::is('users.index'))
                    Users List
                @elseif(Route::is('users.create'))
                    Create User
                @elseif(Route::is('users.edit'))
                    Edit User
                @elseif(Route::is('create.roles'))
                    Create Role
                @elseif(Route::is('upcoming.users'))
                    Upcoming users
                @elseif(Route::is('vendor.application'))
                    Vendor Application
                @else
                    Basic Inputs
                @endif
            </h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                @if (Route::is('users.index'))
                    <li class="breadcrumb-item active">Users</li>
                @elseif(Route::is('users.create'))
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Create User</li>
                @elseif(Route::is('users.edit'))
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                @elseif(Route::is('create.roles'))
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Create Role</li>
                @elseif(Route::is('upcoming.users'))
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Upcoming Users </li>
                @elseif(Route::is('vendor.application'))
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Vendor Application</li>
                @else
                    <li class="breadcrumb-item active">UNINDENTIFIED</li>
                @endif
            </ul>
        </div>
    </div>
</div>
