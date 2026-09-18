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

        .success-message {
            color: #28a745;
        }

        .section-registers {
            padding: 110px 0;
        }

        .div-form-loins {
            width: 100%;
            height: 240px;
        }
    </style>

    <section class="section-registers">
        <img src="{{ asset('supervisor_assets/images/grey-logo.png') }}" alt="" class="img-4">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5">
                    <div class="div-inner-registrations">
                        <h1>LogIn Here !!</h1>
                        <p>Access your account by logging in here and continue your journey with full control and complete
                            transparency.</p>
                        <p class="have-an-button">Not yet registered? Create an account <a
                                href="{{ route('supervisor.register.view') }}">Sign Up</a> </p>


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

            function startResendTimer() {
                let seconds = 15;
                sendOtpBtn.disabled = true;

                otpBtnText.innerText = `Resend OTP (${seconds}s)`;

                resendTimer = setInterval(() => {
                    seconds--;
                    otpBtnText.innerText = `Resend OTP (${seconds}s)`;

                    if (seconds <= 0) {
                        clearInterval(resendTimer);
                        sendOtpBtn.disabled = false;
                        otpBtnText.innerText = "Send OTP";
                    }
                }, 1000);
            }

            // SEND OTP
            sendOtpBtn.onclick = function() {
                let mobile = phone.value.trim();

                if (mobile.length !== 10) {
                    otpResult.classList.add("invalid-phone-msg");
                    otpResult.innerHTML = "Enter valid 10-digit mobile number";
                    return;
                }

                otpInput.readOnly = false;
                otpInput.value = "";
                otpVerified = false;

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
                        otpResult.className = data.status ? "otp-success" : "invalid-phone-msg";
                        otpResult.innerHTML = data.message + (data.otp ? " (OTP: " + data.otp + ")" : "");

                        if (!data.status) return;

                        startResendTimer();
                    })
                    .catch(err => {
                        otpResult.classList.add("invalid-phone-msg");
                        otpResult.innerHTML = "Error sending OTP. Please try again.";
                    });
            };

            // VERIFY OTP
            otpInput.addEventListener("keyup", function() {
                if (otpInput.value.length !== 6) return;

                let mobile = phone.value.trim();

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
                        otpResult.className = data.status ? "otp-success" : "invalid-phone-msg";
                        otpResult.innerHTML = data.message;

                        if (data.status) {
                            otpInput.readOnly = true;
                            phone.readOnly = true;
                            clearInterval(resendTimer);
                            sendOtpBtn.disabled = true;
                            otpVerified = true;
                        }
                    })
                    .catch(err => {
                        otpResult.classList.add("invalid-phone-msg");
                        otpResult.innerHTML = "Error verifying OTP";
                    });
            });

            // LOGIN FORM SUBMISSION
            loginForm.addEventListener("submit", function(e) {
                e.preventDefault();

                if (!otpVerified) {
                    otpResult.classList.add("invalid-phone-msg");
                    otpResult.innerHTML = "Please verify your mobile number with OTP first.";
                    otpInput.focus();
                    return false;
                }

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
                        // otpResult.className = data.status ? "otp-success" : "invalid-phone-msg";
                        // otpResult.innerHTML = data.message;

                        if (data.status) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 800);
                        }
                    })
                    .catch(err => {
                        otpResult.classList.add("invalid-phone-msg");
                        otpResult.innerHTML = "Login failed. Please try again.";
                    });
            });

            // Reset OTP verification if phone number changes
            phone.addEventListener("input", function() {
                if (otpVerified) {
                    otpVerified = false;
                    otpInput.value = "";
                    otpResult.innerHTML = "";
                }
            });
        });
    </script>
@endsection
