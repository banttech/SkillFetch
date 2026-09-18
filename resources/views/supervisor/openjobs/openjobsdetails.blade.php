@extends('layout.supervisor.app')

@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">

                    <div class="welcome-box2">
                        <h1>Open Job Details</h1>
                    </div>

                    {{-- SUCCESS / ERROR MESSAGE --}}
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif


                    <div class="job-detail-container">

                        <div class="job-detail-card">
                            <div class="job-detail-card-border"></div>

                            {{-- JOB ID --}}
                            <div class="job-detail-id-badge">
                                Job ID: {{ $job->id }}
                            </div>

                            {{-- JOB TITLE --}}
                            <h1 class="job-detail-title">
                                Title : {{ $job->title }}
                            </h1>

                            {{-- DESCRIPTION --}}
                            <h3 class="job-detail-section-heading">Description :</h3>
                            <div class="job-detail-description-box">
                                <p class="job-detail-description-text">
                                    {{ $job->description }}
                                </p>
                            </div>

                            {{-- ADMIN SKILLS --}}
                            <h3 class="job-detail-section-heading">Skills:</h3>
                            <div class="job-detail-skills-wrapper">
                                @if($job->skills->count())
                                    @foreach($job->skills as $skillRow)
                                        <span class="job-detail-skill-badge">
                                            {{ $skillRow->name }}
                                        </span>
                                    @endforeach
                               
                            </div>

                            {{-- EMPLOYER SKILLS --}}
                            <h3 class="job-detail-section-heading">Employer Skills:</h3>
                            <div class="job-detail-skills-wrapper">
                                @if($job->employerSkills->count())
                                    @foreach($job->employerSkills as $skillRow)
                                        <span class="job-detail-skill-badge">
                                            {{ $skillRow->skills }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="job-detail-skill-badge">No Employer Skills Added</span>
                                @endif
                            </div>

                            {{-- EXPERIENCE DETAILS --}}
                            <h3 class="job-detail-section-heading">Experience:</h3>
                            <div class="job-detail-skills-wrapper">
                                @if($job->experiences->count())
                                    @foreach($job->experiences as $experience)
                                        <span class="job-detail-skill-badge">{{ $experience->name }}</span>
                                    @endforeach
                                @else
                                     <span class="job-detail-skill-badge">No Specific Experience</span>
                                @endif
                            </div>

                            {{-- TOTAL EXPERIENCE --}}
                            <h3 class="job-detail-section-heading">Total Experience:</h3>
                            <div class="job-detail-experience-wrapper">
                                <span class="experience-text">{{ $job->years ?? 'No Experience' }}</span>
                            </div>

                            {{-- PREFERRED WORK LOCATIONS --}}
                            <h3 class="job-detail-section-heading">Preferred Work Locations:</h3>
                            <div class="job-detail-skills-wrapper">
                                @if($job->workLocations->count())
                                    @foreach($job->workLocations as $loc)
                                        <span class="job-detail-skill-badge">{{ $loc->name }}</span>
                                    @endforeach
                                @else
                                    <span class="job-detail-skill-badge">No Locations Specified</span>
                                @endif
                            </div>

                            {{-- SALARY & ALLOWANCES --}}
                            <h3 class="job-detail-section-heading">Salary & Allowances:</h3>
                            <div class="job-detail-experience-wrapper">
                                <p style="margin-bottom: 5px; color: #475569; font-size: 15px;"><strong>Fixed Salary (In Rs.) (Per Month):</strong> {{ $job->fixed_salary ?? 'N/A' }}</p>
                                <p style="margin-bottom: 5px; color: #475569; font-size: 15px;"><strong>Variable Salary (In Rs.) (Per Month):</strong> {{ $job->variable_salary_from ?? '0' }} - {{ $job->variable_salary_to ?? '0' }}</p>
                                <p style="margin-bottom: 5px; color: #475569; font-size: 15px;"><strong>Petrol Allowance (In Rs.) (Per Month):</strong> {{ $job->petrol_allowance ?? '0' }}</p>
                                <p style="margin-bottom: 5px; color: #475569; font-size: 15px;"><strong>Accommodation (In Rs.) (Per Month):</strong> {{ $job->accommodation ?? '0' }}</p>
                                <p style="margin-bottom: 5px; color: #475569; font-size: 15px;"><strong>Food Allowance (In Rs.) (Per Month):</strong> {{ $job->food_allowance ?? '0' }}</p>
                            </div>

                            <br>

                            {{-- APPLY BUTTON --}}
                            <form action="{{ route('supervisor.apply.job', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="job-detail-apply-btn">
                                    Apply Now
                                    <svg class="job-detail-apply-icon" fill="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                                    </svg>
                                </button>
                            </form>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection