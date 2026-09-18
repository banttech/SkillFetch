@extends('layout.frontend.app')

@section('content')
    <style>
        body {
            font-family: 'Carlito',
                sans-serif !important;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            display: block;
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group input.error,
        .form-group textarea.error {
            border-color: #dc3545;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .find-suoervisor-buttons:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
.div-common-btn .find-suoervisor-buttons {
   
     width: max-content;
}
    </style>

    <div class="new-all-fonts">
        <section class="section-banner">
            <div class="container-fluid">
                <div class="hero-section">
                    <div class="background">
                        <img src="{{ asset('frontend_assets/images/banner-11.png') }}" alt="Professional workspace">
                    </div>
                    <div class="content">
                        <div class="content-wrapper">
                            <h1>
                                Hire Qualified Site Supervisors You Can Trust
                            </h1>
                            <div class="tab-content-container">
                                <h3>Select your role to continue as a Supervisor or Employer</h3>
                                <p>Choose whether you are a Supervisor or Employer to access role-specific tools,
                                    dashboards, and features built for
                                    efficiency.</p>
                                <div class="tabs"><a href="{{ route('supervisor.login.view') }}">
                                        <div class="tab-button">
                                            Supervisor
                                        </div>
                                    </a>
                                    <a href="{{ route('employer.login.view') }}">
                                        <div class="tab-button">
                                            Employer
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="list-tests" id="skill-verified">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h2 class=" com-h2-h2 ">Hire <span style="color: #80929c;">Skill-Verified</span> Construction <br>
                            Professionals
                        </h2>
                        <div class="feature-wrap multi">

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-building-house'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>RCC & Structure</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-paint'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Finishing & Interiors</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-water'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Plumbing & Sanitation</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-bolt-circle'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Electrical Works</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-shield-quarter'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Waterproofing</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-grid-alt'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Flooring & Tiling</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-check-shield'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Safety & Compliance</h4>
                                </div>
                            </div>

                            <div class="feature-box">
                                <div class="feature-icon">
                                    <i class='bx bx-network-chart'></i>
                                </div>
                                <div class="feature-content">
                                    <h4>MEP Coordination</h4>
                                </div>
                            </div>

                        </div>

                    </div>
        </section>


        <section class="process-section" id="how-it-works">
            <div class="image-tabs">
                <img src="{{ asset('frontend_assets/images/testimonial-image-bg.png') }}" alt="image">
            </div>
            <div class="container">
                <div class="col-md-12">
                    <h2 class="process-section-com ">How it works
                    </h2>
                </div>
                <!-- Tabs -->
                <div class="steps-tabs">
                    <button class="tab-btn active" data-tab="owner"><img
                            src="{{ asset('frontend_assets/images/Caretaker.png') }}" alt="Architect">Architect / Property
                        Owner</button>
                    <button class="tab-btn" data-tab="supervisor"><img
                            src="{{ asset('frontend_assets/images/Manager.png') }}" alt="Supervisors">Site
                        Supervisor</button>
                </div>

                <!-- PROPERTY OWNER -->
                <div class="process-wrapper active" id="owner">

                    <div class="process-step">
                        <span class="step-badge">STEP 1</span>
                        <h3>Post Your Requirement</h3>
                        <div class="step-icon">
                            <i class='bx bx-edit'></i>
                        </div>
                        <p>Share your project details and the skills you need. Posting is quick and free.</p>
                        <a class="step-btn">Post Your Project</a>
                    </div>

                    <div class="process-step">
                        <span class="step-badge">STEP 2</span>
                        <h3>View Verified Supervisors</h3>
                        <div class="step-icon">
                            <i class='bx bx-user-check'></i>
                        </div>
                        <p>Browse skill-verified supervisors matched to your requirements.</p>
                        <a class="step-btn">Explore Verified Talent</a>
                    </div>

                    <div class="process-step">
                        <span class="step-badge">STEP 3</span>
                        <h3>Hire with Confidence</h3>
                        <div class="step-icon">
                            <i class='bx bx-check-shield'></i>
                        </div>
                        <p>Select the right supervisor and start your project with clarity and trust.</p>
                        <a class="step-btn">Hire a Supervisor</a>
                    </div>

                </div>

                <!-- SUPERVISOR -->
                <div class="process-wrapper" id="supervisor">

                    <div class="process-step">
                        <span class="step-badge">STEP 1</span>
                        <h3>Create Your Profile</h3>
                        <div class="step-icon">
                            <i class='bx bx-id-card'></i>
                        </div>
                        <p>Sign up, add your experience, and complete the qualification test.</p>
                        <a class="step-btn">Create Supervisor Profile</a>
                    </div>

                    <div class="process-step">
                        <span class="step-badge">STEP 2</span>
                        <h3>Get Skill Verified</h3>
                        <div class="step-icon">
                            <i class='bx bx-badge-check'></i>
                        </div>
                        <p>Show your expertise through Orker’s verification process.</p>
                        <a class="step-btn">Take Qualification Test</a>
                    </div>

                    <div class="process-step">
                        <span class="step-badge">STEP 3</span>
                        <h3>Get Hired for Projects</h3>
                        <div class="step-icon">
                            <i class='bx bx-briefcase'></i>
                        </div>
                        <p>Connect with genuine architects, owners, and developers looking for your skills.</p>
                        <a class="step-btn">Apply for Project</a>
                    </div>

                </div>
            </div>
        </section>



        <section class="orker-make-differnt" id="what-makee-diffrence">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <h2 class="com-h2-h2 ">What Makes
                            <span style="color: #80929c;">Orker</span> Different
                        </h2>
                        <h4>A construction-first platform built on verification, trust, and skill-based hiring.
                        </h4>

                        <div class="values-section">
                            <div class="values-grid">
                                <!-- Card 1: Building With Trust -->
                                <div class="value-card">
                                    <img src="{{ asset('frontend_assets/images/exp.webp') }}" alt="Construction Planning"
                                        class="card-image">
                                    <div class="card-overlay"></div>

                                    <div class="card-icon-wrapper">
                                        <div class="card-icon"><i class='bx bx-badge-check'></i></div>
                                    </div>

                                    <div class="card-content-box">
                                        <h3 class="card-title">Skill Verification</h3>
                                        <div class="card-description2">
                                            <p>Every supervisor on Orker is qualified before being
                                                visible to clients.
                                            </p>
                                            <ul>
                                                <li><i class='bx bxs-hard-hat'></i>Online qualification test</li>
                                                <li><i class='bx bxs-hard-hat'></i>Skill-based evaluation</li>
                                                <li><i class='bx bxs-hard-hat'></i>Only verified profiles listed</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Experience & Quality -->
                                <div class="value-card">
                                    <img src="{{ asset('frontend_assets/images/our-story-bg-image.jpg') }}"
                                        alt="Construction Planning" class="card-image">
                                    <div class="card-overlay"></div>

                                    <div class="card-icon-wrapper">
                                        <div class="card-icon"><i class='bx bx-building-house'></i></div>
                                    </div>

                                    <div class="card-content-box">
                                        <h3 class="card-title">Construction-Focused Platform</h3>
                                        <div class="card-description2">
                                            <p>Orker is designed exclusively for construction supervision roles.

                                            </p>
                                            <ul>
                                                <li><i class='bx bxs-hard-hat'></i>Built for site supervisors
                                                </li>
                                                <li><i class='bx bxs-hard-hat'></i>No generic job listings
                                                </li>
                                                <li><i class='bx bxs-hard-hat'></i>Clear construction-specific skills
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3: Health & Safety -->
                                <div class="value-card">
                                    <img src="{{ asset('frontend_assets/images/skils.webp') }}"
                                        alt="Construction Planning" class="card-image">
                                    <div class="card-overlay"></div>

                                    <div class="card-icon-wrapper">
                                        <div class="card-icon"><i class='bx bx-brain'></i></div>
                                    </div>

                                    <div class="card-content-box">
                                        <h3 class="card-title">Smart Skill Matching
                                        </h3>
                                        <div class="card-description2">
                                            <p>Find supervisors matched precisely to your project needs.

                                            </p>
                                            <ul>
                                                <li><i class='bx bxs-hard-hat'></i>Skill-tagged profiles
                                                </li>
                                                <li><i class='bx bxs-hard-hat'></i>Faster shortlisting
                                                </li>
                                                <li><i class='bx bxs-hard-hat'></i>Better project alignment
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="value-card">
                                    <img src="{{ asset('frontend_assets/images/new-bg.webp') }}"
                                        alt="Construction Planning" class="card-image">
                                    <div class="card-overlay"></div>

                                    <div class="card-icon-wrapper">
                                        <div class="card-icon"><i class='bx bx-shield-quarter'></i></div>
                                    </div>

                                    <div class="card-content-box">
                                        <h3 class="card-title">Trust & Transparency
                                        </h3>
                                        <div class="card-description2">
                                            <p>Hire with confidence using verified data and clear profiles.
                                            </p>
                                            <ul>
                                                <li><i class='bx bxs-hard-hat'></i>Verified experience details
                                                </li>
                                                <li><i class='bx bxs-hard-hat'></i>Transparent skill visibility</li>
                                                <li><i class='bx bxs-hard-hat'></i>Reduced hiring risk
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="div-common-btn">
                            <a href="{{ route('employer.login.view') }}" class="find-suoervisor-buttons">Find a Verified
                                Supervisor</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <div class="bg-two-box-design" id="who-is-orker">

            <div class="container-fluid">
                <h2 class="process-section-com ">Who Is
                    Orker For?
                </h2>
                <h4>Built for professionals who value quality, trust, and verified construction expertise.
                </h4>
                <div class="pricing-container">
                    <div class="row justify-content-center">
                        <!-- Basic Plan -->
                        <div class="col-lg-6 col-md-6 mb-4 mb-lg-0">
                            <div class="pricing-card">
                                <div class="bg-bottom-cart">
                                    <img src="{{ asset('frontend_assets/images/artboard-1.png') }}" alt="bg-image">
                                </div>
                                <h2 class="plan-title">Architects & Property Owners
                                </h2>
                                <p class="plan-subtitle">Best for hiring verified site supervisors
                                </p>
                                <p class="service-fee"><img src="{{ asset('frontend_assets/images/builders.png') }}"
                                        alt="builder">Who it’s for:
                                </p>
                                <ul class="features-list">
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Independent house owners
                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Architects & design studios
                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Property developers & builders
                                        </span>
                                    </li>
                                </ul>
                                <p class="service-fee"><img src="{{ asset('frontend_assets/images/architecture.png') }}"
                                        alt="architecture">Why Orker works
                                    for you:

                                </p>
                                <ul class="features-list">
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Access skill-verified supervisors
                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Faster, risk-free hiring
                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Pay only after hiring
                                        </span>
                                    </li>
                                </ul>

                                <a href="{{ route('employer.login.view') }}"><button class="btn-custom">Get
                                        Verified</button></a>
                            </div>
                        </div>

                        <!-- Business Plus Plan -->
                        <div class="col-lg-6 col-md-6">
                            <div class="pricing-card">
                                <div class="bg-bottom-cart2">
                                    <img src="{{ asset('frontend_assets/images/artboard-1.png') }}" alt="bg-image">
                                </div>
                                <h2 class="plan-title">Site Supervisors
                                </h2>
                                <p class="plan-subtitle">Best for finding quality projects

                                </p>
                                <p class="service-fee"><img src="{{ asset('frontend_assets/images/builders.png') }}"
                                        alt="builder">Who it’s for:

                                </p>
                                <ul class="features-list">
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Experienced site supervisors

                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Freelance supervisors

                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Site in-charges & project coordinators

                                        </span>
                                    </li>
                                </ul>
                                <p class="service-fee"><img src="{{ asset('frontend_assets/images/architecture.png') }}"
                                        alt="architecture">Why Orker works
                                    for you:

                                </p>
                                <ul class="features-list">
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Get skill-verified visibility

                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Connect with genuine clients

                                        </span>
                                    </li>
                                    <li>
                                        <i class='bx bx-right-arrow-alt'></i>
                                        <span>Get hired for the right projects

                                        </span>
                                    </li>
                                </ul>

                                <a href="{{ route('supervisor.login.view') }}"><button class="btn-custom">Find
                                        Work</button></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <section class="customer-sec-padd-new teledoc-clone-steps" id="evealute-skills">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-11">
                        <h2 class="com-h2-h2 ">Skill

                            <span style="color: #80929c;">Verification</span> Process
                        </h2>
                        <h4>Every verified badge on Orker is earned through a structured and transparent evaluation process.

                        </h4>
                        <ul class="work-flow-list">
                            <li>
                                <div class="div-left-icons">
                                    <p class="suffesv3-sub-heading">01</p>
                                    <div class="work-icon-wrap">
                                        <div class="sub-icons">
                                            <div class="sub-icons2">
                                                <img src="{{ asset('frontend_assets/images/one.png') }}" alt="image">
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="subheading">Skill Registration
                                    </h4>
                                    <p class="suffesv3-sub-para">Supervisors register for each specific skill they claim.
                                    </p>
                                    <p><span><i class='bx bxs-right-arrow'></i>One skill, one evaluation
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>No generic or blanket verification
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Clear skill-wise categorisation
                                        </span></p>
                                </div>
                            </li>
                            <li>
                                <div class="div-left-icons">
                                    <p class="suffesv3-sub-heading">02</p>
                                    <div class="work-icon-wrap">
                                        <div class="sub-icons">
                                            <div class="sub-icons2"><img
                                                    src="{{ asset('frontend_assets/images/two.png') }}" alt="image">
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="subheading">Skill Assessment Test
                                    </h4>
                                    <p class="suffesv3-sub-para">A structured qualification test is conducted for each
                                        skill.
                                    </p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Practical, job-relevant questions
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Safety and quality focused

                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Minimum passing criteria required

                                        </span></p>
                                </div>
                            </li>

                            <li>
                                <div class="div-left-icons">
                                    <p class="suffesv3-sub-heading">04</p>
                                    <div class="work-icon-wrap">
                                        <div class="sub-icons">
                                            <div class="sub-icons2"><img
                                                    src="{{ asset('frontend_assets/images/four.png') }}" alt="image">
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="subheading">Verified Badge Issued
                                    </h4>
                                    <p class="suffesv3-sub-para">Only qualified supervisors receive a verified skill badge.
                                    </p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Skill-specific verified badge
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Visible on supervisor profile
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Trusted by architects & property owners
                                        </span></p>

                                </div>
                            </li>
                            <li>
                                <div class="div-left-icons">
                                    <p class="suffesv3-sub-heading">03</p>
                                    <div class="work-icon-wrap">
                                        <div class="sub-icons">
                                            <div class="sub-icons2"><img
                                                    src="{{ asset('frontend_assets/images/three.png') }}" alt="image">
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="subheading">Manual Expert Review
                                    </h4>
                                    <p class="suffesv3-sub-para">Test responses and skill claims are manually reviewed.
                                    </p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Experience validation
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Scenario-based judgement
                                        </span></p>
                                    <p><span><i class='bx bxs-right-arrow'></i>Cross-check of practical understanding
                                        </span></p>

                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="career-cta">
            <div class="career-wrap">
                <div class="career-img">
                    <img src="{{ asset('frontend_assets/images/new-bg.webp') }}" alt="Join Team">
                </div>

                <div class="career-content">
                    <h2>Your Skills. Your Work. <span>Your Future.</span></h2>
                    <p>A place where your skills are verified and your work is valued—connect with real opportunities and
                        grow
                        with confidence.</p>
                </div>

                <a href="#" class="career-arrow">
                    <span class="icon-arrow"></span>
                </a>
            </div>
        </section>

        <section class="contact-us-new" id="contact-us">
            <!-- <div class="img-3"><img src="images/img-head-3.png" alt="image"></div> -->
            <div class="container-fluid">
                <div class="main-div-contacts">
                    <div class="div-contacts-images">
                        <img src="{{ asset('frontend_assets/images/Artboard 1_6.png') }}" alt="">
                    </div>
                    <div class="contact-form-inner">
                        <div class="contact-fomr-sec">
                            <div class="contact-section">
                                <div class="tag">Connect With Us</div>
                                <h1 class="main-heading">Get In Touch With Us</h1>
                                <p class="description">
                                    Have a question in mind? We're here to help you take the next step
                                    with confidence.
                                </p>
                                <div class="info-box">
                                    <div class="info-item">
                                        <strong>Address:</strong> Lorem ipsum dolor sit amet consectetur
                                    </div>
                                    <div class="info-item">
                                        <strong>Mail:</strong> sales@orker.in
                                    </div>
                                    <div class="info-item">
                                        <strong>Support Hours:</strong> Mon - Sat (9 AM to 6 PM)
                                    </div>
                                </div>
                                <a href="tel:+971555961659" class="call-button">
                                    <div class="phone-icon-box">
                                        <svg class="phone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                    </div>
                                    <div class="call-text">
                                        Call Us: +91-8217204994
                                    </div>
                                </a>
                            </div>
                        </div>
                        {{-- <div class="contact-form-sec2">
                        <div class="form-card">
                            <h3>Start The Conversation</h3>
                            <p>Let us know how we can help — we’ll be in touch soon.</p>

                            <form>
                                <div class="form-group">
                                    <input type="text" placeholder="Enter your name *">
                                </div>

                                <div class="form-group">
                                    <input type="email" placeholder="Enter your email *">
                                </div>

                               <div class="form-group">
                                    <input type="text" placeholder="Enter your phone *">
                                </div>

                                <div class="form-group">
                                    <textarea placeholder="Enter your message *"></textarea>
                                </div>

                                <div class="submit-wrap">
                                    <a href="#" class="find-suoervisor-buttons">Submit</a>
                                </div>
                            </form>
                        </div>
                    </div> --}}

                      <div class="contact-form-sec2">
    <div class="form-card">
        <h3>Start The Conversation</h3>
        <p>Let us know how we can help — we'll be in touch soon.</p>

        <div id="successMessage" class="alert alert-success" style="display: none;"></div>
        <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>

        <form id="contactForm">
            @csrf
            <div class="form-group">
                <input type="text" name="name" id="name" placeholder="Enter your name *">
                <span class="error-text" id="name-error"></span>
            </div>

            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Enter your email *">
                <span class="error-text" id="email-error"></span>
            </div>

            <div class="form-group">
                <input type="text" name="phone" id="phone" placeholder="Enter your phone *">
                <span class="error-text" id="phone-error"></span>
            </div>

            <div class="form-group">
                <textarea name="message" id="message" placeholder="Enter your message *"></textarea>
                <span class="error-text" id="message-error"></span>
            </div>

            <div class="submit-wrap">
                <button type="submit" class="find-suoervisor-buttons" id="submitBtn">Submit</button>
            </div>
        </form>
    </div>
</div>
                    </div>
                </div>
            </div>

        </section>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
    // Client-side validation function
    function validateForm() {
        let isValid = true;
        
        // Clear previous errors
        $('.error-text').text('');
        $('.form-group input, .form-group textarea').removeClass('error');
        
        // Name validation
        const name = $('#name').val().trim();
        if (name === '') {
            $('#name-error').text('Name field is required.');
            $('#name').addClass('error');
            isValid = false;
        } else if (name.length > 255) {
            $('#name-error').text('Name must not exceed 255 characters.');
            $('#name').addClass('error');
            isValid = false;
        }
        
        // Email validation
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '') {
            $('#email-error').text('Email field is required.');
            $('#email').addClass('error');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            $('#email-error').text('Please enter a valid email address.');
            $('#email').addClass('error');
            isValid = false;
        }
        
        // Phone validation (Indian phone number)
        const phone = $('#phone').val().trim();
        const phoneRegex = /^[6-9]\d{9}$/;
        if (phone === '') {
            $('#phone-error').text('Phone field is required.');
            $('#phone').addClass('error');
            isValid = false;
        } else if (!phoneRegex.test(phone)) {
            $('#phone-error').text('Please enter a valid Indian phone number.');
            $('#phone').addClass('error');
            isValid = false;
        }
        
        // Message validation
        const message = $('#message').val().trim();
        if (message === '') {
            $('#message-error').text('Message field is required.');
            $('#message').addClass('error');
            isValid = false;
        } else if (message.length > 1000) {
            $('#message-error').text('Message must not exceed 1000 characters.');
            $('#message').addClass('error');
            isValid = false;
        }
        
        return isValid;
    }
    
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();

        // Clear previous messages
        $('#successMessage, #errorMessage').hide();
        
        // Validate form before submitting
        if (!validateForm()) {
            return false;
        }

        // Get form data
        const formData = {
            name: $('#name').val().trim(),
            email: $('#email').val().trim(),
            phone: $('#phone').val().trim(),
            message: $('#message').val().trim(),
            _token: $('input[name="_token"]').val()
        };

        // Disable submit button and change text
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Submitting...');

        // AJAX request
        $.ajax({
            url: '{{ route("contact.submit") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#successMessage').text(response.message).fadeIn();
                    $('#contactForm')[0].reset();
                    
                    // Auto hide success message after 5 seconds
                    setTimeout(function() {
                        $('#successMessage').fadeOut();
                    }, 5000);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation errors from server
                    const errors = xhr.responseJSON.errors;
                    
                    $.each(errors, function(field, messages) {
                        $('#' + field + '-error').text(messages[0]);
                        $('#' + field).addClass('error');
                    });
                } else {
                    // Server error
                    const message = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
                    $('#errorMessage').text(message).fadeIn();
                }
            },
            complete: function() {
                // Re-enable submit button and restore text
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Clear error on input
    $('input, textarea').on('input', function() {
        const fieldName = $(this).attr('name');
        $('#' + fieldName + '-error').text('');
        $(this).removeClass('error');
    });
    
    // Real-time phone validation
    $('#phone').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });
});
</script>
@endsection
