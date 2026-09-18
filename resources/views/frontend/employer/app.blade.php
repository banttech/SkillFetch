<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SkillFetch')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <link rel="icon" type="image/png" href="{{ asset('employe_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('employe_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('employe_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    @stack('styles')
</head>

<body>

    {{-- ================= NAVBAR ================= --}}
    <div class="navigation-wrap start-header start-style">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-md navbar-light">

                        <a class="navbar-brand" href="{{ url('/') }}">
                            <img src="{{ asset('employe_assets/images/logo-orker.png') }}" alt="logo">
                        </a>

                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">

                            <ul class="navbar-nav ml-auto py-4 py-md-0">
                                <li class="nav-item active pl-4">
                                    <a class="nav-link" href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="nav-item pl-4">
                                    <a class="nav-link" href="#">About Us</a>
                                </li>
                                <li class="nav-item pl-4">
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
        </div>
    </div>

    {{-- ================= MAIN PAGE CONTENT ================= --}}
    @yield('content')

    {{-- ================= FOOTER BG IMAGES (Optional) ================= --}}
    @stack('bottom-bg')

    <script src="{{ asset('employe_assets/js/bootstrap.min.js') }}"></script>

    <script>
        (function ($) {
            $(function () {
                var header = $(".start-header");
                $(window).scroll(function () {
                    if ($(window).scrollTop() >= 10)
                        header.addClass("scroll-on");
                    else
                        header.removeClass("scroll-on");
                });
            });
        })(jQuery);
    </script>

    @stack('scripts')

    <!-- <script>
        document.addEventListener("DOMContentLoaded", () => {

            const phone = document.querySelector("#phone");
            const otp = document.querySelector("#otp");
            const sendBtn = document.querySelector("#sendOtpBtn");
            const msgBox = document.querySelector("#alertBox") || document.querySelector("#msgBox");

            if (!sendBtn || !phone || !otp) return;

            const loginBtn = document.querySelector("#loginBtn");
            const registerBtn = document.querySelector("#registerBtn");

            const error = m => msgBox.innerHTML = `<div class="alert alert-danger">${m}</div>`;
            const success = m => msgBox.innerHTML = `<div class="alert alert-success">${m}</div>`;

            // validation rules
            const validPhone = p => /^[0-9]{10}$/.test(p);
            const validOtp = o => /^[0-9]{6}$/.test(o);

            let timer;

            function startTimer() {
                clearInterval(timer);
                let sec = 15;
                sendBtn.disabled = true;
                sendBtn.innerText = `Resend OTP (${sec}s)`;

                timer = setInterval(() => {
                    sec--;
                    sendBtn.innerText = `Resend OTP (${sec}s)`;
                    if (sec <= 0) {
                        clearInterval(timer);
                        sendBtn.disabled = false;
                        sendBtn.innerText = "Send OTP";
                    }
                }, 1000);
            }

            // Detect page properly
            const pageType = sendBtn.dataset.type === "login" ? "login" : "register";

            const sendURL = pageType === "login"
                ? "{{ route('supervisor.send.login.otp') }}"
                : "{{ route('employer.send-otp') }}";

            const verifyURL = pageType === "login"
                ? "{{ route('supervisor.verify.login.otp') }}"
                : "{{ route('employer.verify-otp') }}";

            // ================= SEND OTP ==================
            sendBtn.addEventListener("click", () => {

                let mobile = phone.value.trim();
                if (!validPhone(mobile)) return error("Enter valid 10-digit mobile number");

                fetch(sendURL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ phone: mobile })
                })
                    .then(async res => {
                        let data = await res.json();

                        if (pageType === "register") {
                            if (res.status === 422 || data.message === "Mobile number already registered") {
                                error("This mobile number is already registered. Please login.");
                                otp.readOnly = true;
                                registerBtn.disabled = true;
                                return;
                            }
                        }

                        if (pageType === "login") {
                            if (data.status === false) {
                                error(data.message || "Mobile number not found");
                                otp.readOnly = true;
                                loginBtn.disabled = true;
                                return;
                            }
                        }

                        success(data.message);
                        otp.readOnly = false;
                        otp.value = "";
                        startTimer();
                    })
                    .catch(() => error("Failed to send OTP"));
            });

            // ================= VERIFY OTP ================
            otp.addEventListener("input", () => {

                if (!validOtp(otp.value)) return;

                fetch(verifyURL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        phone: phone.value.trim(),
                        otp: otp.value.trim()
                    })
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.message === "OTP Verified") {

                            success("OTP Verified Successfully");

                            otp.readOnly = true;
                            phone.readOnly = true;

                            clearInterval(timer);
                            sendBtn.disabled = true;

                            if (pageType === "login") loginBtn.disabled = false;
                            if (pageType === "register") registerBtn.disabled = false;

                        } else {
                            error(res.message || "Invalid OTP");
                        }
                    })
                    .catch(() => error("OTP verification failed"));
            });

            // ================= EMAIL VALIDATION (REGISTER PAGE) ==================
            if (pageType === "register") {

                $("#email").on("blur", function () {

                    let email = $(this).val().trim();
                    if (!email) {
                        error("Email is required");
                        registerBtn.disabled = true;
                        return;
                    }

                    if (!email.includes("@") || !email.includes(".")) {
                        error("Enter valid email");
                        registerBtn.disabled = true;
                        return;
                    }

                    $.ajax({
                        url: "{{ route('employer.check.email') }}",
                        type: "POST",
                        data: { email: email },
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        success: function (res) {

                            if (res.status === true) {
                                success(res.message);

                                if ($("#otp").prop("readonly") === true) {
                                    registerBtn.disabled = false;
                                }
                            }
                        },
                        error: function (xhr) {
                            if (xhr.status === 409) {
                                error("Email already exists");
                                registerBtn.disabled = true;
                            } else {
                                error("Email check failed");
                                registerBtn.disabled = true;
                            }
                        }
                    });
                });
            }

        });
    </script> -->



</body>

</html>