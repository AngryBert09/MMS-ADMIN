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
                            We have sent a 6-digit verification code to your email or phone.
                        </p>

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
                                Didn't receive a code? <a href="#" id="resendCode">Resend</a>
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

                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    let code = document.getElementById('otp').value;

                    // Show loading spinner
                    verifyText.classList.add('d-none');
                    verifySpinner.classList.remove('d-none');
                    verifyBtn.disabled = true;

                    fetch("", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                otp: code
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Hide spinner
                            verifyText.classList.remove('d-none');
                            verifySpinner.classList.add('d-none');
                            verifyBtn.disabled = false;

                            if (data.success) {
                                responseMessage.innerHTML =
                                    `<span class="text-success">${data.message}</span>`;
                                setTimeout(() => {
                                    window.location.href = "{{ route('dashboard') }}";
                                }, 1500);
                            } else {
                                responseMessage.innerHTML =
                                    `<span class="text-danger">${data.message}</span>`;
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            responseMessage.innerHTML =
                                `<span class="text-danger">An error occurred. Please try again.</span>`;
                            verifyText.classList.remove('d-none');
                            verifySpinner.classList.add('d-none');
                            verifyBtn.disabled = false;
                        });
                });

                // Handle Resend Code
                document.getElementById('resendCode').addEventListener('click', function(event) {
                    event.preventDefault();
                    responseMessage.innerHTML = `<span class="text-info">Resending code...</span>`;

                    fetch("", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            responseMessage.innerHTML = `<span class="text-success">${data.message}</span>`;
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            responseMessage.innerHTML =
                                `<span class="text-danger">Failed to resend code. Try again later.</span>`;
                        });
                });
            });
        </script>



    </div>


    @include('layout.footerjs')
</body>

</html>
