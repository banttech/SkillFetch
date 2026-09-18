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
                                    <th>Experience (In yrs)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($appliedJobs as $apply)

                                    @php
                                        $job = $apply->job;
                                    @endphp

                                    <tr>
                                        {{-- Job ID --}}
                                        <td data-label="Job ID">
                                            <span class="job-id">{{ $job->id }}</span>
                                        </td>

                                        {{-- Title --}}
                                        <td data-label="Title">
                                            <span class="job-title">{{ $job->title }}</span>
                                        </td>

                                        {{-- Skills --}}
                                        <td data-label="Skills Needed">
                                            @if($job->skills->count())
                                                @foreach($job->skills as $skillRow)
                                                    <span class="skill-badge">{{ $skillRow->skill->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No Skills</span>
                                            @endif
                                        </td>

                                        {{-- Experience --}}
                                        <td data-label="Experience (In yrs)">
                                            @if($job->years)
                                                <span class="experience-text">{{ $job->years }}</span>
                                            @else
                                                <span class="experience-text">No Experience</span>
                                            @endif
                                        </td>


                                        {{-- Action --}}
                                        <td data-label="Action">
                                            <a href="{{ route('supervisor.applied.job.details', $apply->id) }}"
                                                class="view-btn">
                                                View Job
                                            </a>

                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            No applied jobs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    @if (method_exists($appliedJobs, 'links'))
                        <div class="mt-3">
                            {{ $appliedJobs->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

@endsection