<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillFetch - Admin Login</title>
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js">
    </script>

<style>.password-field {
    position: relative;
}

.password-field .form-control {
    padding-right: 40px;
}

.toggle-password {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    font-size: 16px;
}

.toggle-password:hover {
    color: #000;
}</style>

</head>

<body>
{{-- 
    <div class="navigation-wrap start-header start-style">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <a class="navbar-brand" href="index.html"><img
                                src="{{ asset('admin_assets/images/logo-orker.png') }}" alt="logo"></a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
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

                        </div>

                    </nav>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="login-container">
        <div class="login-card">
              <div class="login-card-mian">
                  <a class="navbar-brand" href="{{route('frontend.home')}}"><img src="{{ asset('admin_assets/images/logo-orker.png') }}" alt="logo"></a>
              </div>
            <div class="login-header">
                Admin Login
            </div>
           
         @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>  
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Enter your email address" name="email">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span> 
                        @enderror
                </div>
           <div class="form-group password-wrapper">
    <label class="form-label">Password</label>

    <div class="password-field">
        <input type="password" 
               class="form-control" 
               placeholder="Enter your password" 
               name="password" 
               id="password">

        {{-- <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i> --}}
        <i class="fa fa-eye-slash toggle-password" aria-hidden="true"></i>
    </div>

    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
                <div class="forgot-password">
                    <a href="{{ route('admin.forgot.password.view') }}">Forgot Password</a>
                </div>
                <button type="submit" class="btn btn-login-submit">LOGIN</button>
            </form>
        </div>
    </div>



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
                    var _d = $(e.target).closest('.nav-item'); _d.addClass('show');
                    setTimeout(function () {
                        _d[_d.is(':hover') ? 'addClass' : 'removeClass']('show');
                    }, 1);
                }
            });


        })(jQuery); 


        $(document).ready(function() {
    $('.toggle-password').on('click', function() {
        const passwordInput = $(this).siblings('#password');
        const icon = $(this);
        
        // Toggle password visibility
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        }
    });
});

    </script>

    <script src="{{ asset('admin_assets/js/bootstrap.min.js') }}"></script>
</body>

</html>