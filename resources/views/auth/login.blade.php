<!DOCTYPE html>
<html lang="en">
@include('layout.headerAssets')

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
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="cb1"
                                                    name="remember">
                                                <label class="custom-control-label" for="cb1">Remember me</label>
                                            </div>
                                        </div>
                                        <div class="col-6 text-end">
                                            <a class="forgot-link" href="{{ route('password.request') }}">Forgot
                                                Password?</a>
                                        </div>
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

    </div>


    @include('layout.footerjs')
</body>

</html>
