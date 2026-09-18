<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ 'SkillFetch - ' . $pageTitle ?? '' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('employe_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('employe_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('employe_assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    @stack('styles')
</head>

<body>

    {{-- HEADER --}}
    <header class="top-header">
        <div class="container-fluid">
            <div class="row align-items-center">

                <!-- LEFT LOGO -->
                <div class="col-md-6 col-6">
                    <div class="div-logo-bars">
                        <button class="sidebar-toggle d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="logo">
                            <a href=""><img src="{{ asset('employe_assets/images/logo-white.png') }}"
                                    alt="logo"></a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PROFILE AREA -->
                <div class="col-md-6 col-6">
                    <div class="welcome-section">

                        <div>
                            <p class="welcome-text mb-0">WELCOME !!</p>
                            <hr>
                            <span class="user-name">{{ ucfirst(Auth::user()->name) ?? '' }}</span>
                        </div>

                        <span class="user-avatar">
                            <img src="{{ asset('employe_assets/images/' . (Auth::user()->image ?? 'user-dummy.png')) }}"
                                alt="user">
                        </span>

                        <div class="dropdown">
                            <button class="drop-btn">
                                <i class="fa fa-chevron-down chevron"></i>
                            </button>

                            <ul class="drop-menu">
                                <li>
                                    <a href="{{ route('employer.profile') }}">
                                        <i class="fa fa-eye" aria-hidden="true"></i> Edit Profile
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('employer.logout') }}">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>

                                    <form id="employerLogout" action="{{ route('employer.logout') }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- MAIN AREA --}}
    <section class="main-employs">
        <div class="container-fluid mt-0">
            <div class="row">

                {{-- SIDEBAR --}}
                <div class="col-lg-2 col-md-3 p-0">
                    <div class="sidebar">
                        <nav class="nav flex-column">

                            <a class="nav-link {{ request()->routeIs('employer.myjobs') ? 'active' : '' }}"
                                href="{{ route('employer.myjobs') }}">
                                <i class="fas fa-user-tie"></i>
                                <span>My Jobs</span>
                            </a>

                            <a class="nav-link {{ request()->routeIs('employer.search.supervisor') ? 'active' : '' }}"
                                href="{{ route('employer.search.supervisor') }}">
                                <i class="fas fa-users"></i>
                                <span>Search Supervisors</span>
                            </a>

                            <a class="nav-link {{ request()->routeIs('employer.skill.*') ? 'active' : '' }}"
                                href="{{ route('employer.skill.index') }}">
                                <i class="fas fa-tools"></i>
                                <span>Employer Skills</span>
                            </a>

                        </nav>
                    </div>
                </div>

                {{-- MAIN CONTENT --}}
                <div class="col-lg-10 col-md-9">
                    <div class="main-content">

                        {{-- PAGE HEADER --}}
                        @yield('page-header')

                        {{-- PAGE CONTENT --}}
                        @yield('content')

                    </div>
                </div>

            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="{{ asset('employe_assets/js/bootstrap.min.js') }}"></script>
    @stack('scripts')

    <script>
        const dropdown = document.querySelector('.dropdown');
        const btn = document.querySelector('.drop-btn');

        btn.addEventListener('click', function(e) {
            if (window.innerWidth < 768) {
                dropdown.classList.toggle('open');
                e.stopPropagation();
            }
        });

        // close when clicking outside
        document.addEventListener('click', () => {
            dropdown.classList.remove('open');
        });
    </script>

    <!-- SELECT2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            $('.select2').select2({
                // placeholder: "Select options",
                width: 'resolve',
                allowClear: true
            });
        });
    </script>

    <script>
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('show');
            });
        }

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('show');
        });
    </script>

    {{-- Global Department-Wise Score Popup --}}
    @include('components.dept-score-popup')

</body>

</html>
