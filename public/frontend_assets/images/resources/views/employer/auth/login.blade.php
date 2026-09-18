@extends('layout.frontend.app')
@section('title', 'Employer Login')

@section('content')
    <link rel="stylesheet" href="{{ asset('employe_assets/css/style.css') }}">

    <style>
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 4px;
            display: block;
        }

        .main-wrapper {
            margin-top: 0;
            margin-bottom: 0;
        }

        .section-registrations {
            height: 100vh;
        }

        .form-row-custom {
            gap: 0px;
        }

        .right-panel {
            padding: 0;
        }

        .right-top-panels {
            padding: 46px 10px 46px 30px;
        }

        .left-panel {
            min-height: 532px;
        }

        .right-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            flex-direction: column;
        }

     
    </style>

    <section class="section-registrations">

        {{-- TOP BACKGROUND IMAGES --}}
        <img src="{{ asset('employe_assets/images/top-right-1.png') }}" class="top-right-one">
        <div class="div-tops">
            <img src="{{ asset('employe_assets/images/top-left.png') }}">
        </div>

        <div class="container">
            <div class="main-wrapper">
                <div class="registration-container">

                    {{-- LEFT PANEL --}}
                    <div class="left-panel right-login">
                        <img src="{{ asset('employe_assets/images/top-1.png') }}" class="top-1">

                        <div class="left-content">
                            <h1 class="main-title">Employer Log In</h1>
                            <div class="title-line"></div>
                            <p class="description-text">
                                Log In to your employer account and take control of your recruitment process.
                            </p>
                        </div>

                        <div class="login-section left-content">
                            <h4>Not yet registered? Create an account</h4>
                            <a href="{{ route('employer.register.view') }}" class="btn-white-login">
                                <span>Register</span>
                                <img src="{{ asset('employe_assets/images/Login.png') }}">
                            </a>
                        </div>

                        <img src="{{ asset('employe_assets/images/bottom-1.png') }}" class="bottom-1">
                    </div>

                    {{-- RIGHT PANEL --}}
                    <div class="right-top-panels">
                        <div class="right-panel panel-logina">

                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- General Message Display --}}
                            <div class="msg-100s">
                                <p id="otpResult"></p>
                            </div>

                            <form id="loginForm" class="form-logins">
                                @csrf

                                <div class="div-fomr-only">
                                    {{-- PHONE --}}
                                    <div class="form-row-custom full-width">
                                        <label class="form-label-custom">Mobile Number</label>
                                        <div class="mobile-otp-group">
                                            <input type="tel" id="phone" name="phone" class="form-input"
                                                placeholder="Enter mobile number" maxlength="10"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                                            <button type="button" id="sendOtpBtn" class="btn-send-otp">
                                                <span id="otpBtnText">Send OTP</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- OTP --}}
                                    <div class="form-row-custom full-width">
                                        <label class="form-label-custom">Enter OTP</label>
                                        <input type="text" id="otp" name="otp" class="form-input"
                                            placeholder="Enter 6-digit OTP" maxlength="6"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                    </div>
                                </div>

                                {{-- LOGIN BUTTON --}}
                                <button type="submit" id="loginBtn" class="btn-register-submit">
                                    <span>Log In</span>
                                    <img src="{{ asset('employe_assets/images/register-now.png') }}">
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- BOTTOM IMAGES --}}
        <div class="div-tops2">
            <img src="{{ asset('employe_assets/images/logo-bottom.png') }}" class="logo-botom">
            <img src="{{ asset('employe_assets/images/contractor 1.png') }}" class="contacts">
        </div>

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
                otpResult.className = 'green-phone-msg';
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

                        if (!otpVerified) {
                            sendOtpBtn.disabled = false;
                            otpBtnText.innerText = "Resend OTP";
                        }
                    }
                }, 1000);
            }

            function startOtpExpiryTimer() {
                if (otpExpiryTimer) {
                    clearTimeout(otpExpiryTimer);
                }

                // OTP expires in 5 minutes (300 seconds) for login
                otpExpiryTimer = setTimeout(() => {
                    if (!otpVerified) {
                        clearMessage();
                        showErrorMessage("OTP expired. Please request a new OTP.");
                        otpInput.value = '';

                        clearInterval(resendTimer);
                        resendTimer = null;
                        sendOtpBtn.disabled = false;
                        otpBtnText.innerText = "Resend OTP";
                    }
                }, 300000); // 5 minutes
            }

            // SEND OTP (Login)
            sendOtpBtn.onclick = function() {
                if (isProcessing) return;

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

                sendOtpBtn.disabled = true;
                otpBtnText.innerText = "Sending...";

                fetch("{{ route('employer.send-login-otp') }}", {
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
                        clearMessage();

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

                fetch("{{ route('employer.verify-login-otp') }}", {
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

                            if (otpExpiryTimer) {
                                clearTimeout(otpExpiryTimer);
                            }
                        } else {
                            showErrorMessage(data.message || "Invalid OTP");
                            otpInput.focus();
                        }
                    })
                    .catch(err => {
                        showErrorMessage("Error verifying OTP");
                    })
                    .finally(() => {
                        isProcessing = false;
                    });
            });

            // Monitor phone number changes
            phone.addEventListener("input", function() {
                if (otpVerified && phone.value !== verifiedPhone) {
                    showErrorMessage("Phone number changed. Please verify with new OTP.");
                    otpVerified = false;
                    verifiedPhone = null;
                    otpInput.value = "";
                    otpInput.readOnly = false;

                    if (!resendTimer) {
                        sendOtpBtn.disabled = false;
                        otpBtnText.innerText = "Send OTP";
                    }

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

                loginBtn.disabled = true;
                let originalText = loginBtn.textContent;
                loginBtn.textContent = "Processing...";

                fetch("{{ route('employer.login.submit') }}", {
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
                            showErrorMessage(data.message || "Login failed. Please try again.");
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