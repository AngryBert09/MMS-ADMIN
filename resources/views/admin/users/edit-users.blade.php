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

                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Name Field (optional) -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Name</label>
                                        <div class="col-md-10">
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Enter full name"
                                                value="{{ old('name', $user->name ?? '') }}">
                                        </div>
                                    </div>

                                    <!-- Email Field (readonly to allow submission) -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Email</label>
                                        <div class="col-md-10">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Enter email" value="{{ old('email', $user->email ?? '') }}"
                                                disabled>
                                        </div>
                                    </div>

                                    <!-- Role Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Role</label>
                                        <div class="col-md-10">
                                            <select name="role" class="form-select">
                                                <option value="">-- Select Role --</option>
                                                <option value="admin"> Admin </option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ old('role', $user->role ?? '') == $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Password Toggle Checkbox -->
                                    <div class="form-group row">
                                        <div class="col-md-10 offset-md-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="changePasswordCheckbox">
                                                <label class="form-check-label" for="changePasswordCheckbox">Change
                                                    Password?</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Fields (hidden by default) -->
                                    <div id="passwordFields" style="display: none;">
                                        <div class="form-group row">
                                            <label class="col-form-label col-md-2">Password</label>
                                            <div class="col-md-10">
                                                <div class="input-group">
                                                    <input type="password" name="password" class="form-control"
                                                        id="passwordInput" placeholder="Enter new password"
                                                        autocomplete="new-password">
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
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control" id="confirmPasswordInput"
                                                        placeholder="Confirm password">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="togglePassword('confirmPasswordInput', 'togglePasswordIcon2')">
                                                        <i id="togglePasswordIcon2" class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Field (optional) -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Status</label>
                                        <div class="col-md-10">
                                            <select name="status" class="form-select">
                                                <option value="active"
                                                    {{ old('status', $user->status ?? '') == 'Active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $user->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Submit & Cancel Buttons -->
                                    <div class="form-group row mt-3">
                                        <div class="col-md-10 offset-md-2">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>

                                <script>
                                    // Toggle password visibility
                                    function togglePassword(inputId, iconId) {
                                        const input = document.getElementById(inputId);
                                        const icon = document.getElementById(iconId);
                                        input.type = input.type === "password" ? "text" : "password";
                                        icon.classList.toggle("fa-eye");
                                        icon.classList.toggle("fa-eye-slash");
                                    }

                                    // Toggle password fields based on checkbox
                                    document.getElementById('changePasswordCheckbox').addEventListener('change', function() {
                                        document.getElementById('passwordFields').style.display = this.checked ? 'block' : 'none';
                                    });
                                </script>



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
