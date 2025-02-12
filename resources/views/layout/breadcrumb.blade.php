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
                @else
                    <li class="breadcrumb-item active">Basic Inputs</li>
                @endif
            </ul>
        </div>
    </div>
</div>
