<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillFetch - Forgot Password</title>

    <link rel="icon" type="image/png" href="{{ asset('admin_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>

<body>

    <!-- SAME HEADER AS LOGIN PAGE -->
    {{-- <div class="navigation-wrap start-header start-style">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <a class="navbar-brand" href="#">
                            <img src="{{ asset('admin_assets/images/logo-orker.png') }}" alt="logo">
                        </a>
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

    <!-- FORGOT PASSWORD CARD -->
    <div class="login-container">
        <div class="login-card">
  <div class="login-card-mian">
                  <a class="navbar-brand" href="index.html"><img src="{{ asset('admin_assets/images/logo-orker.png') }}" alt="logo"></a>
              </div>
            <div class="login-header">
                Forgot Password
            </div>

            <p class="text-center mb-3 reset-links">Enter your email to receive a reset link.</p>

            @if(session('error'))
                <div class="alert alert-danger" style="font-size:14px;">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success" style="font-size:14px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('reset_link'))
                <div class="alert alert-info">
                    Reset Link (DEV):
                    <a href="{{ session('reset_link') }}" target="_blank">Open Link</a>
                </div>
            @endif

            <form action="{{ route('admin.forgot.password.submit') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                </div>

                <button type="submit" class="btn btn-login-submit">Send Reset Link</button>
            </form>

            <div class="forgot-password text-center mt-3">
                <a href="{{ route('admin.login.view') }}">← Back to Login</a>
            </div>

        </div>
    </div>

    <script src="{{ asset('admin_assets/js/bootstrap.min.js') }}"></script>

</body>

</html>