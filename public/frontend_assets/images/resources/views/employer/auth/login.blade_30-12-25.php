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
                                Sign in to your employer account and take control of your recruitment process
                            </p>
                        </div>

                        <div class="login-section left-content">
                            <h4>Not yet registered? Create an account</h4>
                            <a href="{{ route('employer.register.view') }}" class="btn-white-login">
                                <span>Sign Up</span>
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

                            <form id="employerForm" class="form-logins">
                                @csrf

                                <div class="div-fomr-only">
                                    {{-- PHONE --}}
                                    <div class="form-row-custom full-width">
                                        <label class="form-label-custom">Mobile Number</label>
                                        <div class="mobile-otp-group">
                                            <input type="tel" id="phone" name="phone" class="form-input"
                                                placeholder="Enter mobile number" maxlength="10">

                                            <button type="button" id="sendOtpBtn" class="btn-send-otp">
                                                <span id="otpBtnText">Send OTP</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- OTP --}}
                                    <div class="form-row-custom full-width">
                                        <label class="form-label-custom">Enter OTP</label>
                                        <input type="text" id="otp" name="otp" class="form-input"
                                            placeholder="Enter 6-digit OTP" maxlength="6">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let otpVerified = false;
            let originalPhone = null;
            let resendTimer = null;

            const otpResult = $("#otpResult");

            // Clear general message
            function clearMessage() {
                otpResult.text("").removeClass("invalid-phone-msg green-phone-msg");
            }

            // Show error message
            function showErrorMessage(message) {
                otpResult.removeClass("green-phone-msg")
                         .addClass("invalid-phone-msg")
                         .text(message);
                otpResult[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Show success message
            function showSuccessMessage(message) {
                otpResult.removeClass("invalid-phone-msg")
                         .addClass("green-phone-msg")
                         .text(message);
                otpResult[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            function startResendTimer() {
                clearInterval(resendTimer);
                let seconds = 15;
                $("#sendOtpBtn").prop("disabled", true);
                $("#otpBtnText").text(`Resend OTP (${seconds}s)`);

                resendTimer = setInterval(() => {
                    seconds--;
                    $("#otpBtnText").text(`Resend OTP (${seconds}s)`);

                    if (seconds <= 0) {
                        clearInterval(resendTimer);
                        $("#sendOtpBtn").prop("disabled", false);
                        $("#otpBtnText").text("Send OTP");
                    }
                }, 1000);
            }

            // Force numbers only
            $("#phone, #otp").on("input", function() {
                this.value = this.value.replace(/[^0-9]/g, "");
            });

            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            });

            // ================= SEND LOGIN OTP =================
            $("#sendOtpBtn").click(function(e) {
                e.preventDefault();
                clearMessage();

                let phone = $("#phone").val().trim();

                if (phone.length !== 10) {
                    showErrorMessage("Enter valid 10-digit mobile number");
                    return;
                }

                $("#otp").prop("readonly", false).val("");
                otpVerified = false;
                originalPhone = phone;

                $.post("{{ route('employer.send-login-otp') }}", { phone })
                    .done(res => {
                        if (!res.status) {
                            showErrorMessage(res.message);
                            originalPhone = null;
                            return;
                        }

                        showSuccessMessage(res.message + (res.otp ? ` (OTP: ${res.otp})` : ""));
                        $("#phone").prop("readonly", true);
                        startResendTimer();
                    })
                    .fail(xhr => {
                        showErrorMessage(xhr.responseJSON?.message || "Failed to send OTP");
                        originalPhone = null;
                    });
            });

            // ================= VERIFY LOGIN OTP =================
            $("#otp").keyup(function() {
                let otp = $(this).val().trim();
                if (otp.length !== 6) return;

                let phone = $("#phone").val().trim();

                // Security check
                if (originalPhone && phone !== originalPhone) {
                    showErrorMessage("Phone number mismatch. Please send OTP again.");
                    otpVerified = false;
                    return;
                }

                $.post("{{ route('employer.verify-login-otp') }}", { phone, otp })
                    .done(res => {
                        if (res.status) {
                            showSuccessMessage(res.message);
                            $("#otp, #phone").prop("readonly", true);
                            clearInterval(resendTimer);
                            $("#sendOtpBtn").prop("disabled", true);
                            otpVerified = true;
                        } else {
                            showErrorMessage(res.message);
                            otpVerified = false;
                        }
                    })
                    .fail(xhr => {
                        showErrorMessage(xhr.responseJSON?.message || "OTP verification failed");
                    });
            });

            // ================= LOGIN SUBMIT =================
            $("#employerForm").submit(function(e) {
                e.preventDefault();
                clearMessage();

                if (!otpVerified) {
                    showErrorMessage("Please verify your mobile number with OTP first.");
                    $("#otp").focus();
                    return false;
                }

                // Final security check
                if (originalPhone && $("#phone").val() !== originalPhone) {
                    showErrorMessage("Security validation failed. Phone number was changed after OTP verification.");
                    return false;
                }

                let phone = $("#phone").val().trim();
                $("#loginBtn").prop("disabled", true);

                $.post("{{ route('employer.login.submit') }}", { phone })
                    .done(res => {
                        $("#loginBtn").prop("disabled", false);

                        if (res.status) {
                             window.location.href = res.redirect;
                        } else {
                            showErrorMessage(res.message);
                        }
                    })
                    .fail(xhr => {
                        $("#loginBtn").prop("disabled", false);
                        showErrorMessage(xhr.responseJSON?.message || "Login failed. Please try again.");
                    });
            });

            // Detect phone tampering
            $("#phone").on("focus", function() {
                if (originalPhone && $(this).val() !== originalPhone) {
                    showErrorMessage("Warning: Changing phone number after OTP verification is not allowed. Please send OTP again.");
                    $(this).val(originalPhone);
                }
            });
        });
    </script>

@endsection