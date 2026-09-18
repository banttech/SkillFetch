<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'SkillFetch' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('supervisor_assets/images/favicon.png') }}">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/bootstrap.min.css') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/style.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body class="hero-anime">

    <!-- HEADER -->
    <div class="navigation-wrap start-header start-style">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light">
                <a class="navbar-brand" href="index.html">
                    <img src="{{ asset('supervisor_assets/images/logo-orker.png') }}" alt="logo">
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">

                    <ul class="navbar-nav ml-auto py-4 py-md-0">
                        <li class="nav-item active pl-4 pl-md-0 ml-0 ml-md-4">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                            <a class="nav-link" href="#">About Us</a>
                        </li>
                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                            <a class="nav-link" href="#">Contact Us</a>
                        </li>
                    </ul>

                    <div class="btn-div-alls">
                        <a href="{{ route('frontend.login') }}" class="login-btn">
                            <div class="div-btn2"><i class="fa-solid fa-user"></i> Log In</div>
                        </a>

                        <a href="{{ route('frontend.register') }}" class="register-btn">
                            <div class="btn-div-3"><i class="fa-solid fa-user"></i> Register</div>
                        </a>
                    </div>

                </div>
            </nav>
        </div>
    </div>
    <!-- END HEADER -->

    <!-- MAIN CONTENT -->
    @yield('content')
    <!-- END MAIN CONTENT -->

    <!-- END FOOTER -->

    <script>
        (function ($) {
            "use strict";

            $(function () {
                var header = $(".start-style");
                $(window).scroll(function () {
                    var scroll = $(window).scrollTop();
                    if (scroll >= 10) {
                        header.removeClass('start-style').addClass("scroll-on");
                    } else {
                        header.removeClass("scroll-on").addClass('start-style');
                    }
                });
            });

            $(document).ready(function () {
                $('body.hero-anime').removeClass('hero-anime');
            });

            $('body').on('mouseenter mouseleave', '.nav-item', function (e) {
                if ($(window).width() > 750) {
                    var _d = $(e.target).closest('.nav-item');
                    _d.addClass('show');
                    setTimeout(function () {
                        _d[_d.is(':hover') ? 'addClass' : 'removeClass']('show');
                    }, 1);
                }
            });

        })(jQuery);
    </script>

    <script src="{{ asset('supervisor_assets/js/bootstrap.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            let phone = document.getElementById("phone") || document.getElementById("mobile");
            let otpInput = document.getElementById("otp") || document.getElementById("otps");
            let sendOtpBtn = document.getElementById("sendOtpBtn");
            let otpResult = document.getElementById("otpResult");
            let loginBtn = document.getElementById("loginBtn");
            let registerBtn = document.getElementById("registerBtn"); // For registration pages
            let otpBtnText = document.getElementById("otpBtnText");
            let emailField = document.querySelector("input[name='email']");

            if (!sendOtpBtn || !otpInput || !phone) return;

            let resendTimer = null;

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

            // -------------------------------
            // SEND OTP (LOGIN + REGISTER)
            // -------------------------------
            sendOtpBtn.onclick = function () {

                let mobile = phone.value.trim();
                if (mobile.length !== 10) {
                    otpResult.style.color = "red";
                    otpResult.innerHTML = "Enter valid 10-digit mobile number";
                    return;
                }

                let otpRoute =
                    sendOtpBtn.dataset.type === "login"
                        ? "{{ route('supervisor.send.login.otp') }}"
                        : "{{ route('supervisor.send.otp') }}";

                // 🔥 FIXED → send correct field name
                let bodyData =
                    sendOtpBtn.dataset.type === "login"
                        ? { phone: mobile }
                        : { mobile: mobile };

                otpInput.readOnly = false;
                otpInput.value = "";

                fetch(otpRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(bodyData)
                })
                    .then(res => res.json())
                    .then(data => {
                        otpResult.style.color = data.status ? "green" : "red";
                        otpResult.innerHTML = data.message + (data.otp ? " (OTP: " + data.otp + ")" : "");

                        if (!data.status) return;

                        startResendTimer();
                    });
            };

            // -------------------------------
            // VERIFY OTP
            // -------------------------------
            otpInput.addEventListener("keyup", function () {

                if (otpInput.value.length !== 6) return;

                let mobile = phone.value.trim();

                let verifyRoute =
                    sendOtpBtn.dataset.type === "login"
                        ? "{{ route('supervisor.verify.login.otp') }}"
                        : "{{ route('supervisor.verify.otp') }}";

                // 🔥 FIXED → send correct field name
                let bodyData =
                    sendOtpBtn.dataset.type === "login"
                        ? { phone: mobile, otp: otpInput.value }
                        : { mobile: mobile, otp: otpInput.value };

                fetch(verifyRoute, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(bodyData)
                })
                    .then(res => res.json())
                    .then(data => {
                        otpResult.style.color = data.status ? "green" : "red";
                        otpResult.innerHTML = data.message;

                        if (data.status) {
                            otpInput.readOnly = true;
                            clearInterval(resendTimer);
                            sendOtpBtn.disabled = true;

                            if (loginBtn) loginBtn.disabled = false;
                            if (registerBtn) registerBtn.disabled = false;
                        }
                    });
            });

            // -------------------------------
            // CHECK EMAIL (REGISTER ONLY)
            // -------------------------------
            if (emailField) {
                emailField.addEventListener("blur", () => {
                    fetch("{{ route('supervisor.check.email') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ email: emailField.value })
                    })
                        .then(res => res.json())
                        .then(data => {
                            otpResult.style.color = data.exists ? "red" : "";
                            otpResult.innerHTML = data.exists ? "Email already exists" : "";
                        });
                });
            }

        });
    </script>


    {{-- SELECT2 JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select options",
                width: '100%'
            });
        });
    </script>


</body>

</html>