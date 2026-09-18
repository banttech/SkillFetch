@extends('layout.frontend.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/style.css') }}">

    <style>
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        /* .section-registers {
            padding: 110px 0;
        } */

        .div-form-loins {
            width: 100%;
            height: 240px;
        }

        /* Loading Spinner */
        .btn-loading {
            position: relative;
            pointer-events: none;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .otp-success {
            color: #28a745;
        }

        .invalid-phone-msg {
            color: #dc3545;
        }
    </style>

    <section class="section-registers">
        <img src="{{ asset('supervisor_assets/images/grey-logo.png') }}" alt="" class="img-4">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5 bg-bluses">
                    <div class="div-inner-registrations">
                        <h1>LogIn Here !!</h1>
                        <p>Access your account by logging in here and continue your journey with full control and complete
                            transparency.</p>
                        <p class="have-an-button">Not yet registered? Create an account <a
                                href="{{ route('supervisor.register.view') }}">Register</a> </p>


                        <img src="{{ asset('supervisor_assets/images/image-conts.png') }}" alt="register">
                    </div>
                </div>

                <div class="col-md-1"></div>

                <div class="col-md-5">
                    <div class="right-section">
                        <div class="form-container">

                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif


                            <p id="otpResult"></p>

                            <h2>Supervisor Login</h2>

                            <form id="loginForm">
                                @csrf
                                <div class="div-form-loins">
                                    <fieldset class="form-group">
                                        <legend>Mobile</legend>
                                        <div class="input-wrapper">
                                            <div class="mobile-group">
                                                <input type="text" id="phone" class="form-control"
                                                    placeholder="Enter 10-digit mobile number" maxlength="10"
                                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                                                <button class="btn btn-otp" type="button" id="sendOtpBtn"
                                                    data-type="login">
                                                    <span id="otpBtnText">Send OTP</span>
                                                </button>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <input type="text" id="otp" class="form-control"
                                        placeholder="Enter 6-digit OTP" maxlength="6"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                </div>

                                <button type="submit" id="loginBtn" class="btn btn-register">
                                    Log In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <img src="{{ asset('supervisor_assets/images/three-olcor.png') }}" alt="" class="img-3">
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let phone = document.getElementById("phone");
            let otpInput = document.getElementById("otp");
            let sendOtpBtn = document.getElementById("sendOtpBtn");
            let otpResult = document.getElementById("otpResult");
            let loginBtn = document.getElementById("loginBtn");
            let otpBtnText = document.getElementById("otpBtnText");
            let loginForm = document.getElementById("loginForm");

            let resendTimer = null;
            let otpVerified = false;
            let verifiedPhone = null;
            let isProcessing = false;
            let otpExpiryTimer = null;

            function clearMessage() {
                otpResult.textContent = '';
                otpResult.className = '';
            }

            function showErrorMessage(message) {
                otpResult.className = 'invalid-phone-msg';
                otpResult.textContent = message;
            }

            function showSuccessMessage(message) {
                otpResult.className = 'otp-success';
                otpResult.textContent = message;
            }

            function startResendTimer() {
                let seconds = 60;
                sendOtpBtn.disabled = true;
                otpBtnText.innerText = `Resend OTP (${seconds}s)`;

                resendTimer = setInterval(() => {
                    seconds--;
                    otpBtnText.innerText = `Resend OTP (${seconds}s)`;

                    if (seconds <= 0) {
                        clearInterval(resendTimer);
                        resendTimer = null;

                        // If OTP not verified yet, allow resend
                        if (!otpVerified) {
                            sendOtpBtn.disabled = false;
                            otpBtnText.innerText = "Resend OTP";
                        }
                    }
                }, 1000);
            }

            function startOtpExpiryTimer() {
                // Clear any existing expiry timer
                if (otpExpiryTimer) {
                    clearTimeout(otpExpiryTimer);
                }

                // OTP expires in 5 minutes (300 seconds) for login
                otpExpiryTimer = setTimeout(() => {
                    if (!otpVerified) {
                        showErrorMessage("OTP expired. Please request a new OTP.");
                        otpInput.value = '';

                        // Enable resend button if countdown finished
                        if (!resendTimer) {
                            sendOtpBtn.disabled = false;
                            otpBtnText.innerText = "Resend OTP";
                        }
                    }
                }, 300000); // 5 minutes
            }


            // SEND OTP (Login)
            sendOtpBtn.onclick = function() {
                if (isProcessing) return;

                // DON'T clear message to prevent blinking
                // clearMessage();

                let mobile = phone.value.trim();

                if (mobile.length !== 10) {
                    clearMessage();
                    showErrorMessage("Enter valid 10-digit mobile number");
                    phone.focus();
                    return;
                }

                if (otpVerified) {
                    if (confirm("Changing phone number will require new OTP verification. Continue?")) {
                        otpVerified = false;
                        verifiedPhone = null;
                        otpInput.value = "";
                        otpInput.readOnly = false;
                        phone.readOnly = false;

                        if (otpExpiryTimer) {
                            clearTimeout(otpExpiryTimer);
                        }
                    } else {
                        return;
                    }
                }

                isProcessing = true;

                // Show loading text
                sendOtpBtn.disabled = true;
                otpBtnText.innerText = "Sending...";

                fetch("{{ route('supervisor.send.login.otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            phone: mobile
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        clearMessage(); // Clear before showing new message

                        if (data.status) {
                            showSuccessMessage(data.message + (data.otp ? " (OTP: " + data.otp + ")" : ""));
                            phone.readOnly = false;
                            otpInput.readOnly = false;
                            otpInput.focus();
                            startResendTimer();
                            startOtpExpiryTimer();
                        } else {
                            showErrorMessage(data.message || "Failed to send OTP");
                            sendOtpBtn.disabled = false;
                            otpBtnText.innerText = "Send OTP";
                        }
                    })
                    .catch(err => {
                        clearMessage();
                        otpBtnText.innerText = "Send OTP";
                        sendOtpBtn.disabled = false;
                        showErrorMessage("Error sending OTP. Please try again.");
                    })
                    .finally(() => {
                        isProcessing = false;
                    });
            };

            // VERIFY OTP
            otpInput.addEventListener("keyup", function() {
                if (otpInput.value.length !== 6) return;
                if (isProcessing) return;

                let mobile = phone.value.trim();

                isProcessing = true;

                fetch("{{ route('supervisor.verify.login.otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            phone: mobile,
                            otp: otpInput.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            showSuccessMessage(data.message);
                            otpInput.readOnly = true;
                            phone.readOnly = true;
                            clearInterval(resendTimer);
                            resendTimer = null;
                            sendOtpBtn.disabled = true;
                            otpBtnText.innerText = "Verified ✓";
                            otpVerified = true;
                            verifiedPhone = mobile;

                            // Clear expiry timer as OTP is verified
                            if (otpExpiryTimer) {
                                clearTimeout(otpExpiryTimer);
                            }
                        } else {
                            showErrorMessage(data.message || "Invalid OTP");
                            // otpInput.value = '';
                            otpInput.focus();
                        }
                    })
                    .catch(err => {
                        showErrorMessage("Error verifying OTP");
                        // otpInput.value = '';
                    })
                    .finally(() => {
                        isProcessing = false;
                    });
            });

            // Monitor phone number changes
            phone.addEventListener("input", function() {
                if (otpVerified && phone.value !== verifiedPhone) {
                    // Phone changed after verification - show warning
                    showErrorMessage("Phone number changed. Please verify with new OTP.");
                    otpVerified = false;
                    verifiedPhone = null;
                    otpInput.value = "";
                    otpInput.readOnly = false;

                    // Enable send OTP button if countdown finished
                    if (!resendTimer) {
                        sendOtpBtn.disabled = false;
                        otpBtnText.innerText = "Send OTP";
                    }

                    // Clear expiry timer
                    if (otpExpiryTimer) {
                        clearTimeout(otpExpiryTimer);
                    }
                }
            });

            // LOGIN FORM SUBMISSION
            loginForm.addEventListener("submit", function(e) {
                e.preventDefault();

                if (isProcessing) return;

                clearMessage();

                if (!otpVerified) {
                    showErrorMessage("Please verify your mobile number with OTP first.");
                    otpResult.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    otpInput.focus();
                    return false;
                }

                if (phone.value !== verifiedPhone) {
                    showErrorMessage("Phone number was changed. Please verify again.");
                    otpResult.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return false;
                }

                isProcessing = true;

                // Show loading text
                loginBtn.disabled = true;
                let originalText = loginBtn.textContent;
                loginBtn.textContent = "Processing...";

                fetch("{{ route('supervisor.login') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            phone: phone.value
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        loginBtn.textContent = originalText;

                        if (data.status) {
                            // showSuccessMessage(data.message);
                            loginBtn.disabled = true;
                            loginBtn.textContent = "Processing...";

                        
                                window.location.href = data.redirect;
                          
                        } else {
                            loginBtn.disabled = false;
                            // showErrorMessage(data.message || "Login failed. Please try again.");
                            otpResult.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    })
                    .catch(err => {
                        loginBtn.textContent = originalText;
                        loginBtn.disabled = false;
                        showErrorMessage("Login failed. Please try again.");
                        otpResult.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    })
                    .finally(() => {
                        if (isProcessing) {
                            isProcessing = false;
                        }
                    });
            });
        });
    </script>
@endsection
