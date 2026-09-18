@extends('layout.supervisor.app')

@section('content')
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">

                    <div class="welcome-box2">
                        <h1>Open Jobs</h1>
                    </div>

                    <div class="table-card">
                        <table class="table custom-table">
                            <thead>
                                <tr>
                                    <th>Job ID</th>
                                    <th>Title</th>
                                    <th>Skills Needed</th>
                                    <th>Experience(In Years)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($jobs as $job)
                                    <tr>

                                        {{-- Job ID --}}
                                        <td data-label="Job ID">
                                            <span class="job-id"><a href="{{ route('supervisor.jobDetail', $job->id) }}">JOB#{{ $job->id }}</a></span>
                                        </td>

                                        {{-- Title --}}
                                        <td data-label="Title">
                                            <span class="job-title">{{ucfirst($job->title) }}</span>
                                        </td>

                                        {{-- Skills (PIVOT TABLE) --}}
                                        <td data-label="Skills Needed">
                                            @if($job->skills->count())
                                                @foreach($job->skills as $skill)
                                                    <span class="skill-badge">{{ ucfirst($skill->name) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No Skills</span>
                                            @endif
                                        </td>

                                        <td data-label="Experience(In Years)">
                                            @if($job->years)
                                                <span class="skill-badge">{{ $job->years }}</span>
                                            @else
                                                <span class="text-muted">No Experience</span>
                                            @endif
                                        </td>


                                        {{-- Action --}}
                                        <td data-label="Action">
                                            <a href="{{ route('supervisor.jobDetail', $job->id) }}" class="btn view-btn">
                                                View Job
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No open jobs found.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(method_exists($jobs, 'links'))
                        <div class="mt-3">
                            {{ $jobs->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
@endsection