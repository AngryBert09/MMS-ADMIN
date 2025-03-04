<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>
    <div class="main-wrapper">
        <div class="d-flex align-items-center justify-content-center vh-100">
            <div class="col-md-5">
                <div class="card shadow-lg">
                    <div class="card-header text-center text-white">
                        <h4 class="mb-0">Two-Factor Authentication</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">
                            We have sent a 6-digit verification code to your email.
                        </p>

                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <!-- 2FA Code Input -->
                        <form id="twoFactorForm">
                            @csrf
                            <div class="form-group">
                                <label for="otp">Authentication Code</label>
                                <input type="text" id="otp" name="otp" class="form-control text-center"
                                    maxlength="6" placeholder="Enter 6-digit code" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-3" id="verifyBtn"
                                style="width:100%">
                                <span id="verifyText">Verify</span>
                                <span id="verifySpinner" class="spinner-border spinner-border-sm d-none"></span>
                            </button>

                            <p class="text-center mt-3">
                                Didn't receive a code?
                                <button id="resendCode" class="btn btn-link p-0" disabled>Resend (<span
                                        id="countdown">30</span>s)</button>
                            </p>
                        </form>

                        <div id="responseMessage" class="mt-3 text-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let form = document.getElementById('twoFactorForm');
                let verifyBtn = document.getElementById('verifyBtn');
                let verifyText = document.getElementById('verifyText');
                let verifySpinner = document.getElementById('verifySpinner');
                let responseMessage = document.getElementById('responseMessage');
                let resendCodeBtn = document.getElementById('resendCode');
                let countdownSpan = document.getElementById('countdown');
                let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Function to handle countdown for resend
                function startCountdown(duration) {
                    let timeLeft = duration;
                    resendCodeBtn.disabled = true;

                    let countdownInterval = setInterval(() => {
                        timeLeft--;
                        countdownSpan.textContent = timeLeft;
                        if (timeLeft <= 0) {
                            clearInterval(countdownInterval);
                            resendCodeBtn.disabled = false;
                            resendCodeBtn.textContent = "Resend";
                        }
                    }, 1000);
                }

                // Initial 30-second countdown on page load
                startCountdown();

                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    let code = document.getElementById('otp').value;

                    // Show loading spinner
                    verifyText.classList.add('d-none');
                    verifySpinner.classList.remove('d-none');
                    verifyBtn.disabled = true;

                    fetch("/2fa/verify", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                otp: code
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                responseMessage.innerHTML =
                                    `<span class="text-success">${data.message}</span>`;
                                setTimeout(() => window.location.href = "/dashboard", 1500);
                            } else {
                                responseMessage.innerHTML =
                                    `<span class="text-danger">${data.message}</span>`;
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            responseMessage.innerHTML =
                                `<span class="text-danger">An error occurred. Please try again.</span>`;
                        })
                        .finally(() => {
                            verifyText.classList.remove('d-none');
                            verifySpinner.classList.add('d-none');
                            verifyBtn.disabled = false;
                        });
                });

                // Resend OTP logic
                resendCodeBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    resendCodeBtn.disabled = true;
                    resendCodeBtn.textContent = "Sending...";

                    fetch("/2fa/resend", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Content-Type": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                responseMessage.innerHTML =
                                    `<span class="text-success">${data.message}</span>`;
                                startCountdown(30); // Restart countdown after resend
                            } else {
                                responseMessage.innerHTML =
                                    `<span class="text-danger">${data.message}</span>`;
                                resendCodeBtn.disabled = false;
                                resendCodeBtn.textContent = "Resend";
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            responseMessage.innerHTML =
                                `<span class="text-danger">Failed to resend. Try again later.</span>`;
                            resendCodeBtn.disabled = false;
                            resendCodeBtn.textContent = "Resend";
                        });
                });
            });
        </script>
    </div>

    @include('layout.footerjs')
</body>

</html>
