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
                                <h5 class="card-title">Create Role</h5>
                            </div>
                            <div class="card-body">
                                @include('admin.users.message')
                                <form action="{{ route('add-role') }}" method="POST">
                                    @csrf

                                    <!-- Role Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Role</label>
                                        <div class="col-md-10 d-flex align-items-center">
                                            <!-- Role Select -->
                                            <select name="role" class="form-select me-2" id="roleSelect"
                                                onchange="toggleNewRoleField()">
                                                <option value="">-- Select Existing Role --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->name }}"
                                                        {{ old('role', $user->role ?? '') == $role->name ? 'selected' : '' }}>
                                                        {{ ucfirst($role->name) }}
                                                    </option>
                                                @endforeach
                                                <option value="custom">Other (Enter New Role)</option>
                                            </select>

                                            <!-- New Role Input (Initially Hidden) -->
                                            <input type="text" name="name" id="newRoleInput" class="form-control"
                                                placeholder="Enter new role" style="display: none;">
                                        </div>
                                    </div>

                                    <!-- Status Field -->
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Status</label>
                                        <div class="col-md-10">
                                            <select name="status" class="form-select" required>
                                                <option value="active"
                                                    {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Submit & Cancel Buttons -->
                                    <div class="form-group row">
                                        <div class="col-md-10 offset-md-2">
                                            <button type="submit" class="btn btn-primary">Create</button>
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </form>

                                <script>
                                    function toggleNewRoleField() {
                                        let roleSelect = document.getElementById("roleSelect");
                                        let newRoleInput = document.getElementById("newRoleInput");
                                        newRoleInput.style.display = (roleSelect.value === "custom") ? "block" : "none";
                                    }
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
