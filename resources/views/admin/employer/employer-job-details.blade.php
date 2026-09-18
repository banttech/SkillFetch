@extends('layout.admin.app')

@section('content')




<div class="content-card">

    <!-- Employer Basic Details -->
    <div class="div-main-haeds">
        <h2 class="page-title">Employer Details</h2>
    </div>

    <div class="job-details-box">
        <div class="detail-row">
            <div class="detail-item">
                <label>Emp ID</label>
                <p>Emp-{{ $employer->id }}</p>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <p>{{ $employer->user->email }}</p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-item">
                <label>Name</label>
                <p>{{ ucfirst($employer->user->name) }}</p>
            </div>
            <div class="detail-item">
                <label>Mobile No.</label>
                <p>{{ $employer->phone }}</p>
            </div>
        </div>
    </div>

    <br>

    <!-- Posted Jobs List -->
    <div class="div-main-haeds">
        <h2 class="page-title">Posted Jobs List</h2>
    </div>

    <div class="table-container ">
        <table class="supervisor-table">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Title</th>
                    <th>Desired Skills</th>
                    <th>Exp. (In Yrs)</th>
                    <th>Status</th>
                    <th>Job Posted On</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($employer->jobs as $job)
                    <tr>
                        <td><strong>{{ $job->id }}</strong></td>

                        <td>{{ ucfirst($job->title) }}</td>

                        {{-- Dynamic Skills --}}
                        <td>
                            @if($job->skills->count())
                                @foreach($job->skills as $skill)
                                    <span class="skill-badge">{{ $skill->name }}</span>
                                @endforeach
                            @else
                                <span class="skill-badge">No Skills</span>
                            @endif
                        </td>

                        {{-- Experience (Years only from post_job table) --}}
                        <td>
                            @if($job->years)
                                {{ $job->years }} Years
                            @else
                                No Experience
                            @endif
                        </td>

                        {{-- Job Status --}}
                        <td>
                            @if ($job->status == 'Active')
                                <span class="status-verified">Active</span>
                            @else
                                <span class="status-not-verified">Inactive</span>
                            @endif
                        </td>

                        {{-- Posted On --}}
                        <td>{{ $job->created_at->format('d-m-Y') }}</td>

                        {{-- View Job Applications --}}
                        <td>
                            <a href="{{ route('admin.employer.job.applications', $job->id) }}" class="btn-view">
                                View Job Applications
                            </a>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:20px;">
                            No jobs posted by this employer.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>

</div>

@endsection
