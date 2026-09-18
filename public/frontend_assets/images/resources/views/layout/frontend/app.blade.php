<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillFetch  {{$pageTitle ? '- '. $pageTitle:''}}</title>
    <link rel="short-cut icon" href="{{asset('frontend_assets/images/favicon.png')}}" type="image/png">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/style.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Carlito:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{asset('frontend_assets/css/bootstrap.min.css')}}">
    <link href='https://fonts.googleapis.com/css?family=Space Grotesk' rel='stylesheet'>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@200,300,400,500,600,700,800&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>

    <header class="header">
        <nav class="nav-container">

            <div class="nav-left">
                <div class="logo-section">
                    <a href="{{route('frontend.home')}}">
                        <img src="{{asset('frontend_assets/images/logo.png')}}" alt="logo">
                    </a>
                </div>

                <ul class="nav-menu" id="navMenu">
                    <li class="nav-item"><a href="{{route('frontend.home')}}" class="nav-link ai-link">Home</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#skill-verified" class="nav-link">Skill Verified</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#how-it-works" class="nav-link">How It Works</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#what-makee-diffrence" class="nav-link">What Makes Us Different</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#who-is-orker" class="nav-link">Who Is It For</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#evealute-skills" class="nav-link">How We Evaluate Skills</a></li>
                    <li class="nav-item"><a href="{{ url('/') }}#contact-us" class="nav-link">Contact Us</a></li>
                </ul>
            </div>

            <div class="nav-right">
                <a href="{{route('frontend.login')}}" class="btn-call">
                    <span class="text-wrap">
                        <span class="text default-text">Log In</span>
                        <span class="text hover-text">Log In</span>
                    </span>
                    <span class="icon"><i class='bx bx-right-arrow-alt bx-flashing'></i></span>
                </a>

                <a href="{{route('frontend.register')}}" class="btn-start">
                    Register
                    <span class="icon"><i class='bx bx-right-arrow-alt bx-tada'></i></span>
                </a>
            </div>

            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

        </nav>
    </header>

    {{-- MAIN CONTENT GOES HERE --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER (EXACT COPY OF STATIC) --}}
    

    <footer class="footer-section footer-2 footer-widget-section dark-widget">
        <div class="art-board">
            <img src="{{asset('frontend_assets/images/art-board-2.png')}}" alt="logo">
        </div>
        <div class="footer-top">
            <div class="footer-bg"></div>
            <!-- <div class="truck"></div>
            <div class="truck-2"></div>
            <div class="truck-3"></div> -->
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-9">
                        <div class="row justify-content-center">
                            <div class="col-lg-3 col-md-6 sm-padding">
                                <div class="footer-widget subscribe">
                                    <div class="widget-box subscribe-widget">
                                        <div class="widget-title">
                                            <div><img src="{{asset('frontend_assets/images/launching_hyderabad.png')}}" alt="logo"></div>
                                            <h3>Proudly Launched in Hyderabad</h3>
                                            <p>From the City of Pearls to every workplace, Orker is creating a smarter
                                                way for skills to meet opportunity.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 sm-padding">
                                <div class="footer-widget about-widget">
                                    <a href="{{route('frontend.home')}}" class="brand"><img src="{{asset('frontend_assets/images/logo-white.png')}}" alt="logo"></a>
                                    <p>Built for professionals who value quality, trust, and verified construction
                                        expertise</p>
                                    <div class="footer-icon-box">
                                        <div><svg height="448pt" viewBox="0 -5 448 447" width="448pt"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="m448 264.5v-264h-368v56h296c4.417969 0 8 3.582031 8 8v200zm0 0">
                                                </path>
                                                <path
                                                    d="m178.34375 338.84375c1.5-1.5 3.535156-2.34375 5.65625-2.34375h184v-264h-368v264h72c4.417969 0 8 3.582031 8 8v92.6875zm0 0">
                                                </path>
                                            </svg></div>
                                        <div class="footer-icon-content">
                                            <h3>Need Help? Connect Now</h3><a href="tel:+91-8217204994"
                                                target="_blank">Call Now</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 sm-padding">
                                <div class="footer-menu">
                                    <div class="widget-box">
                                        <h3>Important Links</h3>
                                        <div class="footer-menu-inners">
                                            <ul class="menu">
                                                <li class="menu-li">
                                                    <i class='bx bxs-label'></i><a href="{{ url('/') }}#skill-verified">Skill Verified</a>
                                                </li>
                                                <li class="menu-li">
                                                    <i class='bx bxs-label'></i> <a href="{{ url('/') }}#how-it-works">How It Works</a>
                                                </li>
                                                <li class="menu-li">
                                                    <i class='bx bxs-label'></i><a href="{{ url('/') }}#what-makee-diffrence">What Makes Us Different</a>
                                                </li>
                                                <li class="menu-li">
                                                    <i class='bx bxs-label'></i><a href="{{ url('/') }}#who-is-orker">Who Is It For</a>
                                                </li>
                                                <li class="menu-li">
                                                    <i class='bx bxs-label'></i> <a href="{{ url('/') }}#evealute-skills">How We Evaluate Skills</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-md-start text-left">
                                <div class="site-info">
                                    Orker ©2026
                                </div>
                            </div>

                            <div class="col-md-6 text-md-end text-right">
                                <div class="footer-links">
                                    <a href="{{route('frontend.termsConditions')}}">Terms & Conditions</a>
                                    <span class="sep">|</span>
                                    <a href="{{route('frontend.privacy')}}">Privacy Policy</a>
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <!---header-->
   <script>
    const menuToggle = document.getElementById('menuToggle');
    const navMenu = document.getElementById('navMenu');
    const header = document.querySelector('.header');

    // Toggle mobile menu
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu on outside click
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !menuToggle.contains(e.target) && navMenu.classList.contains('active')) {
            menuToggle.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });

    // Keep header fixed & add class on scroll
    window.addEventListener('scroll', () => {
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
</script>

    <script>
        const tabs = document.querySelectorAll('.tab-btn');
        const wrappers = document.querySelectorAll('.process-wrapper');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                wrappers.forEach(w => w.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    </script>

    <script src="{{asset('frontend_assets/js/bootstrap.min.js')}}"></script>
</body>

</html>