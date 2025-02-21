<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')


        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="page-title">Settings</h3>
                            <ul class="breadcrumb">

                                </li>
                                <li class="breadcrumb-item active">Change Password</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-md-4">

                        <div class="widget settings-menu">
                            <ul>
                                <li class="nav-item">
                                    <a href="{{ route('profile.edit') }}" class="nav-link">
                                        <i class="far fa-user"></i> <span>Profile Settings</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('profile.change-pass') }}" class="nav-link active">
                                        <i class="fas fa-unlock-alt"></i> <span>Change Password</span>
                                    </a>
                                </li>

                            </ul>
                        </div>

                    </div>
                    <div class="col-xl-9 col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Change Password</h5>
                            </div>
                            <div class="card-body">
                                @include('profile.message')
                                <form action="{{ route('profile.change-password') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="row form-group">
                                        <label for="current_password"
                                            class="col-sm-3 col-form-label input-label">Current Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="current_password"
                                                name="current_password" placeholder="Enter current password">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label for="new_password" class="col-sm-3 col-form-label input-label">New
                                            Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" placeholder="Enter new password">
                                            <div class="progress progress-md mt-2">
                                                <div id="passwordProgress" class="progress-bar" role="progressbar"
                                                    style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Listen to input events on the new password field
                                        document.getElementById('new_password').addEventListener('input', function() {
                                            var password = this.value;
                                            var progressBar = document.getElementById('passwordProgress');

                                            // Calculate strength percentage (max 100)
                                            var strength = calculatePasswordStrength(password);
                                            progressBar.style.width = strength + '%';
                                            progressBar.setAttribute('aria-valuenow', strength);

                                            // Update progress bar color based on strength
                                            if (strength < 40) {
                                                progressBar.classList.remove('bg-warning', 'bg-success');
                                                progressBar.classList.add('bg-danger');
                                            } else if (strength < 70) {
                                                progressBar.classList.remove('bg-danger', 'bg-success');
                                                progressBar.classList.add('bg-warning');
                                            } else {
                                                progressBar.classList.remove('bg-danger', 'bg-warning');
                                                progressBar.classList.add('bg-success');
                                            }
                                        });

                                        // Function to calculate password strength
                                        function calculatePasswordStrength(password) {
                                            var strength = 0;

                                            // Check password length: 25% for 8 or more characters
                                            if (password.length >= 8) strength += 25;

                                            // Check for lowercase letters: 15%
                                            if (/[a-z]/.test(password)) strength += 15;

                                            // Check for uppercase letters: 20%
                                            if (/[A-Z]/.test(password)) strength += 20;

                                            // Check for digits: 20%
                                            if (/\d/.test(password)) strength += 20;

                                            // Check for symbols: 20%
                                            if (/[\W]/.test(password)) strength += 20;

                                            // Return strength percentage, capped at 100%
                                            return Math.min(strength, 100);
                                        }
                                    </script>

                                    <div class="row form-group">
                                        <label for="confirm_password"
                                            class="col-sm-3 col-form-label input-label">Confirm new password</label>
                                        <div class="col-sm-9">
                                            <div class="mb-3">
                                                <input type="password" class="form-control" id="confirm_password"
                                                    name="confirm_password" placeholder="Confirm your new password">
                                            </div>
                                            <h5>Password requirements:</h5>
                                            <p class="mb-2">Ensure that these requirements are met:</p>
                                            <ul class="list-unstyled small">
                                                <li>Minimum 8 characters long - the more, the better</li>
                                                <li>At least one lowercase character</li>
                                                <li>At least one uppercase character</li>
                                                <li>At least one number, symbol</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-warning">Save Changes</button>
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
