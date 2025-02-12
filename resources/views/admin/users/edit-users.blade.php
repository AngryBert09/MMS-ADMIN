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
                                <h5 class="card-title">Edit User</h5>
                            </div>
                            <div class="card-body">

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
                                                {{ isset($user) ? 'readonly' : '' }} disabled>
                                        </div>
                                    </div>

                                    <!-- Role Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Role</label>
                                        <div class="col-md-10">
                                            <select name="role" class="form-select">
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

                                    <!-- Password Fields -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Password</label>
                                        <div class="col-md-10">
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control"
                                                    id="passwordInput" placeholder="Enter password">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePassword('passwordInput', 'togglePasswordIcon1')">
                                                    <i id="togglePasswordIcon1" class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Confirm Password</label>
                                        <div class="col-md-10">
                                            <div class="input-group">
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    id="confirmPasswordInput" placeholder="Confirm password">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePassword('confirmPasswordInput', 'togglePasswordIcon2')">
                                                    <i id="togglePasswordIcon2" class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function togglePassword(inputId, iconId) {
                                            let passwordInput = document.getElementById(inputId);
                                            let toggleIcon = document.getElementById(iconId);

                                            if (passwordInput.type === "password") {
                                                passwordInput.type = "text";
                                                toggleIcon.classList.remove("fa-eye");
                                                toggleIcon.classList.add("fa-eye-slash");
                                            } else {
                                                passwordInput.type = "password";
                                                toggleIcon.classList.remove("fa-eye-slash");
                                                toggleIcon.classList.add("fa-eye");
                                            }
                                        }
                                    </script>

                                    <div class="form-group
                                        row">
                                        <label class="col-form-label col-md-2">Status</label>
                                        <div class="col-md-10">
                                            <select name="status" class="form-select" required>
                                                <option value="active"
                                                    {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>
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
