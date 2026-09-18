<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillFetch - Admin {{ $pageTitle ?? '' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('admin_assets/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/admin-dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>

<body>

    <header class="header">
        <div class="logo">
            <a class="navbar-brand" href="{{route('frontend.home')}}"><img src="{{ asset('admin_assets/images/logo-orker.png') }}"
                    alt="logo"></a>
        </div>
        <div class="user-welcome">
            <div class="welcome-text">
                <h5>Welcome !!</h5>
                <hr>
                <span class="user-name">{{ Auth::user()->name ?? '' }}</span>
            </div>
            <div class="user-avatar">
                <img src="{{ asset('admin_assets/images/' . Auth::user()->image) }}" alt="user">
            </div>

            <div class="dropdown">
                <button class="drop-btn">
                    <i class="fa fa-chevron-down chevron"></i>
                </button>

                <ul class="drop-menu">
                    <li><a href="{{ route('admin.profile.view') }}"><i class="fa fa-eye" aria-hidden="true"></i>Edit
                            Profile</a></li>
                    <li><a href="{{ route('admin.password.edit') }}"><i class="fa fa-eye" aria-hidden="true"></i>Update
                            Password</a></li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('adminLogout').submit();">
                            <i class="fa fa-sign-out"></i> Logout
                        </a>
                        <form id="adminLogout" action="{{ route('admin.logout') }}" method="GET" style="display:none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 p-0">
                <nav class="sidebar">
                    <div class="nav-item">
                        <a class="nav-link active-link">
                            <img src="{{ asset('admin_assets/images/Document.png') }}" alt="Users">
                            <span>Qualification Test</span>
                        </a>
                    </div>
                    <div class="div-nav-items">
                        <div class="nav-item">
                            <a href="{{ route('admin.qualification.setuptest') }}" class="nav-link">
                                <img src="{{ asset('admin_assets/images/Document.png') }}" alt="Users">
                                <span>Setup Q & A</span>
                            </a>
                        </div>

                        <div class="nav-item border-non">
                            <a href="{{ route('admin.test-settings') }}" class="nav-link active">
                                <img src="{{ asset('admin_assets/images/Quiz.png') }}" alt="Users">
                                <span>Test Settings</span>
                            </a>
                        </div>

                      
                    </div><br><br>
                    <div class="nav-item">
                        <a  class="nav-link active-link">
                            <img src="{{ asset('admin_assets/images/user-one.png') }}" alt="Users">
                            <span>Users</span>
                        </a>
                    </div>
                    <div class="div-nav-items">
                        <div class="nav-item border-non">
                            <a href="{{ route('admin.supervisor.supervisors-List') }}" class="nav-link active">
                                <img src="{{ asset('admin_assets/images/writer-user.png') }}" alt="Users">
                                <span>Supervisors</span>
                            </a>
                        </div>
                        <div class="nav-item">
                            <a href="{{ route('admin.employer.employerList') }}" class="nav-link">
                                <img src="{{ asset('admin_assets/images/male-user.png') }}" alt="Users">
                                <span>Employers</span>
                            </a>
                        </div>
                        
                    </div><br><br>
                    <div class="nav-item">
                        <a  class="nav-link active-link">
                            <img src="{{ asset('admin_assets/images/user-one.png') }}" alt="Users">
                            <span>Others</span>
                        </a>
                    </div>
                    <div class="div-nav-items">
                     
                          <div class="nav-item border-non">
                            <a href="{{ route('admin.contacts') }}" class="nav-link active">
                                <img src="{{ asset('admin_assets/images/Quiz.png') }}" alt="Users">
                                <span>Contacts</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-lg-10 col-md-9">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>



    <script>
        const dropdown = document.querySelector('.dropdown');
        const btn = document.querySelector('.drop-btn');

        btn.addEventListener('click', function (e) {
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

    @yield('scripts')


</body>

</html>