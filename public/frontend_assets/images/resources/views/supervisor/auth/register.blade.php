@extends('layout.frontend.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        /* Loading Spinner */
       
        .otp-success {
            color: #28a745;
        }

        /* .invalid-phone-msg {
                        color: #dc3545;
                    } */
    </style>

    <section class="section-registers">
        <img src="{{ asset('supervisor_assets/images/grey-logo.png') }}" alt="" class="img-4">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5 bg-bluses">
                    <div class="div-inner-registrations">
                        <h1>New Here !!</h1>
                        <p>Register as a new supervisor to streamline operations, lead with confidence, and make an
                            impact from day one.</p>
                        <p class="have-an-button">Have an account? <a href="{{ route('supervisor.login.view') }}"><b>Log
                                    In</b></a></p>
                        <img src="{{ asset('supervisor_assets/images/image-conts.png') }}" alt="register">
                    </div>
                </div>

                <div class="col-md-1"></div>

                <div class="col-md-5">
                    <div class="right-section">
                        <div class="form-container">

                            {{-- General Message Display --}}
                            <p id="otpResult"></p>

                            <h2>Supervisor Registration</h2>

                            <form id="registrationForm" enctype="multipart/form-data">
                                @csrf

                                {{-- NAME + EMAIL --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <legend>Name *</legend>
                                            <div class="input-wrapper">
                                                <input type="text" name="name" id="name" class="form-control"
                                                    placeholder="Enter Your Name"
                                                    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                                <span class="error-message" id="error-name"></span>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <legend>Email *</legend>
                                            <div class="input-wrapper">
                                                <input type="email" name="email" id="email" class="form-control"
                                                    placeholder="Enter Your Email">
                                                <span class="error-message" id="error-email"></span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- MOBILE + OTP --}}
                                <fieldset class="form-group">
                                    <legend>Mobile *</legend>
                                    <div class="input-wrapper">
                                        <div class="mobile-group">
                                            <input type="text" name="mobile" id="mobile" class="form-control"
                                                placeholder="Enter Mobile Number" maxlength="10"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">

                                            <button type="button" class="btn btn-otp" id="sendOtpBtn" data-type="register">
                                                <span id="otpBtnText">Send OTP</span>
                                            </button>
                                        </div>
                                        <span class="error-message" id="error-mobile"></span>
                                    </div>
                                </fieldset>

                                <fieldset class="form-group">
                                    <legend>Enter OTP *</legend>
                                    <div class="input-wrapper">
                                        <input type="text" name="otp" id="otps" class="form-control"
                                            placeholder="Enter 6-digit OTP" maxlength="6"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        <span class="error-message" id="error-otp"></span>
                                    </div>
                                </fieldset>

                                {{-- SKILLS --}}
                                <fieldset class="form-group">
                                    <legend>Select Your Skills *</legend>
                                    <div class="input-wrapper form-control">
                                        <select name="skills[]" id="skills" multiple class="form-control select2">
                                            @foreach ($skills as $skill)
                                                <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                            @endforeach
                                        </select>
                                    </div><span class="error-message" id="error-skills"></span>
                                </fieldset>

                                {{-- EXPERIENCE --}}
                                <fieldset class="form-group">
                                    <legend>Experience *</legend>
                                    <div class="input-wrapper form-control">
                                        <select name="experiences[]" id="experiences" multiple class="form-control select2">
                                            @foreach ($experiences as $exp)
                                                <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div> <span class="error-message" id="error-experiences"></span>
                                </fieldset>

                                {{-- LOCATIONS --}}
                                <fieldset class="form-group">
                                    <legend>Preferred Work Locations *</legend>
                                    <div class="input-wrapper form-control">
                                        <select name="locations[]" id="locations" multiple class="form-control select2">
                                            @foreach ($locations as $loc)
                                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div><span class="error-message" id="error-locations"></span>
                                </fieldset>

                                 <fieldset class="form-group">
                                    <legend>Current or last drawn Salary (In Rs.) (Per Month)*</legend>
                                    <div class="input-wrapper">
                                        <input type="number" placeholder="Enter Your Current Salary" name="salary"
                                            id="salary" class="form-control"  step="1" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        <span class="error-message" id="error-salary"></span>
                                    </div>
                                </fieldset>

                                {{-- ADDRESS --}}
                                <fieldset class="form-group">
                                    <legend>Address *</legend>
                                    <div class="input-wrapper">
                                        <input type="text" placeholder="Enter Your Address" name="address"
                                            id="address" class="form-control">
                                        <span class="error-message" id="error-address"></span>
                                    </div>
                                </fieldset>

                                {{-- CITY + STATE + PINCODE --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <legend>City *</legend>
                                            <div class="input-wrapper">
                                                <input type="text" placeholder="Enter Your City" name="city"
                                                    id="city" class="form-control"
                                                    oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                                <span class="error-message" id="error-city"></span>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <legend>State *</legend>
                                            <div class="input-wrapper">
                                                <select name="state_id" id="state_id" class="form-select">
                                                    <option value="">Select State</option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="error-message" id="error-state_id"></span>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <legend>Pin Code *</legend>
                                            <div class="input-wrapper">
                                                <input type="text" placeholder="Enter Your Pincode" name="pincode"
                                                    id="pincode" class="form-control" maxlength="6"
                                                    oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                                <span class="error-message" id="error-pincode"></span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- Aadhar --}}
                                <fieldset class="form-group">
                                    <legend>Upload Aadhar Card *</legend>
                                    <div class="input-wrapper">
                                        <input type="file" name="aadhar" id="aadhar" class="form-control" accept=".pdf,.doc,.docx,image/jpeg,image/jpg,image/png,image/webp">
                                        <span class="error-message" id="error-aadhar"></span>
                                        <small>Only .pdf, .doc, .docx, .png, .jpg, .jpeg, .webp file types are allowed. File size must not exceed 2
                                            MB.</small>
                                    </div>
                                </fieldset>

                                <fieldset class="form-group label-ches">
                                    <legend>Own A Bike</legend>
                                    <div class="div-custom-fls">
                                        <label class="custom-radio">
                                            <input type="radio" name="own_bike" value="Yes" checked>
                                            <span class="radio-mark"></span>
                                            Yes
                                        </label>
                                        <label class="custom-radio">
                                            <input type="radio" name="own_bike" value="No">
                                            <span class="radio-mark"></span>
                                            No
                                        </label>
                                    </div>
                                </fieldset>

                                <fieldset class="form-group label-ches">
                                    <legend>Own A Phone</legend>
                                    <div class="div-custom-fls">
                                        <label class="custom-radio">
                                            <input type="radio" name="own_phone" value="Yes" checked>
                                            <span class="radio-mark"></span>
                                            Yes
                                        </label>
                                        <label class="custom-radio">
                                            <input type="radio" name="own_phone" value="No">
                                            <span class="radio-mark"></span>
                                            No
                                        </label>
                                    </div>
                                </fieldset>

                                {{-- FINAL SUBMIT --}}
                                <button type="submit" id="registerBtn" class="btn btn-register">
                                    Register Now
                                </button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img src="{{ asset('supervisor_assets/images/three-olcor.png') }}" alt="" class="img-3">
    </section>

    {{-- SELECT2 JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select Options",
                width: '100%'
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let phone = document.getElementById("mobile");
            let otpInput = document.getElementById("otps");
            let sendOtpBtn = document.getElementById("sendOtpBtn");
            let otpResult = document.getElementById("otpResult");
            let registerBtn = document.getElementById("registerBtn");
            let otpBtnText = document.getElementById("otpBtnText");
            let emailField = document.getElementById("email");
            let registrationForm = document.getElementById("registrationForm");

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
                otpResult.className = 'otp-success';
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

                        // Store first error field
                        if (!firstErrorField) {
                            firstErrorField = inputElement;
                        }
                    }
                }

                // Scroll to first error OR to otpResult if it's an OTP/mobile error
                if (errors.mobile || errors.otp) {
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

    // OTP expires in 10 minutes (600 seconds) - FIXED: Was 60 seconds
    otpExpiryTimer = setTimeout(() => {
        if (!otpVerified) {
            clearMessage();
            showErrorMessage("OTP expired. Please request a new OTP.");
            otpInput.value = '';
            
            // IMPORTANT: Always enable resend button on expiry
            clearInterval(resendTimer);
            resendTimer = null;
            sendOtpBtn.disabled = false;
            otpBtnText.innerText = "Resend OTP";
        }
    }, 600000); // 10 minutes (600 seconds)
}

            // ============= SEND OTP =============
            sendOtpBtn.onclick = function() {
                if (isProcessing) return;

                // DON'T clear messages here to prevent blinking
                // clearErrors();
                // clearMessage();

                let mobile = phone.value.trim();

                if (mobile.length !== 10) {
                    clearMessage();
                    showErrorMessage("Enter valid 10-digit mobile number");
                    phone.focus();
                    return;
                }

                // If OTP was verified, allow user to change phone and resend
                if (otpVerified) {
                    if (confirm("Changing phone number will require new OTP verification. Continue?")) {
                        otpVerified = false;
                        verifiedPhone = null;
                        otpInput.value = "";
                        otpInput.readOnly = false;
                        phone.readOnly = false;

                        // Clear expiry timer
                        if (otpExpiryTimer) {
                            clearTimeout(otpExpiryTimer);
                        }
                    } else {
                        return;
                    }
                }

                isProcessing = true;

                // Show loading state - CHANGE TEXT instead of hiding
                sendOtpBtn.disabled = true;
                otpBtnText.innerText = "Sending...";

                fetch("{{ route('supervisor.send.otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            mobile: mobile
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Clear old messages before showing new
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

                fetch("{{ route('supervisor.verify.otp') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            mobile: mobile,
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

            // ============= SUBMIT FORM =============
            registrationForm.addEventListener("submit", function(e) {
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

                // Final security check
                if (phone.value !== verifiedPhone) {
                    showErrorMessage("Phone number was changed. Please verify again.");
                    otpResult.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    return false;
                }

                isProcessing = true;
                let formData = new FormData(registrationForm);

                // Show loading text
                registerBtn.disabled = true;
                let originalText = registerBtn.textContent;
                registerBtn.textContent = "Processing...";

                fetch("{{ route('supervisor.register.store') }}", {
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
                            // showSuccessMessage(data.message);
                            registerBtn.disabled = true;
                            registerBtn.textContent = "Processing...";

                           
                                window.location.href = data.redirect;
                           
                        } else {
                            registerBtn.disabled = false;
                            if (data.errors) {
                                displayErrors(data.errors);
                                showErrorMessage(data.message || "Please fix the errors.");
                                // Scroll to error message area
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
                        fetch("{{ route('supervisor.check.email') }}", {
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
