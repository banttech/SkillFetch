<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillFetch - Reset Password</title>

    <link rel="icon" type="image/png" href="{{ asset('admin_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- SAME HEADER AS LOGIN -->
    <div class="navigation-wrap start-header start-style">
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
    </div>

    <!-- RESET PASSWORD CONTAINER -->
    <div class="login-container">
        <div class="login-card">

            <div class="login-header">
                Reset Password
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.resetPassword.submit') }}" method="POST">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request()->email }}">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="password" 
                        placeholder="Enter new password">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="password_confirmation" 
                        placeholder="Confirm new password">
                          @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                </div>

                <button type="submit" class="btn btn-login-submit">Update Password</button>
            </form>

            <div class="forgot-password text-center mt-3">
                <a href="{{ route('admin.login.view') }}">← Back to Login</a>
            </div>

        </div>
    </div>

    <script src="{{ asset('admin_assets/js/bootstrap.min.js') }}"></script>

</body>

</html>