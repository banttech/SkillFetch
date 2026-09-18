@extends('layout.employer.app')

@section('content')


 <div class="content-header">
        <h2>
            <i class="fas fa-briefcase"></i>
            Job Detail
        </h2>
    </div>

    <div class="job-details-card">
        {{-- <h1 class="job-details-title">Job Detail</h1> --}}

        {{-- ================= JOB INFO ================= --}}
        <div class="job-info-grid">

            <div class="job-info-item">
                <div class="job-info-label">Job ID</div>
                <div class="job-info-value">JOB#{{ $job->id }}</div>
            </div>

            {{-- <div class="job-info-item">
                <div class="job-info-label">Title</div>
                <div class="job-info-value">{{ ucfirst($job->title) }}</div>
            </div> --}}

             <div class="job-info-item">
                <div class="job-info-label">Total Experience</div>
                <div class="job-info-value">{{ $job->years }}</div>
            </div>

            <div class="job-info-item">
                <div class="job-info-label">Job Posted On</div>
                <div class="job-info-value">{{ $job->created_at->format('d-m-Y') }}</div>
            </div>

            <div class="job-info-item">
                <div class="job-info-label">Status</div>
                @if ($job->status)
                    <div class="job-info-value green-value">Active</div>
                @else
                    <div class="text-danger">Inactive</div>
                @endif

            </div>

        </div>

        {{-- ================= DESCRIPTION ================= --}}


          <div class="job-description">
                    <div class="job-description-label">Title</div>
                    <div class="job-description-text">
                        {{ $job->title }}
                    </div>
                </div>




                <div class="job-description">
                    <div class="job-description-label">Description</div>
                    <div class="job-description-text">
                        {{ $job->description }}
                    </div>
                </div>

          <div class="job-description">
                    <div class="job-description-label">Admin Skills</div>
                    <div class="job-description-text">
                         @foreach ($job->skills as $skill)
                                        <span class="skill-badge">{{ $skill->name }}</span>
                                    @endforeach
                    </div>
                </div>       

                 <div class="job-description">
                    <div class="job-description-label">Experience</div>
                    <div class="job-description-text">
                        @foreach ($job->experiences as $experience)
                                        <span class="skill-badge">{{ $experience->name }}</span>
                                    @endforeach
                    </div>
                </div>

                 <div class="job-description">
                    <div class="job-description-label">Employer Skills</div>
                    <div class="job-description-text">
                        @forelse ($job->employerSkills as $skill)
                                        <span class="skill-badge">{{ $skill->skills }}</span>
                        @empty
                                        <span class="text-muted">None</span>
                        @endforelse
                    </div>
                </div>

                 <div class="job-description">
                    <div class="job-description-label">Preferred Work Locations</div>
                    <div class="job-description-text">
                        @forelse ($job->workLocations as $loc)
                                        <span class="skill-badge">{{ $loc->name }}</span>
                        @empty
                                        <span class="text-muted">None</span>
                        @endforelse
                    </div>
                </div>

                 <div class="job-description">
                    <div class="job-description-label">Salary & Allowances</div>
                    <div class="job-description-text">
                        <p><strong>Fixed Salary (In Rs.) (Per Month):</strong> {{ $job->fixed_salary ?? 'N/A' }}</p>
                        <p><strong>Variable Salary (In Rs.) (Per Month):</strong> {{ $job->variable_salary_from ?? '0' }} - {{ $job->variable_salary_to ?? '0' }}</p>
                        <p><strong>Petrol Allowance (In Rs.) (Per Month):</strong> {{ $job->petrol_allowance ?? 'N/A' }}</p>
                        <p><strong>Accommodation (In Rs.) (Per Month):</strong> {{ $job->accommodation ?? 'N/A' }}</p>
                        <p><strong>Food Allowance (In Rs.) (Per Month):</strong> {{ $job->food_allowance ?? 'N/A' }}</p>
                    </div>
                </div>

        {{-- ================= SUPERVISORS APPLIED ================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="supervisors-section-title mb-0">List of Supervisors who have applied for this job</h2>
            @if($job->appliedSupervisors->count() > 0)
                <a href="{{ route('employer.job.export-supervisors', $job->id) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Profiles
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table supervisors-table">
                <thead>
                    <tr>
                        <th>Phone No.</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Preferred Work Locations</th>
                        <th>Own a Bike</th>
                        <th>Own a Phone</th>
                        <th>Skills</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($job->appliedSupervisors as $sup)
                        <tr>

                            {{-- Phone --}}
                            <td><b>{{ $sup->user->phone ?? 'N/A' }}</b></td>

                            {{-- Name --}}
                            <td>{{ ucfirst($sup->user->name) ?? 'N/A' }}</td>

                            {{-- City --}}
                            <td>{{ $sup->city ?? 'N/A' }}</td>

                            {{-- Preferred Work Locations --}}
                            <td>
                                @if ($sup->workLocations && $sup->workLocations->count())
                                    {{ $sup->workLocations->pluck('name')->implode(', ') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            {{-- Own Bike --}}
                            <td>
                                @if ($sup->own_bike == 1)
                                    <span class="status-badge">Yes</span>
                                @else
                                    <span class="badge-no">No</span>
                                @endif
                            </td>

                            <td>
                                @if ($sup->own_phone == 1)
                                    <span class="status-badge">Yes</span>
                                @else
                                    <span class="badge-no">No</span>
                                @endif
                            </td>

                            {{-- Skills --}}
                            <td>
                                @if ($sup->skills && $sup->skills->count())
                                    {{ $sup->skills->pluck('name')->implode(', ') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td class="view-profiles">
                                 <a href="{{ route('employer.supervisor.profile', $sup->id) }}"
                                    class="view-profile-btn">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                No supervisors have applied yet.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>
@endsection
