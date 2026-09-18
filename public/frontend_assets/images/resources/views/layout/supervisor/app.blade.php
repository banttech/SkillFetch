<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'SkillFetch - ' . $pageTitle ?? 'SkillFetch' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('supervisor_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('supervisor_assets/css/supervisor-panel.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom Toast Styles -->

    <style>
        /* Enhanced Toastr Styles */
        #toast-container>div {
            opacity: 0.95;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            /* padding: 15px 20px; */
        }

        .toast-success {
            background-color: #28a745 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
            color: #000000 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
        }
    </style>
</head>

<body>

    @php
        $user = Auth::user();
    @endphp

    <div class="sidebar" id="sidebar">
        <div class="close-btn" id="closeSidebar">✖</div>
        <div class="image-logo"><a href="{{ route('frontend.home') }}"><img
                    src="{{ asset('supervisor_assets/images/logo-orker.png') }}" alt="logo"></a></div>

        <ul>
            <li><a href="{{ route('supervisor.dashboard') }}"><img
                        src="{{ asset('supervisor_assets/images/Speed.png') }}" alt="dashboard">Dashboard</a>
            </li>
             <li class="dropdown {{ request()->is('supervisor/open-jobs*') || request()->is('supervisor/applied-jobs*') || request()->is('supervisor/job-detail*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="dropdown-toggle">
        <div>
            <img src="{{ asset('supervisor_assets/images/Profiles.png') }}" alt="dashboard">
        Jobs</div> <div>
            <i class="fa fa-chevron-down"></i>
        </div>
    </a>

    <ul class="dropdown-menu">
        <a href="{{ route('supervisor.openjobs') }}"> <li>
           
                <img src="{{ asset('supervisor_assets/images/Job-Seeker.png') }}" alt="">
                Open Jobs
            
        </li></a><a href="{{ route('supervisor.appliedJobs') }}">
        <li>
            
                <img src="{{ asset('supervisor_assets/images/laywer.png') }}" alt="" style="width:45px;">
                Applied Jobs
           
        </li> </a>
    </ul>
</li>
        </ul>
    </div>


    <div class="header">
        <div class="menu-icon" id="menuBtn">☰</div>
        <div class="logo-only-mob"><a href="{{ route('frontend.home') }}"><img
                    src="{{ asset('supervisor_assets/images/logo-orker.png') }}" alt="logo"></a></div>

        <div class="div-name-cs">My Dashboard</div>
        <div class="user-profile" id="userProfile">
            <div class="dummy-image-2">
                @php
                    $image = $user->image
                        ? asset('supervisor_assets/images/' . $user->image)
                        : asset('supervisor_assets/images/user-dummy.png');
                @endphp
                <img src="{{ $image }}" class="profile-img-preview mt-2" alt="profile" width="50"
                    height="50">
            </div>
            <div class="wel-image">
                welcome !!
                <br>
                <hr>
                <span class="user-name">{{ $user->name }}</span>
            </div>
            <span class="chevron"><i class="fa fa-chevron-down"></i></span>


            <div class="user-dropdown" id="userDropdown">

                <ul>
                    <li><a href="{{ route('supervisor.profile.edit') }}"><i class="fa fa-eye"
                                aria-hidden="true"></i>Edit Profile</a></li>
                    <li>
                        <a href="{{ route('supervisor.logout') }}">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>


                    </li>
                </ul>
            </div>
        </div>
    </div>


    @yield('content')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
      const sidebar = document.getElementById("sidebar");
document.getElementById("menuBtn").onclick = () => sidebar.classList.add("show");
document.getElementById("closeSidebar").onclick = () => sidebar.classList.remove("show");

// Sidebar dropdown (Jobs etc.)
document.querySelectorAll(".dropdown > .dropdown-toggle").forEach(toggle => {
    toggle.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        const parent = this.closest(".dropdown");
        parent.classList.toggle("active");
    });
});

// User profile dropdown
const userProfile = document.getElementById("userProfile");
const userDropdown = document.getElementById("userDropdown");

if (userProfile && userDropdown) {
    userProfile.addEventListener("click", function (e) {
        e.stopPropagation();
        userDropdown.classList.toggle("show");
    });
}

// Bahar click par kuch bhi na ho
document.addEventListener("click", function () {
    // intentionally blank
});

        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "10000",
            "hideDuration": "10000",
            "timeOut": "10000",
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": true,
            "rtl": false
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
            @php
                session()->forget('success');
            @endphp
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
            @php
                session()->forget('error');
            @endphp
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
            @php
                session()->forget('info');
            @endphp
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>


    {{-- Global Department-Wise Score Popup --}}
    @include('components.dept-score-popup')

</body>

</html>
