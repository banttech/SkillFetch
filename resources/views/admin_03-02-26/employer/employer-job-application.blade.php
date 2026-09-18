@extends('layout.admin.app')

@section('content')

<div class="content-card">

    <!-- Page Header -->
    <div class="div-main-haeds">
        <h2 class="page-title">Job Applications - Job #{{ $job->id }}</h2>
    </div>

    <!-- Job Basic Details -->
    <div class="job-details-box">
        <div class="detail-row">
            <div class="detail-item">
                <label>Job Title</label>
                <p>{{ $job->title }}</p>
            </div>

            <div class="detail-item">
                <label>Experience Required</label>
                <p>{{ $job->years ? $job->years . ' Years' : 'No Experience' }}</p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-item">
                <label>Posted On</label>
                <p>{{ $job->created_at->format('d-m-Y') }}</p>
            </div>

            <div class="detail-item">
                <label>Status</label>
                <p>
                    @if($job->status == 'Active')
                        <span class="status-verified">Active</span>
                    @else
                        <span class="status-not-verified">Inactive</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <br>

    <!-- Applications List -->
    <div class="div-main-haeds">
        <h2 class="page-title">Supervisors Who Applied</h2>
    </div>

    <div class="table-container">
        <table class="supervisor-table">
            <thead>
                <tr>
                    <th>Supervisor ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Applied On</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($job->appliedJobs as $applied)

                    <tr>
                        <td><strong>#{{ $applied->supervisor->id }}</strong></td>

                        <td>{{ $applied->supervisor->user->name ?? 'N/A' }}</td>

                        <td>{{ $applied->supervisor->user->email ?? 'N/A' }}</td>

                        <td>{{ $applied->supervisor->user->phone ?? 'N/A' }}</td>

                        <td>
                            <span class="status-badge">
                                {{ ucfirst($applied->status) }}
                            </span>
                        </td>

                        <td>{{ $applied->created_at->format('d-m-Y') }}</td>

                        <td>
                            <a href="{{ route('admin.supervisor.supervisors-qualification-test', $applied->supervisor->id) }}" 
                               class="btn-view">
                                View Profile
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center py-4">
                            No supervisors have applied for this job yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection
