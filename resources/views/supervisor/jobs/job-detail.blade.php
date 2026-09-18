@extends('layout.supervisor.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="welcome-box2">
                        <h1>Job Detail</h1>
                    </div>



                    <div class="job-detail-container">
                        <div class="job-detail-card">
                            <div class="job-detail-card-border"></div>

                            {{-- JOB ID --}}
                            <div class="job-detail-id-badge">
                                Job ID: JOB#{{ $job->id }}
                            </div>

                            {{-- JOB TITLE --}}
                            <h1 class="job-detail-title">
                                Job Title: {{ ucfirst($job->title) }}
                            </h1>

                            {{-- EMPLOYER INFO --}}
                            {{-- @if ($job->employer && $job->employer->user)
                                <div class="employer-info-badge">
                                    <i class="fas fa-building"></i>
                                    <span>{{ $job->employer->user->name }}</span>
                                </div>
                            @endif --}}

                            {{-- DESCRIPTION --}}
                            <h3 class="job-detail-section-heading">
                                <i class="fas fa-file-alt"></i> Description
                            </h3>
                            <div class="job-detail-description-box">
                                <p class="job-detail-description-text">
                                    {{ $job->description }}
                                </p>
                            </div>

                            {{-- EXPERIENCE REQUIRED --}}
                            <h3 class="job-detail-section-heading">
                                <i class="fas fa-calendar-check"></i> Experience Required
                            </h3>
                            <div class="job-detail-experience-wrapper">
                                @if ($job->years)
                                    <span class="experience-text">{{ $job->years }} Years</span>
                                @else
                                    <span class="experience-text">No Specific Experience Required</span>
                                @endif
                            </div>

                            {{-- ADMIN SKILLS --}}
                            <h3 class="job-detail-section-heading">
                                <i class="fas fa-tools"></i>Skills Needed
                            </h3>

                           
                            <div class="job-detail-skills-wrapper">
                                @if ($job->skills->count())
                                    {{-- Matching Skills --}}
                                    @foreach ($matchingSkills as $skill)
                                        <span class="job-detail-skill-badge skill-matched-badge">
                                            <i class="fas fa-check-circle"></i> {{ ucfirst($skill->name) }}
                                        </span>
                                    @endforeach

                                    {{-- Non-Matching Skills --}}
                                    @foreach ($nonMatchingSkills as $skill)
                                        <span class="job-detail-skill-badge skill-not-matched-badge">
                                            {{ ucfirst($skill->name) }}
                                        </span>
                                    @endforeach

                                    @foreach ($job->employerSkills as $skill)
                                       <span class="job-detail-skill-badge skill-not-matched-badge">
                                            {{ ucfirst($skill->skills) }}
                                        </span>
                                    @endforeach
                               
                                @endif
                            </div>

                            {{-- EXPERIENCE AREAS --}}
                            @if ($job->experiences->count())
                                <h3 class="job-detail-section-heading">
                                    <i class="fas fa-briefcase"></i> Experience Areas
                                </h3>
                                <div class="job-detail-skills-wrapper">
                                    {{-- Matching Experiences --}}
                                    @foreach ($matchingExperiences as $experience)
                                        <span class="job-detail-skill-badge skill-matched-badge">
                                            <i class="fas fa-check-circle"></i> {{ ucfirst($experience->name) }}
                                        </span>
                                    @endforeach

                                    {{-- Non-Matching Experiences --}}
                                    @foreach ($nonMatchingExperiences as $experience)
                                        <span class="job-detail-skill-badge skill-not-matched-badge">
                                            {{ ucfirst($experience->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                           
                            {{-- WORK LOCATIONS --}}
                            <h3 class="job-detail-section-heading">
                                <i class="fas fa-map-marker-alt"></i> Preferred Work Locations
                            </h3>
                            <div class="job-detail-skills-wrapper">
                                @if ($job->workLocations->count())
                                    @foreach ($job->workLocations as $loc)
                                        <span class="job-detail-skill-badge skill-not-matched-badge">
                                            {{ ucfirst($loc->name) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="job-detail-skill-badge">No Specific Location</span>
                                @endif
                            </div>

                            {{-- SALARY & ALLOWANCES --}}
                            <h3 class="job-detail-section-heading">
                                <i class="fas fa-money-bill-wave"></i> Salary & Allowances
                            </h3>
                            <div class="job-detail-description-box" style="padding: 15px;">
                                <p style="margin-bottom: 8px; color: #475569; font-size: 15px;"><strong
                                        style="color: #00263A;">Fixed Salary <span style="font-size: 12px;color:#2a2a2a;">(In Rs.) (Per Month)</span>:</strong>
                                    {{ $job->fixed_salary ?? 'N/A' }}</p>
                                <p style="margin-bottom: 8px; color: #475569; font-size: 15px;"><strong
                                        style="color: #00263A;">Variable Salary <span style="font-size: 12px;color:#2a2a2a;">(In Rs.) (Per Month)</span>:</strong>
                                    {{ $job->variable_salary_from ?? '0' }} - {{ $job->variable_salary_to ?? '0' }}</p>
                                <p style="margin-bottom: 8px; color: #475569; font-size: 15px;"><strong
                                        style="color: #00263A;">Petrol Allowance <span style="font-size: 12px;color:#2a2a2a;">(In Rs.) (Per Month)</span>:</strong>
                                    {{ $job->petrol_allowance ?? '0' }}</p>
                                <p style="margin-bottom: 8px; color: #475569; font-size: 15px;"><strong
                                        style="color: #00263A;">Accommodation <span style="font-size: 12px;color:#2a2a2a;">(In Rs.) (Per Month)</span>:</strong>
                                    {{ $job->accommodation ?? '0' }}</p>
                                <p style="margin-bottom: 0px; color: #475569; font-size: 15px;"><strong
                                        style="color: #00263A;">Food Allowance <span style="font-size: 12px;color:#2a2a2a;">(In Rs.) (Per Month)</span>:</strong>
                                    {{ $job->food_allowance ?? '0' }}</p>
                            </div>

                            {{-- MATCH SCORE --}}
                            <div class="match-score-section">
                                <h4 class="match-score-heading">
                                    <i class="fas fa-chart-line"></i> Your Match Score
                                </h4>
                                <div class="match-score-items">
                                    <div class="score-row">
                                        <span class="score-text">Skills Match:</span>
                                        <span class="score-numbers">{{ $matchingSkills->count() }} /
                                            {{ $job->skills->count() }}</span>
                                        @php
                                            $skillPercent =
                                                $job->skills->count() > 0
                                                    ? round(($matchingSkills->count() / $job->skills->count()) * 100)
                                                    : 0;
                                        @endphp
                                        <div class="mini-progress-bar">
                                            <div class="mini-progress-fill" style="width: {{ $skillPercent }}%"></div>
                                        </div>
                                    </div>
                                    <div class="score-row">
                                        <span class="score-text">Experience Areas:</span>
                                        <span class="score-numbers">{{ $matchingExperiences->count() }} /
                                            {{ $job->experiences->count() }}</span>
                                        @php
                                            $expPercent =
                                                $job->experiences->count() > 0
                                                    ? round(
                                                        ($matchingExperiences->count() / $job->experiences->count()) *
                                                            100,
                                                    )
                                                    : 0;
                                        @endphp
                                        <div class="mini-progress-bar">
                                            <div class="mini-progress-fill" style="width: {{ $expPercent }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ACTION BUTTONS --}}
                            <div class="action-buttons-section">
                                @if ($hasApplied)
                                    {{-- <button type="button" class="job-detail-apply-btn btn-already-applied" disabled>
                                        <i class="fas fa-check-circle"></i> Already Applied
                                    </button> --}}
                                @else
                                    <form action="{{ route('supervisor.apply.job', $job->id) }}" method="POST"
                                        style="display: inline-block;"
                                        onsubmit="return confirm('Are you sure you want to apply for this job?');">
                                        @csrf
                                        <button type="submit" class="job-detail-apply-btn">
                                            <i class="fas fa-paper-plane"></i> Apply Now
                                        </button>
                                    </form>
                                @endif

                                {{-- <a href="{{ route('supervisor.openjobs') }}" class="job-detail-back-button">
                                    <i class="fas fa-arrow-left"></i> Back to Open Jobs
                                </a> --}}
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
