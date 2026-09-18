@extends('layout.frontend.app')

@section('content')
<link rel="stylesheet" href="{{ asset('employe_assets/css/style.css') }}">

<style>
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
    .input-error {
        border-color: #dc3545 !important;
    }
    
</style>

<section class="section-registrations section-only-register">

    {{-- TOP DECORATIONS --}}
    <img src="{{ asset('employe_assets/images/top-right-1.png') }}" class="top-right-one">
    <div class="div-tops">
        <img src="{{ asset('employe_assets/images/top-left.png') }}">
    </div>

    <div class="container">
        <div class="main-wrapper">
            <div class="registration-container">

                {{-- LEFT PANEL --}}
                <div class="left-panel">
                    <img src="{{ asset('employe_assets/images/top-1.png') }}" class="top-1">

                    <div class="left-content">
                        <h1 class="main-title">Employer Registration</h1>
                        <div class="title-line"></div>
                        <p class="description-text">
                            Start your hiring journey by registering as an employer and unlock powerful tools to
                            connect, recruit, and grow your organization efficiently.
                        </p>
                    </div>

                    <div class="login-section left-content">
                        <h4>Have an account?</h4>
                        <a href="{{ route('employer.login.view') }}" class="btn-white-login">
                            <span>Log In</span>
                            <img src="{{ asset('employe_assets/images/Login.png') }}">
                        </a>
                    </div>

                    <img src="{{ asset('employe_assets/images/bottom-1.png') }}" class="bottom-1">
                </div>

                {{-- RIGHT PANEL --}}
                <div class="right-panel">
                    <form id="employerForm" enctype="multipart/form-data">
                        @csrf

                        {{-- General Message Display --}}
                        <p id="otpResult"></p>

                        {{-- NAME + EMAIL --}}
                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Name *</label>
                                <input type="text" name="name" id="name" class="form-input"
                                    placeholder="Enter Your Name"
                                    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                <span class="error-message" id="error-name"></span>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom">Email *</label>
                                <input type="email" name="email" id="email" class="form-input"
                                    placeholder="Enter your email">
                                <span class="error-message" id="error-email"></span>
                            </div>
                        </div>

                        {{-- MOBILE + SEND OTP --}}
                        <div class="form-row-custom full-width">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Mobile Number *</label>
                                <div class="mobile-otp-group">
                                    <input type="tel" name="phone" id="phone" class="form-input"
                                        placeholder="Enter Your Mobile Number" maxlength="10"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                    <button type="button" id="sendOtpBtn" class="btn-send-otp">
                                        <span id="otpBtnText">Send OTP</span>
                                    </button>
                                </div>
                                <span class="error-message" id="error-phone"></span>
                            </div>
                        </div>

                        {{-- OTP --}}
                        <div class="form-row-custom full-width">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Enter OTP *</label>
                                <input type="text" name="otp" id="otp" class="form-input"
                                    placeholder="Enter 6-digit OTP" maxlength="6"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <span class="error-message" id="error-otp"></span>
                            </div>
                        </div>

                        {{-- COMPANY NAME --}}
                        <div class="form-row-custom full-width">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Company Name *</label>
                                <input type="text" name="company_name" id="company_name" class="form-input"
                                    placeholder="Enter Your Company Name"
                                    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                <span class="error-message" id="error-company_name"></span>
                            </div>
                        </div>

                        {{-- ADDRESS + CITY --}}
                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Address *</label>
                                <input type="text" name="address" id="address" class="form-input"
                                    placeholder="Enter Your Address">
                                <span class="error-message" id="error-address"></span>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom">City *</label>
                                <input type="text" name="city" id="city" class="form-input"
                                    placeholder="Enter Your City"
                                    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                <span class="error-message" id="error-city"></span>
                            </div>
                        </div>

                        {{-- STATE + PINCODE --}}
                        <div class="form-row-custom">
                            <div class="form-group-custom">
                                <label class="form-label-custom">State *</label>
                                <select name="state" id="state" class="form-input">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->name }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-message" id="error-state"></span>
                            </div>

                            <div class="form-group-custom">
                                <label class="form-label-custom">Pin Code *</label>
                                <input type="text" name="pin_code" id="pin_code" class="form-input"
                                    placeholder="Enter Your Pin Code" maxlength="6"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <span class="error-message" id="error-pin_code"></span>
                            </div>
                        </div>

                        {{-- DOCUMENT UPLOAD --}}
                        <div class="form-row-custom full-width">
                            <div class="form-group-custom">
                                <label class="form-label-custom">Upload Identity Proof *</label>
                                <div class="upload-box" onclick="document.getElementById('identityInput').click()">
                                    <div class="upload-box-text">Click to upload document</div>
                                    <div class="upload-box-subtext">
                                        Aadhaar Card, PAN Card, Passport, Voter ID, Driving License (.pdf, .doc, .docx)
                                    </div>
                                </div>
                                <input type="file" id="identityInput" name="identity" style="display:none;"
                                    accept=".pdf,.doc,.docx">
                                <div>
                                 <p id="fileName" style="margin-top:5px; font-size:14px; color:#333;"></p>
                                <span class="error-message" id="error-identity"></span>
                               </div>
                            </div>
                        </div>

                        {{-- REGISTER BUTTON --}}
                        <button type="submit" id="registerBtn" class="btn-register-submit">
                            <span>Register Now</span>
                            <img src="{{ asset('employe_assets/images/register-now.png') }}">
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- BOTTOM DECORATIONS --}}
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
        let registerBtn = document.getElementById("registerBtn");
        let otpBtnText = document.getElementById("otpBtnText");
        let emailField = document.getElementById("email");
        let employerForm = document.getElementById("employerForm");
        let identityInput = document.getElementById("identityInput");
        let fileName = document.getElementById("fileName");

        let resendTimer = null;
        let otpVerified = false;
        let verifiedPhone = null;
        let isProcessing = false;
        let otpExpiryTimer = null;

        // Clear all validation errors
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        }

        // Clear general message
        function clearMessage() {
            otpResult.textContent = '';
            otpResult.className = '';
        }

        // Show error message
        function showErrorMessage(message) {
            otpResult.className = 'invalid-phone-msg';
            otpResult.textContent = message;
        }

        // Show success message
        function showSuccessMessage(message) {
            otpResult.className = 'green-phone-msg';
            otpResult.textContent = message;
        }

        // Display validation errors
        function displayErrors(errors) {
            clearErrors();

            let firstErrorField = null;

            for (let field in errors) {
                let errorElement = document.getElementById(`error-${field}`);
                let inputElement = document.getElementById(field) || document.querySelector(
                    `[name="${field}"]`);

                if (errorElement) {
                    errorElement.textContent = errors[field][0];
                }
                if (inputElement) {
                    inputElement.classList.add('input-error');

                    if (!firstErrorField) {
                        firstErrorField = inputElement;
                    }
                }
            }

            if (errors.phone || errors.otp) {
                otpResult.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } else if (firstErrorField) {
                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorField.focus();
            }
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

            // OTP expires in 10 minutes (600 seconds)
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
            }, 600000); // 10 minutes
        }

        // File upload display
        identityInput.addEventListener('change', function() {
            fileName.textContent = this.files[0]?.name ? `Selected: ${this.files[0].name}` : '';
        });

        // ============= SEND OTP =============
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

            fetch("{{ route('employer.send-otp') }}", {
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

        // ============= VERIFY OTP =============
        otpInput.addEventListener("keyup", function() {
            if (otpInput.value.length !== 6) return;
            if (isProcessing) return;

            let mobile = phone.value.trim();

            isProcessing = true;

            fetch("{{ route('employer.verify-otp') }}", {
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

        // ============= SUBMIT FORM =============
        employerForm.addEventListener("submit", function(e) {
            e.preventDefault();

            if (isProcessing) return;

            clearErrors();
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
            let formData = new FormData(employerForm);

            registerBtn.disabled = true;
            let originalText = registerBtn.textContent;
            registerBtn.textContent = "Processing...";

            fetch("{{ route('employer.register') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    registerBtn.textContent = originalText;

                    if (data.status) {
                        registerBtn.disabled = true;
                        registerBtn.textContent = "Processing...";
                        window.location.href = data.redirect;
                    } else {
                        registerBtn.disabled = false;
                        if (data.errors) {
                            displayErrors(data.errors);
                            showErrorMessage(data.message || "Please fix the errors.");
                            otpResult.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else {
                            showErrorMessage(data.message ||
                                'Registration failed. Please try again.');
                            otpResult.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                })
                .catch(err => {
                    registerBtn.textContent = originalText;
                    registerBtn.disabled = false;
                    showErrorMessage('An error occurred. Please try again.');
                    otpResult.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    console.error('Error:', err);
                })
                .finally(() => {
                    if (isProcessing) {
                        isProcessing = false;
                    }
                });
        });

        // ============= EMAIL VALIDATION =============
        if (emailField) {
            let emailTimeout;
            emailField.addEventListener("input", () => {
                clearTimeout(emailTimeout);
                let emailError = document.getElementById("error-email");

                if (!emailField.value) {
                    emailError.textContent = '';
                    emailField.classList.remove("input-error");
                    return;
                }

                emailTimeout = setTimeout(() => {
                    fetch("{{ route('employer.check.email') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                email: emailField.value
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.exists) {
                                emailError.textContent = "Email already exists";
                                emailField.classList.add("input-error");
                            } else {
                                emailError.textContent = "";
                                emailField.classList.remove("input-error");
                            }
                        });
                }, 500);
            });
        }
    });
</script>
@endsection