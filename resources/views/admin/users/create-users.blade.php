<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')
        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                @include('layout.breadcrumb')

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Basic Inputs</h5>
                            </div>
                            <div class="card-body">
                                @include('admin.users.message')
                                <form
                                    action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}"
                                    method="POST">
                                    @csrf
                                    @if (isset($user))
                                        @method('PUT')
                                    @endif

                                    <!-- Name Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Name</label>
                                        <div class="col-md-10">
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Enter full name"
                                                value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                                        </div>
                                    </div>

                                    <!-- Email Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Email</label>
                                        <div class="col-md-10">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Enter email"
                                                value="{{ old('email', isset($user) ? $user->email : '') }}"
                                                {{ isset($user) ? 'readonly' : '' }} required>
                                        </div>
                                    </div>

                                    <!-- Roles Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Role</label>
                                        <div class="col-md-10">
                                            <select name="role" class="form-select" required>
                                                <option value="">-- Select Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ old('role', isset($user) ? $user->role : '') == $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <!-- Password Field (Only for Non-Admin & Non-HR) -->
                                    <div class="form-group row" id="passwordField" style="display: none;">
                                        <label class="col-form-label col-md-2">Password</label>
                                        <div class="col-md-10">
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Enter password">
                                        </div>
                                    </div>

                                    <!-- Status Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Status</label>
                                        <div class="col-md-10">
                                            <select name="status" class="form-select" required>
                                                <option value="active"
                                                    {{ old('status', isset($user) ? $user->status : '') == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', isset($user) ? $user->status : '') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Submit & Cancel Buttons -->
                                    <div class="form-group row">
                                        <div class="col-md-10 offset-md-2">
                                            <button type="submit"
                                                class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('layout.footerjs')
</body>

</html>
