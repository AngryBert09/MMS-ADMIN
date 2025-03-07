<!DOCTYPE html>
<html lang="en">
@include('layout.headerAssets')

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Large Modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">📢 System Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </button>
            </div>
            <div class="modal-body">
                <h4>🔹 API Updates & Improvements</h4>
                <ul>
                    {{-- <li>🔄 <strong>Improved Authentication</strong>: Enhanced user authentication with Laravel Sanctum.
                    </li> --}}
                    {{-- <li>📡 <strong>Secure API Access</strong>: Users can now generate long-term API tokens for seamless
                        integration.</li> --}}
                    <li>🚀 <strong>Performance Boost</strong>: Optimized database queries for faster response times.
                    </li>
                </ul>
                <p>For API documentation, please contact the system administrator.</p>

                <hr>

                <h4>🔹 User Roles & Permissions</h4>
                <ul>
                    <li>👑 <strong>Admin</strong>: Full access to the system, including user management and financial
                        transactions.</li>
                    <li>🛍 <strong>Vendor</strong>: Manages their own listings, transactions, and customer interactions.
                    </li>
                    <li>👤 <strong>Employee</strong>: Standard account for booking and interacting with services.</li>
                    <li>📂 <strong>HR</strong>: Manages employee records and internal administration.</li>
                </ul>

                <hr>

                <h4>🔹 Account Statuses</h4>
                <ul>
                    <li>✅ <strong>Active</strong>: The account is fully operational and can log in.</li>
                    <li>⛔ <strong>Inactive</strong>: The account is restricted; please contact support for activation.
                    </li>
                </ul>

                <hr>

                <h4>🔹 Default Password Policy</h4>
                <p>Newly created accounts are assigned a default password based on their role:</p>
                <ul>
                    <li>🔑 <strong>Admin</strong> → <code>#adminGWA</code></li>
                    <li>🔑 <strong>Vendor</strong> → <code>#vendorGWA</code></li>
                    <li>🔑 <strong>Employee</strong> → <code>#yourlastnameGWA</code></li>
                    <li>🔑 <strong>HR</strong> → <code>#hrGWA</code></li>
                </ul>
                <p>💡 <strong>For security reasons, please change your password immediately after logging in.</strong>
                </p>

                <hr>

                <p>If you have any questions or require assistance, feel free to contact support. Thank you for being
                    part of <strong>GreatWallArts</strong>! 🎨✨</p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
        announcementModal.show();
    });
</script>



<body>
    <div class="main-wrapper login-body">
        <div class="login-wrapper">
            <div class="container">
                <img class="img-fluid logo-dark mb-1" src="{{ asset('img/greatwall-logo.png') }}" alt="Logo">
                <div class="loginbox">
                    <div class="login-right">
                        <div class="login-right-wrap">
                            <h1>Login</h1>
                            <p class="account-subtitle">Welcome to GreatWallArts</p>

                            <!-- Login Form -->
                            <form action="{{ route('auth.login.post') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label class="form-control-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Password</label>
                                    <div class="pass-group">
                                        <input type="password" class="form-control pass-input" name="password" required>
                                        <span class="fas fa-eye toggle-password" onclick="togglePassword()"></span>
                                    </div>
                                </div>

                                <!-- Google reCAPTCHA -->
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITE_KEY') }}">
                                    </div>
                                </div>

                                <!-- Login Button -->
                                <button class="btn btn-lg btn-block btn-warning w-100 rounded-3"
                                    type="submit">Login</button>
                            </form>

                            <!-- Display Errors -->
                            @if ($errors->any())
                                <div class="alert alert-danger mt-3">
                                    @foreach ($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load reCAPTCHA Script -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    </div>


    @include('layout.footerjs')
</body>

</html>
