@extends('layout.supervisor.app')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="welcome-box2">
                        <h1>Applied Jobs</h1>
                    </div>

                 

                    <div class="table-card">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Job ID</th>
                                    <th>Title</th>
                                    <th>Skills Needed</th>
                                    <th>Applied Date</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($appliedJobs as $appliedJob)
                                    <tr>
                                        {{-- Job ID --}}
                                        <td data-label="Job ID">
                                            <span class="job-id">
                                                <a href="{{ route('supervisor.jobDetail', $appliedJob->job->id) }}">
                                                    JOB#{{ $appliedJob->job->id }}
                                                </a>
                                            </span>
                                        </td>

                                        {{-- Title --}}
                                        <td data-label="Title">
                                            <span class="job-title">{{ ucfirst($appliedJob->job->title) }}</span>
                                        </td>

                                        {{-- Skills --}}
                                        <td data-label="Skills Needed">
                                            @if($appliedJob->job->skills->count())
                                                @foreach($appliedJob->job->skills as $skill)
                                                    <span class="skill-badge">{{ ucfirst($skill->name) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No Skills</span>
                                            @endif
                                        </td>

                                        {{-- Applied Date --}}
                                        <td data-label="Applied Date">
                                            <span class="text-muted">
                                                {{ $appliedJob->applied_at->format('d M, Y') }}
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        {{-- <td data-label="Status">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'reviewed' => 'info',
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                $color = $statusColors[$appliedJob->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($appliedJob->status) }}
                                            </span>
                                        </td> --}}

                                        {{-- Action --}}
                                        <td data-label="Action">
                                            <a href="{{ route('supervisor.jobDetail', $appliedJob->job->id) }}" 
                                               class="btn view-btn">
                                                View Job
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No applied jobs found. 
                                            <a href="{{ route('supervisor.openjobs') }}">Browse open jobs</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(method_exists($appliedJobs, 'links'))
                        <div class="mt-3">
                            {{ $appliedJobs->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

