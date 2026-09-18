@extends('layout.supervisor.app')

@section('content')

    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">

                    <div class="welcome-box2">
                        <h1>Applied Job Details</h1>
                    </div>

                    <div class="job-detail-container">

                        <div class="job-detail-card">
                            <div class="job-detail-card-border"></div>

                            {{-- JOB ID --}}
                            <div class="job-detail-id-badge">
                                Job ID: {{ $job->id }}
                            </div>

                            {{-- TITLE --}}
                            <h1 class="job-detail-title">
                                {{ $job->title }}
                            </h1>

                            {{-- DESCRIPTION --}}
                            <h3 class="job-detail-section-heading">Description:</h3>
                            <div class="job-detail-description-box">
                                <p class="job-detail-description-text">
                                    {{ $job->description }}
                                </p>
                            </div>

                            {{-- SKILLS --}}
                            <h3 class="job-detail-section-heading">Skills Needed:</h3>
                            <div class="job-detail-skills-wrapper">
                                @if($job->skills->count())
                                    @foreach($job->skills as $skillRow)
                                        <span class="job-detail-skill-badge">
                                            {{ $skillRow->skill->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="job-detail-skill-badge">No Skills Added</span>
                                @endif
                            </div>

                            {{-- EXPERIENCE --}}
                            <h3 class="job-detail-section-heading">Experience Required:</h3>
                            <div class="job-detail-experience-wrapper">

                                @if($job->years)
                                    <span class="job-detail-experience-text">
                                        {{ $job->years }} Years
                                    </span>
                                @else
                                    <span class="job-detail-experience-text">No Experience</span>
                                @endif

                            </div>

                            {{-- APPLICATION STATUS --}}
                            <h3 class="job-detail-section-heading">Application Status:</h3>
                            <div class="job-detail-experience-wrapper">
                                <span class="job-detail-experience-text">
                                    {{ ucfirst($applied->status) }}
                                </span>
                            </div>





                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection