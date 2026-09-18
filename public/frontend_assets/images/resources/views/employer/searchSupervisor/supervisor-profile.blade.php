@extends('layout.employer.app')

@section('content')
    <style>

    </style>


    <div class="content-header">
        <h2>
            <i class="fas fa-user-circle"></i>
            Supervisor Profile
        </h2>
    </div>

    <div class="job-details-card">
        <div class="profile-header-section">
            {{-- <h1 class="job-details-title">Supervisor Profile</h1> --}}

            @if ($hasAppliedToEmployerJob)
                <div class="access-badge applied-badge">
                    <i class="fas fa-check-circle"></i> This supervisor has applied to your jobs
                </div>
            @elseif($hasPaidForProfile)
                <div class="access-badge paid-badge">
                    <i class="fas fa-unlock"></i> Profile unlocked via payment
                </div>
            @endif
        </div>

        {{-- Full Profile Details --}}
        <div class="supervisor-profile-card">

            {{-- Basic Info Header --}}
            <div class="profile-basic-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-basic-info">
                    <h3 class="supervisor-name">{{ $supervisor->user->name }}</h3>
                    <p class="supervisor-location">
                        <i class="fas fa-map-marker-alt"></i> {{ $supervisor->city }},
                        {{ $supervisor->state->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <hr class="section-divider">

            {{-- Contact Information --}}
            <div class="info-section">
                <h4 class="section-heading">
                    <i class="fas fa-phone"></i> Contact Information
                </h4>
                <div class="job-info-grid">

                    <div class="job-info-item">
                        <div class="job-info-label">Email</div>
                        <div class="job-info-value">{{ $supervisor->user->email }}</div>
                    </div>

                    <div class="job-info-item">
                        <div class="job-info-label">Phone</div>
                        <div class="job-info-value">{{ $supervisor->user->phone }}</div>
                    </div>

                    <div class="job-info-item">
                        <div class="job-info-label">Address</div>
                        <div class="job-info-value">{{ ucfirst($supervisor->address) }}, {{ ucfirst($supervisor->city) }},
                            {{ ucfirst($supervisor->state->name) }}, {{ $supervisor->pincode }}</div>
                    </div>


                </div>
            </div>

            <hr class="section-divider">

            {{-- Assets & Resources --}}
            <div class="info-section">
                <h4 class="section-heading">
                    <i class="fas fa-toolbox"></i> Assets & Resources
                </h4>
                <div class="assets-grid">
                    <div class="asset-item">
                        <i class="fas fa-motorcycle"></i>
                        <span>Own a Bike:</span>
                        @if ($supervisor->own_bike)
                            <span class="badge-yes">Yes</span>
                        @else
                            <span class="badge-no">No</span>
                        @endif
                    </div>
                    <div class="asset-item">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Own a Phone:</span>
                        @if ($supervisor->own_phone)
                            <span class="badge-yes">Yes</span>
                        @else
                            <span class="badge-no">No</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="section-divider">

            {{-- Skills & Experience --}}
            <div class="info-section">
                <h4 class="section-heading">
                    <i class="fas fa-tools"></i> Skills & Experience
                </h4>

                <div class="">
                    <div class="job-description">
                        <p class="job-description-label">Skills:</p>
                        <div class="">
                            @forelse($supervisor->skills as $skill)
                                <span class="skill-badge">{{ $skill->name }}</span>
                            @empty
                                <span class="text-muted">No skills added</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="job-description">
                        <p class="job-description-label">Experience Areas:</p>
                        <div class="">
                            @forelse($supervisor->experiences as $exp)
                                <span class="skill-badge">{{ $exp->name }}</span>
                            @empty
                                <span class="text-muted">No experience added</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="job-description">
                        <p class="job-description-label">Preferred Work Locations:</p>
                        <div class="">
                            @forelse($supervisor->workLocations as $loc)
                                <span class="skill-badge">{{ $loc->name }}</span>
                            @empty
                                <span class="text-muted">No locations added</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Qualification Test Score --}}
            {{-- @if ($supervisor->passedTestAttempt)
            <hr class="section-divider">
            <div class="info-section">
                <h4 class="section-heading">
                    <i class="fas fa-graduation-cap"></i>Last Qualification Test Score
                </h4>
                <div class="test-score-box">
                    <div class="score-display">
                        <span class="score-text">Score:</span>
                        <span class="score-value">
                            {{ $supervisor->passedTestAttempt->score_obtained }}/{{ $supervisor->passedTestAttempt->total_marks }}
                        </span>
                        <span class="score-percentage">{{ $supervisor->passedTestAttempt->percentage }}%</span>
                    </div>
                    <p class="test-date">
                        <i class="fas fa-calendar-check"></i> 
                        Passed on: {{ $supervisor->passedTestAttempt->submitted_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        @endif --}}

            {{-- Test Attempt History --}}
            @if ($testAttempts->count() > 0)
                <hr class="section-divider">
                <div class="info-section">
                    <h4 class="section-heading">
                        <i class="fas fa-history"></i> Test Attempt History
                    </h4>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Test Name</th>

                                    <th>Score</th>
                                    <th>Time Taken</th>
                                    <th>Admin Suggested Skills by Admin</th>
                                    <th>Result</th>
                                    <th>Submitted At</th>
                                    <th>Dept. Wise Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($testAttempts as $index => $attempt)
                                    <tr>
                                        <td>{{ ($testAttempts->currentPage() - 1) * $testAttempts->perPage() + $index + 1 }}
                                        </td>
                                        <td>{{ $attempt->test->name ?? 'N/A' }}</td>

                                        <td>
                                            {{ $attempt->score_obtained }}/{{ $attempt->total_marks }}
                                            <br>
                                            <small class="text-muted">({{ $attempt->percentage }}%)</small>
                                        </td>
                                        <td>{{ $attempt->formatted_duration }}</td>
                                        <td>
                                            @forelse($attempt->testSkills as $ts)
                                                <span class="badge bg-secondary"
                                                    style="margin-right: 2px;">{{ $ts->skill->name }}</span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if ($attempt->status === 'pass')
                                                <span class="badge bg-success">Pass</span>
                                            @elseif($attempt->status === 'fail')
                                                <span class="badge bg-danger">Fail</span>
                                            @else
                                                <span
                                                    class="badge bg-warning text-dark">{{ ucfirst($attempt->status) }}</span>
                                            @endif
                                        </td>

                                        <td>{{ $attempt->submitted_at?->format('d M Y, h:i A') ?? 'N/A' }}</td>
                                        <td>
                                            @if ($attempt->score_visible)
                                                <button class="btn-dept-score"
                                                    onclick="DeptScorePopup.open('{{ route('test.dept-score', $attempt->id) }}')"
                                                    title="View Department-wise Score">
                                                    <i class="fas fa-chart-bar"></i> Dept. Wise Score
                                                </button>
                                            @else
                                                <span class="btn-dept-score-pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination Links --}}
                    <div class="mt-3">
                        {{ $testAttempts->links() }}
                    </div>
                </div>
            @endif

            {{-- Aadhar Document --}}
            {{-- @if ($supervisor->aadhar_file)
            <hr class="section-divider">
            <div class="info-section">
                <h4 class="section-heading">
                    <i class="fas fa-file-alt"></i> Identity Document
                </h4>
                <a href="{{ Storage::url($supervisor->aadhar_file) }}" target="_blank" class="document-btn">
                    <i class="fas fa-file-pdf"></i> View Aadhar Document
                </a>
            </div>
        @endif --}}

        </div>

        <div class="text-start mt-4">
            <a href="{{ route('employer.search.supervisor') }}" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Search
            </a>
        </div>

    </div>



@endsection
